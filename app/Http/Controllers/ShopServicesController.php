<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\HasShop;

class ShopServicesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', HasShop::class]);
    }

    public function index()
    {
        $shop = auth()->user()->shop;
        $services = $shop->services()->orderBy('name')->get();

        return view('shop.services.index', compact('services', 'shop'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Storing service with data:', $request->all());
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'pet_types' => 'required|array',
                'pet_types.*' => 'string',
                'size_ranges' => 'required|array',
                'size_ranges.*' => 'string',
                'breed_specific' => 'boolean',
                'special_requirements' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:15',
                'variable_pricing' => 'nullable|array',
                'variable_pricing.*.size' => 'required_with:variable_pricing|string',
                'variable_pricing.*.price' => 'required_with:variable_pricing|numeric|min:0',
                'add_ons' => 'nullable|array'
            ]);

            // Filter out empty variable pricing entries
            if (isset($validated['variable_pricing'])) {
                $validated['variable_pricing'] = array_filter($validated['variable_pricing'], function($price) {
                    return !empty($price['size']) && isset($price['price']);
                });
            }

            \Log::info('Validated data:', $validated);

            $shop = auth()->user()->shop;
            $service = $shop->services()->create($validated);

            \Log::info('Service created:', $service->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Service added successfully',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating service: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Service $service)
    {
        try {
            \Log::info('Updating service with data:', $request->all());
            
            if ($service->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'pet_types' => 'required|array',
                'pet_types.*' => 'string',
                'size_ranges' => 'required|array',
                'size_ranges.*' => 'string',
                'breed_specific' => 'boolean',
                'special_requirements' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:15',
                'variable_pricing' => 'nullable|array',
                'variable_pricing.*.size' => 'required_with:variable_pricing|string',
                'variable_pricing.*.price' => 'required_with:variable_pricing|numeric|min:0',
                'add_ons' => 'nullable|array'
            ]);

            \Log::info('Validated data:', $validated);

            $service->update($validated);

            \Log::info('Service updated:', $service->fresh()->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating service: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Service $service)
    {
        // Check if the service belongs to the authenticated user's shop
        if ($service->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service'
            ], 500);
        }
    }

    public function updateStatus(Service $service)
    {
        // Check if the service belongs to the authenticated user's shop
        if ($service->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $service->status = $service->status === 'active' ? 'inactive' : 'active';
            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Service status updated successfully',
                'status' => $service->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating service status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service status'
            ], 500);
        }
    }

    public function show(Service $service)
    {
        try {
            // Check if the service belongs to the authenticated user's shop
            if ($service->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this service'
                ], 403);
            }

            // The model's cast will handle the JSON conversion
            $serviceData = $service->toArray();

            // Ensure arrays are properly initialized
            $serviceData['pet_types'] = $serviceData['pet_types'] ?? [];
            $serviceData['size_ranges'] = $serviceData['size_ranges'] ?? [];
            $serviceData['variable_pricing'] = $serviceData['variable_pricing'] ?? [];
            $serviceData['add_ons'] = $serviceData['add_ons'] ?? [];

            return response()->json([
                'success' => true,
                'data' => $serviceData
            ]);

        } catch (\Exception $e) {
            Log::error('Error in show method: ' . $e->getMessage(), [
                'service_id' => $service->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load service details'
            ], 500);
        }
    }
} 