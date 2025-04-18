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
        // Get appointments
        $appointments = auth()->user()->appointments()
            ->with(['shop', 'pet', 'employee'])
            ->orderBy('appointment_date', 'asc')
            ->get();

        // Auto-cancel past due appointments
        $appointments->each(function ($appointment) {
            $isPastDue = \Carbon\Carbon::parse($appointment->appointment_date)->addDay()->isPast();
            if ($isPastDue && $appointment->status === 'pending') {
                $appointment->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Automatically cancelled due to being past due',
                    'cancelled_at' => now()
                ]);
            }
        });

        // Group appointments by date
        $groupedAppointments = $appointments->groupBy(function ($appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        });

        return view('appointments.index', compact('groupedAppointments'));
    }

    public function show(Appointment $appointment)
    {
        // Check if the user owns this appointment
        if ($appointment->user_id !== auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->route('appointments.index')
                ->with('error', 'You are not authorized to view this appointment.');
        }

        // Load the relationships
        $appointment->load(['shop', 'pet', 'employee']);

        // Return JSON response for AJAX requests
        if (request()->wantsJson()) {
            return response()->json($appointment);
        }

        // Return view for regular requests
        return view('appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment, Request $request)
    {
        try {
            Log::info('Cancel request received', [
                'appointment_id' => $appointment->id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'is_last_minute' => $request->boolean('is_last_minute'),
                'auto_approved' => $request->boolean('auto_approved'),
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

            // Check if this is a last-minute cancellation that needs approval
            if ($request->boolean('is_last_minute') && !$request->boolean('auto_approved')) {
                // Set status to cancellation_requested instead of cancelled
                $appointment->status = 'cancellation_requested';
                $appointment->cancellation_reason = $request->reason;
                $appointment->cancellation_requested_at = now();
                $appointment->save();
                
                // Notify shop owner about cancellation request
                $appointment->shop->user->notifyWithEmail([
                    'type' => 'cancellation_requested',
                    'title' => 'Cancellation Request',
                    'message' => "A customer has requested to cancel their appointment scheduled for {$appointment->appointment_date->format('F j, Y g:i A')}. Reason: {$request->reason}",
                    'action_url' => route('shop.appointments.show', $appointment),
                    'action_text' => 'View Request',
                    'status' => 'unread',
                    'icon' => 'customer_cancelled_appointment'
                ]);
                
                Log::info('Cancellation request submitted successfully');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Cancellation request submitted successfully'
                ]);
            }
            
            // Regular cancellation
            $appointment->status = 'cancelled';
            $appointment->cancellation_reason = $request->reason;
            $appointment->cancelled_at = now();
            $appointment->save();

            // Create notification for cancelled appointment
            $appointment->user->notifyWithEmail([
                'type' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled',
                'message' => "Your appointment at {$appointment->shop->name} for {$appointment->appointment_date->format('F j, Y g:i A')} has been cancelled.",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'customer_cancelled_appointment'
            ]);
            
            // Also notify the shop owner about the cancellation
            $appointment->shop->user->notifyWithEmail([
                'type' => 'customer_cancelled_appointment',
                'title' => 'Appointment Cancelled by Customer',
                'message' => "The appointment for {$appointment->user->name} scheduled for {$appointment->appointment_date->format('F j, Y g:i A')} has been cancelled by the customer. Reason: " . ($request->reason ?: 'No reason provided'),
                'action_url' => route('shop.appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'customer_cancelled_appointment'
            ]);

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

    /**
     * Approve a cancellation request for an appointment
     * 
     * @param Appointment $appointment
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveCancellation(Appointment $appointment)
    {
        try {
            // Check if the user is authorized (must be shop owner)
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'error' => 'Unauthorized to approve this cancellation request'
                ], 403);
            }

            // Check if the appointment is in the cancellation_requested status
            if ($appointment->status !== 'cancellation_requested') {
                return response()->json([
                    'error' => 'This appointment is not pending cancellation'
                ], 400);
            }

            // Update the appointment status to cancelled
            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => 'shop',
                'cancellation_approved_at' => now()
            ]);

            // Create notification for the customer
            $appointment->user->notifyWithEmail([
                'type' => 'cancellation_approved',
                'title' => 'Cancellation Request Approved',
                'message' => "Your cancellation request for the appointment at {$appointment->shop->name} scheduled for {$appointment->appointment_date->format('F j, Y g:i A')} has been approved.",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'appointment_reschedule'
            ]);

            Log::info('Cancellation request approved', [
                'appointment_id' => $appointment->id,
                'approved_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cancellation request approved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving cancellation request: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to approve cancellation request',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decline a cancellation request for an appointment
     * 
     * @param Appointment $appointment
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function declineCancellation(Appointment $appointment, Request $request)
    {
        try {
            // Check if the user is authorized (must be shop owner)
            if ($appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'error' => 'Unauthorized to decline this cancellation request'
                ], 403);
            }

            // Check if the appointment is in the cancellation_requested status
            if ($appointment->status !== 'cancellation_requested') {
                return response()->json([
                    'error' => 'This appointment is not pending cancellation'
                ], 400);
            }

            // Update the appointment status back to accepted
            $appointment->update([
                'status' => 'accepted', // Revert to previous accepted status
                'cancellation_rejected_at' => now(),
                'cancellation_rejection_reason' => $request->reason
            ]);

            // Create notification for the customer
            $appointment->user->notifyWithEmail([
                'type' => 'cancellation_declined',
                'title' => 'Cancellation Request Declined',
                'message' => "Your cancellation request for the appointment at {$appointment->shop->name} scheduled for {$appointment->appointment_date->format('F j, Y g:i A')} has been declined. Reason: {$request->reason}",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'appointment'
            ]);

            Log::info('Cancellation request declined', [
                'appointment_id' => $appointment->id,
                'declined_by' => auth()->id(),
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cancellation request declined successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error declining cancellation request: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to decline cancellation request',
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
            DB::beginTransaction();
            
            $appointment->status = 'reschedule_requested';
            $appointment->requested_date = $newDateTime;
            $appointment->employee_id = $validated['employee_id'];
            $appointment->reschedule_reason = $validated['reschedule_reason'];
            $appointment->last_reschedule_at = now();
            $appointment->save();
            
            DB::commit();

            // Log the reschedule request
            \Log::info('Reschedule request submitted:', [
                'appointment_id' => $appointment->id,
                'new_datetime' => $newDateTime,
                'employee_id' => $validated['employee_id'],
                'reason' => $validated['reschedule_reason']
            ]);

            // Return view with the updated appointment and new datetime
            return view('appointments.reschedule-confirmation', [
                'appointment' => $appointment->fresh(),
                'newDateTime' => $newDateTime->format('F j, Y g:i A')
            ])->with('success', 'Your reschedule request has been submitted successfully. Please wait for the shop\'s confirmation.');

        } catch (\Exception $e) {
            DB::rollBack();
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
            if (!auth()->user()->shop || $appointment->shop_id !== auth()->user()->shop->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized to approve this reschedule request'
                ], 403);
            }

            if ($appointment->status !== 'reschedule_requested') {
                return response()->json([
                    'success' => false,
                    'error' => 'This appointment is not pending reschedule'
                ], 400);
            }

            $appointment->update([
                'status' => 'accepted',
                'appointment_date' => $appointment->requested_date,
                'service_type' => $appointment->requested_service ?: $appointment->service_type,
                'reschedule_approved_at' => now()
            ]);

            // Create notification for approved reschedule
            $appointment->user->notifyWithEmail([
                'type' => 'reschedule_approved',
                'title' => 'Reschedule Request Approved',
                'message' => "Your reschedule request for {$appointment->shop->name} has been approved. Your new appointment is on {$appointment->appointment_date->format('F j, Y g:i A')}.",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Appointment',
                'status' => 'unread',
                'icon' => 'appointment_reschedule'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request approved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving reschedule request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to approve reschedule request: ' . $e->getMessage()
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

            // Notify the customer
            $appointment->user->notifyWithEmail([
                'type' => 'reschedule_declined',
                'title' => 'Reschedule Request Declined',
                'message' => "Your reschedule request for the appointment at {$appointment->shop->name} scheduled for {$appointment->appointment_date->format('F j, Y g:i A')} has been declined. Reason: {$request->reason}",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'appointment'
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

            // Create notification for accepted appointment
            $appointment->user->notifyWithEmail([
                'type' => 'appointment_accepted',
                'title' => 'Appointment Accepted',
                'message' => "Your appointment at {$appointment->shop->name} for {$appointment->appointment_date->format('F j, Y g:i A')} has been accepted.",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Appointment',
                'status' => 'unread',
                'icon' => 'appointment'
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

            // Create notification for completed appointment
            $appointment->user->notifyWithEmail([
                'type' => 'appointment_completed',
                'title' => 'Appointment Completed',
                'message' => "Your appointment at {$appointment->shop->name} has been completed. Thank you for your business!",
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'appointment'
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
                'cancelled_by' => 'shop',
                'cancelled_at' => now()
            ]);

            // Notify the customer about the cancellation
            $appointment->user->notifyWithEmail([
                'type' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled by Shop',
                'message' => "Your appointment at {$appointment->shop->name} for {$appointment->appointment_date->format('F j, Y g:i A')} has been cancelled by the shop. Reason: " . ($request->reason ?: 'No reason provided'),
                'action_url' => route('appointments.show', $appointment),
                'action_text' => 'View Details',
                'status' => 'unread',
                'icon' => 'customer_cancelled_appointment'
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
                'note_image' => 'nullable|image|max:5120' // Max 5MB image
            ]);

            $data = ['note' => $validated['note']];

            // Handle image upload if present
            if ($request->hasFile('note_image')) {
                $image = $request->file('note_image');
                $imagePath = $image->store('appointment-notes', 'public');
                $data['image'] = $imagePath;
            }

            // Create a new note
            $note = $appointment->appointmentNotes()->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'note' => $note,
                'image_url' => $request->hasFile('note_image') ? asset('storage/' . $imagePath) : null
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
            $appointment->load(['user', 'pet', 'shop', 'employee', 'appointmentNotes']);

            // Get the profile photo URL
            $profilePhotoUrl = $appointment->user->profile_photo_url ?? asset('images/default-profile.png');

            return response()->json([
                'success' => true,
                'notes' => $appointment->appointmentNotes,
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
                    'updated_at' => $appointment->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving notes for appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve notes'
            ], 500);
        }
    }

    // Add method to check for upcoming appointments
    public function checkUpcomingAppointments()
    {
        try {
            $upcomingAppointments = Appointment::where('status', 'accepted')
                ->where('appointment_date', '>', now())
                ->where('appointment_date', '<=', now()->addHour())
                ->where('reminder_sent', false)
                ->get();

            foreach ($upcomingAppointments as $appointment) {
                // Create notification for upcoming appointment
                $appointment->user->notifyWithEmail([
                    'type' => 'appointment_reminder',
                    'title' => 'Upcoming Appointment Reminder',
                    'message' => "Your appointment at {$appointment->shop->name} is in less than an hour! Please be ready for your {$appointment->service_type} appointment at {$appointment->appointment_date->format('g:i A')}.",
                    'action_url' => route('appointments.show', $appointment),
                    'action_text' => 'View Appointment',
                    'status' => 'unread',
                    'icon' => 'appointment'
                ]);

                // Mark reminder as sent
                $appointment->update(['reminder_sent' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Upcoming appointments checked successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking upcoming appointments: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to check upcoming appointments'
            ], 500);
        }
    }
}
