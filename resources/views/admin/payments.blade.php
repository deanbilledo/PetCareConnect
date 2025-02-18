<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Management - Pet Service Platform Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden ml-4">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-md flex items-center justify-between p-4 rounded-b-2xl">
                <h2 class="text-xl font-bold">Payment Management</h2>
                <div class="flex items-center">
                    <button id="darkModeToggle" class="mr-4 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="relative">
                        <button id="profileDropdown" class="flex items-center focus:outline-none bg-gray-100 dark:bg-gray-700 p-2 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ auth()->user()->profile_photo_url }}" alt="Admin">
                            <span class="hidden md:block mr-1">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl z-10 hidden">
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Payment Management Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
            <!-- Subscription Plans -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold mb-4">Subscription Plans</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" for="subscriptionRate">Monthly Subscription Rate (₱)</label>
                    <div class="flex space-x-2">
                        <input type="number" id="subscriptionRate" name="subscriptionRate" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ $monthlyRate }}">
                        <button onclick="updateSubscriptionRate()" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700">
                            Update Rate
                        </button>
                    </div>
                </div>
            </div>

            <!-- Shops in Trial Period -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold mb-4">Shops in Trial Period</h3>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Shop Name</th>
                                <th class="px-4 py-2 text-left">Trial Ends</th>
                                <th class="px-4 py-2 text-left">Days Left</th>
                                <th class="px-4 py-2 text-left rounded-tr-xl">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($trialShops as $shop)
                                @php
                                    $subscription = $shop->subscriptions->first();
                                    $daysLeft = now()->diffInDays($subscription->trial_ends_at, false);
                                @endphp
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $shop->name }}</td>
                                    <td class="px-4 py-2">{{ $subscription->trial_ends_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-2">{{ $daysLeft }} days</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">
                                            Trial
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- All Subscriptions -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold mb-4">Shop Subscriptions</h3>
                    <div class="flex mb-4 space-x-4">
                    <input type="text" placeholder="Search shops..." class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Status</option>
                        <option value="verified">Verified</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="px-4 py-2 text-left rounded-tl-xl">Shop Name</th>
                                <th class="px-4 py-2 text-left">Reference Number</th>
                                <th class="px-4 py-2 text-left">Amount</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Subscription Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $subscription->shop->name }}</td>
                                    <td class="px-4 py-2">{{ $subscription->reference_number ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($subscription->amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @if($subscription->payment_status === 'pending')
                                        <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">Pending</span>
                                        @elseif($subscription->payment_status === 'verified')
                                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Verified</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-sm">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        @if($subscription->status === 'trial')
                                            <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">Trial</span>
                                        @elseif($subscription->status === 'active')
                                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-sm">{{ ucfirst($subscription->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 space-x-2">
                                        <button onclick="viewPaymentDetails({{ $subscription->id }})" 
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg">
                                            View
                                        </button>
                                        @if($subscription->payment_status === 'pending')
                                            <button onclick="verifyPayment({{ $subscription->id }})" 
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg">
                                            Verify
                                        </button>
                                            <button onclick="rejectPayment({{ $subscription->id }})" 
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">
                                            Reject
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

<!-- Payment Details Modal -->
    <div id="paymentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Payment Details</h3>
                <div id="paymentDetailsContent" class="space-y-4">
                    <!-- Payment details will be populated here -->
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="hidePaymentModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Close</button>
        </div>
        </div>
        </div>
    </div>

    <script>
        // Dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
        });

        // Profile dropdown
        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

    function viewPaymentDetails(subscriptionId) {
        const modal = document.getElementById('paymentDetailsModal');
        modal.classList.remove('hidden');
        
        fetch(`/admin/payments/${subscriptionId}/details`)
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('paymentDetailsContent');
                content.innerHTML = `
                    <div class="space-y-4">
                            <div>
                            <span class="font-medium">Shop Name:</span>
                            <span>${data.shop_name}</span>
                        </div>
                            <div>
                            <span class="font-medium">Reference Number:</span>
                            <span>${data.reference_number || 'N/A'}</span>
                        </div>
                            <div>
                            <span class="font-medium">Amount:</span>
                            <span>₱${parseFloat(data.amount).toFixed(2)}</span>
                        </div>
                            <div>
                            <span class="font-medium">Payment Status:</span>
                            <span class="px-2 py-1 rounded-full text-sm ${getStatusClass(data.payment_status)}">
                                ${data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1)}
                            </span>
                        </div>
                        ${data.payment_screenshot ? `
                            <div class="mt-4">
                                <span class="font-medium block mb-2">Payment Screenshot:</span>
                                <img src="${data.payment_screenshot}" alt="Payment Screenshot" class="w-full rounded-lg">
                            </div>
                        ` : ''}
                    </div>
                `;
            })
            .catch(error => {
                document.getElementById('paymentDetailsContent').innerHTML = `
                    <p class="text-red-600">Error loading payment details. Please try again.</p>
                `;
            });
    }

        function hidePaymentModal() {
            document.getElementById('paymentDetailsModal').classList.add('hidden');
        }

    function getStatusClass(status) {
        switch (status) {
            case 'pending':
                return 'bg-yellow-200 text-yellow-800';
            case 'verified':
                return 'bg-green-200 text-green-800';
            case 'rejected':
                return 'bg-red-200 text-red-800';
            default:
                return 'bg-gray-200 text-gray-800';
        }
    }

    function verifyPayment(subscriptionId) {
        if (confirm('Are you sure you want to verify this payment?')) {
            fetch(`/admin/payments/${subscriptionId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(error => {
                alert('Error verifying payment');
            });
        }
    }

    function rejectPayment(subscriptionId) {
        if (confirm('Are you sure you want to reject this payment?')) {
            fetch(`/admin/payments/${subscriptionId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(error => {
                alert('Error rejecting payment');
            });
        }
    }

    function updateSubscriptionRate() {
        const rate = document.getElementById('subscriptionRate').value;
        fetch('/admin/payments/update-rate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ rate })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            window.location.reload();
        })
        .catch(error => {
            alert('Error updating subscription rate');
        });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('paymentDetailsModal');
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>