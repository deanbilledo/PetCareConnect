@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('profile.pets.health-record', $pet->id) }}" class="flex items-center text-gray-600 hover:text-gray-900 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Health Record
            </a>
        </div>

        <!-- Pet Info Header -->
        <div class="flex items-center mb-8">
            <img src="{{ $pet->profile_photo_url }}" alt="{{ $pet->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-teal-500">
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800">Add Health Record for {{ $pet->name }}</h1>
                <p class="text-sm text-gray-500">Keep track of your pet's health history for better care</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div x-data="{ activeTab: localStorage.getItem('activeHealthTab') || 'vaccination' }" class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px space-x-8" aria-label="Health record types">
                    <button 
                        @click="activeTab = 'vaccination'; localStorage.setItem('activeHealthTab', 'vaccination')" 
                        :class="activeTab === 'vaccination' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Vaccination
                    </button>
                    <button 
                        @click="activeTab = 'parasite'; localStorage.setItem('activeHealthTab', 'parasite')" 
                        :class="activeTab === 'parasite' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Parasite Control
                    </button>
                    <button 
                        @click="activeTab = 'health'; localStorage.setItem('activeHealthTab', 'health')" 
                        :class="activeTab === 'health' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Health Issue
                    </button>
                </nav>
            </div>

            <!-- Health Record Forms -->
            <div class="pt-6">
                <!-- Vaccination Record Form -->
                <div x-show="activeTab === 'vaccination'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-gradient-to-r from-teal-50 to-blue-50 p-4 rounded-lg mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Record vaccinations to keep track of your pet's immunization history. Regular vaccinations help protect your pet from serious diseases.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if ($errors->vaccination->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6 border-l-4 border-red-500">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->vaccination->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.pets.vaccination.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ route('profile.pets.health-record', $pet->id) }}">
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Vaccine Name</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" name="vaccine_name" required value="{{ old('vaccine_name') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                       placeholder="e.g. Rabies, Distemper">
                            </div>
                            <p class="text-xs text-gray-500">Enter the full name of the vaccine</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Date Administered</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="administered_date" required value="{{ old('administered_date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <p class="text-xs text-gray-500">The date when the vaccine was given</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Administered By</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" name="administered_by" required value="{{ old('administered_by') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                       placeholder="e.g. Dr. Smith, ABC Veterinary Clinic">
                            </div>
                            <p class="text-xs text-gray-500">Name of the vet or clinic that gave the vaccine</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Next Due Date</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <input type="date" name="next_due_date" required value="{{ old('next_due_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <p class="text-xs text-gray-500">When the next booster should be given</p>
                        </div>
                        
                        <div class="md:col-span-2 pt-4">
                            <button type="submit" 
                                    class="w-full md:w-auto bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-md transition duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Save Vaccination Record
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Parasite Control Form -->
                <div x-show="activeTab === 'parasite'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-gradient-to-r from-teal-50 to-blue-50 p-4 rounded-lg mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Regular parasite prevention is essential to protect your pet from fleas, ticks, worms, and other pests that can cause health problems.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if ($errors->parasite->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6 border-l-4 border-red-500">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->parasite->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.pets.parasite-control.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ route('profile.pets.health-record', $pet->id) }}">
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Treatment Name</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <input type="text" name="treatment_name" required value="{{ old('treatment_name') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                       placeholder="e.g. Frontline Plus, Heartgard">
                            </div>
                            <p class="text-xs text-gray-500">Brand name or medication used</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Treatment Type</label>
                            <select name="treatment_type" required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Select Type</option>
                                <option value="Flea" {{ old('treatment_type') == 'Flea' ? 'selected' : '' }}>Flea Treatment</option>
                                <option value="Tick" {{ old('treatment_type') == 'Tick' ? 'selected' : '' }}>Tick Treatment</option>
                                <option value="Worm" {{ old('treatment_type') == 'Worm' ? 'selected' : '' }}>Deworming</option>
                                <option value="Other" {{ old('treatment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <p class="text-xs text-gray-500">What kind of parasites this treats</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Treatment Date</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="treatment_date" required value="{{ old('treatment_date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <p class="text-xs text-gray-500">When treatment was applied</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Next Treatment Due</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <input type="date" name="next_treatment_date" required value="{{ old('next_treatment_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <p class="text-xs text-gray-500">When next treatment should be applied</p>
                        </div>
                        
                        <div class="md:col-span-2 pt-4">
                            <button type="submit" 
                                    class="w-full md:w-auto bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-md transition duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Save Parasite Control Record
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Health Issues Form -->
                <div x-show="activeTab === 'health'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-gradient-to-r from-teal-50 to-blue-50 p-4 rounded-lg mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-700">
                                    Document any health issues, illnesses, or conditions your pet has experienced. This helps establish a complete medical history.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if ($errors->health->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6 border-l-4 border-red-500">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->health->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.pets.health-issue.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ route('profile.pets.health-record', $pet->id) }}">
                        
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Issue Title</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <input type="text" name="issue_title" required value="{{ old('issue_title') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                       placeholder="e.g. Skin Rash, Ear Infection, Digestive Issues">
                            </div>
                            <p class="text-xs text-gray-500">Concise name of the health issue</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Date Identified</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="identified_date" required value="{{ old('identified_date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <p class="text-xs text-gray-500">When you first noticed the issue</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Treatment/Medication</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                </div>
                                <input type="text" name="treatment" required value="{{ old('treatment') }}"
                                       class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                       placeholder="e.g. Amoxicillin 250mg, Anti-inflammatory cream">
                            </div>
                            <p class="text-xs text-gray-500">Medications or treatments prescribed</p>
                        </div>
                        
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="3" required 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                      placeholder="Describe symptoms, severity, and progression">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500">Include any symptoms observed and their severity</p>
                        </div>
                        
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea name="vet_notes" rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                      placeholder="Any additional notes, test results, or follow-up care">{{ old('vet_notes') }}</textarea>
                            <p class="text-xs text-gray-500">Optional notes from vet or follow-up care instructions</p>
                        </div>
                        
                        <div class="md:col-span-2 pt-4">
                            <button type="submit" 
                                    class="w-full md:w-auto bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-md transition duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Save Health Issue Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set today's date as default for date fields if not already set
    document.addEventListener('DOMContentLoaded', function() {
        // Show success message if redirect from another page
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            // Add success notification logic here if needed
        }
    });
</script>
@endpush

@endsection 