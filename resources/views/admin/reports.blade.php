<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports and Analytics - Pet Care Connect Platform Admin Dashboard</title>
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
        @include('admin.partials.sidebar')
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden ml-4">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-md flex items-center justify-between p-4 rounded-b-2xl">
                <h2 class="text-xl font-bold">Reports and Analytics</h2>
                <div class="flex items-center">
                    <button id="darkModeToggle" class="mr-4 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="relative">
                        <button id="profileDropdown" class="flex items-center focus:outline-none bg-gray-100 dark:bg-gray-700 p-2 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <img class="h-8 w-8 rounded-full object-cover mr-2" src="../images/01.jpg" alt="Admin">
                            <span class="hidden md:block mr-1">Christian Jude Faminiano</span>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <!-- Dropdown menu (hidden by default) -->
                        <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl z-10 hidden">
                            <a href="profile.html" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="settings.html" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Reports and Analytics Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Filter Reports</h3>
                    <div class="flex space-x-4">
                        <input type="date" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" id="startDate">
                        <input type="date" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" id="endDate">
                        <select class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" id="shopFilter">
                            <option value="">All Shops</option>
                            <option value="shop1">Pawsome Grooming</option>
                            <option value="shop2">Happy Paws</option>
                        </select>
                        <select class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" id="serviceFilter">
                            <option value="">All Services</option>
                            <option value="grooming">Grooming</option>
                            <option value="veterinary">Veterinary</option>
                            <option value="boarding">Boarding</option>
                        </select>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl" onclick="applyFilters()">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Financial Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Financial Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-xl">
                            <h4 class="text-blue-800 dark:text-blue-200 font-semibold">Total Revenue</h4>
                            <p class="text-2xl font-bold">₱1,250,000</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900 p-4 rounded-xl">
                            <h4 class="text-green-800 dark:text-green-200 font-semibold">Platform Commission</h4>
                            <p class="text-2xl font-bold">₱187,500</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-xl">
                            <h4 class="text-yellow-800 dark:text-yellow-200 font-semibold">Total Refunds</h4>
                            <p class="text-2xl font-bold">₱15,000</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900 p-4 rounded-xl">
                            <h4 class="text-purple-800 dark:text-purple-200 font-semibold">Net Profit</h4>
                            <p class="text-2xl font-bold">₱172,500</p>
                        </div>
                    </div>
                    <div class="mt-6 h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Appointment Trends -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Appointment Trends</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-green-100 dark:bg-green-900 p-4 rounded-xl">
                            <h4 class="text-green-800 dark:text-green-200 font-semibold">Completed Appointments</h4>
                            <p class="text-2xl font-bold">1,250</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-xl">
                            <h4 class="text-yellow-800 dark:text-yellow-200 font-semibold">Pending Appointments</h4>
                            <p class="text-2xl font-bold">150</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900 p-4 rounded-xl">
                            <h4 class="text-red-800 dark:text-red-200 font-semibold">Canceled Appointments</h4>
                            <p class="text-2xl font-bold">75</p>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="appointmentChart"></canvas>
                    </div>
                </div>

                <!-- Top Performing Shops -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Top Performing Shops</h3>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Shop Name</th>
                                    <th class="px-4 py-2 text-left">Total Revenue</th>
                                    <th class="px-4 py-2 text-left">Appointments</th>
                                    <th class="px-4 py-2 text-left">Average Rating</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">Pawsome Grooming</td>
                                    <td class="px-4 py-2">₱250,000</td>
                                    <td class="px-4 py-2">500</td>
                                    <td class="px-4 py-2">4.8 <i class="fas fa-star text-yellow-400"></i></td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg">View Details</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Export Reports</h3>
                    <div class="flex space-x-4">
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl flex items-center" onclick="exportReport('csv')">
                            <i class="fas fa-file-csv mr-2"></i> Export as CSV
                        </button>
                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-xl flex items-center" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf mr-2"></i> Export as PDF
                        </button>
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

        // Profile dropdown toggle
        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Function to apply filters (to be implemented)
        function applyFilters() {
            // Implement filter logic here
            console.log('Applying filters...');
        }

        // Mock data for charts
        const revenueData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [65000, 59000, 80000, 81000, 56000, 55000],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        };

        const appointmentData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Completed',
                data: [65, 59, 80, 81, 56, 55],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }, {
                label: 'Canceled',
                data: [28, 48, 40, 19, 86, 27],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            }]
        };

        // Create charts
        window.addEventListener('load', function() {
            const ctx1 = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: revenueData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Revenue'
                        }
                    }
                }
            });

            const ctx2 = document.getElementById('appointmentChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: appointmentData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Appointments'
                        }
                    }
                }
            });
        });

        // Function to export reports (to be implemented)
        function exportReport(format) {
            // Implement export logic here
            console.log(`Exporting report as ${format}...`);
        }
    </script>