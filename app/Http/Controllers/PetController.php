<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetVaccination;
use App\Models\PetParasiteControl;
use App\Models\PetHealthIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'type' => 'required|string|max:255',
                'species' => 'required_if:type,Exotic|nullable|string|max:255',
                'breed' => 'required|string|max:255',
                'size_category' => 'required|string|in:Small,Medium,Large',
                'weight' => 'required|numeric|min:0.1',
                'color_markings' => 'required|string|max:255',
                'coat_type' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before_or_equal:today',
            ]);

            $pet = new Pet($validated);
            $pet->user_id = auth()->id();
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

            return redirect()
                ->route('profile.pets.details', $pet->id)
                ->with('success', 'Vaccination record added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add vaccination record: ' . $e->getMessage());
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

            return redirect()
                ->route('profile.pets.details', $pet->id)
                ->with('success', 'Parasite control record added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add parasite control record: ' . $e->getMessage());
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

            return redirect()
                ->route('profile.pets.details', $pet->id)
                ->with('success', 'Health issue record added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add health issue record: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['health' => 'Failed to add health issue record. Please try again.']);
        }
    }

    public function markDeceased(Request $request, Pet $pet)
    {
        try {
            $validated = $request->validate([
                'death_date' => 'required|date|before_or_equal:today',
                'death_reason' => 'nullable|string|max:500',
            ]);

            $pet->markAsDeceased($validated['death_date'], $validated['death_reason']);

            return redirect()->back()->with('success', 'Pet status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update pet status: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update pet status. Please try again.']);
        }
    }
} 