@php
use Illuminate\Support\Facades\Log;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4 mt-5">
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
                    $addOns = $bookingData['add_ons'] ?? [];
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
                            }

                            // Calculate add-ons total for this service
                            $addOnTotal = 0;
                            $selectedAddOns = $addOns[$pet->id][$service->id] ?? [];
                            $addOnDetails = [];
                            if (!empty($selectedAddOns) && !empty($service->add_ons)) {
                                foreach ($selectedAddOns as $selectedAddOn) {
                                    foreach ($service->add_ons as $addOn) {
                                        if ($addOn['name'] === $selectedAddOn) {
                                            $addOnTotal += (float) $addOn['price'];
                                            $addOnDetails[] = $addOn;
                                        }
                                    }
                                }
                            }

                            $serviceTotal = $price + $addOnTotal;
                            $total += $serviceTotal;
                        @endphp
                        
                        @if($service)
                            <!-- Hidden inputs for this pet's service -->
                            <input type="hidden" name="pet_ids[]" value="{{ $pet->id }}">
                            <input type="hidden" name="services[]" value="{{ $service->id }}">
                            <input type="hidden" name="service_prices[]" value="{{ $price }}">
                            
                            @foreach($selectedAddOns as $addOnName)
                                <input type="hidden" name="add_ons[{{ $pet->id }}][{{ $service->id }}][]" value="{{ $addOnName }}">
                            @endforeach
                            
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex-grow">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-medium">{{ $pet->name }}</h4>
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                                            {{ ucfirst($pet->size_category) }} {{ ucfirst($pet->type) }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-gray-800">{{ $service->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $service->description }}</p>
                                        <p class="text-sm text-gray-500 mt-1">Duration: {{ $service->duration }} minutes</p>
                                        
                                        @if(!empty($addOnDetails))
                                            <div class="mt-3">
                                                <p class="text-sm font-medium text-gray-700">Add-ons:</p>
                                                <ul class="mt-1 space-y-1">
                                                    @foreach($addOnDetails as $addOn)
                                                        <li class="text-sm text-gray-600 flex justify-between">
                                                            <span>{{ $addOn['name'] }}</span>
                                                            <span class="text-gray-700">₱{{ number_format($addOn['price'], 2) }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="font-medium">Service: ₱{{ number_format($price, 2) }}</p>
                                    @if($addOnTotal > 0)
                                        <p class="text-sm text-gray-600">Add-ons: ₱{{ number_format($addOnTotal, 2) }}</p>
                                        <div class="mt-1 pt-1 border-t border-gray-200">
                                            <p class="font-medium text-blue-600">Total: ₱{{ number_format($serviceTotal, 2) }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-gray-500 text-center">No services selected</p>
                @endif
            </div>

            <!-- Coupon Discount -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium">Discount</h3>
                    @if(isset($services) && $services->isNotEmpty())
                        @php
                            $activeDiscounts = collect();
                            foreach($services as $service) {
                                $serviceDiscounts = $service->getActiveDiscounts();
                                $activeDiscounts = $activeDiscounts->concat($serviceDiscounts);
                            }
                            $voucherDiscounts = $activeDiscounts->where('voucher_code', '!=', null);
                        @endphp
                        @if($voucherDiscounts->isNotEmpty())
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Available codes:</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($voucherDiscounts as $discount)
                                        <span class="bg-blue-100 text-blue-800 font-medium px-3 py-1 rounded-full text-sm">
                                            {{ $discount->voucher_code }}
                                            @if($discount->discount_type === 'percentage')
                                                ({{ number_format($discount->discount_value, 0) }}%)
                                            @else
                                                (₱{{ number_format($discount->discount_value, 2) }})
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    <input type="text" 
                           name="coupon_code" 
                           id="couponCode"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           placeholder="Enter voucher code">
                    <button type="button" 
                            onclick="applyCoupon()"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        Apply
                    </button>
                </div>
                <!-- Discount Amount Display (initially hidden) -->
                <div id="discountDisplay" class="mt-3 hidden">
                    <div class="flex justify-between items-center text-green-600">
                        <span>Discount Applied</span>
                        <span id="discountAmount">-₱0.00</span>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <div class="flex flex-col space-y-2">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div id="discountLine" class="flex justify-between items-center text-green-600 hidden">
                        <span>Discount (WELCOME10)</span>
                        <span id="discountLineAmount">-₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center font-semibold text-lg">
                        <span>Total Amount</span>
                        <span id="finalTotal">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>
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

            <!-- Selected Employee -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="font-medium mb-4">Your Groomer</h3>
                <div class="flex items-center bg-gray-50 p-4 rounded-lg">
                    <img src="{{ $bookingData['employee']['profile_photo_url'] }}" 
                         alt="{{ $bookingData['employee']['name'] }}"
                         class="w-16 h-16 rounded-full object-cover">
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">{{ $bookingData['employee']['name'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $bookingData['employee']['position'] }}</p>
                    </div>
                </div>
                <input type="hidden" name="employee_id" value="{{ $bookingData['employee_id'] }}">
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
                <button type="button" 
                        onclick="showConfirmationModal()"
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                    Confirm Booking
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4 sm:p-6">
    <div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-sm sm:max-w-lg md:max-w-xl lg:max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Normal State -->
        <div id="modalNormalState">
            <h3 class="text-lg sm:text-xl font-semibold mb-4">Confirm Your Booking</h3>
            
            <!-- Booking Summary -->
            <div class="mb-6 bg-gray-50 p-3 sm:p-4 rounded-lg">
                <h4 class="font-medium text-gray-800 mb-3">Booking Summary</h4>
                
                <!-- Date and Time -->
                <div class="mb-3">
                    <p class="text-sm sm:text-base text-gray-600">
                        <span class="font-medium">Schedule:</span> 
                        {{ $appointmentDateTime->format('l, F j, Y') }} at {{ $appointmentDateTime->format('g:i A') }}
                    </p>
                </div>

                <!-- Services List -->
                <div class="mb-3">
                    <p class="text-sm sm:text-base font-medium text-gray-700 mb-2">Services:</p>
                    <div class="space-y-2">
                        @foreach($pets as $pet)
                            @php
                                $serviceId = $bookingData['pet_services'][$pet->id] ?? null;
                                $service = $services->firstWhere('id', $serviceId);
                                
                                // Get price based on pet size
                                $price = $service->base_price;
                                if ($service && !empty($service->variable_pricing)) {
                                    $variablePricing = is_string($service->variable_pricing) ? 
                                        json_decode($service->variable_pricing, true) : 
                                        $service->variable_pricing;
                                    
                                    $sizePrice = collect($variablePricing)->first(function($pricing) use ($pet) {
                                        return strtolower($pricing['size']) === strtolower($pet->size_category);
                                    });
                                    
                                    if ($sizePrice && isset($sizePrice['price'])) {
                                        $price = (float) $sizePrice['price'];
                                    }
                                }
                            @endphp
                            @if($service)
                                <div class="flex justify-between items-start text-sm sm:text-base">
                                    <div>
                                        <p class="text-gray-800">{{ $pet->name }} - {{ $service->name }}</p>
                                        <p class="text-xs sm:text-sm text-gray-600">{{ ucfirst($pet->size_category) }} {{ ucfirst($pet->type) }}</p>
                                    </div>
                                    <span class="text-gray-700">₱{{ number_format($price, 2) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700 text-sm sm:text-base">Total Amount:</span>
                        <span class="font-semibold text-blue-600 text-sm sm:text-base" id="modalTotalAmount">₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="mt-2 text-xs sm:text-sm text-gray-600 flex items-center">
                        <svg class="w-4 h-4 text-blue-500 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>All fees are to be paid directly to the shop upon your visit.</span>
                    </div>
                </div>
            </div>

            <p class="text-sm sm:text-base text-gray-600 mb-6">Please review and agree to our terms before confirming your booking.</p>
            
            <!-- Payment Information -->
            <div class="mb-6 bg-yellow-50 p-3 sm:p-4 rounded-lg border border-yellow-100">
                <h4 class="font-medium text-yellow-800 mb-2 text-sm sm:text-base">Payment Information</h4>
                <div class="text-xs sm:text-sm text-yellow-700 space-y-2">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>The appointment fee must be paid directly to the shop when you visit for your service.</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Please arrive 10-15 minutes before your appointment with exact payment if possible.</span>
                    </div>
                </div>
            </div>
            
            <!-- Cancellation and Reschedule Policy -->
            <div class="mb-6 bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-100">
                <h4 class="font-medium text-blue-800 mb-2 text-sm sm:text-base">Cancellation & Reschedule Policy</h4>
                <ul class="text-xs sm:text-sm text-blue-700 space-y-2">
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>You are allowed to reschedule or cancel your appointment up to 2 times per week.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Please note that excessive cancellations or rescheduling may affect your future booking privileges.</span>
                    </li>
                </ul>
            </div>
            
            <div class="mb-6">
                <label class="flex items-start space-x-2">
                    <input type="checkbox" id="terms" class="mt-1">
                    <span class="text-xs sm:text-sm text-gray-600">
                        I agree to the cancellation policy and understand that this booking is subject to the shop's terms and conditions.
                    </span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button onclick="hideConfirmationModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors text-sm sm:text-base">
                    Cancel
                </button>
                <button onclick="submitBooking()" 
                        id="confirmButton"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors text-sm sm:text-base">
                    Confirm Booking
                </button>
            </div>
        </div>
        
        <!-- Loading State -->
        <div id="modalLoadingState" class="hidden">
            <x-loading-screen 
                message="Catho is Securing Your Spot..." 
                subMessage="Hang tight! We're finalizing your appointment details..."
                size="md">
                <p class="text-xs sm:text-sm text-gray-500 mt-2 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Please do not close this window
                </p>
            </x-loading-screen>
        </div>
    </div>
</div>

<!-- Discount Success Modal -->
<div id="discountSuccessModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true" role="dialog">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full sm:w-full sm:p-6 mx-auto">
            <div class="bg-white">
                <div class="flex items-center justify-center mb-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="discountSuccessTitle">Discount Applied!</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500" id="discountSuccessMessage">
                            Your voucher has been applied successfully.
                        </p>
                    </div>
                    <div class="mt-4 bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-gray-700">Discount Amount:</span>
                            <span class="text-green-600" id="discountSuccessAmount">-₱0.00</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium mt-2">
                            <span class="text-gray-700">New Total:</span>
                            <span class="text-blue-600" id="discountSuccessTotal">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6">
                <button type="button" 
                        onclick="hideDiscountSuccessModal()" 
                        class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('hidden');
    // Add animation classes
    requestAnimationFrame(() => {
        modal.querySelector('.bg-white').classList.add('animate-modal-content');
    });
}

function hideConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('hidden');
}

function showDiscountSuccessModal(code, discountAmount, newTotal) {
    const modal = document.getElementById('discountSuccessModal');
    const title = document.getElementById('discountSuccessTitle');
    const message = document.getElementById('discountSuccessMessage');
    const amount = document.getElementById('discountSuccessAmount');
    const total = document.getElementById('discountSuccessTotal');
    
    // Update content
    title.textContent = 'Discount Applied!';
    message.textContent = `Your voucher code ${code} has been applied successfully.`;
    amount.textContent = `-₱${discountAmount.toFixed(2)}`;
    total.textContent = `₱${newTotal.toFixed(2)}`;
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Add animation if needed
    requestAnimationFrame(() => {
        modal.querySelector('.bg-white').classList.add('animate-modal-content');
    });
}

function hideDiscountSuccessModal() {
    const modal = document.getElementById('discountSuccessModal');
    modal.classList.add('hidden');
}

function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim().toUpperCase();
    if (!couponCode) {
        alert('Please enter a voucher code');
        return;
    }

    const subtotal = {{ $total }}; // Get the original total from PHP
    const petServices = @json($bookingData['pet_services'] ?? []); // Get pet services from PHP
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Make an AJAX call to validate the coupon
    fetch(`/book/{{ $shop->id }}/validate-discount/${couponCode}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            services: Object.values(petServices),
            total: subtotal
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Calculate discount
            const discountAmount = parseFloat(data.discount_amount);
            const newTotal = subtotal - discountAmount;
            
            // Update discount display
            document.getElementById('discountDisplay').classList.remove('hidden');
            document.getElementById('discountAmount').textContent = `-₱${discountAmount.toFixed(2)}`;
            
            // Update total section
            document.getElementById('discountLine').classList.remove('hidden');
            document.getElementById('discountLineAmount').textContent = `-₱${discountAmount.toFixed(2)}`;
            document.getElementById('finalTotal').textContent = `₱${newTotal.toFixed(2)}`;
            
            // Update modal total amount
            document.getElementById('modalTotalAmount').textContent = `₱${newTotal.toFixed(2)}`;
            
            // Show success modal instead of alert
            showDiscountSuccessModal(couponCode, discountAmount, newTotal);
        } else {
            // Hide discount displays
            document.getElementById('discountDisplay').classList.add('hidden');
            document.getElementById('discountLine').classList.add('hidden');
            document.getElementById('finalTotal').textContent = `₱${subtotal.toFixed(2)}`;
            document.getElementById('modalTotalAmount').textContent = `₱${subtotal.toFixed(2)}`;
            
            // Show error message
            alert(data.message || 'Invalid voucher code. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Reset displays
        document.getElementById('discountDisplay').classList.add('hidden');
        document.getElementById('discountLine').classList.add('hidden');
        document.getElementById('finalTotal').textContent = `₱${subtotal.toFixed(2)}`;
        document.getElementById('modalTotalAmount').textContent = `₱${subtotal.toFixed(2)}`;
        
        alert('An error occurred while validating the voucher code. Please try again.');
    });
}

function submitBooking() {
    const termsCheckbox = document.getElementById('terms');
    if (!termsCheckbox.checked) {
        alert('Please agree to the cancellation policy and terms before proceeding.');
        return;
    }
    
    // Disable the confirm button and show loading state
    const confirmButton = document.getElementById('confirmButton');
    const modalNormalState = document.getElementById('modalNormalState');
    const modalLoadingState = document.getElementById('modalLoadingState');
    
    confirmButton.disabled = true;
    modalNormalState.classList.add('hidden');
    modalLoadingState.classList.remove('hidden');
    
    // Make sure the GIF is preloaded before showing loading screen
    const loadingGif = new Image();
    loadingGif.src = "{{ asset('images/loadingscreen.gif') }}";
    loadingGif.onload = function() {
        // Add the discount information to the form
        const form = document.getElementById('confirmForm');
        const discountDisplay = document.getElementById('discountDisplay');
        
        if (!discountDisplay.classList.contains('hidden')) {
            // Add voucher code
            const voucherInput = document.createElement('input');
            voucherInput.type = 'hidden';
            voucherInput.name = 'voucher_code';
            voucherInput.value = document.getElementById('couponCode').value.trim().toUpperCase();
            form.appendChild(voucherInput);
            
            // Add discount amount
            const discountAmount = document.getElementById('discountAmount').textContent
                .replace('₱', '').replace('-', '').trim();
            const discountInput = document.createElement('input');
            discountInput.type = 'hidden';
            discountInput.name = 'discount_amount';
            discountInput.value = discountAmount;
            form.appendChild(discountInput);
            
            // Add final total
            const finalTotal = document.getElementById('finalTotal').textContent
                .replace('₱', '').trim();
            const totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'final_total';
            totalInput.value = finalTotal;
            form.appendChild(totalInput);
        }
        
        // Submit the form after a short delay to show loading state
        setTimeout(() => {
            form.submit();
        }, 500);
    };
    
    // Fallback if image doesn't load for some reason
    loadingGif.onerror = function() {
        // Submit form anyway
        document.getElementById('confirmForm').submit();
    };
}

document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showConfirmationModal();
});

// Close modal when clicking outside
document.getElementById('confirmationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConfirmationModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('confirmationModal').classList.contains('hidden')) {
        hideConfirmationModal();
    }
});
</script>

<style>
@keyframes modal-content {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-modal-content {
    animation: modal-content 0.2s ease-out forwards;
}
</style>
@endpush

@endsection 