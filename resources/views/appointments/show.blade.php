@extends('layouts.app')

@section('content')
<div x-data="appointmentHandler()" class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('appointments.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointments
        </a>
    </div>

    <!-- Appointment Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
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
            </div>
        </div>
    </div>

    <!-- Pet Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="relative">
                    <img src="{{ $appointment->pet->profile_photo_url ?? asset('images/default-pet.png') }}"
                         alt="{{ $appointment->pet->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold">{{ $appointment->pet->name }}</h2>
                    <p class="text-gray-600">{{ $appointment->pet->breed }} • {{ $appointment->pet->type }}</p>
                </div>
                </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <div class="flex items-center mt-1">
                        @if($appointment->pet->isDeceased())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                </svg>
                                Deceased
                            </span>
                            <span class="ml-2 text-sm text-gray-600">
                                ({{ $appointment->pet->death_date->format('M d, Y') }})
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Active
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date of Birth</p>
                    <p class="font-medium">
                        {{ $appointment->pet->date_of_birth ? $appointment->pet->date_of_birth->format('M d, Y') : 'Not set' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Age</p>
                    <p class="font-medium">
                        @php
                            $birthDate = $appointment->pet->date_of_birth;
                            if ($birthDate) {
                                $now = $appointment->pet->isDeceased() ? $appointment->pet->death_date : now();
                                $years = (int)$birthDate->diffInYears($now);
                                $totalMonths = (int)$birthDate->diffInMonths($now);
                                $months = (int)($totalMonths % 12);
                                $days = (int)$birthDate->copy()->addMonths($totalMonths)->diffInDays($now);
                                
                                if ($years >= 1) {
                                    echo $years . ' ' . Str::plural('year', $years);
                                    if ($months > 0) {
                                        echo ' and ' . $months . ' ' . Str::plural('month', $months);
                                    }
                                    echo $appointment->pet->isDeceased() ? ' (at time of death)' : ' old';
                                } else {
                                    if ($months > 0) {
                                        echo $months . ' ' . Str::plural('month', $months);
                                        if ($days > 0) {
                                            echo ' and ' . $days . ' ' . Str::plural('day', $days);
                                        }
                                        echo $appointment->pet->isDeceased() ? ' (at time of death)' : ' old';
                                    } else {
                                        $days = (int)$birthDate->diffInDays($now);
                                        echo $days . ' ' . Str::plural('day', $days);
                                        echo $appointment->pet->isDeceased() ? ' (at time of death)' : ' old';
                                    }
                                }
                            } else {
                                echo 'Not set';
                            }
                        @endphp
                    </p>
                </div>
            </div>

            <!-- Additional Pet Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Weight</p>
                    <p class="font-medium">{{ $appointment->pet->weight ?? 'Not set' }} {{ $appointment->pet->weight ? 'kg' : '' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Size Category</p>
                    <p class="font-medium">{{ Str::title($appointment->pet->size_category ?? 'Not set') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Color/Markings</p>
                    <p class="font-medium">{{ $appointment->pet->color_markings ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Coat Type</p>
                    <p class="font-medium">{{ $appointment->pet->coat_type ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Type</p>
                    <p class="font-medium">{{ $appointment->pet->type }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Breed</p>
                    <p class="font-medium">{{ $appointment->pet->breed }}</p>
                </div>
            </div>

            <!-- View Full Pet Profile Button -->
            <div class="mt-6 text-center">
                <a href="{{ route('profile.pets.show', $appointment->pet) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Full Pet Profile
                </a>
            </div>
        </div>
            </div>

            <!-- Actions -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
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

    <!-- Cancel Modal -->
    @include('appointments.partials._cancel_modal')
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