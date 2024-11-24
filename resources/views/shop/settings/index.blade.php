@extends('layouts.shop')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Shop Settings</h1>

    <!-- Shop Profile Section -->
    <div id="shop-profile" class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Shop Profile</h2>
            <span class="text-sm text-green-600 hidden" id="profile-saved">
                Changes saved successfully
            </span>
        </div>
        <form class="space-y-6">
            <div class="flex items-center space-x-6">
                <div class="relative group">
                    <div class="h-32 w-32 rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200 hover:border-blue-500 transition-colors duration-150">
                        <img src="https://via.placeholder.com/150" alt="Shop logo" class="h-full w-full object-cover">
                    </div>
                    <button type="button" class="absolute bottom-2 right-2 p-2 bg-white rounded-full shadow-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                    <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Business Hours Section -->
    <div id="business-hours" class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Business Hours</h2>
            <span class="text-sm text-green-600 hidden" id="hours-saved">
                Hours updated successfully
            </span>
        </div>
        <div class="space-y-4">
            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                <span class="text-sm font-medium text-gray-700 w-28">{{ $day }}</span>
                <div class="flex items-center space-x-4">
                    <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(6, 22) as $hour)
                            <option>{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</option>
                        @endforeach
                    </select>
                    <span>to</span>
                    <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(6, 22) as $hour)
                            <option>{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</option>
                        @endforeach
                    </select>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Closed</span>
                    </label>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Update Hours
            </button>
        </div>
    </div>

    <!-- Notifications Section -->
    <div id="notifications" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Notification Preferences</h2>
        <div class="space-y-4">
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Email notifications for new appointments</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">SMS notifications for new appointments</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Daily summary email</span>
            </label>
        </div>
    </div>


    <!-- Subscription Payment Section -->
    <div id="subscription-payment" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Subscription Payment</h2>

        <!-- Trial Status -->
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-yellow-800">Trial Period Ending Soon</p>
                    <p class="text-sm text-yellow-600">Your 30-day free trial ends in 5 days</p>
                </div>
            </div>
        </div>

        <!-- GCash Payment Section -->
        <div class="border rounded-lg p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2">Partner Plan</h3>
                <p class="text-2xl font-bold">₱299<span class="text-sm font-normal text-gray-600">/month</span></p>
            </div>

            <!-- GCash Payment Details -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex items-center justify-center mb-4">
                    <img src="{{ asset('images/gcash-logo.png') }}" alt="GCash Logo" class="h-8">
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
                        <span>₱299.00</span>
                    </div>
                </div>
            </div>

            <button onclick="showGcashModal()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Pay with GCash
            </button>

            <p class="text-sm text-gray-500 text-center mt-4">
                By subscribing, you agree to our Terms of Service and Privacy Policy
            </p>
        </div>

        <!-- Cancel Subscription Warning -->
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
            <button class="mt-4 px-4 py-2 border border-red-600 text-red-600 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Cancel Subscription
            </button>
        </div>

        <!-- Secure Payment Notice -->
        <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Secured payment with SSL encryption
        </div>
    </div>

        <!-- Security Section -->
    <div id="security" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Security Settings</h2>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Simplified script without scroll tracking -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove all scroll-related JavaScript since we no longer need it
});
</script>

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
                    <span class="font-medium">₱299.00</span>
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

            <button onclick="hideGcashModal()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Add this script section -->
<script>
    function showGcashModal() {
        document.getElementById('gcashModal').classList.remove('hidden');
        document.getElementById('gcashModal').classList.add('flex');
    }

    function hideGcashModal() {
        document.getElementById('gcashModal').classList.add('hidden');
        document.getElementById('gcashModal').classList.remove('flex');
    }
</script>
@endsection 