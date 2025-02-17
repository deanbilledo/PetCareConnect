<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Pet Care Connect Platform Admin Dashboard</title>
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
                <h2 class="text-xl font-bold">Settings</h2>
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

            <!-- Settings Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- General Platform Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">General Platform Settings</h3>
                    <form>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2" for="platformFee">Platform Commission (%)</label>
                            <input type="number" id="platformFee" name="platformFee" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" value="15">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2" for="subscriptionFee">Monthly Subscription Fee (₱)</label>
                            <input type="number" id="subscriptionFee" name="subscriptionFee" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" value="500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2" for="globalPolicies">Global Policies</label>
                            <textarea id="globalPolicies" name="globalPolicies" rows="4" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">All users must adhere to our community guidelines. Service providers are responsible for maintaining accurate service listings and pricing.</textarea>
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl">
                            Save General Settings
                        </button>
                    </form>
                </div>

                <!-- Notification Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Notification Settings</h3>
                    <form>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Send payment receipts via email</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Send appointment confirmations via SMS</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-sm">Send promotional notifications</span>
                            </label>
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl">
                            Save Notification Settings
                        </button>
                    </form>
                </div>

                <!-- User Permissions -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">User Permissions</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" for="roleSelect">Select Role</label>
                        <select id="roleSelect" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="superAdmin">Super Admin</option>
                            <option value="financialAdmin">Financial Admin</option>
                            <option value="supportAdmin">Support Admin</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-md font-medium mb-2">Permissions</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Manage Shops</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Manage Users</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Manage Services</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Manage Payments</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">View Reports</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                                <span class="ml-2 text-sm">Modify Settings</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl">
                            Save Permissions
                        </button>
                        <button type="button" id="createRoleBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl">
                            Create New Role
                        </button>
                    </div>
                </div>

                <!-- Create New Role Form (Hidden by default) -->
                <div id="createRoleForm" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300 hidden">
                    <h3 class="text-lg font-semibold mb-4">Create New Role</h3>
                    <form>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2" for="newRoleName">Role Name</label>
                            <input type="text" id="newRoleName" name="newRoleName" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter new role name">
                        </div>
                        <div class="mb-4">
                            <h4 class="text-md font-medium mb-2">Permissions</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-sm">Manage Shops</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-sm">Manage Users</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-sm">Manage Services</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-sm">Manage Payments</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-sm">View Reports</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-sm">Modify Settings</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl">
                            Create Role
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Add this modal just before the closing </body> tag -->
    <div id="createRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Create New Role</h3>
                <form id="createRoleForm" class="mt-2 text-left">
                    <div class="mb-4">
                        <label for="roleName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Name</label>
                        <input type="text" id="roleName" name="roleName" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div class="mb-4">
                        <label for="roleDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="roleDescription" name="roleDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</label>
                        <div class="mt-2 space-y-2">
                            <div>
                                <input type="checkbox" id="permissionUsers" name="permissions" value="users">
                                <label for="permissionUsers" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Manage Users</label>
                            </div>
                            <div>
                                <input type="checkbox" id="permissionShops" name="permissions" value="shops">
                                <label for="permissionShops" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Manage Shops</label>
                            </div>
                            <div>
                                <input type="checkbox" id="permissionPayments" name="permissions" value="payments">
                                <label for="permissionPayments" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Manage Payments</label>
                            </div>
                        </div>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                            Create Role
                        </button>
                    </div>
                </form>
                <button id="closeCreateRoleModal" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
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

        // Create New Role form toggle
        document.getElementById('createRoleBtn').addEventListener('click', function() {
            document.getElementById('createRoleForm').classList.toggle('hidden');
        });

        // Function to open the Create Role modal
        function openCreateRoleModal() {
            document.getElementById('createRoleModal').classList.remove('hidden');
        }

        // Function to close the Create Role modal
        function closeCreateRoleModal() {
            document.getElementById('createRoleModal').classList.add('hidden');
        }

        // Event listener for the Create New Role button
        document.getElementById('createRoleBtn').addEventListener('click', openCreateRoleModal);

        // Event listener for the Cancel button in the modal
        document.getElementById('closeCreateRoleModal').addEventListener('click', closeCreateRoleModal);

        // Event listener for the Create Role form submission
        document.getElementById('createRoleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const roleName = document.getElementById('roleName').value;
            const roleDescription = document.getElementById('roleDescription').value;
            const permissions = Array.from(document.querySelectorAll('input[name="permissions"]:checked')).map(el => el.value);

            // Here you would typically send this data to your backend
            console.log('New Role:', { roleName, roleDescription, permissions });

            // For demonstration purposes, we'll just show an alert
            alert(`New role "${roleName}" created successfully!`);

            // Close the modal
            closeCreateRoleModal();

            // Reset the form
            this.reset();
        });

        // Close modal when clicking outside the modal content
        window.onclick = function(event) {
            const modal = document.getElementById('createRoleModal');
            if (event.target == modal) {
                closeCreateRoleModal();
            }
        }
    </script>
</body>
</html>