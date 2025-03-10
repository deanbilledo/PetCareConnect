@php
use Illuminate\Support\Str;


@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    @if(session('shop_mode'))
        <a href="{{ route('shop.appointments') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-6 mt-10">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointments
        </a>
    @else
        <a href="{{ route('profile.pets.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-6 mt-10">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to My Pets
        </a>
    @endif

    <!-- Pet Info Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center">
            <img src="{{ $pet->profile_photo_url }}" alt="{{ $pet->name }}" class="w-20 h-20 rounded-full object-cover mr-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $pet->name }}'s Health Record</h1>
                <div class="flex items-center">
                    @if($pet->isDeceased())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 mr-3">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                            Deceased
                        </span>
                        <span class="text-sm text-gray-600">
                            Passed away on {{ $pet->death_date->format('M d, Y') }}
                            @if($pet->death_reason)
                                • {{ $pet->death_reason }}
                            @endif
                        </span>
                        <button onclick="openDeceasedModal()" class="ml-3 text-sm text-blue-600 hover:text-blue-800">
                            Edit Details
                        </button>
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
            </div>
            @if(!$pet->isDeceased())
            <div class="ml-auto">
                @if(session('shop_mode'))
                <button onclick="window.location.href='{{ route('profile.pets.add-health-record', $pet) }}'" 
                        class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Health Record
                </button>
                @else
                <button onclick="window.location.href='{{ route('profile.pets.user-add-health-record', $pet) }}'" 
                        class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Health Record
                </button>
                @endif
            </div>
            @endif
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

    @if($pet->isDeceased())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-8">
            <p class="text-gray-600 text-sm">
                This is a historical health record. No new health records can be added as this pet is deceased.
            </p>
        </div>
    @endif

    <!-- Health Records Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Vaccination Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Vaccination History</h2>
                @if($pet->vaccinations->isNotEmpty())
                    <span class="text-sm text-gray-500">
                        Last Updated: {{ $pet->vaccinations->first()->administered_date->format('M d, Y') }}
                    </span>
                @endif
            </div>
            <div class="space-y-4">
                @forelse($pet->vaccinations as $vaccination)
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                            <span class="font-medium">{{ $vaccination->vaccine_name }}</span>
                            <span class="text-sm {{ $vaccination->next_due_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                {{ $vaccination->next_due_date->isPast() ? 'Overdue' : 'Up to date' }}
                            </span>
                    </div>
                    <div class="text-sm text-gray-600">
                            <p>Administered: {{ $vaccination->administered_date->format('M d, Y') }}</p>
                            <p>Next Due: {{ $vaccination->next_due_date->format('M d, Y') }}</p>
                            <p>Administered By: {{ $vaccination->administered_by }}</p>
                </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No vaccination records found</p>
                @endforelse
            </div>
        </div>

        <!-- Health Issues -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Health Issues</h2>
            <div class="space-y-4">
                @forelse($pet->healthIssues as $issue)
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <div class="flex items-center">
                            <span class="font-medium">{{ $issue->issue_title }}</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $issue->is_resolved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $issue->is_resolved ? 'Resolved' : 'Active' }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-500 text-sm">{{ $issue->identified_date->format('M d, Y') }}</span>
                            @if(!$pet->isDeceased())
                            <form action="{{ route('profile.pets.update-health-issue', ['pet' => $pet->id, 'issue' => $issue->id]) }}" 
                                  method="POST" 
                                  class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="text-sm {{ $issue->is_resolved ? 'text-yellow-600 hover:text-yellow-700' : 'text-green-600 hover:text-green-700' }}">
                                    {{ $issue->is_resolved ? 'Mark as Active' : 'Mark as Resolved' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $issue->description }}</p>
                    <div class="text-sm text-gray-600">
                        <p>Treatment: {{ $issue->treatment }}</p>
                        @if($issue->vet_notes)
                            <p class="mt-1">Vet Notes: {{ $issue->vet_notes }}</p>
                        @endif
                        @if($issue->resolved_date)
                            <p class="mt-1 text-green-600">Resolved on: {{ \Carbon\Carbon::parse($issue->resolved_date)->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No health issues recorded</p>
                @endforelse
            </div>
        </div>

        <!-- Parasite Prevention -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Parasite Prevention</h2>
            <div class="space-y-4">
                @forelse($pet->parasiteControls as $control)
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                            <span class="font-medium">{{ $control->treatment_name }}</span>
                            <span class="text-sm {{ $control->next_treatment_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                {{ $control->next_treatment_date->isPast() ? 'Overdue' : 'Active' }}
                            </span>
                    </div>
                    <div class="text-sm text-gray-600">
                            <p>Last Applied: {{ $control->treatment_date->format('M d, Y') }}</p>
                            <p>Next Due: {{ $control->next_treatment_date->format('M d, Y') }}</p>
                            <p>Type: {{ $control->treatment_type }} Treatment</p>
                </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No parasite control records found</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Health Summary</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="border rounded-lg p-4">
                    <p class="text-sm text-gray-600">Total Vaccinations</p>
                    <p class="text-2xl font-semibold">{{ $pet->vaccinations->count() }}</p>
                    </div>
                <div class="border rounded-lg p-4">
                    <p class="text-sm text-gray-600">Active Health Issues</p>
                    <p class="text-2xl font-semibold">{{ $pet->healthIssues->where('is_resolved', false)->count() }}</p>
                    </div>
                <div class="border rounded-lg p-4">
                    <p class="text-sm text-gray-600">Upcoming Due Dates</p>
                    <p class="text-2xl font-semibold">
                        {{ $pet->vaccinations->concat($pet->parasiteControls)
                            ->filter(function($record) {
                                return $record->next_due_date->isFuture() && 
                                       $record->next_due_date->diffInDays(now()) <= 30;
                            })->count() }}
                    </p>
                </div>
                <div class="border rounded-lg p-4">
                    <p class="text-sm text-gray-600">Overdue Items</p>
                    <p class="text-2xl font-semibold text-red-600">
                        {{ $pet->vaccinations->concat($pet->parasiteControls)
                            ->filter(function($record) {
                                return $record->next_due_date->isPast();
                            })->count() }}
                    </p>
                </div>
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
@endsection 