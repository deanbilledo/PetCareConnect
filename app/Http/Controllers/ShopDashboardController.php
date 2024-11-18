<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\HasShop::class]);
    }

    public function index()
    {
        $shop = auth()->user()->shop;
        // Set shop mode in session
        session(['shop_mode' => true]);
        session()->save();
        \Log::info('Switched to shop mode');
        
        return view('shop.dashboard', compact('shop'));
    }

    public function switchToCustomerMode()
    {
        // Clear shop mode completely
        session()->forget('shop_mode');
        session()->save();
        \Log::info('Switched to customer mode');
        
        // Redirect with history state
        return redirect()->route('home')
            ->with('mode_switch', true)
            ->with('previous_mode', 'shop');
    }
} 