@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('profile.index') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Profile
        </a>
    </div>

    <!-- Pet Profile Header -->
    <div class="flex flex-col items-center mb-8">
        <div class="relative inline-block">
            <img src="{{ $pet->profile_photo_url ?? asset('images/default-pet.png') }}" 
                 alt="Pet Photo" 
                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                 onerror="this.src='{{ asset('images/default-pet.png') }}'">
            <form action="{{ route('profile.pets.update-photo', $pet) }}" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                @csrf
                <label for="pet_photo" class="cursor-pointer bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 border border-gray-200 flex items-center justify-center w-8 h-8">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input type="file" id="pet_photo" name="pet_photo" class="hidden" onchange="this.form.submit()">
            </form>
        </div>
        <h1 class="text-3xl font-bold mt-4">{{ $pet->name }}</h1>
        <p class="text-gray-600">{{ $pet->breed }} • {{ $pet->type }}</p>
    </div>

    <!-- Basic Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Basic Information</h2>
            <button class="text-teal-500 hover:text-teal-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
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

    <!-- Health Records Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Health Records</h2>
            <div class="flex gap-4">
                <a href="{{ route('profile.pets.health-record', $pet) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    View Health Record
                </a>
                <a href="{{ route('profile.pets.add-health-record', $pet) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    Add Health Record
                </a>
            </div>
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

    <!-- Recent Visits Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Recent Visits</h2>
        </div>
        <div class="space-y-4">
            @forelse($pet->appointments as $appointment)
                <div class="flex items-center space-x-4 {{ !$loop->last ? 'border-b pb-4' : '' }}">
                    <img src="{{ $appointment->shop->profile_photo_url ?? asset('images/shops/default.png') }}" 
                         alt="{{ $appointment->shop->name }}" 
                         class="w-16 h-16 object-cover rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-medium">{{ $appointment->shop->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $appointment->service->name ?? 'Service' }}</p>
                        <p class="text-sm text-gray-500">{{ $appointment->appointment_date->format('M d, Y') }}</p>
                    </div>
                    @if($appointment->shop->rating)
                        <div class="text-yellow-400 flex items-center">
                            <span>★</span>
                            <span class="ml-1 text-gray-600">{{ number_format($appointment->shop->rating, 1) }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-2">No recent visits found</p>
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

    @push('scripts')
    <script>
        function openDeceasedModal() {
            document.getElementById('deceased-modal').classList.remove('hidden');
        }

        function closeDeceasedModal() {
            document.getElementById('deceased-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deceased-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeceasedModal();
            }
        });
    </script>
    @endpush
</div>
@endsection 