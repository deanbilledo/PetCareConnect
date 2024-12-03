@php
use Illuminate\Support\Facades\Log;
@endphp
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4 mt-8">
        <form action="{{ route('booking.process', $shop) }}" method="GET" id="backForm">
            <a href="javascript:void(0)" 
               onclick="document.getElementById('backForm').submit()"
               class="text-gray-600 hover:text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </form>
    </div>

    <!-- Shop Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center">
            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                 alt="{{ $shop->name }}" 
                 class="w-20 h-20 object-cover rounded-lg mr-4">
            <div>
                <h1 class="text-xl font-bold">{{ $shop->name }}</h1>
                <p class="text-gray-600">{{ $shop->address }}</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Appointment Process</h2>
        <div class="flex justify-between items-center relative">
            <!-- Progress Line -->
            <div class="absolute left-0 right-0 top-1/2 h-0.5 bg-gray-200 -z-10">
                <div class="w-1/3 h-full bg-blue-500"></div>
            </div>
            
            <!-- Steps -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Select Service</span>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Choose Date</span>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Confirm</span>
            </div>
        </div>
    </div>

    <!-- Service Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Select Services for Your Pets</h2>

        <form action="{{ route('booking.select-datetime', $shop) }}" method="POST">
            @csrf
            
            <!-- Hidden pet IDs -->
            @foreach($pets as $pet)
                <input type="hidden" name="pet_ids[]" value="{{ $pet->id }}">
            @endforeach

            <!-- Hidden appointment type -->
            <input type="hidden" name="appointment_type" value="{{ request('appointment_type') }}">

            <!-- Service selection for each pet -->
            @foreach($pets as $index => $pet)
                <div class="mb-6 pb-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                    <h3 class="font-medium mb-4">Services for {{ $pet->name }} ({{ $pet->size_category }})</h3>
                    
                    <div class="space-y-4">
                        @php
                            $petServices = $bookingData['pet_services'] ?? [];
                            $selectedServiceId = $petServices[$pet->id] ?? null;
                            $selectedAddOns = $bookingData['add_ons'][$pet->id] ?? [];
                        @endphp

                        @foreach($services as $service)
                            @php
                                $petTypes = is_array($service->pet_types) ? $service->pet_types : json_decode($service->pet_types, true) ?? [];
                                $sizeRanges = is_array($service->size_ranges) ? $service->size_ranges : json_decode($service->size_ranges, true) ?? [];
                                $addOns = is_array($service->addOns) ? $service->addOns : json_decode($service->addOns, true) ?? [];
                                
                                $petType = strtolower(rtrim($pet->type, 's'));
                                $petSize = strtolower($pet->size_category);
                                
                                $normalizedPetTypes = array_map(function($type) {
                                    return strtolower(rtrim($type, 's'));
                                }, $petTypes);
                                
                                $normalizedSizeRanges = array_map('strtolower', $sizeRanges);

                                $typeMatch = in_array($petType, $normalizedPetTypes);
                                $sizeMatch = in_array($petSize, $normalizedSizeRanges);
                            @endphp

                            @if($typeMatch && $sizeMatch)
                                <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-start">
                                        <input type="radio" 
                                               id="service_{{ $pet->id }}_{{ $service->id }}" 
                                               name="services[{{ $pet->id }}]" 
                                               value="{{ $service->id }}"
                                               {{ $selectedServiceId == $service->id ? 'checked' : '' }}
                                               required
                                               class="mt-1 mr-3">
                                        <label for="service_{{ $pet->id }}_{{ $service->id }}" class="flex-grow">
                                            <div class="flex justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium">{{ $service->name }}</span>
                                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                                            {{ ucfirst($service->category) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $service->description }}</p>
                                                    <p class="text-sm text-gray-500 mt-2">Duration: {{ $service->duration }} minutes</p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-medium text-lg">₱{{ number_format($service->base_price, 2) }}</span>
                                                    <p class="text-xs text-gray-500">Base price</p>
                                                </div>
                                            </div>

                                            <!-- Add-ons Section -->
                                            @if(!empty($addOns))
                                                <div class="mt-4 border-t border-gray-200 pt-4">
                                                    <p class="text-sm font-medium text-gray-700 mb-2">Available Add-ons:</p>
                                                    <div class="space-y-2">
                                                        @foreach($addOns as $addOn)
                                                            <label class="flex items-center">
                                                                <input type="checkbox" 
                                                                       name="add_ons[{{ $pet->id }}][{{ $service->id }}][]" 
                                                                       value="{{ $addOn['name'] }}"
                                                                       {{ in_array($addOn['name'], $selectedAddOns) ? 'checked' : '' }}
                                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                                <span class="ml-2 text-sm text-gray-600">{{ $addOn['name'] }}</span>
                                                                <span class="ml-auto text-sm font-medium">+₱{{ number_format($addOn['price'], 2) }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Next Button -->
            <div class="mt-6">
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                    Next
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 