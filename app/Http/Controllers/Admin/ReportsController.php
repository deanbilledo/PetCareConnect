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

    public function shopReports(Request $request)
    {
        // Get all shop reports with their relationships
        $query = \App\Models\ShopReport::with(['user', 'shop'])
            ->orderBy('created_at', 'desc');
            
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Apply report type filter if provided
        if ($request->has('report_type') && !empty($request->report_type)) {
            $query->where('report_type', $request->report_type);
        }
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('shop', function ($shopQuery) use ($search) {
                    $shopQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $reports = $query->paginate(10);
        
        // Get unique report types for filter dropdown
        $reportTypes = \App\Models\ShopReport::distinct('report_type')->pluck('report_type');
        
        // Get user reports
        $userReportsData = $this->userReports($request);
        $userReports = $userReportsData['userReports'];
        $userReportTypes = $userReportsData['userReportTypes'];
        
        return view('admin.support', compact('reports', 'reportTypes', 'userReports', 'userReportTypes'));
    }

    // Update an existing shop report status
    public function updateReportStatus(Request $request, $id)
    {
        $report = \App\Models\ShopReport::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,resolved,dismissed',
            'admin_notes' => 'nullable|string',
        ]);
        
        $report->status = $validated['status'];
        $report->admin_notes = $validated['admin_notes'] ?? $report->admin_notes;
        
        if ($validated['status'] === 'resolved' || $validated['status'] === 'dismissed') {
            $report->resolved_at = now();
        }
        
        $report->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Report status updated successfully'
        ]);
    }

    // Get a single shop report by ID
    public function getReport($id)
    {
        try {
            $report = \App\Models\ShopReport::findOrFail($id);
            
            // Explicitly load the relationships
            $report->load(['user', 'shop']);
            
            // Create a safe response with null checks
            $response = [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'description' => $report->description,
                'status' => $report->status,
                'admin_notes' => $report->admin_notes,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'resolved_at' => $report->resolved_at,
                'user' => $report->user ? [
                    'id' => $report->user->id,
                    'name' => $report->user->name,
                    'email' => $report->user->email
                ] : null,
                'shop' => $report->shop ? [
                    'id' => $report->shop->id,
                    'name' => $report->shop->name
                ] : null
            ];
            
            // Log the report data for debugging
            \Log::info('Shop report data:', [
                'id' => $report->id,
                'has_user' => isset($report->user),
                'has_shop' => isset($report->shop),
                'response' => $response
            ]);
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error fetching shop report: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load report: ' . $e->getMessage()], 500);
        }
    }
    
    // Get all user reports with pagination and filtering
    public function userReports(Request $request)
    {
        // Get all user reports with their relationships
        $query = \App\Models\UserReport::with(['reporter', 'reportedUser'])
            ->orderBy('created_at', 'desc');
            
        // Apply status filter if provided
        if ($request->has('user_status') && !empty($request->user_status)) {
            $query->where('status', $request->user_status);
        }
        
        // Apply report type filter if provided
        if ($request->has('user_report_type') && !empty($request->user_report_type)) {
            $query->where('report_type', $request->user_report_type);
        }
        
        // Apply search filter if provided
        if ($request->has('user_search') && !empty($request->user_search)) {
            $search = $request->user_search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('reportedUser', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('reporter', function ($reporterQuery) use ($search) {
                    $reporterQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $userReports = $query->paginate(10, ['*'], 'user_page');
        
        // Get unique report types for filter dropdown
        $userReportTypes = \App\Models\UserReport::distinct('report_type')->pluck('report_type');
        
        return compact('userReports', 'userReportTypes');
    }
    
    // Get a single user report by ID
    public function getUserReport($id)
    {
        try {
            $report = \App\Models\UserReport::findOrFail($id);
            
            // Explicitly load the relationships
            $report->load(['reporter', 'reportedUser']);
            
            // Create a safe response with null checks
            $response = [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'description' => $report->description,
                'status' => $report->status,
                'admin_notes' => $report->admin_notes,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'resolved_at' => $report->resolved_at,
                'reporter' => $report->reporter ? [
                    'id' => $report->reporter->id,
                    'name' => $report->reporter->name,
                    'email' => $report->reporter->email
                ] : null,
                'reportedUser' => $report->reportedUser ? [
                    'id' => $report->reportedUser->id,
                    'name' => $report->reportedUser->name,
                    'email' => $report->reportedUser->email
                ] : null
            ];
            
            // Log the report data for debugging
            \Log::info('User report data:', [
                'id' => $report->id,
                'has_reporter' => isset($report->reporter),
                'has_reportedUser' => isset($report->reportedUser),
                'response' => $response
            ]);
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error fetching user report: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load report: ' . $e->getMessage()], 500);
        }
    }
    
    // Update user report status
    public function updateUserReportStatus(Request $request, $id)
    {
        $report = \App\Models\UserReport::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,resolved,dismissed',
            'admin_notes' => 'nullable|string',
        ]);
        
        $report->status = $validated['status'];
        $report->admin_notes = $validated['admin_notes'] ?? $report->admin_notes;
        
        if ($validated['status'] === 'resolved' || $validated['status'] === 'dismissed') {
            $report->resolved_at = now();
        }
        
        $report->save();
        
        return response()->json([
            'success' => true,
            'message' => 'User report status updated successfully'
        ]);
    }
} 