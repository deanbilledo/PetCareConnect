<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Shop;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('has-shop');
    }
    
    /**
     * Display the shop dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        
        // Set shop mode in session
        session(['shop_mode' => true]);
        
        // Today's appointments
        $todayAppointments = Appointment::where('shop_id', $shop->id)
            ->whereDate('appointment_date', Carbon::today())
            ->count();
            
        // Total revenue
        $totalRevenue = Appointment::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->sum('service_price');
            
        // Pending appointments
        $pendingAppointments = Appointment::where('shop_id', $shop->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();
            
        // Recent appointments for the dashboard
        $appointments = Appointment::where('shop_id', $shop->id)
            ->with(['user', 'pet', 'employee', 'shop'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);
            
        // Get shop rating
        $shop->rating = $shop->reviews()->avg('rating') ?? 0;
        
        return view('shop.dashboard', compact(
            'shop',
            'todayAppointments',
            'totalRevenue',
            'pendingAppointments',
            'appointments'
        ));
    }
} 