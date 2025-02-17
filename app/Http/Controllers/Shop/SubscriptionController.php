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
            // Create initial trial subscription
            $subscription = Subscription::create([
                'shop_id' => $shop->id,
                'status' => 'trial',
                'trial_ends_at' => Carbon::now()->addDays(30),
                'amount' => 299.00
            ]);
        }

        $daysLeft = $subscription->status === 'trial' 
            ? Carbon::now()->diffInDays($subscription->trial_ends_at, false)
            : Carbon::now()->diffInDays($subscription->subscription_ends_at, false);

        return view('shop.subscriptions.index', compact('subscription', 'daysLeft'));
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

        return response()->json([
            'message' => 'Payment verification submitted successfully. Please wait for admin approval.'
        ]);
    }

    public function cancel(Request $request)
    {
        $shop = Auth::user()->shop;
        $subscription = Subscription::where('shop_id', $shop->id)->latest()->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'subscription_ends_at' => Carbon::now()
            ]);
        }

        return redirect()->back()->with('success', 'Subscription cancelled successfully.');
    }
} 