<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Service Platform Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h2 class="text-xl font-bold">Dashboard Overview</h2>
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
                        <!-- Dropdown menu (hidden by default) -->
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

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-blue-100 dark:bg-blue-900 p-3 mr-4">
                            <i class="fas fa-store text-2xl text-blue-500 dark:text-blue-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Shops</h3>
                            <p class="text-2xl font-bold">{{ $totalShops }}</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mr-4">
                            <i class="fas fa-peso-sign text-2xl text-green-500 dark:text-green-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Revenue</h3>
                            <p class="text-2xl font-bold">₱{{ number_format($totalRevenue, 2) }}</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                            <i class="fas fa-calendar text-2xl text-yellow-500 dark:text-yellow-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Appointments</h3>
                            <p class="text-2xl font-bold">{{ $totalAppointments }}</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-red-100 dark:bg-red-900 p-3 mr-4">
                            <i class="fas fa-exclamation-circle text-2xl text-red-500 dark:text-red-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Shops Reported</h3>
                            <p class="text-2xl font-bold">{{ $reportedShops }}</p>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                        <h3 class="text-lg font-semibold mb-4">Revenue</h3>
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                        <h3 class="text-lg font-semibold mb-4">Appointments</h3>
                        <canvas id="appointmentsChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Services Distribution</h3>
                    <div style="height: 300px;">
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>

                <!-- Shops Management -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Shops Management</h3>
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
                                    <th class="px-4 py-2 text-left">Revenue</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shops as $shop)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $shop->name }}</td>
                                    <td class="px-4 py-2">{{ $shop->user->name }}</td>
                                    <td class="px-4 py-2">{{ $shop->address }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 {{ $shop->status === 'active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} rounded-full text-sm">
                                            {{ ucfirst($shop->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">₱{{ number_format($shop->appointments->sum('service_price'), 2) }}</td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2 edit-shop-btn" data-shop-id="{{ $shop->id }}">Edit</button>
                                        @if($shop->status === 'active')
                                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg toggle-shop-status-btn" data-shop-id="{{ $shop->id }}">Suspend</button>
                                        @else
                                            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg toggle-shop-status-btn" data-shop-id="{{ $shop->id }}">Activate</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $shops->links() }}
                    </div>
                </div>

                <!-- User Management -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">User Management</h3>
                    <div class="flex mb-4 gap-4">
                        <input type="text" placeholder="Search users..." class="flex-1 p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Roles</option>
                            <option value="customer">Customer</option>
                            <option value="shop_owner">Shop Owner</option>
                            <option value="admin">Admin</option>
                        </select>
                        <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 rounded-xl add-user-btn">
                            Add User
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Name</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Joined Date</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full mr-3" src="{{ $user->profile_photo_url }}" alt="User">
                                            <span>{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 {{ $user->role === 'admin' ? 'bg-yellow-200 text-yellow-800' : ($user->role === 'shop_owner' ? 'bg-purple-200 text-purple-800' : 'bg-blue-200 text-blue-800') }} rounded-full text-sm">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2 edit-user-btn" data-user-id="{{ $user->id }}">Edit</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg delete-user-btn" data-user-id="{{ $user->id }}">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </main>
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

    <!-- User Edit Modal -->
    <div id="userEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Edit User</h3>
                <form id="userEditForm" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end mt-6 gap-4">
                        <button type="button" onclick="closeUserEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Add New User</h3>
                <form id="addUserForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                        <input type="text" name="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                        <input type="text" name="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="customer">Customer</option>
                            <option value="shop_owner">Shop Owner</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeAddUserModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to create and update charts
        function createCharts() {
            const colorPalette = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8'];

            // Revenue Chart
            const revenueData = @json($monthlyRevenue);
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map(item => item.month),
                    datasets: [{
                        label: 'Revenue',
                        data: revenueData.map(item => item.total),
                        borderColor: colorPalette[0],
                        backgroundColor: `${colorPalette[0]}33`,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (₱)'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Revenue'
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Appointments Chart
            const appointmentsData = @json($weeklyAppointments);
            const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
            new Chart(appointmentsCtx, {
                type: 'bar',
                data: {
                    labels: appointmentsData.map(item => item.date),
                    datasets: [{
                        label: 'Appointments',
                        data: appointmentsData.map(item => item.total),
                        backgroundColor: colorPalette[1]
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Appointments'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Weekly Appointments'
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Services Distribution Chart
            const servicesData = @json($servicesDistribution);
            const servicesCtx = document.getElementById('servicesChart').getContext('2d');
            new Chart(servicesCtx, {
                type: 'doughnut',
                data: {
                    labels: servicesData.map(item => item.service),
                    datasets: [{
                        data: servicesData.map(item => item.count),
                        backgroundColor: colorPalette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Services Distribution'
                        },
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        }

        // Call createCharts when the page loads
        window.addEventListener('load', createCharts);

        // Dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
        });

        // Profile dropdown
        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Shop Management
        let currentShopId = null;

        function openShopEditModal(shopId) {
            currentShopId = shopId;
            fetch(`/admin/shops/${shopId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#shopEditForm [name="name"]').value = data.name;
                    document.querySelector('#shopEditForm [name="address"]').value = data.address;
                    document.querySelector('#shopEditForm [name="phone"]').value = data.phone;
                    document.querySelector('#shopEditForm [name="status"]').value = data.status;
                    document.getElementById('shopEditModal').classList.remove('hidden');
                });
        }

        function closeShopEditModal() {
            document.getElementById('shopEditModal').classList.add('hidden');
            currentShopId = null;
        }

        document.getElementById('shopEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(`/admin/shops/${currentShopId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        });

        function toggleShopStatus(shopId) {
            fetch(`/admin/shops/${shopId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        // User Management
        let currentUserId = null;

        function openUserEditModal(userId) {
            currentUserId = userId;
            fetch(`/admin/users/${userId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#userEditForm [name="name"]').value = data.name;
                    document.querySelector('#userEditForm [name="email"]').value = data.email;
                    document.querySelector('#userEditForm [name="phone"]').value = data.phone || '';
                    document.querySelector('#userEditForm [name="status"]').value = data.status || 'active';
                    document.getElementById('userEditModal').classList.remove('hidden');
                });
        }

        function closeUserEditModal() {
            document.getElementById('userEditModal').classList.add('hidden');
            currentUserId = null;
        }

        document.getElementById('userEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(`/admin/users/${currentUserId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update user');
            });
        });

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function openAddUserModal() {
            document.getElementById('addUserModal').classList.remove('hidden');
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
        }

        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/admin/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        });

        // Update the button click handlers in the tables
        document.querySelectorAll('.edit-shop-btn').forEach(button => {
            button.addEventListener('click', () => openShopEditModal(button.dataset.shopId));
        });

        document.querySelectorAll('.toggle-shop-status-btn').forEach(button => {
            button.addEventListener('click', () => toggleShopStatus(button.dataset.shopId));
        });

        document.querySelectorAll('.edit-user-btn').forEach(button => {
            button.addEventListener('click', () => openUserEditModal(button.dataset.userId));
        });

        document.querySelectorAll('.delete-user-btn').forEach(button => {
            button.addEventListener('click', () => deleteUser(button.dataset.userId));
        });

        document.querySelector('.add-user-btn').addEventListener('click', openAddUserModal);
    </script>
</body>
</html> 