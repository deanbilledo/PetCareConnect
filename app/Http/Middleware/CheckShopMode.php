<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckShopMode
{
    public function handle(Request $request, Closure $next)
    {
        // Get referrer and current route info
        $previousUrl = url()->previous();
        $currentUrl = $request->url();
        
        // Check if we're coming from shop dashboard
        $fromShopDashboard = str_contains($previousUrl, 'shop/dashboard');
        
        // Always ensure customer mode (no shop_mode) for these routes
        if ($request->routeIs('home') || 
            $request->is('/') || 
            $request->routeIs('shop.mode.customer') ||
            $request->routeIs('groomingShops') ||
            $request->routeIs('petlandingpage') ||
            !$request->routeIs('shop.dashboard')) // If not in shop dashboard, ensure customer mode
        {
            // Clear shop mode
            if (session()->has('shop_mode')) {
                session()->forget('shop_mode');
                session()->save();
                \Log::info('Shop mode cleared', [
                    'route' => $request->route()->getName(),
                    'from_dashboard' => $fromShopDashboard
                ]);
            }
        }

        // Only allow shop mode in shop dashboard
        if ($request->routeIs('shop.dashboard')) {
            session(['shop_mode' => true]);
            session()->save();
            \Log::info('Shop mode set for dashboard');
        }

        // Validate shop mode
        if (session()->has('shop_mode')) {
            if (!auth()->check() || !auth()->user()->shop) {
                session()->forget('shop_mode');
                session()->save();
                \Log::info('Invalid shop mode cleared');
            }
        }

        return $next($request);
    }
} 