<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeOffRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('has-shop');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = auth()->user()->shop;
        $employees = $shop->employees()->with('services')->latest()->get();
        return view('shop.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time',
            'profile_photo' => 'nullable|image|max:1024',
            'bio' => 'nullable|string'
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('employee-photos', 'public');
        }

        $employee = $shop->employees()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employee->id)],
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time',
            'profile_photo' => 'nullable|image|max:1024',
            'bio' => 'nullable|string'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('employee-photos', 'public');
        }

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        if ($employee->profile_photo) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee removed successfully'
        ]);
    }

    public function restore(Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $employee->restore();

        return response()->json([
            'success' => true,
            'message' => 'Employee restored successfully'
        ]);
    }

    public function getEvents(Request $request)
    {
        try {
            $shop = auth()->user()->shop;
            
            // Parse and validate dates
            $start = $request->query('start');
            $end = $request->query('end');
            $employeeId = $request->query('employee_id');

            if (!$start || !$end) {
                return response()->json([
                    'success' => false,
                    'error' => 'Start and end dates are required',
                    'events' => []
                ]);
            }

            \Log::info('Fetching events with params:', [
                'start' => $start,
                'end' => $end,
                'employee_id' => $employeeId,
                'shop_id' => $shop->id
            ]);

            // Get employee schedules
            $query = $shop->employeeSchedules();
            $query->whereBetween('start', [$start, $end]);

            // Filter by employee if provided and valid
            if (!empty($employeeId) && $employeeId !== 'null' && $employeeId !== 'undefined') {
                // Verify the employee belongs to this shop
                $employee = $shop->employees()->find($employeeId);
                if ($employee) {
                    $query->where('employee_id', $employeeId);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid employee ID',
                        'events' => []
                    ]);
                }
            }

            $events = $query->with('employee')->get();

            // Get time off requests
            $timeOffQuery = $shop->timeOffRequests()
                ->whereIn('status', ['pending', 'approved'])
                ->where(function($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
                });

            // Apply employee filter to time off requests if needed
            if (!empty($employeeId) && $employeeId !== 'null' && $employeeId !== 'undefined') {
                $timeOffQuery->where('employee_id', $employeeId);
            }

            $timeOffRequests = $timeOffQuery->with('employee')->get();

            // Format regular events
            $formattedEvents = $events->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->employee 
                        ? $schedule->title . ' - ' . $schedule->employee->name
                        : $schedule->title,
                    'start' => $schedule->start->format('Y-m-d\TH:i:s'),
                    'end' => $schedule->end->format('Y-m-d\TH:i:s'),
                    'employee_id' => $schedule->employee_id,
                    'type' => $schedule->type,
                    'status' => $schedule->status,
                    'notes' => $schedule->notes,
                    'color' => $schedule->type === 'time_off' ? '#ff4d4d' : '#4299e1'
                ];
            });

            // Format and add time off requests
            $timeOffEvents = $timeOffRequests->map(function ($timeOff) {
                $status = $timeOff->status === 'pending' ? '' : '';
                return [
                    'id' => 'timeoff_' . $timeOff->id,
                    'title' => "Time Off - {$timeOff->employee->name} {$status}",
                    'start' => $timeOff->start_date->format('Y-m-d'),
                    'end' => $timeOff->end_date->addDays(1)->format('Y-m-d'), // Add one day to make it inclusive
                    'employee_id' => $timeOff->employee_id,
                    'type' => 'time_off',
                    'status' => $timeOff->status,
                    'notes' => $timeOff->reason,
                    'color' => $timeOff->status === 'pending' ? '#ffa500' : '#ff4d4d', // Orange for pending, Red for approved
                    'allDay' => true
                ];
            });

            // Combine both types of events
            $allEvents = $formattedEvents->concat($timeOffEvents);

            return response()->json([
                'success' => true,
                'events' => $allEvents
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getEvents:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load events: ' . $e->getMessage(),
                'events' => []
            ], 500);
        }
    }

    public function storeEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'employee_id' => 'required|exists:employees,id',
                'type' => 'sometimes|string|in:shift,time_off',
                'status' => 'sometimes|string|in:active,cancelled'
            ]);

            $shop = auth()->user()->shop;
            
            // Verify the employee belongs to this shop
            $employee = $shop->employees()->findOrFail($validated['employee_id']);
            
            // Add shop_id to the validated data
            $validated['shop_id'] = $shop->id;
            
            $schedule = $shop->employeeSchedules()->create($validated);

            return response()->json([
                'success' => true,
                'event' => $schedule
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating event: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Failed to create event: ' . $e->getMessage()], 500);
        }
    }

    public function updateEvent(Request $request, $eventId)
    {
        try {
            $validated = $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after:start'
            ]);

            $shop = auth()->user()->shop;
            $schedule = $shop->employeeSchedules()->findOrFail($eventId);
            $schedule->update($validated);

            return response()->json([
                'success' => true,
                'event' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update event'], 500);
        }
    }

    public function deleteEvent($eventId)
    {
        try {
            $shop = auth()->user()->shop;
            $schedule = $shop->employeeSchedules()->findOrFail($eventId);
            $schedule->delete();

            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete event'], 500);
        }
    }

    public function getTimeOff()
    {
        try {
            $shop = auth()->user()->shop;
            $timeOff = $shop->timeOffRequests()
                ->with('employee')
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'timeOff' => $timeOff
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching time off requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch time off requests'
            ], 500);
        }
    }

    public function storeTimeOff(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'required|string'
            ]);

            $shop = auth()->user()->shop;
            
            // Verify the employee belongs to this shop
            $employee = $shop->employees()->findOrFail($validated['employee_id']);
            
            $timeOff = $shop->timeOffRequests()->create([
                'employee_id' => $validated['employee_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'reason' => $validated['reason'],
                'status' => 'pending'
            ]);

            // Load the employee relationship for the response
            $timeOff->load('employee');

            return response()->json([
                'success' => true,
                'timeOff' => $timeOff
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating time off request: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create time off request'
            ], 500);
        }
    }

    public function deleteTimeOff($id)
    {
        try {
            $shop = auth()->user()->shop;
            $timeOff = $shop->timeOffRequests()->findOrFail($id);
            $timeOff->delete();

            return response()->json([
                'success' => true,
                'message' => 'Time off request deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting time off request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete time off request'
            ], 500);
        }
    }

    /**
     * Get employee analytics data for performance tab
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics(Request $request)
    {
        $shop = auth()->user()->shop;
        $period = $request->input('period', 30); // Default to 30 days
        $serviceType = $request->input('service_type', '');
        
        // Set date range based on period
        $endDate = now();
        $startDate = ($period === 'all') ? null : now()->subDays($period);
        
        // Build the base query for appointments
        $appointmentsQuery = $shop->appointments()
            ->where('status', 'completed');
            
        // Apply date filter if not 'all time'
        if ($startDate) {
            $appointmentsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Apply service type filter if specified
        if ($serviceType) {
            $appointmentsQuery->where('service_type', $serviceType);
        }
        
        // Get all relevant appointments
        $appointments = $appointmentsQuery->with(['staffRatings', 'employee'])->get();
        
        // Initialize aggregate stats
        $totalStats = [
            'completed' => 0,
            'revenue' => 0,
            'avgRating' => 0,
            'avgTime' => 0,
        ];
        
        // Initialize employee stats tracking
        $employeeStats = [];
        
        // Process all appointments
        foreach ($appointments as $appointment) {
            // Skip if no employee assigned
            if (!$appointment->employee_id) continue;
            
            // Update total stats
            $totalStats['completed']++;
            $totalStats['revenue'] += $appointment->service_price;
            
            // Initialize employee entry if not exists
            if (!isset($employeeStats[$appointment->employee_id])) {
                $employee = $appointment->employee;
                $employeeStats[$appointment->employee_id] = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'profile_photo_url' => $employee->profile_photo_url,
                    'completed' => 0,
                    'revenue' => 0,
                    'ratings' => [],
                    'avg_rating' => 0,
                    'avg_completion_time' => 0
                ];
            }
            
            // Update employee stats
            $employeeStats[$appointment->employee_id]['completed']++;
            $employeeStats[$appointment->employee_id]['revenue'] += $appointment->service_price;
            
            // Get staff rating for this appointment if it exists
            $staffRating = $appointment->staffRatings()->first();
            if ($staffRating) {
                $employeeStats[$appointment->employee_id]['ratings'][] = $staffRating->rating;
                
                // Add to total ratings count for calculating overall average
                if (!isset($totalStats['ratingSum'])) $totalStats['ratingSum'] = 0;
                if (!isset($totalStats['ratingCount'])) $totalStats['ratingCount'] = 0;
                $totalStats['ratingSum'] += $staffRating->rating;
                $totalStats['ratingCount']++;
            }
        }
        
        // Calculate averages for each employee
        foreach ($employeeStats as $employeeId => $stats) {
            if (count($stats['ratings']) > 0) {
                $employeeStats[$employeeId]['avg_rating'] = array_sum($stats['ratings']) / count($stats['ratings']);
            }
            
            // Set a placeholder completion time (this would ideally come from real data)
            $employeeStats[$employeeId]['avg_completion_time'] = rand(30, 90);
        }
        
        // Calculate overall averages
        if ($totalStats['completed'] > 0) {
            $totalStats['avgTime'] = 60; // Placeholder - would come from real data
        }
        
        if (isset($totalStats['ratingCount']) && $totalStats['ratingCount'] > 0) {
            $totalStats['avgRating'] = $totalStats['ratingSum'] / $totalStats['ratingCount'];
        }
        
        // Remove temp calculation fields
        unset($totalStats['ratingSum']);
        unset($totalStats['ratingCount']);
        
        // Convert to array of employee stats
        $employeeStatsArray = array_values($employeeStats);
        
        return response()->json([
            'success' => true,
            'stats' => [
                'total' => $totalStats,
                'employees' => $employeeStatsArray
            ]
        ]);
    }
    
    /**
     * Get detailed stats for a specific employee
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Employee $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailedStats(Request $request, Employee $employee)
    {
        // Check if the employee belongs to the authenticated user's shop
        if ($employee->shop_id !== auth()->user()->shop->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $period = $request->input('period', 30);
        $serviceType = $request->input('service_type', '');
        
        // Set date range based on period
        $endDate = now();
        $startDate = ($period === 'all') ? null : now()->subDays($period);
        
        // Get recent appointments for this employee
        $appointmentsQuery = $employee->appointments()
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc');
            
        // Apply date filter if not 'all time'
        if ($startDate) {
            $appointmentsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Apply service type filter if specified
        if ($serviceType) {
            $appointmentsQuery->where('service_type', $serviceType);
        }
        
        $appointments = $appointmentsQuery->with(['staffRatings', 'user'])->limit(10)->get();
        
        // Format recent appointments
        $recentAppointments = $appointments->map(function($appointment) {
            $staffRating = $appointment->staffRatings()->first();
            return [
                'id' => $appointment->id,
                'date' => $appointment->appointment_date,
                'service_type' => $appointment->service_type,
                'revenue' => $appointment->service_price,
                'rating' => $staffRating ? $staffRating->rating : null,
            ];
        });
        
        // Get reviews
        $reviews = [];
        foreach ($appointments as $appointment) {
            $staffRating = $appointment->staffRatings()->first();
            if ($staffRating && ($staffRating->review || $staffRating->rating > 0)) {
                $reviews[] = [
                    'id' => $staffRating->id,
                    'rating' => $staffRating->rating,
                    'review' => $staffRating->review,
                    'date' => $staffRating->created_at,
                    'customer_name' => $appointment->user->first_name . ' ' . $appointment->user->last_name,
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'details' => [
                'recent_appointments' => $recentAppointments,
                'reviews' => $reviews
            ]
        ]);
    }
}
