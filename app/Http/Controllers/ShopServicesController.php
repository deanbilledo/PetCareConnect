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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        try {
            $shop = auth()->user()->shop;
            $service = $shop->services()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service added successfully',
                'service' => $service
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add service'
            ], 500);
        }
    }

    public function update(Request $request, Service $service)
    {
        // Check if the service belongs to the authenticated user's shop
        if ($service->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        try {
            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'service' => $service
            ]);
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
        // Check if the service belongs to the authenticated user's shop
        if ($service->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json($service);
    }
} 