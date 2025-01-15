@php
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4 mt-8">
        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Shop Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center">
            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                 alt="{{ $shop->name }}" 
                 class="w-20 h-20 object-cover rounded-lg mr-4">
            <div>
                <h1 class="text-xl font-bold">{{ $shop->name }}</h1>
                <p class="text-gray-600">{{ $shop->address }}</p>
            </div>
        </div>
    </div>

    <!-- Appointment Process -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Appointment Process</h2>
        <div class="flex justify-between items-center relative">
            <!-- Progress Line -->
            <div class="absolute left-0 right-0 top-1/2 h-0.5 bg-gray-200 -z-10"></div>
            
            <!-- Step 1: Select Service -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Select Service</span>
            </div>

            <!-- Step 2: Choose Date -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Choose Date</span>
            </div>

            <!-- Step 3: Confirm -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Confirm</span>
            </div>
        </div>
    </div>

    <!-- Appointment Options -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Choose an appointment option</h2>
        
        <form action="{{ route('booking.select-service', ['shop' => $shop]) }}" method="POST">
            @csrf
            
            <!-- Pet Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Your Pet(s)</label>
                <div class="space-y-2">
                    @forelse($pets as $pet)
                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50 relative">
                        <input type="checkbox" 
                               id="pet_{{ $pet->id }}"
                               name="pet_ids[]" 
                               value="{{ $pet->id }}" 
                               data-pet-type="{{ Str::singular(strtolower($pet->type)) }}"
                               data-size="{{ $pet->size_category }}"
                               class="pet-checkbox rounded border-gray-300 text-blue-500 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="pet_{{ $pet->id }}" class="ml-3 flex-grow cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ $pet->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $pet->type }} - {{ $pet->breed }}</p>
                                    <p class="text-xs text-gray-500">Size: {{ ucfirst($pet->size_category) }}</p>
                                </div>
                            </div>
                        </label>
                        <!-- Error message container -->
                        <div class="hidden text-red-500 text-xs mt-1 pet-error" id="error_pet_{{ $pet->id }}"></div>
                    </div>
                    @empty
                    <div class="text-center py-4 border rounded-lg">
                        <p class="text-gray-500 mb-2">Please add a pet to your profile first.</p>
                        <a href="{{ route('profile.index') }}" 
                           class="inline-flex items-center text-blue-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Pet
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Appointment Type -->
            <div class="space-y-4">
                <div class="border rounded-lg p-4 relative" id="singleAppointmentOption">
                    <input type="radio" 
                           name="appointment_type" 
                           value="single" 
                           id="single" 
                           class="absolute top-4 right-4"
                           required>
                    <label for="single" class="block cursor-pointer">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Single Pet Appointment</h3>
                                <p class="text-sm text-gray-600">Book an appointment for one pet</p>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="border rounded-lg p-4 relative" id="multipleAppointmentOption">
                    <input type="radio" 
                           name="appointment_type" 
                           value="multiple" 
                           id="multiple" 
                           class="absolute top-4 right-4">
                    <label for="multiple" class="block cursor-pointer">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Multiple Pet Appointment</h3>
                                <p class="text-sm text-gray-600">Book appointments for multiple pets (up to 3)</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Next Button -->
            <div class="mt-6">
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                    Next
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const petCheckboxes = document.querySelectorAll('.pet-checkbox');
    const singleRadio = document.getElementById('single');
    const multipleRadio = document.getElementById('multiple');
    const singleOption = document.getElementById('singleAppointmentOption');
    const multipleOption = document.getElementById('multipleAppointmentOption');

    // Get shop's services and their supported pet types
    const shopServices = @json($shop->services->pluck('pet_types', 'id'));

    // Add debug logging
    console.log('Shop Services:', shopServices);

    function validatePetType(petType, checkbox) {
        let isValid = false;
        const errorDiv = document.getElementById(`error_pet_${checkbox.value}`);
        
        // Normalize the pet type to lowercase and handle singular/plural
        const normalizedPetType = petType.toLowerCase();
        const singularPetType = normalizedPetType.endsWith('s') ? 
            normalizedPetType.slice(0, -1) : normalizedPetType;
        const pluralPetType = normalizedPetType.endsWith('s') ? 
            normalizedPetType : normalizedPetType + 's';
        
        // Debug logging
        console.log('Checking pet types:', {
            original: petType,
            normalized: normalizedPetType,
            singular: singularPetType,
            plural: pluralPetType
        });

        // Check if any service supports this pet type
        Object.values(shopServices).forEach(supportedTypes => {
            if (Array.isArray(supportedTypes)) {
                // Convert all types to lowercase for comparison
                const normalizedTypes = supportedTypes.map(type => type.toLowerCase());
                console.log('Normalized supported types:', normalizedTypes);
                
                // Check both singular and plural forms
                if (normalizedTypes.includes(singularPetType) || 
                    normalizedTypes.includes(pluralPetType)) {
                    isValid = true;
                }
            }
        });

        console.log('Is valid:', isValid);

        if (!isValid) {
            checkbox.checked = false;
            errorDiv.textContent = `Sorry, we don't offer services for ${petType}s at this time.`;
            errorDiv.classList.remove('hidden');
            checkbox.closest('.border').classList.add('border-red-300');
        } else {
            errorDiv.classList.add('hidden');
            checkbox.closest('.border').classList.remove('border-red-300');
        }

        return isValid;
    }

    function updateAppointmentOptions() {
        const checkedPets = Array.from(petCheckboxes).filter(cb => cb.checked);
        const count = checkedPets.length;

        // Reset styles
        singleOption.classList.remove('opacity-50', 'cursor-not-allowed');
        multipleOption.classList.remove('opacity-50', 'cursor-not-allowed');
        singleRadio.disabled = false;
        multipleRadio.disabled = false;

        if (count === 0) {
            // No pets selected
            singleRadio.checked = false;
            multipleRadio.checked = false;
        } else if (count === 1) {
            // One pet selected - enable single, disable multiple
            singleRadio.checked = true;
            multipleOption.classList.add('opacity-50', 'cursor-not-allowed');
            multipleRadio.disabled = true;
        } else if (count > 1) {
            // Multiple pets selected - enable multiple, disable single
            multipleRadio.checked = true;
            singleOption.classList.add('opacity-50', 'cursor-not-allowed');
            singleRadio.disabled = true;
        }

        // Disable checkboxes if max pets reached
        if (count >= 3) {
            petCheckboxes.forEach(cb => {
                if (!cb.checked) {
                    cb.disabled = true;
                    cb.closest('.border').classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        } else {
            petCheckboxes.forEach(cb => {
                cb.disabled = false;
                cb.closest('.border').classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    }

    // Add event listeners
    petCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const petType = this.dataset.petType;
            if (this.checked) {
                if (!validatePetType(petType, this)) {
                    return;
                }
            }
            updateAppointmentOptions();
        });
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const checkedPets = Array.from(petCheckboxes).filter(cb => cb.checked);
        
        if (checkedPets.length === 0) {
            e.preventDefault();
            alert('Please select at least one pet');
            return;
        }

        // Validate each selected pet
        let allValid = checkedPets.every(checkbox => {
            const petType = checkbox.dataset.petType;
            return validatePetType(petType, checkbox);
        });

        if (!allValid) {
            e.preventDefault();
            return;
        }

        if (!singleRadio.checked && !multipleRadio.checked) {
            e.preventDefault();
            alert('Please select an appointment type');
            return;
        }
    });

    // Initial update
    updateAppointmentOptions();
});
</script>
@endpush

@endsection
