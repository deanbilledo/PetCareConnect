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
        session()->regenerate(); // Force session regeneration
        \Log::info('Switched to shop mode');
        
        return view('shop.dashboard', compact('shop'));
    }

    public function switchToCustomerMode()
    {
        // Clear shop mode completely
        session()->forget('shop_mode');
        session()->save();
        session()->regenerate(); // Force session regeneration
        \Log::info('Switched to customer mode');
        
        // Force a complete page reload when switching to customer mode
        return redirect()->route('home', ['refresh' => time()])->with('mode_switch', true);
    }
} 