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
                <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Name</label>
                        <input type="text" name="vaccine_name" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Administered</label>
                        <input type="date" name="administered_date" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Administered By</label>
                        <input type="text" name="administered_by" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                        <input type="date" name="next_due_date" required 
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
                <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Name</label>
                        <input type="text" name="treatment_name" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Type</label>
                        <select name="treatment_type" required 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Type</option>
                            <option value="Flea">Flea Treatment</option>
                            <option value="Tick">Tick Treatment</option>
                            <option value="Worm">Deworming</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Date</label>
                        <input type="date" name="treatment_date" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Treatment Due</label>
                        <input type="date" name="next_treatment_date" required 
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
                <form action="#" method="POST" class="grid grid-cols-1 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issue Title</label>
                        <input type="text" name="issue_title" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Identified</label>
                        <input type="date" name="identified_date" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" required 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment/Medication</label>
                        <input type="text" name="treatment" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Veterinarian Notes</label>
                        <textarea name="vet_notes" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
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