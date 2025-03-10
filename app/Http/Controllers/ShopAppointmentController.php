<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ShopAppointmentController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        
        // Get appointments grouped by date
        $appointments = $shop->appointments()
            ->with(['user', 'pet'])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->groupBy(function($appointment) {
                return $appointment->appointment_date->format('Y-m-d');
            });

        // Get cancellation requests
        $cancellationRequests = $shop->appointments()
            ->where('status', 'cancellation_requested')
            ->with(['user', 'pet'])
            ->get();

        // Get reschedule requests with necessary relationships
        $rescheduleRequestsQuery = $shop->appointments()
            ->where('status', 'reschedule_requested')
            ->with(['user', 'pet', 'service']);

        // Check if requested_date column exists, if not use appointment_date
        if (Schema::hasColumn('appointments', 'requested_date')) {
            $rescheduleRequestsQuery->orderBy('requested_date', 'asc');
        } else {
            $rescheduleRequestsQuery->orderBy('appointment_date', 'asc');
            // Log missing column for debugging
            Log::warning('requested_date column not found in appointments table. Using appointment_date instead.');
        }

        $rescheduleRequests = $rescheduleRequestsQuery->get();

        // Log reschedule requests for debugging
        Log::debug('Reschedule requests:', [
            'count' => $rescheduleRequests->count(),
            'requests' => $rescheduleRequests->map(function($req) {
                return [
                    'id' => $req->id,
                    'status' => $req->status,
                    'appointment_date' => $req->appointment_date,
                    'requested_date' => $req->requested_date ?? null,
                ];
            })
        ]);

        // Count pending requests
        $pendingCancellations = $cancellationRequests->count();
        $pendingReschedules = $rescheduleRequests->count();

        // Count new appointments (created within the last 24 hours and with status 'pending')
        $newAppointments = $shop->appointments()
            ->where('status', 'pending')
            ->whereNull('viewed_at')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();

        // If we have any new appointments, update the session to track that we've shown the notification
        if ($newAppointments > 0) {
            session()->put('shown_new_appointments_notification', true);
        }

        return view('shop.appointments.index', compact(
            'appointments',
            'cancellationRequests',
            'rescheduleRequests',
            'pendingCancellations',
            'pendingReschedules',
            'newAppointments'
        ));
    }

    /**
     * Mark an appointment as viewed.
     * 
     * @param Appointment $appointment
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsViewed(Appointment $appointment)
    {
        // Check if the user has permission to view this appointment
        if ($appointment->shop_id !== auth()->user()->shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark the appointment as viewed
        $appointment->markAsViewed();

        return response()->json([
            'success' => true,
            'message' => 'Appointment marked as viewed'
        ]);
    }

    /**
     * Display the specified appointment with pet health records.
     *
     * @param Appointment $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        // Check if the shop owner has permission to view this appointment
        if ($appointment->shop_id !== auth()->user()->shop->id) {
            return redirect()->route('shop.appointments')
                ->with('error', 'You are not authorized to view this appointment.');
        }

        // Load the appointment with all necessary relationships
        $appointment->load([
            'user', 
            'pet', 
            'employee',
            'pet.vaccinations',
            'pet.parasiteControls',
            'pet.healthIssues',
            'appointmentNotes'
        ]);

        // Debug logging
        \Log::info('Loading appointment in ShopAppointmentController:', [
            'appointment_id' => $appointment->id,
            'has_notes_relation' => $appointment->relationLoaded('appointmentNotes'),
            'notes_count' => $appointment->appointmentNotes->count(),
            'raw_notes' => $appointment->appointmentNotes->toArray()
        ]);

        // Mark appointment as viewed if not already
        if (!$appointment->viewed_at) {
            $appointment->markAsViewed();
        }

        return view('shop.appointments.show', compact('appointment'));
    }

    /**
     * Display all paid appointments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function payments(Request $request)
    {
        $shop = auth()->user()->shop;
        
        // Build query for paid appointments
        $query = $shop->appointments()
            ->where('payment_status', 'paid')
            ->with(['user', 'pet', 'service', 'services', 'employee']);
        
        // Filter by date range
        $dateRange = $request->input('date_range', 'all');
        $startDate = null;
        $endDate = now();
        
        switch ($dateRange) {
            case 'this_week':
                $startDate = now()->startOfWeek();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                break;
            case 'last_year':
                $startDate = now()->subYear()->startOfYear();
                $endDate = now()->subYear()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                }
                if ($request->filled('end_date')) {
                    $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                }
                break;
        }
        
        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }
        
        $query->where('paid_at', '<=', $endDate);
        
        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }
        
        // Save the original query for later use
        $baseQuery = clone $query;
        
        // Get all appointments with applied filters except service filter
        $allFilteredAppointments = $baseQuery->get();
        
        // Filter by service (handle this in PHP since the structure might be different)
        if ($request->filled('service_id')) {
            $serviceId = $request->input('service_id');
            $paidAppointments = $allFilteredAppointments->filter(function($appointment) use ($serviceId) {
                // Check if the appointment has the service directly
                if ($appointment->service && $appointment->service->id == $serviceId) {
                    return true;
                }
                
                // Check if the appointment has the service through the many-to-many relationship
                if ($appointment->services && $appointment->services->contains('id', $serviceId)) {
                    return true;
                }
                
                return false;
            });
        } else {
            // If no service filter, use all appointments
            $paidAppointments = $allFilteredAppointments;
        }
        
        // Sort the collection by paid_at in descending order
        $paidAppointments = $paidAppointments->sortByDesc('paid_at')->values();
        
        // Check if export requested
        if ($request->has('export')) {
            $exportFormat = $request->input('export');
            
            // Prepare export data
            $exportData = $paidAppointments->map(function($appointment) {
                return [
                    'Date' => $appointment->paid_at->format('Y-m-d'),
                    'Customer' => $appointment->user ? $appointment->user->name : 'Unknown',
                    'Pet' => $appointment->pet ? $appointment->pet->name : 'Unknown',
                    'Service' => $appointment->service ? $appointment->service->name : $appointment->service_type,
                    'Employee' => $appointment->employee ? $appointment->employee->name : 'Unassigned',
                    'Amount' => $appointment->service_price,
                ];
            });
            
            if ($exportFormat === 'csv') {
                // Generate CSV file
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="payment_history_'.now()->format('Y-m-d').'.csv"',
                ];
                
                $callback = function() use ($exportData) {
                    $file = fopen('php://output', 'w');
                    
                    // Add headers
                    if ($exportData->count() > 0) {
                        fputcsv($file, array_keys($exportData->first()));
                    }
                    
                    // Add rows
                    foreach ($exportData as $row) {
                        fputcsv($file, $row);
                    }
                    
                    fclose($file);
                };
                
                return response()->stream($callback, 200, $headers);
            } elseif ($exportFormat === 'pdf') {
                // For PDF export, you would typically use a package like dompdf or barryvdh/laravel-dompdf
                // For now, we'll just return a message that PDF export is not implemented
                return back()->with('info', 'PDF export functionality requires additional setup.');
            }
        }
        
        // Group appointments by month
        $groupedAppointments = $paidAppointments->groupBy(function($appointment) {
            return $appointment->paid_at->format('F Y');
        });
        
        // Calculate total revenue
        $totalRevenue = $paidAppointments->sum('service_price');
        
        // Count total paid appointments
        $totalPaidAppointments = $paidAppointments->count();
        
        // Get recent payments (last 30 days)
        $thirtyDaysAgo = now()->subDays(30);
        $recentPayments = $paidAppointments->filter(function($appointment) use ($thirtyDaysAgo) {
            return $appointment->paid_at >= $thirtyDaysAgo;
        });
        
        // Calculate recent revenue
        $recentRevenue = $recentPayments->sum('service_price');
        
        // Get all employees and services for filter dropdowns
        $employees = $shop->employees()->get();
        $services = $shop->services()->get();
        
        return view('shop.payments.index', compact(
            'groupedAppointments',
            'totalRevenue',
            'totalPaidAppointments',
            'recentRevenue',
            'employees',
            'services',
            'dateRange',
            'startDate',
            'endDate'
        ));
    }
} 