@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('profile.pets.details', $pet->id) }}" class="flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Pet Details
            </a>
        </div>

        <h1 class="text-2xl font-bold mb-6">Add Health Record for {{ $pet->name }}</h1>

        <!-- Health Record Forms -->
        <div class="space-y-8">
            <!-- Vaccination Record Form -->
            <div class="border-b pb-8">
                <h2 class="text-xl font-semibold mb-4">Vaccination Record</h2>
                @if ($errors->vaccination->any())
                    <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->vaccination->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('profile.pets.vaccination.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Name</label>
                        <input type="text" name="vaccine_name" required value="{{ old('vaccine_name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Administered</label>
                        <input type="date" name="administered_date" required value="{{ old('administered_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Administered By</label>
                        <input type="text" name="administered_by" required value="{{ old('administered_by') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                        <input type="date" name="next_due_date" required value="{{ old('next_due_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                            Add Vaccination Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Parasite Control Form -->
            <div class="border-b pb-8">
                <h2 class="text-xl font-semibold mb-4">Parasite Control</h2>
                @if ($errors->parasite->any())
                    <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->parasite->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('profile.pets.parasite-control.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Name</label>
                        <input type="text" name="treatment_name" required value="{{ old('treatment_name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Type</label>
                        <select name="treatment_type" required 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Type</option>
                            <option value="Flea" {{ old('treatment_type') == 'Flea' ? 'selected' : '' }}>Flea Treatment</option>
                            <option value="Tick" {{ old('treatment_type') == 'Tick' ? 'selected' : '' }}>Tick Treatment</option>
                            <option value="Worm" {{ old('treatment_type') == 'Worm' ? 'selected' : '' }}>Deworming</option>
                            <option value="Other" {{ old('treatment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Date</label>
                        <input type="date" name="treatment_date" required value="{{ old('treatment_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Treatment Due</label>
                        <input type="date" name="next_treatment_date" required value="{{ old('next_treatment_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                            Add Parasite Control Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Health Issues Form -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Health Issue</h2>
                @if ($errors->health->any())
                    <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->health->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('profile.pets.health-issue.store', $pet->id) }}" method="POST" class="grid grid-cols-1 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issue Title</label>
                        <input type="text" name="issue_title" required value="{{ old('issue_title') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Identified</label>
                        <input type="date" name="identified_date" required value="{{ old('identified_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" required 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment/Medication</label>
                        <input type="text" name="treatment" required value="{{ old('treatment') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Veterinarian Notes</label>
                        <textarea name="vet_notes" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('vet_notes') }}</textarea>
                    </div>
                    <div>
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                            Add Health Issue Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection