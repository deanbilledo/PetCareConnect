@extends('layouts.app')

@section('content')
<div x-data="appointmentHandler()" class="container mx-auto px-4 py-6">
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
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Status Banner -->
        <div class="mb-6 flex flex-wrap items-center justify-between bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center space-x-4">
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    <span class="w-2 h-2 inline-block rounded-full mr-2
                        @if($appointment->status === 'completed') bg-green-400
                        @elseif($appointment->status === 'cancelled') bg-red-400
                        @else bg-blue-400
                        @endif">
                    </span>
                    {{ ucfirst($appointment->status) }}
                </span>
                <span class="text-gray-600">
                    Appointment #{{ str_pad($appointment->id, 8, '0', STR_PAD_LEFT) }}
                </span>
            </div>
            <div class="flex space-x-3">
                @if($appointment->status === 'pending')
                    <button type="button"
                            @click="showCancelModal = true"
                            class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel Appointment
                    </button>
                @endif
                @if($appointment->status === 'completed')
                    <a href="/book/{{ $appointment->shop_id }}#reviews"
                       class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Leave Review
                    </a>
                @endif
            </div>
        </div>

        <!-- Shop Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="relative h-48 md:h-64">
                <img src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                     alt="{{ $appointment->shop->name }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <h1 class="text-2xl font-bold">{{ $appointment->shop->name }}</h1>
                    <div class="flex items-center mt-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p>{{ $appointment->shop->address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-6xl mx-auto">
            <!-- Left Column -->
            <div class="space-y-6 w-full">
                <!-- Date & Time Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Date & Time</h3>
                            <div class="mt-2 text-gray-600">
                                <p class="font-semibold text-lg">{{ $appointment->appointment_date->format('l, F j, Y') }}</p>
                                <p class="text-blue-600">{{ $appointment->appointment_date->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Details Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Service Details</h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Service Type</span>
                                    <span class="font-medium">{{ $appointment->service_type }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Duration</span>
                                    <span class="font-medium">1 hour</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Price</span>
                                    <span class="font-medium text-blue-600">PHP {{ number_format($appointment->service_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6 w-full">
                <!-- Pet Details Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Pet Information</h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Name</span>
                                    <span class="font-medium">{{ $appointment->pet->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Type</span>
                                    <span class="font-medium">{{ $appointment->pet->type }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Breed</span>
                                    <span class="font-medium">{{ $appointment->pet->breed }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Size</span>
                                    <span class="font-medium">{{ ucfirst($appointment->pet->size_category) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-10">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center">
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="ml-2 font-medium">{{ $appointment->shop->phone }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Address:</span>
                                    <p class="mt-1 font-medium">{{ $appointment->shop->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>      
        </div>

        <!-- Notes and Additional Information -->
        <div class="max-w-6xl mx-auto">
            @if($appointment->notes || $appointment->status === 'cancelled')
            <div class="mt-6">
                @if($appointment->notes)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Notes</h3>
                    <p class="text-gray-600">{{ $appointment->notes }}</p>
                </div>
                @endif

                @if($appointment->status === 'cancelled')
                <div class="bg-red-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-red-800 mb-4">Cancellation Information</h3>
                    <p class="text-gray-700 mb-2">{{ $appointment->cancellation_reason }}</p>
                    <p class="text-sm text-red-600">Cancelled on {{ $appointment->updated_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    <div class="mb-10"></div>

    <!-- Cancel Modal -->
    <div x-show="showCancelModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
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