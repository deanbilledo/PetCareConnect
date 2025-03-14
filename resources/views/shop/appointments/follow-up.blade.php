@extends('layouts.shop')

@section('title', 'Schedule Follow-up Appointment')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-2 mb-4">
            <a href="{{ route('shop.appointments.show', $appointment) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Appointment
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Schedule Follow-up Appointment</h1>
                
                <!-- Original Appointment Information -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Original Appointment Details</h3>
                            <div class="mt-2 text-sm text-gray-600">
                                <p><span class="font-medium">Customer:</span> {{ $appointment->user->name }}</p>
                                <p><span class="font-medium">Pet:</span> {{ $appointment->pet->name }} ({{ $appointment->pet->species }})</p>
                                <p><span class="font-medium">Service:</span> {{ $appointment->service_type }}</p>
                                <p><span class="font-medium">Date:</span> {{ $appointment->appointment_date->format('F j, Y g:i A') }}</p>
                                <p><span class="font-medium">Handled by:</span> {{ $appointment->employee->name ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('appointments.schedule-follow-up', $appointment) }}" id="followUpForm">
                    @csrf
                    <input type="hidden" name="pet_id" value="{{ $appointment->pet_id }}">
                    
                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Service Selection -->
                        <div>
                            <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                            <select id="service_type" name="service_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required x-data x-on:change="updateServiceDetails()">
                                <option value="">Select a service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->name }}" data-duration="{{ $service->duration }}" data-price="{{ $service->price }}" data-id="{{ $service->id }}">
                                        {{ $service->name }} - ₱{{ number_format($service->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="service_id" id="service_id" value="">
                            <input type="hidden" name="service_price" id="service_price" value="">
                            @error('service_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            <!-- Service Duration Info -->
                            <div class="mt-2 bg-blue-50 rounded-lg p-4 hidden" id="serviceDurationInfo">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Service Duration Information</h3>
                                        <p class="mt-1 text-sm text-blue-600" id="durationText"></p>
                                        <p class="mt-1 text-sm text-blue-600">Time slots are adjusted to ensure all services can be completed within the shop's operating hours.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date Selection -->
                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" id="appointment_date" name="appointment_date" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   max="{{ date('Y-m-d', strtotime('+2 months')) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   x-data x-on:change="getTimeSlots()">
                            <p class="mt-1 text-sm text-gray-500">Available dates for the next 2 months</p>
                            @error('appointment_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Time Slots -->
                    <div class="mt-6" id="timeSlotsContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Time Slots</label>
                        <div id="timeSlotsLoading" class="text-gray-500 text-sm mb-2 hidden">
                            <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading available time slots...
                        </div>
                        <div id="timeSlotsError" class="text-red-500 text-sm mb-2 hidden"></div>
                        <div id="noTimeSlotsMessage" class="text-yellow-600 text-sm p-4 bg-yellow-50 rounded-md hidden">
                            No available time slots for the selected date. Please choose another date.
                        </div>
                        
                        <!-- Time Slots Grid -->
                        <div id="timeSlotsGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 hidden">
                            <!-- Time slots will be added here dynamically -->
                        </div>
                        <input type="hidden" id="appointment_time" name="appointment_time" required>
                        @error('appointment_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Employee Selection -->
                    <div class="mt-6" id="employeesContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Employee</label>
                        <p class="text-sm text-gray-500 mb-2">Choose an available employee for your service</p>
                        
                        <div id="employeesLoading" class="text-gray-500 text-sm mb-2 hidden">
                            <svg class="animate-spin h-5 w-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading available employees...
                        </div>
                        
                        <div id="employeesError" class="text-red-500 text-sm mb-2 hidden"></div>
                        
                        <div id="employeesGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                            <!-- Employees will be added here dynamically -->
                        </div>
                        
                        <div id="noEmployeesMessage" class="text-yellow-600 text-sm p-4 bg-yellow-50 rounded-md hidden">
                            No employees available for the selected time slot. Please choose another time.
                        </div>
                        @error('employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Notes -->
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Any specific notes for this follow-up appointment...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" id="submitButton" disabled
                                class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Schedule Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let serviceDuration = 0;
    let originalEmployeeId = {{ $appointment->employee_id ?? 'null' }};
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for service selection changes
        document.getElementById('service_type').addEventListener('change', updateServiceDetails);
        
        // Listen for date changes
        document.getElementById('appointment_date').addEventListener('change', getTimeSlots);
    });
    
    function updateServiceDetails() {
        const serviceSelect = document.getElementById('service_type');
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const durationInfo = document.getElementById('serviceDurationInfo');
        const durationText = document.getElementById('durationText');
        const serviceIdInput = document.getElementById('service_id');
        const servicePriceInput = document.getElementById('service_price');
        
        // Reset dependent fields
        resetTimeSlots();
        resetEmployees();
        updateSubmitButton();
        
        if (serviceSelect.value) {
            serviceDuration = parseInt(selectedOption.getAttribute('data-duration') || 0);
            const servicePrice = parseFloat(selectedOption.getAttribute('data-price') || 0);
            const serviceId = selectedOption.getAttribute('data-id') || '';
            
            // Set the service_id hidden field
            serviceIdInput.value = serviceId;
            
            // Set the service_price hidden field
            servicePriceInput.value = servicePrice.toString();
            
            console.log('Selected service:', serviceSelect.value);
            console.log('Service ID:', serviceId);
            console.log('Service Price:', servicePrice);
            
            if (serviceDuration > 0) {
                // Format duration text
                const hours = Math.floor(serviceDuration / 60);
                const minutes = serviceDuration % 60;
                let durationString = '';
                
                if (hours > 0) {
                    durationString += hours + ' hour' + (hours > 1 ? 's' : '');
                }
                
                if (minutes > 0) {
                    if (hours > 0) durationString += ' ';
                    durationString += minutes + ' min';
                }
                
                durationText.textContent = `Duration: ${serviceDuration} minutes (${durationString})`;
                durationInfo.classList.remove('hidden');
            } else {
                durationInfo.classList.add('hidden');
            }
        } else {
            durationInfo.classList.add('hidden');
            serviceIdInput.value = '';
            servicePriceInput.value = '';
        }
    }
    
    async function getTimeSlots() {
        const dateInput = document.getElementById('appointment_date');
        const serviceSelect = document.getElementById('service_type');
        const serviceIdInput = document.getElementById('service_id');
        
        // Reset and hide time slots and employees
        resetTimeSlots();
        resetEmployees();
        updateSubmitButton();
        
        if (!dateInput.value || !serviceSelect.value) {
            return;
        }
        
        const timeSlotsLoading = document.getElementById('timeSlotsLoading');
        const timeSlotsError = document.getElementById('timeSlotsError');
        const noTimeSlotsMessage = document.getElementById('noTimeSlotsMessage');
        const timeSlotsGrid = document.getElementById('timeSlotsGrid');
        
        // Show loading state
        timeSlotsLoading.classList.remove('hidden');
        
        try {
            // Get the service ID from the hidden field
            const serviceId = serviceIdInput.value;
            
            // Fetch available time slots
            const response = await fetch(`/time-slots/shop/{{ $appointment->shop_id }}?date=${dateInput.value}&duration=${serviceDuration}&service_id=${serviceId || ''}&pet_id={{ $appointment->pet_id }}`, {
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
            
            const timeSlotsData = await response.json();
            
            if (timeSlotsData.error) {
                throw new Error(timeSlotsData.error);
            }
            
            // Ensure we have an array of time slots
            const timeSlotArray = Array.isArray(timeSlotsData) ? timeSlotsData : 
                                (timeSlotsData.slots || timeSlotsData.data || []);
            
            // Clear the grid
            timeSlotsGrid.innerHTML = '';
            
            if (timeSlotArray.length === 0) {
                noTimeSlotsMessage.classList.remove('hidden');
            } else {
                // Create time slot buttons
                for (const slot of timeSlotArray) {
                    // Calculate end time
                    const [time, period] = slot.time.split(' ');
                    const [hours, minutes] = time.split(':');
                    let startHour = parseInt(hours);
                    const startMinutes = parseInt(minutes);
                    
                    if (period === 'PM' && startHour < 12) startHour += 12;
                    if (period === 'AM' && startHour === 12) startHour = 0;
                    
                    const startDate = new Date();
                    startDate.setHours(startHour, startMinutes, 0, 0);
                    
                    const endDate = new Date(startDate.getTime() + serviceDuration * 60000);
                    const endTime = endDate.toLocaleTimeString('en-US', { 
                        hour: 'numeric', 
                        minute: '2-digit', 
                        hour12: true 
                    });
                    
                    // Create button for time slot
                    const timeSlotButton = document.createElement('button');
                    timeSlotButton.type = 'button';
                    timeSlotButton.dataset.time = slot.time;
                    timeSlotButton.className = 'border rounded-lg p-3 text-center transition-colors border-gray-200 hover:bg-gray-50';
                    timeSlotButton.innerHTML = `
                        <div class="font-medium">${slot.time} - ${endTime}</div>
                    `;
                    
                    // Add click event
                    timeSlotButton.addEventListener('click', function() {
                        // Remove selection from other buttons
                        const buttons = timeSlotsGrid.querySelectorAll('button');
                        buttons.forEach(btn => {
                            btn.classList.remove('bg-blue-50', 'border-blue-200', 'text-blue-700');
                            btn.classList.add('border-gray-200');
                        });
                        
                        // Mark this button as selected
                        this.classList.remove('border-gray-200');
                        this.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
                        
                        // Set the hidden input value
                        document.getElementById('appointment_time').value = this.dataset.time;
                        
                        // Load employees for selected time slot
                        getAvailableEmployees();
                    });
                    
                    timeSlotsGrid.appendChild(timeSlotButton);
                }
                
                timeSlotsGrid.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading time slots:', error);
            timeSlotsError.textContent = error.message || 'Failed to load time slots';
            timeSlotsError.classList.remove('hidden');
        } finally {
            timeSlotsLoading.classList.add('hidden');
        }
    }
    
    async function getAvailableEmployees() {
        const dateInput = document.getElementById('appointment_date');
        const timeInput = document.getElementById('appointment_time');
        const serviceSelect = document.getElementById('service_type');
        const serviceIdInput = document.getElementById('service_id');
        
        // Reset employees
        resetEmployees();
        updateSubmitButton();
        
        if (!dateInput.value || !timeInput.value || !serviceSelect.value) {
            return;
        }
        
        const employeesLoading = document.getElementById('employeesLoading');
        const employeesError = document.getElementById('employeesError');
        const employeesGrid = document.getElementById('employeesGrid');
        const noEmployeesMessage = document.getElementById('noEmployeesMessage');
        
        // Show loading state
        employeesLoading.classList.remove('hidden');
        
        try {
            // Get the service ID from the hidden field
            const serviceId = serviceIdInput.value;
            
            // Fetch available employees
            const response = await fetch(`/book/{{ $appointment->shop_id }}/available-employees`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    date: dateInput.value,
                    time: timeInput.value,
                    duration: serviceDuration,
                    include_ratings: true,
                    service_ids: serviceId ? [serviceId] : [],  // Include service_ids parameter
                    pet_ids: [{{ $appointment->pet_id }}],      // Include pet_id from the original appointment
                    services: serviceId ? { "{{ $appointment->pet_id }}": [serviceId] } : {} // Include services mapping
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
            
            // Clear the grid
            employeesGrid.innerHTML = '';
            
            const employees = data.employees || [];
            
            if (employees.length === 0) {
                noEmployeesMessage.classList.remove('hidden');
            } else {
                // Create employee cards
                employees.forEach(employee => {
                    const rating = employee.rating || 0;
                    const ratingsCount = employee.ratings_count || 0;
                    
                    const employeeCard = document.createElement('label');
                    employeeCard.className = 'relative flex items-start p-4 cursor-pointer bg-white border rounded-lg hover:bg-gray-50';
                    employeeCard.innerHTML = `
                        <div class="flex items-center h-5">
                            <input type="radio" 
                                   name="employee_id" 
                                   value="${employee.id}"
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                   ${employee.id === originalEmployeeId ? 'checked' : ''}
                                   required>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center">
                                <img src="${employee.profile_photo_url || '{{ asset('images/default-avatar.png') }}'}" 
                                     alt="${employee.name}"
                                     class="w-12 h-12 rounded-full object-cover mr-3">
                                <div class="flex flex-col flex-1">
                                    <span class="text-sm font-medium text-gray-900">${employee.name}</span>
                                    <span class="text-sm text-gray-500">${employee.position || ''}</span>
                                    
                                    <!-- Star Rating Display -->
                                    <div class="flex items-center mt-1">
                                        <!-- 5 stars display -->
                                        <div class="flex space-x-0.5">
                                            <svg class="w-4 h-4 ${rating >= 1 ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4 ${rating >= 2 ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4 ${rating >= 3 ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4 ${rating >= 4 ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4 ${rating >= 5 ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <span class="ml-1 text-sm text-gray-600">${rating > 0 ? `${Math.min(rating, 5).toFixed(1)} / 5.0 (${ratingsCount} ${ratingsCount === 1 ? 'rating' : 'ratings'})` : 'No ratings yet'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Add change event for the radio button
                    const radioButton = employeeCard.querySelector('input[type="radio"]');
                    radioButton.addEventListener('change', updateSubmitButton);
                    
                    employeesGrid.appendChild(employeeCard);
                });
                
                employeesGrid.classList.remove('hidden');
                updateSubmitButton();
            }
        } catch (error) {
            console.error('Error loading employees:', error);
            employeesError.textContent = error.message || 'Failed to load available employees';
            employeesError.classList.remove('hidden');
        } finally {
            employeesLoading.classList.add('hidden');
        }
    }
    
    function resetTimeSlots() {
        const timeSlotsError = document.getElementById('timeSlotsError');
        const noTimeSlotsMessage = document.getElementById('noTimeSlotsMessage');
        const timeSlotsGrid = document.getElementById('timeSlotsGrid');
        const appointmentTimeInput = document.getElementById('appointment_time');
        
        timeSlotsError.classList.add('hidden');
        noTimeSlotsMessage.classList.add('hidden');
        timeSlotsGrid.classList.add('hidden');
        timeSlotsGrid.innerHTML = '';
        appointmentTimeInput.value = '';
    }
    
    function resetEmployees() {
        const employeesError = document.getElementById('employeesError');
        const noEmployeesMessage = document.getElementById('noEmployeesMessage');
        const employeesGrid = document.getElementById('employeesGrid');
        
        employeesError.classList.add('hidden');
        noEmployeesMessage.classList.add('hidden');
        employeesGrid.classList.add('hidden');
        employeesGrid.innerHTML = '';
    }
    
    function updateSubmitButton() {
        const serviceSelect = document.getElementById('service_type');
        const dateInput = document.getElementById('appointment_date');
        const timeInput = document.getElementById('appointment_time');
        const employeeSelected = document.querySelector('input[name="employee_id"]:checked');
        const submitButton = document.getElementById('submitButton');
        
        if (serviceSelect.value && dateInput.value && timeInput.value && employeeSelected) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }
</script>
@endpush
@endsection 