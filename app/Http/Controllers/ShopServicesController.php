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
        $this->middleware(['auth', 'has-shop']);
    }

    public function index()
    {
        $shop = auth()->user()->shop;
        $services = $shop->services()->orderBy('name')->get();
        $employees = $shop->employees()->orderBy('name')->get();

        return view('shop.services.index', compact('services', 'shop', 'employees'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'pet_types' => 'required|array',
                'pet_types.*' => 'string',
                'size_ranges' => 'required|array',
                'size_ranges.*' => 'string',
                'exotic_pet_service' => 'boolean',
                'exotic_pet_species' => 'required_if:exotic_pet_service,true|array|nullable',
                'exotic_pet_species.*' => 'string',
                'base_price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:15',
                'employee_ids' => 'required|array',
                'employee_ids.*' => 'exists:employees,id'
            ]);

            // Ensure Exotic is in pet_types if exotic_pet_service is true
            if ($validated['exotic_pet_service'] && !in_array('Exotic', $validated['pet_types'])) {
                $validated['pet_types'][] = 'Exotic';
            }

            Log::info('Creating service with data:', $validated);

            DB::beginTransaction();
            try {
                $shop = auth()->user()->shop;
                
                // Remove employee_ids from validated data before creating service
                $employeeIds = $validated['employee_ids'];
                unset($validated['employee_ids']);
                
                $service = $shop->services()->create($validated);
                
                // Attach employees to the service
                $service->employees()->attach($employeeIds);
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Service added successfully',
                    'service' => $service
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error creating service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Service $service)
    {
        try {
            // Authorization check
            if ($service->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this service'
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
                'exotic_pet_service' => 'boolean',
                'exotic_pet_species' => 'required_if:exotic_pet_service,true|array|nullable',
                'exotic_pet_species.*' => 'string',
                'special_requirements' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:15',
                'variable_pricing' => 'nullable|array',
                'variable_pricing.*.size' => 'required_with:variable_pricing|string',
                'variable_pricing.*.price' => 'required_with:variable_pricing|numeric|min:0',
                'add_ons' => 'nullable|array',
                'add_ons.*.name' => 'required_with:add_ons|string',
                'add_ons.*.price' => 'required_with:add_ons|numeric|min:0',
                'employee_ids' => 'required|array',
                'employee_ids.*' => 'exists:employees,id'
            ]);

            DB::beginTransaction();
            try {
                // Remove employee_ids from validated data before updating service
                $employeeIds = $validated['employee_ids'];
                unset($validated['employee_ids']);
                
                $service->update($validated);
                
                // Sync employees with the service
                $service->employees()->sync($employeeIds);
                
                DB::commit();

                // Load the updated service with its employees
                $service->load('employees');

                return response()->json([
                    'success' => true,
                    'message' => 'Service updated successfully',
                    'data' => $service->fresh()
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error updating service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service'
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

            // Load the service with its employees
            $service->load('employees');

            // Log the raw service data for debugging
            Log::info('Raw service data:', [
                'service' => $service->toArray(),
                'pet_types' => $service->pet_types,
                'size_ranges' => $service->size_ranges,
                'variable_pricing' => $service->variable_pricing,
                'add_ons' => $service->add_ons,
                'employees' => $service->employees
            ]);

            // Clean and prepare the data
            $serviceData = [
                'id' => $service->id,
                'name' => $service->name,
                'category' => $service->category,
                'description' => $service->description,
                'pet_types' => $service->pet_types ?? [],
                'size_ranges' => $service->size_ranges ?? [],
                'exotic_pet_service' => (bool) $service->exotic_pet_service,
                'exotic_pet_species' => $service->exotic_pet_species ?? [],
                'special_requirements' => $service->special_requirements,
                'base_price' => (float) $service->base_price,
                'duration' => (int) $service->duration,
                'variable_pricing' => $service->variable_pricing ?? [],
                'add_ons' => $service->add_ons ?? [],
                'status' => $service->status,
                'employee_ids' => $service->employees->pluck('id')->toArray()
            ];

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
                'message' => 'Failed to load service details: ' . $e->getMessage()
            ], 500);
        }
    }
} 