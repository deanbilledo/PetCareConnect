<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Add debugging
        \Log::info('Admin controller accessed');
        \Log::info('User: ' . auth()->user()->email);
        \Log::info('Role: ' . auth()->user()->role);
        
        return view('admin.dashboard');
    }
} 