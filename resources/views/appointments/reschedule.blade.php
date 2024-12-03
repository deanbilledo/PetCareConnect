@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>    
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto">
        <!-- Current Appointment Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Reschedule Appointment</h2>
                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    Current Schedule
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium">{{ $appointment->appointment_date->format('l, F j, Y') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-500">Time</p>
                        <p class="font-medium">{{ $appointment->appointment_date->format('g:i A') }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-500">Service</p>
                        <p class="font-medium">{{ $appointment->service_type }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule Form -->
        <div class="bg-white rounded-lg shadow-md p-6"
             x-data="{ 
                ...timeSlotPicker(),
                showCalendar: false,
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                maxYear: new Date().getFullYear() + (new Date().getMonth() + 2 >= 12 ? 1 : 0),
                maxMonth: (new Date().getMonth() + 2) % 12,
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                
                // Service price data
                petSize: '{{ strtolower($appointment->pet->size_category ?? 'small') }}',
                basePrice: {{ $appointment->service->base_price ?? 0 }},
                price: {{ $appointment->service ? $appointment->service->getPriceForSize($appointment->pet->size_category ?? 'small') : 0 }},
                
                // Calendar functions
                get calendarDays() {
                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                    const days = [];
                    
                    // Add empty days for padding
                    for (let i = 0; i < firstDay.getDay(); i++) {
                        days.push(null);
                    }
                    
                    // Add month days
                    for (let i = 1; i <= lastDay.getDate(); i++) {
                        days.push(new Date(this.currentYear, this.currentMonth, i));
                    }
                    
                    return days;
                },
                
                isDateSelectable(date) {
                    if (!date) return false;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const maxDate = new Date();
                    maxDate.setMonth(maxDate.getMonth() + 2);
                    return date > today && date <= maxDate;
                },
                
                isSelectedDate(date) {
                    if (!date || !this.selectedDate) return false;
                    const selected = new Date(this.selectedDate);
                    return date.toDateString() === selected.toDateString();
                },
                
                selectDate(date) {
                    if (!this.isDateSelectable(date)) return;
                    this.selectedDate = date.toISOString().split('T')[0];
                    this.showCalendar = false;
                }
             }">

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">New Schedule Details</h3>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Please note that rescheduling is subject to availability. The shop will be notified of your request.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('appointments.update-schedule', $appointment) }}" 
                      method="POST" 
                      id="rescheduleForm"
                      onsubmit="return validateForm()"
                      class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                            <div class="relative">
                                <!-- Calendar Trigger Button -->
                                <button type="button"
                                        @click="showCalendar = !showCalendar"
                                        class="w-full bg-white px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex items-center justify-between">
                                    <span x-text="selectedDate ? new Date(selectedDate).toLocaleDateString('en-US', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) : 'Select a date'"></span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </button>

                                <!-- Hidden Date Input -->
                                <input type="date" 
                                       x-model="selectedDate"
                                       name="new_date"
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       max="{{ now()->addMonths(2)->format('Y-m-d') }}"
                                       class="hidden"
                                       required>

                                <!-- Calendar Dropdown -->
                                <div x-show="showCalendar"
                                     @click.away="showCalendar = false"
                                     class="absolute z-10 mt-1 w-full bg-white rounded-lg shadow-lg p-4"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95">
                                    
                                    <!-- Month Navigation -->
                                    <div class="flex items-center justify-between mb-4">
                                        <button type="button"
                                                @click="currentMonth--"
                                                :disabled="currentMonth <= new Date().getMonth() && currentYear <= new Date().getFullYear()"
                                                :class="{'opacity-50 cursor-not-allowed': currentMonth <= new Date().getMonth() && currentYear <= new Date().getFullYear()}"
                                                class="p-1 hover:bg-gray-100 rounded-full">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <span class="text-lg font-semibold" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                        <button type="button"
                                                @click="currentMonth++"
                                                :disabled="currentYear >= maxYear && currentMonth >= maxMonth"
                                                :class="{'opacity-50 cursor-not-allowed': currentYear >= maxYear && currentMonth >= maxMonth}"
                                                class="p-1 hover:bg-gray-100 rounded-full">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Weekday Headers -->
                                    <div class="grid grid-cols-7 gap-1 mb-2">
                                        <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                                            <div class="text-center text-sm font-medium text-gray-500 py-1" x-text="day"></div>
                                        </template>
                                    </div>

                                    <!-- Calendar Days -->
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="day in calendarDays">
                                            <button type="button"
                                                    @click="selectDate(day)"
                                                    :disabled="!isDateSelectable(day)"
                                                    :class="{
                                                        'bg-blue-50 text-blue-600 font-semibold': isSelectedDate(day),
                                                        'hover:bg-gray-50': isDateSelectable(day),
                                                        'opacity-50 cursor-not-allowed': !isDateSelectable(day),
                                                        'text-gray-900': isDateSelectable(day),
                                                        'text-gray-400': !isDateSelectable(day)
                                                    }"
                                                    class="text-center py-2 rounded-full">
                                                <span x-text="day.getDate()"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
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
                                
                                <!-- Time Slots Dropdown -->
                                <select name="new_time" 
                                        x-model="selectedTime"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        x-show="!loading && timeSlots.length > 0"
                                        required>
                                    <option value="">Select a time</option>
                                    <template x-for="slot in timeSlots" :key="slot">
                                        <option x-text="slot" :value="slot"></option>
                                    </template>
                                </select>
                                
                                <!-- No Slots Available Message -->
                                <div x-show="!loading && !errorMessage && selectedDate && timeSlots.length === 0" 
                                     class="text-red-500 text-sm mt-1">
                                    No available time slots for the selected date. Please choose another date.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                        <div class="relative">
                            <select name="service_type" 
                                    x-data="{ 
                                        price: {{ $appointment->service_price ?? 'null' }},
                                        petSize: '{{ $appointment->pet->size_category }}',
                                        calculatePrice() {
                                            const option = this.$el.querySelector('option:checked');
                                            const basePrice = option.dataset.price;
                                            let finalPrice = parseFloat(basePrice);

                                            // Apply size multiplier
                                            switch(this.petSize) {
                                                case 'large':
                                                    finalPrice *= 1.4; // 40% more for large pets
                                                    break;
                                                case 'medium':
                                                    finalPrice *= 1.2; // 20% more for medium pets
                                                    break;
                                                // small pets use base price
                                            }

                                            this.price = finalPrice;
                                            this.$refs.servicePrice.value = finalPrice;
                                        }
                                    }"
                                    x-init="calculatePrice()"
                                    @change="calculatePrice()"
                                    class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @foreach($services as $service)
                                    <option value="{{ $service->name }}" 
                                            data-price="{{ $service->price }}"
                                            {{ $appointment->service_type === $service->name ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Hidden input for service price -->
                            <input type="hidden" 
                                   name="service_price" 
                                   x-ref="servicePrice"
                                   :value="price">
                               
                            <!-- Price display with size info -->
                            <div class="mt-2 space-y-1">
                                <!-- Pet Size Info -->
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">Pet Size:</span>
                                    <span class="text-gray-600">{{ ucfirst($appointment->pet->size_category ?? 'small') }}</span>
                                </div>
                                
                                <!-- Base Price -->
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">Base Price:</span>
                                    <span class="text-gray-600" x-text="'₱' + Number(basePrice).toLocaleString('en-US', { minimumFractionDigits: 2 })"></span>
                                </div>

                                <!-- Size Multiplier -->
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">Size Adjustment:</span>
                                    <span class="text-gray-600" x-text="petSize === 'large' ? '+40%' : (petSize === 'medium' ? '+20%' : 'No adjustment')"></span>
                                </div>

                                <!-- Final Price -->
                                <div class="flex justify-between text-sm border-t pt-2 mt-2">
                                    <span class="font-medium text-gray-700">Final Price:</span>
                                    <span class="text-blue-600 font-semibold" 
                                          x-text="'₱' + Number(price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                    </span>
                                </div>

                                <!-- Price Explanation -->
                                <p class="text-xs text-gray-500 mt-2">
                                    *Price is automatically calculated based on:
                                    <br>• Base service price
                                    <br>• Pet size adjustments: Small (base price), Medium (+20%), Large (+40%)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Reason for Rescheduling -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Rescheduling
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <textarea name="reschedule_reason" 
                                    rows="3" 
                                    class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Please provide a reason for rescheduling your appointment..."
                                    required></textarea>
                            <div class="absolute top-0 right-0 mt-2 mr-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Confirm Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
function timeSlotPicker() {
    return {
        selectedDate: '',
        selectedTime: '',
        timeSlots: [],
        loading: false,
        errorMessage: '',
        
        async getTimeSlots() {
            if (!this.selectedDate) {
                this.timeSlots = [];
                this.selectedTime = '';
                return;
            }
            
            this.loading = true;
            this.timeSlots = [];
            this.selectedTime = '';
            this.errorMessage = '';
            
            try {
                const response = await fetch(`/time-slots/shop/{{ $appointment->shop_id }}?date=${this.selectedDate}&duration=60`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`Server error: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }

                this.timeSlots = data.slots || [];
                
                if (this.timeSlots.length === 0) {
                    this.errorMessage = 'No available time slots for the selected date';
                }
            } catch (error) {
                console.error('Error fetching time slots:', error);
                this.errorMessage = 'Failed to load available time slots. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        init() {
            this.$watch('selectedDate', () => {
                this.getTimeSlots();
            });
        }
    }
}

function validateForm() {
    const form = document.getElementById('rescheduleForm');
    const date = form.querySelector('input[name="new_date"]').value;
    const time = form.querySelector('select[name="new_time"]').value;
    
    if (!date || !time) {
        alert('Please select both date and time');
        return false;
    }
    
    return true;
}
</script>
@endpush 