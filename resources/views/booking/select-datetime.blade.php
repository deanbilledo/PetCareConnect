@php
use Illuminate\Support\Facades\Log;
@endphp
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4 mt-8">
        <form action="{{ route('booking.select-service', $shop) }}" method="POST" id="backForm">
            @csrf
            @foreach($bookingData['pet_ids'] as $petId)
                <input type="hidden" name="pet_ids[]" value="{{ $petId }}">
            @endforeach
            <input type="hidden" name="appointment_type" value="single">
            <a href="javascript:void(0)" 
               onclick="document.getElementById('backForm').submit()"
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

    <!-- Operating Hours -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Operating Hours</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 0 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Sunday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 0)->first() && $operatingHours->where('day', 0)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 0)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 0)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 1 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Monday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 1)->first() && $operatingHours->where('day', 1)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 1)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 1)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 2 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Tuesday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 2)->first() && $operatingHours->where('day', 2)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 2)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 2)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 3 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Wednesday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 3)->first() && $operatingHours->where('day', 3)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 3)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 3)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 4 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Thursday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 4)->first() && $operatingHours->where('day', 4)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 4)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 4)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 5 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Friday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 5)->first() && $operatingHours->where('day', 5)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 5)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 5)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2 {{ now()->dayOfWeek == 6 ? 'bg-blue-50 px-2 rounded' : '' }}">
                <span class="font-medium">Saturday</span>
                <span class="text-gray-600">
                    @if($operatingHours->where('day', 6)->first() && $operatingHours->where('day', 6)->first()->is_open)
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 6)->first()->open_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($operatingHours->where('day', 6)->first()->close_time)->format('g:i A') }}
                    @else
                        Closed
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Date and Time Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6" x-data="timeSlotPicker()">
        <h2 class="text-lg font-semibold mb-4">Select Date and Time</h2>
        <p class="text-sm text-gray-600 mb-4">Total duration of selected services: {{ $totalDuration }} minutes</p>

        <form action="{{ route('booking.confirm.show', $shop) }}" method="GET" id="bookingForm" onsubmit="return validateForm()">
            @csrf
            
            <!-- Hidden fields for pet_ids and services -->
            @if(isset($bookingData['pet_ids']))
                @foreach($bookingData['pet_ids'] as $petId)
                    <input type="hidden" name="pet_ids[]" value="{{ $petId }}">
                @endforeach
            @endif
            
            @if(isset($bookingData['pet_services']))
                @foreach($bookingData['pet_services'] as $petId => $serviceId)
                    <input type="hidden" name="services[]" value="{{ $serviceId }}">
                @endforeach
            @endif

            <!-- Date Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                <input type="date" 
                       name="appointment_date" 
                       x-model="selectedDate"
                       @change="getTimeSlots"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       max="{{ now()->addMonths(2)->format('Y-m-d') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                       required>
                <p class="mt-1 text-sm text-gray-500">Available dates for the next 2 months</p>
            </div>

            <!-- Time Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Time</label>
                
                <!-- Loading State -->
                <div x-show="loading" class="text-gray-500 text-sm mb-2">
                    <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading available time slots...
                </div>
                
                <!-- Error Message -->
                <div x-show="errorMessage" 
                     x-text="errorMessage"
                     class="text-red-500 text-sm mb-2">
                </div>
                
                <!-- Time Slots Dropdown -->
                <select name="appointment_time" 
                        x-model="selectedTime"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        x-show="!loading && timeSlots.length > 0"
                        required>
                    <option value="">Select a time</option>
                    <template x-for="slot in timeSlots" :key="slot">
                        <option x-text="slot" :value="slot"></option>
                    </template>
                </select>
                
                <!-- No Slots Available Message -->
                <p x-show="!loading && !errorMessage && selectedDate && timeSlots.length === 0" 
                   class="text-yellow-600 text-sm mt-1">
                    No available time slots for the selected date. Please choose another date.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors"
                        :disabled="!selectedTime || loading">
                    Next
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function validateForm() {
    const form = document.getElementById('bookingForm');
    const date = form.querySelector('input[name="appointment_date"]').value;
    const time = form.querySelector('select[name="appointment_time"]').value;
    
    if (!date || !time) {
        alert('Please select both date and time');
        return false;
    }
    
    return true;
}

function timeSlotPicker() {
    return {
        selectedDate: '',
        timeSlots: [],
        selectedTime: '',
        loading: false,
        errorMessage: '',
        
        async getTimeSlots() {
            if (!this.selectedDate) return;
            
            this.loading = true;
            this.timeSlots = [];
            this.selectedTime = '';
            this.errorMessage = '';
            
            try {
                const response = await fetch(`/time-slots/shop/{{ $shop->id }}?date=${this.selectedDate}&duration={{ $totalDuration }}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.error || `Server error: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                this.timeSlots = data.slots || [];
                
                if (data.message && this.timeSlots.length === 0) {
                    this.errorMessage = data.message;
                }
            } catch (error) {
                this.errorMessage = error.message || 'Failed to load time slots';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush

@endsection 