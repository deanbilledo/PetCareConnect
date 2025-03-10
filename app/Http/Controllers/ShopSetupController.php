<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Service;
use App\Models\OperatingHour;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\HasShop;
use Illuminate\Support\Facades\Log;

class ShopSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', HasShop::class]);
    }

    public function welcome()
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        return view('shop.setup.welcome');
    }

    public function details()
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        return view('shop.setup.details');
    }

    public function storeDetails(Request $request)
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'contact_email' => 'required|email',
            'contact_number' => 'required|string|max:20',
            'gallery.*' => 'required|image|mimes:jpeg,png|max:5120', // 5MB max
            'gallery' => 'array|max:6' // Maximum 6 images
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $shop = $user->shop;
                
                // Update shop details
                $shop->update([
                    'description' => $request->description,
                    'contact_email' => $request->contact_email,
                    'contact_number' => $request->contact_number
                ]);

                // Handle gallery images
                if ($request->hasFile('gallery')) {
                    foreach ($request->file('gallery') as $image) {
                        $path = $image->store('shop-gallery', 'public');
                        $shop->gallery()->create([
                            'image_path' => $path
                        ]);
                    }
                }
            });

            return redirect()->route('shop.setup.employees.index');
        } catch (\Exception $e) {
            Log::error('Error storing shop details: ' . $e->getMessage());
            return back()->with('error', 'Failed to save shop details. Please try again.')
                        ->withInput();
        }
    }

    public function services()
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        $shop = $user->shop;
        $employees = $shop->employees()->get();
        return view('shop.setup.services', compact('shop', 'employees'));
    }

    public function storeServices(Request $request)
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        // Preprocess the request data to ensure required array indices exist
        $services = $request->input('services', []);
        
        // Log raw input before processing
        \Log::info('Raw services input', ['services' => $services]);
        
        // Ensure numeric keys for services array to prevent missing indices
        $reindexedServices = [];
        foreach ($services as $service) {
            $reindexedServices[] = $service;
        }
        $services = $reindexedServices;
        
        // Handle each service
        foreach ($services as $index => $service) {
            // Default values for pet_types if missing or empty
            if (!isset($service['pet_types']) || !is_array($service['pet_types']) || empty($service['pet_types'])) {
                $services[$index]['pet_types'] = ['dogs', 'cats']; // Default values
            } else {
                // Ensure numeric keys for pet_types and cast all values to strings
                $petTypes = [];
                foreach (array_values($service['pet_types']) as $i => $type) {
                    $petTypes[$i] = (string)$type; // Explicitly cast to string
                }
                $services[$index]['pet_types'] = $petTypes;
            }
            
            // Default values for size_ranges if missing or empty
            if (!isset($service['size_ranges']) || !is_array($service['size_ranges']) || empty($service['size_ranges'])) {
                $services[$index]['size_ranges'] = ['small', 'medium', 'large']; // Default values
            } else {
                // Ensure numeric keys for size_ranges and cast all values to strings
                $sizeRanges = [];
                foreach (array_values($service['size_ranges']) as $i => $size) {
                    $sizeRanges[$i] = (string)$size; // Explicitly cast to string
                }
                $services[$index]['size_ranges'] = $sizeRanges;
            }
        }
        
        // Replace the original services data with our preprocessed data
        $request->merge(['services' => $services]);
        
        // Log the preprocessed request data for debugging
        \Log::info('Preprocessed service submission data', [
            'services' => $request->services,
            'service_count' => count($request->services)
        ]);
        
        // More detailed logging for troubleshooting
        foreach ($request->services as $index => $service) {
            \Log::info("Service $index details", [
                'name' => $service['name'] ?? 'Not set',
                'pet_types' => isset($service['pet_types']) ? $service['pet_types'] : 'Not set',
                'pet_types_types' => isset($service['pet_types']) ? array_map(function($val) { 
                    return gettype($val) . '(' . (is_string($val) ? $val : json_encode($val)) . ')'; 
                }, (array)$service['pet_types']) : 'N/A',
                'size_ranges' => isset($service['size_ranges']) ? $service['size_ranges'] : 'Not set',
                'size_ranges_types' => isset($service['size_ranges']) ? array_map(function($val) { 
                    return gettype($val) . '(' . (is_string($val) ? $val : json_encode($val)) . ')'; 
                }, (array)$service['size_ranges']) : 'N/A'
            ]);
        }
        
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.name' => 'required|string',
            'services.*.category' => 'required|string',
            'services.*.description' => 'nullable|string',
            'services.*.pet_types' => 'required|array|min:1',
            'services.*.pet_types.*' => 'string',
            'services.*.size_ranges' => 'required|array|min:1',
            'services.*.size_ranges.*' => 'string',
            'services.*.exotic_pet_service' => 'nullable|boolean',
            'services.*.exotic_pet_species' => 'nullable|array',
            'services.*.exotic_pet_species.*' => 'nullable|string',
            'services.*.special_requirements' => 'nullable|string',
            'services.*.base_price' => 'required|numeric|min:0',
            'services.*.duration' => 'required|integer|min:15',
            'services.*.variable_pricing' => 'nullable|array',
            'services.*.variable_pricing.*.size' => 'required_with:services.*.variable_pricing|string',
            'services.*.variable_pricing.*.price' => 'required_with:services.*.variable_pricing|numeric|min:0',
            'services.*.add_ons' => 'nullable|array',
            'services.*.add_ons.*.name' => 'required_with:services.*.add_ons|string',
            'services.*.add_ons.*.price' => 'required_with:services.*.add_ons|numeric|min:0',
            'services.*.employee_ids' => 'nullable|array',
            'services.*.employee_ids.*' => 'nullable|string|exists:employees,id'
        ]);

        $shop = $user->shop;

        try {
            DB::transaction(function () use ($validated, $shop) {
                // Clear existing services
                $shop->services()->delete();

                // Add new services
                foreach ($validated['services'] as $serviceData) {
                    // Extract employee IDs before creating service
                    $employeeIds = $serviceData['employee_ids'] ?? [];
                    
                    // Filter out null or empty values
                    $employeeIds = array_filter($employeeIds, function($id) {
                        return !is_null($id) && $id !== '';
                    });
                    
                    unset($serviceData['employee_ids']);

                    // Ensure required array fields have proper format
                    $serviceData['pet_types'] = array_values((array)$serviceData['pet_types']);
                    $serviceData['size_ranges'] = array_values((array)$serviceData['size_ranges']);

                    // Log the service data before creation for debugging
                    \Log::info('Creating service', [
                        'name' => $serviceData['name'],
                        'pet_types' => $serviceData['pet_types'], 
                        'size_ranges' => $serviceData['size_ranges']
                    ]);

                    // Create service
                    $service = $shop->services()->create([
                        'name' => $serviceData['name'],
                        'category' => $serviceData['category'],
                        'description' => $serviceData['description'] ?? null,
                        'pet_types' => $serviceData['pet_types'],
                        'size_ranges' => $serviceData['size_ranges'],
                        'exotic_pet_service' => $serviceData['exotic_pet_service'] ?? false,
                        'exotic_pet_species' => $serviceData['exotic_pet_species'] ?? null,
                        'special_requirements' => $serviceData['special_requirements'] ?? null,
                        'base_price' => $serviceData['base_price'],
                        'duration' => $serviceData['duration'],
                        'variable_pricing' => $serviceData['variable_pricing'] ?? null,
                        'add_ons' => $serviceData['add_ons'] ?? null,
                        'status' => 'active'
                    ]);

                    // Attach employees to the service ONLY if there are valid employee IDs
                    if (!empty($employeeIds)) {
                        \Log::info('Attaching employees to service', ['service_id' => $service->id, 'employee_ids' => $employeeIds]);
                        $service->employees()->attach($employeeIds);
                    } else {
                        \Log::info('No employees to attach to service', ['service_id' => $service->id]);
                    }
                }
            });

            return redirect()->route('shop.setup.hours');
        } catch (\Exception $e) {
            Log::error('Error storing services: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to save services. Please try again.')
                        ->withInput();
        }
    }

    public function hours()
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        $shop = $user->shop;
        return view('shop.setup.hours', compact('shop'));
    }

    public function storeHours(Request $request)
    {
        $user = auth()->user();
        
        // If setup is already completed, redirect to dashboard
        if ($user->setup_completed) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }
        
        try {
            $validated = $request->validate([
                'hours.*.day' => 'required|integer|between:0,6',
                'hours.*.is_open' => 'required|boolean',
                'hours.*.open_time' => 'required_if:hours.*.is_open,1|nullable|date_format:H:i',
                'hours.*.close_time' => 'required_if:hours.*.is_open,1|nullable|date_format:H:i|after:hours.*.open_time',
                'hours.*.has_lunch_break' => 'boolean',
                'hours.*.lunch_start' => 'required_if:hours.*.has_lunch_break,1|nullable|date_format:H:i|after:hours.*.open_time|before:hours.*.close_time',
                'hours.*.lunch_end' => 'required_if:hours.*.has_lunch_break,1|nullable|date_format:H:i|after:hours.*.lunch_start|before:hours.*.close_time',
            ]);

            $shop = $user->shop;

            DB::transaction(function () use ($validated, $shop, $user) {
                // Delete existing hours
                $shop->operatingHours()->delete();

                // Create new operating hours
                foreach ($validated['hours'] as $hour) {
                    $shop->operatingHours()->create([
                        'day' => $hour['day'],
                        'is_open' => $hour['is_open'],
                        'open_time' => $hour['is_open'] ? $hour['open_time'] : null,
                        'close_time' => $hour['is_open'] ? $hour['close_time'] : null,
                        'has_lunch_break' => $hour['is_open'] && ($hour['has_lunch_break'] ?? false),
                        'lunch_start' => $hour['is_open'] && ($hour['has_lunch_break'] ?? false) ? $hour['lunch_start'] : null,
                        'lunch_end' => $hour['is_open'] && ($hour['has_lunch_break'] ?? false) ? $hour['lunch_end'] : null,
                    ]);
                }

                // Mark setup as completed
                $user->setup_completed = true;
                $user->save();
            });

            // Set shop mode and redirect to dashboard
            session(['shop_mode' => true]);

            return redirect()->route('shop.dashboard')
                ->with('success', 'Shop setup completed successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to save operating hours: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to save operating hours. Please try again.']);
        }
    }
} 
