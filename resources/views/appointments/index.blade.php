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

    <!-- Appointments Table -->
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

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shop</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dayAppointments as $appointment)
                                <tr x-show="isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}')"
                                    @click="viewAppointmentDetails({{ $appointment->id }}, $event)"
                                    class="hover:bg-gray-50 cursor-pointer transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <img class="h-12 w-12 rounded-lg object-cover shadow-sm"
                                                     src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                                                     alt="{{ $appointment->shop->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $appointment->shop->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ Str::limit($appointment->shop->address, 30) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $appointment->appointment_date->format('g:i A') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $appointment->appointment_date->format('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $appointment->pet->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->pet->breed }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $appointment->service_type }}</div>
                                        <div class="text-xs text-gray-500">Duration: 1 hour</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            PHP {{ number_format($appointment->service_price, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex items-center rounded-full text-xs font-medium
                                            @if($appointment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            <span class="w-2 h-2 mr-1.5 rounded-full
                                                @if($appointment->status === 'completed') bg-green-400
                                                @elseif($appointment->status === 'cancelled') bg-red-400
                                                @else bg-blue-400
                                                @endif">
                                            </span>
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center space-x-3">
                                            <button type="button"
                                                    onclick="event.stopPropagation(); window.location.href='{{ route('appointments.show', $appointment->id) }}'"
                                                    class="text-gray-600 hover:text-gray-800 transition-colors">
                                                <span class="sr-only">View details</span>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            
                                            @if($appointment->status === 'pending')
                                                <button type="button"
                                                        onclick="event.stopPropagation();"
                                                        @click="showCancelModal = true; appointmentToCancel = {{ $appointment->id }}"
                                                        class="text-red-600 hover:text-red-800 transition-colors">
                                                    <span class="sr-only">Cancel appointment</span>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                        onclick="event.stopPropagation(); window.location.href='{{ route('appointments.reschedule', $appointment) }}'"
                                                        class="text-blue-600 hover:text-blue-800 transition-colors">
                                                    <span class="sr-only">Reschedule appointment</span>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            @endif
                                            @if($appointment->status === 'completed')
                                                <button type="button"
                                                        onclick="event.stopPropagation(); window.location.href='/book/{{ $appointment->shop_id }}#reviews'"
                                                        class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                                    <span class="sr-only">Leave review</span>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
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

        <!-- No Results Message -->
        <div x-show="$el.querySelectorAll('[x-show]:not([x-show=\'false\'])').length === 0" 
             class="bg-white rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-600"></p>
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