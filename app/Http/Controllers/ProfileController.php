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

        // Get completed appointments for Recent Transactions
        $recentTransactions = $user->appointments()
            ->where('status', 'completed')
            ->with('shop') // Eager load the shop relationship
            ->orderBy('appointment_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'service' => $appointment->service_type,
                    'shop_name' => $appointment->shop ? $appointment->shop->name : 'Unknown Shop',
                    'amount' => $appointment->service_price,
                    'date' => $appointment->appointment_date->format('m/d/y')
                ];
            });

        // Get all visited shops for Recent Visits
        $recentVisits = $user->appointments()
            ->where('status', 'completed')
            ->with(['shop' => function($query) {
                $query->withAvg('ratings', 'rating');
            }])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                if (!$appointment->shop) {
                    return null;
                }
                return [
                    'name' => $appointment->shop->name,
                    'rating' => number_format($appointment->shop->ratings_avg_rating ?? 0, 1),
                    'address' => $appointment->shop->address,
                    'image' => $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png'),
                    'last_visit' => $appointment->appointment_date->format('M d, Y'),
                    'shop_id' => $appointment->shop->id
                ];
            })
            ->filter() // Remove null values
            ->unique('shop_id')
            ->take(5)
            ->values();

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
        \Log::info('Pet creation attempt with data:', $request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'size_category' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'color_markings' => 'required|string|max:255',
            'coat_type' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ]);

        try {
            $pet = auth()->user()->pets()->create([
                'name' => $request->name,
                'type' => $request->type,
                'breed' => $request->breed,
                'size_category' => $request->size_category,
                'weight' => $request->weight,
                'color_markings' => $request->color_markings,
                'coat_type' => $request->coat_type,
                'date_of_birth' => $request->date_of_birth,
            ]);

            return back()->with('success', 'Pet added successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating pet: ' . $e->getMessage());
            return back()->with('error', 'Failed to add pet. Please try again.');
        }
    }

    public function updatePet(Request $request, Pet $pet)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'size_category' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'color_markings' => 'required|string|max:255',
            'coat_type' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ]);

        try {
            $pet->update([
                'name' => $request->name,
                'type' => $request->type,
                'breed' => $request->breed,
                'size_category' => $request->size_category,
                'weight' => $request->weight,
                'color_markings' => $request->color_markings,
                'coat_type' => $request->coat_type,
                'date_of_birth' => $request->date_of_birth,
            ]);

            return back()->with('success', 'Pet updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating pet: ' . $e->getMessage());
            return back()->with('error', 'Failed to update pet. Please try again.');
        }
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

    public function showHealthRecord(Pet $pet)
    {
        // Check if the user is authorized to view this pet's records
        if ($pet->user_id !== auth()->id() && !auth()->user()->hasRole('shop_owner')) {
            abort(403);
        }

        return view('pets.health-record', compact('pet'));
    }
}