<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VeterinaryController extends Controller
{
    /**
     * Display a listing of veterinary clinics
     */
    public function index()
    {
        // Fetch all active veterinary clinics with ratings
        $veterinaryShops = Shop::where('type', 'veterinary')
            ->where('status', 'active')
            ->withAvg('ratings', 'rating')
            ->orderBy('ratings_avg_rating', 'desc')
            ->get();

        try {
            // Get the 3 most popular veterinary services based on appointment count
            $serviceIds = DB::table('services')
                ->join('shops', 'services.shop_id', '=', 'shops.id')
                ->leftJoin('appointment_services', 'services.id', '=', 'appointment_services.service_id')
                ->where('shops.type', 'veterinary')
                ->where('shops.status', 'active')
                ->whereNull('services.deleted_at')
                ->groupBy('services.id')
                ->select('services.id', DB::raw('COUNT(appointment_services.id) as appointment_count'))
                ->orderBy('appointment_count', 'desc')
                ->limit(3)
                ->pluck('id');

            // Fetch the full service records by ID
            $veterinaryServices = Service::whereIn('id', $serviceIds)
                ->with('shop')
                ->get()
                ->sortBy(function($service) use ($serviceIds) {
                    // Maintain the order from the subquery
                    return array_search($service->id, $serviceIds->toArray());
                })
                ->values();
        } catch (\Exception $e) {
            // Fallback to just getting 3 services randomly if there's any error
            $veterinaryServices = Service::whereHas('shop', function($query) {
                $query->where('type', 'veterinary')
                      ->where('status', 'active');
            })
            ->with('shop')
            ->inRandomOrder()
            ->take(3)
            ->get();
        }

        // If no services found, just get 3 random services
        if ($veterinaryServices->isEmpty()) {
            $veterinaryServices = Service::whereHas('shop', function($query) {
                $query->where('type', 'veterinary')
                      ->where('status', 'active');
            })
            ->with('shop')
            ->inRandomOrder()
            ->take(3)
            ->get();
        }

        // Pass the data to the view
        return view('groomVetLandingPage.petlandingpage', compact('veterinaryShops', 'veterinaryServices'));
    }
    
    /**
     * API endpoint to search for veterinary shops
     * Used for live search in the frontend
     */
    public function searchShops(Request $request)
    {
        // Validate the request
        $request->validate([
            'query' => 'required|string|min:2'
        ]);
        
        $query = $request->input('query');
        $rating = $request->input('rating');
        $serviceType = $request->input('service_type');
        
        // Start query builder for veterinary shops
        $shopsQuery = Shop::where('type', 'veterinary')
            ->withAvg('ratings', 'rating')
            ->where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhereHas('services', function($subQuery) use ($query) {
                      $subQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                  });
            });
        
        // Apply rating filter if provided
        if (!empty($rating)) {
            $shopsQuery->having('ratings_avg_rating', '>=', $rating);
        }
        
        // Apply service type filter if provided
        if (!empty($serviceType)) {
            $shopsQuery->whereHas('services', function($q) use ($serviceType) {
                $q->where('type', $serviceType);
            });
        }
        
        // Get results, limited to 5 for performance
        $shops = $shopsQuery->orderBy('ratings_avg_rating', 'desc')
            ->limit(5)
            ->get();
            
        // Transform the data for the frontend
        $results = $shops->map(function($shop) {
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'image_url' => $shop->image_url ? asset($shop->image_url) : asset('images/shops/default-shop.svg'),
                'address' => $shop->address,
                'rating' => $shop->ratings_avg_rating,
                'services_count' => $shop->services->count()
            ];
        });
        
        return response()->json($results);
    }
} 