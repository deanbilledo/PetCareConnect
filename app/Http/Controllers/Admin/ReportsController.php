<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        try {
            // Get all active shops with their relationships
            $shops = Shop::with(['appointments', 'ratings'])
                ->where('status', 'active')
                ->get();

            // Get all services
            $services = Service::all();

            // Get date range (default to last 6 months)
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subMonths(5)->startOfMonth();

            // Calculate financial metrics with error handling
            $totalRevenue = Appointment::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount') ?? 0;

            $platformCommission = $totalRevenue * 0.15; // Assuming 15% platform commission
            
            $totalRefunds = Appointment::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'refunded')
                ->sum('amount') ?? 0;
                
            $netProfit = $platformCommission - $totalRefunds;

            // Get appointment statistics with error handling
            $completedAppointments = Appointment::where('status', 'completed')->count() ?? 0;
            $pendingAppointments = Appointment::where('status', 'pending')->count() ?? 0;
            $canceledAppointments = Appointment::where('status', 'cancelled')->count() ?? 0;

            // Prepare chart data
            $revenueChartData = $this->getRevenueChartData($startDate, $endDate);
            $appointmentChartData = $this->getAppointmentChartData($startDate, $endDate);

            // Get top performing shops with proper error handling
            $topShops = Shop::withCount('appointments')
                ->withAvg('ratings as average_rating', 'rating')
                ->withSum(['appointments as total_revenue' => function($query) {
                    $query->where('status', 'completed');
                }], 'amount')
                ->where('status', 'active')
                ->orderByDesc('total_revenue')
                ->take(10)
                ->get();

            // Format the data for shops that have no revenue yet
            $topShops->transform(function ($shop) {
                $shop->total_revenue = $shop->total_revenue ?? 0;
                $shop->average_rating = $shop->average_rating ?? 0;
                $shop->appointments_count = $shop->appointments_count ?? 0;
                return $shop;
            });

            return view('admin.reports', compact(
                'shops',
                'services',
                'totalRevenue',
                'platformCommission',
                'totalRefunds',
                'netProfit',
                'completedAppointments',
                'pendingAppointments',
                'canceledAppointments',
                'revenueChartData',
                'appointmentChartData',
                'topShops'
            ));

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in ReportsController@index: ' . $e->getMessage());
            
            // Redirect back with error message
            return redirect()->back()->with('error', 'There was an error loading the reports. Please try again.');
        }
    }

    public function filter(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $shopId = $request->shop_id;
        $serviceId = $request->service_id;

        $query = Appointment::whereBetween('created_at', [$startDate, $endDate]);

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        if ($serviceId) {
            $query->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            });
        }

        $totalRevenue = $query->where('status', 'completed')->sum('amount');
        $platformCommission = $totalRevenue * 0.15;
        $totalRefunds = $query->where('status', 'refunded')->sum('amount');
        $netProfit = $platformCommission - $totalRefunds;

        $completedAppointments = $query->where('status', 'completed')->count();
        $pendingAppointments = $query->where('status', 'pending')->count();
        $canceledAppointments = $query->where('status', 'cancelled')->count();

        $revenueChartData = $this->getRevenueChartData($startDate, $endDate, $shopId, $serviceId);
        $appointmentChartData = $this->getAppointmentChartData($startDate, $endDate, $shopId, $serviceId);

        return response()->json([
            'totalRevenue' => $totalRevenue,
            'platformCommission' => $platformCommission,
            'totalRefunds' => $totalRefunds,
            'netProfit' => $netProfit,
            'completedAppointments' => $completedAppointments,
            'pendingAppointments' => $pendingAppointments,
            'canceledAppointments' => $canceledAppointments,
            'revenueChartData' => $revenueChartData,
            'appointmentChartData' => $appointmentChartData,
        ]);
    }

    private function getRevenueChartData($startDate, $endDate, $shopId = null, $serviceId = null)
    {
        $months = [];
        $revenue = [];

        $currentDate = Carbon::parse($startDate);
        while ($currentDate <= $endDate) {
            $query = Appointment::whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->where('status', 'completed');

            if ($shopId) {
                $query->where('shop_id', $shopId);
            }

            if ($serviceId) {
                $query->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('services.id', $serviceId);
                });
            }

            $months[] = $currentDate->format('M Y');
            $revenue[] = $query->sum('amount');

            $currentDate->addMonth();
        }

        return [
            'labels' => $months,
            'data' => $revenue
        ];
    }

    private function getAppointmentChartData($startDate, $endDate, $shopId = null, $serviceId = null)
    {
        $months = [];
        $completed = [];
        $canceled = [];

        $currentDate = Carbon::parse($startDate);
        while ($currentDate <= $endDate) {
            $query = Appointment::whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month);

            if ($shopId) {
                $query->where('shop_id', $shopId);
            }

            if ($serviceId) {
                $query->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('services.id', $serviceId);
                });
            }

            $months[] = $currentDate->format('M Y');
            $completed[] = (clone $query)->where('status', 'completed')->count();
            $canceled[] = (clone $query)->where('status', 'cancelled')->count();

            $currentDate->addMonth();
        }

        return [
            'labels' => $months,
            'completed' => $completed,
            'canceled' => $canceled
        ];
    }

    public function export(Request $request, $format)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $shopId = $request->shop_id;
        $serviceId = $request->service_id;

        $query = Appointment::with(['shop', 'user', 'services'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        if ($serviceId) {
            $query->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            });
        }

        $appointments = $query->get();

        if ($format === 'csv') {
            return $this->exportToCsv($appointments);
        } else {
            return $this->exportToPdf($appointments);
        }
    }

    private function exportToCsv($appointments)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reports.csv"',
        ];

        $callback = function() use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Date',
                'Shop',
                'Customer',
                'Services',
                'Status',
                'Amount',
            ]);

            // Add data
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->created_at->format('Y-m-d'),
                    $appointment->shop->name,
                    $appointment->user->name,
                    $appointment->services->pluck('name')->implode(', '),
                    $appointment->status,
                    $appointment->amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($appointments)
    {
        $pdf = \PDF::loadView('admin.reports.pdf', compact('appointments'));
        return $pdf->download('reports.pdf');
    }
} 