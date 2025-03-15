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
        
        // Get appeals data
        $appealsQuery = \App\Models\Appeal::with(['appealable'])
            ->orderBy('created_at', 'desc');
            
        // Apply status filter if provided
        if ($request->has('appeal_status') && !empty($request->appeal_status)) {
            $appealsQuery->where('status', $request->appeal_status);
        }
        
        // Apply appeal type filter if provided
        if ($request->has('appeal_type') && !empty($request->appeal_type)) {
            if ($request->appeal_type === 'shop') {
                $appealsQuery->where('appealable_type', 'App\Models\ShopReport');
            } elseif ($request->appeal_type === 'user') {
                $appealsQuery->where('appealable_type', 'App\Models\UserReport');
            }
        }
        
        // Apply search filter if provided
        if ($request->has('appeal_search') && !empty($request->appeal_search)) {
            $search = $request->appeal_search;
            $appealsQuery->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('appealable', function ($subQuery) use ($search) {
                      $subQuery->where('description', 'like', "%{$search}%");
                  });
            });
        }
        
        $appeals = $appealsQuery->paginate(10, ['*'], 'appeals_page');
        
        return view('admin.support', compact('reports', 'reportTypes', 'userReports', 'userReportTypes', 'appeals'));
    }

    // Update an existing shop report status
    public function updateReportStatus(Request $request, $id)
    {
        $report = \App\Models\ShopReport::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,resolved,dismissed',
            'admin_notes' => 'nullable|string',
            'resolution_explanation' => 'nullable|string|required_if:status,resolved',
        ]);
        
        $previousStatus = $report->status;
        $report->status = $validated['status'];
        $report->admin_notes = $validated['admin_notes'] ?? $report->admin_notes;
        
        if ($validated['status'] === 'resolved' || $validated['status'] === 'dismissed') {
            $report->resolved_at = now();
        }
        
        $report->save();
        
        // Send notification if status changed to under_review or resolved
        if ($previousStatus !== $validated['status']) {
            $user = $report->user;
            
            if ($user) {
                if ($validated['status'] === 'under_review') {
                    // Send notification that report is under review
                    $this->sendReportStatusNotification(
                        $user,
                        'report_under_review',
                        'Your report is under review',
                        'Your report for ' . $report->shop->name . ' is now under review by our admin team.',
                        null
                    );
                } elseif ($validated['status'] === 'resolved') {
                    // Send notification that report is resolved
                    $this->sendReportStatusNotification(
                        $user,
                        'report_resolved',
                        'Your report has been resolved',
                        'Your report for ' . $report->shop->name . ' has been resolved by our admin team.',
                        $validated['resolution_explanation'] ?? null
                    );
                } elseif ($validated['status'] === 'dismissed') {
                    // Send notification that report is dismissed
                    $this->sendReportStatusNotification(
                        $user,
                        'report_dismissed',
                        'Your report has been dismissed',
                        'Your report for ' . $report->shop->name . ' has been dismissed by our admin team.',
                        $validated['resolution_explanation'] ?? null
                    );
                }
            }
        }
        
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
                'image_url' => $report->getImageUrl(),
                'has_image' => !is_null($report->image_path),
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
                'has_image' => !is_null($report->image_path),
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
                'image_url' => $report->getImageUrl(),
                'has_image' => !is_null($report->image_path),
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
                'has_image' => !is_null($report->image_path),
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
            'resolution_explanation' => 'nullable|string|required_if:status,resolved',
        ]);
        
        $previousStatus = $report->status;
        $report->status = $validated['status'];
        $report->admin_notes = $validated['admin_notes'] ?? $report->admin_notes;
        
        if ($validated['status'] === 'resolved' || $validated['status'] === 'dismissed') {
            $report->resolved_at = now();
        }
        
        $report->save();
        
        // Send notification if status changed to under_review or resolved
        if ($previousStatus !== $validated['status']) {
            $reporter = $report->reporter;
            
            if ($reporter) {
                if ($validated['status'] === 'under_review') {
                    // Send notification that report is under review
                    $this->sendReportStatusNotification(
                        $reporter,
                        'report_under_review',
                        'Your report is under review',
                        'Your report about user ' . $report->reportedUser->name . ' is now under review by our admin team.',
                        null
                    );
                } elseif ($validated['status'] === 'resolved') {
                    // Send notification that report is resolved
                    $this->sendReportStatusNotification(
                        $reporter,
                        'report_resolved',
                        'Your report has been resolved',
                        'Your report about user ' . $report->reportedUser->name . ' has been resolved by our admin team.',
                        $validated['resolution_explanation'] ?? null
                    );
                } elseif ($validated['status'] === 'dismissed') {
                    // Send notification that report is dismissed
                    $this->sendReportStatusNotification(
                        $reporter,
                        'report_dismissed',
                        'Your report has been dismissed',
                        'Your report about user ' . $report->reportedUser->name . ' has been dismissed by our admin team.',
                        $validated['resolution_explanation'] ?? null
                    );
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'User report status updated successfully'
        ]);
    }
    
    /**
     * Send notification about report status change to user
     */
    private function sendReportStatusNotification($user, $type, $title, $message, $explanation = null)
    {
        if ($explanation) {
            $message .= "\n\nExplanation from admin: " . $explanation;
        }
        
        $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'status' => 'unread',
            'action_text' => 'View Details',
            'action_url' => route('notifications.index')
        ]);
    }

    /**
     * Send a notification to a user who reported a shop
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The shop report ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotificationToShop(Request $request, $id)
    {
        $report = \App\Models\ShopReport::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Get the user who submitted the report
        $user = $report->user;
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Could not find the user who submitted this report'
            ], 404);
        }
        
        try {
            // Create a notification
            $notification = $user->notifications()->create([
                'type' => 'admin_note',
                'title' => $validated['title'],
                'message' => $validated['message'],
                'status' => 'unread',
                'action_text' => 'View Details',
                'action_url' => route('notifications.index')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'notification' => $notification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send a notification to a user who was reported or who reported another user
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The user report ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotificationToUser(Request $request, $id)
    {
        $report = \App\Models\UserReport::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient' => 'nullable|string|in:reporter,reported', // Optional parameter to determine recipient
        ]);
        
        // Determine the recipient - default to reporter if not specified
        $recipient = $validated['recipient'] ?? 'reporter';
        
        // Get the appropriate user
        $user = $recipient === 'reporter' ? $report->reporter : $report->reportedUser;
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Could not find the specified user'
            ], 404);
        }
        
        try {
            // Create a notification
            $notification = $user->notifications()->create([
                'type' => 'admin_note',
                'title' => $validated['title'],
                'message' => $validated['message'],
                'status' => 'unread',
                'action_text' => 'View Details',
                'action_url' => route('notifications.index')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'notification' => $notification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }
} 