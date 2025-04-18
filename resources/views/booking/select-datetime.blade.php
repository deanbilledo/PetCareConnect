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
                <p class="text-sm text-gray-500 mb-2">
                    Each time slot ensures {{ $totalDuration }} minutes for your service(s). 
                    <span class="text-blue-600 font-medium">
                        Service duration: {{ $totalDuration }} minutes 
                        ({{ floor($totalDuration/60) > 0 ? floor($totalDuration/60) . ' hour' . (floor($totalDuration/60) > 1 ? 's' : '') : '' }}
                        {{ $totalDuration % 60 > 0 ? ($totalDuration % 60) . ' min' : '' }})
                    </span>
                </p>
                
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
                    <template x-for="slot in timeSlots" :key="slot.time">
                        <option :value="slot.time">
                            <span x-text="slot.time"></span>
                            <span x-show="slot.end_time"> - </span>
                            <span x-text="slot.end_time"></span>
                            <span x-text="' - ' + slot.available_employees + ' of ' + slot.total_employees + ' groomers available'"></span>
                        </option>
                    </template>
                </select>

                <!-- Time Slots Grid (Alternative View) -->
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-4" x-show="!loading && timeSlots.length > 0">
                    <template x-for="slot in timeSlots" :key="slot.time">
                        <button type="button"
                                @click="selectedTime = slot.time; getAvailableEmployees()"
                                :class="{
                                    'border rounded-lg p-3 text-center transition-colors': true,
                                    'bg-blue-50 border-blue-200 text-blue-700': selectedTime === slot.time,
                                    'border-gray-200 hover:bg-gray-50': selectedTime !== slot.time
                                }">
                            <div class="font-medium">
                                <span x-text="slot.time"></span>
                                <span x-show="slot.end_time"> - </span>
                                <span x-text="slot.end_time"></span>
                            </div>
                            <div class="text-xs mt-1" :class="{
                                'text-blue-600': selectedTime === slot.time,
                                'text-gray-500': selectedTime !== slot.time
                            }">
                                <span x-text="slot.available_employees"></span>
                                <span>of</span>
                                <span x-text="slot.total_employees"></span>
                                <span>available</span>
                            </div>
                        </button>
                    </template>
                </div>

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
                            <div class="ml-3 flex-1">
                                <div class="flex items-center">
                                    <img :src="employee.profile_photo_url" 
                                         :alt="employee.name"
                                         class="w-12 h-12 rounded-full object-cover mr-3">
                                    <div class="flex flex-col flex-1">
                                        <span class="text-sm font-medium text-gray-900" x-text="employee.name"></span>
                                        <span class="text-sm text-gray-500" x-text="employee.position"></span>
                                        <!-- Star Rating Display -->
                                        <div class="flex items-center mt-1">
                                            <!-- 5 stars only -->
                                            <div class="flex space-x-0.5">
                                                <svg class="w-4 h-4" :class="employee.rating >= 1 ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4" :class="employee.rating >= 2 ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4" :class="employee.rating >= 3 ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4" :class="employee.rating >= 4 ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4" :class="employee.rating >= 5 ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </div>
                                            <span class="ml-1 text-sm text-gray-600" x-text="employee.rating > 0 ? `${Math.min(employee.rating, 5).toFixed(1)} / 5.0 (${employee.ratings_count} ${employee.ratings_count === 1 ? 'rating' : 'ratings'})` : 'No ratings yet'"></span>
                                        </div>
                                    </div>
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
                // First, get the time slots
                const timeSlotsResponse = await fetch(`/time-slots/shop/{{ $shop->id }}?date=${this.selectedDate}&duration=${parseInt({{ $totalDuration }}, 10)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });
                
                if (!timeSlotsResponse.ok) {
                    const errorData = await timeSlotsResponse.json().catch(() => ({}));
                    throw new Error(errorData.error || `Server error: ${timeSlotsResponse.status}`);
                }
                
                const timeSlotsData = await timeSlotsResponse.json();
                
                if (timeSlotsData.error) {
                    throw new Error(timeSlotsData.error);
                }

                // Ensure timeSlotsData is an array, if not, try to access the slots property
                const timeSlotArray = Array.isArray(timeSlotsData) ? timeSlotsData : 
                                    (timeSlotsData.slots || timeSlotsData.data || []);

                // For each time slot, check employee availability
                const serviceIds = @json($bookingData['pet_services'] ?? []);
                const slots = [];

                for (const slot of timeSlotArray) {
                    const employeesResponse = await fetch(`{{ route('booking.available-employees', $shop) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            date: this.selectedDate,
                            time: slot.time,
                            duration: {{ $totalDuration }},
                            service_ids: Object.values(serviceIds)
                        })
                    });

                    if (!employeesResponse.ok) {
                        continue;
                    }

                    const employeesData = await employeesResponse.json();
                    
                    if (employeesData.success) {
                        // The backend should now correctly filter out employees with existing appointments
                        // So we can just use the count of available employees from the response
                        const availableEmployees = employeesData.employees.length;
                        const totalEmployees = employeesData.employees.length;
                        
                        slots.push({
                            ...slot,
                            available_employees: availableEmployees,
                            total_employees: totalEmployees
                        });
                    }
                }
                
                // Keep only slots that have available employees
                this.timeSlots = slots.filter(slot => slot.available_employees > 0);
                
                if (this.timeSlots.length === 0) {
                    this.errorMessage = 'No available time slots for the selected date';
                }
            } catch (error) {
                console.error('Error loading time slots:', error);
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
                        service_ids: Object.values(serviceIds),
                        include_ratings: true
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

                // Debug employee time off data
                console.log('Raw employee data from API:', data.employees);
                
                // Check if time_off_requests data is present
                const hasTimeOffData = data.employees.some(emp => emp.time_off_requests && emp.time_off_requests.length > 0);
                console.log('Does API include time_off_requests data?', hasTimeOffData);
                
                if (!hasTimeOffData) {
                    console.warn('⚠️ No time_off_requests data found in the API response. This might be why time off is not being considered.');
                } else {
                    // Log details of time off data
                    data.employees.forEach(emp => {
                        if (emp.time_off_requests && emp.time_off_requests.length > 0) {
                            console.log(`Employee ${emp.name} has ${emp.time_off_requests.length} time off request(s):`, emp.time_off_requests);
                        }
                    });
                }

                // Parse the selected appointment start time properly
                const [selectedTimeHours, selectedTimeMinutes, selectedTimePeriod] = this.selectedTime.match(/(\d+):(\d+)\s*([APM]{2})/).slice(1);
                let hours = parseInt(selectedTimeHours);
                if (selectedTimePeriod.toUpperCase() === 'PM' && hours < 12) hours += 12;
                if (selectedTimePeriod.toUpperCase() === 'AM' && hours === 12) hours = 0;
                
                const appointmentStart = new Date(this.selectedDate);
                appointmentStart.setHours(hours, parseInt(selectedTimeMinutes), 0, 0);
                
                const appointmentEnd = new Date(appointmentStart);
                appointmentEnd.setMinutes(appointmentEnd.getMinutes() + {{ $totalDuration }});

                // Filter employees based on their schedules and time off requests
                this.availableEmployees = data.employees.map(employee => ({
                    ...employee,
                    rating: employee.rating || 0,
                    ratings_count: employee.ratings_count || 0
                })).filter(employee => {
                    // Check for time off requests
                    const hasTimeOff = employee.time_off_requests?.some(timeOff => {
                        // Only consider approved or pending time off requests
                        if (timeOff.status !== 'approved' && timeOff.status !== 'pending') {
                            return false;
                        }
                        
                        // Create date objects for proper comparison
                        const timeOffStart = new Date(timeOff.start_date);
                        const timeOffEnd = new Date(timeOff.end_date);
                        
                        // Set times to beginning/end of day for date comparison only
                        timeOffStart.setHours(0, 0, 0, 0);
                        timeOffEnd.setHours(23, 59, 59, 999);
                        
                        const appointmentDate = new Date(this.selectedDate);
                        appointmentDate.setHours(0, 0, 0, 0);
                        
                        // If time off status is listed, log it for debugging
                        console.log(`Checking time off for employee ${employee.name}:`, {
                            timeOffId: timeOff.id,
                            status: timeOff.status,
                            startDate: timeOffStart.toLocaleDateString(),
                            endDate: timeOffEnd.toLocaleDateString(),
                            appointmentDate: appointmentDate.toLocaleDateString(),
                            isInTimeOffRange: appointmentDate >= timeOffStart && appointmentDate <= timeOffEnd
                        });
                        
                        // Return true if the appointment date is within the time off period
                        return (
                            appointmentDate >= timeOffStart && 
                            appointmentDate <= timeOffEnd
                        );
                    });
                    
                    // Check for existing appointments
                    const hasAppointment = employee.appointments?.some(appointment => {
                        // Skip cancelled appointments
                        if (appointment.status === 'cancelled' || appointment.status === 'completed') {
                            return false;
                        }
                        
                        // Parse appointment time properly using a more robust method
                        try {
                            // Create a base date from the appointment date
                            const existingAppointmentDate = new Date(appointment.appointment_date);
                            if (isNaN(existingAppointmentDate.getTime())) {
                                console.error('Invalid appointment date:', appointment.appointment_date);
                                return false;
                            }
                            
                            // Parse the time portion
                            const appointmentTime = appointment.appointment_time || '00:00 AM';
                            const timeParts = appointmentTime.match(/(\d+):(\d+)\s*([APM]{2})/i);
                            
                            if (!timeParts) {
                                console.error('Invalid appointment time format:', appointmentTime);
                                return false;
                            }
                            
                            let [_, hours, minutes, period] = timeParts;
                            hours = parseInt(hours);
                            minutes = parseInt(minutes);
                            
                            // Convert to 24-hour format
                            if (period.toUpperCase() === 'PM' && hours < 12) hours += 12;
                            if (period.toUpperCase() === 'AM' && hours === 12) hours = 0;
                            
                            // Set the time on our date object
                            existingAppointmentDate.setHours(hours, minutes, 0, 0);
                            
                            // Calculate duration and end time
                            let duration = appointment.total_duration || 0;
                            
                            // If no total_duration property and we have services, calculate it
                            if (!duration && appointment.services && appointment.services.length > 0) {
                                duration = appointment.services.reduce((total, service) => total + (service.duration || 0), 0);
                            }
                            
                            // Default to 60 minutes if no duration calculated
                            duration = duration || 60;
                            
                            const existingAppointmentEnd = new Date(existingAppointmentDate);
                            existingAppointmentEnd.setMinutes(existingAppointmentEnd.getMinutes() + duration);
                            
                            // Only log for debugging if dates are valid
                            if (!isNaN(existingAppointmentDate.getTime()) && 
                                !isNaN(existingAppointmentEnd.getTime()) &&
                                !isNaN(appointmentStart.getTime()) &&
                                !isNaN(appointmentEnd.getTime())) {
                                
                                console.log('Checking appointment:', {
                                    employeeId: employee.id,
                                    employeeName: employee.name,
                                    appointmentId: appointment.id,
                                    requestedStart: appointmentStart.toLocaleString(),
                                    requestedEnd: appointmentEnd.toLocaleString(),
                                    existingStart: existingAppointmentDate.toLocaleString(),
                                    existingEnd: existingAppointmentEnd.toLocaleString(),
                                    duration: duration
                                });
                            }
                            
                            // Check for overlap
                            return (
                                (appointmentStart >= existingAppointmentDate && appointmentStart < existingAppointmentEnd) ||
                                (appointmentEnd > existingAppointmentDate && appointmentEnd <= existingAppointmentEnd) ||
                                (appointmentStart <= existingAppointmentDate && appointmentEnd >= existingAppointmentEnd)
                            );
                        } catch (error) {
                            console.error('Error parsing appointment dates:', error);
                            return false;
                        }
                    });

                    return !hasTimeOff && !hasAppointment;
                });

                console.log('Available employees with ratings:', this.availableEmployees);

            } catch (error) {
                console.error('Error loading employees:', error);
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