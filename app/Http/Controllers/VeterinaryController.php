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
} 