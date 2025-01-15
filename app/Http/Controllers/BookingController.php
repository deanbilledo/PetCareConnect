<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Pet;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
            Log::info('Loading shop data:', [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'ratings_count' => $shop->ratings->count()
            ]);

            return view('booking.book', compact('shop'));
        } catch (\Exception $e) {
            Log::error('Error in show method: ' . $e->getMessage(), [
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
        try {
            $validated = $request->validate([
                'appointment_type' => 'required|in:single,multiple',
                'pet_ids' => 'required|array|min:1',
                'pet_ids.*' => 'exists:pets,id'
            ]);

            $pets = Pet::whereIn('id', $validated['pet_ids'])
                ->where('user_id', auth()->id())
                ->get();

            if ($pets->isEmpty()) {
                return back()->with('error', 'Please select valid pets.');
            }

            // Load active services
            $services = $shop->services()
                ->where('status', 'active')
                ->get();

            // Get any existing booking data from session
            $bookingData = session('booking', []);

            Log::info('Processing service selection:', [
                'shop_id' => $shop->id,
                'pet_ids' => $validated['pet_ids'],
                'appointment_type' => $validated['appointment_type'],
                'booking_data' => $bookingData
            ]);

            return view('booking.select-service', compact('shop', 'pets', 'services', 'bookingData'));
        } catch (\Exception $e) {
            Log::error('Error in selectService: ' . $e->getMessage());
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }

    public function selectDateTime(Request $request, Shop $shop)
    {
        try {
            $validated = $request->validate([
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
                ->whereIn('id', $validated['services'])
                ->where('status', 'active')
                ->get();

            if ($selectedServices->isEmpty()) {
                return back()->with('error', 'Selected services are not available.');
            }

            // Calculate total duration
            $totalDuration = $selectedServices->sum('duration');

            // Create pet services mapping
            $petServices = array_combine($validated['pet_ids'], $validated['services']);

            // Store in session
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices
            ];
            
            session(['booking' => $bookingData]);

            // Debug log
            Log::info('Booking data stored in session:', [
                'booking_data' => $bookingData,
                'session_data' => session('booking')
            ]);

            // Pass session data directly to view
            return view('booking.select-datetime', [
                'shop' => $shop,
                'operatingHours' => $operatingHours,
                'totalDuration' => $totalDuration,
                'bookingData' => $bookingData
            ]);

        } catch (\Exception $e) {
            Log::error('Error in selectDateTime: ' . $e->getMessage());
            return back()->with('error', 'There was an error loading the booking form. Please try again.');
        }
    }

    public function showConfirm(Request $request, Shop $shop)
    {
        try {
            $validated = $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|string'
            ]);

            // Parse the appointment date and time
            $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);
            
            // Load pets with their services
            $pets = Pet::whereIn('id', $validated['pet_ids'])
                ->where('user_id', auth()->id())
                ->get();
            
            // Load services
            $services = $shop->services()
                ->whereIn('id', $validated['services'])
                ->where('status', 'active')
                ->get();

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            // Create pet services mapping
            $petServices = array_combine($validated['pet_ids'], $validated['services']);

            // Store booking data
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time']
            ];
            
            session(['booking' => $bookingData]);

            return view('booking.confirm', compact('shop', 'pets', 'services', 'appointmentDateTime', 'bookingData'));
        } catch (\Exception $e) {
            Log::error('Error in showConfirm method: ' . $e->getMessage());
            return back()->with('error', 'There was an error processing your booking. Please try again.');
        }
    }

    public function confirm(Request $request, Shop $shop)
    {
        try {
            $validated = $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|string'
            ]);

            // Parse the appointment date and time
            $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);
            
            // Load pets with their services
            $pets = Pet::whereIn('id', $validated['pet_ids'])
                ->where('user_id', auth()->id())
                ->get();
            
            // Load services
            $services = $shop->services()
                ->whereIn('id', $validated['services'])
                ->where('status', 'active')
                ->get();

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            // Create pet services mapping
            $petServices = array_combine($validated['pet_ids'], $validated['services']);

            // Store booking data
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time']
            ];
            
            session(['booking' => $bookingData]);

            Log::info('Confirm booking data:', [
                'booking_data' => $bookingData,
                'pets' => $pets->pluck('name', 'id'),
                'services' => $services->pluck('name', 'id')
            ]);

            return view('booking.confirm', compact('shop', 'pets', 'services', 'appointmentDateTime', 'bookingData'));
        } catch (\Exception $e) {
            Log::error('Error in confirm method: ' . $e->getMessage());
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

            // Get all selected services with their prices
            $services = $shop->services()
                ->whereIn('id', $request->services)
                ->where('status', 'active')
                ->get();

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            DB::beginTransaction();
            
            $servicesBreakdown = [];
            $total = 0;

            // Create appointments for each pet
            foreach ($request->pet_ids as $index => $petId) {
                $serviceId = $request->services[$index] ?? null;
                $service = $services->firstWhere('id', $serviceId);
                $pet = Pet::find($petId);
                
                if (!$service || !$pet) {
                    throw new \Exception('Service or pet not found');
                }

                // Calculate price based on pet size
                $price = $service->base_price;
                if (!empty($service->variable_pricing)) {
                    $sizePrice = collect($service->variable_pricing)
                        ->firstWhere('size', $pet->size_category);
                    if ($sizePrice && isset($sizePrice['price'])) {
                        $price = $sizePrice['price'];
                    }
                }

                $total += $price;

                $appointment = Appointment::create([
                    'user_id' => auth()->id(),
                    'shop_id' => $shop->id,
                    'pet_id' => $petId,
                    'service_type' => $service->name,
                    'service_price' => $price,
                    'appointment_date' => $appointmentDateTime,
                    'notes' => $request->notes,
                    'status' => 'pending'
                ]);

                // Add to services breakdown
                $servicesBreakdown[] = [
                    'pet_name' => $pet->name,
                    'service_name' => $service->name,
                    'size' => $pet->size_category,
                    'price' => $price
                ];
                
                $appointmentDateTime->addMinutes($service->duration);
            }

            DB::commit();

            // Store booking details in session for thank you page
            session(['booking_details' => [
                'shop_name' => $shop->name,
                'date' => $appointmentDateTime->format('F j, Y'),
                'time' => $appointmentDateTime->format('g:i A'),
                'services' => $servicesBreakdown,
                'total_amount' => $total
            ]]);

            // Clear booking session data
            session()->forget([
                'booking.pet_ids',
                'booking.services',
                'booking.appointment_date',
                'booking.appointment_time'
            ]);

            return redirect()->route('booking.thank-you', $shop)
                ->with('success', 'Your appointment has been booked successfully!');

        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
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
            Log::error('Error getting time slots: ' . $e->getMessage());
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

    public function thankYou(Shop $shop)
    {
        // Check if we have booking details in session
        if (!session()->has('booking_details')) {
            return redirect()->route('home');
        }

        return view('booking.thank-you', [
            'shop' => $shop,
            'booking_details' => session('booking_details')
        ]);
    }
} 