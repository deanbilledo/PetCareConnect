<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // If not in shop mode, ensure it's cleared from session
        if (!session('shop_mode')) {
            session()->forget('shop_mode');
            session()->save();
        }
        
        // Get popular shops for both guests and authenticated users
        $popularShops = Shop::select('shops.*')
            ->selectRaw('(SELECT AVG(rating) FROM ratings WHERE shops.id = ratings.shop_id) as ratings_avg_rating')
            ->where('status', '=', 'active')
            ->orderByDesc('ratings_avg_rating')
            ->take(6)
            ->get();

        $services = [
            (object)[
                'name' => 'Grooming',
                'description' => 'Professional grooming services to keep your pet looking and feeling their best. Our expert groomers provide personalized care for all breeds.'
            ],
            (object)[
                'name' => 'Bath',
                'description' => 'Professional grooming services to keep your pet looking and feeling their best. Our expert groomers provide personalized care for all breeds.'
            ],
            (object)[
                'name' => 'Check-Up',
                'description' => 'Comprehensive veterinary check-ups to ensure your pet\'s health and well-being. Our experienced vets provide thorough examinations and preventive care.'
            ]
        ];

        return view('home', compact('popularShops', 'services'));
    }

    public function dashboard()
    {
        $popularShops = Shop::select('shops.*')
            ->selectRaw('(SELECT AVG(rating) FROM ratings WHERE shops.id = ratings.shop_id) as ratings_avg_rating')
            ->where('status', '=', 'active')
            ->orderByDesc('ratings_avg_rating')
            ->take(6)
            ->get();
            
        $services = [
            (object)[
                'name' => 'Dog Grooming',
                'description' => 'Professional grooming services for all breeds'
            ],
            (object)[
                'name' => 'Dog Walking',
                'description' => 'Regular exercise and outdoor activities'
            ],
            (object)[
                'name' => 'Pet Sitting',
                'description' => 'In-home care when you\'re away'
            ]
        ];
        
        return view('dashboard', compact('services', 'popularShops'));
    }
} 