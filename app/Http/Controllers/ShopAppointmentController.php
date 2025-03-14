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
            ->with(['user', 'pet', 'employee'])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->groupBy(function($appointment) {
                return $appointment->appointment_date->format('Y-m-d');
            });

        // Get employees for filter
        $employees = $shop->employees()
            ->orderBy('name')
            ->get();

        // Get cancellation requests - these are just appointments with the right status
        $cancellationRequests = $shop->appointments()
            ->where('status', 'cancellation_requested')
            ->with(['user', 'pet', 'service'])
            ->orderBy('cancellation_requested_at', 'desc')
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

        // Get count of unread reschedule requests
        $unreadRescheduleCount = $rescheduleRequests->filter(function($request) {
            return !$request->viewed_at;
        })->count();

        // Log reschedule requests for debugging
        Log::debug('Reschedule requests:', [
            'count' => $rescheduleRequests->count(),
            'unread_count' => $unreadRescheduleCount,
            'requests' => $rescheduleRequests->map(function($req) {
                return [
                    'id' => $req->id,
                    'status' => $req->status,
                    'appointment_date' => $req->appointment_date,
                    'requested_date' => $req->requested_date ?? null,
                    'viewed_at' => $req->viewed_at
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

        // Get recently cancelled appointments (within the last 48 hours)
        $recentlyCancelled = $shop->appointments()
            ->where('status', 'cancelled')
            ->where('cancelled_by', '!=', 'shop') // Only customer cancellations
            ->where('cancelled_at', '>=', Carbon::now()->subHours(48))
            ->with(['user', 'pet', 'service'])
            ->orderBy('cancelled_at', 'desc')
            ->get();

        // If we have any new appointments, update the session to track that we've shown the notification
        if ($newAppointments > 0) {
            session()->put('shown_new_appointments_notification', true);
        }

        // If we have new reschedule requests, add a flash message to notify the shop owner
        if ($unreadRescheduleCount > 0) {
            session()->flash('reschedule_notification', [
                'count' => $unreadRescheduleCount,
                'message' => "You have {$unreadRescheduleCount} new reschedule " . 
                            ($unreadRescheduleCount == 1 ? 'request' : 'requests') . 
                            " that " . ($unreadRescheduleCount == 1 ? 'requires' : 'require') . 
                            " your attention."
            ]);
        }

        return view('shop.appointments.index', compact(
            'appointments',
            'cancellationRequests',
            'rescheduleRequests',
            'pendingCancellations',
            'pendingReschedules',
            'newAppointments',
            'unreadRescheduleCount',
            'employees',
            'recentlyCancelled'
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
     * Mark a reschedule request as viewed.
     *
     * @param Appointment $appointment
     * @return \Illuminate\Http\JsonResponse
     */
    public function markRescheduleAsViewed(Appointment $appointment)
    {
        // Check if the user has permission to view this appointment
        if ($appointment->shop_id !== auth()->user()->shop->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ensure it's a reschedule request
        if ($appointment->status !== 'reschedule_requested') {
            return response()->json(['error' => 'This is not a reschedule request'], 400);
        }

        // Mark the appointment as viewed
        $appointment->viewed_at = now();
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Reschedule request marked as viewed'
        ]);
    }

    /**
     * Create a notification for the shop owner when a reschedule request is received.
     * This method should be called from the AppointmentController when a reschedule request is submitted.
     *
     * @param Appointment $appointment
     * @return bool
     */
    public static function createRescheduleNotification(Appointment $appointment)
    {
        try {
            // Get the shop owner
            $shop = $appointment->shop;
            $shopOwner = $shop->user;
            
            if (!$shopOwner) {
                Log::error('Shop owner not found for appointment reschedule notification', [
                    'appointment_id' => $appointment->id,
                    'shop_id' => $shop->id
                ]);
                return false;
            }
            
            // Format dates for notification
            $oldDate = $appointment->appointment_date->format('F j, Y g:i A');
            $newDate = \Carbon\Carbon::parse($appointment->requested_date)->format('F j, Y g:i A');
            
            // Create a notification for the shop owner
            $notification = $shopOwner->notifications()->create([
                'type' => 'appointment_reschedule',
                'title' => 'Appointment Reschedule Request',
                'message' => "{$appointment->user->name} has requested to reschedule their appointment from {$oldDate} to {$newDate}.",
                'action_url' => route('shop.appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread'
            ]);
            
            Log::info('Created reschedule notification', [
                'notification_id' => $notification->id,
                'appointment_id' => $appointment->id,
                'shop_owner_id' => $shopOwner->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create reschedule notification', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
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

    /**
     * Reassign an appointment to a different employee
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reassignEmployee(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            // Check if the shop owner has permission to modify this appointment
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You are not authorized to modify this appointment.'
                ], 403);
            }
            
            // Check if the appointment status allows reassignment
            if (!in_array($appointment->status, ['pending', 'accepted'])) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Only pending or accepted appointments can be reassigned.'
                ], 400);
            }
            
            // Validate the request
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id'
            ]);
            
            // Update the appointment
            $oldEmployeeId = $appointment->employee_id;
            $appointment->employee_id = $validated['employee_id'];
            $appointment->save();
            
            // Get the old and new employee names for the notification
            $oldEmployee = null;
            $newEmployee = null;
            
            if ($oldEmployeeId) {
                $oldEmployee = \App\Models\Employee::find($oldEmployeeId);
            }
            
            if ($appointment->employee_id) {
                $newEmployee = \App\Models\Employee::find($appointment->employee_id);
            }
            
            // Create notification for the user about employee reassignment
            $appointment->user->notifications()->create([
                'type' => 'employee_reassigned',
                'title' => 'Appointment Employee Reassigned',
                'message' => "Your appointment at {$appointment->shop->name} for {$appointment->appointment_date->format('F j, Y g:i A')} has been reassigned to " . ($newEmployee ? $newEmployee->name : 'a new employee') . ".",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Appointment',
                'status' => 'unread'
            ]);
            
            // Log the reassignment
            Log::info('Appointment reassigned', [
                'appointment_id' => $appointment->id,
                'old_employee_id' => $oldEmployeeId,
                'new_employee_id' => $appointment->employee_id,
                'modified_by' => auth()->user()->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment reassigned successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error reassigning appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while reassigning the appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for scheduling a follow-up appointment
     *
     * @param \App\Models\Appointment $appointment
     * @return \Illuminate\View\View
     */
    public function showFollowUpForm(\App\Models\Appointment $appointment)
    {
        // Check if the shop owner has permission to create follow-up for this appointment
        if ($appointment->shop_id !== auth()->user()->shop->id) {
            return redirect()->back()->with('error', 'You are not authorized to schedule follow-ups for this appointment.');
        }
        
        // Check if the appointment is completed
        if ($appointment->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed appointments can have follow-ups scheduled.');
        }
        
        // Get all active services for the shop
        $services = auth()->user()->shop->services()->where('status', 'active')->get();
        
        return view('shop.appointments.follow-up', [
            'appointment' => $appointment,
            'services' => $services
        ]);
    }

    /**
     * Schedule a follow-up appointment for an existing appointment
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Appointment $appointment
     * @return \Illuminate\Http\Response
     */
    public function scheduleFollowUp(Request $request, \App\Models\Appointment $appointment)
    {
        try {
            // Check if the shop owner has permission to create follow-up for this appointment
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You are not authorized to schedule follow-ups for this appointment.'
                    ], 403);
                }
                return redirect()->back()->with('error', 'You are not authorized to schedule follow-ups for this appointment.');
            }
            
            // Check if the appointment is completed
            if ($appointment->status !== 'completed') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Only completed appointments can have follow-ups scheduled.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Only completed appointments can have follow-ups scheduled.');
            }
            
            // Validate the request
            $validated = $request->validate([
                'service_type' => 'required|string',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|string',
                'employee_id' => 'required|exists:employees,id',
                'service_price' => 'required|numeric',
                'notes' => 'nullable|string'
            ]);
            
            // Get price for the service
            $service = auth()->user()->shop->services()
                ->where('name', $validated['service_type'])
                ->first();
                
            if (!$service) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected service is not available in this shop.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Selected service is not available in this shop.')->withInput();
            }
            
            // Combine date and time to create appointment datetime
            $appointmentDateTime = \Carbon\Carbon::parse(
                $validated['appointment_date'] . ' ' . $validated['appointment_time']
            );
            
            // Create the follow-up appointment
            $followUpAppointment = new \App\Models\Appointment([
                'user_id' => $appointment->user_id,
                'shop_id' => $appointment->shop_id,
                'pet_id' => $appointment->pet_id,
                'employee_id' => $validated['employee_id'],
                'service_type' => $validated['service_type'],
                'service_price' => $validated['service_price'],
                'appointment_date' => $appointmentDateTime,
                'status' => 'accepted', // Auto-accept the follow-up appointment
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'],
                'is_follow_up' => true,
                'follow_up_for' => $appointment->id,
                'accepted_at' => now()
            ]);
            
            $followUpAppointment->save();
            
            // Create notification for the customer
            $appointment->user->notifications()->create([
                'type' => 'follow_up_scheduled',
                'title' => 'Follow-up Appointment Scheduled',
                'message' => "A follow-up appointment has been scheduled for you at {$appointment->shop->name} on {$appointmentDateTime->format('F j, Y')} at {$appointmentDateTime->format('g:i A')}.",
                'action_url' => route('shop.appointments.show', $followUpAppointment),
                'action_text' => 'View Appointment',
                'status' => 'unread'
            ]);
            
            // Log the follow-up appointment creation
            \Log::info('Follow-up appointment scheduled', [
                'original_appointment_id' => $appointment->id,
                'follow_up_appointment_id' => $followUpAppointment->id,
                'scheduled_by' => auth()->user()->id,
                'for_user' => $appointment->user_id,
                'date_time' => $appointmentDateTime
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Follow-up appointment scheduled successfully',
                    'appointment_id' => $followUpAppointment->id
                ]);
            }
            
            return redirect()->route('shop.appointments.show', $followUpAppointment)
                ->with('success', 'Follow-up appointment scheduled successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error scheduling follow-up appointment', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while scheduling the follow-up appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while scheduling the follow-up appointment: ' . $e->getMessage())
                ->withInput();
        }
    }
} 