<?php

namespace App\Http\Controllers;

use App\Models\ShopReport;
use App\Models\UserReport;
use App\Models\Appeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppealController extends Controller
{
    /**
     * Constructor to ensure user is authenticated
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the appeal form for a shop report
     */
    public function showShopAppealForm(ShopReport $report)
    {
        // Ensure the user viewing this form is the shop owner
        $shop = $report->shop;
        if (!$shop || $shop->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to appeal this report.');
        }

        // Check if an appeal was already submitted
        $existingAppeal = $report->appeal;
        if ($existingAppeal) {
            return view('appeals.already-submitted', compact('report', 'existingAppeal'));
        }

        return view('appeals.shop-form', compact('report'));
    }

    /**
     * Show the appeal form for a user report
     */
    public function showUserAppealForm(UserReport $report)
    {
        // Ensure the user viewing this form is the reported user
        if ($report->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to appeal this report.');
        }

        // Check if an appeal was already submitted
        $existingAppeal = $report->appeal;
        if ($existingAppeal) {
            return view('appeals.already-submitted', compact('report', 'existingAppeal'));
        }

        return view('appeals.user-form', compact('report'));
    }

    /**
     * Submit an appeal for a shop report
     */
    public function submitShopAppeal(Request $request, ShopReport $report)
    {
        // Validate user is the shop owner
        $shop = $report->shop;
        if (!$shop || $shop->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:20|max:1000',
            'evidence' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if an appeal was already submitted
        if ($report->appeal) {
            return redirect()->back()->with('error', 'You have already submitted an appeal for this report.');
        }

        // Handle evidence upload if provided
        $evidencePath = null;
        if ($request->hasFile('evidence') && $request->file('evidence')->isValid()) {
            $file = $request->file('evidence');
            $evidencePath = $file->store('appeal-evidence', 'public');
        }

        // Create the appeal
        $appeal = new Appeal([
            'reason' => $request->input('reason'),
            'evidence_path' => $evidencePath,
            'status' => 'pending'
        ]);

        // Associate with the report
        $report->appeal()->save($appeal);

        // Notify the admin team
        $this->notifyAdminOfAppeal($report, 'shop');

        // Notify the shop owner
        $this->notifyUserOfAppealSubmission($report, 'shop');

        return redirect()->route('notifications.index')->with('success', 'Your appeal has been submitted successfully and is under review.');
    }

    /**
     * Submit an appeal for a user report
     */
    public function submitUserAppeal(Request $request, UserReport $report)
    {
        // Validate user is the reported user
        if ($report->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:20|max:1000',
            'evidence' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if an appeal was already submitted
        if ($report->appeal) {
            return redirect()->back()->with('error', 'You have already submitted an appeal for this report.');
        }

        // Handle evidence upload if provided
        $evidencePath = null;
        if ($request->hasFile('evidence') && $request->file('evidence')->isValid()) {
            $file = $request->file('evidence');
            $evidencePath = $file->store('appeal-evidence', 'public');
        }

        // Create the appeal
        $appeal = new Appeal([
            'reason' => $request->input('reason'),
            'evidence_path' => $evidencePath,
            'status' => 'pending'
        ]);

        // Associate with the report
        $report->appeal()->save($appeal);

        // Notify the admin team
        $this->notifyAdminOfAppeal($report, 'user');

        // Notify the user
        $this->notifyUserOfAppealSubmission($report, 'user');

        return redirect()->route('notifications.index')->with('success', 'Your appeal has been submitted successfully and is under review.');
    }

    /**
     * Notify admins of a new appeal
     */
    private function notifyAdminOfAppeal($report, $type)
    {
        // Get all admin users
        $admins = \App\Models\User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            if ($type === 'shop') {
                $reportedEntity = $report->shop->name;
                $reportType = 'shop';
            } else {
                $reportedEntity = $report->reportedUser->name;
                $reportType = 'user';
            }
            
            $admin->notifications()->create([
                'type' => 'appeal_submitted',
                'title' => 'New Appeal Submitted',
                'message' => "A new appeal has been submitted for a {$reportType} report against {$reportedEntity}. Please review it as soon as possible.",
                'status' => 'unread',
                'action_text' => 'Review Appeal',
                'action_url' => $type === 'shop' 
                    ? route('admin.appeals.shop.show', ['report' => $report->id])
                    : route('admin.appeals.user.show', ['report' => $report->id])
            ]);
        }
    }

    /**
     * Notify the user that their appeal was submitted
     */
    private function notifyUserOfAppealSubmission($report, $type)
    {
        $user = $type === 'shop' ? $report->shop->user : $report->reportedUser;
        
        if ($user) {
            $user->notifications()->create([
                'type' => 'appeal_submitted',
                'title' => 'Appeal Submitted',
                'message' => 'Your appeal has been submitted successfully and is under review by our administrative team. You will be notified once a decision has been made.',
                'status' => 'unread',
            ]);
        }
    }
}
