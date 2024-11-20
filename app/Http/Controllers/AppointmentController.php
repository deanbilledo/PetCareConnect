<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $appointments = $user->appointments()
            ->with(['shop', 'pet'])
            ->orderBy('appointment_date', 'desc')
            ->get();
        
        // Add debugging
        \Log::info('User ID: ' . $user->id);
        \Log::info('Appointments count: ' . $appointments->count());
        \Log::info('Raw appointments:', $appointments->toArray());

        $groupedAppointments = $appointments->groupBy(function($appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        });

        return view('appointments.index', compact('groupedAppointments'));
    }

    public function show(Appointment $appointment)
    {
        try {
            \Log::info('Attempting to show appointment:', [
                'appointment_id' => $appointment->id,
                'user_id' => auth()->id(),
                'appointment_user_id' => $appointment->user_id
            ]);
            
            if ($appointment->user_id !== auth()->id()) {
                \Log::warning('Unauthorized appointment access attempt');
                abort(403);
            }

            $appointment->load(['shop', 'pet']);
            \Log::info('Appointment loaded successfully with relations');

            return view('appointments.show', compact('appointment'));
        } catch (\Exception $e) {
            \Log::error('Error showing appointment: ' . $e->getMessage());
            abort(500);
        }
    }

    public function cancel(Appointment $appointment, Request $request)
    {
        try {
            \Log::info('Cancel request received', [
                'appointment_id' => $appointment->id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'request_data' => $request->all()
            ]);

            if ($appointment->user_id !== auth()->id()) {
                \Log::warning('Unauthorized cancellation attempt');
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ($appointment->status === 'cancelled') {
                \Log::info('Appointment already cancelled');
                return response()->json(['error' => 'Appointment is already cancelled'], 400);
            }

            $appointment->status = 'cancelled';
            $appointment->cancellation_reason = $request->reason;
            $appointment->save();

            \Log::info('Appointment cancelled successfully');

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error cancelling appointment: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to cancel appointment',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reschedule(Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status !== 'pending') {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Only pending appointments can be rescheduled');
        }

        return view('appointments.reschedule', compact('appointment'));
    }

    public function updateSchedule(Request $request, Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->status !== 'pending') {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Only pending appointments can be rescheduled');
        }

        $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|date_format:H:i',
            'reschedule_reason' => 'required|string|max:500'
        ]);

        try {
            $newDateTime = Carbon::parse($request->new_date . ' ' . $request->new_time);
            
            $appointment->update([
                'appointment_date' => $newDateTime,
                'reschedule_reason' => $request->reschedule_reason,
                'last_reschedule_at' => now()
            ]);

            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Appointment rescheduled successfully');
        } catch (\Exception $e) {
            \Log::error('Error rescheduling appointment: ' . $e->getMessage());
            return back()->with('error', 'Failed to reschedule appointment. Please try again.');
        }
    }
}
