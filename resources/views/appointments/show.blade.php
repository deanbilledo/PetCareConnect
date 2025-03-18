@extends('layouts.app')

@section('content')
<div x-data="appointmentHandler()" class="container mx-auto px-4 md:px-6 py-8 max-w-6xl">
    <!-- Back Button -->
    <div class="mb-6 mt-2">
        <a href="{{ route('appointments.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointments
        </a>
    </div>

    <!-- Page Title -->
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Appointment Details</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Appointment Details Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Shop Header -->
                <div class="relative h-56">
                    <img src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                         alt="{{ $appointment->shop->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-black/10"></div>
                    <div class="absolute bottom-6 left-6 text-white">
                        <h2 class="text-2xl font-bold mb-1">{{ $appointment->shop->name }}</h2>
                        <div class="flex items-center text-white/90">
                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p>{{ $appointment->shop->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Appointment Info -->
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex items-center mb-6">
                        <span class="px-3 py-1 rounded-full text-sm font-medium mr-2
                            @if($appointment->status === 'completed') bg-green-100 text-green-800 border border-green-200
                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800 border border-red-200
                            @else bg-blue-100 text-blue-800 border border-blue-200
                            @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                        <span class="text-gray-500 text-sm">Appointment #{{ $appointment->id }}</span>
                    </div>

                    <!-- Cancellation Reason (if applicable) -->
                    @if($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-lg">
                        <h3 class="text-sm font-medium text-red-800 mb-2">Cancellation Reason:</h3>
                        <p class="text-red-700">{{ $appointment->cancellation_reason }}</p>
                        @if($appointment->cancelled_by)
                        <p class="mt-2 text-sm text-red-600">Cancelled by: {{ $appointment->cancelled_by === 'shop' ? 'Shop' : 'Customer' }}</p>
                        @endif
                        @if($appointment->cancelled_at)
                        <p class="mt-1 text-sm text-red-600">Cancelled on: {{ $appointment->cancelled_at->format('F j, Y g:i A') }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Date & Time -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Date & Time</h3>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-lg font-medium text-gray-800">
                                        {{ $appointment->appointment_date->format('l, F j, Y') }}
                                    </p>
                                    <p class="text-gray-600 flex items-center mt-1">
                                        <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Service</h3>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-lg font-medium text-gray-800">{{ $appointment->service_type }}</p>
                                    <p class="text-blue-600 font-medium mt-1">PHP {{ number_format($appointment->service_price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pet Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="relative mr-4">
                                <img src="{{ $appointment->pet->profile_photo_url ?? asset('images/default-pet.png') }}"
                                     alt="{{ $appointment->pet->name }}"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-white ring-2 ring-gray-100">
                                @if(!$appointment->pet->isDeceased())
                                    <div class="absolute -bottom-1 -right-1 h-5 w-5 bg-green-500 rounded-full border-2 border-white"></div>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $appointment->pet->name }}</h2>
                                <p class="text-gray-600 text-sm">{{ $appointment->pet->breed }} • {{ $appointment->pet->type }}</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('profile.pets.show', $appointment->pet) }}" 
                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Profile
                        </a>
                    </div>

                    <!-- Pet Information Tabs -->
                    <div x-data="{ activeTab: 'basic' }">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'basic'" :class="{'text-blue-600 border-blue-500': activeTab === 'basic', 'text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'basic'}" class="py-2 px-1 border-b-2 font-medium text-sm">
                                    Basic Info
                                </button>
                                <button @click="activeTab = 'additional'" :class="{'text-blue-600 border-blue-500': activeTab === 'additional', 'text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'additional'}" class="py-2 px-1 border-b-2 font-medium text-sm">
                                    Additional Details
                                </button>
                            </nav>
                        </div>

                        <!-- Basic Information Tab -->
                        <div x-show="activeTab === 'basic'">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Status</p>
                                    <div class="flex items-center">
                                        @if($appointment->pet->isDeceased())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                                </svg>
                                                Deceased
                                            </span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                ({{ $appointment->pet->death_date->format('M d, Y') }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Active
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Date of Birth</p>
                                    <p class="font-medium text-gray-800">
                                        {{ $appointment->pet->date_of_birth ? $appointment->pet->date_of_birth->format('M d, Y') : 'Not set' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Age</p>
                                    <p class="font-medium text-gray-800">
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
                        </div>

                        <!-- Additional Information Tab -->
                        <div x-show="activeTab === 'additional'" class="animate-fade-in">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Weight</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->pet->weight ?? 'Not set' }} {{ $appointment->pet->weight ? 'kg' : '' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Size Category</p>
                                    <p class="font-medium text-gray-800">{{ Str::title($appointment->pet->size_category ?? 'Not set') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Color/Markings</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->pet->color_markings ?? 'Not set' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Coat Type</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->pet->coat_type ?? 'Not set' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Type</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->pet->type }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Breed</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->pet->breed }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
            <!-- Appointment Notes Section -->
            @if($appointment->appointmentNotes && $appointment->appointmentNotes->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Notes from the Shop
                        <span class="ml-2 text-sm bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full">{{ $appointment->appointmentNotes->count() }}</span>
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($appointment->appointmentNotes->sortByDesc('created_at') as $note)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-start">
                                @if($note->image)
                                <div class="mr-4">
                                    <img src="{{ Storage::url($note->image) }}" 
                                         alt="Note image" 
                                         class="w-24 h-24 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer"
                                         onclick="window.open('{{ Storage::url($note->image) }}', '_blank')">
                                </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $note->created_at->format('M d, Y g:i A') }}
                                        </p>
                                    </div>
                                    @if($note->note)
                                    <p class="text-gray-700">{{ $note->note }}</p>
                                    @else
                                    <p class="text-gray-500 italic text-sm">No text note added</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="lg:col-span-1 space-y-6">
            <!-- Appointment Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Appointment Summary</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-gray-600">Service:</span>
                            <span class="font-medium">{{ $appointment->service_type }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $appointment->appointment_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">{{ $appointment->appointment_date->format('g:i A') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium 
                                @if($appointment->status === 'completed') text-green-600
                                @elseif($appointment->status === 'cancelled') text-red-600
                                @else text-blue-600
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center font-medium text-lg">
                            <span>Total:</span>
                            <span class="text-blue-600">PHP {{ number_format($appointment->service_price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        @if($appointment->status === 'pending')
                            <button type="button"
                                    @click="showCancelModal = true"
                                    class="w-full flex justify-center items-center px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel Appointment
                            </button>
                        @endif

                        @if($appointment->status === 'completed')
                            <a href="{{ route('appointments.rate', $appointment) }}"
                               class="w-full flex justify-center items-center px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Leave Review
                            </a>
                        @endif

                        @if($appointment->status === 'pending')
                            <a href="{{ route('appointments.reschedule', $appointment) }}"
                               class="w-full flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Reschedule
                            </a>
                        @endif
                    </div>

                    <!-- Shop Contact -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-medium text-gray-900 mb-3">Contact the Shop</h4>
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-gray-600">{{ $appointment->shop->phone_number ?? 'Not available' }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600">{{ $appointment->shop->email ?? 'Not available' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div x-show="showCancelModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <!-- Background overlay -->
            <div x-show="showCancelModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity" 
                 @click="showCancelModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div x-show="showCancelModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-lg bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all align-middle">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Appointment</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to cancel this appointment? This action cannot be undone.
                                </p>
                                <div class="mt-4">
                                    <label for="cancelReason" class="block text-sm font-medium text-gray-700">Reason for cancellation (optional)</label>
                                    <textarea 
                                        x-ref="cancelReason"
                                        id="cancelReason" 
                                        name="reason" 
                                        rows="3" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Please provide a reason for cancellation..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="button" 
                        @click="handleCancel()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Cancellation
                    </button>
                    <button 
                        type="button" 
                        @click="showCancelModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Keep Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
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