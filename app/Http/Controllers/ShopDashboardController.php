<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ShopDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\HasShop::class]);
    }

    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        
        // Set shop mode in session
        session(['shop_mode' => true]);
        session()->save();
        
        // Get appointments query
        $appointmentsQuery = $shop->appointments()->with(['user', 'pet']);

        // Apply status filter
        if ($request->status && $request->status !== 'all') {
            $appointmentsQuery->where('status', $request->status);
        }

        // Apply date filter
        if ($request->date) {
            $appointmentsQuery->whereDate('appointment_date', $request->date);
        }

        // Get stats
        $todayAppointments = $shop->appointments()
            ->whereDate('appointment_date', Carbon::today())
            ->count();

        $totalRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->sum('service_price');

        $pendingAppointments = $shop->appointments()
            ->where('status', 'pending')
            ->count();

        // Get paginated appointments
        $appointments = $appointmentsQuery
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('shop.dashboard', compact(
            'shop',
            'appointments',
            'todayAppointments',
            'totalRevenue',
            'pendingAppointments'
        ));
    }

    public function switchToCustomerMode()
    {
        session()->forget('shop_mode');
        session()->save();
        
        return redirect()->route('home')
            ->with('mode_switch', true)
            ->with('previous_mode', 'shop');
    }
} 