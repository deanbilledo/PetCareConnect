<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Management - Pet Care Connect Platform Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h2 class="text-xl font-bold">Shop Management</h2>
                <div class="flex items-center">
                    <button id="darkModeToggle" class="mr-4 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="relative">
                        <button id="profileDropdown" class="flex items-center focus:outline-none bg-gray-100 dark:bg-gray-700 p-2 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ asset('images/01.jpg') }}" alt="Admin">
                            <span class="hidden md:block mr-1">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <!-- Dropdown menu (hidden by default) -->
                        <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl z-10 hidden">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Shop Management Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- Tabs -->
                <div class="mb-6" x-data="{ activeTab: 'pending' }">
                    <div class="flex border-b border-gray-200">
                        <button @click="activeTab = 'pending'" :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'pending'}" class="px-4 py-2 font-medium">
                            Pending Registrations
                        </button>
                        <button @click="activeTab = 'active'" :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'active'}" class="px-4 py-2 font-medium">
                            Active Shops
                        </button>
                        <button @click="activeTab = 'rejected'" :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'rejected'}" class="px-4 py-2 font-medium">
                            Rejected Shops
                        </button>
                    </div>

                    <!-- Pending Registrations Tab -->
                    <div x-show="activeTab === 'pending'" class="mt-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                            <h3 class="text-lg font-semibold mb-4">New Shop Registrations</h3>
                            <div class="overflow-x-auto rounded-xl">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                            <th class="px-4 py-2 text-left rounded-tl-xl">Shop Name</th>
                                            <th class="px-4 py-2 text-left">Owner</th>
                                            <th class="px-4 py-2 text-left">Location</th>
                                            <th class="px-4 py-2 text-left">Shop Type</th>
                                            <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingShops as $shop)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="px-4 py-2">{{ $shop->name }}</td>
                                            <td class="px-4 py-2">{{ $shop->user->name }}</td>
                                            <td class="px-4 py-2">{{ $shop->address }}</td>
                                            <td class="px-4 py-2">{{ ucfirst($shop->type) }}</td>
                                            <td class="px-4 py-2">
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2" onclick="viewRegistrationDetails('{{ $shop->id }}')">View</button>
                                                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Approve</button>
                                                </form>
                                                <button onclick="showRejectModal('{{ $shop->id }}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Reject</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-2 text-center">No pending shop registrations</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Active Shops Tab -->
                    <div x-show="activeTab === 'active'" class="mt-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                            <h3 class="text-lg font-semibold mb-4">Active Shops</h3>
                            <div class="flex mb-4">
                                <input type="text" placeholder="Search shops..." class="w-full p-2 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <select class="p-2 border border-gray-300 rounded-r-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="overflow-x-auto rounded-xl">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                            <th class="px-4 py-2 text-left rounded-tl-xl">Shop Name</th>
                                            <th class="px-4 py-2 text-left">Owner</th>
                                            <th class="px-4 py-2 text-left">Location</th>
                                            <th class="px-4 py-2 text-left">Status</th>
                                            <th class="px-4 py-2 text-left">Shop Type</th>
                                            <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($existingShops as $shop)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="px-4 py-2">{{ $shop->name }}</td>
                                            <td class="px-4 py-2">{{ $shop->user->name }}</td>
                                            <td class="px-4 py-2">{{ $shop->address }}</td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 {{ $shop->status === 'active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} rounded-full text-sm">
                                                    {{ ucfirst($shop->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">{{ ucfirst($shop->type) }}</td>
                                            <td class="px-4 py-2">
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2" onclick="editShop('{{ $shop->id }}')">Edit</button>
                                                <form action="{{ route('admin.shops.toggle-status', $shop) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded-lg mr-2">
                                                        {{ $shop->status === 'active' ? 'Suspend' : 'Activate' }}
                                                    </button>
                                                </form>
                                                <a href="#" onclick="openAnalyticsModal('{{ $shop->id }}')" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded-lg">Analytics</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 text-center">No active shops found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Shops Tab -->
                    <div x-show="activeTab === 'rejected'" class="mt-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                            <h3 class="text-lg font-semibold mb-4">Rejected Shops</h3>
                            <div class="overflow-x-auto rounded-xl">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                            <th class="px-4 py-2 text-left rounded-tl-xl">Shop Name</th>
                                            <th class="px-4 py-2 text-left">Owner</th>
                                            <th class="px-4 py-2 text-left">Location</th>
                                            <th class="px-4 py-2 text-left">Shop Type</th>
                                            <th class="px-4 py-2 text-left">Rejection Date</th>
                                            <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rejectedShops as $shop)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="px-4 py-2">{{ $shop->name }}</td>
                                            <td class="px-4 py-2">{{ $shop->user->name }}</td>
                                            <td class="px-4 py-2">{{ $shop->address }}</td>
                                            <td class="px-4 py-2">{{ ucfirst($shop->type) }}</td>
                                            <td class="px-4 py-2">{{ $shop->updated_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-2">
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2" onclick="viewRegistrationDetails('{{ $shop->id }}')">View Details</button>
                                                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg">Accept</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 text-center">No rejected shops found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileMenu');
            const button = document.getElementById('profileDropdown');
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Add other JavaScript functions from shop_management.html as needed
    </script>

    <!-- Shop Details Modal -->
    <div id="shopDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-10 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold" id="modalShopName"></h3>
                <button onclick="closeShopDetailsModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Shop Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-xl">
                    <h4 class="text-blue-800 dark:text-blue-200 font-medium mb-2">Revenue</h4>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="modalShopRevenue">₱0</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-xl">
                    <h4 class="text-green-800 dark:text-green-200 font-medium mb-2">Total Appointments</h4>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100" id="modalShopAppointments">0</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-xl">
                    <h4 class="text-yellow-800 dark:text-yellow-200 font-medium mb-2">Average Rating</h4>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100" id="modalShopRating">0</p>
                </div>
            </div>

            <!-- Shop Details Tabs -->
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px" role="tablist">
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 text-blue-600 border-blue-600 active" 
                                onclick="switchTab('details')" id="details-tab">
                            Details
                        </button>
                    </li>
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                onclick="switchTab('services')" id="services-tab">
                            Services
                        </button>
                    </li>
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                onclick="switchTab('appointments')" id="appointments-tab">
                            Recent Appointments
                        </button>
                    </li>
                    <li class="mr-2">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                onclick="switchTab('reviews')" id="reviews-tab">
                            Reviews
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Contents -->
            <div class="tab-content">
                <!-- Details Tab -->
                <div id="details-content" class="tab-pane active">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Basic Information</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Owner:</span> <span id="modalShopOwner"></span></p>
                                <p><span class="font-medium">Email:</span> <span id="modalShopEmail"></span></p>
                                <p><span class="font-medium">Phone:</span> <span id="modalShopPhone"></span></p>
                                <p><span class="font-medium">Address:</span> <span id="modalShopAddress"></span></p>
                                <p><span class="font-medium">Type:</span> <span id="modalShopType"></span></p>
                                <p><span class="font-medium">Status:</span> <span id="modalShopStatus"></span></p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Operating Hours</h4>
                            <div id="modalShopHours" class="space-y-1">
                                <!-- Operating hours will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services Tab -->
                <div id="services-content" class="tab-pane hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="modalShopServices">
                        <!-- Services will be populated here -->
                    </div>
                </div>

                <!-- Appointments Tab -->
                <div id="appointments-content" class="tab-pane hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Customer</th>
                                    <th class="px-4 py-2 text-left">Service</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Price</th>
                                </tr>
                            </thead>
                            <tbody id="modalShopAppointmentsList">
                                <!-- Appointments will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div id="reviews-content" class="tab-pane hidden">
                    <div class="space-y-4" id="modalShopReviews">
                        <!-- Reviews will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Make table rows clickable
        document.querySelectorAll('tr[data-shop-id]').forEach(row => {
            row.addEventListener('click', () => {
                const shopId = row.dataset.shopId;
                openShopDetailsModal(shopId);
            });
        });

        // Shop Details Modal Functions
        function openShopDetailsModal(shopId) {
            fetch(`/admin/shops/${shopId}/details`)
                .then(response => response.json())
                .then(data => {
                    populateModalData(data);
                    document.getElementById('shopDetailsModal').classList.remove('hidden');
                });
        }

        function closeShopDetailsModal() {
            document.getElementById('shopDetailsModal').classList.add('hidden');
        }

        function populateModalData(data) {
            const shop = data.shop;
            const stats = data.stats;

            // Populate shop name and basic stats
            document.getElementById('modalShopName').textContent = shop.name;
            document.getElementById('modalShopRevenue').textContent = `₱${formatNumber(stats.total_revenue)}`;
            document.getElementById('modalShopAppointments').textContent = stats.total_appointments;
            document.getElementById('modalShopRating').textContent = stats.average_rating.toFixed(1);

            // Populate basic information
            document.getElementById('modalShopOwner').textContent = shop.user.name;
            document.getElementById('modalShopEmail').textContent = shop.user.email;
            document.getElementById('modalShopPhone').textContent = shop.phone;
            document.getElementById('modalShopAddress').textContent = shop.address;
            document.getElementById('modalShopType').textContent = capitalizeFirstLetter(shop.type);
            document.getElementById('modalShopStatus').textContent = capitalizeFirstLetter(shop.status);

            // Populate operating hours
            const hoursContainer = document.getElementById('modalShopHours');
            hoursContainer.innerHTML = shop.operating_hours.map(hour => `
                <p><span class="font-medium">${capitalizeFirstLetter(hour.day)}:</span> 
                   ${formatTime(hour.opening_time)} - ${formatTime(hour.closing_time)}</p>
            `).join('');

            // Populate services
            const servicesContainer = document.getElementById('modalShopServices');
            servicesContainer.innerHTML = shop.services.map(service => `
                <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow">
                    <h5 class="font-medium text-lg mb-2">${service.name}</h5>
                    <p class="text-gray-600 dark:text-gray-300 mb-2">${service.description}</p>
                    <p class="font-medium">₱${formatNumber(service.price)}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Duration: ${service.duration} minutes</p>
                </div>
            `).join('');

            // Populate appointments
            const appointmentsList = document.getElementById('modalShopAppointmentsList');
            appointmentsList.innerHTML = shop.appointments.map(appointment => `
                <tr class="border-b dark:border-gray-700">
                    <td class="px-4 py-2">${formatDate(appointment.appointment_date)}</td>
                    <td class="px-4 py-2">${appointment.user.name}</td>
                    <td class="px-4 py-2">${appointment.service_type}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-sm ${getStatusClass(appointment.status)}">
                            ${capitalizeFirstLetter(appointment.status)}
                        </span>
                    </td>
                    <td class="px-4 py-2">₱${formatNumber(appointment.service_price)}</td>
                </tr>
            `).join('');

            // Populate reviews
            const reviewsContainer = document.getElementById('modalShopReviews');
            reviewsContainer.innerHTML = shop.ratings.map(rating => `
                <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <img src="${rating.user.profile_photo_url}" alt="${rating.user.name}" 
                                 class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <p class="font-medium">${rating.user.name}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${formatDate(rating.created_at)}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            ${getStarRating(rating.rating)}
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">${rating.review}</p>
                </div>
            `).join('');
        }

        function switchTab(tabName) {
            // Remove active class from all tabs and content
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-600');
                tab.classList.add('border-transparent');
            });
            document.querySelectorAll('.tab-pane').forEach(content => {
                content.classList.add('hidden');
            });

            // Add active class to selected tab and content
            document.getElementById(`${tabName}-tab`).classList.add('text-blue-600', 'border-blue-600');
            document.getElementById(`${tabName}-content`).classList.remove('hidden');
        }

        // Utility functions
        function formatNumber(number) {
            return number.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatTime(timeString) {
            return new Date(`2000-01-01 ${timeString}`).toLocaleTimeString('en-PH', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-200 text-yellow-800',
                'completed': 'bg-green-200 text-green-800',
                'cancelled': 'bg-red-200 text-red-800'
            };
            return classes[status] || 'bg-gray-200 text-gray-800';
        }

        function getStarRating(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += `<i class="fas fa-star ${i <= rating ? 'text-yellow-400' : 'text-gray-300'} mr-1"></i>`;
            }
            return stars;
        }

        // Registration Details Modal Functions
        function viewRegistrationDetails(shopId) {
            // Show loading state
            const modal = document.getElementById('registrationDetailsModal');
            const imageContainer = document.getElementById('modalShopImage');
            const loadingSpinner = document.getElementById('modalShopImageLoading');
            const certificateContainer = document.getElementById('modalBIRCertificateContainer');
            const noCertificateText = document.getElementById('modalNoBIRCertificate');
            
            // Reset and show loading state
            imageContainer.style.display = 'none';
            loadingSpinner.style.display = 'flex';
            certificateContainer.classList.add('hidden');
            noCertificateText.classList.add('hidden');
            modal.classList.remove('hidden');

            fetch(`/admin/shops/${shopId}/registration-details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Registration details:', data); // Debug log

                    // Handle shop image
                    if (data.shop_image_url) {
                        imageContainer.src = data.shop_image_url;
                        imageContainer.onload = function() {
                            loadingSpinner.style.display = 'none';
                            imageContainer.style.display = 'block';
                        };
                        imageContainer.onerror = function() {
                            console.error('Failed to load shop image');
                            this.src = '{{ asset('images/default-shop.png') }}';
                            loadingSpinner.style.display = 'none';
                            imageContainer.style.display = 'block';
                        };
                    }

                    // Update modal title
                    const modalTitle = document.getElementById('modalShopTitle');
                    if (modalTitle) {
                        modalTitle.textContent = data.name ? `Registration Details - ${data.name}` : 'Registration Details';
                    }

                    // Update shop details - Basic Information
                    const shopName = document.getElementById('modalShopName');
                    const shopType = document.getElementById('modalShopType');
                    if (shopName) shopName.textContent = data.name || 'Not provided';
                    if (shopType) shopType.textContent = data.type ? capitalizeFirstLetter(data.type) : 'Not provided';

                    // Update shop details - Contact Information
                    const shopPhone = document.getElementById('modalShopPhone');
                    const shopAddress = document.getElementById('modalShopAddress');
                    if (shopPhone) shopPhone.textContent = data.phone || 'Not provided';
                    if (shopAddress) shopAddress.textContent = data.address || 'Not provided';

                    // Update shop details - Business Information
                    const shopTIN = document.getElementById('modalShopTIN');
                    const shopVAT = document.getElementById('modalShopVAT');
                    if (shopTIN) shopTIN.textContent = data.tin || 'Not provided';
                    if (shopVAT) shopVAT.textContent = data.vat_status ? 
                        capitalizeFirstLetter(data.vat_status.replace('_', ' ')) : 'Not specified';

                    // Handle BIR Certificate display
                    if (data.bir_certificate_url) {
                        certificateContainer.classList.remove('hidden');
                        noCertificateText.classList.add('hidden');
                        const certificateLink = document.getElementById('modalBIRCertificate');
                        if (certificateLink) {
                            certificateLink.href = data.bir_certificate_url;
                            certificateLink.innerHTML = `
                                <i class="fas fa-file-pdf mr-2"></i>
                                <span>View BIR Certificate</span>
                            `;
                            certificateLink.setAttribute('download', '');
                            certificateLink.setAttribute('target', '_blank');
                        }
                    } else {
                        certificateContainer.classList.add('hidden');
                        noCertificateText.classList.remove('hidden');
                        noCertificateText.textContent = 'No certificate uploaded';
                    }

                    // Update owner information
                    if (data.user) {
                        const ownerName = document.getElementById('modalOwnerName');
                        const ownerEmail = document.getElementById('modalOwnerEmail');
                        
                        if (ownerName) ownerName.textContent = data.user.name || 'Not provided';
                        if (ownerEmail) ownerEmail.textContent = data.user.email || 'Not provided';
                    }
                })
                .catch(error => {
                    console.error('Error fetching registration details:', error);
                    alert('Failed to load shop registration details. Please try again.');
                    loadingSpinner.style.display = 'none';
                    modal.classList.add('hidden');
                });
        }

        function closeRegistrationDetailsModal() {
            document.getElementById('registrationDetailsModal').classList.add('hidden');
        }

        // Shop Edit Functions
        let currentShopId = null;

        function editShop(shopId) {
            currentShopId = shopId;
            const modal = document.getElementById('shopEditModal');
            
            // Show loading state
            modal.classList.remove('hidden');
            
            // Fetch shop details
            fetch(`/admin/shops/${shopId}/edit`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Populate form fields
                    document.querySelector('#shopEditForm [name="name"]').value = data.name;
                    document.querySelector('#shopEditForm [name="address"]').value = data.address;
                    document.querySelector('#shopEditForm [name="phone"]').value = data.phone;
                    document.querySelector('#shopEditForm [name="status"]').value = data.status;
                })
                .catch(error => {
                    console.error('Error fetching shop details:', error);
                    alert('Failed to load shop details. Please try again.');
                    closeShopEditModal();
                });
        }

        function closeShopEditModal() {
            document.getElementById('shopEditModal').classList.add('hidden');
            currentShopId = null;
        }

        // Handle form submission
        document.getElementById('shopEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentShopId) {
                console.error('No shop ID set');
                return;
            }

            const formData = new FormData(this);
            
            fetch(`/admin/shops/${currentShopId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to update shop');
                }
            })
            .catch(error => {
                console.error('Error updating shop:', error);
                alert('Failed to update shop. Please try again.');
            });
        });

        function showRejectModal(shopId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/shops/${shopId}/reject`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Add form submission handler
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to reject shop');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to reject shop. Please try again.');
            });
        });

        let monthlyRevenueChart = null;
        let weeklyAppointmentsChart = null;

        function openAnalyticsModal(shopId) {
            document.getElementById('analyticsModal').classList.remove('hidden');
            
            // Fetch analytics data
            fetch(`/admin/shops/${shopId}/analytics`)
                .then(response => response.json())
                .then(data => {
                    // Update summary stats
                    document.getElementById('analyticsRevenue').textContent = `₱${formatNumber(data.total_revenue)}`;
                    document.getElementById('analyticsTotalAppointments').textContent = data.total_appointments;
                    document.getElementById('analyticsRating').textContent = data.average_rating.toFixed(1);

                    // Destroy existing charts if they exist
                    if (monthlyRevenueChart) monthlyRevenueChart.destroy();
                    if (weeklyAppointmentsChart) weeklyAppointmentsChart.destroy();

                    // Create Monthly Revenue Chart
                    const monthlyData = data.monthly_revenue;
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    
                    monthlyRevenueChart = new Chart(document.getElementById('monthlyRevenueChart'), {
                        type: 'bar',
                        data: {
                            labels: monthlyData.map(item => monthNames[item.month - 1]),
                            datasets: [{
                                label: 'Revenue',
                                data: monthlyData.map(item => item.revenue),
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '₱' + formatNumber(value);
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Create Weekly Appointments Chart
                    const weeklyData = data.weekly_appointments;
                    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    
                    weeklyAppointmentsChart = new Chart(document.getElementById('weeklyAppointmentsChart'), {
                        type: 'line',
                        data: {
                            labels: weeklyData.map(item => dayNames[item.day - 1]),
                            datasets: [{
                                label: 'Appointments',
                                data: weeklyData.map(item => item.count),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching analytics:', error);
                    alert('Failed to load analytics data. Please try again.');
                });
        }

        function closeAnalyticsModal() {
            document.getElementById('analyticsModal').classList.add('hidden');
        }
    </script>

    <!-- Registration Details Modal -->
    <div id="registrationDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-10 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold" id="modalShopTitle"></h3>
                <button onclick="closeRegistrationDetailsModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h4 class="text-lg font-semibold mb-4">Basic Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Shop Image</p>
                            <div class="relative w-32 h-32 mt-2 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600">
                                <img id="modalShopImage" src="" alt="Shop Image" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='{{ asset('images/default-shop.png') }}'; console.log('Failed to load shop image');">
                                <div id="modalShopImageLoading" class="absolute inset-0 flex items-center justify-center bg-gray-200 dark:bg-gray-600">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Shop Name</p>
                                <p id="modalShopName" class="font-medium"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Shop Type</p>
                                <p id="modalShopType" class="font-medium"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h4 class="text-lg font-semibold mb-4">Contact Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Phone Number</p>
                            <p id="modalShopPhone" class="font-medium"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Address</p>
                            <p id="modalShopAddress" class="font-medium"></p>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h4 class="text-lg font-semibold mb-4">Business Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">TIN</p>
                            <p id="modalShopTIN" class="font-medium"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">VAT Status</p>
                            <p id="modalShopVAT" class="font-medium"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">BIR Certificate</p>
                            <div id="modalBIRCertificateContainer">
                                <a id="modalBIRCertificate" href="#" target="_blank" 
                                   class="inline-flex items-center text-blue-500 hover:text-blue-700 mt-2">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    <span>View Certificate</span>
                                </a>
                            </div>
                            <p id="modalNoBIRCertificate" class="text-gray-500 italic hidden mt-2">No certificate uploaded</p>
                        </div>
                    </div>
                </div>

                <!-- Owner Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h4 class="text-lg font-semibold mb-4">Owner Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Owner Name</p>
                            <p id="modalOwnerName" class="font-medium"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                            <p id="modalOwnerEmail" class="font-medium"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Edit Modal -->
    <div id="shopEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Edit Shop</h3>
                <form id="shopEditForm" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shop Name</label>
                        <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <input type="text" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeShopEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Reject Shop Registration</h3>
                <form id="rejectForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rejection Reason</label>
                        <textarea name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeRejectModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Reject Shop</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Analytics Modal -->
    <div id="analyticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-10 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold">Shop Analytics</h3>
                <button onclick="closeAnalyticsModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Analytics Content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-xl">
                    <h4 class="text-blue-800 dark:text-blue-200 font-medium mb-2">Total Revenue</h4>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="analyticsRevenue">₱0</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-xl">
                    <h4 class="text-green-800 dark:text-green-200 font-medium mb-2">Total Appointments</h4>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100" id="analyticsTotalAppointments">0</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-xl">
                    <h4 class="text-yellow-800 dark:text-yellow-200 font-medium mb-2">Average Rating</h4>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100" id="analyticsRating">0</p>
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow mb-6">
                <h4 class="text-lg font-semibold mb-4">Monthly Revenue</h4>
                <canvas id="monthlyRevenueChart"></canvas>
            </div>

            <!-- Weekly Appointments Chart -->
            <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow">
                <h4 class="text-lg font-semibold mb-4">Weekly Appointments</h4>
                <canvas id="weeklyAppointmentsChart"></canvas>
            </div>
        </div>
    </div>
</body>
</html> 