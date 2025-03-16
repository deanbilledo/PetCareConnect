<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Shop;
use App\Notifications\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function index()
    {
        // Get all subscriptions with their shops
        $subscriptions = Subscription::with('shop')
            ->latest()
            ->get()
            ->groupBy('shop_id')
            ->map(function ($shopSubscriptions) {
                return $shopSubscriptions->first(); // Get the latest subscription for each shop
            });

        // Get shops in trial period
        $trialShops = Shop::whereHas('subscriptions', function ($query) {
            $query->where('status', 'trial')
                ->where('trial_ends_at', '>', Carbon::now());
        })->with(['subscriptions' => function ($query) {
            $query->latest()->limit(1);
        }])->get();

        $monthlyRate = 299.00; // Default monthly rate

        return view('admin.payments', compact('subscriptions', 'trialShops', 'monthlyRate'));
    }

    /**
     * Verify a payment for a subscription
     */
    public function verifyPayment(Request $request, Subscription $subscription)
    {
        try {
            // Calculate the end date (30 days from now)
            $endDate = now()->addDays(30);
            
            $subscription->update([
                'payment_status' => 'verified',
                'status' => 'active',
                'subscription_starts_at' => now(),
                'subscription_ends_at' => $endDate
            ]);
    
            // Get the shop owner
            $shopOwner = $subscription->shop->user;
            
            // Send email notification using Laravel's notification system
            $shopOwner->notify(new PaymentStatus($subscription, $subscription->shop, 'verified'));
            
            // Create database notification using the custom notifyWithEmail method
            $notificationData = (new PaymentStatus($subscription, $subscription->shop, 'verified'))->toNotifyWithEmail();
            $shopOwner->notifyWithEmail($notificationData);
    
            // Notify admin users
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                if ($admin->id !== auth()->id()) { // Don't notify the admin who performed the action
                    $admin->notifications()->create([
                        'type' => 'shop_payment_verified',
                        'title' => 'Shop Payment Verified',
                        'message' => auth()->user()->name . ' verified payment for shop ' . $subscription->shop->name . '. Payment amount: ₱' . number_format($subscription->amount, 2) . '.',
                        'action_url' => route('admin.payments'),
                        'action_text' => 'View Payments',
                        'icon' => 'check-circle',
                        'status' => 'unread',
                        'read_at' => null
                    ]);
                }
            }
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Payment has been verified successfully.', 'status' => 'success']);
            }
    
            return redirect()->route('admin.payments')->with('success', 'Payment has been verified successfully.');
        } catch (\Exception $e) {
            \Log::error('Error verifying payment: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'exception' => $e->getMessage()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Error verifying payment: ' . $e->getMessage(), 'status' => 'error'], 500);
            }
            
            return redirect()->route('admin.payments')->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }

    /**
     * Reject a payment for a subscription
     */
    public function rejectPayment(Request $request, Subscription $subscription)
    {
        try {
            $subscription->update([
                'payment_status' => 'rejected'
            ]);
    
            // Get the shop owner
            $shopOwner = $subscription->shop->user;
            
            // Send email notification using Laravel's notification system
            $shopOwner->notify(new PaymentStatus($subscription, $subscription->shop, 'rejected'));
            
            // Create database notification using the custom notifyWithEmail method
            $notificationData = (new PaymentStatus($subscription, $subscription->shop, 'rejected'))->toNotifyWithEmail();
            $shopOwner->notifyWithEmail($notificationData);
            
            // Notify admin users
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                if ($admin->id !== auth()->id()) { // Don't notify the admin who performed the action
                    $admin->notifications()->create([
                        'type' => 'shop_payment_rejected',
                        'title' => 'Shop Payment Rejected',
                        'message' => auth()->user()->name . ' rejected payment for shop ' . $subscription->shop->name . '. Payment amount: ₱' . number_format($subscription->amount, 2) . '.',
                        'action_url' => route('admin.payments'),
                        'action_text' => 'View Payments',
                        'icon' => 'x-circle',
                        'status' => 'unread',
                        'read_at' => null
                    ]);
                }
            }
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Payment has been rejected successfully.', 'status' => 'success']);
            }
    
            return redirect()->route('admin.payments')->with('success', 'Payment has been rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting payment: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'exception' => $e->getMessage()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Error rejecting payment: ' . $e->getMessage(), 'status' => 'error'], 500);
            }
            
            return redirect()->route('admin.payments')->with('error', 'Error rejecting payment: ' . $e->getMessage());
        }
    }

    public function updateSubscriptionRate(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0'
        ]);

        // Update all subscriptions with new rate
        Subscription::where('status', 'active')
            ->orWhere('status', 'trial')
            ->update(['amount' => $request->rate]);

        return response()->json(['message' => 'Subscription rate updated successfully']);
    }

    public function getPaymentDetails(Subscription $subscription)
    {
        $subscription->load('shop');
        
        return response()->json([
            'shop_name' => $subscription->shop->name,
            'reference_number' => $subscription->reference_number,
            'amount' => $subscription->amount,
            'payment_status' => $subscription->payment_status,
            'subscription_status' => $subscription->status,
            'payment_screenshot' => $subscription->payment_screenshot ? asset('storage/' . $subscription->payment_screenshot) : null,
            'created_at' => $subscription->created_at->format('M d, Y H:i:s'),
            'subscription_starts_at' => $subscription->subscription_starts_at ? $subscription->subscription_starts_at->format('M d, Y') : null,
            'subscription_ends_at' => $subscription->subscription_ends_at ? $subscription->subscription_ends_at->format('M d, Y') : null,
        ]);
    }
} 