<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    public function index()
    {
        $shop = Auth::user()->shop;
        
        // Basic stats
        $totalAppointments = Appointment::where('shop_id', $shop->id)->count();
        $totalRevenue = Appointment::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->sum('service_price');
        $totalCustomers = Appointment::where('shop_id', $shop->id)
            ->distinct('user_id')
            ->count('user_id');
        $activeServices = Service::where('shop_id', $shop->id)
            ->where('status', 'active')
            ->count();
            
        // Growth rates
        $appointmentGrowth = $this->calculateGrowth($shop->id, 'count');
        $revenueGrowth = $this->calculateGrowth($shop->id, 'sum', 'service_price', 'completed');
        $customerGrowth = $this->calculateCustomerGrowth($shop->id);
        $servicesGrowth = $this->calculateServicesGrowth($shop->id);
        
        // Monthly metrics
        $monthlyRevenue = $this->getMonthlyRevenue($shop->id);
        $monthlyAppointments = $this->getMonthlyAppointments($shop->id);
        
        // Recent activity
        $recentActivity = Appointment::where('shop_id', $shop->id)
            ->with(['user', 'employee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Service Popularity Analytics
        $serviceBookingCounts = $this->getServiceBookingCounts($shop->id);
        $mostBookedService = $serviceBookingCounts->sortByDesc('count')->first();
        $leastBookedService = $serviceBookingCounts->sortBy('count')->first();
        
        // Peak Hours Analytics
        $hourlyBookings = $this->getHourlyBookings($shop->id);
        $peakBookingHour = $hourlyBookings->sortByDesc('count')->first();
        
        // Day of Week Analytics
        $dayOfWeekBookings = $this->getDayOfWeekBookings($shop->id);
        
        // Paid Appointments Analytics
        $paidAppointments = Appointment::where('shop_id', $shop->id)
            ->with(['user', 'pet', 'employee'])
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->limit(10)
            ->get();
            
        // Calculate total revenue from paid appointments
        $paidRevenue = $paidAppointments->sum('service_price');
        
        return view('shop.analytics.index', compact(
            'totalAppointments',
            'totalRevenue',
            'totalCustomers',
            'activeServices',
            'appointmentGrowth',
            'revenueGrowth',
            'customerGrowth',
            'servicesGrowth',
            'monthlyRevenue',
            'monthlyAppointments',
            'recentActivity',
            'serviceBookingCounts',
            'mostBookedService',
            'leastBookedService',
            'hourlyBookings',
            'peakBookingHour',
            'dayOfWeekBookings',
            'paidAppointments',
            'paidRevenue'
        ));
    }
    
    /**
     * Get service booking counts for popularity analytics
     */
    private function getServiceBookingCounts($shopId)
    {
        // Get counts of appointments by service type
        return DB::table('appointments')
            ->select('service_type as name', DB::raw('COUNT(*) as count'))
            ->where('shop_id', $shopId)
            ->groupBy('service_type')
            ->orderBy('count', 'desc')
            ->get();
    }
    
    /**
     * Get hourly booking distribution
     */
    private function getHourlyBookings($shopId)
    {
        $hourlyData = DB::table('appointments')
            ->select(DB::raw('HOUR(appointment_date) as hour'), DB::raw('COUNT(*) as count'))
            ->where('shop_id', $shopId)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        // Format hours for display (12-hour format with AM/PM)
        return $hourlyData->map(function($item) {
            $hour = (int)$item->hour;
            $hour12 = $hour % 12 === 0 ? 12 : $hour % 12;
            $amPm = $hour < 12 ? 'AM' : 'PM';
            
            $item->hour_label = "{$hour12}{$amPm}";
            return $item;
        });
    }
    
    /**
     * Get day of week booking distribution
     */
    private function getDayOfWeekBookings($shopId)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        $dayData = DB::table('appointments')
            ->select(DB::raw('DAYOFWEEK(appointment_date) as day_num'), DB::raw('COUNT(*) as count'))
            ->where('shop_id', $shopId)
            ->groupBy('day_num')
            ->orderBy('day_num')
            ->get();
            
        // Convert day numbers to day names
        return $dayData->map(function($item) use ($days) {
            // DAYOFWEEK in MySQL: 1 = Sunday, 2 = Monday, etc.
            $item->day = $days[$item->day_num - 1];
            return $item;
        });
    }
    
    /**
     * Calculate growth rate compared to previous month
     */
    private function calculateGrowth($shopId, $aggregateFunction = 'count', $field = '*', $status = null)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Current month query
        $currentQuery = Appointment::where('shop_id', $shopId)
            ->whereBetween('created_at', [$currentMonth, Carbon::now()]);
            
        // Previous month query
        $previousQuery = Appointment::where('shop_id', $shopId)
            ->whereBetween('created_at', [$previousMonth, $currentMonth]);
            
        // Add status condition if provided
        if ($status) {
            $currentQuery->where('status', $status);
            $previousQuery->where('status', $status);
        }
        
        // Get counts or sums
        if ($aggregateFunction === 'count') {
            $currentValue = $currentQuery->count();
            $previousValue = $previousQuery->count();
        } elseif ($aggregateFunction === 'sum') {
            $currentValue = $currentQuery->sum($field);
            $previousValue = $previousQuery->sum($field);
        }
        
        // Calculate growth rate
        if ($previousValue > 0) {
            return (($currentValue - $previousValue) / $previousValue) * 100;
        }
        
        return $previousValue == 0 && $currentValue > 0 ? 100 : 0;
    }
    
    /**
     * Calculate customer growth rate
     */
    private function calculateCustomerGrowth($shopId)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $currentCustomers = Appointment::where('shop_id', $shopId)
            ->whereBetween('created_at', [$currentMonth, Carbon::now()])
            ->distinct('user_id')
            ->count('user_id');
            
        $previousCustomers = Appointment::where('shop_id', $shopId)
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->distinct('user_id')
            ->count('user_id');
            
        if ($previousCustomers > 0) {
            return (($currentCustomers - $previousCustomers) / $previousCustomers) * 100;
        }
        
        return $previousCustomers == 0 && $currentCustomers > 0 ? 100 : 0;
    }
    
    /**
     * Calculate services growth rate
     */
    private function calculateServicesGrowth($shopId)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $currentServices = Service::where('shop_id', $shopId)
            ->where('created_at', '<', Carbon::now())
            ->count();
            
        $previousServices = Service::where('shop_id', $shopId)
            ->where('created_at', '<', $previousMonth)
            ->count();
            
        if ($previousServices > 0) {
            return (($currentServices - $previousServices) / $previousServices) * 100;
        }
        
        return $previousServices == 0 && $currentServices > 0 ? 100 : 0;
    }
    
    /**
     * Get monthly revenue data
     */
    private function getMonthlyRevenue($shopId)
    {
        $months = collect();
        
        // Get data for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            
            $revenue = Appointment::where('shop_id', $shopId)
                ->where('status', 'completed')
                ->whereYear('appointment_date', $date->year)
                ->whereMonth('appointment_date', $date->month)
                ->sum('service_price');
                
            $months->push([
                'month' => $month,
                'total' => $revenue
            ]);
        }
        
        return $months;
    }
    
    /**
     * Get monthly appointment data
     */
    private function getMonthlyAppointments($shopId)
    {
        $months = collect();
        
        // Get data for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            
            $completed = Appointment::where('shop_id', $shopId)
                ->where('status', 'completed')
                ->whereYear('appointment_date', $date->year)
                ->whereMonth('appointment_date', $date->month)
                ->count();
                
            $pending = Appointment::where('shop_id', $shopId)
                ->whereIn('status', ['pending', 'accepted'])
                ->whereYear('appointment_date', $date->year)
                ->whereMonth('appointment_date', $date->month)
                ->count();
                
            $months->push([
                'month' => $month,
                'completed' => $completed,
                'pending' => $pending
            ]);
        }
        
        return $months;
    }
    
    /**
     * Export analytics data based on type (pdf, excel, csv)
     * 
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export($type)
    {
        $shop = Auth::user()->shop;
        $filename = 'shop_analytics_report_' . now()->format('Y-m-d_H-i-s');
        
        // Basic stats
        $totalAppointments = Appointment::where('shop_id', $shop->id)->count();
        $totalRevenue = Appointment::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->sum('service_price');
        $totalCustomers = Appointment::where('shop_id', $shop->id)
            ->distinct('user_id')
            ->count('user_id');
        $activeServices = Service::where('shop_id', $shop->id)
            ->where('status', 'active')
            ->count();
            
        // Monthly metrics
        $monthlyRevenue = $this->getMonthlyRevenue($shop->id);
            
        // Get paid appointments
        $paidAppointments = Appointment::where('shop_id', $shop->id)
            ->with(['user', 'pet', 'employee'])
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->limit(50)
            ->get();
            
        // Format data for export
        $reportData = [
            'shop_name' => $shop->name,
            'shop_type' => ucfirst($shop->type),
            'generated_at' => now()->format('F d, Y h:i A'),
            'summary' => [
                'total_appointments' => $totalAppointments,
                'total_revenue' => '₱' . number_format($totalRevenue, 2),
                'total_customers' => $totalCustomers,
                'active_services' => $activeServices,
            ],
            'monthly_revenue' => $monthlyRevenue,
            'paid_appointments' => $paidAppointments->map(function($appointment) {
                return [
                    'customer' => $appointment->user->first_name . ' ' . $appointment->user->last_name,
                    'email' => $appointment->user->email,
                    'pet' => $appointment->pet->name,
                    'service' => $appointment->service_type,
                    'employee' => $appointment->employee ? $appointment->employee->name : 'Not assigned',
                    'date' => $appointment->appointment_date->format('M d, Y'),
                    'time' => $appointment->appointment_date->format('h:i A'),
                    'amount' => '₱' . number_format($appointment->service_price, 2),
                    'paid_at' => $appointment->paid_at ? $appointment->paid_at->format('M d, Y h:i A') : 'N/A',
                ];
            })
        ];
        
        switch ($type) {
            case 'pdf':
                $pdf = PDF::loadView('exports.analytics_pdf', ['data' => $reportData]);
                return $pdf->download($filename . '.pdf');
                
            case 'excel':
                return Excel::download(new AnalyticsExport($reportData), $filename . '.xlsx');
                
            case 'csv':
                return Excel::download(new AnalyticsExport($reportData), $filename . '.csv');
                
            default:
                return back()->with('error', 'Invalid export type');
        }
    }
} 