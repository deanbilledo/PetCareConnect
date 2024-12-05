@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('appointments.show', $appointment) }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointment
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Reschedule Appointment</h2>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Current Appointment</h3>
            <p class="text-gray-600">Date: {{ $appointment->appointment_date->format('l, F j, Y') }}</p>
            <p class="text-gray-600">Time: {{ $appointment->appointment_date->format('g:i A') }}</p>
            <p class="text-gray-600">Service: {{ $appointment->service_type }}</p>
        </div>

        <form action="{{ route('appointments.update-schedule', $appointment) }}" 
              method="POST" 
              class="space-y-6"
              x-data="{ 
                selectedDate: '', 
                selectedDay: null,
                operatingHours: @json($operatingHours),
                timeSlots: [],
                initTimeSlots() {
                    if (!this.selectedDate) return;
                    const date = new Date(this.selectedDate);
                    this.selectedDay = date.getDay();
                    const hours = this.operatingHours[this.selectedDay];
                    
                    if (!hours || !hours.is_open) {
                        this.timeSlots = [];
                        return;
                    }

                    const openTime = new Date(`2000-01-01 ${hours.open_time}`);
                    const closeTime = new Date(`2000-01-01 ${hours.close_time}`);
                    const slots = [];
                    
                    let currentTime = openTime;
                    while (currentTime <= closeTime) {
                        slots.push(currentTime.toLocaleTimeString('en-US', { 
                            hour: '2-digit', 
                            minute: '2-digit', 
                            hour12: true 
                        }));
                        currentTime.setMinutes(currentTime.getMinutes() + 30);
                    }
                    
                    this.timeSlots = slots;
                }
              }"
              @change="initTimeSlots()">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                <input type="date" 
                       name="new_date" 
                       x-model="selectedDate"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
                <select name="new_time" 
                        class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        required
                        x-show="timeSlots.length > 0">
                    <template x-for="slot in timeSlots" :key="slot">
                        <option x-text="slot" :value="slot"></option>
                    </template>
                </select>
                <p x-show="selectedDate && timeSlots.length === 0" 
                   class="text-red-500 text-sm mt-1">
                    No available time slots for the selected date. Please choose another date.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <select name="service_type" 
                        class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        required>
                    @foreach($services as $service)
                        <option value="{{ $service->name }}" 
                                {{ $appointment->service_type === $service->name ? 'selected' : '' }}>
                            {{ $service->name }} - ₱{{ number_format($service->price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rescheduling</label>
                <textarea name="reschedule_reason" 
                          rows="3" 
                          class="w-full px-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                          required></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Confirm Reschedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 