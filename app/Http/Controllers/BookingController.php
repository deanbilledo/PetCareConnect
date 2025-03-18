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
            // CRITICAL FIX: Determine if this is a GET or POST request and handle accordingly
            $isGetRequest = $request->isMethod('get');
            
            Log::info('BookingController@selectDateTime - Request method: ' . $request->method(), [
                'is_get' => $isGetRequest,
                'has_form_data' => !empty($request->all())
            ]);
            
            if ($isGetRequest) {
                // For GET requests, load data from session
                $bookingData = session('booking', []);
                
                // Log what data we have for debugging
                Log::info('Session booking data for GET select-datetime:', [
                    'has_booking_data' => !empty($bookingData),
                    'has_pet_ids' => isset($bookingData['pet_ids']),
                    'has_pet_services' => isset($bookingData['pet_services']),
                    'appointment_type' => $bookingData['appointment_type'] ?? 'not set'
                ]);
                
                // If we don't have the necessary data to proceed, redirect to the beginning
                if (empty($bookingData) || !isset($bookingData['pet_ids']) || !isset($bookingData['pet_services'])) {
                    Log::warning('Missing required booking data for select-datetime GET request');
                    
                    // Redirect to the booking process start
                    return redirect()->route('booking.process', $shop)
                        ->with('error', 'Please start the booking process from the beginning');
                }
                
                // Get pet and service IDs from session
                $petIds = $bookingData['pet_ids'];
                $serviceIds = array_values($bookingData['pet_services'] ?? []);
            } else {
                // For POST requests, validate input data
                $validated = $request->validate([
                    'pet_ids' => 'required|array',
                    'pet_ids.*' => 'exists:pets,id',
                    'services' => 'required|array',
                    'services.*' => 'exists:services,id',
                    'add_ons' => 'nullable|array'
                ]);
                
                // Get pet and service IDs from request
                $petIds = $validated['pet_ids'];
                $serviceIds = $validated['services'];
                
                // Create pet services mapping for session storage
                $petServices = array_combine($validated['pet_ids'], $validated['services']);
                
                // Get existing booking data to preserve appointment_type
                $existingBookingData = session('booking', []);
                
                // Store in session
                $bookingData = [
                    'pet_ids' => $validated['pet_ids'],
                    'pet_services' => $petServices,
                    'appointment_type' => $existingBookingData['appointment_type'] ?? 'single', // Default to single if not set
                    'add_ons' => $request->input('add_ons', []) // Store add-ons data
                ];
            }

            // Common code for both GET and POST requests
            
            // Load shop's operating hours
            $operatingHours = $shop->operatingHours()
                ->orderBy('day')
                ->get();

            // Get selected services for duration calculation
            $selectedServices = $shop->services()
                ->whereIn('id', $serviceIds)
                ->where('status', 'active')
                ->get();

            if ($selectedServices->isEmpty()) {
                return back()->with('error', 'Selected services are not available.');
            }

            // Calculate total duration
            $totalDuration = $selectedServices->sum('duration');

            // Load pets data
            $pets = auth()->user()->pets()
                ->whereIn('id', $petIds)
                ->get();
                
            // Add pets to session data if coming from POST
            if (!$isGetRequest) {
                $bookingData['pets'] = $pets;
                session(['booking' => $bookingData]);
                
                // Debug log
                Log::info('Booking data stored in session:', [
                    'booking_data' => $bookingData,
                    'session_data' => session('booking'),
                    'add_ons' => $bookingData['add_ons'] ?? []
                ]);
            }

            // Pass session data directly to view
            return view('booking.select-datetime', [
                'shop' => $shop,
                'operatingHours' => $operatingHours,
                'totalDuration' => $totalDuration,
                'bookingData' => $bookingData,
                'pets' => $pets // Explicitly pass pets collection to the view
            ]);

        } catch (\Exception $e) {
            Log::error('Error in selectDateTime: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'There was an error loading the booking form. Please try again.');
        }
    }

    public function showConfirm(Request $request, Shop $shop)
    {
        try {
            // CRITICAL FIX: Add comprehensive debugging for all request data
            Log::info('BookingController@showConfirm - RECEIVED ALL REQUEST DATA:', [
                'all_request_data' => $request->all(),
                'request_method' => $request->method(),
                'request_path' => $request->path(),
                'has_employee_id' => $request->has('employee_id'),
                'has_employee_assignment' => $request->has('employee_assignment'),
                'employee_id_value' => $request->input('employee_id'),
                'employee_assignment_value' => $request->input('employee_assignment'),
            ]);
            
            // Enhanced debug logging for troubleshooting employee data issues
            Log::info('BookingController@showConfirm - EMPLOYEE DATA FIELDS:', [
                'employee_id' => $request->input('employee_id'),
                'hidden_employee_id' => $request->input('hidden_employee_id'),
                'employee_id_backup' => $request->input('employee_id_backup'),
                'employee_assignment' => $request->input('employee_assignment'),
            ]);
            
            // Log all request keys that might contain employee data
            $employeeRelatedKeys = [];
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'employee') !== false || strpos($key, 'pet_employee') !== false) {
                    $employeeRelatedKeys[$key] = $value;
                }
            }
            
            Log::info('Employee-related form fields:', $employeeRelatedKeys);
            
            // Log existing session data
            Log::info('Existing session data before processing:', [
                'booking_data' => session('booking'),
                'has_employee_data' => isset(session('booking')['employee_data']),
                'has_employee' => isset(session('booking')['employee']),
                'employee_assignment' => session('booking')['employee_assignment'] ?? 'not set',
            ]);
            
            // Ensure we have the basic data before proceeding
            if (!$request->has('pet_ids') || !$request->has('services') || 
                !$request->has('appointment_date') || !$request->has('appointment_time')) {
                Log::error('Missing basic booking data', ['request' => $request->all()]);
                return back()->with('error', 'Missing required booking information. Please try again.');
            }
            
            // CRITICAL FIX: If employee_assignment or employee_id is missing, redirect back with error
            if (!$request->has('employee_assignment')) {
                Log::error('Missing employee_assignment in request', ['request' => $request->all()]);
                return redirect()->route('booking.select-datetime', $shop)
                    ->with('error', 'Please select an employee assignment type before proceeding.');
            }
            
            // Validate base data
            $validated = $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required',
                'employee_assignment' => 'required|in:single,multiple',
            ]);

            // Determine employee assignment type (single or multiple)
            $employeeAssignment = $validated['employee_assignment'];
            Log::info('Employee assignment type: ' . $employeeAssignment);
            
            // CRITICAL FIX: Get the employee data from the request with fallbacks
            if ($employeeAssignment === 'single') {
                $employeeId = $request->input('employee_id') ?? 
                              $request->input('hidden_employee_id') ?? 
                              $request->input('employee_id_backup');
                
                if (empty($employeeId)) {
                    Log::error('No employee ID found for single assignment', [
                        'employee_id' => $request->input('employee_id'),
                        'hidden_employee_id' => $request->input('hidden_employee_id'),
                        'employee_id_backup' => $request->input('employee_id_backup')
                    ]);
                    
                    return redirect()->route('booking.select-datetime', $shop)
                        ->with('error', 'Please select an employee for your service before proceeding.');
                }
                
                // CRITICAL FIX: Load employee data immediately to ensure it exists
                $employee = $shop->employees()->find($employeeId);
                if (!$employee) {
                    Log::error("Employee not found with ID: {$employeeId}");
                    return redirect()->route('booking.select-datetime', $shop)
                        ->with('error', 'Selected employee is not available. Please select another employee.');
                }
                
                Log::info('Found employee for single assignment', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name
                ]);
            } else {
                // For multiple assignment, check for pet_employee_ids
                $petEmployeeIds = [];
                
                // Get all possible pet employee ID inputs
                foreach ($request->all() as $key => $value) {
                    if (strpos($key, 'pet_employee_ids') === 0) {
                        $petEmployeeIds = $value;
                        break;
                    }
                }
                
                // Also check hidden fields as backup
                if (empty($petEmployeeIds)) {
                    foreach ($request->all() as $key => $value) {
                        if (strpos($key, 'hidden_pet_employee_') === 0 && !empty($value)) {
                            $petId = str_replace('hidden_pet_employee_', '', $key);
                            $petEmployeeIds[$petId] = $value;
                        }
                    }
                }
                
                Log::info('Pet employee IDs collected:', [
                    'pet_employee_ids' => $petEmployeeIds,
                    'pet_count' => count($validated['pet_ids'])
                ]);
                
                if (empty($petEmployeeIds)) {
                    Log::error('No pet employee IDs found for multiple assignment', [
                        'request_keys' => array_keys($request->all())
                    ]);
                    
                    return redirect()->route('booking.select-datetime', $shop)
                        ->with('error', 'Please select employees for your pets before proceeding.');
                }
                
                // Extract all employee IDs to fetch employee data
                $employeeIds = array_values(array_filter($petEmployeeIds));
                
                // Get all needed employees at once
                $employees = $shop->employees()->whereIn('id', $employeeIds)->get();
                
                if ($employees->isEmpty()) {
                    return redirect()->route('booking.select-datetime', $shop)
                        ->with('error', 'No employees found. Please select employees again.');
                }
                
                // Build employee data for each pet
                $employeeData = [];
                foreach ($petEmployeeIds as $petId => $employeeId) {
                    if (empty($employeeId)) continue;
                    
                    $employee = $employees->firstWhere('id', $employeeId);
                    if ($employee) {
                        $employeeData[$petId] = [
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'position' => $employee->position ?? 'Groomer',
                            'profile_photo_url' => $employee->profile_photo_url ?? asset('images/default-avatar.png')
                        ];
                    }
                }
                
                // Log the final employee data
                Log::info('Final employee data for multiple assignment:', [
                    'employee_data' => $employeeData,
                    'employee_data_count' => count($employeeData),
                    'pet_ids_count' => count($validated['pet_ids'])
                ]);
            }

            // Load appointment data
            $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);
            
            // Load pets and services
            $pets = Pet::whereIn('id', $validated['pet_ids'])
                ->where('user_id', auth()->id())
                ->get();
            
            $services = $shop->services()
                ->whereIn('id', $validated['services'])
                ->where('status', 'active')
                ->get();

            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are no longer available.');
            }

            // Create pet services mapping
            $petServices = array_combine($validated['pet_ids'], $validated['services']);

            // Get existing booking data to preserve add-ons
            $existingBookingData = session('booking', []);
            
            // Create booking data with essential information
            $bookingData = [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'appointment_type' => $existingBookingData['appointment_type'] ?? 'single',
                'employee_assignment' => $employeeAssignment,
                'add_ons' => $existingBookingData['add_ons'] ?? []
            ];
            
            // Add employee data based on assignment type
            if ($employeeAssignment === 'single') {
                // Single employee assignment
                $bookingData['employee_id'] = $employeeId;
                $bookingData['employee'] = [
                    'id' => $employee->id,
                    'name' => $employee->name ?? 'Unknown',
                    'position' => $employee->position ?? 'Groomer',
                    'profile_photo_url' => $employee->profile_photo_url ?? asset('images/default-avatar.png')
                ];
                // Initialize empty employee_data for consistency
                $bookingData['employee_data'] = [];
            } else {
                // Multiple employee assignment
                $bookingData['employee_data'] = [];
                
                // Explicitly set these to null for consistent structure
                $bookingData['employee'] = null;
                $bookingData['employee_id'] = null;
            }
            
            // Store booking data in session
            session(['booking' => $bookingData]);

            // Log final booking data
            Log::info('Booking data stored in session', [
                'booking_data' => $bookingData,
                'session_data' => session('booking'),
                'employee_assignment_type' => $employeeAssignment,
                'has_employee_data' => !empty($bookingData['employee_data']),
                'has_employee' => !empty($bookingData['employee'])
            ]);
            
            // Return view with all necessary data
            return view('booking.confirm', [
                'shop' => $shop,
                'bookingData' => $bookingData,
                'pets' => $pets,
                'services' => $services,
                'appointmentDateTime' => $appointmentDateTime,
                'total' => 0  // This will be calculated in the view
            ]);
        } catch (\Exception $e) {
            Log::error('Error in showConfirm: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'There was an error processing your booking. Please try again.');
        }
    }

    public function confirm(Request $request, Shop $shop)
    {
        try {
            // CRITICAL FIX: Log the incoming request data
            Log::info('BookingController@confirm - Request data:', [
                'all_data' => $request->all(),
                'method' => $request->method(),
                'has_employee_assignment' => $request->has('employee_assignment'),
                'employee_assignment_value' => $request->input('employee_assignment'),
            ]);
            
            // Log existing session data
            Log::info('BookingController@confirm - Existing session data:', [
                'booking_data' => session('booking'),
                'has_employee_data' => isset(session('booking')['employee_data']),
                'has_employee' => isset(session('booking')['employee']),
                'employee_assignment' => session('booking')['employee_assignment'] ?? 'not set',
            ]);

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

            // CRITICAL FIX: Get existing booking data to KEEP employee information
            $existingBookingData = session('booking', []);
            
            // CRITICAL FIX: Check that we have employee data and log it
            $hasEmployeeData = 
                isset($existingBookingData['employee_assignment']) && 
                (
                    (
                        $existingBookingData['employee_assignment'] === 'single' && 
                        isset($existingBookingData['employee_id']) && 
                        isset($existingBookingData['employee'])
                    ) ||
                    (
                        $existingBookingData['employee_assignment'] === 'multiple' && 
                        isset($existingBookingData['employee_data']) && 
                        !empty($existingBookingData['employee_data'])
                    )
                );
                
            Log::info('BookingController@confirm - Employee data check:', [
                'has_employee_data' => $hasEmployeeData,
                'employee_assignment' => $existingBookingData['employee_assignment'] ?? 'not set',
                'employee_id' => $existingBookingData['employee_id'] ?? null,
                'has_employee_obj' => isset($existingBookingData['employee']),
                'has_employee_data_array' => isset($existingBookingData['employee_data']),
                'employee_data_count' => isset($existingBookingData['employee_data']) ? count($existingBookingData['employee_data']) : 0
            ]);
                
            if (!$hasEmployeeData) {
                Log::error('Missing employee data in session', [
                    'session_data' => $existingBookingData
                ]);
                return redirect()->route('booking.select-datetime', $shop)
                    ->with('error', 'Missing employee information. Please select employees before proceeding.');
            }
            
            // Update booking data WITHOUT losing employee info
            $bookingData = array_merge($existingBookingData, [
                'pet_ids' => $validated['pet_ids'],
                'pet_services' => $petServices,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time']
            ]);
            
            // CRITICAL FIX: Ensure the employee data is still there
            Log::info('BookingController@confirm - Final booking data check:', [
                'has_employee_assignment' => isset($bookingData['employee_assignment']),
                'employee_assignment' => $bookingData['employee_assignment'] ?? 'not set',
                'has_employee_id' => isset($bookingData['employee_id']),
                'employee_id' => $bookingData['employee_id'] ?? null,
                'has_employee_data' => isset($bookingData['employee_data']),
                'employee_data_count' => isset($bookingData['employee_data']) ? count($bookingData['employee_data']) : 0
            ]);
            
            // Save complete booking data to session
            session(['booking' => $bookingData]);

            Log::info('Confirm booking data:', [
                'booking_data' => $bookingData,
                'pets' => $pets->pluck('name', 'id'),
                'services' => $services->pluck('name', 'id'),
                'has_employee_data' => isset($bookingData['employee_data']),
                'has_employee' => isset($bookingData['employee']),
                'employee_assignment' => $bookingData['employee_assignment'] ?? 'not set'
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
            Log::info('BookingController@store - Starting booking process with request:', [
                'request_data' => $request->all()
            ]);

            // Validate basic booking data
            $validated = $request->validate([
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'notes' => 'nullable|string|max:500'
            ]);
            
            // Get booking data from session
            $bookingData = session('booking', []);
            
            // Make sure we have the employee assignment type
            $employeeAssignment = $bookingData['employee_assignment'] ?? 'single';
            
            Log::info('store() - Employee assignment info:', [
                'assignment_type' => $employeeAssignment,
                'has_employee_data' => isset($bookingData['employee_data']),
                'employee_id' => $bookingData['employee_id'] ?? null,
                'employee_data_count' => isset($bookingData['employee_data']) ? count($bookingData['employee_data']) : 0
            ]);
            
            // Load pets
            $pets = Pet::whereIn('id', $validated['pet_ids'])
                ->where('user_id', auth()->id())
                ->get();

            if ($pets->isEmpty()) {
                return back()->with('error', 'No valid pets found for booking.');
            }
            
            // Load services
            $services = $shop->services()
                ->whereIn('id', $validated['services'])
                ->where('status', 'active')
                ->get();
                
            if ($services->isEmpty()) {
                return back()->with('error', 'Selected services are not available.');
            }
            
            // Create appointment date time
            $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);
            
            // Get service IDs for each pet
            $petServices = $bookingData['pet_services'] ?? [];
            
            // Map pet IDs to service IDs
            $petServiceMap = [];
            foreach ($validated['pet_ids'] as $index => $petId) {
                $serviceId = $validated['services'][$index] ?? null;
                if ($serviceId) {
                    $petServiceMap[$petId] = $serviceId;
                }
            }
            
            // Create appointments based on employee assignment type
            $createdAppointments = [];
            
            if ($employeeAssignment === 'multiple') {
                // Multiple employees - one appointment per pet with different employees
                $employeeData = $bookingData['employee_data'] ?? [];
                
                Log::info('Creating multiple appointments with different employees:', [
                    'employee_data' => $employeeData,
                    'pet_count' => count($pets)
                ]);
                
                foreach ($pets as $pet) {
                    $petId = $pet->id;
                    $serviceId = $petServices[$petId] ?? null;
                    $service = $services->firstWhere('id', $serviceId);
                    
                    if (!$service) {
                        Log::error("Service not found for pet {$petId}");
                        continue;
                    }
                    
                    // Get employee for this pet
                    $petEmployeeData = $employeeData[$petId] ?? null;
                    $employeeId = $petEmployeeData ? $petEmployeeData['id'] : null;
                    
                    if (!$employeeId) {
                        Log::error("Employee ID not found for pet {$petId}");
                        continue;
                    }
                    
                    // Get price based on pet size
                    $price = $this->calculateServicePrice($service, $pet);
                    
                    // Create appointment
                    $appointment = new Appointment([
                        'shop_id' => $shop->id,
                        'user_id' => auth()->id(),
                        'pet_id' => $petId,
                        'employee_id' => $employeeId,
                        'service_id' => $serviceId,
                        'appointment_date' => $appointmentDateTime->format('Y-m-d'),
                        'appointment_time' => $appointmentDateTime->format('g:i A'),
                        'status' => 'pending',
                        'payment_status' => 'pending',
                        'price' => $price,
                        'notes' => $validated['notes'] ?? null,
                        'reference_number' => $this->generateReferenceNumber()
                    ]);
                    
                    $appointment->save();
                    $createdAppointments[] = $appointment;
                    
                    Log::info("Created appointment for pet {$petId} with employee {$employeeId}", [
                        'appointment_id' => $appointment->id,
                        'reference_number' => $appointment->reference_number
                    ]);
                }
            } else {
                // Single employee for all pets - could be one appointment for each pet with the same employee
                $employeeId = $bookingData['employee_id'] ?? null;
                
                if (!$employeeId) {
                    Log::error('Employee ID not found for single assignment');
                    return back()->with('error', 'Employee information is missing. Please try again.');
                }
                
                // Get employee details from database
                $employee = $shop->employees()->find($employeeId);
                if (!$employee) {
                    Log::error("Employee not found with ID: {$employeeId}");
                    return back()->with('error', 'Selected employee not found. Please choose another employee.');
                }
                
                Log::info('Creating appointments with same employee:', [
                    'employee_id' => $employeeId,
                    'employee_name' => $employee->name,
                    'pet_count' => count($pets)
                ]);
                
                foreach ($pets as $pet) {
                    $petId = $pet->id;
                    $serviceId = $petServices[$petId] ?? null;
                    $service = $services->firstWhere('id', $serviceId);
                    
                    if (!$service) {
                        Log::error("Service not found for pet {$petId}");
                        continue;
                    }
                    
                    // Get price based on pet size
                    $price = $this->calculateServicePrice($service, $pet);
                    
                    // Create appointment
                    $appointment = new Appointment([
                        'shop_id' => $shop->id,
                        'user_id' => auth()->id(),
                        'pet_id' => $petId,
                        'employee_id' => $employeeId,
                        'service_id' => $serviceId,
                        'appointment_date' => $appointmentDateTime->format('Y-m-d'),
                        'appointment_time' => $appointmentDateTime->format('g:i A'),
                        'status' => 'pending',
                        'payment_status' => 'pending',
                        'price' => $price,
                        'notes' => $validated['notes'] ?? null,
                        'reference_number' => $this->generateReferenceNumber()
                    ]);
                    
                    $appointment->save();
                    $createdAppointments[] = $appointment;
                    
                    Log::info("Created appointment for pet {$petId} with employee {$employeeId}", [
                        'appointment_id' => $appointment->id,
                        'reference_number' => $appointment->reference_number
                    ]);
                }
            }

                // Prepare booking details for session
                $bookingDetails = [
                    'shop_name' => $shop->name,
                    'date' => $appointmentDateTime->format('F j, Y'),
                    'time' => $appointmentDateTime->format('g:i A'),
                'services' => $createdAppointments,
                'total_amount' => array_sum(array_column($createdAppointments, 'price')),
                'original_total' => array_sum(array_column($createdAppointments, 'price')),
                'discount_amount' => 0,
                'voucher_code' => null,
                    'appointment_type' => $bookingData['appointment_type'],
                'employee_assignment' => $employeeAssignment,
                'appointments' => $createdAppointments
                ];
                
                // Add employee information based on assignment type
            if ($employeeAssignment === 'single' && $employeeId) {
                    $bookingDetails['employee'] = [
                        'id' => $employee->id,
                        'name' => $employee->name ?? 'Unknown',
                        'position' => $employee->position ?? 'Groomer',
                        'profile_photo_url' => $employee->profile_photo_url ?? asset('images/default-avatar.png')
                    ];
                } else {
                $bookingDetails['employee_data'] = $employeeData;
                }
                
                // Store booking details in session for thank you page
                session(['booking_details' => $bookingDetails]);
                
                // Log the final booking details
                Log::info('Final booking details stored in session:', [
                    'booking_details' => $bookingDetails
                ]);

                // Clear booking session data
                session()->forget('booking');

                // Create notifications for each appointment
                $appointments = Appointment::where('user_id', auth()->id())
                                         ->where('shop_id', $shop->id)
                                         ->where('status', 'pending')
                                         ->whereNull('viewed_at')
                                     ->whereIn('pet_id', array_column($createdAppointments, 'pet_id'))
                                         ->orderBy('created_at', 'desc')
                                         ->get();
                
                foreach($appointments as $appointment) {
                    AppointmentNotificationService::createNewAppointmentNotification($appointment);
                }

                return redirect()->route('booking.thank-you', $shop)
                    ->with('success', 'Your appointment has been booked successfully!');

        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
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
                // First check if employee has any time off requests for this date
                if ($employee->timeOffRequests->isNotEmpty()) {
                    foreach ($employee->timeOffRequests as $timeOff) {
                        // Employee has pending or approved time off on this date
                        if ($timeOff->status === 'pending' || $timeOff->status === 'approved') {
                            \Log::info('Employee has time off on requested date', [
                                'employee_id' => $employee->id,
                                'employee_name' => $employee->name,
                                'time_off_id' => $timeOff->id,
                                'start_date' => $timeOff->start_date,
                                'end_date' => $timeOff->end_date,
                                'status' => $timeOff->status
                            ]);
                            return false;
                        }
                    }
                }
                
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

    // Helper method to calculate service price based on pet size
    private function calculateServicePrice($service, $pet)
    {
        // Default to base price
        $price = $service->base_price; 
        
        // If variable pricing is available, use it
        if ($service && !empty($service->variable_pricing)) {
            // Convert variable_pricing from string to array if needed
            $variablePricing = is_string($service->variable_pricing) ? 
                json_decode($service->variable_pricing, true) : 
                $service->variable_pricing;
            
            // Find matching size price
            $sizePrice = collect($variablePricing)->first(function($pricing) use ($pet) {
                return strtolower($pricing['size']) === strtolower($pet->size_category);
            });
            
            if ($sizePrice && isset($sizePrice['price'])) {
                $price = (float) $sizePrice['price'];
            }
        }
        
        return $price;
    }
    
    // Helper method to generate a reference number for appointments
    private function generateReferenceNumber()
    {
        $prefix = 'PCC'; // PetCareConnect prefix
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        
        return $prefix . $timestamp . $random;
    }
} 