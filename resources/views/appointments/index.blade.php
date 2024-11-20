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
    <div class="flex justify-between items-center mb-6">
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
                    <h2 class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h2>
                </div>

                <!-- Appointments for the day -->
                <div class="divide-y">
                    @foreach($dayAppointments as $appointment)
                        <div x-show="isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}')"
                             class="p-6 hover:bg-gray-50 cursor-pointer" 
                             x-on:click="viewAppointmentDetails({{ $appointment->id }}, $event)">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4">
                                    <!-- Shop Image -->
                                    <img src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                                         alt="{{ $appointment->shop->name }}"
                                         class="w-16 h-16 rounded-lg object-cover">
                                    
                                    <!-- Appointment Details -->
                                    <div>
                                        <h3 class="font-medium">{{ $appointment->shop->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $appointment->appointment_date->format('g:i A') }}</p>
                                        <p class="text-sm text-gray-600">Pet: {{ $appointment->pet->name }}</p>
                                        <p class="text-sm text-gray-600">Service: {{ $appointment->service_type }}</p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    <span class="px-3 py-1 rounded-full text-sm 
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Additional Info & Actions -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Price: PHP {{ number_format($appointment->service_price, 2) }}
                                </div>
                                <div class="space-x-2">
                                    @if($appointment->status === 'pending')
                                        <button type="button"
                                                onclick="event.stopPropagation();"
                                                @click="showCancelModal = true; appointmentToCancel = {{ $appointment->id }}"
                                                class="px-3 py-1 text-sm text-red-600 hover:text-red-800">
                                            Cancel
                                        </button>
                                        <button type="button"
                                                onclick="event.stopPropagation(); window.location.href='{{ route('appointments.reschedule', $appointment) }}'"
                                                class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800">
                                            Reschedule
                                        </button>
                                    @endif
                                    @if($appointment->status === 'completed')
                                        <button type="button"
                                                onclick="event.stopPropagation(); window.location.href='/book/{{ $appointment->shop_id }}#reviews'"
                                                class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800">
                                            Leave Review
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-600">No appointments found</p>
                <a href="{{ route('home') }}" class="text-blue-500 hover:underline mt-2 inline-block">Book an appointment</a>
            </div>
        @endforelse

        <!-- No Results Message -->
        <div x-show="$el.querySelectorAll('[x-show]:not([x-show=\'false\'])').length === 0" 
             class="bg-white rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-600">No appointments found for the selected filters</p>
        </div>
    </div>

    <!-- Add the Cancel Modal -->
    <div x-show="showCancelModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         x-cloak>
        <div class="relative mx-auto p-8 border w-[500px] shadow-lg rounded-md bg-white">
            <div class="text-center">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Appointment Cancellation</h3>
                <div class="mt-2">
                    <p class="text-base text-gray-600 mb-6">Are you sure want to cancel this appointment?</p>
                    <div class="mb-6">
                        <label class="block text-left text-sm font-medium text-gray-700 mb-2">Reason for Cancellation:</label>
                        <textarea x-ref="cancelReason"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                placeholder="Please provide a reason for the cancellation"
                                rows="4"></textarea>
                    </div>
                </div>
                <div class="flex flex-col gap-4 mt-8">
                    <button @click="handleCancel()" 
                            class="w-full px-6 py-2.5 bg-red-600 text-white font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors">
                        Yes, cancel this appointment
                    </button>
                    <button @click="showCancelModal = false" 
                            class="w-full px-6 py-2.5 text-blue-600 font-medium hover:text-blue-800 transition-colors">
                        No, go back
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