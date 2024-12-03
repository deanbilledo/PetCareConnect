@php
use Illuminate\Support\Facades\Log;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4">
        <form action="{{ route('booking.select-datetime', $shop) }}" method="POST" id="backForm">
            @csrf
            @if(isset($bookingData['pet_ids']))
                @foreach($bookingData['pet_ids'] as $petId)
                    <input type="hidden" name="pet_ids[]" value="{{ $petId }}">
                @endforeach
            @endif
            @if(isset($bookingData['pet_services']))
                @foreach($bookingData['pet_services'] as $petId => $serviceId)
                    <input type="hidden" name="services[]" value="{{ $serviceId }}">
                @endforeach
            @endif
            <input type="hidden" name="appointment_type" value="single">
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
                <div class="w-full h-full bg-blue-500"></div>
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
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Choose Date</span>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Confirm</span>
            </div>
        </div>
    </div>

    <!-- Appointment Summary -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Appointment Summary</h2>

        <form action="{{ route('booking.store', $shop) }}" method="POST" id="confirmForm">
            @csrf
            
            <!-- Services Summary -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="font-medium mb-4">Services</h3>
                @php 
                    $total = 0;
                    $petServices = $bookingData['pet_services'] ?? [];
                @endphp
                
                @if(isset($pets) && $pets->isNotEmpty() && isset($services) && $services->isNotEmpty() && !empty($petServices))
                    @foreach($pets as $pet)
                        @php
                            $serviceId = $petServices[$pet->id] ?? null;
                            $service = $services->firstWhere('id', $serviceId);
                            
                          
                            
                            // Get price based on pet size
                            $price = $service->base_price; // Default to base price
                            if ($service && !empty($service->variable_pricing)) {
                                // Convert variable_pricing from string to array if needed
                                $variablePricing = is_string($service->variable_pricing) ? 
                                    json_decode($service->variable_pricing, true) : 
                                    $service->variable_pricing;
                                
                                // Find matching size price
                                $sizePrice = collect($variablePricing)->first(function($pricing) use ($pet) {
                                    return strtolower($pricing['size']) === strtolower($pet->size_category);
                                });
                                
                                if ($sizePrice && isset($sizePrice['price'])) {
                                    $price = (float) $sizePrice['price'];
                                }
                                
                                // Debug the price calculation
                                Log::info('Price Calculation:', [
                                    'pet_name' => $pet->name,
                                    'size_category' => $pet->size_category,
                                    'variable_pricing' => $variablePricing,
                                    'matched_price' => $sizePrice ?? null,
                                    'final_price' => $price
                                ]);
                            }
                        @endphp
                        
                        @if($service)
                            <!-- Hidden inputs for this pet's service -->
                            <input type="hidden" name="pet_ids[]" value="{{ $pet->id }}">
                            <input type="hidden" name="services[]" value="{{ $service->id }}">
                            <input type="hidden" name="service_prices[]" value="{{ $price }}">
                            
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="font-medium">{{ $pet->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $service->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $service->description }}
                                        <span class="font-medium">({{ ucfirst($pet->size_category) }} Size)</span>
                                    </p>
                                    <!-- Debug output -->
                                  
                                </div>
                                <p class="font-medium">₱{{ number_format($price, 2) }}</p>
                            </div>
                            @php $total += $price; @endphp
                        @endif
                    @endforeach
                @else
                    <p class="text-gray-500 text-center">No services selected</p>
                @endif
            </div>

            <!-- Hidden appointment fields -->
            <input type="hidden" name="appointment_date" value="{{ $bookingData['appointment_date'] }}">
            <input type="hidden" name="appointment_time" value="{{ $bookingData['appointment_time'] }}">

            <!-- Date and Time -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-medium">Appointment Schedule</h3>
                        <p class="text-gray-600">
                            {{ $appointmentDateTime->format('l, F j, Y') }} at 
                            {{ $appointmentDateTime->format('g:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="font-semibold">Total Amount</span>
                    <span class="font-semibold">₱{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" 
                          rows="3" 
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                          placeholder="Any special instructions or requests..."></textarea>
            </div>

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

            <!-- Confirm Button -->
            <div class="mt-6">
                <button type="submit" 
                        onclick="event.preventDefault(); document.getElementById('confirmForm').submit();"
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                    Confirm Booking
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();
    this.submit();
});
</script>
@endpush

@endsection 