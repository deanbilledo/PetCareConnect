<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Rating;
use App\Models\StaffRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the rating form for the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        // Check if the appointment is completed and not yet rated
        if ($appointment->status !== 'completed' || $appointment->has_rating) {
            return redirect()->route('appointments.index')
                ->with('error', 'This appointment cannot be rated.');
        }

        // Check if the user owns this appointment
        if ($appointment->user_id !== auth()->id()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You are not authorized to rate this appointment.');
        }

        return view('appointments.rate', compact('appointment'));
    }

    /**
     * Store a new rating for the appointment.
     */
    public function store(Request $request, Appointment $appointment)
    {
        // Validate the request
        $validated = $request->validate([
            'shop_rating' => 'required|integer|min:1|max:5',
            'staff_rating' => 'required|integer|min:1|max:5',
            'shop_review' => 'nullable|string|max:1000',
            'staff_review' => 'nullable|string|max:1000',
        ]);

        // Check if the appointment is completed and not yet rated
        if ($appointment->status !== 'completed' || $appointment->has_rating) {
            return redirect()->route('appointments.index')
                ->with('error', 'This appointment cannot be rated.');
        }

        // Check if the user owns this appointment
        if ($appointment->user_id !== auth()->id()) {
            return redirect()->route('appointments.index')
                ->with('error', 'You are not authorized to rate this appointment.');
        }

        try {
            DB::transaction(function () use ($appointment, $validated) {
                // Update or create shop rating
                Rating::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'shop_id' => $appointment->shop_id,
                        'appointment_id' => $appointment->id,
                    ],
                    [
                        'rating' => $validated['shop_rating'],
                        'review' => $validated['shop_review'],
                    ]
                );

                // Update or create employee rating
                StaffRating::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'employee_id' => $appointment->employee_id,
                        'appointment_id' => $appointment->id,
                    ],
                    [
                        'rating' => $validated['staff_rating'],
                        'review' => $validated['staff_review'],
                    ]
                );

                // Update appointment has_rating status
                $appointment->update(['has_rating' => true]);

                // Update average ratings
                $this->updateAverageRatings($appointment->shop_id, $appointment->employee_id);
            });

            return redirect()->route('appointments.index')
                ->with('success', 'Thank you for your rating and review!');
        } catch (\Exception $e) {
            Log::error('Rating submission error:', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while submitting your rating. Please try again.')
                ->withInput();
        }
    }

    private function updateAverageRatings($shop_id, $employee_id)
    {
        // Update shop average rating
        $shopAvgRating = Rating::where('shop_id', $shop_id)->avg('rating');
        DB::table('shops')->where('id', $shop_id)->update(['rating' => $shopAvgRating]);

        // Update employee average rating
        $employeeAvgRating = StaffRating::where('employee_id', $employee_id)->avg('rating');
        DB::table('employees')->where('id', $employee_id)->update(['rating' => $employeeAvgRating]);
    }
} 