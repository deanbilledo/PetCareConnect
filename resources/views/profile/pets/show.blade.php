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
        <div class="relative inline-block">
            <img src="{{ $pet->profile_photo_url ?? asset('images/default-pet.png') }}" 
                 alt="Pet Photo" 
                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                 onerror="this.src='{{ asset('images/default-pet.png') }}'">
            
            <!-- Status Badge -->
            @if($pet->isDeceased())
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-3 py-1 rounded-full text-sm">
                    Deceased
                </div>
            @else
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                    Active
                </div>
            @endif
        </div>

        <h1 class="text-3xl font-bold mt-6">{{ $pet->name }}</h1>
        <p class="text-gray-600">{{ $pet->breed }} • {{ $pet->type }}</p>

        @if($pet->isDeceased())
            <div class="mt-4 text-gray-600">
                <p>Passed away on {{ $pet->death_date->format('F j, Y') }}</p>
                @if($pet->death_reason)
                    <p class="mt-1">Reason: {{ $pet->death_reason }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Basic Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-6">Basic Information</h2>
        
        <!-- Date of Birth and Age -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600">Date of Birth</p>
                <p class="font-medium">
                    {{ $pet->date_of_birth ? $pet->date_of_birth->format('F j, Y') : 'Not set' }}
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
        </div>

        <!-- Physical Characteristics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Weight</p>
                <p class="font-medium">{{ $pet->weight ?? 'Not set' }} {{ $pet->weight ? 'kg' : '' }}</p>
            </div>
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
                            @if($vaccination->vet_notes)
                                <p class="text-sm text-gray-600 mt-2">
                                    <span class="font-medium">Veterinarian Notes:</span><br>
                                    {{ $vaccination->vet_notes }}
                                </p>
                            @endif
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
                            <p class="text-sm text-gray-600">Type: {{ $control->treatment_type }}</p>
                            @if($control->vet_notes)
                                <p class="text-sm text-gray-600 mt-2">
                                    <span class="font-medium">Veterinarian Notes:</span><br>
                                    {{ $control->vet_notes }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Date: {{ $control->treatment_date->format('M d, Y') }}</p>
                            <p class="text-sm {{ $control->next_treatment_date->isPast() ? 'text-red-600' : 'text-teal-600' }}">
                                Next due: {{ $control->next_treatment_date->format('M d, Y') }}
                            </p>
                            @if($control->administered_by)
                                <p class="text-sm text-gray-600">By: {{ $control->administered_by }}</p>
                            @endif
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
                    <div class="flex justify-between items-center {{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                        <div class="flex-1">
                            <p class="font-medium">{{ $issue->issue_title }}</p>
                            <p class="text-sm text-gray-600">{{ $issue->description }}</p>
                            @if($issue->vet_notes)
                                <p class="text-sm text-gray-600 mt-2">
                                    <span class="font-medium">Veterinarian Notes:</span><br>
                                    {{ $issue->vet_notes }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-sm text-gray-600">Identified: {{ $issue->identified_date->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-600">Treatment: {{ $issue->treatment }}</p>
                            @if($issue->administered_by)
                                <p class="text-sm text-gray-600">By: {{ $issue->administered_by }}</p>
                            @endif
                        </div>
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
            @empty
                <p class="text-gray-500 text-center py-2">No recent appointments</p>
            @endforelse
        </div>
    </div>
</div>
@endsection 