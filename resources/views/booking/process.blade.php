@php
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Back Button -->
    <div class="mb-4 mt-4">
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Shop Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 transform transition-all hover:shadow-md">
        <div class="flex items-center">
            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                 alt="{{ $shop->name }}" 
                 class="w-24 h-24 object-cover rounded-xl mr-6 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $shop->name }}</h1>
                <p class="text-gray-600 mt-1">{{ $shop->address }}</p>
            </div>
        </div>
    </div>

    <!-- Appointment Process -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-6 text-gray-800">Booking Progress</h2>
        <div class="flex justify-between items-center relative">
            <!-- Progress Line -->
            <div class="absolute left-0 right-0 top-1/2 h-1 bg-gray-200 -z-10"></div>
            
            <!-- Step 1: Select Service -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2 shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-blue-500">Select Service</span>
            </div>

            <!-- Step 2: Choose Date -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-400">Choose Date</span>
            </div>

            <!-- Step 3: Confirm -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-400">Confirm</span>
            </div>
        </div>
    </div>

    <!-- Appointment Options -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Choose Your Appointment</h2>
        
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('booking.select-service', $shop) }}" method="POST">
            @csrf
            
            <!-- Appointment Type Selection -->
            <div class="mb-8 space-y-4">
                <!-- Single Pet Appointment -->
                <label class="relative block cursor-pointer group">
                    <input type="radio" name="appointment_type" value="single" class="sr-only peer" required>
                    <div class="border-2 border-gray-200 rounded-xl p-6 transition-all duration-300 ease-in-out
                        hover:border-blue-200 hover:bg-blue-50/30
                        peer-checked:border-blue-500 peer-checked:bg-blue-50/50
                        group-hover:shadow-md">
                        <div class="flex items-center space-x-4">
                            <!-- Radio Circle -->
                            <div class="relative flex-shrink-0 w-6 h-6 border-2 border-gray-300 rounded-full 
                                group-hover:border-blue-400 peer-checked:border-blue-500 transition-colors">
                                <div class="absolute inset-0 m-1 rounded-full bg-blue-500 opacity-0 
                                    peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            
                            <!-- Text Content -->
                            <div class="flex-grow">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    Pet Appointment
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Book an appointment for your pet
                                </p>
                            </div>
                            
                            <!-- Plus Icon -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-gray-200 
                                flex items-center justify-center group-hover:border-blue-400 
                                peer-checked:border-blue-500 transition-colors">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 peer-checked:text-blue-500" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Multiple Pet Appointment -->
                <label class="relative block cursor-pointer group">
                    <input type="radio" name="appointment_type" value="multiple" class="sr-only peer">
                    <div class="border-2 border-gray-200 rounded-xl p-6 transition-all duration-300 ease-in-out
                        hover:border-blue-200 hover:bg-blue-50/30
                        peer-checked:border-blue-500 peer-checked:bg-blue-50/50
                        group-hover:shadow-md">
                        <div class="flex items-center space-x-4">
                            <!-- Radio Circle -->
                            <div class="relative flex-shrink-0 w-6 h-6 border-2 border-gray-300 rounded-full 
                                group-hover:border-blue-400 peer-checked:border-blue-500 transition-colors">
                                <div class="absolute inset-0 m-1 rounded-full bg-blue-500 opacity-0 
                                    peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            
                            <!-- Text Content -->
                            <div class="flex-grow">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    Multiple Pet Appointment
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Book an appointment for your pets (maximum of 3)
                                </p>
                            </div>
                            
                            <!-- Plus Icon -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-gray-200 
                                flex items-center justify-center group-hover:border-blue-400 
                                peer-checked:border-blue-500 transition-colors">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 peer-checked:text-blue-500" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Pet Selection Sections -->
            @if($exoticPets->count() > 0)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Exotic Pets</h3>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">Exotic pets must be booked individually and separately from other pets.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($exoticPets as $pet)
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <input type="checkbox" 
                                       name="pet_ids[]" 
                                       value="{{ $pet->id }}" 
                                       class="h-5 w-5 text-blue-500 border-gray-300 rounded focus:ring-blue-500 exotic-pet"
                                       onchange="validateExoticSelection(this)">
                                <span class="ml-3 text-gray-700">{{ $pet->name }} <span class="text-gray-500">({{ $pet->type }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($regularPets->count() > 0)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Regular Pets</h3>
                    <div class="space-y-3">
                        @foreach($regularPets as $pet)
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <input type="checkbox" 
                                       name="pet_ids[]" 
                                       value="{{ $pet->id }}" 
                                       class="h-5 w-5 text-blue-500 border-gray-300 rounded focus:ring-blue-500 regular-pet">
                                <span class="ml-3 text-gray-700">{{ $pet->name }} <span class="text-gray-500">({{ $pet->type }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Next Button -->
            <div class="mt-8">
                <button type="submit" 
                        class="w-full bg-blue-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition-all active:scale-95">
                    Continue to Services
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function validateExoticSelection(checkbox) {
    const exoticCheckboxes = document.querySelectorAll('.exotic-pet');
    const regularCheckboxes = document.querySelectorAll('.regular-pet');
    
    if (checkbox.checked) {
        // Uncheck all other exotic pets
        exoticCheckboxes.forEach(box => {
            if (box !== checkbox) {
                box.checked = false;
                box.closest('label').classList.remove('bg-gray-50');
            } else {
                box.closest('label').classList.add('bg-gray-50');
            }
        });
        
        // Uncheck all regular pets
        regularCheckboxes.forEach(box => {
            box.checked = false;
            box.closest('label').classList.remove('bg-gray-50');
        });
        
        // Auto-select "single" appointment type
        document.querySelector('input[name="appointment_type"][value="single"]').checked = true;
    } else {
        checkbox.closest('label').classList.remove('bg-gray-50');
    }
}

// Track selected regular pets
let selectedRegularPetsCount = 0;

document.querySelectorAll('.regular-pet').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const regularPets = document.querySelectorAll('.regular-pet:checked');
        selectedRegularPetsCount = regularPets.length;
        
        if (this.checked) {
            // Uncheck all exotic pets
            document.querySelectorAll('.exotic-pet').forEach(box => {
                box.checked = false;
                box.closest('label').classList.remove('bg-gray-50');
            });
            this.closest('label').classList.add('bg-gray-50');
        } else {
            this.closest('label').classList.remove('bg-gray-50');
        }
        
        // Auto-select appointment type based on pet count
        updateAppointmentTypeBasedOnSelection();
    });
});

