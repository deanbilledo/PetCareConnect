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
} 