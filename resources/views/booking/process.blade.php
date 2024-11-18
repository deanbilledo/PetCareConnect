@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4">
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
                @forelse($pets as $pet)
                <div class="flex items-center mb-2">
                    <input type="checkbox" 
                           id="pet_{{ $pet->id }}"
                           name="pet_ids[]" 
                           value="{{ $pet->id }}" 
                           class="rounded border-gray-300 text-blue-500 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="pet_{{ $pet->id }}" class="ml-2">{{ $pet->name }} ({{ $pet->type }} - {{ $pet->breed }})</label>
                </div>
                @empty
                <p class="text-red-500">Please add a pet to your profile first.</p>
                <a href="{{ route('profile.index') }}" class="text-blue-500 hover:underline">Add Pet</a>
                @endforelse
            </div>

            <!-- Appointment Type -->
            <div class="space-y-4">
                <div class="border rounded-lg p-4 relative">
                    <input type="radio" 
                           name="appointment_type" 
                           value="single" 
                           id="single" 
                           class="absolute top-4 right-4">
                    <label for="single" class="block cursor-pointer">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Pet Appointment</h3>
                                <p class="text-sm text-gray-600">Book an appointment for your pet</p>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="border rounded-lg p-4 relative">
                    <input type="radio" 
                           name="appointment_type" 
                           value="multiple" 
                           id="multiple" 
                           class="absolute top-4 right-4">
                    <label for="multiple" class="block cursor-pointer">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Multiple Pet Appointment</h3>
                                <p class="text-sm text-gray-600">Book an appointment for your pets (maximum of 3)</p>
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
    const form = document.getElementById('appointmentForm');
    const petCheckboxes = document.querySelectorAll('input[name="pet_ids[]"]');
    const appointmentTypes = document.querySelectorAll('input[name="appointment_type"]');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkedPets = document.querySelectorAll('input[name="pet_ids[]"]:checked').length;
            const selectedType = document.querySelector('input[name="appointment_type"]:checked');
            
            if (checkedPets === 0) {
                alert('Please select at least one pet');
                return;
            }
            
            if (!selectedType) {
                alert('Please select an appointment type');
                return;
            }

            // If validation passes, submit the form
            form.submit();
        });
    }

    function updateAppointmentTypes() {
        const checkedPets = document.querySelectorAll('input[name="pet_ids[]"]:checked').length;
        
        if (checkedPets > 1) {
            document.getElementById('multiple').checked = true;
            document.getElementById('single').disabled = true;
        } else if (checkedPets === 1) {
            document.getElementById('single').checked = true;
            document.getElementById('single').disabled = false;
        } else {
            appointmentTypes.forEach(type => type.checked = false);
        }
    }

    petCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateAppointmentTypes);
    });
});
</script>
@endpush

@endsection
