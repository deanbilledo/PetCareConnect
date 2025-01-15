<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Management - Pet Care Connect Platform Admin Dashboard</title>
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
                <!-- New Shop Registrations -->
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
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2" onclick="viewShopDetails('{{ $shop->id }}')">View</button>
                                        <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.shops.reject', $shop) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Reject</button>
                                        </form>
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

                <!-- Existing Shops Management -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Existing Shops</h3>
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
                                        <a href="{{ route('admin.shops.analytics', $shop) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded-lg">Analytics</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-2 text-center">No shops found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
</body>
</html> 