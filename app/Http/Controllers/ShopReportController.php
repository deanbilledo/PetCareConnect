<?php

namespace App\Http\Controllers;

use App\Models\ShopReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ShopReportController extends Controller
{
    /**
     * Store a newly created report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Log the request for debugging
        Log::info('Shop report submission received', [
            'shop_id' => $request->shop_id,
            'report_type' => $request->report_type,
            'has_image' => $request->hasFile('evidence_image'),
            'request_ajax' => $request->ajax(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'authenticated' => Auth::check()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('Unauthenticated user attempted to submit a shop report');
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit a report',
                'redirect' => route('login')
            ], 401);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'report_type' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'evidence_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Add validation for image
        ]);

        if ($validator->fails()) {
            Log::warning('Shop report validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get authenticated user ID
            $userId = Auth::id();
            Log::info('Authenticated user is submitting report', ['user_id' => $userId]);

            // Handle image upload if provided
            $imagePath = null;
            if ($request->hasFile('evidence_image') && $request->file('evidence_image')->isValid()) {
                $image = $request->file('evidence_image');
                Log::info('Processing image upload', [
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize()
                ]);
                
                try {
                    $imagePath = $image->store('report-evidence', 'public');
                    Log::info('Image uploaded successfully', ['path' => $imagePath]);
                } catch (\Exception $imageException) {
                    Log::error('Failed to store image: ' . $imageException->getMessage(), [
                        'exception' => $imageException
                    ]);
                    // Continue without image if upload fails
                }
            }

            // Create the shop report
            $report = ShopReport::create([
                'user_id' => $userId,
                'shop_id' => $request->shop_id,
                'report_type' => $request->report_type,
                'description' => $request->description,
                'status' => 'pending',
                'image_path' => $imagePath, // Save the image path
            ]);

            Log::info('Shop report created successfully', ['report_id' => $report->id]);

            // Send notification to the shop that they were reported - do this in a try-catch to prevent it from breaking the whole request
            try {
                // Don't throw an error if the shop doesn't exist or doesn't have a user
                $shop = \App\Models\Shop::find($request->shop_id);
                if ($shop && $shop->user) {
                    $reportType = ucwords(str_replace('_', ' ', $request->report_type));
                    
                    // Make sure the notifications relationship exists
                    if (method_exists($shop->user, 'notifications')) {
                        $shop->user->notifications()->create([
                            'type' => 'shop_reported',
                            'title' => 'Your shop has been reported',
                            'message' => "Your shop has been reported for '{$reportType}'. Our administrative team will review this report and take appropriate action if necessary. You can submit an appeal if you believe this report is incorrect.",
                            'status' => 'unread',
                            'action_text' => 'Submit Appeal',
                            'action_url' => route('shop.report.appeal.form', ['report' => $report->id])
                        ]);
                        
                        Log::info('Notification created for shop owner', ['shop_id' => $shop->id, 'user_id' => $shop->user->id]);
                    } else {
                        Log::warning('Cannot create notification: notifications relationship not defined for shop user');
                    }
                } else {
                    Log::warning('Cannot create notification: shop or shop user not found', [
                        'shop_id' => $request->shop_id,
                        'shop_exists' => isset($shop),
                        'user_exists' => isset($shop) && isset($shop->user)
                    ]);
                }
            } catch (\Exception $notificationException) {
                // Log notification error but don't fail the whole request
                Log::error('Failed to create notification: ' . $notificationException->getMessage(), [
                    'exception' => $notificationException,
                    'shop_id' => $request->shop_id
                ]);
                // Don't throw the exception - just log it and continue
            }

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            // Delete uploaded image if report creation fails
            if (isset($imagePath) && $imagePath) {
                try {
                    Storage::disk('public')->delete($imagePath);
                    Log::info('Deleted uploaded image after error', ['path' => $imagePath]);
                } catch (\Exception $deleteException) {
                    Log::error('Failed to delete image after error: ' . $deleteException->getMessage());
                }
            }
            
            Log::error('Error submitting shop report: ' . $e->getMessage(), [
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
