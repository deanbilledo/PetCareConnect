<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            <!-- Service Management Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- Service Catalog -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Service Catalog</h3>
                    <div class="flex mb-4">
                        <input type="text" placeholder="Search services..." class="w-full p-2 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select class="p-2 border border-gray-300 rounded-r-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">Basic Grooming</td>
                                    <td class="px-4 py-2">Pawsome Grooming</td>
                                    <td class="px-4 py-2">Grooming</td>
                                    <td class="px-4 py-2">₱500</td>
                                    <td class="px-4 py-2"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Active</span></td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2" onclick="openEditForm('Basic Grooming', 'Pawsome Grooming', 'Grooming', 500)">Edit</button>
                                        <button class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded-lg mr-2">View</button>
                                        <button class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Deactivate</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Remove</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Edit Service Form (Hidden by default) -->
                <div id="editServiceForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Service</h3>
                            <div class="mt-2 px-7 py-3">
                                <form id="serviceForm">
                                    <input type="text" id="serviceName" placeholder="Service Name" class="mb-3 w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <input type="text" id="shopName" placeholder="Shop Name" class="mb-3 w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <select id="serviceCategory" class="mb-3 w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <option value="grooming">Grooming</option>
                                        <option value="veterinary">Veterinary</option>
                                        <option value="boarding">Boarding</option>
                                        <option value="training">Training</option>
                                    </select>
                                    <input type="number" id="servicePrice" placeholder="Price" class="mb-3 w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <div class="items-center px-4 py-3">
                                        <button id="saveButton" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
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

        // Profile dropdown toggle
        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Delete category confirmation
        document.getElementById('deleteCategory').addEventListener('click', function() {
            const selectedCategory = document.getElementById('existingCategories').value;
            if (confirm(`Are you sure you want to delete the "${selectedCategory}" category? This action cannot be undone.`)) {
                // Implement delete logic here
                console.log(`Category "${selectedCategory}" deleted`);
            }
        });

        // Open edit form
        function openEditForm(serviceName, shopName, category, price) {
            document.getElementById('serviceName').value = serviceName;
            document.getElementById('shopName').value = shopName;
            document.getElementById('serviceCategory').value = category.toLowerCase();
            document.getElementById('servicePrice').value = price;
            document.getElementById('editServiceForm').classList.remove('hidden');
        }

        // Close edit form
        document.getElementById('editServiceForm').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Save changes
        document.getElementById('saveButton').addEventListener('click', function(e) {
            e.preventDefault();
            // Implement save logic here
            console.log('Changes saved');
            document.getElementById('editServiceForm').classList.add('hidden');
        });
    </script>
</body>
</html>