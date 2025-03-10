<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroomingController extends Controller
{
    public function groomingShops()
    {
        // Fetch popular grooming shops, you can add more complex queries if needed
        $popularShops = Shop::withAvg('ratings', 'rating')
            ->orderBy('ratings_avg_rating', 'desc')
            ->take(6)
            ->get();

        // Pass the data to the view
        return view('partials.groomingShops', compact('popularShops'));
    }

    // Added new method for grooming landing page
    public function index()
    {
        // Fetch all active grooming shops with ratings
        $groomingShops = Shop::grooming() // Using the scopeGrooming method
            ->withAvg('ratings', 'rating')
            ->orderBy('ratings_avg_rating', 'desc')
            ->get();

        try {
            // Get the 3 most popular grooming services based on appointment count
            // Use a different approach that works with MySQL's ONLY_FULL_GROUP_BY mode
            $serviceIds = DB::table('services')
                ->join('shops', 'services.shop_id', '=', 'shops.id')
                ->leftJoin('appointment_services', 'services.id', '=', 'appointment_services.service_id')
                ->where('shops.type', 'grooming')
                ->where('shops.status', 'active')
                ->whereNull('services.deleted_at')
                ->groupBy('services.id')
                ->select('services.id', DB::raw('COUNT(appointment_services.id) as appointment_count'))
                ->orderBy('appointment_count', 'desc')
                ->limit(3)
                ->pluck('id');

            // Fetch the full service records by ID
            $groomingServices = Service::whereIn('id', $serviceIds)
                ->with('shop')
                ->get()
                ->sortBy(function($service) use ($serviceIds) {
                    // Maintain the order from the subquery
                    return array_search($service->id, $serviceIds->toArray());
                })
                ->values();
        } catch (\Exception $e) {
            // Fallback to just getting 3 services randomly if there's any error
            $groomingServices = Service::whereHas('shop', function($query) {
                $query->where('type', 'grooming')
                      ->where('status', 'active');
            })
            ->with('shop')
            ->inRandomOrder()
            ->take(3)
            ->get();
        }

        // If no services found, just get 3 random services
        if ($groomingServices->isEmpty()) {
            $groomingServices = Service::whereHas('shop', function($query) {
                $query->where('type', 'grooming')
                      ->where('status', 'active');
            })
            ->with('shop')
            ->inRandomOrder()
            ->take(3)
            ->get();
        }

        // Pass the data to the view
        return view('groomVetLandingPage.groominglandingpage', compact('groomingShops', 'groomingServices'));
    }
}
