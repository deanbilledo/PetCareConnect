<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this feature.');
        }

        $user = auth()->user();
        
        // If user has no shop
        if (!$user->shop) {
            if ($request->routeIs('shop.profile*')) {
                return redirect()->route('shop.register.form')
                    ->with('error', 'You need to register a shop first.');
            }
            return redirect()->route('home')
                ->with('error', 'You need to register a shop first.');
        }

        // If shop is pending approval
        if ($user->shop->status === 'pending') {
            return redirect()->route('shop.registration.pending')
                ->with('info', 'Your shop registration is still pending approval.');
        }

        // If shop is suspended
        if ($user->shop->status === 'suspended') {
            return redirect()->route('home')
                ->with('error', 'Your shop access has been suspended. Please contact support for more information.');
        }

        // Only allow access if shop is active
        if ($user->shop->status !== 'active') {
            return redirect()->route('home')
                ->with('error', 'Shop access denied. Please contact support for assistance.');
        }

        return $next($request);
    }
} 