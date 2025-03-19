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
use Illuminate\Support\Str;
use App\Models\Service;
use App\Services\AppointmentNotificationService;

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

            // Dynamically calculate if the shop is open based on operating hours
            $shopController = new \App\Http\Controllers\ShopController();
            $shop->setAttribute('is_open', $shopController->isShopOpen($shop));

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
            // Use our updated method to get only living pets (non-deceased)
            $petsByType = $this->getPetsByType(auth()->user());
            $exoticPets = $petsByType['exoticPets'];
            $regularPets = $petsByType['regularPets'];
            
            // Debug log
            Log::info('Processing pets for booking:', [
                'total_pets' => $exoticPets->count() + $regularPets->count(),
                'exotic_pets' => $exoticPets->count(),
                'regular_pets' => $regularPets->count()
            ]);
            
            // Dynamically calculate if the shop is open based on operating hours
            $shopController = new \App\Http\Controllers\ShopController();
            $shop->setAttribute('is_open', $shopController->isShopOpen($shop));
            
            return view('booking.process', compact('shop', 'exoticPets', 'regularPets'));
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
            
            // Check if any of the selected pets are deceased
            $deceasedPets = $pets->filter(function($pet) {
                return $pet->isDeceased();
            });
            
            if ($deceasedPets->count() > 0) {
                $petNames = $deceasedPets->pluck('name')->implode(', ');
                return back()->with('error', "Cannot book appointments for deceased pets: {$petNames}");
            }

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

            // Dynamically calculate if the shop is open based on operating hours
            $shopController = new \App\Http\Controllers\ShopController();
            $shop->setAttribute('is_open', $shopController->isShopOpen($shop));

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
                'services.*' => 'exists:services,id',
                'add_ons' => 'nullable|array'
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
                'appointment_type' => $existingBookingData['appointment_type'] ?? 'single', // Default to single if not set
                'add_ons' => $request->input('add_ons', []) // Store add-ons data
            ];
            
            session(['booking' => $bookingData]);

            // Debug log
            Log::info('Booking data stored in session:', [
                'booking_data' => $bookingData,
                'session_data' => session('booking'),
                'add_ons' => $bookingData['add_ons']
            ]);

            // Dynamically calculate if the shop is open based on operating hours
            $shopController = new \App\Http\Controllers\ShopController();
            $shop->setAttribute('is_open', $shopController->isShopOpen($shop));

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
                'appointment_time' => 'required',
                'employee_id' => 'required|exists:employees,id'
            ]);

            $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
            
            // Load employee information
            $employee = $shop->employees()->findOrFail($request->employee_id);
            
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

            // Get existing booking data to preserve appointment_type and add-ons
            $existingBookingData = session('booking', []);
            
            // Store booking data
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'pets' => $pets,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'appointment_type' => $existingBookingData['appointment_type'] ?? 'single',
                'employee_id' => $employee->id,
                'employee' => [
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'profile_photo_url' => $employee->profile_photo_url
                ],
                // Preserve add-ons data from the previous step
                'add_ons' => $request->input('add_ons', $existingBookingData['add_ons'] ?? [])
            ];

            // Debug log the booking data
            Log::info('Booking data in showConfirm:', [
                'booking_data' => $bookingData,
                'appointment_type' => $bookingData['appointment_type'],
                'add_ons' => $bookingData['add_ons']
            ]);

            session(['booking' => $bookingData]);

            // Dynamically calculate if the shop is open based on operating hours
            $shopController = new \App\Http\Controllers\ShopController();
            $shop->setAttribute('is_open', $shopController->isShopOpen($shop));

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

            // Dynamically calculate if the shop is open based on operating hours
            $shopController = new \App\Http\Controllers\ShopController();
            $shop->setAttribute('is_open', $shopController->isShopOpen($shop));

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
                'notes' => 'nullable|string|max:500',
                'voucher_code' => 'nullable|string',
                'discount_amount' => 'nullable|numeric',
                'final_total' => 'nullable|numeric',
                'add_ons' => 'nullable|array'
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
            $bookingData['appointment_type'] = $bookingData['appointment_type'] ?? 'single';
            
            Log::info('Session booking data:', [
                'booking_data' => $bookingData,
                'appointment_type' => $bookingData['appointment_type'],
                'add_ons' => $bookingData['add_ons'] ?? []
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

                // Get the employee data from the booking session
                $employee = $shop->employees()->findOrFail($bookingData['employee_id']);

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
                    $price = $service->getPriceForSize($pet->size_category);

                    // Calculate add-ons total
                    $addOnTotal = 0;
                    $selectedAddOns = $bookingData['add_ons'][$petId][$serviceId] ?? [];
                    $addOnDetails = [];
                    
                    if (!empty($selectedAddOns) && !empty($service->add_ons)) {
                        foreach ($selectedAddOns as $selectedAddOn) {
                            foreach ($service->add_ons as $addOn) {
                                if ($addOn['name'] === $selectedAddOn) {
                                    $addOnTotal += (float) $addOn['price'];
                                    $addOnDetails[] = $addOn;
                                }
                            }
                        }
                    }

                    // Apply discount if voucher code is present
                    $originalPrice = $price + $addOnTotal;
                    $finalPrice = $originalPrice;
                    
                    if ($request->filled('voucher_code')) {
                        $discountedPrice = $service->getDiscountedPrice($originalPrice, $request->voucher_code);
                        $finalPrice = $discountedPrice;
                    }

                    $total += $finalPrice;

                    $appointment = Appointment::create([
                        'user_id' => auth()->id(),
                        'shop_id' => $shop->id,
                        'pet_id' => $petId,
                        'employee_id' => $employee->id,
                        'service_type' => $service->name,
                        'service_price' => $finalPrice,
                        'original_price' => $originalPrice,
                        'add_ons' => !empty($addOnDetails) ? json_encode($addOnDetails) : null,
                        'add_ons_total' => $addOnTotal,
                        'voucher_code' => $request->voucher_code,
                        'discount_amount' => $request->filled('voucher_code') ? 
                            ($originalPrice - $finalPrice) : null,
                        'appointment_date' => $currentDateTime,
                        'notes' => $request->notes,
                        'status' => 'pending'
                    ]);

                    // Add to services breakdown
                    $servicesBreakdown[] = [
                        'pet_id' => $pet->id,
                        'pet_name' => $pet->name,
                        'service_name' => $service->name,
                        'size' => $pet->size_category,
                        'price' => $price,
                        'add_ons' => $addOnDetails,
                        'add_ons_total' => $addOnTotal,
                        'final_price' => $finalPrice
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
                    'original_total' => array_sum(array_column($servicesBreakdown, 'final_price')),
                    'discount_amount' => $request->discount_amount,
                    'voucher_code' => $request->voucher_code,
                    'appointment_type' => $bookingData['appointment_type'] ?? 'single',
                    'employee' => [
                        'name' => $employee->name,
                        'position' => $employee->position,
                        'profile_photo_url' => $employee->profile_photo_url
                    ]
                ]]);

                // Clear booking session data
                session()->forget('booking');

                // Create notifications for each appointment
                $appointments = Appointment::where('user_id', auth()->id())
                                         ->where('shop_id', $shop->id)
                                         ->where('status', 'pending')
                                         ->whereNull('viewed_at')
                                         ->whereIn('pet_id', array_column($servicesBreakdown, 'pet_id'))
                                         ->orderBy('created_at', 'desc')
                                         ->get();
                
                foreach($appointments as $appointment) {
                    AppointmentNotificationService::createNewAppointmentNotification($appointment);
                }

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

        // Get all employees who can perform services
        $employees = $shop->employees()->get();
        $totalEmployees = $employees->count();

        if ($totalEmployees === 0) {
            return [];
        }

        // Get all appointments for the day
        $appointments = $shop->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'accepted', 'confirmed', 'reschedule_requested'])
            ->with('services') // Load services to calculate duration
            ->get();

        $slots = [];
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->open_time);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->close_time);

        // Subtract total duration from end time to ensure service can be completed
        $end->subMinutes($totalDuration);

        while ($start <= $end) {
            $slotEnd = (clone $start)->addMinutes($totalDuration);
            
            // Count how many employees are available in this time slot
            $busyEmployees = 0;
            foreach ($appointments as $appointment) {
                $appointmentStart = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
                
                // Get the appointment's actual duration from its services
                $appointmentDuration = 0;
                foreach ($appointment->services as $service) {
                    $appointmentDuration += $service->duration;
                }
                
                // Use the actual duration or default to 60 minutes if no services
                $appointmentDuration = $appointmentDuration > 0 ? $appointmentDuration : 60;
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointmentDuration);

                // Check if appointment overlaps with current slot
                if (($start >= $appointmentStart && $start < $appointmentEnd) ||
                    ($slotEnd > $appointmentStart && $slotEnd <= $appointmentEnd) ||
                    ($start <= $appointmentStart && $slotEnd >= $appointmentEnd)) {
                    $busyEmployees++;
                }
            }

            $availableEmployees = $totalEmployees - $busyEmployees;

            // Only add slot if there are available employees
            if ($availableEmployees > 0) {
                $slots[] = [
                    'time' => $start->format('g:i A'),
                    'available_employees' => $availableEmployees,
                    'total_employees' => $totalEmployees,
                    'end_time' => $slotEnd->format('g:i A') // Add end time
                ];
            }
            
            // For services longer than 60 minutes, use a smarter increment to avoid too many overlapping slots
            // For shorter services, keep the 30-minute standard increment
            $incrementMinutes = min(max(30, $totalDuration / 2), 60);
            $start->addMinutes($incrementMinutes);
        }

        return $slots;
    }

    public function getTimeSlots(Request $request, Shop $shop)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'duration' => 'required|integer|min:15'
            ]);

            $date = Carbon::parse($validated['date']);
            $duration = (int) $validated['duration'];
            $dayOfWeek = $date->dayOfWeek;

            // Get operating hours for the selected day
            $operatingHours = $shop->operatingHours()
                ->where('day', $dayOfWeek)
                ->where('is_open', true)
                ->first();

            if (!$operatingHours) {
                return response()->json(['slots' => [], 'message' => 'Shop is closed on this day'], 200);
            }

            // Get all employees
            $totalEmployees = $shop->employees()->count();
            if ($totalEmployees === 0) {
                return response()->json(['slots' => [], 'message' => 'No employees available'], 200);
            }

            // Get existing appointments for the day
            $appointments = $shop->appointments()
                ->whereDate('appointment_date', $date)
                ->whereIn('status', ['pending', 'accepted', 'confirmed', 'reschedule_requested'])
                ->with('services') // Load services to calculate real duration
                ->get();

            $timeSlots = [];
            $currentTime = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->open_time);
            $closeTime = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->close_time);

            // Ensure we don't create slots that would end after closing time
            $closeTime = $closeTime->subMinutes($duration);

            while ($currentTime <= $closeTime) {
                $slotEnd = $currentTime->copy()->addMinutes($duration);
                
                // Skip if current time is in the past
                if ($currentTime <= now()) {
                    $currentTime->addMinutes(30);
                    continue;
                }

                // Skip slot if it's during lunch break
                if ($operatingHours->has_lunch_break) {
                    $lunchStart = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->lunch_start);
                    $lunchEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $operatingHours->lunch_end);
                    
                    if (($currentTime >= $lunchStart && $currentTime < $lunchEnd) || 
                        ($slotEnd > $lunchStart && $slotEnd <= $lunchEnd) ||
                        ($currentTime < $lunchStart && $slotEnd > $lunchEnd)) {
                        $currentTime = $lunchEnd;
                        continue;
                    }
                }

                // Count busy employees in this time slot
                $busyEmployees = 0;
                foreach ($appointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
                    
                    // Calculate the actual duration from services
                    $serviceDuration = 0;
                    foreach ($appointment->services as $service) {
                        $serviceDuration += $service->duration;
                    }
                    
                    // Use calculated duration or default
                    $appointmentDuration = $serviceDuration > 0 ? $serviceDuration : 60;
                    $appointmentEnd = $appointmentStart->copy()->addMinutes($appointmentDuration);

                    // Debug logging
                    Log::debug('Checking appointment overlap in getTimeSlots', [
                        'slot_start' => $currentTime->format('Y-m-d H:i:s'),
                        'slot_end' => $slotEnd->format('Y-m-d H:i:s'),
                        'appointment_id' => $appointment->id,
                        'appointment_start' => $appointmentStart->format('Y-m-d H:i:s'),
                        'appointment_end' => $appointmentEnd->format('Y-m-d H:i:s'),
                        'appointment_duration' => $appointmentDuration
                    ]);

                    if (($currentTime >= $appointmentStart && $currentTime < $appointmentEnd) ||
                        ($slotEnd > $appointmentStart && $slotEnd <= $appointmentEnd) ||
                        ($currentTime < $appointmentStart && $slotEnd > $appointmentEnd)) {
                        $busyEmployees++;
                    }
                }

                $availableEmployees = $totalEmployees - $busyEmployees;

                // Only add slot if there are available employees
                if ($availableEmployees > 0) {
                    $timeSlots[] = [
                        'time' => $currentTime->format('g:i A'),
                        'available_employees' => $availableEmployees,
                        'total_employees' => $totalEmployees,
                        'end_time' => $slotEnd->format('g:i A') // Add end time for clarity
                    ];
                }

                // For services longer than 60 minutes, use a smarter increment to avoid too many overlapping slots
                // For shorter services, keep the 30-minute standard increment
                $incrementMinutes = min(max(30, $duration / 2), 60);
                $currentTime->addMinutes($incrementMinutes);
            }

            if (empty($timeSlots)) {
                return response()->json([
                    'slots' => [],
                    'message' => 'No available time slots for the selected date'
                ], 200);
            }

            return response()->json([
                'slots' => $timeSlots,
                'message' => null
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting time slots: ' . $e->getMessage());
            return response()->json([
                'slots' => [], 
                'message' => 'Failed to get time slots: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thankYou(Shop $shop)
    {
        // Check if we have booking details in session
        if (!session()->has('booking_details')) {
            return redirect()->route('home');
        }

        // Dynamically calculate if the shop is open based on operating hours
        $shopController = new \App\Http\Controllers\ShopController();
        $shop->setAttribute('is_open', $shopController->isShopOpen($shop));

        return view('booking.thank-you', [
            'shop' => $shop,
            'booking_details' => session('booking_details')
        ]);
    }

    public function downloadAcknowledgement(Request $request, Shop $shop)
    {
        try {
            $booking_details = session('booking_details');
            
            if (!$booking_details) {
                return back()->with('error', 'Booking details not found. Please try again.');
            }

            // Generate receipt view
            $pdf = \PDF::loadView('pdfs.acknowledgement-receipt', [
                'booking_details' => $booking_details
            ]);

            // Generate filename
            $filename = 'booking_acknowledgement_' . time() . '_' . Str::slug($booking_details['shop_name']) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error generating acknowledgement receipt: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate acknowledgement receipt. Please try again.');
        }
    }

    public function getAvailableEmployees(Request $request, Shop $shop)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'time' => 'required',
                'duration' => 'required|integer|min:15',
                'service_ids' => 'required|array',
                'service_ids.*' => 'exists:services,id'
            ]);

            // Parse date and time for appointment
            $date = $validated['date'];
            $time = $validated['time'];
            $duration = $validated['duration'];
            
            // Calculate appointment start and end times
            $appointmentDateTime = \Carbon\Carbon::parse("$date $time");
            $appointmentEndTime = (clone $appointmentDateTime)->addMinutes($duration);

            // Get existing appointments for this shop on the same date
            $existingAppointments = $shop->appointments()
                ->whereDate('appointment_date', $date)
                ->whereIn('status', ['pending', 'accepted', 'confirmed', 'reschedule_requested'])
                ->with('employee') // Load the employee relationship
                ->with('services') // Load the services relationship to calculate duration
                ->get();

            // Get all employees who can perform the requested services
            $employees = $shop->employees()
                ->whereHas('services', function ($query) use ($validated) {
                    $query->whereIn('services.id', $validated['service_ids']);
                })
                ->with(['schedules' => function ($query) use ($validated) {
                    $query->whereDate('start', $validated['date']);
                }])
                ->with(['timeOffRequests' => function ($query) use ($validated) {
                    $query->whereDate('start_date', '<=', $validated['date'])
                          ->whereDate('end_date', '>=', $validated['date'])
                          ->whereIn('status', ['pending', 'approved']);
                }])
                ->with(['staffRatings' => function($query) {
                    $query->select('employee_id', 'rating');
                }])
                ->with(['appointments' => function($query) use ($date) {
                    $query->whereDate('appointment_date', $date)
                          ->whereIn('status', ['pending', 'accepted', 'confirmed', 'reschedule_requested']);
                }])
                ->get()
                ->each(function ($employee) {
                    // Append the profile_photo_url attribute
                    $employee->append('profile_photo_url');
                    // Calculate average rating
                    $employee->rating = $employee->staffRatings->avg('rating') ?? 0;
                    $employee->ratings_count = $employee->staffRatings->count();
                    // Calculate total duration for each appointment
                    foreach ($employee->appointments as $appointment) {
                        // Ensure each appointment has a total_duration property
                        $services = $appointment->services;
                        $totalDuration = 0;
                        foreach ($services as $service) {
                            $totalDuration += $service->duration;
                        }
                        $appointment->total_duration = $totalDuration > 0 ? $totalDuration : 60; // Default to 60 mins
                    }
                    // Remove the staffRatings relationship from the response
                    unset($employee->staffRatings);
                });

            // Filter out employees who already have appointments during the requested time slot
            $availableEmployees = $employees->filter(function ($employee) use ($appointmentDateTime, $appointmentEndTime, $existingAppointments, $time) {
                // Check if employee has any overlapping appointments
                foreach ($existingAppointments as $appointment) {
                    // Skip if this appointment is not for this employee
                    if ($appointment->employee_id !== $employee->id) {
                        continue;
                    }
                    
                    // Parse appointment start and end times
                    $existingAppointmentStart = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
                    
                    // Get appointment total duration from services
                    $totalDuration = 0;
                    foreach ($appointment->services as $service) {
                        $totalDuration += $service->duration;
                    }
                    $appointmentDuration = $totalDuration > 0 ? $totalDuration : 60; // Default to 60 mins if not specified
                    
                    $existingAppointmentEnd = (clone $existingAppointmentStart)->addMinutes($appointmentDuration);
                    
                    \Log::info('Checking appointment overlap', [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'requested_date' => $appointmentDateTime->format('Y-m-d'),
                        'requested_time' => $time,
                        'requested_start' => $appointmentDateTime->format('Y-m-d H:i:s'),
                        'requested_end' => $appointmentEndTime->format('Y-m-d H:i:s'),
                        'existing_appointment_id' => $appointment->id,
                        'existing_date' => $appointment->appointment_date,
                        'existing_time' => $appointment->appointment_time,
                        'existing_start' => $existingAppointmentStart->format('Y-m-d H:i:s'),
                        'existing_end' => $existingAppointmentEnd->format('Y-m-d H:i:s'),
                        'duration' => $appointmentDuration
                    ]);
                    
                    // Check for overlap
                    if (
                        ($appointmentDateTime >= $existingAppointmentStart && $appointmentDateTime < $existingAppointmentEnd) ||
                        ($appointmentEndTime > $existingAppointmentStart && $appointmentEndTime <= $existingAppointmentEnd) ||
                        ($appointmentDateTime <= $existingAppointmentStart && $appointmentEndTime >= $existingAppointmentEnd)
                    ) {
                        \Log::info('Employee has overlapping appointment', [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'appointment_id' => $appointment->id
                        ]);
                        // This employee has an overlapping appointment
                        return false;
                    }
                }
                
                return true;
            });

            return response()->json([
                'success' => true,
                'employees' => $availableEmployees->values()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting available employees: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get available employees'
            ], 500);
        }
    }

    public function validateDiscount(Request $request, Shop $shop, $code)
    {
        try {
            $validated = $request->validate([
                'services' => 'required|array',
                'total' => 'required|numeric|min:0'
            ]);

            // Debug log the incoming request
            Log::info('Validating discount code:', [
                'code' => $code,
                'shop_id' => $shop->id,
                'services' => $validated['services'],
                'total' => $validated['total']
            ]);

            $services = Service::whereIn('id', array_values($validated['services']))->get();
            
            // Debug log the services found
            Log::info('Services found:', [
                'service_count' => $services->count(),
                'service_ids' => $services->pluck('id')->toArray()
            ]);

            $total = $validated['total'];
            $discountAmount = 0;
            $discountFound = false;

            foreach ($services as $service) {
                $activeDiscounts = $service->getActiveDiscounts();
                
                // Debug log active discounts for each service
                Log::info("Active discounts for service {$service->id}:", [
                    'service_name' => $service->name,
                    'active_discounts' => $activeDiscounts->map(function($discount) {
                        return [
                            'id' => $discount->id,
                            'voucher_code' => $discount->voucher_code,
                            'discount_type' => $discount->discount_type,
                            'discount_value' => $discount->discount_value,
                            'valid_from' => $discount->valid_from,
                            'valid_until' => $discount->valid_until,
                            'is_active' => $discount->is_active
                        ];
                    })->toArray()
                ]);
                
                // Case-insensitive voucher code comparison
                $voucherDiscount = $activeDiscounts->first(function($discount) use ($code) {
                    return strcasecmp($discount->voucher_code, $code) === 0;
                });
                
                if ($voucherDiscount) {
                    $discountFound = true;
                    // Calculate service-specific discount
                    $servicePrice = $service->getPriceForSize($request->input('pet_size', 'medium'));
                    $discountedPrice = $voucherDiscount->calculateDiscountedPrice($servicePrice);
                    $discountAmount += ($servicePrice - $discountedPrice);
                    
                    // Debug log discount calculation
                    Log::info("Discount calculation for service {$service->id}:", [
                        'service_price' => $servicePrice,
                        'discounted_price' => $discountedPrice,
                        'discount_amount' => $servicePrice - $discountedPrice
                    ]);
                }
            }

            if ($discountAmount > 0) {
                Log::info('Discount applied successfully:', [
                    'code' => $code,
                    'total_discount' => $discountAmount
                ]);
                
                return response()->json([
                    'success' => true,
                    'discount_amount' => $discountAmount,
                    'message' => 'Discount applied successfully'
                ]);
            }

            Log::warning('No valid discount found:', [
                'code' => $code,
                'discount_found' => $discountFound
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No valid discount found for this code'
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating discount: ' . $e->getMessage(), [
                'code' => $code,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating the discount'
            ], 500);
        }
    }

    // Get the user's pets filtered by type (exotic and regular)
    private function getPetsByType($user)
    {
        // Get all active (non-deceased) pets for the user
        $pets = $user->pets()->whereNull('death_date')->get();
        
        // Define exotic pet types (case-insensitive)
        $exoticTypes = ['bird', 'reptile', 'exotic', 'amphibian', 'small mammal', 'other'];
        
        // Split pets by type
        $exoticPets = $pets->filter(function($pet) use ($exoticTypes) {
            return in_array(strtolower($pet->type), $exoticTypes);
        });
        
        $regularPets = $pets->filter(function($pet) use ($exoticTypes) {
            return !in_array(strtolower($pet->type), $exoticTypes);
        });
        
        return [
            'exoticPets' => $exoticPets,
            'regularPets' => $regularPets
        ];
    }
} 