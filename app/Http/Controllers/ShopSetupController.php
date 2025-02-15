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
        
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.name' => 'required|string',
            'services.*.category' => 'required|string',
            'services.*.description' => 'nullable|string',
            'services.*.pet_types' => 'required|array',
            'services.*.pet_types.*' => 'string',
            'services.*.size_ranges' => 'required|array',
            'services.*.size_ranges.*' => 'string',
            'services.*.exotic_pet_service' => 'boolean',
            'services.*.exotic_pet_species' => 'required_if:services.*.exotic_pet_service,true|array|nullable',
            'services.*.exotic_pet_species.*' => 'string',
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
            'services.*.employee_ids.*' => 'exists:employees,id'
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
                    unset($serviceData['employee_ids']);

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

                    // Attach employees to the service
                    if (!empty($employeeIds)) {
                        $service->employees()->attach($employeeIds);
                    }
                }
            });

            return redirect()->route('shop.setup.hours');
        } catch (\Exception $e) {
            Log::error('Error storing services: ' . $e->getMessage());
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
