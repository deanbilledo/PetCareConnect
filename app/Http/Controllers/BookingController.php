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
        try {
            $pets = auth()->user()->pets;
            
            // Separate exotic and non-exotic pets
            $exoticPets = $pets->filter(function($pet) {
                return strtolower($pet->type) === 'exotic';
            });
            
            $regularPets = $pets->filter(function($pet) {
                return strtolower($pet->type) !== 'exotic';
            });
            
            // Debug log
            Log::info('Processing pets for booking:', [
                'total_pets' => $pets->count(),
                'exotic_pets' => $exoticPets->count(),
                'regular_pets' => $regularPets->count()
            ]);
            
            return view('booking.process', compact('shop', 'pets', 'exoticPets', 'regularPets'));
        } catch (\Exception $e) {
            Log::error('Error in process method: ' . $e->getMessage());
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }

    public function selectService(Request $request, Shop $shop)
    {
        try {
            $validated = $request->validate([
                'appointment_type' => 'required|in:single,multiple',
                'pet_ids' => 'required|array|min:1',
                'pet_ids.*' => 'exists:pets,id'
            ]);

            // Load pets with their details
            $pets = Pet::whereIn('id', $validated['pet_ids'])
                ->where('user_id', auth()->id())
                ->get();

            // Check for mixed exotic and non-exotic pets
            $hasExotic = $pets->contains(function($pet) {
                return strtolower($pet->type) === 'exotic';
            });
            
            $hasRegular = $pets->contains(function($pet) {
                return strtolower($pet->type) !== 'exotic';
            });

            // If trying to book both exotic and regular pets together
            if ($hasExotic && $hasRegular) {
                return back()->with('error', 'Exotic pets must be booked separately from other pets.');
            }

            // If multiple exotic pets are selected
            if ($hasExotic && $pets->count() > 1) {
                return back()->with('error', 'Exotic pets must be booked individually.');
            }

            Log::info('Pet validation for booking:', [
                'has_exotic' => $hasExotic,
                'has_regular' => $hasRegular,
                'pet_count' => $pets->count(),
                'pet_types' => $pets->pluck('type')->toArray()
            ]);

            // Load services and debug their data
            $services = $shop->services()
                ->where('status', 'active')
                ->get();

            // Debug log services
            foreach ($services as $service) {
                Log::info('Service data:', [
                    'id' => $service->id,
                    'name' => $service->name,
                    'pet_types' => $service->pet_types,
                    'exotic_pet_service' => $service->exotic_pet_service,
                    'exotic_pet_species' => $service->exotic_pet_species
                ]);
            }

            if ($pets->isEmpty()) {
                return back()->with('error', 'Please select valid pets.');
            }

            // Store appointment type in session
            $bookingData = session('booking', []);
            $bookingData['appointment_type'] = $validated['appointment_type'];
            $bookingData['pet_ids'] = $validated['pet_ids'];
            session(['booking' => $bookingData]);

            // Debug log booking data
            Log::info('Booking data stored:', [
                'appointment_type' => $validated['appointment_type'],
                'pet_ids' => $validated['pet_ids'],
                'session_data' => session('booking')
            ]);

            return view('booking.select-service', compact('shop', 'pets', 'services', 'bookingData'));
        } catch (\Exception $e) {
            Log::error('Error in selectService: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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

            // Load pets data
            $pets = auth()->user()->pets()
                ->whereIn('id', $validated['pet_ids'])
                ->get();

            // Create pet services mapping
            $petServices = array_combine($validated['pet_ids'], $validated['services']);

            // Get existing booking data to preserve appointment_type
            $existingBookingData = session('booking', []);
            
            // Store in session
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'pets' => $pets,
                'appointment_type' => $existingBookingData['appointment_type'] ?? 'single' // Default to single if not set
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

            // Calculate total amount
            $totalAmount = 0;
            $servicesBreakdown = [];

            foreach ($pets as $pet) {
                $serviceId = $petServices[$pet->id] ?? null;
                $service = $services->firstWhere('id', $serviceId);
                
                if ($service) {
                    // Get base price
                    $price = $service->base_price;
                    
                    // Apply size-based pricing if available
                    if (!empty($service->variable_pricing)) {
                        $variablePricing = is_string($service->variable_pricing) ? 
                            json_decode($service->variable_pricing, true) : 
                            $service->variable_pricing;
                        
                        $sizePrice = collect($variablePricing)->firstWhere('size', $pet->size_category);
                        if ($sizePrice && isset($sizePrice['price'])) {
                            $price = $sizePrice['price'];
                        }
                    }

                    $totalAmount += $price;

                    // Add to services breakdown
                    $servicesBreakdown[] = [
                        'pet_name' => $pet->name,
                        'service_name' => $service->name,
                        'size' => $pet->size_category,
                        'price' => $price
                    ];
                }
            }

            // Get existing booking data to preserve appointment_type
            $existingBookingData = session('booking', []);
            
            // Store booking data
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'pets' => $pets,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'total_amount' => $totalAmount,
                'services' => $servicesBreakdown,
                'appointment_type' => $existingBookingData['appointment_type'] ?? 'single' // Default to single if not set
            ];

            // Debug log the booking data
            Log::info('Booking data in showConfirm:', [
                'booking_data' => $bookingData,
                'appointment_type' => $bookingData['appointment_type']
            ]);

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
            Log::info('Booking store request:', $request->all());

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

            // Get booking data from session with default values
            $bookingData = session('booking', []);
            $bookingData['appointment_type'] = $bookingData['appointment_type'] ?? 'single'; // Ensure appointment_type has a default
            
            Log::info('Session booking data:', [
                'booking_data' => $bookingData,
                'appointment_type' => $bookingData['appointment_type']
            ]);

            // Get all selected services with their prices
            $services = $shop->services()
                ->whereIn('id', array_values($bookingData['pet_services'] ?? []))
                ->where('status', 'active')
                ->get();

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            DB::beginTransaction();
            
            try {
                $servicesBreakdown = [];
                $total = 0;
                $currentDateTime = clone $appointmentDateTime;

                // Create appointments for each pet
                foreach ($bookingData['pet_ids'] as $petId) {
                    $serviceId = $bookingData['pet_services'][$petId] ?? null;
                    $service = $services->firstWhere('id', $serviceId);
                    $pet = Pet::find($petId);
                    
                    if (!$service || !$pet) {
                        throw new \Exception("Service or pet not found for pet ID: $petId");
                    }

                    Log::info('Creating appointment:', [
                        'pet_id' => $petId,
                        'service_id' => $serviceId,
                        'datetime' => $currentDateTime->format('Y-m-d H:i:s')
                    ]);

                    // Calculate price based on pet size
                    $price = $service->base_price;
                    if (!empty($service->variable_pricing)) {
                        $variablePricing = is_string($service->variable_pricing) ? 
                            json_decode($service->variable_pricing, true) : 
                            $service->variable_pricing;
                        
                        $sizePrice = collect($variablePricing)->firstWhere('size', $pet->size_category);
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
                        'appointment_date' => $currentDateTime,
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
                    
                    // For multiple appointments, add service duration
                    if ($bookingData['appointment_type'] === 'multiple') {
                        $currentDateTime->addMinutes($service->duration);
                    }
                }

                DB::commit();

                // Store booking details in session for thank you page
                session(['booking_details' => [
                    'shop_name' => $shop->name,
                    'date' => $appointmentDateTime->format('F j, Y'),
                    'time' => $appointmentDateTime->format('g:i A'),
                    'services' => $servicesBreakdown,
                    'total_amount' => $total,
                    'appointment_type' => $bookingData['appointment_type']
                ]]);

                // Clear booking session data
                session()->forget('booking');

                return redirect()->route('booking.thank-you', $shop)
                    ->with('success', 'Your appointment has been booked successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction error: ' . $e->getMessage());
                throw $e;
            }
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