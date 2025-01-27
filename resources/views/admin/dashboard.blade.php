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

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-blue-100 dark:bg-blue-900 p-3 mr-4">
                            <i class="fas fa-store text-2xl text-blue-500 dark:text-blue-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Shops</h3>
                            <p class="text-2xl font-bold">
                                @php
                                    $activeShopsCount = DB::table('shops')->where('status', 'active')->count();
                                @endphp
                            </p>

                        <p class="text-2xl font-bold">{{ $activeShopsCount }}</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mr-4">
                            <i class="fas fa-peso-sign text-2xl text-green-500 dark:text-green-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Revenue</h3>
                            <p class="text-2xl font-bold">₱950,000</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                            <i class="fas fa-calendar text-2xl text-yellow-500 dark:text-yellow-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Appointments</h3>
                            <p class="text-2xl font-bold">
                                @php
                                    $totalAppointmentsCount = DB::table('appointments')->count();
                                @endphp
                                {{ $totalAppointmentsCount }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 flex items-center transform hover:scale-105 transition-transform duration-300">
                        <div class="rounded-full bg-red-100 dark:bg-red-900 p-3 mr-4">
                            <i class="fas fa-exclamation-circle text-2xl text-red-500 dark:text-red-300"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Pending Complaints</h3>
                            <p class="text-2xl font-bold">15</p>
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
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">Pawsome Grooming</td>
                                    <td class="px-4 py-2">John Doe</td>
                                    <td class="px-4 py-2">New York</td>
                                    <td class="px-4 py-2"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span></td>
                                    <td class="px-4 py-2">₱70,000</td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Edit</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Suspend</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
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
                        <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 rounded-xl">
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
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full mr-3" src="../images/01.jpg" alt="User">
                                            <span>John Smith</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">john.smith@email.com</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">Customer</span></td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span></td>
                                    <td class="px-4 py-3">2024-01-15</td>
                                    <td class="px-4 py-3">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Edit</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Delete</button>
                                    </td>
                                </tr>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full mr-3" src="../images/02.jpg" alt="User">
                                            <span>Sarah Johnson</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">sarah.j@email.com</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-purple-200 text-purple-800 rounded-full text-sm">Shop Owner</span></td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span></td>
                                    <td class="px-4 py-3">2024-02-01</td>
                                    <td class="px-4 py-3">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Edit</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Delete</button>
                                    </td>
                                </tr>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full mr-3" src="../images/03.jpg" alt="User">
                                            <span>Mike Wilson</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">mike.w@email.com</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">Admin</span></td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span></td>
                                    <td class="px-4 py-3">2023-12-10</td>
                                    <td class="px-4 py-3">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Edit</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing 1 to 3 of 50 entries
                        </div>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600">Previous</button>
                            <button class="px-3 py-1 rounded-lg bg-indigo-500 text-white hover:bg-indigo-600">1</button>
                            <button class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600">2</button>
                            <button class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600">3</button>
                            <button class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600">Next</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mock API function to simulate fetching data from a server
        function fetchDataFromAPI(endpoint) {
            // Simulated API responses
            const apiData = {
                revenue: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Revenue 2023',
                            data: [120000, 190000, 150000, 220000, 180000, 250000],
                        },
                        {
                            label: 'Revenue 2022',
                            data: [100000, 150000, 130000, 180000, 160000, 210000],
                        }
                    ]
                },
                appointments: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [
                        {
                            label: 'Grooming',
                            data: [65, 59, 80, 81, 56, 55, 40],
                        },
                        {
                            label: 'Veterinary',
                            data: [28, 48, 40, 19, 86, 27, 90],
                        }
                    ]
                },
                services: {
                    labels: ['Grooming', 'Veterinary', 'Boarding', 'Training', 'Pet Sitting'],
                    data: [30, 25, 20, 15, 10],
                }
            };

            return new Promise((resolve) => {
                setTimeout(() => resolve(apiData[endpoint]), 100);
            });
        }

        // Function to create and update charts
        async function createCharts() {
            const colorPalette = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8'];

            // Revenue Chart
            const revenueData = await fetchDataFromAPI('revenue');
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.labels,
                    datasets: revenueData.datasets.map((dataset, index) => ({
                        ...dataset,
                        borderColor: colorPalette[index],
                        backgroundColor: `${colorPalette[index]}33`, // Adding 33 for 20% opacity
                        tension: 0.1,
                        fill: true
                    }))
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
                            text: 'Monthly Revenue Comparison'
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Appointments Chart
            const appointmentsData = await fetchDataFromAPI('appointments');
            const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
            new Chart(appointmentsCtx, {
                type: 'bar',
                data: {
                    labels: appointmentsData.labels,
                    datasets: appointmentsData.datasets.map((dataset, index) => ({
                        ...dataset,
                        backgroundColor: colorPalette[index]
                    }))
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
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
                            text: 'Weekly Appointment Distribution by Service Type'
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Services Distribution Chart
            const servicesData = await fetchDataFromAPI('services');
            const servicesCtx = document.getElementById('servicesChart').getContext('2d');
            new Chart(servicesCtx, {
                type: 'doughnut',
                data: {
                    labels: servicesData.labels,
                    datasets: [{
                        data: servicesData.data,
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed + '%';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Call createCharts when the page loads
        window.addEventListener('load', createCharts);

        // Other event listeners remain unchanged
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
        });

        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });
    </script>
</body>
</html> 