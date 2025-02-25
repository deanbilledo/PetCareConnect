<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();

            // Update shop status to approved
            $shop->update(['status' => 'approved']);
            
            // Create a free trial subscription
            $trialDays = 30; // Set trial period (30 days)
            $subscription = $shop->subscriptions()->create([
                'status' => 'trial',
                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays($trialDays),
                'amount' => 0, // Free trial
                'payment_status' => 'verified', // Auto-verified for trial
                'reference_number' => 'TRIAL-' . strtoupper(uniqid()),
            ]);

            \Log::info('Free trial subscription created', [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'subscription_id' => $subscription->id,
                'trial_ends_at' => $subscription->trial_ends_at
            ]);
            
            // Call the notification handler
            app(ShopRegistrationController::class)->handleApproval($shop);

            DB::commit();
            return redirect()->back()->with('success', 'Shop has been approved and 30-day free trial has been activated.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in approveShop: ' . $e->getMessage(), [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name
            ]);
            return redirect()->back()->with('error', 'Failed to approve shop: ' . $e->getMessage());
        }
    }

    public function rejectShop(Request $request, Shop $shop)
    {
        try {
            DB::beginTransaction();

            $shop->update(['status' => 'rejected']);
            
            // Call the notification handler with rejection reason
            app(ShopRegistrationController::class)->handleRejection($shop, $request->reason);

            DB::commit();
            return redirect()->back()->with('success', 'Shop has been rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject shop: ' . $e->getMessage());
        }
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
        try {
            \Log::info('Fetching users for admin panel');

            // Get all users with pagination
            $users = User::latest()
                ->with(['shop', 'employeeShop'])
                ->paginate(10);

            \Log::info('Users found:', ['count' => $users->count()]);

            return view('admin.users', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Error in users() method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.users')->with('error', 'Failed to load users. Please try again.');
        }
    }

    public function editUser(User $user)
    {
        return response()->json($user->load('shop'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    public function getUserActivity(User $user)
    {
        // Get user's activity log (you'll need to implement your activity logging system)
        $activities = []; // Replace with actual activity data from your database

        return response()->json([
            'user' => $user,
            'activities' => $activities
        ]);
    }

    public function getUserComplaints(User $user)
    {
        // Get user's complaints (you'll need to implement your complaints system)
        $complaints = []; // Replace with actual complaints data from your database

        return response()->json([
            'user' => $user,
            'complaints' => $complaints
        ]);
    }

    public function toggleUserStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'status' => $user->status
        ]);
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

    public function getShopDetails(Shop $shop)
    {
        $shop->load([
            'user',
            'services',
            'appointments' => function ($query) {
                $query->latest()->take(5);
            },
            'ratings' => function ($query) {
                $query->with('user')->latest()->take(5);
            },
            'operatingHours'
        ]);

        $stats = [
            'total_revenue' => $shop->appointments()->where('status', 'completed')->sum('service_price'),
            'total_appointments' => $shop->appointments()->count(),
            'completed_appointments' => $shop->appointments()->where('status', 'completed')->count(),
            'pending_appointments' => $shop->appointments()->where('status', 'pending')->count(),
            'average_rating' => $shop->ratings()->avg('rating') ?? 0,
            'total_ratings' => $shop->ratings()->count(),
        ];

        return response()->json([
            'shop' => $shop,
            'stats' => $stats
        ]);
    }

    public function getRegistrationDetails(Shop $shop)
    {
        try {
            // Eager load the user relationship
            $shop->load('user');
            
            // Debug logging for shop details
            \Log::info('Shop details:', [
                'shop_id' => $shop->id,
                'name' => $shop->name,
                'type' => $shop->type
            ]);
            
            // Get the shop image URL with proper error handling
            $shopImageUrl = null;
            if ($shop->image) {
                if (Storage::disk('public')->exists($shop->image)) {
                    $shopImageUrl = asset('storage/' . $shop->image);
                    \Log::info('Shop image found', ['url' => $shopImageUrl]);
                } else {
                    \Log::warning('Shop image file not found in storage', [
                        'shop_id' => $shop->id,
                        'path' => $shop->image
                    ]);
                }
            }
            $shopImageUrl = $shopImageUrl ?: asset('images/default-shop.png');

            // Get the BIR certificate URL with proper error handling
            $birCertificateUrl = null;
            if ($shop->bir_certificate) {
                $birPath = $shop->bir_certificate;
                if (Storage::disk('public')->exists($birPath)) {
                    $birCertificateUrl = asset('storage/' . $birPath);
                    \Log::info('BIR certificate found', ['url' => $birCertificateUrl]);
                } else {
                    \Log::warning('BIR certificate file not found in storage', [
                        'shop_id' => $shop->id,
                        'path' => $birPath
                    ]);
                }
            }

            $response = [
                'id' => $shop->id,
                'name' => $shop->name,
                'type' => $shop->type,
                'phone' => $shop->phone,
                'address' => $shop->address,
                'tin' => $shop->tin,
                'vat_status' => $shop->vat_status,
                'shop_image_url' => $shopImageUrl,
                'bir_certificate_url' => $birCertificateUrl,
                'user' => $shop->user ? [
                    'name' => $shop->user->name,
                    'email' => $shop->user->email,
                ] : null
            ];

            \Log::info('Returning registration details', $response);
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in getRegistrationDetails: ' . $e->getMessage(), [
                'shop_id' => $shop->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch shop registration details'
            ], 500);
        }
    }

    public function show(Shop $shop)
    {
        return view('admin.shops.show', compact('shop'));
    }
} 