<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserReportController extends Controller
{
    /**
     * Store a newly created user report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Log the request for debugging
        Log::info('User report submission received', [
            'user_id' => $request->user_id,
            'report_type' => $request->report_type,
            'has_image' => $request->hasFile('evidence_image'),
            'request_ajax' => $request->ajax(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept')
        ]);
    
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'report_type' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'evidence_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Add validation for image
        ]);

        if ($validator->fails()) {
            Log::warning('User report validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle image upload if provided
            $imagePath = null;
            if ($request->hasFile('evidence_image') && $request->file('evidence_image')->isValid()) {
                $image = $request->file('evidence_image');
                $imagePath = $image->store('report-evidence', 'public');
                Log::info('Image uploaded successfully', ['path' => $imagePath]);
            }

            // Find the user being reported
            $reportedUser = User::find($request->user_id);
            if (!$reportedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'The user you are trying to report does not exist',
                ], 404);
            }

            // Create the user report
            $report = UserReport::create([
                'reporter_id' => Auth::id(), // Current logged in user (shop owner/employee)
                'user_id' => $request->user_id, // User being reported
                'report_type' => $request->report_type,
                'description' => $request->description,
                'status' => 'pending',
                'image_path' => $imagePath, // Save the image path
            ]);

            // Check if the report was created successfully
            if (!$report) {
                throw new \Exception('Failed to create the report');
            }

            Log::info('User report created successfully', ['report_id' => $report->id]);

            // Send notification to the reported user
            try {
                $reportType = ucwords(str_replace('_', ' ', $request->report_type));
                
                // Verify notification relationship exists and create the notification
                if (method_exists($reportedUser, 'notifications')) {
                    // Check if the route exists
                    $actionUrl = route('notifications.index'); // Fallback to notifications index
                    if (\Route::has('user.report.appeal.form')) {
                        $actionUrl = route('user.report.appeal.form', ['report' => $report->id]);
                    }
                    
                    $reportedUser->notifications()->create([
                        'type' => 'user_reported',
                        'title' => 'You have been reported',
                        'message' => "You have been reported for '{$reportType}'. Our administrative team will review this report and take appropriate action if necessary. You can submit an appeal if you believe this report is incorrect.",
                        'status' => 'unread',
                        'action_text' => 'Submit Appeal',
                        'action_url' => $actionUrl
                    ]);
                    
                    Log::info('Notification created for user', ['user_id' => $reportedUser->id]);
                } else {
                    Log::warning('Cannot create notification for user '.$reportedUser->id.': notifications relationship not defined');
                }
            } catch (\Exception $notificationException) {
                // Log notification error but don't fail the whole request
                Log::error('Failed to create notification: ' . $notificationException->getMessage(), [
                    'exception' => $notificationException,
                    'user_id' => $reportedUser->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            // Delete uploaded image if report creation fails
            if (isset($imagePath) && $imagePath) {
                Storage::disk('public')->delete($imagePath);
                Log::info('Deleted uploaded image after error', ['path' => $imagePath]);
            }
            
            Log::error('Error submitting report: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
