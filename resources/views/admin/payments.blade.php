<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - Pet Care Connect Platform Admin Dashboard</title>
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
                <h2 class="text-xl font-bold">Payment Management</h2>
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
                            <a href="admin/profile.html" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="admin/settings.html" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Payment Management Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- All Payments -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">All Payments</h3>
                    <div class="flex mb-4 space-x-4">
                        <input type="text" placeholder="Search payments..." class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Shops</option>
                            <option value="shop1">Pawsome Grooming</option>
                            <option value="shop2">Happy Paws</option>
                        </select>
                        <input type="date" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <select class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Transaction ID</th>
                                    <th class="px-4 py-2 text-left">Shop</th>
                                    <th class="px-4 py-2 text-left">Amount</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">TRX123456</td>
                                    <td class="px-4 py-2">Pawsome Grooming</td>
                                    <td class="px-4 py-2">₱500</td>
                                    <td class="px-4 py-2">2023-05-15</td>
                                    <td class="px-4 py-2"><span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Completed</span></td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2 view-details-btn" data-id="TRX123456">View Details</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Refund Management -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Refund Requests</h3>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Refund ID</th>
                                    <th class="px-4 py-2 text-left">Shop</th>
                                    <th class="px-4 py-2 text-left">Amount</th>
                                    <th class="px-4 py-2 text-left">Reason</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">RFD789012</td>
                                    <td class="px-4 py-2">Happy Paws</td>
                                    <td class="px-4 py-2">₱250</td>
                                    <td class="px-4 py-2">Service not provided</td>
                                    <td class="px-4 py-2"><span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">Pending</span></td>
                                    <td class="px-4 py-2">
                                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg mr-2 approve-refund-btn" data-id="RFD789012">Approve</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg deny-refund-btn" data-id="RFD789012">Deny</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Revenue Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Revenue Distribution</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" for="commissionRate">Platform Commission Rate (%)</label>
                        <input type="number" id="commissionRate" name="commissionRate" class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" value="15">
                    </div>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Shop</th>
                                    <th class="px-4 py-2 text-left">Total Revenue</th>
                                    <th class="px-4 py-2 text-left">Shop Owner Receives</th>
                                    <th class="px-4 py-2 text-left">Platform Commission</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">Pawsome Grooming</td>
                                    <td class="px-4 py-2">₱10,000</td>
                                    <td class="px-4 py-2">₱8,500</td>
                                    <td class="px-4 py-2">₱1,500</td>
                                    <td class="px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg process-commission-btn" data-shop="Pawsome Grooming" data-commission="1500">Process Commission</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
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

        // Profile dropdown toggle
        document.getElementById('profileDropdown').addEventListener('click', function() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Sample data for payments
        const payments = [
            { id: "P001", shopOwner: "John Doe", shop: "Pawsome Grooming", date: "2023-05-01", amount: 100, commission: 10, status: "Completed" },
            { id: "P002", shopOwner: "Jane Smith", shop: "Happy Paws", date: "2023-05-02", amount: 150, commission: 15, status: "Pending" },
            { id: "P003", shopOwner: "John Doe", shop: "Pawsome Grooming", date: "2023-05-03", amount: 80, commission: 8, status: "Completed" },
        ];

        // Function to populate table with payment data
        function populateTable() {
            const tableBody = document.getElementById('paymentsTableBody');
            tableBody.innerHTML = '';
            payments.forEach(payment => {
                const row = document.createElement('tr');
                row.className = 'border-b dark:border-gray-700';
                row.innerHTML = `
                    <td class="px-4 py-2">${payment.id}</td>
                    <td class="px-4 py-2">${payment.shopOwner}</td>
                    <td class="px-4 py-2">${payment.shop}</td>
                    <td class="px-4 py-2">${payment.date}</td>
                    <td class="px-4 py-2">$${payment.amount.toFixed(2)}</td>
                    <td class="px-4 py-2">$${payment.commission.toFixed(2)}</td>
                    <td class="px-4 py-2"><span class="px-2 py-1 ${payment.status === 'Completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800'} rounded-full text-sm">${payment.status}</span></td>
                    <td class="px-4 py-2">
                        <button onclick="viewPaymentDetails('${payment.id}')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg mr-2">View Details</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Function to view payment details
        function viewPaymentDetails(paymentId) {
            const payment = payments.find(p => p.id === paymentId);
            const modal = document.getElementById('paymentDetailsModal');
            const modalContent = document.getElementById('paymentDetailsContent');
            
            modalContent.innerHTML = `
                <p><strong>Payment ID:</strong> ${payment.id}</p>
                <p><strong>Shop Owner:</strong> ${payment.shopOwner}</p>
                <p><strong>Shop:</strong> ${payment.shop}</p>
                <p><strong>Date:</strong> ${payment.date}</p>
                <p><strong>Total Amount:</strong> $${payment.amount.toFixed(2)}</p>
                <p><strong>Commission (Platform Fee):</strong> $${payment.commission.toFixed(2)}</p>
                <p><strong>Shop Owner Receives:</strong> $${(payment.amount - payment.commission).toFixed(2)}</p>
                <p><strong>Status:</strong> ${payment.status}</p>
                <p><strong>Note:</strong> The shop owner handles the full payment. The platform receives only the commission as a fee.</p>
            `;
            
            modal.classList.remove('hidden');
        }

        // Initialize the page with payment data
        populateTable();
    </script>

    <!-- Commission Processed Modal -->
    <div id="commissionProcessedModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modalTitle">Commission Processed</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="modalContent">
                        Commission has been successfully processed and received by the admin.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModal" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>