// Initialize selection when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check for any pre-selected pets (might happen if coming back from another page)
    const regularPets = document.querySelectorAll('.regular-pet:checked');
    selectedRegularPetsCount = regularPets.length;
    
    // Set initial selection state
    updateAppointmentTypeBasedOnSelection();
    
    // Highlight selected pet items
    document.querySelectorAll('.regular-pet:checked, .exotic-pet:checked').forEach(checkbox => {
        checkbox.closest('label').classList.add('bg-gray-50');
    });
});

// Function to update appointment type based on pet selection
function updateAppointmentTypeBasedOnSelection() {
    const singleRadio = document.querySelector('input[name="appointment_type"][value="single"]');
    const multipleRadio = document.querySelector('input[name="appointment_type"][value="multiple"]');
    
    // Check if any exotic pet is selected
    const exoticSelected = document.querySelectorAll('.exotic-pet:checked').length > 0;
    
    if (exoticSelected) {
        // Exotic pets can only be booked with "single" appointment type
        singleRadio.checked = true;
        return;
    }
    
    // Auto-select based on number of regular pets
    if (selectedRegularPetsCount === 1) {
        singleRadio.checked = true;
    } else if (selectedRegularPetsCount >= 2 && selectedRegularPetsCount <= 3) {
        multipleRadio.checked = true;
    }
}
</script>

@endsection
