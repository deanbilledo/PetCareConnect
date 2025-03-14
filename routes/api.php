<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Service;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Service lookup route - maps service_type to service_id
// This more specific route must come BEFORE the generic service ID route
Route::get('/services/lookup', function (Request $request) {
    try {
        $shopId = $request->query('shop_id');
        $serviceType = $request->query('service_type');
        
        if (!$shopId || !$serviceType) {
            return response()->json(['error' => 'Missing shop_id or service_type parameters'], 400);
        }
        
        // Find a service matching the type in the specified shop
        $service = Service::where('shop_id', $shopId)
            ->where(function($query) use ($serviceType) {
                // Try to match on name or type fields (adapt based on your schema)
                $query->where('name', 'like', "%{$serviceType}%")
                    ->orWhere('type', 'like', "%{$serviceType}%")
                    ->orWhere('description', 'like', "%{$serviceType}%");
            })
            ->where('status', 'active')
            ->first();
        
        if ($service) {
            return response()->json([
                'success' => true,
                'service_id' => $service->id,
                'service_name' => $service->name
            ]);
        }
        
        // If no exact match, return the first active service as fallback
        $fallbackService = Service::where('shop_id', $shopId)
            ->where('status', 'active')
            ->first();
            
        if ($fallbackService) {
            return response()->json([
                'success' => true,
                'service_id' => $fallbackService->id,
                'service_name' => $fallbackService->name,
                'fallback' => true
            ]);
        }
        
        return response()->json(['error' => 'No matching service found'], 404);
    } catch (\Exception $e) {
        \Log::error('Service lookup error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to look up service'], 500);
    }
});

// Public API route for service details with popular appointments
Route::get('/services/{serviceId}', 'App\Http\Controllers\ShopServicesController@getServiceDetails');

// Shop search routes
Route::get('/shops/search', 'App\Http\Controllers\ShopController@searchByLocation');
Route::get('/shops/all', 'App\Http\Controllers\ShopController@getAllShops'); 