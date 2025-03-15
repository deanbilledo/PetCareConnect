<?php

namespace App\Http\Controllers;

use App\Models\ShopReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'report_type' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'evidence_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Add validation for image
        ]);

        if ($validator->fails()) {
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
            }

            // Create the shop report
            $report = ShopReport::create([
                'user_id' => Auth::id(),
                'shop_id' => $request->shop_id,
                'report_type' => $request->report_type,
                'description' => $request->description,
                'status' => 'pending',
                'image_path' => $imagePath, // Save the image path
            ]);

            // Send notification to the shop that they were reported
            $shop = \App\Models\Shop::find($request->shop_id);
            if ($shop && $shop->user) {
                $reportType = ucwords(str_replace('_', ' ', $request->report_type));
                
                $shop->user->notifications()->create([
                    'type' => 'shop_reported',
                    'title' => 'Your shop has been reported',
                    'message' => "Your shop has been reported for '{$reportType}'. Our administrative team will review this report and take appropriate action if necessary. You can submit an appeal if you believe this report is incorrect.",
                    'status' => 'unread',
                    'action_text' => 'Submit Appeal',
                    'action_url' => route('shop.report.appeal.form', ['report' => $report->id])
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
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
