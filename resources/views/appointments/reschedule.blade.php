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
                    </div>

                    <!-- New Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            New Time
                            <span class="text-red-500">*</span>
                            <span class="text-sm text-gray-500 font-normal">
                                ({{ $appointment->service->duration ?? 30 }} mins duration)
                            </span>
                        </label>
                        <div class="relative">
                            <select name="new_time" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    required
                                    x-show="timeSlots.length > 0"
                                    x-model="selectedTime">
                                <template x-for="slot in timeSlots" :key="slot.start">
                                    <option :value="slot.start" x-text="slot.display"></option>
                                </template>
                            </select>
                            <p x-show="selectedDate && timeSlots.length === 0" 
                               class="text-red-500 text-sm mt-1">
                                No available time slots for the selected date. Please choose another date.
                            </p>
                        </div>
                    </div>

                    <!-- Service Information (Read-only) -->
                    <div class="md:col-span-2">
                        <h3 class="block text-sm font-medium text-gray-700 mb-3">Service Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">{{ $appointment->service_type }}</span>
                                </div>
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium">₱{{ number_format($appointment->service_price, 2) }}</span>
                                </div>
                            </div>
                            <input type="hidden" name="service_type" value="{{ $appointment->service_type }}">
                        </div>
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
                        Confirm Reschedule
                    </button>
                </div>
            </form>

            <script>
                function rescheduleForm() {
                    return {
                        selectedDate: '',
                        selectedTime: '',
                        timeSlots: [],
                        operatingHours: @json($operatingHours),
                        serviceDuration: {{ $appointment->service->duration ?? 30 }},

                        init() {
                            // Initialize any needed data
                            this.selectedDate = '';
                            this.selectedTime = '';
                            this.timeSlots = [];
                        },

                        updateTimeSlots() {
                            if (!this.selectedDate) {
                                this.timeSlots = [];
                                return;
                            }

                            const date = new Date(this.selectedDate);
                            const dayOfWeek = date.getDay();
                            const hours = this.operatingHours[dayOfWeek];

                            if (!hours || !hours.is_open) {
                                this.timeSlots = [];
                                return;
                            }

                            const openTime = new Date(`2000-01-01 ${hours.open_time}`);
                            const closeTime = new Date(`2000-01-01 ${hours.close_time}`);
                            const slots = [];

                            // Subtract service duration from closing time
                            closeTime.setMinutes(closeTime.getMinutes() - this.serviceDuration);

                            let currentTime = openTime;
                            while (currentTime <= closeTime) {
                                // Format the time slot
                                const timeSlot = currentTime.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit', 
                                    hour12: true 
                                });

                                // Calculate end time for display
                                const endTime = new Date(currentTime);
                                endTime.setMinutes(endTime.getMinutes() + this.serviceDuration);
                                const endTimeString = endTime.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });

                                // Add the time slot with duration
                                slots.push({
                                    start: timeSlot,
                                    end: endTimeString,
                                    display: `${timeSlot} - ${endTimeString} (${this.serviceDuration} mins)`
                                });

                                // Increment by 30-minute intervals
                                currentTime.setMinutes(currentTime.getMinutes() + 30);
                            }

                            this.timeSlots = slots;
                        }
                    }
                }
            </script>
        </div>
    </div>
</div>
@endsection 