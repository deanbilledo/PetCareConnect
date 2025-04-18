<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Rating;
use App\Http\Middleware\HasShop;

class ShopDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', HasShop::class]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;
        
        // Check if setup is completed
        if (!$user->setup_completed) {
            return redirect()->route('shop.setup.welcome')
                ->with('info', 'Please complete your shop setup first.');
        }
        
        // Set shop mode in session
        session(['shop_mode' => true]);
        
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
        return redirect()->route('home');
    }

    public function switchToShopMode()
    {
        session(['shop_mode' => true]);
        session()->save();
        return redirect()->route('shop.dashboard');
    }

    public function reviews()
    {
        $shop = auth()->user()->shop;
        
        // Get paginated ratings instead of all at once
        $ratings = $shop->ratings()
            ->with(['user', 'appointment.employee', 'appointment.services'])
            ->latest()
            ->paginate(10);
            
        return view('shop.reviews.index', compact('shop', 'ratings'));
    }

    public function addComment(Request $request, Rating $rating)
    {
        // Validate request
        $validated = $request->validate([
            'shop_comment' => 'required|string|max:1000',
        ]);

        // Check if the rating belongs to the authenticated shop
        if ($rating->shop_id !== auth()->user()->shop->id) {
            return back()->with('error', 'You are not authorized to comment on this review.');
        }

        // Update the rating with the shop's comment
        $rating->update([
            'shop_comment' => $validated['shop_comment']
        ]);

        return back()->with('success', 'Your response has been added to the review.');
    }
} 