<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Service Management - Pet Service Platform Admin Dashboard</title>
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
                <h2 class="text-xl font-bold">Service Management</h2>
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

            <!-- Service Management Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- Service Catalog -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Service Catalog</h3>
                    <div class="flex mb-4">
                        <input type="text" id="searchInput" placeholder="Search services..." class="w-full p-2 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select id="categoryFilter" class="p-2 border border-gray-300 rounded-r-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Categories</option>
                            <option value="grooming">Grooming</option>
                            <option value="veterinary">Veterinary</option>
                            <option value="boarding">Boarding</option>
                            <option value="training">Training</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Service Name</th>
                                    <th class="px-4 py-2 text-left">Shop</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-left">Price</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $service->name }}</td>
                                    <td class="px-4 py-2">{{ $service->shop->name }}</td>
                                    <td class="px-4 py-2">{{ ucfirst($service->category) }}</td>
                                    <td class="px-4 py-2">₱{{ number_format($service->base_price, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 {{ $service->status === 'active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} rounded-full text-sm">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2 view-service-btn" data-service-id="{{ $service->id }}">View</button>
                                        <button class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded-lg mr-2 toggle-status-btn" 
                                                data-service-id="{{ $service->id }}" 
                                                data-current-status="{{ $service->status }}">
                                            {{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg delete-service-btn" data-service-id="{{ $service->id }}">Remove</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $services->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- View Service Modal -->
    <div id="viewServiceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Service Details</h3>
                <div id="serviceDetails" class="space-y-4">
                    <!-- Service details will be populated here -->
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeViewModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Close</button>
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

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            filterServices(searchText, category);
        });

        document.getElementById('categoryFilter').addEventListener('change', function(e) {
            const category = e.target.value.toLowerCase();
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            filterServices(searchText, category);
        });

        function filterServices(searchText, category) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const serviceName = row.cells[0].textContent.toLowerCase();
                const serviceCategory = row.cells[2].textContent.toLowerCase();
                const matchesSearch = serviceName.includes(searchText);
                const matchesCategory = !category || serviceCategory === category;
                row.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        }

        // Toggle service status
        document.querySelectorAll('.toggle-status-btn').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.dataset.serviceId;
                const currentStatus = this.dataset.currentStatus;
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                
                if (confirm(`Are you sure you want to ${currentStatus === 'active' ? 'deactivate' : 'activate'} this service?`)) {
                    fetch(`/admin/services/${serviceId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                }
            });
        });

        // Delete service
        document.querySelectorAll('.delete-service-btn').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.dataset.serviceId;
                
                if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                    fetch(`/admin/services/${serviceId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                }
            });
        });

        // View service details
        document.querySelectorAll('.view-service-btn').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.dataset.serviceId;
                fetch(`/admin/services/${serviceId}`)
                    .then(response => response.json())
                    .then(data => {
                        const service = data.service;
                        const detailsHtml = `
                            <div class="space-y-3">
                                <div>
                                    <label class="font-bold">Service Name:</label>
                                    <p>${service.name}</p>
                                </div>
                                <div>
                                    <label class="font-bold">Shop:</label>
                                    <p>${service.shop.name}</p>
                                </div>
                                <div>
                                    <label class="font-bold">Category:</label>
                                    <p>${service.category}</p>
                                </div>
                                <div>
                                    <label class="font-bold">Description:</label>
                                    <p>${service.description || 'No description available'}</p>
                                </div>
                                <div>
                                    <label class="font-bold">Base Price:</label>
                                    <p>₱${service.base_price}</p>
                                </div>
                                <div>
                                    <label class="font-bold">Duration:</label>
                                    <p>${service.duration} minutes</p>
                                </div>
                                <div>
                                    <label class="font-bold">Status:</label>
                                    <p>${service.status}</p>
                                </div>
                            </div>
                        `;
                        document.getElementById('serviceDetails').innerHTML = detailsHtml;
                        document.getElementById('viewServiceModal').classList.remove('hidden');
                    });
            });
        });

        function closeViewModal() {
            document.getElementById('viewServiceModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('viewServiceModal');
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>