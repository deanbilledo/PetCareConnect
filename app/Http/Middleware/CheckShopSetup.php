<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckShopSetup
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shop.register.form');
        }

        // If setup is completed and trying to access setup pages
        if ($user->setup_completed && $request->is('shop/setup*')) {
            return redirect()->route('shop.dashboard')
                ->with('info', 'Shop setup has already been completed.');
        }

        // If setup is not completed and trying to access shop dashboard
        if (!$user->setup_completed && 
            $shop->status === 'active' && 
            !$request->is('shop/setup*')) {
            return redirect()->route('shop.setup.welcome')
                ->with('info', 'Please complete your shop setup first.');
        }

        return $next($request);
    }
} 