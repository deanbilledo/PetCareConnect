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
        
        // Check if user has an approved and active shop
        $hasApprovedShop = auth()->check() && 
                          auth()->user()->shop && 
                          auth()->user()->shop->status === 'active';
        
        // Always ensure customer mode (no shop_mode) for these routes
        if ($request->routeIs('home') || 
            $request->is('/') || 
            $request->routeIs('shop.mode.customer') ||
            $request->routeIs('groomingShops') ||
            $request->routeIs('petlandingpage') ||
            !$request->routeIs('shop.dashboard') ||
            !$hasApprovedShop) // If shop is not approved or suspended, ensure customer mode
        {
            // Clear shop mode
            if (session()->has('shop_mode')) {
                session()->forget('shop_mode');
                session()->save();
                \Log::info('Shop mode cleared', [
                    'route' => $request->route()->getName(),
                    'shop_status' => auth()->user()->shop->status ?? 'no_shop'
                ]);
            }
        }

        // Only allow shop mode in shop dashboard for approved shops
        if ($request->routeIs('shop.dashboard')) {
            if (!$hasApprovedShop) {
                if (auth()->user()->shop) {
                    $message = auth()->user()->shop->status === 'pending' 
                        ? 'Your shop registration is still pending approval.' 
                        : 'Your shop access has been suspended.';
                    
                    return redirect()->route('home')->with('error', $message);
                }
                return redirect()->route('home');
            }
            
            session(['shop_mode' => true]);
            session()->save();
            \Log::info('Shop mode set for dashboard');
        }

        return $next($request);
    }
} 