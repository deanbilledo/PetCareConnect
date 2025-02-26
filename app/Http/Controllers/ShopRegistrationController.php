<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ShopRegistrationController extends Controller
{
    public function __construct()
    {
        // Remove any middleware from constructor
        // We'll handle middleware in the routes file instead
    }

    public function showPreRegistration()
    {
        Log::info('Accessing pre-registration page', [
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'has_shop' => auth()->check() ? auth()->user()->shop : null
        ]);

        if (auth()->check() && auth()->user()->shop) {
            Log::info('User already has a shop, redirecting to home');
            return redirect()->route('home')
                           ->with('error', 'You already have a registered shop.');
        }

        Log::info('Showing pre-registration page');
        return view('shopRegistration.pre-register');
    }

    public function showRegistrationForm()
    {
        if (!auth()->check()) {
            return redirect()->route('shop.pre.register')
                           ->with('message', 'Please login or create an account first.');
        }

        if (auth()->user()->shop) {
            return redirect()->route('home')
                           ->with('error', 'You already have a registered shop.');
        }

        return view('shopRegistration.register');
    }

    public function handlePreRegistration(Request $request)
    {
        if (auth()->check() && auth()->user()->shop) {
            return redirect()->route('home')
                           ->with('error', 'You already have a registered shop.');
        }

        if (!auth()->check()) {
            return redirect()->route('login')
                           ->with('message', 'Please login or create an account to register your shop.');
        }

        return redirect()->route('shop.register.form');
    }

    public function register(Request $request)
    {
        Log::info('Session ID: ' . session()->getId());
        Log::info('Auth check: ' . (auth()->check() ? 'true' : 'false'));
        Log::info('User ID: ' . (auth()->id() ?? 'null'));
        
        if (!auth()->check()) {
            Log::error('User not authenticated');
            return redirect()->route('login')
                            ->with('error', 'Please login to register your shop.');
        }

        Log::info('Starting shop registration process');
        
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255|unique:shops,name',
            'shop_type' => 'required|in:veterinary,grooming',
            'phone' => 'required|string|max:20',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'shop_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'tin' => 'required|string|max:255',
            'vat_status' => 'required|in:registered,non_registered',
            'bir_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'terms' => 'required|accepted'
        ]);

        try {
            DB::beginTransaction();

            // Store the files
            $imagePath = $request->file('shop_image')->store('shop-images', 'public');
            $birCertificatePath = $request->file('bir_certificate')->store('bir-certificates', 'public');

            Log::info('Files stored successfully', [
                'image' => $imagePath,
                'certificate' => $birCertificatePath
            ]);

            // Create the shop with status set to 'pending'
            $shop = Shop::create([
                'user_id' => auth()->id(),
                'name' => $request->shop_name,
                'type' => $request->shop_type,
                'phone' => $request->phone,
                'description' => $request->description ?? '',
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'image' => $imagePath,
                'tin' => $request->tin,
                'vat_status' => $request->vat_status,
                'bir_certificate' => $birCertificatePath,
                'rating' => 0.0,
                'terms_accepted' => true,
                'status' => 'pending'
            ]);

            // Create notification for successful shop registration
            $user = auth()->user();
            $user->notify(
                'system',
                'Shop Registration Submitted',
                "Your shop '{$request->shop_name}' has been successfully registered and is pending approval. We'll notify you once it's reviewed.",
                route('shop.registration.pending'),
                'View Status'
            );

            Log::info('Shop created successfully', $shop->toArray());

            DB::commit();

            return redirect()->route('shop.registration.pending');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Shop registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up stored files if they exist
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            if (isset($birCertificatePath) && Storage::disk('public')->exists($birCertificatePath)) {
                Storage::disk('public')->delete($birCertificatePath);
            }

            return back()->withInput()
                        ->withErrors(['error' => 'An error occurred during registration. Please try again. ' . $e->getMessage()]);
        }
    }

    public function showPendingApproval()
    {
        if (!auth()->user()->shop) {
            return redirect()->route('home');
        }

        return view('shopRegistration.pending-approval');
    }

    // Add new methods for handling shop approval/rejection notifications
    public function handleApproval(Shop $shop)
    {
        try {
            DB::beginTransaction();

            // Update shop status to approved
            $shop->update(['status' => 'active']);

            // Create notification for shop approval with trial information
            $shop->user->notify(
                'system',
                'Shop Registration Approved - Free Trial Started',
                "Congratulations! Your shop '{$shop->name}' has been approved. Your 30-day free trial has been activated. " .
                "After the trial period, you'll need to subscribe to continue using our platform. " .
                "Click below to view your subscription details and payment options.",
                route('shop.subscriptions'),
                'View Subscription Details'
            );

            // Create a second notification for shop setup
            $shop->user->notify(
                'system',
                'Set Up Your Shop Profile',
                "Start setting up your shop profile to make the most of your trial period. " .
                "Add your services, operating hours, and other important details.",
                route('shop.setup.welcome'),
                'Start Shop Setup'
            );

            DB::commit();
            return redirect()->back()->with('success', 'Shop has been approved and owner notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approving shop: ' . $e->getMessage(), [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to approve shop. Please try again.');
        }
    }

    public function handleRejection(Shop $shop, string $reason = null)
    {
        try {
            DB::beginTransaction();

            // Update shop status to rejected
            $shop->update(['status' => 'rejected']);

            // Create notification for shop rejection
            $shop->user->notify(
                'system',
                'Shop Registration Rejected',
                "We regret to inform you that your shop '{$shop->name}' registration has been rejected." . 
                ($reason ? " Reason: {$reason}" : " Please contact support for more information."),
                route('home'),
                'Return to Home'
            );

            DB::commit();
            return redirect()->back()->with('success', 'Shop has been rejected and owner notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in rejecting shop: ' . $e->getMessage(), [
                'shop_id' => $shop->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to reject shop. Please try again.');
        }
    }
} 