<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        Log::info('User ID: ' . $user->id);
        Log::info('Appointments count: ' . $appointments->count());
        Log::info('Raw appointments:', $appointments->toArray());

        $groupedAppointments = $appointments->groupBy(function($appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        });

        return view('appointments.index', compact('groupedAppointments'));
    }

    public function show(Appointment $appointment)
    {
        // Check if the user owns this appointment
        if ($appointment->user_id !== auth()->id()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You are not authorized to view this appointment.');
        }

        return view('appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment, Request $request)
    {
        try {
            Log::info('Cancel request received', [
                'appointment_id' => $appointment->id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'request_data' => $request->all()
            ]);

            if ($appointment->user_id !== auth()->id()) {
                Log::warning('Unauthorized cancellation attempt');
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ($appointment->status === 'cancelled') {
                Log::info('Appointment already cancelled');
                return response()->json(['error' => 'Appointment is already cancelled'], 400);
            }

            $appointment->status = 'cancelled';
            $appointment->cancellation_reason = $request->reason;
            $appointment->save();

            Log::info('Appointment cancelled successfully');

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage(), [
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

        // Load shop with operating hours and services
        $shop = $appointment->shop->load(['operatingHours', 'services']);
        
        // Get available time slots based on operating hours
        $timeSlots = [];
        $operatingHours = $shop->operatingHours->keyBy('day');
        
        // Get services for the shop
        $services = $shop->services->where('status', 'active');

        return view('appointments.reschedule', compact('appointment', 'operatingHours', 'services'));
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
            'new_time' => 'required',
            'service_type' => 'required|string',
            'reschedule_reason' => 'required|string|max:500'
        ]);

        try {
            // Convert 12-hour format to 24-hour format
            $time = date('H:i', strtotime($request->new_time));
            $newDateTime = Carbon::parse($request->new_date . ' ' . $time);
            
            // Get the service details
            $service = $appointment->shop->services()
                ->where('name', $request->service_type)
                ->where('status', 'active')
                ->firstOrFail();
            
            $appointment->update([
                'appointment_date' => $newDateTime,
                'service_type' => $service->name,
                'service_price' => $service->price,
                'reschedule_reason' => $request->reschedule_reason,
                'last_reschedule_at' => now()
            ]);

            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Appointment rescheduled successfully');
        } catch (\Exception $e) {
            Log::error('Error rescheduling appointment: ' . $e->getMessage());
            return back()->with('error', 'Failed to reschedule appointment. Please try again.');
        }
    }

    public function accept(Appointment $appointment)
    {
        try {
            Log::info('Accept request received', [
                'appointment_id' => $appointment->id,
                'shop_id' => auth()->user()->shop->id
            ]);

            // Verify the shop owner owns this appointment
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                Log::warning('Unauthorized accept attempt', [
                    'appointment_id' => $appointment->id,
                    'shop_id' => auth()->user()->shop->id
                ]);
                return response()->json([
                    'error' => 'Unauthorized to accept this appointment'
                ], 403);
            }

            // Check if appointment is pending
            if ($appointment->status !== 'pending') {
                Log::info('Invalid status for acceptance', [
                    'appointment_id' => $appointment->id,
                    'current_status' => $appointment->status
                ]);
                return response()->json([
                    'error' => 'Can only accept pending appointments'
                ], 400);
            }

            // Update appointment status
            $appointment->update([
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            Log::info('Appointment accepted successfully', [
                'appointment_id' => $appointment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment accepted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting appointment: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to accept appointment',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsPaid(Appointment $appointment)
    {
        try {
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'error' => 'Unauthorized to update this appointment'
                ], 403);
            }

            $appointment->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment marked as paid'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking appointment as paid: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update appointment status'
            ], 500);
        }
    }

    public function shopCancel(Appointment $appointment, Request $request)
    {
        try {
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'error' => 'Unauthorized to cancel this appointment'
                ], 403);
            }

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->reason,
                'cancelled_by' => 'shop'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to cancel appointment'
            ], 500);
        }
    }

    public function downloadReceipt(Appointment $appointment)
    {
        try {
            // Check if user is authorized (either shop owner or appointment owner)
            if ($appointment->user_id !== auth()->id() && 
                (!auth()->user()->shop || $appointment->shop_id !== auth()->user()->shop->id)) {
                abort(403, 'Unauthorized to download this receipt');
            }

            // Check if appointment is either accepted or completed
            if (!in_array($appointment->status, ['accepted', 'completed'])) {
                abort(400, 'Receipt is only available for accepted or completed appointments');
            }

            // Load the appointment with its relationships
            $appointment->load(['user', 'shop', 'pet']);

            // Generate receipt view with appropriate template
            $pdf = \PDF::loadView('pdfs.official-receipt', [
                'appointment' => $appointment
            ]);

            // Generate filename
            $filename = 'official_receipt_' . $appointment->id . '_' . Str::slug($appointment->shop->name) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error downloading receipt: ' . $e->getMessage());
            return back()->with('error', 'Failed to download receipt. Please try again.');
        }
    }
}
