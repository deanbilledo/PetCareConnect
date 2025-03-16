<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubscriptionController extends Controller
{
    public function index()
    {
        $shop = Auth::user()->shop;
        $subscription = Subscription::where('shop_id', $shop->id)
            ->latest()
            ->first();

        if (!$subscription) {
            // Get the default subscription amount
            $defaultAmount = 299.00;
            
            // Create initial trial subscription
            $subscription = Subscription::create([
                'shop_id' => $shop->id,
                'status' => 'trial',
                'trial_ends_at' => Carbon::now()->addDays(30),
                'amount' => $defaultAmount
            ]);
        }

        $daysLeft = $subscription->status === 'trial' 
            ? Carbon::now()->diffInDays($subscription->trial_ends_at, false)
            : Carbon::now()->diffInDays($subscription->subscription_ends_at, false);

        // Check for payment verification notifications using the custom notification system
        $paymentVerified = false;
        $user = Auth::user();
        $paymentNotification = $user->unreadNotifications()
            ->where('type', 'payment_verified')
            ->first();
            
        if ($paymentNotification) {
            $paymentVerified = true;
            // Mark the notification as read
            $paymentNotification->update([
                'status' => 'read',
                'read_at' => now()
            ]);
        }

        return view('shop.subscriptions.index', compact('subscription', 'daysLeft', 'paymentVerified'));
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|string',
            'payment_screenshot' => 'required|image|max:10240' // 10MB max
        ]);

        $shop = Auth::user()->shop;
        $subscription = Subscription::where('shop_id', $shop->id)->latest()->first();

        if ($request->hasFile('payment_screenshot')) {
            $path = $request->file('payment_screenshot')->store('payment-screenshots', 'public');
        }

        $subscription->update([
            'reference_number' => $request->reference_number,
            'payment_screenshot' => $path ?? null,
            'payment_status' => 'pending'
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Payment verification submitted successfully. Please wait for admin approval.'
            ]);
        }

        return redirect()->route('shop.subscriptions.index')->with('success', 'Payment verification submitted successfully. Please wait for admin approval.');
    }

    /**
     * Cancel the current subscription for the authenticated user's shop
     */
    public function cancel()
    {
        // Get the authenticated user's shop
        $shop = auth()->user()->shop;
        
        // Get the latest subscription
        $subscription = $shop->subscriptions()->latest()->first();
        
        if ($subscription) {
            // Update the subscription status to cancelled
            $subscription->update([
                'status' => 'cancelled',
                'subscription_ends_at' => now()
            ]);
            
            // Create the subscription cancelled notification with complete details
            auth()->user()->notify(new \App\Notifications\SubscriptionCancelled($subscription, $shop));
            
            // Notify admin users via email
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notifyWithEmail([
                    'type' => 'shop_subscription_cancelled',
                    'title' => 'Shop Subscription Cancelled',
                    'message' => $shop->name . ' has cancelled their subscription. Subscription #' . $subscription->id . ' with an amount of ₱' . number_format($subscription->amount, 2) . '.',
                    'action_url' => route('admin.payments'),
                    'action_text' => 'View Payments',
                    'read_at' => null
                ]);
            }
        }
        
        return redirect()->route('shop.subscriptions.index')->with('success', 'Subscription cancelled successfully');
    }
} 