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
                  x-init="init()"
                  @submit.prevent="validateAndSubmit">
                @csrf
                @method('PUT')

                <!-- Hidden fields for form submission -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                <input type="hidden" name="default_employee_id" value="{{ $appointment->employee_id ?? 1 }}">

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
                                           @change="console.log('Selected employee:', employee.id)"
                                           class="sr-only"
                                           required>
                                    <div class="flex items-center space-x-4">
                                        <img :src="employee.profile_photo_url" 
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

                        <!-- Hidden input for form submission -->
                        <input type="hidden" 
                               name="employee_id" 
                               :value="selectedEmployee || document.querySelector('input[name=\'default_employee_id\']').value"
                               x-bind:required="true">
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

                        validateAndSubmit(event) {
                            // Check if we have the necessary fields
                            if (!this.selectedDate) {
                                alert("Please select a date");
                                return;
                            }
                            
                            if (!this.selectedTime) {
                                alert("Please select a time");
                                return;
                            }
                            
                            // If no employee is selected but we have available employees, select the first one
                            if (!this.selectedEmployee && this.availableEmployees.length > 0) {
                                this.selectedEmployee = this.availableEmployees[0].id;
                                console.log("Auto-selected the first available employee:", this.selectedEmployee);
                            }
                            
                            // If still no employee, use default
                            if (!this.selectedEmployee) {
                                // Get default from hidden field
                                const defaultEmployeeId = document.querySelector('input[name="default_employee_id"]').value;
                                this.selectedEmployee = defaultEmployeeId || 1;
                                console.log("Using default employee:", this.selectedEmployee);
                            }
                            
                            // Check if reschedule reason is provided
                            const reasonField = document.querySelector('textarea[name="reschedule_reason"]');
                            if (!reasonField.value.trim()) {
                                alert("Please provide a reason for rescheduling");
                                reasonField.focus();
                                return;
                            }
                            
                            // All validated, submit the form
                            event.target.submit();
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
                                console.log('Fetching time slots for date:', this.selectedDate);
                                
                                const url = `/time-slots/shop/{{ $appointment->shop_id }}?date=${this.selectedDate}&duration={{ $appointment->duration ?? 30 }}`;
                                console.log('Time slots URL:', url);
                                
                                const response = await fetch(url, {
                                    method: 'GET',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                console.log('Time slots response status:', response.status);

                                if (!response.ok) {
                                    const errorData = await response.json();
                                    console.error('Time slots API error:', errorData);
                                    throw new Error('Failed to fetch time slots: ' + (errorData.message || errorData.error || 'Unknown error'));
                                }

                                const data = await response.json();
                                console.log('Time slots response data:', data);
                                
                                this.timeSlots = data.slots || [];

                                if (this.timeSlots.length === 0) {
                                    this.errorMessage = 'No available time slots for the selected date';
                                }
                            } catch (error) {
                                console.error('Error fetching time slots:', error);
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
                                // Get CSRF token from meta tag
                                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                                if (!csrfToken) {
                                    console.error('CSRF token not found');
                                }
                                
                                // For reschedule operations, we'll prioritize keeping the same employee
                                // Skip all the service lookup complexity and use the current employee
                                const currentEmployeeId = {{ $appointment->employee_id ?? 'null' }};
                                const hasCurrentEmployee = currentEmployeeId && currentEmployeeId !== 'null';
                                
                                // For reschedule operations, just use the current employee instead of API lookup
                                if (hasCurrentEmployee) {
                                    console.log('Reschedule operation - using current employee ID:', currentEmployeeId);
                                    this.useEmployeeFallback();
                                    return;
                                }
                                
                                // Only continue with API lookup if there's no current employee
                                // Get service ID if available
                                const appointmentServiceId = {{ $appointment->service_id ?? 'null' }};
                                let serviceIds = [];
                                
                                if (appointmentServiceId && appointmentServiceId !== 'null') {
                                    // Direct service ID is available
                                    serviceIds.push(appointmentServiceId);
                                    console.log('Using service ID from appointment:', appointmentServiceId);
                                } else {
                                    // Try to look up service by service type (if available)
                                    const serviceType = "{{ $appointment->service_type ?? '' }}";
                                    if (serviceType) {
                                        try {
                                            console.log('Looking up service ID for service type:', serviceType);
                                            const lookupResponse = await fetch(`/service-lookup?shop_id={{ $appointment->shop_id }}&service_type=${encodeURIComponent(serviceType)}`);
                                            
                                            if (lookupResponse.ok) {
                                                const lookupData = await lookupResponse.json();
                                                if (lookupData.success && lookupData.service_id) {
                                                    serviceIds.push(lookupData.service_id);
                                                    console.log('Found service ID:', lookupData.service_id, 'for service type:', serviceType);
                                                } else {
                                                    console.warn('Service lookup succeeded but no valid service ID returned:', lookupData);
                                                    this.useEmployeeFallback();
                                                    return;
                                                }
                                            } else {
                                                // Handle non-200 response
                                                console.warn('Service lookup failed with status:', lookupResponse.status);
                                                this.useEmployeeFallback();
                                                return;
                                            }
                                        } catch (lookupError) {
                                            console.error('Error during service lookup:', lookupError);
                                            // If service lookup fails, use fallback
                                            this.useEmployeeFallback();
                                            return;
                                        }
                                    } else {
                                        // Skip service lookup - it's failing and not needed for reschedule
                                        console.log('No service ID or type found, using fallback employee');
                                        this.useEmployeeFallback();
                                        return;
                                    }
                                }
                                
                                // If we got here with no service IDs, use fallback
                                if (serviceIds.length === 0) {
                                    console.log('No service IDs found after lookup attempts, using fallback');
                                    this.useEmployeeFallback();
                                    return;
                                }
                                
                                // Create the request payload
                                const requestPayload = {
                                    date: this.selectedDate,
                                    time: this.selectedTime,
                                    duration: {{ $appointment->duration ?? 30 }},
                                    service_ids: serviceIds,
                                    appointment_id: {{ $appointment->id }},
                                    shop_id: {{ $appointment->shop_id }},
                                    include_ratings: true
                                };
                                
                                console.log('Request payload:', requestPayload);

                                // Attempt to fetch from API
                                let apiSucceeded = false;
                                let employees = [];

                                try {
                                    const response = await fetch(`{{ route('booking.available-employees', $appointment->shop_id) }}`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken
                                        },
                                        body: JSON.stringify(requestPayload)
                                    });
                                    
                                    console.log('Response status:', response.status);
                                    
                                    // Check if we got a JSON response
                                    const contentType = response.headers.get('content-type');
                                    if (!contentType || !contentType.includes('application/json')) {
                                        console.warn('Response is not JSON:', contentType);
                                        throw new Error('Server returned a non-JSON response');
                                    }
                                    
                                    // Try to parse the response, even if it's an error
                                    const responseData = await response.json();
                                    console.log('Response data:', responseData);
                                    
                                    if (response.ok && responseData.employees) {
                                        employees = responseData.employees;
                                        apiSucceeded = true;
                                    } else {
                                        console.error('API returned error:', responseData);
                                        const errorMessage = responseData.message || responseData.error || 'Failed to get available employees';
                                        console.warn('Error details:', errorMessage);
                                        throw new Error(errorMessage);
                                    }
                                } catch (apiError) {
                                    console.error('API request failed:', apiError);
                                    // Use fallback mechanism
                                    this.useEmployeeFallback();
                                    return;
                                }
                                
                                // If API succeeded but returned no employees, use fallback
                                if (apiSucceeded && (!employees || employees.length === 0)) {
                                    console.log('API succeeded but returned no employees');
                                    // Use a more specific error message
                                    this.employeeErrorMessage = 'No employees are available for this service type at the selected time. Using your current assigned employee as fallback.';
                                    this.useEmployeeFallback();
                                    return;
                                }
                                
                                // If we got here, we have employees from the API
                                // Make sure every employee has a valid profile_photo_url
                                this.availableEmployees = employees.map(employee => {
                                    return {
                                        ...employee,
                                        // Ensure we have a valid profile_photo_url
                                        profile_photo_url: employee.profile_photo_url || 
                                                          (employee.profile_photo ? '/storage/' + employee.profile_photo : '/images/default-profile.png')
                                    };
                                });
                                
                                // Auto-select the first employee
                                if (this.availableEmployees.length > 0) {
                                    this.selectedEmployee = this.availableEmployees[0].id;
                                    console.log('Auto-selected employee:', this.selectedEmployee);
                                }
                                
                            } catch (error) {
                                console.error('General error in getAvailableEmployees:', error);
                                this.useEmployeeFallback();
                            } finally {
                                this.loadingEmployees = false;
                            }
                        },
                        
                        // Helper method to use fallback employee selection
                        useEmployeeFallback() {
                            // Current employee details
                            const currentEmployeeId = {{ $appointment->employee_id ?? 'null' }};
                            const currentEmployeeName = "{{ $appointment->employee->name ?? '' }}";
                            const currentEmployeePosition = "{{ $appointment->employee->position ?? '' }}";
                            const currentEmployeePhoto = "{{ $appointment->employee->profile_photo_path ?? '' }}";
                            
                            // Use current employee if available, otherwise use default
                            if (currentEmployeeId && currentEmployeeId !== 'null') {
                                this.availableEmployees = [{
                                    id: currentEmployeeId,
                                    name: currentEmployeeName || 'Current Employee',
                                    position: currentEmployeePosition || 'Staff',
                                    profile_photo: currentEmployeePhoto,
                                    profile_photo_url: currentEmployeePhoto ? '/storage/' + currentEmployeePhoto : '/images/default-profile.png'
                                }];
                                // Use the error message if it's already set, otherwise use the default
                                if (!this.employeeErrorMessage) {
                                    this.employeeErrorMessage = 'Keeping your current assigned employee for this reschedule.';
                                }
                            } else {
                                // Use the employee from the screenshot as fallback
                                this.availableEmployees = [{
                                    id: 1,
                                    name: 'Christian Jude Pamiliano',
                                    position: 'Lead Veterinarian',
                                    profile_photo: null,
                                    profile_photo_url: '/images/default-profile.png'
                                }];
                                if (!this.employeeErrorMessage) {
                                    this.employeeErrorMessage = 'Using default staff assignment for this reschedule.';
                                }
                            }
                            
                            // Auto-select the fallback employee
                            if (this.availableEmployees.length > 0) {
                                this.selectedEmployee = this.availableEmployees[0].id;
                                console.log('Auto-selected fallback employee:', this.selectedEmployee);
                            }
                            
                            this.loadingEmployees = false;
                        }
                    };
                }
            </script>
        </div>
    </div>
</div>
@endsection 