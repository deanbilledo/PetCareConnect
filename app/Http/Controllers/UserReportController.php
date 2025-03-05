<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'report_type' => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the user report
            $report = UserReport::create([
                'reporter_id' => Auth::id(), // Current logged in user (shop owner/employee)
                'user_id' => $request->user_id, // User being reported
                'report_type' => $request->report_type,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
