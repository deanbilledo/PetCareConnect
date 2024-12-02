<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasShop
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this feature.');
        }

        $user = auth()->user();
        
        if (!$user->shop) {
            return redirect()->route('home')
                ->with('error', 'You need to register a shop first.');
        }

        if ($user->shop->status === 'pending') {
            return redirect()->route('shop.registration.pending')
                ->with('info', 'Your shop registration is still pending approval.');
        }

        if ($user->shop->status === 'suspended') {
            return redirect()->route('home')
                ->with('error', 'Your shop access has been suspended.');
        }

        if ($user->shop->status !== 'active') {
            return redirect()->route('home')
                ->with('error', 'Shop access denied.');
        }

        return $next($request);
    }
} 