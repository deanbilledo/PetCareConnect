<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $pets = $user->pets;
        $recentTransactions = [
            [
                'service' => 'DELUXE FUR CARE',
                'date' => '10/5/24'
            ],
            [
                'service' => 'DELUXE FUR CARE',
                'date' => '10/5/24'
            ]
        ];
        
        $recentVisits = [
            [
                'name' => 'Paws and Claws',
                'rating' => 5.0,
                'address' => 'Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur',
                'image' => 'images/shops/shop1.png'
            ]
        ];

        return view('profile.index', compact('user', 'pets', 'recentTransactions', 'recentVisits'));
    }

    public function updatePersonalInfo(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Personal information updated successfully');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $user = auth()->user();
            \Log::info('Updating profile photo for user: ' . $user->id);

            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo if exists
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    \Log::info('Deleting old profile photo: ' . $user->profile_photo_path);
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Store new profile photo
                $file = $request->file('profile_photo');
                $path = $file->store('profile-photos', 'public');
                \Log::info('New profile photo stored at: ' . $path);
                
                // Verify file exists
                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('Failed to store profile photo');
                }

                // Update user with new photo path
                $user->update([
                    'profile_photo_path' => $path
                ]);

                // Log the full URL that should be used
                \Log::info('Full URL should be: ' . Storage::disk('public')->url($path));
                \Log::info('Asset URL would be: ' . asset('storage/' . $path));

                return back()->with('success', 'Profile photo updated successfully');
            }

            return back()->with('error', 'No photo uploaded');
        } catch (\Exception $e) {
            \Log::error('Error updating profile photo: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Failed to update profile photo: ' . $e->getMessage());
        }
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $user->update([
            'address' => $request->address,
        ]);

        return back()->with('success', 'Location updated successfully');
    }

    public function storePet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'weight' => 'required|string|max:255',
            'height' => 'required|string|max:255',
        ]);

        auth()->user()->pets()->create($request->all());

        return back()->with('success', 'Pet added successfully');
    }

    public function updatePet(Request $request, Pet $pet)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'weight' => 'required|string|max:255',
            'height' => 'required|string|max:255',
        ]);

        $pet->update($request->all());

        return back()->with('success', 'Pet updated successfully');
    }

    public function deletePet(Pet $pet)
    {
        $pet->delete();
        return back()->with('success', 'Pet deleted successfully');
    }
} 