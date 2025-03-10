@extends('layouts.shop')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Subscriptions</h1>
        <p class="text-gray-600">Manage your shop's subscription and payment details</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

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
                                Your 30-day free trial ends in {{ (int)$daysLeft }} days
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
                            Your subscription is active until {{ $subscription->subscription_ends_at->format('F j, Y') }}
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

            <!-- Pending Payment Notification (only show if no success message) -->
            @if(!session('success'))
            <div id="pendingNotification" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg {{ $subscription->payment_status === 'pending' ? '' : 'hidden' }}">
                <p id="successMessage" class="text-blue-800">Your payment is being verified. Please wait for admin approval.</p>
            </div>
            @endif

            <!-- GCash Payment Details -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex items-center justify-center mb-4">
                    <img src="{{ asset('images/GCash_logo.svg') }}" alt="GCash Logo" class="h-8">
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Account Name:</span>
                        <span class="font-medium">DE*N RE***T B.</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">GCash Number:</span>
                        <span class="font-medium">0970 981 3882</span>
                    </div>
                    <div class="flex justify-between font-medium">
                        <span>Total Amount:</span>
                        <span>₱{{ number_format($subscription->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($subscription->payment_status === 'rejected')
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
                <button type="button" 
                        onclick="showCancelModal()" 
                        class="mt-4 px-4 py-2 border border-red-600 text-red-600 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Cancel Subscription
                </button>
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
<div id="gcashModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 sm:p-6">
    <div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-sm sm:max-w-lg md:max-w-xl lg:max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900">GCash Payment Details</h3>
            <button onclick="hideGcashModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="paymentForm" action="{{ route('shop.subscriptions.verify') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <!-- Two-column layout for larger screens -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left column -->
                    <div>
                        <!-- GCash QR Code -->
                        <div class="flex justify-center">
                            <img src="{{ asset('images/QRcode.jpg') }}" alt="GCash QR Code" class="w-48 h-48 object-contain">
                        </div>

                        <!-- Payment Details -->
                        <div class="bg-blue-50 p-4 rounded-lg space-y-2 mt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Account Name:</span>
                                <span class="font-medium">DE*N RE***T B.</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">GCash Number:</span>
                                <span class="font-medium">0970 981 3882</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-medium">₱{{ number_format($subscription->amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right column -->
                    <div>
                        <!-- Add Reference Number Input -->
                        <div>
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
                                    <div class="flex flex-col sm:flex-row justify-center text-sm text-gray-600">
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
                    </div>
                </div>

                <!-- Instructions -->
                <div class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
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
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Payment for Verification
                    </button>
                    
                    <button type="button" onclick="hideGcashModal()" class="w-full sm:w-auto border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Close
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payment Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 sm:p-6">
    <div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-sm sm:max-w-lg md:max-w-xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Payment Successful!</h3>
            <button onclick="hideSuccessModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Verified</h3>
            <div class="mt-2">
                <p class="text-gray-600">
                    Your payment has been successfully verified. Your subscription is now active.
                </p>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subscription Plan:</span>
                    <span class="font-medium">Partner Plan</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-medium">₱{{ number_format($subscription->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium text-green-600">Active</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Valid Until:</span>
                    <span class="font-medium">{{ $subscription->subscription_ends_at ? $subscription->subscription_ends_at->format('F j, Y') : 'Pending Verification' }}</span>
                </div>
            </div>
        </div>
        
        <div class="text-center text-sm text-gray-600 mb-4">
            <p>Thank you for your subscription! You now have full access to all shop features.</p>
        </div>
        
        <button onclick="hideSuccessModal()" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            Continue
        </button>
    </div>
</div>

<!-- Cancellation Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 sm:p-6">
    <div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-md mx-auto overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Confirm Cancellation</h3>
            <button onclick="hideCancelModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-1">
            <div class="bg-red-50 p-4 rounded-lg mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Are you sure you want to cancel your subscription?</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>This action cannot be undone. Your access to premium features will be revoked immediately.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">You will lose access to:</h4>
                <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li>Manage appointments</li>
                    <li>Accept bookings</li>
                    <li>Access shop analytics</li>
                    <li>Use premium features</li>
                </ul>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="hideCancelModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Keep Subscription
                </button>
                <form action="{{ route('shop.subscriptions.cancel') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Confirm Cancellation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showGcashModal() {
        document.getElementById('gcashModal').classList.remove('hidden');
        document.getElementById('gcashModal').classList.add('flex');
    }

    function hideGcashModal() {
        document.getElementById('gcashModal').classList.remove('flex');
        document.getElementById('gcashModal').classList.add('hidden');
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

    // Form validation before submit
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const referenceNumber = document.getElementById('reference_number').value;
        const paymentScreenshot = document.getElementById('payment_screenshot').files[0];
        
        if (!referenceNumber.trim()) {
            e.preventDefault();
            alert('Please enter your GCash reference number');
            return false;
        }
        
        if (!paymentScreenshot) {
            e.preventDefault();
            alert('Please upload a screenshot of your payment');
            return false;
        }
        
        // Form is valid, continue with submission
        return true;
    });

    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    
    // Cancellation Modal Functions
    function showCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Check for payment verification
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($paymentVerified) && $paymentVerified)
            showSuccessModal();
        @endif
        
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment_verified') === 'true') {
            showSuccessModal();
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
@endpush
@endsection 