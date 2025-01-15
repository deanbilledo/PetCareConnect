<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function shops()
    {
        // Get pending shop registrations
        $pendingShops = Shop::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Get existing shops
        $existingShops = Shop::with('user')
            ->whereIn('status', ['active', 'suspended'])
            ->latest()
            ->get();

        return view('admin.shops', compact('pendingShops', 'existingShops'));
    }

    public function approveShop(Shop $shop)
    {
        $shop->update(['status' => 'active']);
        return back()->with('success', 'Shop has been approved successfully.');
    }

    public function rejectShop(Shop $shop)
    {
        $shop->update(['status' => 'rejected']);
        return back()->with('success', 'Shop has been rejected.');
    }

    public function toggleShopStatus(Shop $shop)
    {
        $newStatus = $shop->status === 'active' ? 'suspended' : 'active';
        $shop->update(['status' => $newStatus]);
        
        $message = $newStatus === 'active' ? 'Shop has been activated.' : 'Shop has been suspended.';
        return back()->with('success', $message);
    }

    public function getShopAnalytics(Shop $shop)
    {
        // Get shop analytics data
        $analytics = [
            'total_revenue' => $shop->appointments()->sum('total_amount'),
            'total_appointments' => $shop->appointments()->count(),
            'average_rating' => $shop->ratings()->avg('rating') ?? 0,
            'monthly_revenue' => $shop->appointments()
                ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
                ->groupBy('month')
                ->get(),
            'weekly_appointments' => $shop->appointments()
                ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
                ->groupBy('day')
                ->get(),
        ];

        return response()->json($analytics);
    }

    public function users()
    {
        return view('admin.users');
    }

    public function services()
    {
        return view('admin.services');
    }

    public function payments()
    {
        return view('admin.payments');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function support()
    {
        return view('admin.support');
    }

    public function settings()
    {
        return view('admin.settings');
    }
} 