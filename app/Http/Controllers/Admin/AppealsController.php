<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appeal;
use App\Models\ShopReport;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppealsController extends Controller
{
    /**
     * Show the details of a shop report appeal.
     */
    public function showShopAppeal(ShopReport $report)
    {
        $appeal = $report->appeal;
        
        if (!$appeal) {
            return redirect()->route('admin.support')->with('error', 'No appeal found for this report.');
        }
        
        return view('admin.appeals.shop-show', compact('report', 'appeal'));
    }
    
    /**
     * Show the details of a user report appeal.
     */
    public function showUserAppeal(UserReport $report)
    {
        $appeal = $report->appeal;
        
        if (!$appeal) {
            return redirect()->route('admin.support')->with('error', 'No appeal found for this report.');
        }
        
        return view('admin.appeals.user-show', compact('report', 'appeal'));
    }
    
    /**
     * Update the status of an appeal.
     */
    public function updateAppealStatus(Request $request, Appeal $appeal)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'required|string|min:10|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Update the appeal
        $appeal->status = $request->status;
        $appeal->admin_notes = $request->admin_notes;
        $appeal->resolved_at = now();
        $appeal->save();
        
        // Get the appropriate user to notify
        $user = null;
        $report = $appeal->appealable;
        $reportType = null;
        
        if ($report instanceof ShopReport) {
            $user = $report->shop->user;
            $reportType = 'shop';
        } elseif ($report instanceof UserReport) {
            $user = $report->reportedUser;
            $reportType = 'user';
        }
        
        // Notify the user of the appeal status
        if ($user) {
            $title = $appeal->status === 'approved' 
                ? 'Your Appeal Has Been Approved' 
                : 'Your Appeal Has Been Rejected';
                
            $message = $appeal->status === 'approved'
                ? "Your appeal against the {$reportType} report has been approved. The report has been dismissed."
                : "Your appeal against the {$reportType} report has been rejected. The report will remain active.";
                
            if ($appeal->admin_notes) {
                $message .= "\n\nAdmin Notes: " . $appeal->admin_notes;
            }
            
            $user->notifications()->create([
                'type' => 'appeal_' . $appeal->status,
                'title' => $title,
                'message' => $message,
                'status' => 'unread',
                'action_text' => 'View Details',
                'action_url' => route('notifications.index')
            ]);
            
            // If appeal is approved, dismiss the report
            if ($appeal->status === 'approved') {
                $report->status = 'dismissed';
                $report->resolved_at = now();
                $report->admin_notes = 'Report dismissed due to successful appeal.';
                $report->save();
            }
        }
        
        return redirect()->route('admin.support')
            ->with('success', 'Appeal status updated successfully to ' . ucfirst($appeal->status));
    }
    
    /**
     * Get appeal details for the modal display via AJAX.
     */
    public function getAppealDetails(Request $request, $id)
    {
        $appeal = Appeal::findOrFail($id);
        
        // Get the report based on the appealable type
        $report = $appeal->appealable;
        
        if (!$report) {
            return response()->json(['error' => 'No report found for this appeal.'], 404);
        }
        
        // Determine the type of report
        $type = $report instanceof ShopReport ? 'shop' : 'user';
        
        // Format the data based on report type
        if ($type === 'shop') {
            // Get shop report details with related data
            $reportData = [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'description' => $report->description,
                'reason' => $report->description, // Using description as reason
                'status' => $report->status,
                'created_at' => $report->created_at,
                'reporter_name' => $report->user ? $report->user->name : 'Unknown',
                'shop' => $report->shop ? [
                    'id' => $report->shop->id,
                    'name' => $report->shop->name,
                    'email' => $report->shop->email,
                    'phone' => $report->shop->phone,
                    'owner' => [
                        'name' => $report->shop->user ? $report->shop->user->name : 'Unknown'
                    ]
                ] : null
            ];
        } else {
            // Get user report details with related data
            $reportData = [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'description' => $report->description,
                'reason' => $report->description, // Using description as reason
                'status' => $report->status,
                'created_at' => $report->created_at,
                'reporter_name' => $report->reporter ? $report->reporter->name : 'Unknown',
                'reported_user' => $report->reportedUser ? [
                    'id' => $report->reportedUser->id,
                    'name' => $report->reportedUser->name,
                    'email' => $report->reportedUser->email,
                    'created_at' => $report->reportedUser->created_at
                ] : null
            ];
        }
        
        // Format the appeal data
        $appealData = [
            'id' => $appeal->id,
            'reason' => $appeal->reason,
            'status' => $appeal->status,
            'created_at' => $appeal->created_at,
            'resolved_at' => $appeal->resolved_at,
            'admin_notes' => $appeal->admin_notes,
            'evidence_path' => $appeal->evidence_path,
            'has_evidence' => !is_null($appeal->evidence_path),
            'evidence_url' => $appeal->evidence_path ? asset('storage/' . $appeal->evidence_path) : null
        ];
        
        return response()->json([
            'appeal' => $appealData,
            'report' => $reportData,
            'type' => $type
        ]);
    }
}
