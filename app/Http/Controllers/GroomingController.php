<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

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
}
