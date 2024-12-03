@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div x-data="{
    showCancelModal: false,
    appointmentToCancel: null,
    currentFilter: 'all',
    dateFilter: '',
    showFilters: false,
    
    viewAppointmentDetails(id, event) {
        if (event.target.tagName === 'BUTTON' || event.target.closest('button')) {
            return;
        }
        window.location.href = `/appointments/${id}`;
    },
    
    async handleCancel() {
        const reason = this.$refs.cancelReason.value;
        try {
            const token = document.querySelector('meta[name=csrf-token]').content;
            
            console.log('Cancelling appointment:', this.appointmentToCancel);
            console.log('Reason:', reason);
            console.log('CSRF Token:', token);

            const response = await fetch(`/appointments/${this.appointmentToCancel}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    _token: token,
                    reason: reason
                })
            });

            console.log('Raw response:', response);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Failed to cancel appointment');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while cancelling the appointment');
        }
        
        this.showCancelModal = false;
    },

    isAppointmentVisible(status, date) {
        if (this.currentFilter !== 'all') {
            if (this.currentFilter === 'upcoming' && status !== 'pending') return false;
            if (this.currentFilter !== 'upcoming' && status !== this.currentFilter) return false;
        }
        
        if (this.dateFilter) {
            const appointmentDate = new Date(date).toISOString().split('T')[0];
            if (appointmentDate !== this.dateFilter) return false;
        }
        
        return true;
    }
}" class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6 mt-8">
        <h1 class="text-2xl font-bold">My Appointments</h1>
        
        <!-- Filter Toggle Button -->
        <button @click="showFilters = !showFilters"
                class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
        </button>
    </div>

    <!-- Enhanced Filters Section -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-white rounded-lg shadow-md p-6 mb-6">
        
        <!-- Status Filters -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Appointment Status</h3>
            <div class="flex flex-wrap gap-3">
                <button @click="currentFilter = 'all'" 
                        :class="{
                            'bg-blue-500 text-white ring-2 ring-blue-300': currentFilter === 'all',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'all'
                        }"
                        class="px-4 py-2 rounded-full transition-all duration-200 focus:outline-none">
                    <span class="flex items-center gap-2">
                        <svg x-show="currentFilter === 'all'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        All
                    </span>
                </button>
                <button @click="currentFilter = 'upcoming'"
                        :class="{
                            'bg-blue-500 text-white ring-2 ring-blue-300': currentFilter === 'upcoming',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'upcoming'
                        }"
                        class="px-4 py-2 rounded-full transition-all duration-200 focus:outline-none">
                    <span class="flex items-center gap-2">
                        <svg x-show="currentFilter === 'upcoming'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Upcoming
                    </span>
                </button>
                <button @click="currentFilter = 'completed'"
                        :class="{
                            'bg-blue-500 text-white ring-2 ring-blue-300': currentFilter === 'completed',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'completed'
                        }"
                        class="px-4 py-2 rounded-full transition-all duration-200 focus:outline-none">
                    <span class="flex items-center gap-2">
                        <svg x-show="currentFilter === 'completed'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Completed
                    </span>
                </button>
                <button @click="currentFilter = 'cancelled'"
                        :class="{
                            'bg-blue-500 text-white ring-2 ring-blue-300': currentFilter === 'cancelled',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'cancelled'
                        }"
                        class="px-4 py-2 rounded-full transition-all duration-200 focus:outline-none">
                    <span class="flex items-center gap-2">
                        <svg x-show="currentFilter === 'cancelled'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Cancelled
                    </span>
                </button>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Date</label>
                <div class="relative">
                    <input type="date" 
                           x-model="dateFilter"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <div x-show="dateFilter" 
                         @click="dateFilter = ''"
                         class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="dateFilter || currentFilter !== 'all'" 
                      class="text-sm text-gray-600">
                    Active Filters: 
                    <span x-show="currentFilter !== 'all'" 
                          class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        Status: <span class="font-medium ml-1" x-text="currentFilter"></span>
                    </span>
                    <span x-show="dateFilter" 
                          class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        Date: <span class="font-medium ml-1" x-text="dateFilter"></span>
                    </span>
                </span>
                <button x-show="dateFilter || currentFilter !== 'all'"
                        @click="dateFilter = ''; currentFilter = 'all'"
                        class="text-sm text-red-600 hover:text-red-800">
                    Clear All
                </button>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="space-y-6">
        @forelse($groupedAppointments as $date => $dayAppointments)
            <div x-show="dateFilter === '' || dateFilter === '{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}'"
                 class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Date Header -->
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                    </h2>
                </div>

                <!-- Appointments Cards -->
                <div class="divide-y divide-gray-200">
                    @foreach($dayAppointments as $appointment)
                        <div x-show="isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}')"
                             @click="viewAppointmentDetails({{ $appointment->id }}, $event)"
                             class="p-6 hover:bg-gray-50 cursor-pointer transition-all">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Shop Info -->
                                <div class="flex items-center flex-1">
                                    <img class="h-16 w-16 rounded-lg object-cover shadow-sm"
                                         src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                                         alt="{{ $appointment->shop->name }}">
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->shop->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ Str::limit($appointment->shop->address, 50) }}</p>
                                    </div>
                                </div>

                                <!-- Appointment Details -->
                                <div class="flex flex-col sm:flex-row gap-4 flex-1">
                                    <!-- Time and Pet -->
                                    <div class="flex-1">
                                        <div class="flex items-center text-gray-700 mb-2">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">{{ $appointment->appointment_date->format('g:i A') }}</span>
                                        </div>
                                        <div class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                            <span>{{ $appointment->pet->name }} ({{ $appointment->pet->breed }})</span>
                                        </div>
                                    </div>

                                    <!-- Service and Price -->
                                    <div class="flex-1">
                                        <div class="flex items-center text-gray-700 mb-2">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span>{{ $appointment->service_type }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center text-gray-700">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>₱{{ number_format($appointment->service ? $appointment->service->getPriceForSize($appointment->pet->size_category) : $appointment->service_price, 2) }}</span>
                                            </div>
                                            
                                            @if($appointment->status === 'accepted')
                                            <a href="{{ route('receipt.download', $appointment) }}" 
                                               class="ml-2 text-blue-600 hover:text-blue-800"
                                               title="Download Receipt"
                                               onclick="event.stopPropagation();">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status and Actions -->
                                <div class="flex flex-col sm:flex-row items-center gap-4">
                                    <!-- Status Badge -->
                                    <span class="px-4 py-2 inline-flex items-center rounded-full text-sm font-medium
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        <span class="w-2 h-2 mr-2 rounded-full
                                            @if($appointment->status === 'completed') bg-green-400
                                            @elseif($appointment->status === 'cancelled') bg-red-400
                                            @else bg-blue-400
                                            @endif">
                                        </span>
                                        {{ ucfirst($appointment->status) }}
                                    </span>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                onclick="event.stopPropagation(); window.location.href='{{ route('appointments.show', $appointment->id) }}'"
                                                class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        
                                        @if($appointment->status === 'pending')
                                            <button type="button"
                                                    onclick="event.stopPropagation();"
                                                    @click="showCancelModal = true; appointmentToCancel = {{ $appointment->id }}"
                                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-full transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    onclick="event.stopPropagation(); window.location.href='{{ route('appointments.reschedule', $appointment) }}'"
                                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-full transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        @endif
                                        @if($appointment->status === 'completed')
                                            <button type="button"
                                                    onclick="event.stopPropagation(); window.location.href='/book/{{ $appointment->shop_id }}#reviews'"
                                                    class="p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded-full transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <!-- No Appointments Message -->
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-600 mb-4">No appointments found</p>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Book an appointment
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Cancel Modal -->
    <div x-show="showCancelModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         x-cloak>
        <div class="relative mx-auto p-8 border w-[500px] shadow-lg rounded-md bg-white"
             @click.away="showCancelModal = false">
            <div class="text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-semibold text-gray-900 mb-2">Cancel Appointment?</h3>
                
                <!-- Warning Message -->
                <div class="bg-red-50 p-4 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                This action cannot be undone. The appointment slot will be freed up for other customers.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cancellation Form -->
                <div class="mb-6">
                    <label class="block text-left text-sm font-medium text-gray-700 mb-2">
                        Please provide a reason for cancellation:
                    </label>
                    <div class="relative">
                        <textarea x-ref="cancelReason"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                                placeholder="Example: Schedule conflict, Emergency, etc."
                                rows="4"></textarea>
                        <div class="absolute inset-y-0 right-3 flex items-start pt-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 text-left">
                        Your reason will help us improve our services.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <button @click="handleCancel()" 
                            class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Confirm Cancellation
                    </button>
                    <button @click="showCancelModal = false" 
                            class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Keep Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name=csrf-token]');
    if (!token) {
        console.error('CSRF token not found');
    } else {
        console.log('CSRF token is present');
    }
});
</script>
@endpush 