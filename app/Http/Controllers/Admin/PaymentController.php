<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Shop;
use Carbon\Carbon;

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
        
        // Notify the shop owner
        $shopOwner->notifications()->create([
            'type' => 'payment_verified',
            'title' => 'Payment Verified',
            'message' => 'Your payment of ₱' . number_format($subscription->amount, 2) . ' has been verified. Your subscription is now active until ' . $endDate->format('F j, Y') . '.',
            'action_url' => route('shop.subscriptions.index'),
            'action_text' => 'View Subscription',
            'icon' => 'check-circle'
        ]);

        return redirect()->route('admin.payments')->with('success', 'Payment has been verified successfully.');
    }

    /**
     * Reject a payment for a subscription
     */
    public function rejectPayment(Request $request, Subscription $subscription)
    {
        $subscription->update([
            'payment_status' => 'rejected'
        ]);

        // Get the shop owner
        $shopOwner = $subscription->shop->user;
        
        // Notify the shop owner
        $shopOwner->notifications()->create([
            'type' => 'payment_rejected',
            'title' => 'Payment Rejected',
            'message' => 'Your payment of ₱' . number_format($subscription->amount, 2) . ' has been rejected. Please submit a new payment.',
            'action_url' => route('shop.subscriptions.index'),
            'action_text' => 'Resubmit Payment',
            'icon' => 'x-circle'
        ]);

        return redirect()->route('admin.payments')->with('success', 'Payment has been rejected successfully.');
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