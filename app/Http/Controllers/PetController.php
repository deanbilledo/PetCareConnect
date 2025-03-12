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

            // Track changes before updating
            $trackableFields = [
                'name', 'type', 'species', 'breed', 'size_category', 
                'weight', 'color_markings', 'coat_type', 'date_of_birth'
            ];

            foreach ($trackableFields as $field) {
                if (isset($validated[$field])) {
                    $oldValue = $pet->$field;
                    $newValue = $validated[$field];

                    // Handle date fields
                    if ($field === 'date_of_birth') {
                        $oldValue = $pet->date_of_birth ? $pet->date_of_birth->format('Y-m-d') : null;
                        $newValue = $validated['date_of_birth'];
                    }

                    // Only create history if values are actually different
                    if ($oldValue != $newValue) {
                        $pet->updateHistories()->create([
                            'user_id' => auth()->id(),
                            'field_name' => $field,
                            'old_value' => $oldValue,
                            'new_value' => $newValue
                        ]);
                    }
                }
            }

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
        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'administered_date' => 'required|date',
            'next_due_date' => 'required|date|after:administered_date',
            'administered_by' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $vaccination = $pet->vaccinations()->create($validated);

        // Check for due dates and create notifications
        $this->checkHealthRecordsDueDate($pet);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Vaccination record added successfully',
                'vaccination' => $vaccination
            ]);
        }

        return back()->with('success', 'Vaccination record added successfully');
    }

    public function storeParasiteControl(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'treatment_name' => 'required|string|max:255',
            'treatment_type' => 'required|string|max:255',
            'treatment_date' => 'required|date',
            'next_treatment_date' => 'required|date|after:treatment_date',
            'notes' => 'nullable|string'
        ]);

        $parasiteControl = $pet->parasiteControls()->create($validated);

        // Check for due dates and create notifications
        $this->checkHealthRecordsDueDate($pet);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Parasite control record added successfully',
                'parasiteControl' => $parasiteControl
            ]);
        }

        return back()->with('success', 'Parasite control record added successfully');
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

    public function updateHealthIssue(Request $request, Pet $pet, PetHealthIssue $issue)
    {
        try {
            // Check if the authenticated user owns this pet
            if ($pet->user_id !== auth()->id()) {
                return back()->with('error', 'Unauthorized action.');
            }

            // Check if the health issue belongs to the pet
            if ($issue->pet_id !== $pet->id) {
                return back()->with('error', 'Invalid health issue.');
            }

            // Toggle the resolution status
            $issue->is_resolved = !$issue->is_resolved;
            $issue->resolved_date = $issue->is_resolved ? now() : null;
            $issue->save();

            return back()->with('success', 
                $issue->is_resolved ? 
                'Health issue marked as resolved.' : 
                'Health issue marked as active.'
            );
        } catch (\Exception $e) {
            Log::error('Error updating health issue status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update health issue status. Please try again.');
        }
    }

    /**
     * Check and create notifications for upcoming and overdue health records
     */
    private function checkHealthRecordsDueDate(Pet $pet)
    {
        $now = now();
        $sevenDaysFromNow = $now->copy()->addDays(7);

        // Check vaccinations
        foreach ($pet->vaccinations as $vaccination) {
            if ($vaccination->next_due_date->between($now, $sevenDaysFromNow)) {
                // Create upcoming vaccination notification
                $pet->user->notifications()->create([
                    'type' => 'appointment',
                    'title' => 'Upcoming Vaccination Due',
                    'message' => "Vaccination '{$vaccination->vaccine_name}' for {$pet->name} is due on {$vaccination->next_due_date->format('M d, Y')}",
                    'action_url' => route('profile.pets.health-record', $pet),
                    'action_text' => 'View Health Record'
                ]);
            } elseif ($vaccination->next_due_date->isPast()) {
                // Create overdue vaccination notification
                $pet->user->notifications()->create([
                    'type' => 'appointment',
                    'title' => 'Overdue Vaccination',
                    'message' => "Vaccination '{$vaccination->vaccine_name}' for {$pet->name} was due on {$vaccination->next_due_date->format('M d, Y')}",
                    'action_url' => route('profile.pets.health-record', $pet),
                    'action_text' => 'View Health Record'
                ]);
            }
        }

        // Check parasite controls
        foreach ($pet->parasiteControls as $control) {
            if ($control->next_treatment_date->between($now, $sevenDaysFromNow)) {
                // Create upcoming parasite control notification
                $pet->user->notifications()->create([
                    'type' => 'appointment',
                    'title' => 'Upcoming Parasite Treatment Due',
                    'message' => "Parasite treatment '{$control->treatment_name}' for {$pet->name} is due on {$control->next_treatment_date->format('M d, Y')}",
                    'action_url' => route('profile.pets.health-record', $pet),
                    'action_text' => 'View Health Record'
                ]);
            } elseif ($control->next_treatment_date->isPast()) {
                // Create overdue parasite control notification
                $pet->user->notifications()->create([
                    'type' => 'appointment',
                    'title' => 'Overdue Parasite Treatment',
                    'message' => "Parasite treatment '{$control->treatment_name}' for {$pet->name} was due on {$control->next_treatment_date->format('M d, Y')}",
                    'action_url' => route('profile.pets.health-record', $pet),
                    'action_text' => 'View Health Record'
                ]);
            }
        }
    }

    /**
     * Check grooming status for a specific pet and create notification if needed
     */
    public function checkGroomingStatus(Pet $pet)
    {
        try {
            // Ensure the pet belongs to the authenticated user
            if ($pet->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to pet.'
                ], 403);
            }
            
            // Get grooming preference for this pet (default to 30 days if not set)
            $groomingInterval = $pet->grooming_interval ?? 30;
            $cutoffDate = now()->subDays($groomingInterval);
            
            // Debug flag
            $debug = [];
            
            // Approach 1: Look through services relationship
            $lastGroomingViaServices = $pet->appointments()
                ->where('status', 'completed')
                ->whereHas('services', function($query) {
                    $query->where('category', 'grooming')
                          ->orWhere('name', 'like', '%groom%');
                })
                ->latest('appointment_date')
                ->first();
                
            // Approach 2: Check for grooming in the service_type or name
            $lastGroomingViaType = $pet->appointments()
                ->where('status', 'completed')
                ->where(function($query) {
                    $query->where('service_type', 'like', '%groom%')
                          ->orWhere('service_type', 'like', '%Exotic%')
                          ->orWhere('notes', 'like', '%groom%');
                })
                ->latest('appointment_date')
                ->first();
                
            // Get the most recent of the two approaches
            $lastGrooming = null;
            if ($lastGroomingViaServices && $lastGroomingViaType) {
                $lastGrooming = $lastGroomingViaServices->appointment_date->gt($lastGroomingViaType->appointment_date) 
                    ? $lastGroomingViaServices 
                    : $lastGroomingViaType;
            } else {
                $lastGrooming = $lastGroomingViaServices ?? $lastGroomingViaType;
            }
            
            // Log debugging info
            \Log::info('Grooming status check for ' . $pet->name, [
                'found_via_services' => $lastGroomingViaServices ? true : false,
                'found_via_type' => $lastGroomingViaType ? true : false,
                'last_grooming_date' => $lastGrooming ? $lastGrooming->appointment_date->format('Y-m-d') : 'none'
            ]);
            
            $needsGrooming = !$lastGrooming || $lastGrooming->appointment_date->lt($cutoffDate);
            
            if ($needsGrooming) {
                // Determine timeframe message
                if ($lastGrooming) {
                    $daysSince = $lastGrooming->appointment_date->diffInDays(now());
                    $timeframe = $this->formatTimeframeSinceLastGrooming($daysSince);
                    $lastGroomingDate = $lastGrooming->appointment_date->format('M d, Y');
                    $message = "{$pet->name} hasn't been groomed in {$timeframe}. Last grooming was on {$lastGroomingDate}.";
                } else {
                    $message = "{$pet->name} has no record of grooming appointments. Regular grooming is important for your pet's health.";
                }
                
                // Create notification if not created in the last week
                $recentNotification = auth()->user()->notifications()
                    ->where('type', 'pet_care')
                    ->where('created_at', '>=', now()->subWeek())
                    ->where('message', 'like', "%{$pet->name}%grooming%")
                    ->exists();
                    
                if (!$recentNotification) {
                    auth()->user()->notifications()->create([
                        'type' => 'pet_care',
                        'title' => 'Grooming Reminder',
                        'message' => $message,
                        'action_url' => route('profile.pets.show', $pet),
                        'action_text' => 'View Pet Details',
                        'status' => 'unread'
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'needs_grooming' => true,
                    'message' => $message,
                    'last_grooming_date' => $lastGrooming ? $lastGrooming->appointment_date->format('Y-m-d') : null,
                    'days_since_last_grooming' => $lastGrooming ? $lastGrooming->appointment_date->diffInDays(now()) : null
                ]);
            }
            
            return response()->json([
                'success' => true,
                'needs_grooming' => false,
                'message' => "{$pet->name} is up to date with grooming.",
                'last_grooming_date' => $lastGrooming ? $lastGrooming->appointment_date->format('Y-m-d') : null,
                'days_since_last_grooming' => $lastGrooming ? $lastGrooming->appointment_date->diffInDays(now()) : null,
                'next_recommended_grooming' => $lastGrooming ? $lastGrooming->appointment_date->addDays($groomingInterval)->format('Y-m-d') : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking pet grooming status: ' . $e->getMessage(), [
                'pet_id' => $pet->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking grooming status.'
            ], 500);
        }
    }
    
    /**
     * Update grooming preference for a pet
     */
    public function updateGroomingPreference(Request $request, Pet $pet)
    {
        try {
            // Ensure the pet belongs to the authenticated user
            if ($pet->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to pet.'
                ], 403);
            }
            
            // Validate the interval
            $validated = $request->validate([
                'grooming_interval' => 'required|integer|min:7|max:180'
            ]);
            
            // Update the pet's grooming interval
            $pet->update([
                'grooming_interval' => $validated['grooming_interval']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Grooming reminder interval updated to {$validated['grooming_interval']} days."
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating pet grooming preferences: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating grooming preferences.'
            ], 500);
        }
    }
    
    /**
     * Format timeframe for grooming notifications
     */
    private function formatTimeframeSinceLastGrooming($days)
    {
        if ($days < 7) {
            return "{$days} days";
        } elseif ($days < 30) {
            $weeks = floor($days / 7);
            return $weeks == 1 ? "1 week" : "{$weeks} weeks";
        } elseif ($days < 365) {
            $months = floor($days / 30);
            return $months == 1 ? "1 month" : "{$months} months";
        } else {
            $years = floor($days / 365);
            $extraMonths = floor(($days % 365) / 30);
            
            if ($extraMonths == 0) {
                return $years == 1 ? "1 year" : "{$years} years";
            } else {
                $yearText = $years == 1 ? "1 year" : "{$years} years";
                $monthText = $extraMonths == 1 ? "1 month" : "{$extraMonths} months";
                return "{$yearText} and {$monthText}";
            }
        }
    }
} 