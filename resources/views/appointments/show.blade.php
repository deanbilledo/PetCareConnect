@extends('layouts.app')

@section('content')
<div x-data="appointmentHandler()" class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('appointments.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointments
        </a>
    </div>

    <!-- Appointment Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Shop Header -->
        <div class="relative h-48">
            <img src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                 alt="{{ $appointment->shop->name }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="absolute bottom-4 left-4 text-white">
                <h1 class="text-2xl font-bold">{{ $appointment->shop->name }}</h1>
                <p>{{ $appointment->shop->address }}</p>
            </div>
        </div>

        <!-- Appointment Info -->
        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date & Time -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date & Time</h3>
                    <p class="mt-1 text-lg">
                        {{ $appointment->appointment_date->format('l, F j, Y') }}<br>
                        {{ $appointment->appointment_date->format('g:i A') }}
                    </p>
                </div>

                <!-- Service Details -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Service</h3>
                    <p class="mt-1 text-lg">{{ $appointment->service_type }}</p>
                    <p class="text-blue-600 font-medium">PHP {{ number_format($appointment->service_price, 2) }}</p>
                </div>

                <!-- Pet Information -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Pet Information</h3>
                    <div class="mt-1">
                        <p class="text-lg">{{ $appointment->pet->name }}</p>
                        <p class="text-gray-600">{{ $appointment->pet->breed }} ({{ $appointment->pet->type }})</p>
                    </div>
                </div>

                <!-- Notes -->
                @if($appointment->notes)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Additional Notes</h3>
                    <p class="mt-1 text-gray-700">{{ $appointment->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-8 border-t pt-6">
                <div class="flex justify-end space-x-4">
                    @if($appointment->status === 'pending')
                        <button type="button"
                                @click="showCancelModal = true"
                                class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                            Cancel Appointment
                        </button>
                    @endif
                    @if($appointment->status === 'completed')
                        <a href="/book/{{ $appointment->shop_id }}#reviews"
                           class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                            Leave Review
                        </a>
                    @endif
                </div>
            </div>
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

@push('scripts')
<script>
function appointmentHandler() {
    return {
        showCancelModal: false,

        async handleCancel() {
            const reason = this.$refs.cancelReason.value;
            
            try {
                const response = await fetch(`/appointments/{{ $appointment->id }}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reason: reason
                    })
                });

                const data = await response.json();
                console.log('Response:', data);
                
                if (data.success) {
                    window.location.href = '{{ route('appointments.index') }}';
                } else {
                    alert(data.error || 'Failed to cancel appointment');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while cancelling the appointment');
            }
            
            this.showCancelModal = false;
        }
    }
}
</script>
@endpush
@endsection 