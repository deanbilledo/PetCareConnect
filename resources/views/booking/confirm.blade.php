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
<div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

    <!-- Modal panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all max-w-lg w-full mx-auto">
                <!-- Modal Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="bg-blue-100 rounded-full p-2">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                            Confirm Your Booking
                        </h3>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="px-6 py-4">
                    <!-- Booking Details Card -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-gray-900 mb-3">Booking Details</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium">{{ $appointmentDateTime->format('l, F j, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-medium">{{ $appointmentDateTime->format('g:i A') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Groomer:</span>
                                <span class="font-medium">{{ $bookingData['employee']['name'] }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                <span class="text-gray-900 font-medium">Total Amount:</span>
                                <span class="text-lg font-semibold text-blue-600">₱{{ number_format($total ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Policy Card -->
                    <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800 mb-2">Cancellation Policy</h4>
                                <ul class="text-sm text-yellow-700 space-y-1.5">
                                    <li class="flex items-start">
                                        <span class="mr-2">•</span>
                                        <span>You can cancel your appointment up to 2 hours before the scheduled time</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="mr-2">•</span>
                                        <span>Maximum of 2 cancellations allowed per week</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="mr-2">•</span>
                                        <span>Excessive cancellations may result in booking restrictions</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start space-x-3 bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" 
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </div>
                        <label for="terms" class="text-sm text-gray-700">
                            <span class="font-medium">I agree to the cancellation policy and terms</span>
                            <p class="mt-1 text-gray-500">By checking this box, you acknowledge that you have read and agree to our cancellation policy and booking terms.</p>
                        </label>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                    <button type="button" 
                            onclick="hideConfirmationModal()"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button type="button" 
                            onclick="submitBooking()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Confirm Booking
                    </button>
                </div>
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
            
            // Show success message
            alert(`Voucher ${couponCode} applied successfully!`);
        } else {
            // Hide discount displays
            document.getElementById('discountDisplay').classList.add('hidden');
            document.getElementById('discountLine').classList.add('hidden');
            document.getElementById('finalTotal').textContent = `₱${subtotal.toFixed(2)}`;
            
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
        
        alert('An error occurred while validating the voucher code. Please try again.');
    });
}

function submitBooking() {
    const termsCheckbox = document.getElementById('terms');
    if (!termsCheckbox.checked) {
        alert('Please agree to the cancellation policy and terms before proceeding.');
        return;
    }
    
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
    
    form.submit();
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