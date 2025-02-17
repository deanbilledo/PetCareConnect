@extends('layouts.shop')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Subscriptions</h1>
        <p class="text-gray-600">Manage your shop's subscription and payment details</p>
    </div>

    <!-- Subscription Status -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        @if($subscription->status === 'trial')
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800">Trial Period {{ $daysLeft > 0 ? 'Ending Soon' : 'Ended' }}</p>
                        <p class="text-sm text-yellow-600">
                            @if($daysLeft > 0)
                                Your 30-day free trial ends in {{ $daysLeft }} days
                            @else
                                Your trial period has ended. Please subscribe to continue using shop features.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @elseif($subscription->status === 'active')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="font-medium text-green-800">Active Subscription</p>
                        <p class="text-sm text-green-600">
                            Your subscription is active until {{ $subscription->subscription_ends_at->format('F d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- GCash Payment Section -->
        <div class="border rounded-lg p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2">Partner Plan</h3>
                <p class="text-2xl font-bold">₱{{ number_format($subscription->amount, 2) }}<span class="text-sm font-normal text-gray-600">/month</span></p>
            </div>

            <!-- GCash Payment Details -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex items-center justify-center mb-4">
                    <img src="{{ asset('images/GCash_logo.svg') }}" alt="GCash Logo" class="h-8">
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Account Name:</span>
                        <span class="font-medium">Dean R****</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">GCash Number:</span>
                        <span class="font-medium">0917 123 4567</span>
                    </div>
                    <div class="flex justify-between font-medium">
                        <span>Total Amount:</span>
                        <span>₱{{ number_format($subscription->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($subscription->payment_status === 'pending')
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-blue-800">Your payment is being verified. Please wait for admin approval.</p>
                </div>
            @elseif($subscription->payment_status === 'rejected')
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800">Your last payment was rejected. Please try again.</p>
                </div>
            @endif

            @if($subscription->payment_status !== 'pending')
                <button onclick="showGcashModal()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Pay with GCash
                </button>
            @endif

            <p class="text-sm text-gray-500 text-center mt-4">
                By subscribing, you agree to our Terms of Service and Privacy Policy
            </p>
        </div>

        <!-- Cancel Subscription Warning -->
        @if($subscription->status === 'active')
            <div class="mt-6 border-t pt-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-red-600">Cancel Subscription</h3>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Warning: Canceling your subscription will immediately restrict access to shop mode features. You will no longer be able to:
                </p>
                <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li>Manage appointments</li>
                    <li>Accept bookings</li>
                    <li>Access shop analytics</li>
                    <li>Use premium features</li>
                </ul>
                <form action="{{ route('shop.subscriptions.cancel') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-red-600 text-red-600 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Cancel Subscription
                    </button>
                </form>
            </div>
        @endif

        <!-- Secure Payment Notice -->
        <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Secured payment with SSL encryption
        </div>
    </div>
</div>

<!-- GCash Payment Modal -->
<div id="gcashModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">GCash Payment Details</h3>
            <button onclick="hideGcashModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="paymentForm" action="{{ route('shop.subscriptions.verify') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <!-- GCash QR Code -->
                <div class="flex justify-center">
                    <img src="{{ asset('images/QRcode.jpg') }}" alt="GCash QR Code" class="w-48 h-48">
                </div> 

                <!-- Payment Details -->
                <div class="bg-blue-50 p-4 rounded-lg space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Account Name:</span>
                        <span class="font-medium">Dean R****</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">GCash Number:</span>
                        <span class="font-medium">0917 123 4567</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium">₱{{ number_format($subscription->amount, 2) }}</span>
                    </div>
                </div>

                <!-- Add Reference Number Input -->
                <div class="mt-4">
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                        GCash Reference Number
                    </label>
                    <input 
                        type="text" 
                        id="reference_number" 
                        name="reference_number" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your GCash reference number"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500">
                        Please enter the reference number from your GCash transaction
                    </p>
                </div>

                <!-- Add Screenshot Upload Section -->
                <div class="mt-4">
                    <label for="payment_screenshot" class="block text-sm font-medium text-gray-700 mb-1">
                        Payment Screenshot
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="payment_screenshot" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="payment_screenshot" name="payment_screenshot" type="file" class="sr-only" accept="image/*" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                    <div id="preview" class="mt-2 hidden">
                        <img id="preview_image" src="" alt="Preview" class="max-h-40 rounded-md">
                    </div>
                </div>

                <!-- Instructions -->
                <div class="text-sm text-gray-600">
                    <p class="font-medium mb-2">How to pay:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Open your GCash app</li>
                        <li>Scan the QR code or send to the number above</li>
                        <li>Enter the exact amount</li>
                        <li>Complete the payment</li>
                        <li>Take a screenshot of your receipt</li>
                    </ol>
                </div>

                <!-- Submit Payment Button -->
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mb-2">
                    Submit Payment for Verification
                </button>
                
                <button type="button" onclick="hideGcashModal()" class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showGcashModal() {
        const modal = document.getElementById('gcashModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideGcashModal() {
        const modal = document.getElementById('gcashModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Preview uploaded image
    document.getElementById('payment_screenshot').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_image').src = e.target.result;
                document.getElementById('preview').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Handle form submission
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert(data.message);
                hideGcashModal();
                window.location.reload();
            } else {
                alert('Error submitting payment. Please try again.');
            }
        } catch (error) {
            alert('Error submitting payment. Please try again.');
        }
    });
</script>
@endpush
@endsection 