<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

                // Store new profile photo with timestamp
                $fileName = time() . '_' . $request->file('profile_photo')->getClientOriginalName();
                $path = $request->file('profile_photo')->storeAs('profile-photos', $fileName, 'public');
                
                $user->update([
                    'profile_photo_path' => $path
                ]);

                return back()->with('success', 'Profile photo updated successfully');
            }

            return back()->with('error', 'No photo uploaded');
        } catch (\Exception $e) {
            Log::error('Error updating profile photo: ' . $e->getMessage());
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Dog,Cat,Bird,Other',
            'breed' => 'required|string|max:255',
            'size_category' => 'required|string|in:Small,Medium,Large',
            'weight' => 'required|numeric|min:0.1|max:100',
            'color_markings' => 'required|string|max:255',
            'coat_type' => 'required|string|in:Short,Medium,Long,Curly,Double,Hairless',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ], [
            'weight.min' => 'Weight must be greater than 0 kg',
            'weight.max' => 'Please enter a valid weight (less than 100 kg)',
            'weight.numeric' => 'Weight must be a valid number',
        ]);

        try {
            $pet = auth()->user()->pets()->create($validated);
            return back()->with('success', 'Pet added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add pet. Please try again.');
        }
    }

    public function updatePet(Request $request, Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Dog,Cat,Bird,Other',
            'breed' => 'required|string|max:255',
            'size_category' => 'required|string|in:Small,Medium,Large',
            'weight' => 'required|numeric|min:0.1|max:100',
            'color_markings' => 'required|string|max:255',
            'coat_type' => 'required|string|in:Short,Medium,Long,Curly,Double,Hairless',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ], [
            'weight.min' => 'Weight must be greater than 0 kg',
            'weight.max' => 'Please enter a valid weight (less than 100 kg)',
            'weight.numeric' => 'Weight must be a valid number',
        ]);

        try {
            $pet->update($validated);
            return back()->with('success', 'Pet information updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update pet information. Please try again.');
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
            Log::error('Error updating pet photo: ' . $e->getMessage());
            return back()->with('error', 'Failed to update pet photo');
        }
    }

    public function showPetDetails(Pet $pet)
    {
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
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        return view('profile.pets.add-health-record', compact('pet'));
    }

    public function showHealthRecord(Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Load health records and vaccinations
        $pet->load(['healthRecords', 'vaccinations']);
        
        return view('profile.pets.health-record', compact('pet'));
    }

    public function storeHealthRecord(Request $request, Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'record_type' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $pet->healthRecords()->create($validated);

        return redirect()->route('profile.pets.health-record', $pet)
                        ->with('success', 'Health record added successfully');
    }

    public function storeParasiteControl(Request $request, Pet $pet)
    {
        // Validation and storage logic for parasite control
    }

    public function storeHealthIssue(Request $request, Pet $pet)
    {
        // Validation and storage logic for health issues
    }

    public function showPetDashboard()
    {
        $user = auth()->user();
        $pets = $user->pets()->with(['vaccinations', 'healthIssues', 'parasiteControls'])->get();
        
        // Calculate upcoming appointments
        $upcomingAppointments = $user->appointments()
            ->where('appointment_date', '>', now())
            ->where('status', '!=', 'cancelled')
            ->count();

        // Calculate due vaccinations
        $dueVaccinations = 0;
        foreach ($pets as $pet) {
            $dueVaccinations += $pet->vaccinations()
                ->where('next_due_date', '<=', now()->addDays(30))
                ->count();
        }

        // Calculate monthly expenses
        $monthlyExpenses = $user->appointments()
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->where('status', 'completed')
            ->sum('service_price');

        // Get recent appointments
        $recentAppointments = $user->appointments()
            ->with(['pet', 'shop'])
            ->latest('appointment_date')
            ->take(5)
            ->get();

        // Calculate expense breakdown
        $expenses = [
            'veterinary' => $user->appointments()
                ->where('service_type', 'veterinary')
                ->whereMonth('appointment_date', now()->month)
                ->sum('service_price'),
            'grooming' => $user->appointments()
                ->where('service_type', 'grooming')
                ->whereMonth('appointment_date', now()->month)
                ->sum('service_price'),
            'supplies' => 0, // This would need to be tracked separately
            'other' => 0,    // This would need to be tracked separately
        ];

        // Calculate care trends
        $careTrends = [
            [
                'title' => 'Vaccination Compliance',
                'description' => 'Based on scheduled vs. completed vaccinations',
                'change' => 5, // This would need to be calculated based on historical data
            ],
            [
                'title' => 'Regular Checkups',
                'description' => 'Frequency of veterinary visits',
                'change' => -2,
            ],
            [
                'title' => 'Grooming Routine',
                'description' => 'Consistency in grooming appointments',
                'change' => 8,
            ],
        ];

        // Get upcoming health tasks
        $upcomingTasks = collect();
        foreach ($pets as $pet) {
            // Add vaccination due dates
            $pet->vaccinations()
                ->where('next_due_date', '>', now())
                ->get()
                ->each(function ($vaccination) use (&$upcomingTasks) {
                    $upcomingTasks->push((object)[
                        'title' => $vaccination->vaccine_name . ' Vaccination Due',
                        'pet' => $vaccination->pet,
                        'due_date' => $vaccination->next_due_date,
                    ]);
                });

            // Add parasite control due dates
            $pet->parasiteControls()
                ->where('next_treatment_date', '>', now())
                ->get()
                ->each(function ($control) use (&$upcomingTasks) {
                    $upcomingTasks->push((object)[
                        'title' => $control->treatment_name . ' Treatment Due',
                        'pet' => $control->pet,
                        'due_date' => $control->next_treatment_date,
                    ]);
                });
        }
        $upcomingTasks = $upcomingTasks->sortBy('due_date')->take(5);

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

            // Calculate health score (example algorithm)
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

        return view('profile.pets.dashboard', compact(
            'pets',
            'upcomingAppointments',
            'dueVaccinations',
            'monthlyExpenses',
            'recentAppointments',
            'expenses',
            'careTrends',
            'upcomingTasks'
        ));
    }

    public function showUserAddHealthRecord(Pet $pet)
    {
        // Ensure the user owns the pet
        if ($pet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('profile.pets.user-add-health-record', compact('pet'));
    }
}