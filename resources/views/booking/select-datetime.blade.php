@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4">
        <form action="{{ route('booking.select-service', $shop) }}" method="POST" id="backForm">
            @csrf
            @foreach(request('pet_ids') as $petId)
                <input type="hidden" name="pet_ids[]" value="{{ $petId }}">
            @endforeach
            <input type="hidden" name="appointment_type" value="{{ request('appointment_type') }}">
            <a href="javascript:void(0)" 
               onclick="event.preventDefault(); document.getElementById('backForm').submit();"
               class="text-gray-600 hover:text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </form>
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

    <!-- Progress Steps -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Appointment Process</h2>
        <div class="flex justify-between items-center relative">
            <!-- Progress Line -->
            <div class="absolute left-0 right-0 top-1/2 h-0.5 bg-gray-200 -z-10">
                <div class="w-2/3 h-full bg-blue-500"></div>
            </div>
            
            <!-- Steps -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Select Service</span>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Choose Date</span>
            </div>

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

    <!-- Date and Time Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Select Date and Time</h2>

        <form action="{{ route('booking.confirm', $shop) }}" method="POST">
            @csrf
            
            <!-- Hidden fields for pet_ids and services -->
            @foreach(request('pet_ids') as $petId)
                <input type="hidden" name="pet_ids[]" value="{{ $petId }}">
            @endforeach
            
            @foreach(request('services') as $service)
                <input type="hidden" name="services[]" value="{{ $service }}">
            @endforeach

            <!-- Date Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                <input type="date" 
                       name="appointment_date" 
                       min="{{ date('Y-m-d') }}"
                       max="{{ date('Y-m-d', strtotime('+2 months')) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                       required>
                <p class="mt-1 text-sm text-gray-500">Available dates for the next 2 months</p>
            </div>

            <!-- Time Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Time</label>
                <select name="appointment_time" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        required>
                    <option value="">Choose a time slot</option>
                    <optgroup label="Morning">
                        @foreach($availableSlots as $slot)
                            @if(strtotime($slot) < strtotime('12:00'))
                                <option value="{{ $slot }}">{{ date('g:i A', strtotime($slot)) }}</option>
                            @endif
                        @endforeach
                    </optgroup>
                    <optgroup label="Afternoon">
                        @foreach($availableSlots as $slot)
                            @if(strtotime($slot) >= strtotime('12:00'))
                                <option value="{{ $slot }}">{{ date('g:i A', strtotime($slot)) }}</option>
                            @endif
                        @endforeach
                    </optgroup>
                </select>
                <p class="mt-1 text-sm text-gray-500">Business hours: 8:30 AM to 5:00 PM</p>
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
    // Get today's date in YYYY-MM-DD format
    const today = new Date().toISOString().split('T')[0];
    
    // Set min date to today and max date to 2 months from now
    const dateInput = document.querySelector('input[name="appointment_date"]');
    dateInput.min = today;
    
    // Disable Sundays in the date picker
    dateInput.addEventListener('input', function(e) {
        const selected = new Date(this.value);
        if (selected.getDay() === 0) { // 0 is Sunday
            alert('Sorry, we are closed on Sundays. Please select another day.');
            this.value = '';
        }
    });
});
</script>
@endpush

@endsection 