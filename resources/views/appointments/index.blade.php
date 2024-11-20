@extends('layouts.app')

@section('content')
<div x-data="{
    showCancelModal: false,
    appointmentToCancel: null,
    currentFilter: 'all',
    
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

    isAppointmentVisible(status) {
        if (this.currentFilter === 'all') return true;
        if (this.currentFilter === 'upcoming') return status === 'pending';
        return status === this.currentFilter;
    }
}" class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">My Appointments</h1>
    </div>

    <!-- Appointment Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <button @click="currentFilter = 'all'" 
                    :class="{'bg-blue-500 text-white': currentFilter === 'all', 'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'all'}"
                    class="px-4 py-2 rounded-full transition-colors">
                All
            </button>
            <button @click="currentFilter = 'upcoming'"
                    :class="{'bg-blue-500 text-white': currentFilter === 'upcoming', 'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'upcoming'}"
                    class="px-4 py-2 rounded-full transition-colors">
                Upcoming
            </button>
            <button @click="currentFilter = 'completed'"
                    :class="{'bg-blue-500 text-white': currentFilter === 'completed', 'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'completed'}"
                    class="px-4 py-2 rounded-full transition-colors">
                Completed
            </button>
            <button @click="currentFilter = 'cancelled'"
                    :class="{'bg-blue-500 text-white': currentFilter === 'cancelled', 'bg-gray-100 text-gray-700 hover:bg-gray-200': currentFilter !== 'cancelled'}"
                    class="px-4 py-2 rounded-full transition-colors">
                Cancelled
            </button>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="space-y-6">
        @forelse($groupedAppointments as $date => $dayAppointments)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Date Header -->
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h2 class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h2>
                </div>

                <!-- Appointments for the day -->
                <div class="divide-y">
                    @foreach($dayAppointments as $appointment)
                        <div x-show="isAppointmentVisible('{{ $appointment->status }}')"
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