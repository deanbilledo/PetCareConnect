@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">My Appointments</h1>
    </div>

    <!-- Appointment Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <button class="px-4 py-2 rounded-full bg-blue-500 text-white">
                All
            </button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                Upcoming
            </button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                Completed
            </button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
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
                        <div class="p-6 hover:bg-gray-50">
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
                                        <button class="px-3 py-1 text-sm text-red-600 hover:text-red-800"
                                                onclick="cancelAppointment({{ $appointment->id }})">
                                            Cancel
                                        </button>
                                    @endif
                                    @if($appointment->status === 'completed')
                                        <button class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800"
                                                onclick="leaveReview({{ $appointment->shop_id }})">
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
</div>

@push('scripts')
<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        // Add your cancellation logic here
    }
}

function leaveReview(shopId) {
    window.location.href = `/book/${shopId}#reviews`;
}
</script>
@endpush
@endsection 