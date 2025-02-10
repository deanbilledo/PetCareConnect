<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        return view('shop.appointments.index', compact(
            'appointments',
            'cancellationRequests',
            'rescheduleRequests',
            'pendingCancellations',
            'pendingReschedules'
        ));
    }
} 