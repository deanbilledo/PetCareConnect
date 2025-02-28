<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetVaccination;
use App\Models\PetParasiteControl;
use App\Models\PetHealthIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pets = $user->pets()->with(['vaccinations', 'parasiteControls', 'healthIssues'])->get();

        // Calculate health metrics for each pet
        foreach ($pets as $pet) {
            // Calculate vaccination percentage
            $totalVaccines = $pet->vaccinations->count();
            $upToDateVaccines = $pet->vaccinations->filter(function ($vaccination) {
                return $vaccination->next_due_date->isFuture();
            })->count();
            $pet->vaccination_percentage = $totalVaccines > 0 
                ? round(($upToDateVaccines / $totalVaccines) * 100) 
                : 0;

            // Calculate health score
            $healthFactors = [
                'vaccinations' => $pet->vaccination_percentage,
                'parasiteControl' => $pet->parasiteControls()
                    ->where('next_treatment_date', '>', now())
                    ->exists() ? 100 : 0,
                'recentCheckup' => $pet->appointments()
                    ->where('service_type', 'veterinary')
                    ->where('appointment_date', '>', now()->subMonths(6))
                    ->exists() ? 100 : 0,
            ];
            $pet->health_score = round(array_sum($healthFactors) / count($healthFactors));
        }

        return view('profile.pets.index', compact('pets'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'species' => 'required_if:type,Exotic',
                'breed' => 'required|string|max:255',
                'size_category' => 'required|string|in:Small,Medium,Large',
                'weight' => 'required|numeric|min:0.1',
                'color_markings' => 'required|string|max:255',
                'coat_type' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before_or_equal:today',
            ]);

            $pet = new Pet($validated);
            $pet->user_id = auth()->id();
            $pet->species = $validated['species'] ?? null;
            $pet->save();

            return redirect()->back()->with('success', 'Pet successfully registered!');
        } catch (\Exception $e) {
            Log::error('Pet registration failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to register pet. Please try again.']);
        }
    }

    public function update(Request $request, Pet $pet)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'species' => 'required_if:type,Exotic|nullable|string|max:255',
                'breed' => 'required|string|max:255',
                'size_category' => 'required|string|in:Small,Medium,Large',
                'weight' => 'required|numeric|min:0.1',
                'color_markings' => 'required|string|max:255',
                'coat_type' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before_or_equal:today',
            ]);

            $pet->update($validated);

            return redirect()->back()->with('success', 'Pet information updated successfully!');
        } catch (\Exception $e) {
            Log::error('Pet update failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update pet information. Please try again.']);
        }
    }

    public function storeVaccination(Request $request, Pet $pet)
    {
        try {
            $validated = $request->validate([
                'vaccine_name' => 'required|string|max:255',
                'administered_date' => 'required|date|before_or_equal:today',
                'administered_by' => 'required|string|max:255',
                'next_due_date' => 'required|date|after:administered_date',
            ]);

            $pet->vaccinations()->create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vaccination record added successfully!'
                ]);
            }

            return redirect()
                ->route('profile.pets.details', $pet->id)
                ->with('success', 'Vaccination record added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add vaccination record: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add vaccination record. Please try again.',
                    'errors' => ['vaccination' => 'Failed to add vaccination record. Please try again.']
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['vaccination' => 'Failed to add vaccination record. Please try again.']);
        }
    }

    public function storeParasiteControl(Request $request, Pet $pet)
    {
        try {
            $validated = $request->validate([
                'treatment_name' => 'required|string|max:255',
                'treatment_type' => 'required|string|in:Flea,Tick,Worm,Other',
                'treatment_date' => 'required|date|before_or_equal:today',
                'next_treatment_date' => 'required|date|after:treatment_date',
            ]);

            $pet->parasiteControls()->create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parasite control record added successfully!'
                ]);
            }

            return redirect()
                ->route('profile.pets.details', $pet->id)
                ->with('success', 'Parasite control record added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add parasite control record: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add parasite control record. Please try again.',
                    'errors' => ['parasite' => 'Failed to add parasite control record. Please try again.']
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['parasite' => 'Failed to add parasite control record. Please try again.']);
        }
    }

    public function storeHealthIssue(Request $request, Pet $pet)
    {
        try {
            $validated = $request->validate([
                'issue_title' => 'required|string|max:255',
                'identified_date' => 'required|date|before_or_equal:today',
                'description' => 'required|string',
                'treatment' => 'required|string|max:255',
                'vet_notes' => 'nullable|string',
            ]);

            $pet->healthIssues()->create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Health issue record added successfully!'
                ]);
            }

            return redirect()
                ->route('profile.pets.details', $pet->id)
                ->with('success', 'Health issue record added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add health issue record: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add health issue record. Please try again.',
                    'errors' => ['health' => 'Failed to add health issue record. Please try again.']
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['health' => 'Failed to add health issue record. Please try again.']);
        }
    }

    public function show(Pet $pet)
    {
        // Check if the authenticated user owns this pet
        if ($pet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load the necessary relationships
        $pet->load([
            'vaccinations' => function ($query) {
                $query->latest('administered_date');
            },
            'parasiteControls' => function ($query) {
                $query->latest('treatment_date');
            },
            'healthIssues' => function ($query) {
                $query->latest('identified_date');
            },
            'appointments' => function ($query) {
                $query->with('shop')
                      ->latest('appointment_date')
                      ->take(5);
            }
        ]);

        return view('profile.pets.show', compact('pet'));
    }

    public function updatePhoto(Request $request, Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'pet_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('pet_photo')) {
            // Delete old photo if exists
            if ($pet->profile_photo && Storage::exists($pet->profile_photo)) {
                Storage::delete($pet->profile_photo);
            }

            // Store new photo
            $path = $request->file('pet_photo')->store('pets', 'public');
            $pet->update(['profile_photo' => $path]);
        }

        return back()->with('success', 'Pet photo updated successfully');
    }

    public function markDeceased(Request $request, Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'death_date' => 'required|date|before_or_equal:today',
            'death_reason' => 'nullable|string|max:500'
        ]);

        $pet->update([
            'death_date' => $validated['death_date'],
            'death_reason' => $validated['death_reason']
        ]);

        return back()->with('success', 'Pet status updated successfully');
    }

    public function showHealthRecord(Pet $pet)
    {
        try {
            // Check if pet exists and belongs to authenticated user or if user is a shop owner
            $user = auth()->user();
            $isShopOwner = $user->shop && $user->shop->status === 'active';
            
            if (!$pet || (!$isShopOwner && $pet->user_id !== $user->id)) {
                Log::warning('Unauthorized access attempt to view pet health record', [
                    'pet_id' => $pet->id,
                    'user_id' => $user->id,
                    'is_shop_owner' => $isShopOwner,
                    'actual_owner' => $pet->user_id
                ]);
                return redirect()
                    ->route('profile.pets.index')
                    ->with('error', 'You are not authorized to view this pet\'s health records.');
            }

            // Load necessary relationships
            $pet->load([
                'vaccinations' => function ($query) {
                    $query->latest('administered_date');
                },
                'parasiteControls' => function ($query) {
                    $query->latest('treatment_date');
                },
                'healthIssues' => function ($query) {
                    $query->latest('identified_date');
                }
            ]);

            return view('profile.pets.health-record', [
                'pet' => $pet,
                'isShopOwner' => $isShopOwner
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing pet health record: ' . $e->getMessage(), [
                'pet_id' => $pet->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->route('profile.pets.index')
                ->with('error', 'Unable to view health record. Please try again.');
        }
    }

    public function createHealthRecord(Pet $pet)
    {
        try {
            // Check if pet exists and belongs to authenticated user or if user is a shop owner
            $user = auth()->user();
            $isShopOwner = $user->shop && $user->shop->status === 'active';
            
            if (!$pet || (!$isShopOwner && $pet->user_id !== $user->id)) {
                Log::warning('Unauthorized access attempt to pet health record', [
                    'pet_id' => $pet->id,
                    'user_id' => $user->id,
                    'is_shop_owner' => $isShopOwner,
                    'actual_owner' => $pet->user_id
                ]);
                return redirect()
                    ->route('profile.pets.index')
                    ->with('error', 'You are not authorized to access this pet\'s health records.');
            }

            // Load necessary relationships
            $pet->load(['vaccinations', 'parasiteControls', 'healthIssues']);

            return view('profile.pets.add-health-record', [
                'pet' => $pet,
                'isShopOwner' => $isShopOwner
            ]);
        } catch (\Exception $e) {
            Log::error('Error accessing pet health record: ' . $e->getMessage(), [
                'pet_id' => $pet->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->route('profile.pets.index')
                ->with('error', 'Unable to access health record. Please try again.');
        }
    }

    public function showUserAddHealthRecord(Pet $pet)
    {
        try {
            // Check if pet exists and belongs to authenticated user
            if (!$pet || $pet->user_id !== auth()->id()) {
                Log::warning('Unauthorized access attempt to pet health record', [
                    'pet_id' => $pet->id,
                    'user_id' => auth()->id(),
                    'actual_owner' => $pet->user_id
                ]);
                return redirect()
                    ->route('profile.pets.index')
                    ->with('error', 'You are not authorized to access this pet\'s health records.');
            }

            // Load necessary relationships
            $pet->load(['vaccinations', 'parasiteControls', 'healthIssues']);

            return view('profile.pets.user-add-health-record', [
                'pet' => $pet
            ]);
        } catch (\Exception $e) {
            Log::error('Error accessing pet health record: ' . $e->getMessage(), [
                'pet_id' => $pet->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->route('profile.pets.index')
                ->with('error', 'Unable to access health record. Please try again.');
        }
    }
} 