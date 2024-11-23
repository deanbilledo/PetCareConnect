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
        
        // Add this debug code temporarily
        \Log::info('Profile photo path: ' . $user->profile_photo_path);
        \Log::info('Full URL: ' . asset('storage/' . $user->profile_photo_path));
        
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

            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo if exists
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Store new profile photo with timestamp to prevent caching issues
                $fileName = time() . '_' . $request->file('profile_photo')->getClientOriginalName();
                $path = $request->file('profile_photo')->storeAs('profile-photos', $fileName, 'public');
                
                // Update user with new photo path
                $user->update([
                    'profile_photo_path' => $path
                ]);

                return back()->with('success', 'Profile photo updated successfully');
            }

            return back()->with('error', 'No photo uploaded');
        } catch (\Exception $e) {
            \Log::error('Error updating profile photo: ' . $e->getMessage());
            return back()->with('error', 'Failed to update profile photo');
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

    public function updatePetPhoto(Request $request, Pet $pet)
    {
        $request->validate([
            'pet_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            if ($request->hasFile('pet_photo')) {
                // Delete old photo if exists
                if ($pet->profile_photo_path && Storage::disk('public')->exists($pet->profile_photo_path)) {
                    Storage::disk('public')->delete($pet->profile_photo_path);
                }

                // Store new photo
                $path = $request->file('pet_photo')->store('pet-photos', 'public');
                
                // Update pet with new photo path
                $pet->update([
                    'profile_photo_path' => $path
                ]);

                return back()->with('success', 'Pet photo updated successfully');
            }

            return back()->with('error', 'No photo uploaded');
        } catch (\Exception $e) {
            \Log::error('Error updating pet photo: ' . $e->getMessage());
            return back()->with('error', 'Failed to update pet photo');
        }
    }

    public function showPetDetails(Pet $pet)
    {
        // Ensure the user can only view their own pets
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('profile.pets.details', compact('pet'));
    }

    public function storeVaccination(Request $request, Pet $pet)
    {
        // Ensure the user owns the pet
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'veterinarian' => 'required|string|max:255',
            'date' => 'required|date',
            'next_due_date' => 'required|date|after:date'
        ]);

        $pet->vaccinations()->create([
            'vaccine_name' => $request->vaccine_name,
            'veterinarian' => $request->veterinarian,
            'date' => $request->date,
            'next_due_date' => $request->next_due_date
        ]);

        return redirect()
            ->route('profile.pets.details', $pet)
            ->with('success', 'Vaccination record added successfully');
    }

    public function showAddHealthRecord(Pet $pet)
    {
        // Ensure the user can only access their own pets
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('profile.pets.add-health-record', compact('pet'));
    }
}