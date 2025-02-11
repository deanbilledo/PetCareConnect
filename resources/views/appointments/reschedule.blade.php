@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6 mt-10">
        <a href="{{ route('appointments.show', $appointment) }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-800 rounded-lg px-4 py-2 shadow-sm transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointment
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- Current Appointment Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Reschedule Appointment</h2>
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($appointment->status === 'pending') bg-blue-100 text-blue-800
                    @else bg-green-100 text-green-800
                    @endif">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-lg mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Current Schedule</h3>
                    <div class="space-y-2">
                        <p class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $appointment->appointment_date->format('l, F j, Y') }}
                        </p>
                        <p class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $appointment->appointment_date->format('g:i A') }}
                        </p>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Service Details</h3>
                    <div class="space-y-2">
                        <p class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $appointment->service_type }}
                        </p>
                        <p class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            PHP {{ number_format($appointment->service_price, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            @if($appointment->status === 'accepted')
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                You can reschedule accepted appointments up to 2 times per week.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Reschedule Form -->
            <form action="{{ route('appointments.update-schedule', $appointment) }}" 
                  method="POST" 
                  class="space-y-6"
                  x-data="rescheduleForm()" 
                  x-init="init()">
                @csrf
                @method('PUT')

                <!-- Hidden fields for form submission -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                <!-- Form content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            New Date
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" 
                                   name="new_date" 
                                   x-model="selectedDate"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   @change="updateTimeSlots()"
                                   required>
                        </div>
                        @error('new_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            New Time
                            <span class="text-red-500">*</span>
                            <span class="text-sm text-gray-500 font-normal">
                                ({{ $appointment->duration }} mins duration)
                            </span>
                        </label>
                        <div class="relative">
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

                            <!-- Time Slots Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" x-show="!loading && timeSlots.length > 0">
                                <template x-for="slot in timeSlots" :key="slot.time">
                                    <button type="button"
                                            @click="selectedTime = slot.time; getAvailableEmployees()"
                                            :class="{
                                                'w-full p-3 rounded-lg border text-center transition-colors': true,
                                                'bg-blue-50 border-blue-300 text-blue-700': selectedTime === slot.time,
                                                'border-gray-200 hover:bg-gray-50': selectedTime !== slot.time
                                            }">
                                        <div class="text-sm font-medium" x-text="slot.time"></div>
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

                            <!-- Hidden input for form submission -->
                            <input type="hidden" 
                                   name="new_time" 
                                   :value="selectedTime" 
                                   required>

                            <!-- No Slots Message -->
                            <p x-show="!loading && selectedDate && timeSlots.length === 0" 
                               class="text-yellow-600 text-sm mt-1">
                                No available time slots for the selected date. Please choose another date.
                            </p>
                        </div>
                        @error('new_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employee Selection -->
                    <div class="md:col-span-2" x-show="selectedTime && !loading">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Employee
                            <span class="text-red-500">*</span>
                        </label>

                        <!-- Employee Loading State -->
                        <div x-show="loadingEmployees" class="flex items-center justify-center p-6 border rounded-lg">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-500">Loading available employees...</span>
                        </div>

                        <!-- Employee Error Message -->
                        <div x-show="employeeErrorMessage" 
                             class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="text-red-700" x-text="employeeErrorMessage"></span>
                            </div>
                        </div>

                        <!-- Employee Selection Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-3" x-show="!loadingEmployees && availableEmployees.length > 0">
                            <template x-for="employee in availableEmployees" :key="employee.id">
                                <label :class="{
                                    'relative flex items-center p-4 cursor-pointer border rounded-lg transition-all duration-200': true,
                                    'bg-blue-50 border-blue-300 ring-2 ring-blue-500': selectedEmployee == employee.id,
                                    'bg-white border-gray-200 hover:bg-gray-50': selectedEmployee != employee.id
                                }">
                                    <input type="radio" 
                                           name="employee_id" 
                                           :value="employee.id"
                                           x-model="selectedEmployee"
                                           class="sr-only"
                                           required>
                                    <div class="flex items-center space-x-4">
                                        <img :src="employee.profile_photo ? '/storage/' + employee.profile_photo : '/images/default-avatar.png'" 
                                             :alt="employee.name"
                                             class="w-12 h-12 rounded-full object-cover">
                                        <div>
                                            <p class="text-sm font-medium" 
                                               :class="{ 'text-blue-700': selectedEmployee == employee.id, 'text-gray-900': selectedEmployee != employee.id }"
                                               x-text="employee.name"></p>
                                            <p class="text-sm" 
                                               :class="{ 'text-blue-600': selectedEmployee == employee.id, 'text-gray-500': selectedEmployee != employee.id }"
                                               x-text="employee.position"></p>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- No Available Employees Message -->
                        <div x-show="!loadingEmployees && availableEmployees.length === 0" 
                             class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mt-3">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="text-yellow-700">No employees available for the selected time slot. Please choose another time.</span>
                            </div>
                        </div>

                        <input type="hidden" 
                               name="employee_id" 
                               :value="selectedEmployee" 
                               required>
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reason for Rescheduling -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Rescheduling
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reschedule_reason" 
                                  rows="3" 
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Please provide a reason for rescheduling your appointment..."
                                  required></textarea>
                        @error('reschedule_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('appointments.show', $appointment) }}"
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Submit Reschedule Request
                    </button>
                </div>
            </form>

            <script>
                function rescheduleForm() {
                    return {
                        selectedDate: '',
                        selectedTime: '',
                        timeSlots: [],
                        loading: false,
                        errorMessage: '',
                        availableEmployees: [],
                        selectedEmployee: null,
                        loadingEmployees: false,
                        employeeErrorMessage: '',

                        init() {
                            // Initialize any needed data
                            this.selectedDate = '';
                            this.selectedTime = '';
                            this.timeSlots = [];
                            this.availableEmployees = [];
                            this.selectedEmployee = null;
                        },

                        async updateTimeSlots() {
                            if (!this.selectedDate) {
                                this.timeSlots = [];
                                return;
                            }

                            this.loading = true;
                            this.timeSlots = [];
                            this.errorMessage = '';
                            this.availableEmployees = [];
                            this.selectedEmployee = null;

                            try {
                                const response = await fetch(`/time-slots/shop/{{ $appointment->shop_id }}?date=${this.selectedDate}&duration={{ $appointment->service->duration ?? 30 }}`, {
                                    method: 'GET',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Failed to fetch time slots');
                                }

                                const data = await response.json();
                                this.timeSlots = data.slots || [];

                                if (this.timeSlots.length === 0) {
                                    this.errorMessage = 'No available time slots for the selected date';
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                this.errorMessage = error.message;
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
                                const response = await fetch(`{{ route('booking.available-employees', $appointment->shop_id) }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        date: this.selectedDate,
                                        time: this.selectedTime,
                                        duration: {{ $appointment->service->duration ?? 30 }},
                                        service_ids: [{{ $appointment->service->id ?? 1 }}]
                                    })
                                });

                                if (!response.ok) {
                                    throw new Error('Failed to fetch available employees');
                                }

                                const data = await response.json();
                                this.availableEmployees = data.employees || [];

                                if (this.availableEmployees.length === 0) {
                                    this.employeeErrorMessage = 'No employees available for the selected time slot';
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                this.employeeErrorMessage = error.message;
                            } finally {
                                this.loadingEmployees = false;
                            }
                        }
                    };
                }
            </script>
        </div>
    </div>
</div>
@endsection 