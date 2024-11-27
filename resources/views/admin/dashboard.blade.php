<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Service Platform Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <aside id="sidebar" class="bg-white text-gray-800 w-64 min-h-screen p-4 transition-all duration-300 ease-in-out transform rounded-r-3xl shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)]">
            <div class="flex items-center mb-12">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-180 h-70">
            </div>
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 bg-gray-100">
                    <i class="fas fa-home mr-3 w-6"></i>Dashboard
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-store mr-3 w-6"></i>Shops
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-users mr-3 w-6"></i>Users
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-briefcase mr-3 w-6"></i>Services
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-credit-card mr-3 w-6"></i>Payments
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-chart-bar mr-3 w-6"></i>Reports
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-cog mr-3 w-6"></i>Settings
                </a>
                <a href="#" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1">
                    <i class="fas fa-life-ring mr-3 w-6"></i>Support
                </a>
            </nav>
        </aside>

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

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- Add your dashboard content here -->
                <h1>Welcome to Admin Dashboard</h1>
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
    </script>
</body>
</html> 