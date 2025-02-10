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
            ->with(['shop', 'pet', 'employee'])
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
        // Check if the appointment belongs to the authenticated user
        if ($appointment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the appointment can be rescheduled (pending or accepted)
        if (!in_array($appointment->status, ['pending', 'accepted'])) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'This appointment cannot be rescheduled.');
        }

        // Get available services for the shop
        $services = $appointment->shop->services()
            ->where('status', 'active')
            ->get();

        // Get shop's operating hours
        $operatingHours = $appointment->shop->operatingHours()
            ->get()
            ->keyBy('day_of_week')
            ->toArray();

        return view('appointments.reschedule', compact('appointment', 'services', 'operatingHours'));
    }

    public function updateSchedule(Request $request, Appointment $appointment)
    {
        // Check if the appointment belongs to the authenticated user
        if ($appointment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the appointment can be rescheduled (pending or accepted)
        if (!in_array($appointment->status, ['pending', 'accepted'])) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'This appointment cannot be rescheduled.');
        }

        // Validate the request
        $validated = $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required',
            'employee_id' => 'required|exists:employees,id',
            'reschedule_reason' => 'required|string|min:10'
        ]);

        try {
            // Combine date and time
            $newDateTime = \Carbon\Carbon::parse($validated['new_date'] . ' ' . $validated['new_time']);

            // Update appointment with reschedule request
            $appointment->update([
                'status' => 'reschedule_requested',
                'requested_date' => $newDateTime,
                'employee_id' => $validated['employee_id'],
                'reschedule_reason' => $validated['reschedule_reason'],
                'last_reschedule_at' => now()
            ]);

            // Log the reschedule request
            \Log::info('Reschedule request submitted:', [
                'appointment_id' => $appointment->id,
                'new_datetime' => $newDateTime,
                'employee_id' => $validated['employee_id'],
                'reason' => $validated['reschedule_reason']
            ]);

            // Redirect to waiting confirmation page
            return view('appointments.reschedule-confirmation', [
                'appointment' => $appointment->fresh(),
                'newDateTime' => $newDateTime->format('F j, Y g:i A')
            ])->with('success', 'Your reschedule request has been submitted successfully. Please wait for the shop\'s confirmation.');
        } catch (\Exception $e) {
            \Log::error('Reschedule error: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to submit reschedule request. Please try again.');
        }
    }

    public function approveReschedule(Appointment $appointment)
    {
        try {
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'error' => 'Unauthorized to approve this reschedule request'
                ], 403);
            }

            if ($appointment->status !== 'reschedule_requested') {
                return response()->json([
                    'error' => 'This appointment is not pending reschedule'
                ], 400);
            }

            $appointment->update([
                'status' => 'accepted',
                'appointment_date' => $appointment->requested_date,
                'service_type' => $appointment->requested_service,
                'reschedule_approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request approved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving reschedule request: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to approve reschedule request'
            ], 500);
        }
    }

    public function declineReschedule(Appointment $appointment, Request $request)
    {
        try {
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'error' => 'Unauthorized to decline this reschedule request'
                ], 403);
            }

            if ($appointment->status !== 'reschedule_requested') {
                return response()->json([
                    'error' => 'This appointment is not pending reschedule'
                ], 400);
            }

            $appointment->update([
                'status' => 'accepted', // Revert to previous status
                'reschedule_rejection_reason' => $request->reason,
                'reschedule_rejected_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request declined successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error declining reschedule request: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to decline reschedule request'
            ], 500);
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

    public function addNote(Request $request, Appointment $appointment)
    {
        try {
            // Check if user is authorized (must be shop owner)
            if (!auth()->user()->shop || $appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized to add notes to this appointment'
                ], 403);
            }

            // Validate the request
            $validated = $request->validate([
                'note' => 'required|string|max:1000',
                'image' => 'nullable|image|max:5120' // Max 5MB image
            ]);

            $data = ['notes' => $validated['note']];

            // Handle image upload if present
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('appointment-notes', 'public');
                $data['note_image'] = $imagePath;
            }

            // Update the appointment with the note and image
            $appointment->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'image_url' => $request->hasFile('image') ? asset('storage/' . $imagePath) : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding note to appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to add note'
            ], 500);
        }
    }

    public function getNote(Appointment $appointment)
    {
        try {
            // Check if user is authorized (must be shop owner)
            if (!auth()->user()->shop || $appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized to view notes for this appointment'
                ], 403);
            }

            // Load the appointment with necessary relationships
            $appointment->load(['user', 'pet', 'shop', 'employee']);

            // Get the profile photo URL
            $profilePhotoUrl = $appointment->user->profile_photo_url ?? asset('images/default-profile.png');

            return response()->json([
                'success' => true,
                'note' => $appointment->notes,
                'image_url' => $appointment->note_image ? asset('storage/' . $appointment->note_image) : null,
                'appointment' => [
                    'id' => $appointment->id,
                    'user' => [
                        'name' => $appointment->user->name,
                        'email' => $appointment->user->email,
                        'profile_photo_url' => $profilePhotoUrl
                    ],
                    'employee' => $appointment->employee ? [
                        'id' => $appointment->employee->id,
                        'name' => $appointment->employee->name,
                        'position' => $appointment->employee->position,
                        'profile_photo_url' => $appointment->employee->profile_photo_url ?? asset('images/default-avatar.png')
                    ] : null,
                    'appointment_date' => $appointment->appointment_date,
                    'service_type' => $appointment->service_type,
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                    'note_image' => $appointment->note_image ? asset('storage/' . $appointment->note_image) : null,
                    'updated_at' => $appointment->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving note for appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve note'
            ], 500);
        }
    }
}
