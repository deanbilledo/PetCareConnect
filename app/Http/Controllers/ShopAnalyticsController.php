<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

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
            
        // Get recent paid appointments
        $paidAppointments = $shop->appointments()
            ->with(['user', 'pet', 'employee'])
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->take(10)
            ->get();
            
        // Calculate total revenue from paid appointments
        $paidRevenue = $paidAppointments->sum('service_price');

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
            'recentActivity',
            'paidAppointments',
            'paidRevenue'
        ));
    }

    /**
     * Export analytics data based on type (pdf, excel, csv)
     * 
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export($type)
    {
        $shop = auth()->user()->shop;
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $filename = 'shop_analytics_report_' . now()->format('Y-m-d_H-i-s');

        // Get total appointments
        $totalAppointments = $shop->appointments()->count();
        
        // Get total revenue
        $totalRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->sum('service_price');
            
        // Get total customers
        $totalCustomers = $shop->appointments()
            ->distinct('user_id')
            ->count('user_id');
            
        // Get active services
        $activeServices = $shop->services()
            ->where('status', 'active')
            ->count();
            
        // Get monthly revenue data for chart
        $monthlyRevenue = $shop->appointments()
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(5), now()])
            ->select(
                DB::raw('sum(service_price) as total'),
                DB::raw("DATE_FORMAT(created_at, '%M %Y') as month")
            )
            ->groupBy('month')
            ->orderBy('created_at')
            ->get();
            
        // Get recent paid appointments
        $paidAppointments = $shop->appointments()
            ->with(['user', 'pet', 'employee'])
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->take(50)
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