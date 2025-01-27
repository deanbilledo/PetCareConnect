<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
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
} 