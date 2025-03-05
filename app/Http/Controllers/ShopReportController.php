<?php

namespace App\Http\Controllers;

use App\Models\ShopReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the shop report
            $report = ShopReport::create([
                'user_id' => Auth::id(),
                'shop_id' => $request->shop_id,
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
