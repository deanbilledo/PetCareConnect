<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShopAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'has-shop']);
    }

    public function index()
    {
        $shop = auth()->user()->shop;
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Get total appointments
        $totalAppointments = $shop->appointments()->count();
        $lastMonthAppointments = $shop->appointments()
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        $currentMonthAppointments = $shop->appointments()
            ->whereMonth('created_at', $now->month)
            ->count();
        $appointmentGrowth = $lastMonthAppointments > 0 
            ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100 
            : 100;

        // Get total revenue
        $totalRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->sum('service_price');
        $lastMonthRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('service_price');
        $currentMonthRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->whereMonth('created_at', $now->month)
            ->sum('service_price');
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 100;

        // Get total customers (unique users)
        $totalCustomers = $shop->appointments()
            ->distinct('user_id')
            ->count('user_id');
        $lastMonthCustomers = $shop->appointments()
            ->whereMonth('created_at', $lastMonth->month)
            ->distinct('user_id')
            ->count('user_id');
        $currentMonthCustomers = $shop->appointments()
            ->whereMonth('created_at', $now->month)
            ->distinct('user_id')
            ->count('user_id');
        $customerGrowth = $lastMonthCustomers > 0 
            ? (($currentMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100 
            : 100;

        // Get active services
        $activeServices = $shop->services()
            ->where('status', 'active')
            ->count();
        $lastMonthServices = $shop->services()
            ->where('status', 'active')
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        $currentMonthServices = $shop->services()
            ->where('status', 'active')
            ->whereMonth('created_at', $now->month)
            ->count();
        $servicesGrowth = $lastMonthServices > 0 
            ? (($currentMonthServices - $lastMonthServices) / $lastMonthServices) * 100 
            : 0;

        // Get monthly revenue data for chart
        $monthlyRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(5), now()])
            ->select(
                DB::raw('sum(service_price) as total'),
                DB::raw("DATE_FORMAT(created_at, '%M') as month")
            )
            ->groupBy('month')
            ->orderBy('created_at')
            ->get();

        // Get monthly appointments data for chart
        $monthlyAppointments = $shop->appointments()
            ->whereBetween('created_at', [now()->subMonths(5), now()])
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('count(case when status = "completed" then 1 end) as completed'),
                DB::raw('count(case when status = "pending" then 1 end) as pending'),
                DB::raw("DATE_FORMAT(created_at, '%M') as month")
            )
            ->groupBy('month')
            ->orderBy('created_at')
            ->get();

        // Get recent activity
        $recentActivity = $shop->appointments()
            ->with(['user', 'employee'])
            ->latest()
            ->take(5)
            ->get();

        return view('shop.analytics.index', compact(
            'totalAppointments',
            'appointmentGrowth',
            'totalRevenue',
            'revenueGrowth',
            'totalCustomers',
            'customerGrowth',
            'activeServices',
            'servicesGrowth',
            'monthlyRevenue',
            'monthlyAppointments',
            'recentActivity'
        ));
    }
} 