<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        if (auth()->user()->role !== 'admin') {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
} 