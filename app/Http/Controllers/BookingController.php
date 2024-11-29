<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Pet;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('web');
    }

    public function show(Shop $shop)
    {
        try {
            // Load the shop with its relationships
            $shop->load(['ratings' => function($query) {
                $query->with(['user' => function($query) {
                    $query->select('id', 'first_name', 'last_name', 'profile_photo_path');
                }]);
            }]);

            // Add debug logging
            \Log::info('Loading shop data:', [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'ratings_count' => $shop->ratings->count()
            ]);

            return view('booking.book', compact('shop'));
        } catch (\Exception $e) {
            \Log::error('Error in show method: ' . $e->getMessage(), [
                'shop_id' => $shop->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Unable to load shop details. Please try again.');
        }
    }

    public function process(Shop $shop)
    {
        $pets = auth()->user()->pets;
        return view('booking.process', compact('shop', 'pets'));
    }

    public function selectService(Shop $shop, Request $request)
    {
        $request->validate([
            'appointment_type' => 'required|in:single,multiple',
            'pet_ids' => 'required|array',
            'pet_ids.*' => 'exists:pets,id'
        ]);

        $pets = Pet::whereIn('id', $request->pet_ids)->get();
        
        // Load active services from the shop
        $services = $shop->services()
            ->where('status', 'active')
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'description' => $service->description,
                    'duration' => $service->duration
                ];
            });

        return view('booking.select-service', compact('shop', 'pets', 'services'));
    }

    public function selectDateTime(Shop $shop, Request $request)
    {
        try {
            $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id'
            ]);

            // Load shop's operating hours
            $operatingHours = $shop->operatingHours()
                ->orderBy('day')
                ->get();

            // Get selected services for duration calculation
            $selectedServices = $shop->services()
                ->whereIn('id', $request->services)
                ->where('status', 'active')
                ->get();

            if ($selectedServices->isEmpty()) {
                return back()->with('error', 'Selected services are not available.');
            }

            // Calculate total duration
            $totalDuration = $selectedServices->sum('duration');

            // Store selected data in session for confirmation
            session([
                'booking.pet_ids' => $request->pet_ids,
                'booking.services' => $request->services
            ]);

            return view('booking.select-datetime', compact('shop', 'operatingHours', 'totalDuration'));
            
        } catch (\Exception $e) {
            \Log::error('Error in selectDateTime: ' . $e->getMessage());
            return back()->with('error', 'There was an error loading the booking form. Please try again.');
        }
    }

    public function confirm(Shop $shop, Request $request)
    {
        try {
            $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|string'
            ]);

            // Parse the appointment date and time
            $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
            
            // Get the day of week for the appointment
            $dayOfWeek = $appointmentDateTime->dayOfWeek;
            
            // Check if shop is open on this day
            $operatingHours = $shop->operatingHours()
                ->where('day', $dayOfWeek)
                ->where('is_open', true)
                ->first();
                
            if (!$operatingHours) {
                return back()->with('error', 'The shop is not open on the selected day.');
            }
            
            // Load pets
            $pets = Pet::whereIn('id', $request->pet_ids)->get();
            
            // Load services with full details
            $services = $shop->services()
                ->whereIn('id', $request->services)
                ->where('status', 'active')
                ->get()
                ->mapWithKeys(function ($service) {
                    return [$service->id => [
                        'name' => $service->name,
                        'description' => $service->description,
                        'price' => $service->price,
                        'duration' => $service->duration
                    ]];
                });

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            // Store data in session
            session([
                'booking.pet_ids' => $request->pet_ids,
                'booking.services' => $request->services,
                'booking.appointment_date' => $request->appointment_date,
                'booking.appointment_time' => $request->appointment_time
            ]);

            return view('booking.confirm', compact('shop', 'pets', 'services', 'appointmentDateTime'));
            
        } catch (\Exception $e) {
            \Log::error('Error in booking confirmation: ' . $e->getMessage());
            return back()->with('error', 'There was an error processing your booking. Please try again.');
        }
    }

    public function store(Shop $shop, Request $request)
    {
        try {
            $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required',
                'notes' => 'nullable|string|max:500'
            ]);

            $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);

            // Check if shop is open
            $dayOfWeek = $appointmentDateTime->dayOfWeek;
            $operatingHours = $shop->operatingHours()
                ->where('day', $dayOfWeek)
                ->where('is_open', true)
                ->first();

            if (!$operatingHours) {
                return back()->with('error', 'The shop is not open on the selected day.');
            }

            // Get all selected services
            $services = $shop->services()
                ->whereIn('id', $request->services)
                ->where('status', 'active')
                ->get();

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            \DB::beginTransaction();
            try {
                // Create appointments for each pet
                foreach ($request->pet_ids as $petId) {
                    foreach ($services as $service) {
                        $appointment = Appointment::create([
                            'user_id' => auth()->id(),
                            'shop_id' => $shop->id,
                            'pet_id' => $petId,
                            'service_type' => $service->name,
                            'service_price' => $service->price,
                            'appointment_date' => $appointmentDateTime,
                            'notes' => $request->notes,
                            'status' => 'pending'
                        ]);
                        
                        \Log::info('Created appointment:', $appointment->toArray());
                        
                        // Add duration for next appointment if there are more services
                        $appointmentDateTime->addMinutes($service->duration);
                    }
                }

                \DB::commit();

                // Clear booking session data
                session()->forget([
                    'booking.pet_ids',
                    'booking.services',
                    'booking.appointment_date',
                    'booking.appointment_time'
                ]);

                return redirect()->route('appointments.index')
                    ->with('success', 'Your appointment has been booked successfully!');

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Error creating appointments: ' . $e->getMessage());
                return back()->with('error', 'Failed to create appointments. Please try again.');
            }

        } catch (\Exception $e) {
            \Log::error('Error in store method: ' . $e->getMessage());
            return back()->with('error', 'There was an error processing your booking. Please try again.');
        }
    }

    public function getTimeSlots(Request $request, Shop $shop)
    {
        try {
            // Validate request
            $request->validate([
                'date' => 'required|date|after:today',
                'duration' => 'required|integer|min:1'
            ]);

            // Parse the requested date
            $date = Carbon::parse($request->date);
            
            // Get available time slots
            $slots = $this->getAvailableTimeSlots($shop, $date, $request->duration);

            return response()->json([
                'slots' => $slots,
                'message' => count($slots) > 0 ? null : 'No available time slots for this date'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting time slots: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to get time slots: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAvailableTimeSlots(Shop $shop, Carbon $date, int $totalDuration)
    {
        $dayOfWeek = $date->dayOfWeek;
        
        // Get operating hours for the selected day
        $operatingHours = $shop->operatingHours()
            ->where('day', $dayOfWeek)
            ->where('is_open', true)
            ->first();
            
        if (!$operatingHours) {
            return [];
        }

        $slots = [];
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->open_time);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->close_time);

        // Subtract total duration from end time to ensure service can be completed
        $end->subMinutes($totalDuration);

        while ($start <= $end) {
            // Check if this time slot is available (not booked)
            $slotEnd = (clone $start)->addMinutes($totalDuration);
            
            $conflictingAppointments = Appointment::where('shop_id', $shop->id)
                ->where('appointment_date', '>=', $start)
                ->where('appointment_date', '<', $slotEnd)
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($conflictingAppointments === 0) {
                $slots[] = $start->format('g:i A'); // Changed to 12-hour format with AM/PM
            }
            
            $start->addMinutes(30);
        }

        return $slots;
    }
} 