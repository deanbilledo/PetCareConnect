@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('profile.pets.index') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Pets
        </a>
    </div>

    <!-- Pet Profile Header -->
    <div class="flex flex-col items-center mb-8">
        <div class="relative">
            <div class="w-32 h-32 relative">
                <img src="{{ $pet->profile_photo_url ?? asset('images/default-pet.png') }}" 
                     alt="Pet Photo" 
                     class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg"
                     onerror="this.src='{{ asset('images/default-pet.png') }}'">
                <form action="{{ route('profile.pets.update-photo', $pet) }}" method="POST" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    <label for="pet_photo" class="absolute bottom-0 right-0 cursor-pointer bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 border border-gray-200 flex items-center justify-center w-8 h-8 z-10">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </label>
                    <input type="file" id="pet_photo" name="pet_photo" class="hidden" accept="image/*" onchange="document.getElementById('photoForm').submit()">
                </form>
            </div>
        </div>
        <h1 class="text-3xl font-bold mt-4">{{ $pet->name }}</h1>
        <p class="text-gray-600">{{ $pet->breed }} • {{ $pet->type }}</p>
        @if(session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                @foreach($errors->all() as $error)
                    <span class="block sm:inline">{{ $error }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Basic Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Basic Information</h2>
            <button type="button" onclick="openEditModal()" class="inline-flex items-center text-teal-500 hover:text-teal-600 bg-white rounded-md p-2 hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span class="ml-2">Edit</span>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <div class="flex items-center mt-1">
                    @if($pet->isDeceased())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                            Deceased
                        </span>
                        <button onclick="openDeceasedModal()" class="ml-3 text-sm text-blue-600 hover:text-blue-800">
                            Edit Details
                        </button>
                        <span class="ml-2 text-sm text-gray-600">
                            ({{ $pet->death_date->format('M d, Y') }})
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Active
                        </span>
                        <button onclick="openDeceasedModal()" class="ml-3 text-sm text-gray-600 hover:text-gray-800">
                            Mark as Deceased
                        </button>
                    @endif
                </div>
                @if($pet->isDeceased() && $pet->death_reason)
                    <p class="text-sm text-gray-600 mt-1">
                        Reason: {{ $pet->death_reason }}
                    </p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600">Date of Birth</p>
                <p class="font-medium">
                    {{ $pet->date_of_birth ? $pet->date_of_birth->format('M d, Y') : 'Not set' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Age</p>
                <p class="font-medium">
                    @php
                        $birthDate = $pet->date_of_birth;
                        if ($birthDate) {
                            $now = $pet->isDeceased() ? $pet->death_date : now();
                            $years = (int)$birthDate->diffInYears($now);
                            $totalMonths = (int)$birthDate->diffInMonths($now);
                            $months = (int)($totalMonths % 12);
                            $days = (int)$birthDate->copy()->addMonths($totalMonths)->diffInDays($now);
                            
                            if ($years >= 1) {
                                echo $years . ' ' . Str::plural('year', $years);
                                if ($months > 0) {
                                    echo ' and ' . $months . ' ' . Str::plural('month', $months);
                                }
                                echo $pet->isDeceased() ? ' (at time of death)' : ' old';
                            } else {
                                if ($months > 0) {
                                    echo $months . ' ' . Str::plural('month', $months);
                                    if ($days > 0) {
                                        echo ' and ' . $days . ' ' . Str::plural('day', $days);
                                    }
                                    echo $pet->isDeceased() ? ' (at time of death)' : ' old';
                                } else {
                                    $days = (int)$birthDate->diffInDays($now);
                                    echo $days . ' ' . Str::plural('day', $days);
                                    echo $pet->isDeceased() ? ' (at time of death)' : ' old';
                                }
                            }
                        } else {
                            echo 'Not set';
                        }
                    @endphp
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Weight</p>
                <p class="font-medium">{{ $pet->weight ?? 'Not set' }} {{ $pet->weight ? 'kg' : '' }}</p>
            </div>
        </div>

        <!-- Additional Pet Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <p class="text-sm text-gray-600">Size Category</p>
                <p class="font-medium">{{ Str::title($pet->size_category ?? 'Not set') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Color/Markings</p>
                <p class="font-medium">{{ $pet->color_markings ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Coat Type</p>
                <p class="font-medium">{{ $pet->coat_type ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="font-medium">{{ $pet->type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Breed</p>
                <p class="font-medium">{{ $pet->breed }}</p>
            </div>
        </div>
    </div>

    <!-- Grooming Status - Add before health records section -->
    @if(!$pet->isDeceased())
        <x-pet-grooming-status :pet="$pet" />
    @endif

    <!-- Pet Health Records -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Health Records</h2>
            <a href="{{ route('profile.pets.health-record', $pet->id) }}" class="inline-flex items-center text-teal-500 hover:text-teal-600">
                <span>View Complete Records</span>
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Vaccination History -->
        <div class="mb-8">
            <h3 class="text-lg font-medium mb-4">Vaccination History</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                @forelse($pet->vaccinations as $vaccination)
                    <div class="flex justify-between items-center {{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                        <div>
                            <p class="font-medium">{{ $vaccination->vaccine_name }}</p>
                            <p class="text-sm text-gray-600">Administered by: {{ $vaccination->administered_by }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Date: {{ $vaccination->administered_date->format('M d, Y') }}</p>
                            <p class="text-sm {{ $vaccination->next_due_date->isPast() ? 'text-red-600' : 'text-teal-600' }}">
                                Next due: {{ $vaccination->next_due_date->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-2">No vaccination records found</p>
                @endforelse
            </div>
        </div>

        <!-- Parasite Control -->
        <div class="mb-8">
            <h3 class="text-lg font-medium mb-4">Parasite Control</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                @forelse($pet->parasiteControls as $control)
                    <div class="flex justify-between items-center {{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                        <div>
                            <p class="font-medium">{{ $control->treatment_name }}</p>
                            <p class="text-sm text-gray-600">{{ $control->treatment_type }} Treatment</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Last treatment: {{ $control->treatment_date->format('M d, Y') }}</p>
                            <p class="text-sm {{ $control->next_treatment_date->isPast() ? 'text-red-600' : 'text-teal-600' }}">
                                Next due: {{ $control->next_treatment_date->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-2">No parasite control records found</p>
                @endforelse
            </div>
        </div>

        <!-- Health Issues -->
        <div>
            <h3 class="text-lg font-medium mb-4">Health Issues</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                @forelse($pet->healthIssues as $issue)
                    <div class="{{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                        <div class="flex justify-between items-start mb-2">
                            <p class="font-medium">{{ $issue->issue_title }}</p>
                            <p class="text-sm text-gray-600">{{ $issue->identified_date->format('M d, Y') }}</p>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $issue->description }}</p>
                        <p class="text-sm"><span class="text-gray-600">Treatment:</span> {{ $issue->treatment }}</p>
                        @if($issue->vet_notes)
                            <p class="text-sm mt-2"><span class="text-gray-600">Vet Notes:</span> {{ $issue->vet_notes }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-2">No health issues recorded</p>
                @endforelse
            </div>
        </div>
    </div>

   
    <!-- Recent Appointments -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-6">Recent Appointments</h2>
        <div class="bg-gray-50 rounded-lg p-4">
            @forelse($pet->appointments as $appointment)
                <a href="{{ route('appointments.show', $appointment) }}" class="block hover:bg-gray-100 transition-colors rounded-md -mx-2 px-2 py-2">
                    <div class="flex justify-between items-center {{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                        <div>
                            <p class="font-medium">{{ $appointment->service_type }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->shop->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">{{ $appointment->appointment_date->format('M d, Y g:i A') }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($appointment->status === 'completed') bg-green-100 text-green-800
                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ Str::title($appointment->status) }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 text-center py-2">No recent appointments</p>
            @endforelse
        </div>
    </div>

    <!-- Update History -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <h2 class="text-xl font-semibold mb-6">Update History</h2>
        <div class="bg-gray-50 rounded-lg p-4">
            @forelse($pet->updateHistories as $history)
                <div class="flex justify-between items-start {{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                    <div>
                        <p class="font-medium">{{ Str::title(str_replace('_', ' ', $history->field_name)) }}</p>
                        <div class="text-sm">
                            <span class="text-red-600">{{ $history->old_value ?: 'Not set' }}</span>
                            <svg class="w-4 h-4 inline mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            <span class="text-green-600">{{ $history->new_value }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">{{ $history->created_at->format('M d, Y g:i A') }}</p>
                        <p class="text-xs text-gray-500">by {{ $history->user->name }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-2">No update history available</p>
            @endforelse
        </div>
    </div>

    <!-- Deceased Status Modal -->
    <div id="deceased-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" x-data>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ $pet->isDeceased() ? 'Update Deceased Details' : 'Mark as Deceased' }}
                    </h3>
                    <form action="{{ route('profile.pets.mark-deceased', $pet) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="death_date" class="block text-sm font-medium text-gray-700">Date of Death</label>
                                <input type="date" id="death_date" name="death_date" required
                                       max="{{ date('Y-m-d') }}"
                                       value="{{ $pet->death_date?->format('Y-m-d') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="death_reason" class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                                <textarea id="death_reason" name="death_reason" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ $pet->death_reason }}</textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeDeceasedModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-teal-500 text-white rounded-md text-sm font-medium hover:bg-teal-600">
                                {{ $pet->isDeceased() ? 'Update Details' : 'Mark as Deceased' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Pet Information Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Edit Pet Information</h3>
                    <form action="{{ route('profile.pets.update', $pet) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="name" name="name" value="{{ $pet->name }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select id="type" name="type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="Dog" {{ $pet->type === 'Dog' ? 'selected' : '' }}>Dog</option>
                                    <option value="Cat" {{ $pet->type === 'Cat' ? 'selected' : '' }}>Cat</option>
                                    <option value="Exotic" {{ $pet->type === 'Exotic' ? 'selected' : '' }}>Exotic</option>
                                </select>
                            </div>
                            <div id="species-field" class="{{ $pet->type !== 'Exotic' ? 'hidden' : '' }}">
                                <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                                <input type="text" id="species" name="species" value="{{ $pet->species }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                                <input type="text" id="breed" name="breed" value="{{ $pet->breed }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" 
                                       value="{{ $pet->date_of_birth ? $pet->date_of_birth->format('Y-m-d') : '' }}" required
                                       max="{{ date('Y-m-d') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" value="{{ $pet->weight }}" required
                                       step="0.1" min="0.1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="size_category" class="block text-sm font-medium text-gray-700">Size Category</label>
                                <select id="size_category" name="size_category" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="Small" {{ strtolower($pet->size_category) === 'small' ? 'selected' : '' }}>Small</option>
                                    <option value="Medium" {{ strtolower($pet->size_category) === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Large" {{ strtolower($pet->size_category) === 'large' ? 'selected' : '' }}>Large</option>
                                </select>
                            </div>
                            <div>
                                <label for="color_markings" class="block text-sm font-medium text-gray-700">Color/Markings</label>
                                <input type="text" id="color_markings" name="color_markings" value="{{ $pet->color_markings }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="coat_type" class="block text-sm font-medium text-gray-700">Coat Type</label>
                                <input type="text" id="coat_type" name="coat_type" value="{{ $pet->coat_type }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-teal-500 text-white rounded-md text-sm font-medium hover:bg-teal-600">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add error handling and debugging
        document.addEventListener('DOMContentLoaded', function() {
            // Debug elements existence
            console.log('Edit modal exists:', !!document.getElementById('edit-modal'));
            console.log('Photo input exists:', !!document.getElementById('pet_photo'));
            
            function openEditModal() {
                const modal = document.getElementById('edit-modal');
                if (modal) {
                    modal.classList.remove('hidden');
                } else {
                    console.error('Edit modal not found');
                }
            }

            function closeEditModal() {
                const modal = document.getElementById('edit-modal');
                if (modal) {
                    modal.classList.add('hidden');
                } else {
                    console.error('Edit modal not found');
                }
            }

            // Attach modal events
            const editModal = document.getElementById('edit-modal');
            if (editModal) {
                editModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeEditModal();
                    }
                });
            }

            // Attach type change handler
            const typeSelect = document.getElementById('type');
            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    const speciesField = document.getElementById('species-field');
                    if (speciesField) {
                        if (this.value === 'Exotic') {
                            speciesField.classList.remove('hidden');
                            document.getElementById('species')?.setAttribute('required', 'required');
                        } else {
                            speciesField.classList.add('hidden');
                            document.getElementById('species')?.removeAttribute('required');
                        }
                    }
                });
            }

            // Attach photo upload handler
            const photoInput = document.getElementById('pet_photo');
            if (photoInput) {
                photoInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) { // 2MB
                            alert('File size must be less than 2MB');
                            this.value = '';
                            return;
                        }
                        
                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!validTypes.includes(file.type)) {
                            alert('Please select a valid image file (JPEG, PNG, or JPG)');
                            this.value = '';
                            return;
                        }

                        // Submit the form
                        const form = document.getElementById('photoForm');
                        if (form) {
                            form.submit();
                        } else {
                            console.error('Photo form not found');
                        }
                    }
                });
            }
        });

        // Make functions globally available
        window.openEditModal = function() {
            const modal = document.getElementById('edit-modal');
            if (modal) {
                modal.classList.remove('hidden');
            } else {
                console.error('Edit modal not found');
            }
        };

        window.closeEditModal = function() {
            const modal = document.getElementById('edit-modal');
            if (modal) {
                modal.classList.add('hidden');
            } else {
                console.error('Edit modal not found');
            }
        };

            // Deceased modal functionality
            function openDeceasedModal() {
                document.getElementById('deceased-modal').classList.remove('hidden');
            }
            
            function closeDeceasedModal() {
                document.getElementById('deceased-modal').classList.add('hidden');
            }
    </script>
    @endpush
</div>
@endsection 