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
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->shop) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Shop access required.'
                ], 403);
            }
            return redirect()->route('home')->with('error', 'Unauthorized. Shop access required.');
        }

        $user = auth()->user();
        
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