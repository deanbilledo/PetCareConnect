@php
use Illuminate\Support\Facades\Log;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4 mt-8">
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

            <div x-data="{ showVoucherInput: false, voucherCode: '', voucherApplied: false }">
                <!-- Voucher Code Section -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-medium">Voucher Code</h3>
                            <p x-show="!showVoucherInput && !voucherApplied" class="text-sm text-gray-500">Have a voucher code? Apply it here</p>
                            <p x-show="voucherApplied" class="text-sm text-green-500">Voucher "PETCARE10" applied successfully!</p>
                        </div>
                        
                        <!-- Apply Voucher Button -->
                        <button type="button" 
                                x-show="!showVoucherInput && !voucherApplied"
                                @click="showVoucherInput = true"
                                class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                            Apply Voucher
                        </button>
                    </div>

                    <!-- Voucher Input Form -->
                    <div x-show="showVoucherInput" class="mt-4">
                        <div class="flex gap-2">
                            <input type="text" 
                                   x-model="voucherCode"
                                   placeholder="Enter voucher code"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <button type="button"
                                    @click="if(voucherCode.toUpperCase() === 'PETCARE10') { voucherApplied = true; showVoucherInput = false; } else { alert('Invalid voucher code'); }"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">
                                Apply
                            </button>
                            <button type="button"
                                    @click="showVoucherInput = false; voucherCode = ''"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm">
                                Cancel
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Try using code: PETCARE10</p>
                    </div>

                    <!-- Applied Voucher Display -->
                    <div x-show="voucherApplied" class="mt-4 flex justify-between items-center bg-blue-50 p-3 rounded-md">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-800">PETCARE10</span>
                            <span class="ml-2 text-sm text-blue-600">- 10% off</span>
                        </div>
                        <button type="button"
                                @click="voucherApplied = false; voucherCode = ''"
                                class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="space-y-2">
                        <!-- Original Price -->
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Original Price</span>
                            <span class="text-gray-800">₱{{ number_format($total, 2) }}</span>
                        </div>
                        
                        <!-- Discount (shows only when voucher is applied) -->
                        <div x-show="voucherApplied" class="flex justify-between items-center text-green-600">
                            <span>Discount (10%)</span>
                            <span>-₱{{ number_format($total * 0.10, 2) }}</span>
                        </div>

                        <!-- Final Total -->
                        <div class="flex justify-between items-center font-semibold text-lg pt-2 border-t border-gray-200">
                            <span>Total Amount</span>
                            <span x-text="voucherApplied ? 
                                '₱' + ({{ $total * 0.9 }}).toFixed(2) : 
                                '₱{{ number_format($total, 2) }}'">
                                ₱{{ number_format($total, 2) }}
                            </span>
                        </div>
                    </div>
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

    <!-- Service Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Service Details</h2>
        
        @foreach($bookingData['pets'] as $pet)
            <div class="mb-6 {{ !$loop->last ? 'border-b border-gray-200 pb-6' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-medium">{{ $pet->name }}</h3>
                        <p class="text-sm text-gray-600">{{ ucfirst($pet->type) }} - {{ $pet->size_category }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $service = $services->firstWhere('id', $bookingData['pet_services'][$pet->id]);
                            $addOns = $bookingData['add_ons'][$pet->id][$service->id] ?? [];
                            $addOnDetails = collect($service->addOns)->whereIn('name', $addOns);
                            $addOnTotal = $addOnDetails->sum('price');
                            $totalPrice = $service->base_price + $addOnTotal;
                        @endphp
                        <p class="font-medium">₱{{ number_format($totalPrice, 2) }}</p>
                    </div>
                </div>

                <div class="mt-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $service->name }}</span>
                        <span class="text-gray-600">₱{{ number_format($service->base_price, 2) }}</span>
                    </div>

                    @if(!empty($addOns))
                        <div class="mt-2 pl-4 border-l-2 border-gray-200">
                            <p class="text-sm font-medium text-gray-700">Add-ons:</p>
                            @foreach($addOnDetails as $addOn)
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-600">{{ $addOn['name'] }}</span>
                                    <span class="text-gray-600">+₱{{ number_format($addOn['price'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-2 text-sm text-gray-500">
                    <p>Duration: {{ $service->duration }} minutes</p>
                </div>
            </div>
        @endforeach

        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <span class="font-medium">Total Amount</span>
                <span class="font-medium text-lg">₱{{ number_format($bookingData['total_amount'], 2) }}</span>
            </div>
        </div>
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