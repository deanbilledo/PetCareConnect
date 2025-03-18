<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VeterinaryController extends Controller
{
    /**
     * Display a listing of veterinary clinics
     */
    public function index(Request $request)
    {
        try {
            // Start query builder for veterinary shops
            $shopsQuery = Shop::where('type', 'veterinary')
                ->where('status', 'active')
                ->withAvg('ratings', 'rating');
                
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $shopsQuery->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('address', 'like', "%{$search}%")
                          ->orWhereHas('services', function($subQuery) use ($search) {
                              $subQuery->where('name', 'like', "%{$search}%")
                                      ->orWhere('description', 'like', "%{$search}%");
                          });
                });
            }

            // Apply rating filter
            if ($request->filled('rating')) {
                $minRating = $request->input('rating');
                $shopsQuery->having('ratings_avg_rating', '>=', $minRating);
            }

            // Apply service type filter
            if ($request->filled('service_type')) {
                $serviceType = $request->input('service_type');
                $shopsQuery->whereHas('services', function($query) use ($serviceType) {
                    $query->where('type', $serviceType);
                });
            }

            // Apply sorting
            if ($request->filled('sort')) {
                $sort = $request->input('sort');
                
                switch ($sort) {
                    case 'rating':
                        $shopsQuery->orderBy('ratings_avg_rating', 'desc');
                        break;
                    case 'name_asc':
                        $shopsQuery->orderBy('name', 'asc');
                        break;
                    case 'name_desc':
                        $shopsQuery->orderBy('name', 'desc');
                        break;
                    case 'popular':
                    default:
                        // For popularity, we can use relationship count or another metric
                        $shopsQuery->withCount('appointments')
                                  ->orderBy('appointments_count', 'desc')
                                  ->orderBy('ratings_avg_rating', 'desc');
                        break;
                }
            } else {
                // Default ordering
                $shopsQuery->orderBy('ratings_avg_rating', 'desc');
            }

            // Get the filtered shops
            $veterinaryShops = $shopsQuery->get();
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching veterinary shops: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Set an empty collection as fallback
            $veterinaryShops = collect([]);
        }

        try {
            // Get the 3 most popular veterinary services based on appointment count
            $servicesQuery = DB::table('services')
                ->join('shops', 'services.shop_id', '=', 'shops.id')
                ->leftJoin('appointment_services', 'services.id', '=', 'appointment_services.service_id')
                ->where('shops.type', 'veterinary')
                ->where('shops.status', 'active')
                ->whereNull('services.deleted_at');
                
            // Apply search filter to services if provided
            if ($request->filled('search')) {
                $search = $request->input('search');
                $servicesQuery->where(function($query) use ($search) {
                    $query->where('services.name', 'like', "%{$search}%")
                          ->orWhere('services.description', 'like', "%{$search}%")
                          ->orWhere('shops.name', 'like', "%{$search}%");
                });
            }
            
            // Apply service type filter if provided
            if ($request->filled('service_type')) {
                $serviceType = $request->input('service_type');
                $servicesQuery->where('services.type', $serviceType);
            }
            
            $serviceIds = $servicesQuery->groupBy('services.id')
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
            // Log the error
            Log::error('Error fetching veterinary services: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Fallback to just getting 3 services randomly if there's any error
            $veterinaryServices = $this->getFallbackServices($request);
        }

        // If no services found, use fallback
        if (!isset($veterinaryServices) || $veterinaryServices->isEmpty()) {
            try {
                $veterinaryServices = $this->getFallbackServices($request);
            } catch (\Exception $e) {
                // If even the fallback fails, use an empty collection
                Log::error('Error fetching fallback veterinary services: ' . $e->getMessage(), [
                    'exception' => $e
                ]);
                $veterinaryServices = collect([]);
            }
        }

        // Ensure variables are always defined
        if (!isset($veterinaryShops)) {
            $veterinaryShops = collect([]);
        }
        if (!isset($veterinaryServices)) {
            $veterinaryServices = collect([]);
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
        try {
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
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in searchShops: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Return empty results in case of error
            return response()->json([]);
        }
    }
    
    /**
     * Get fallback services when the main query fails or returns empty results
     */
    private function getFallbackServices(Request $request)
    {
        try {
            $query = Service::whereHas('shop', function($query) {
                $query->where('type', 'veterinary')
                      ->where('status', 'active');
            })->with('shop');
            
            // Apply search if provided
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Apply service type filter if provided
            if ($request->filled('service_type')) {
                $query->where('type', $request->input('service_type'));
            }
            
            return $query->inRandomOrder()->take(3)->get();
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in getFallbackServices: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Return empty collection if even the fallback fails
            return collect([]);
        }
    }
} 