<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Pet Service Platform Admin Dashboard</title>
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
                <h2 class="text-xl font-bold">User Management</h2>
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

            <!-- User Management Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- User Type Tabs -->
                <div class="mb-6">  
                    <div class="flex space-x-4">
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-lg" onclick="showUserType('shopOwners')">Shop Owners</button>
                        <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg" onclick="showUserType('staff')">Staff</button>
                        <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg" onclick="showUserType('customers')">Customers</button>
                    </div>
                </div>

                <!-- Shop Owners Management -->
                <div id="shopOwners" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Shop Owners</h3>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Name</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Shop</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="shopOwnersTableBody">
                                <!-- Table rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Staff Management (hidden by default) -->
                <div id="staff" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Staff</h3>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Name</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Shop</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                <!-- Table rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Customers Management (hidden by default) -->
                <div id="customers" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Customers</h3>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Name</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Join Date</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                <!-- Table rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="userDetailsTitle">User Details</h3>
                <div class="mt-2 px-7 py-3">
                    <div id="userDetailsContent" class="text-left">
                        <!-- User details will be inserted here -->
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeUserDetailsModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity Modal -->
    <div id="userActivityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="userActivityTitle">User Activity</h3>
                <div class="mt-2 px-7 py-3">
                    <div class="mb-4">
                        <input type="text" id="activitySearch" placeholder="Search activities..." class="w-full px-3 py-2 border rounded-md">
                    </div>
                    <div class="mb-4">
                        <select id="activityFilter" class="w-full px-3 py-2 border rounded-md">
                            <option value="all">All Activities</option>
                            <option value="login">Logins</option>
                            <option value="appointment">Appointments</option>
                            <option value="transaction">Transactions</option>
                            <option value="review">Reviews</option>
                        </select>
                    </div>
                    <div id="userActivityContent" class="text-left max-h-96 overflow-y-auto">
                        <!-- Activity details will be inserted here -->
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeUserActivityModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data for users
        const users = {
            shopOwners: [
                { name: "John Doe", email: "john@example.com", shop: "Pawsome Grooming", status: "Active" },
                { name: "Jane Smith", email: "jane@example.com", shop: "Happy Paws", status: "Active" }
            ],
            staff: [
                { name: "Alice Johnson", email: "alice@example.com", shop: "Pawsome Grooming", role: "Groomer", status: "Active" },
                { name: "Bob Williams", email: "bob@example.com", shop: "Happy Paws", role: "Veterinarian", status: "Active" }
            ],
            customers: [
                { name: "Charlie Brown", email: "charlie@example.com", joinDate: "2023-01-15", status: "Active" },
                { name: "Diana Prince", email: "diana@example.com", joinDate: "2023-02-20", status: "Active" }
            ]
        };

        // Function to populate table with user data
        function populateTable(userType) {
            const tableBody = document.getElementById(`${userType}TableBody`);
            tableBody.innerHTML = '';
            users[userType].forEach(user => {
                const row = document.createElement('tr');
                row.className = 'border-b dark:border-gray-700';
                row.innerHTML = `
                    <td class="px-4 py-2">${user.name}</td>
                    <td class="px-4 py-2">${user.email}</td>
                    <td class="px-4 py-2">${user[userType === 'shopOwners' ? 'shop' : (userType === 'staff' ? 'shop' : 'joinDate')]}</td>
                    ${userType === 'staff' ? `<td class="px-4 py-2">${user.role}</td>` : ''}
                    <td class="px-4 py-2"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">${user.status}</span></td>
                    <td class="px-4 py-2">
                        <form onsubmit="viewEditProfile('${user.name}', '${userType}'); return false;">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2">View/Edit Profile</button>
                        </form>
                        ${userType !== 'customers' ? `
                        <button onclick="viewActivity('${user.name}', '${userType}')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded-lg mr-2">View Activity</button>
                        ` : ''}
                        ${userType === 'customers' ? `
                        <button onclick="manageComplaints('${user.name}')" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded-lg mr-2">Manage Complaints</button>
                        ` : ''}
                        <button onclick="deactivateUser('${user.name}', '${userType}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">Deactivate</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Function to switch between user types
        function showUserType(userType) {
            document.getElementById('shopOwners').classList.add('hidden');
            document.getElementById('staff').classList.add('hidden');
            document.getElementById('customers').classList.add('hidden');
            document.getElementById(userType).classList.remove('hidden');

            // Update button styles
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                if (button.textContent.toLowerCase().includes(userType)) {
                    button.classList.remove('bg-gray-300', 'text-gray-700');
                    button.classList.add('bg-blue-500', 'text-white');
                } else {
                    button.classList.remove('bg-blue-500', 'text-white');
                    button.classList.add('bg-gray-300', 'text-gray-700');
                }
            });

            populateTable(userType);
        }

        // Function to view/edit profile
        function viewEditProfile(name, userType) {
            const modal = document.getElementById('userDetailsModal');
            const modalTitle = document.getElementById('userDetailsTitle');
            const modalContent = document.getElementById('userDetailsContent');
            
            modalTitle.textContent = `${name}'s Profile`;
            const user = users[userType].find(u => u.name === name);
            
            modalContent.innerHTML = `
                <p><strong>Name:</strong> ${user.name}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>${userType === 'shopOwners' ? 'Shop' : (userType === 'staff' ? 'Shop' : 'Join Date')}:</strong> ${user[userType === 'shopOwners' ? 'shop' : (userType === 'staff' ? 'shop' : 'joinDate')]}</p>
                ${userType === 'staff' ? `<p><strong>Role:</strong> ${user.role}</p>` : ''}
                <p><strong>Status:</strong> ${user.status}</p>
            `;
            
            modal.classList.remove('hidden');
        }

        // Mock data for user activities
        const userActivities = {
            "John Doe": [
                { type: "login", date: "2023-05-01 09:00:00", details: "Logged in from IP 192.168.1.1" },
                { type: "appointment", date: "2023-05-02 14:30:00", details: "Booked grooming appointment for Fluffy" },
                { type: "transaction", date: "2023-05-02 15:00:00", details: "Paid $50 for grooming service" },
                { type: "review", date: "2023-05-03 10:00:00", details: "Left a 5-star review for Pawsome Grooming" }
            ],
            "Jane Smith": [
                { type: "login", date: "2023-05-01 10:30:00", details: "Logged in from mobile device" },
                { type: "appointment", date: "2023-05-03 11:00:00", details: "Booked vet checkup for Max" },
                { type: "transaction", date: "2023-05-03 12:00:00", details: "Paid $75 for vet consultation" }
            ]
            // Add more mock data for other users as needed
        };

        // Function to view activity
        function viewActivity(name, userType) {
            const modal = document.getElementById('userActivityModal');
            const modalTitle = document.getElementById('userActivityTitle');
            const modalContent = document.getElementById('userActivityContent');
            
            modalTitle.textContent = `${name}'s Activity Log`;
            
            const activities = userActivities[name] || [];
            displayActivities(activities);
            
            modal.classList.remove('hidden');
        }

        function displayActivities(activities) {
            const modalContent = document.getElementById('userActivityContent');
            modalContent.innerHTML = activities.map(activity => `
                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p><strong>Type:</strong> ${activity.type}</p>
                    <p><strong>Date:</strong> ${activity.date}</p>
                    <p><strong>Details:</strong> ${activity.details}</p>
                </div>
            `).join('');
        }

        // Filter and search functionality
        document.getElementById('activityFilter').addEventListener('change', filterActivities);
        document.getElementById('activitySearch').addEventListener('input', filterActivities);

        function filterActivities() {
            const filterValue = document.getElementById('activityFilter').value;
            const searchValue = document.getElementById('activitySearch').value.toLowerCase();
            const userName = document.getElementById('userActivityTitle').textContent.split("'")[0];
            
            const activities = userActivities[userName] || [];
            const filteredActivities = activities.filter(activity => 
                (filterValue === 'all' || activity.type === filterValue) &&
                (activity.type.includes(searchValue) || activity.details.toLowerCase().includes(searchValue))
            );
            
            displayActivities(filteredActivities);
        }

        // Function to manage complaints
        function manageComplaints(name) {
            alert(`Managing complaints for ${name}`);
            // In a real application, you would show a list of complaints and options to handle them
        }

        // Function to deactivate user
        function deactivateUser(name, userType) {
            if (confirm(`Are you sure you want to deactivate ${name}?`)) {
                const userIndex = users[userType].findIndex(u => u.name === name);
                if (userIndex !== -1) {
                    users[userType][userIndex].status = 'Inactive';
                    populateTable(userType);
                    alert(`${name} has been deactivated.`);
                }
            }
        }

        // Close user details modal when clicking the close button
        document.getElementById('closeUserDetailsModal').addEventListener('click', function() {
            document.getElementById('userDetailsModal').classList.add('hidden');
        });

        // Close user activity modal when clicking the close button
        document.getElementById('closeUserActivityModal').addEventListener('click', function() {
            document.getElementById('userActivityModal').classList.add('hidden');
        });

        // Close modal when clicking outside the modal content
        window.onclick = function(event) {
            const modal = document.getElementById('userDetailsModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }

        // Initialize the page with shop owners data
        showUserType('shopOwners');
    </script>
</body>
</html>