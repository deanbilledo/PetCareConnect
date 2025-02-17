<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Appointment;
use App\Models\Rating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get total shops
        $totalShops = Shop::count();

        // Get total revenue from completed appointments
        $totalRevenue = Appointment::where('status', 'completed')
            ->sum('service_price');

        // Get total appointments
        $totalAppointments = Appointment::count();

        // Get total reported shops (shops with ratings less than 3)
        $reportedShops = Shop::where('rating', '<', 3)->count();

        // Get monthly revenue data for the last 6 months
        $monthlyRevenue = Appointment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(5), now()])
            ->select(
                DB::raw('SUM(service_price) as total'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M'),
                    'total' => $item->total,
                ];
            });

        // Get weekly appointments data
        $weeklyAppointments = Appointment::whereBetween('created_at', [now()->subDays(6), now()])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('DATE(created_at) as date')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('D'),
                    'total' => $item->total,
                ];
            });

        // Get services distribution
        $servicesDistribution = Appointment::select('service_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('service_type')
            ->get()
            ->map(function ($item) {
                return [
                    'service' => $item->service_type,
                    'count' => $item->count,
                ];
            });

        // Get recent shops with pagination
        $shops = Shop::with(['user', 'ratings'])
            ->withCount('appointments')
            ->withAvg('ratings', 'rating')
            ->latest()
            ->paginate(10);

        // Get recent users with pagination
        $users = User::with(['appointments', 'ratings'])
            ->withCount(['appointments', 'ratings'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact(
            'totalShops',
            'totalRevenue',
            'totalAppointments',
            'reportedShops',
            'monthlyRevenue',
            'weeklyAppointments',
            'servicesDistribution',
            'shops',
            'users'
        ));
    }

    // Shop Management Methods
    public function editShop(Shop $shop)
    {
        return response()->json($shop->load('user'));
    }

    public function updateShop(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'status' => 'required|in:active,suspended'
        ]);

        $shop->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully'
        ]);
    }

    public function toggleShopStatus(Shop $shop)
    {
        $shop->status = $shop->status === 'active' ? 'suspended' : 'active';
        $shop->save();

        return response()->json([
            'success' => true,
            'message' => 'Shop status updated successfully',
            'status' => $shop->status
        ]);
    }

    // User Management Methods
    public function editUser(User $user)
    {
        return response()->json($user);
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

    public function deleteUser(User $user)
    {
        // Don't allow deleting the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last admin user'
            ], 422);
        }

        // Delete associated records
        $user->ratings()->delete();
        $user->appointments()->delete();
        $user->favorites()->delete();
        
        // If user is a shop owner, handle shop deletion
        if ($user->shop) {
            $user->shop->appointments()->delete();
            $user->shop->ratings()->delete();
            $user->shop->services()->delete();
            $user->shop->delete();
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function addUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string',
            'role' => 'required|in:admin,customer,shop_owner'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User added successfully',
            'user' => $user
        ]);
    }
} 