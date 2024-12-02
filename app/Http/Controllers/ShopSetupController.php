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
        return view('shop.setup.services', compact('shop'));
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
            'services.*.breed_specific' => 'boolean',
            'services.*.special_requirements' => 'nullable|string',
            'services.*.base_price' => 'required|numeric|min:0',
            'services.*.duration' => 'required|integer|min:15',
            'services.*.variable_pricing' => 'nullable|array',
            'services.*.variable_pricing.*.size' => 'required_with:services.*.variable_pricing|string',
            'services.*.variable_pricing.*.price' => 'required_with:services.*.variable_pricing|numeric|min:0',
            'services.*.add_ons' => 'nullable|array',
            'services.*.add_ons.*.name' => 'required_with:services.*.add_ons|string',
            'services.*.add_ons.*.price' => 'required_with:services.*.add_ons|numeric|min:0'
        ]);

        $shop = $user->shop;

        try {
            DB::transaction(function () use ($validated, $shop) {
                // Clear existing services
                $shop->services()->delete();

                // Add new services
                foreach ($validated['services'] as $service) {
                    $shop->services()->create([
                        'name' => $service['name'],
                        'category' => $service['category'],
                        'description' => $service['description'] ?? null,
                        'pet_types' => $service['pet_types'],
                        'size_ranges' => $service['size_ranges'],
                        'breed_specific' => $service['breed_specific'] ?? false,
                        'special_requirements' => $service['special_requirements'] ?? null,
                        'base_price' => $service['base_price'],
                        'duration' => $service['duration'],
                        'variable_pricing' => $service['variable_pricing'] ?? null,
                        'add_ons' => $service['add_ons'] ?? null,
                        'status' => 'active'
                    ]);
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
                'hours' => 'required|array',
                'hours.*.day' => 'required|integer|between:0,6',
                'hours.*.is_open' => 'required|in:0,1',
                'hours.*.open_time' => 'required_if:hours.*.is_open,1|nullable|date_format:H:i',
                'hours.*.close_time' => 'required_if:hours.*.is_open,1|nullable|date_format:H:i|after:hours.*.open_time'
            ]);

            $shop = $user->shop;

            DB::transaction(function () use ($validated, $shop, $user) {
                // Clear existing hours
                $shop->operatingHours()->delete();

                // Add new hours
                foreach ($validated['hours'] as $hour) {
                    // Convert is_open to boolean
                    $hour['is_open'] = (bool)$hour['is_open'];
                    
                    // Only set times if the day is open
                    if (!$hour['is_open']) {
                        $hour['open_time'] = null;
                        $hour['close_time'] = null;
                    }

                    Log::info('Creating operating hour:', $hour);
                    $shop->operatingHours()->create($hour);
                }

                // Mark setup as completed
                $user->setup_completed = true;
                $user->save();
            });

            Log::info('Shop setup completed for user: ' . $user->id);
            
            // Set shop mode and redirect to dashboard
            session(['shop_mode' => true]);
            session()->save();
            
            return redirect()->route('shop.dashboard')
                ->with('success', 'Shop setup completed successfully!');
        } catch (\Exception $e) {
            Log::error('Error in storeHours: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Failed to save operating hours. Please try again.');
        }
    }
} 
