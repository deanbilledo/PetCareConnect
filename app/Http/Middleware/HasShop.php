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
        if (!auth()->check() || !auth()->user()->shop) {
            if ($request->routeIs('shop.profile*')) {
                return redirect()->route('shop.register.form')
                    ->with('error', 'You need to register a shop first.');
            }
            return redirect()->route('home')
                ->with('error', 'You need to register a shop first.');
        }

        return $next($request);
    }
} 