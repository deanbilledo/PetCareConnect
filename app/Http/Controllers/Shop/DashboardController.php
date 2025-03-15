<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Shop;
use App\Models\Service;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
    public function index(Request $request)
    {
        try {
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
                
            // Get all employees for the shop for the filter - with error handling
            try {
                $employees = Employee::where('shop_id', $shop->id)->get();
            } catch (\Exception $e) {
                Log::error('Error loading employees: ' . $e->getMessage());
                $employees = collect([]); // Empty collection as fallback
            }
                
            // Recent appointments query with filters
            $appointmentsQuery = Appointment::where('shop_id', $shop->id)
                ->with(['user', 'pet', 'employee', 'shop']);
                
            // Apply status filter
            if ($request->has('status') && $request->status != 'all') {
                $appointmentsQuery->where('status', $request->status);
            }
            
            // Apply employee filter
            if ($request->has('employee_id') && $request->employee_id != 'all') {
                $appointmentsQuery->where('employee_id', $request->employee_id);
            }
            
            // Apply date range filter
            if ($request->has('start_date') && $request->start_date) {
                $appointmentsQuery->whereDate('appointment_date', '>=', $request->start_date);
            }
            
            if ($request->has('end_date') && $request->end_date) {
                $appointmentsQuery->whereDate('appointment_date', '<=', $request->end_date);
            }
            
            // If only one date is specified without a range, filter by that exact date
            if ($request->has('start_date') && $request->start_date && !$request->has('end_date')) {
                $appointmentsQuery->whereDate('appointment_date', $request->start_date);
            }
            
            // Get appointments with pagination
            $appointments = $appointmentsQuery->orderBy('appointment_date', 'desc')
                ->paginate(10)
                ->withQueryString(); // This preserves the query parameters in pagination links
                
            // Get shop rating
            $shop->rating = $shop->reviews()->avg('rating') ?? 0;
            
            return view('shop.dashboard', compact(
                'shop',
                'todayAppointments',
                'totalRevenue',
                'pendingAppointments',
                'appointments',
                'employees'
            ));
        } catch (\Exception $e) {
            Log::error('Error in shop dashboard: ' . $e->getMessage());
            return view('shop.dashboard', [
                'error' => 'There was an error loading the dashboard. Please try again later.',
                'employees' => collect([]),
                'appointments' => collect([]),
                'todayAppointments' => 0,
                'totalRevenue' => 0,
                'pendingAppointments' => 0,
                'shop' => Auth::user()->shop
            ]);
        }
    }
} 