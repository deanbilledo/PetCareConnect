<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckShopMode
{
    public function handle(Request $request, Closure $next)
    {
        // Always clear shop mode for these routes/conditions
        if ($request->routeIs('home') || 
            $request->is('/') || 
            $request->routeIs('shop.mode.customer') ||
            $request->routeIs('groomingShops') ||
            $request->routeIs('petlandingpage'))
        {
            session()->forget('shop_mode');
            session()->save();
            session()->regenerate(); // Force session regeneration
            \Log::info('Shop mode cleared by middleware', [
                'route' => $request->route()->getName(),
                'user' => auth()->check() ? auth()->id() : 'guest'
            ]);
        }

        // If accessing any non-shop route, ensure we're in customer mode
        if (!$request->routeIs('shop.dashboard') && !$request->routeIs('shop.*')) {
            session()->forget('shop_mode');
            session()->save();
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