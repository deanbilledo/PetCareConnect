<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->shop) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Shop access required.'], 403);
            }
            return redirect()->route('home')->with('error', 'Unauthorized. Shop access required.');
        }

        return $next($request);
    }
} 