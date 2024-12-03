<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasShop
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to access this feature'
                ], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Please login to access this feature.');
        }

        $user = auth()->user();
        
        if (!$user->shop) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must have a registered shop to access this resource'
                ], 403);
            }
            return redirect()->route('shop.register.form')
                ->with('error', 'You need to register a shop first.');
        }

        if ($user->shop->status === 'pending') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your shop registration is still pending approval'
                ], 403);
            }
            return redirect()->route('shop.registration.pending')
                ->with('info', 'Your shop registration is still pending approval.');
        }

        if ($user->shop->status === 'suspended' || $user->shop->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your shop access has been suspended or is inactive'
                ], 403);
            }
            return redirect()->route('home')
                ->with('error', 'Shop access denied.');
        }

        return $next($request);
    }
} 