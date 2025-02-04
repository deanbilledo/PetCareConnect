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
        
        <!-- Service Duration Info -->
        <div class="mb-6 bg-blue-50 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Service Duration Information</h3>
                    <p class="mt-1 text-sm text-blue-600">Total duration of selected services: {{ $totalDuration }} minutes</p>
                    <p class="mt-1 text-sm text-blue-600">Time slots are adjusted to ensure all services can be completed within the shop's operating hours.</p>
                </div>
            </div>
        </div>

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
                <p class="text-sm text-gray-500 mb-2">Each time slot ensures {{ $totalDuration }} minutes for your service(s)</p>
                
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
                        @change="getAvailableEmployees"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        x-show="!loading && timeSlots.length > 0"
                        required>
                    <option value="">Select a time</option>
                    <template x-for="slot in timeSlots" :key="slot">
                        <option x-text="slot + ' - ' + getEndTime(slot, {{ $totalDuration }})" :value="slot"></option>
                    </template>
                </select>
                
                <!-- No Slots Available Message -->
                <p x-show="!loading && !errorMessage && selectedDate && timeSlots.length === 0" 
                   class="text-yellow-600 text-sm mt-1">
                    No available time slots for the selected date. Please choose another date.
                </p>
            </div>

            <!-- Employee Selection -->
            <div class="mb-6" x-show="selectedTime && !loading">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Employee</label>
                <p class="text-sm text-gray-500 mb-2">Choose an available employee for your service</p>

                <!-- Employee Loading State -->
                <div x-show="loadingEmployees" class="text-gray-500 text-sm mb-2">
                    <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading available employees...
                </div>

                <!-- Employee Error Message -->
                <div x-show="employeeErrorMessage" 
                     x-text="employeeErrorMessage"
                     class="text-red-500 text-sm mb-2">
                </div>

                <!-- Employee Selection Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!loadingEmployees && availableEmployees.length > 0">
                    <template x-for="employee in availableEmployees" :key="employee.id">
                        <label class="relative flex items-start p-4 cursor-pointer bg-white border rounded-lg hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input type="radio" 
                                       name="employee_id" 
                                       :value="employee.id"
                                       x-model="selectedEmployee"
                                       class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                       required>
                            </div>
                            <div class="ml-3 flex items-center">
                                <img :src="employee.profile_photo_url" 
                                     :alt="employee.name"
                                     class="w-12 h-12 rounded-full object-cover mr-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900" x-text="employee.name"></span>
                                    <span class="text-sm text-gray-500" x-text="employee.position"></span>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- No Available Employees Message -->
                <p x-show="!loadingEmployees && availableEmployees.length === 0" 
                   class="text-yellow-600 text-sm mt-1 p-4 bg-yellow-50 rounded-md">
                    No employees available for the selected time slot. Please choose another time.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors"
                        :disabled="!selectedTime || !selectedEmployee || loading || loadingEmployees">
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
        availableEmployees: [],
        selectedEmployee: null,
        loadingEmployees: false,
        employeeErrorMessage: '',
        
        async getTimeSlots() {
            if (!this.selectedDate) return;
            
            this.loading = true;
            this.timeSlots = [];
            this.selectedTime = '';
            this.errorMessage = '';
            this.availableEmployees = [];
            this.selectedEmployee = null;
            
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
        },

        async getAvailableEmployees() {
            if (!this.selectedTime || !this.selectedDate) return;

            this.loadingEmployees = true;
            this.availableEmployees = [];
            this.selectedEmployee = null;
            this.employeeErrorMessage = '';

            try {
                const serviceIds = @json($bookingData['pet_services'] ?? []);
                const response = await fetch(`{{ route('booking.available-employees', $shop) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        date: this.selectedDate,
                        time: this.selectedTime,
                        duration: {{ $totalDuration }},
                        service_ids: Object.values(serviceIds)
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.error || `Server error: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }

                this.availableEmployees = data.employees || [];

            } catch (error) {
                this.employeeErrorMessage = error.message || 'Failed to load available employees';
            } finally {
                this.loadingEmployees = false;
            }
        }
    }
}

// Function to calculate and format end time
function getEndTime(startTime, duration) {
    const [hours, minutes] = startTime.match(/(\d+):(\d+)/).slice(1);
    const startDate = new Date();
    startDate.setHours(parseInt(hours));
    startDate.setMinutes(parseInt(minutes));
    
    const endDate = new Date(startDate.getTime() + duration * 60000);
    return endDate.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit', 
        hour12: true 
    });
}
</script>
@endpush

@endsection 