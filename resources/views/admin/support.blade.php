<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Support - Pet Care Connect Platform Admin Dashboard</title>
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
                <h2 class="text-xl font-bold">Support</h2>
                <div class="flex items-center">
                    <button id="darkModeToggle" class="mr-4 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="relative">
                        <button id="profileDropdown" class="flex items-center focus:outline-none bg-gray-100 dark:bg-gray-700 p-2 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <img class="h-8 w-8 rounded-full object-cover mr-2" src="../images/01.jpg" alt="Admin">
                            <span class="hidden md:block mr-1">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <!-- Dropdown menu (hidden by default) -->
                        <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl z-10 hidden">
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Support Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <!-- Shop Reports -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Shop Reports</h3>
                    
                    <!-- Filters -->
                    <form action="{{ route('admin.support') }}" method="GET" class="flex mb-4 space-x-4">
                        <input type="text" name="search" placeholder="Search reports..." 
                               value="{{ request('search') }}"
                               class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        
                        <select name="status" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        </select>
                        
                        <select name="report_type" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">Report Type</option>
                            @foreach($reportTypes as $type)
                                <option value="{{ $type }}" {{ request('report_type') == $type ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-xl">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                    <!-- Reports Table -->
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Report ID</th>
                                    <th class="px-4 py-2 text-left">Shop Name</th>
                                    <th class="px-4 py-2 text-left">Reported By</th>
                                    <th class="px-4 py-2 text-left">Report Type</th>
                                    <th class="px-4 py-2 text-left">Description</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-4 py-2">RPT-{{ $report->id }}</td>
                                        <td class="px-4 py-2">{{ $report->shop->name }}</td>
                                        <td class="px-4 py-2">{{ $report->user->name }}</td>
                                        <td class="px-4 py-2">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</td>
                                        <td class="px-4 py-2">
                                            <span class="inline-block max-w-xs truncate" title="{{ $report->description }}">
                                                {{ Str::limit($report->description, 50) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($report->status == 'pending')
                                                <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">Pending</span>
                                            @elseif($report->status == 'under_review')
                                                <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">Under Review</span>
                                            @elseif($report->status == 'resolved')
                                                <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Resolved</span>
                                            @elseif($report->status == 'dismissed')
                                                <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-sm">Dismissed</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $report->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-2 flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                    onclick="viewReport({{ $report->id }})">
                                                View
                                            </button>
                                            
                                            @if($report->status == 'pending')
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                        onclick="updateStatus({{ $report->id }}, 'under_review')">
                                                    Review
                                                </button>
                                            @endif
                                            
                                            @if($report->status == 'pending' || $report->status == 'under_review')
                                                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                        onclick="updateStatus({{ $report->id }}, 'resolved')">
                                                    Resolve
                                                </button>
                                                <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                        onclick="updateStatus({{ $report->id }}, 'dismissed')">
                                                    Dismiss
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                            No reports found. All shops are behaving well!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                </div>
                
                <!-- User Reports -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">User Reports</h3>
                    
                    <!-- Filters -->
                    <form action="{{ route('admin.support') }}" method="GET" class="flex mb-4 space-x-4">
                        <input type="text" name="user_search" placeholder="Search reports..." 
                               value="{{ request('user_search') }}"
                               class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        
                        <select name="user_status" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('user_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('user_status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="resolved" {{ request('user_status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="dismissed" {{ request('user_status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        </select>
                        
                        <select name="user_report_type" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">Report Type</option>
                            @foreach($userReportTypes as $type)
                                <option value="{{ $type }}" {{ request('user_report_type') == $type ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-xl">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                    <!-- Reports Table -->
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Report ID</th>
                                    <th class="px-4 py-2 text-left">Reported User</th>
                                    <th class="px-4 py-2 text-left">Reported By</th>
                                    <th class="px-4 py-2 text-left">Report Type</th>
                                    <th class="px-4 py-2 text-left">Description</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userReports as $report)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-4 py-2">USR-{{ $report->id }}</td>
                                        <td class="px-4 py-2">{{ $report->reportedUser->name }}</td>
                                        <td class="px-4 py-2">{{ $report->reporter->name }}</td>
                                        <td class="px-4 py-2">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</td>
                                        <td class="px-4 py-2">
                                            <span class="inline-block max-w-xs truncate" title="{{ $report->description }}">
                                                {{ Str::limit($report->description, 50) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($report->status == 'pending')
                                                <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">Pending</span>
                                            @elseif($report->status == 'under_review')
                                                <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">Under Review</span>
                                            @elseif($report->status == 'resolved')
                                                <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Resolved</span>
                                            @elseif($report->status == 'dismissed')
                                                <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-sm">Dismissed</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $report->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-2 flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                    onclick="viewUserReport({{ $report->id }})">
                                                View
                                            </button>
                                            
                                            @if($report->status == 'pending')
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                        onclick="updateUserStatus({{ $report->id }}, 'under_review')">
                                                    Review
                                                </button>
                                            @endif
                                            
                                            @if($report->status == 'pending' || $report->status == 'under_review')
                                                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                        onclick="updateUserStatus({{ $report->id }}, 'resolved')">
                                                    Resolve
                                                </button>
                                                <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded-lg" 
                                                        onclick="updateUserStatus({{ $report->id }}, 'dismissed')">
                                                    Dismiss
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                            No user reports found. All users are behaving well!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $userReports->links() }}
                    </div>
                </div>
                
                <!-- Report Detail Modal -->
                <div id="reportDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    
                    <!-- Modal Content -->
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 relative">
                            <!-- Close Button -->
                            <button onclick="closeReportModal()" 
                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Modal Header -->
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalReportId">Report Details</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" id="modalDate"></p>
                            </div>

                            <!-- Report Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="modal-field-shop">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Shop</p>
                                    <p class="text-md font-semibold" id="modalShopName"></p>
                                </div>
                                <div class="modal-field-user hidden">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Reported User</p>
                                    <p class="text-md font-semibold" id="modalReportedUser"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Reported By</p>
                                    <p class="text-md font-semibold" id="modalReportedBy"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Report Type</p>
                                    <p class="text-md font-semibold" id="modalReportType"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                                    <p class="text-md font-semibold" id="modalStatus"></p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</p>
                                <p class="text-md mt-1 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg" id="modalDescription"></p>
                            </div>
                            
                            <!-- Admin Notes -->
                            <div class="mb-4">
                                <label for="adminNotes" class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Admin Notes</label>
                                <textarea id="adminNotes" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-2"></textarea>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-3" id="modalActions">
                                <!-- Buttons will be dynamically inserted here based on report status -->
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

        let currentReportId = null;
        let isUserReport = false;

        // Function to view report details
        function viewReport(reportId) {
            // Set current report ID
            currentReportId = reportId;
            isUserReport = false;
            
            // Fetch report details via AJAX
            fetch(`/admin/reports/${reportId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Debug log
                    console.log('Shop report data:', data);
                    
                    if (!data || data.error) {
                        throw new Error(data.error || 'Invalid data received');
                    }
                    
                    try {
                        // Fill modal with data
                        document.getElementById('modalReportId').textContent = `Report Details: RPT-${data.id}`;
                        document.getElementById('modalDate').textContent = `Submitted on ${new Date(data.created_at).toLocaleDateString()}`;
                        
                        // Update the fields to show shop instead of user
                        document.querySelectorAll('#reportDetailModal .modal-field-shop').forEach(el => {
                            el.classList.remove('hidden');
                        });
                        document.querySelectorAll('#reportDetailModal .modal-field-user').forEach(el => {
                            el.classList.add('hidden');
                        });
                        
                        document.getElementById('modalShopName').textContent = data.shop && data.shop.name ? data.shop.name : 'Unknown Shop';
                        document.getElementById('modalReportedBy').textContent = data.user && data.user.name ? data.user.name : 'Unknown User';
                        document.getElementById('modalReportType').textContent = data.report_type ? data.report_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown Type';
                        
                        let statusText = '';
                        let statusClass = '';
                        if (data.status === 'pending') {
                            statusText = 'Pending';
                            statusClass = 'bg-yellow-200 text-yellow-800';
                        } else if (data.status === 'under_review') {
                            statusText = 'Under Review';
                            statusClass = 'bg-blue-200 text-blue-800';
                        } else if (data.status === 'resolved') {
                            statusText = 'Resolved';
                            statusClass = 'bg-green-200 text-green-800';
                        } else if (data.status === 'dismissed') {
                            statusText = 'Dismissed';
                            statusClass = 'bg-gray-200 text-gray-800';
                        }
                        
                        document.getElementById('modalStatus').innerHTML = `<span class="px-2 py-1 ${statusClass} rounded-full text-sm">${statusText}</span>`;
                        document.getElementById('modalDescription').textContent = data.description || 'No description provided';
                        document.getElementById('adminNotes').value = data.admin_notes || '';
                        
                        // Generate action buttons based on status
                        const actionsContainer = document.getElementById('modalActions');
                        actionsContainer.innerHTML = '';
                        
                        if (data.status === 'pending') {
                            actionsContainer.innerHTML = `
                                <button onclick="updateStatusWithNotes('under_review')" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Start Review</button>
                                <button onclick="updateStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                                <button onclick="updateStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                            `;
                        } else if (data.status === 'under_review') {
                            actionsContainer.innerHTML = `
                                <button onclick="updateStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                                <button onclick="updateStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                            `;
                        } else {
                            // For resolved or dismissed, just allow updating notes
                            actionsContainer.innerHTML = `
                                <button onclick="updateNotes()" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update Notes</button>
                            `;
                        }
                    } catch (err) {
                        console.error('Error rendering shop report data:', err);
                        alert(`Error displaying report details: ${err.message}. Please check the console for more information.`);
                        // Still show the modal with limited information
                        document.getElementById('modalReportId').textContent = `Report Details: RPT-${data.id || 'Unknown'}`;
                        document.getElementById('modalDescription').textContent = data.description || 'No description available';
                    }
                    
                    // Show the modal
                    document.getElementById('reportDetailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching shop report details:', error);
                    alert(`Failed to load shop report details: ${error.message || 'Unknown error'}`);
                    
                    // Close the modal if there's an error
                    closeReportModal();
                });
        }

        // Function to close the report modal
        function closeReportModal() {
            document.getElementById('reportDetailModal').classList.add('hidden');
            currentReportId = null;
            isUserReport = false;
        }

        // Function to update report status
        function updateStatus(reportId, status) {
            if (confirm(`Are you sure you want to mark this report as ${status.replace('_', ' ')}?`)) {
                // Send AJAX request to update status
                fetch(`/admin/reports/${reportId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to show updated data
                        window.location.reload();
                    } else {
                        alert('Failed to update report status');
                    }
                })
                .catch(error => {
                    console.error('Error updating report status:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
        
        // Function to update status with notes from the modal
        function updateStatusWithNotes(status) {
            if (!currentReportId) return;
            
            const adminNotes = document.getElementById('adminNotes').value;
            
            // Determine the correct endpoint based on report type
            const endpoint = isUserReport ? 
                `/admin/user-reports/${currentReportId}/status` : 
                `/admin/reports/${currentReportId}/status`;
            
            // Send AJAX request to update status and notes
            fetch(endpoint, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status,
                    admin_notes: adminNotes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal and reload the page
                    closeReportModal();
                    window.location.reload();
                } else {
                    alert('Failed to update report status');
                }
            })
            .catch(error => {
                console.error('Error updating report:', error);
                alert('An error occurred. Please try again.');
            });
        }
        
        // Function to update only notes
        function updateNotes() {
            if (!currentReportId) return;
            
            const adminNotes = document.getElementById('adminNotes').value;
            
            // Determine the correct endpoint based on report type
            const endpoint = isUserReport ? 
                `/admin/user-reports/${currentReportId}/status` : 
                `/admin/reports/${currentReportId}/status`;
            
            // Send AJAX request to update notes only
            fetch(endpoint, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    admin_notes: adminNotes,
                    status: document.getElementById('modalStatus').textContent.trim().toLowerCase()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal and reload the page
                    alert('Notes updated successfully!');
                    closeReportModal();
                } else {
                    alert('Failed to update notes');
                }
            })
            .catch(error => {
                console.error('Error updating notes:', error);
                alert('An error occurred. Please try again.');
            });
        }
        
        // Function to view user report details
        function viewUserReport(reportId) {
            // Set current report ID and mark as user report
            currentReportId = reportId;
            isUserReport = true;
            
            // Fetch user report details via AJAX
            fetch(`/admin/user-reports/${reportId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Debug log to see what data is being returned
                    console.log('User report data:', data);
                    
                    if (!data || data.error) {
                        throw new Error(data.error || 'Invalid data received');
                    }
                    
                    // Fill modal with data
                    try {
                    document.getElementById('modalReportId').textContent = `Report Details: USR-${data.id}`;
                    document.getElementById('modalDate').textContent = `Submitted on ${new Date(data.created_at).toLocaleDateString()}`;
                    
                    // Update the fields to show reported user instead of shop
                    document.querySelectorAll('#reportDetailModal .modal-field-shop').forEach(el => {
                        el.classList.add('hidden');
                    });
                    document.querySelectorAll('#reportDetailModal .modal-field-user').forEach(el => {
                        el.classList.remove('hidden');
                    });
                    
                    // Set user specific fields
                    document.getElementById('modalReportedUser').textContent = data.reportedUser && data.reportedUser.name ? data.reportedUser.name : 'Unknown User';
                    document.getElementById('modalReportedBy').textContent = data.reporter && data.reporter.name ? data.reporter.name : 'Unknown Reporter';
                    document.getElementById('modalReportType').textContent = data.report_type ? data.report_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown Type';
                    
                    let statusText = '';
                    let statusClass = '';
                    if (data.status === 'pending') {
                        statusText = 'Pending';
                        statusClass = 'bg-yellow-200 text-yellow-800';
                    } else if (data.status === 'under_review') {
                        statusText = 'Under Review';
                        statusClass = 'bg-blue-200 text-blue-800';
                    } else if (data.status === 'resolved') {
                        statusText = 'Resolved';
                        statusClass = 'bg-green-200 text-green-800';
                    } else if (data.status === 'dismissed') {
                        statusText = 'Dismissed';
                        statusClass = 'bg-gray-200 text-gray-800';
                    }
                    
                    document.getElementById('modalStatus').innerHTML = `<span class="px-2 py-1 ${statusClass} rounded-full text-sm">${statusText}</span>`;
                        document.getElementById('modalDescription').textContent = data.description || 'No description provided';
                    document.getElementById('adminNotes').value = data.admin_notes || '';
                    
                    // Generate action buttons based on status
                    const actionsContainer = document.getElementById('modalActions');
                    actionsContainer.innerHTML = '';
                    
                    if (data.status === 'pending') {
                        actionsContainer.innerHTML = `
                            <button onclick="updateUserStatusWithNotes('under_review')" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Start Review</button>
                            <button onclick="updateUserStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                            <button onclick="updateUserStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                        `;
                    } else if (data.status === 'under_review') {
                        actionsContainer.innerHTML = `
                            <button onclick="updateUserStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                            <button onclick="updateUserStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                        `;
                    } else {
                        // For resolved or dismissed, just allow updating notes
                        actionsContainer.innerHTML = `
                            <button onclick="updateUserNotes()" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update Notes</button>
                        `;
                        }
                    } catch (err) {
                        console.error('Error rendering user report data:', err);
                        alert(`Error displaying report details: ${err.message}. Please check the console for more information.`);
                        // Still show the modal with limited information
                        document.getElementById('modalReportId').textContent = `Report Details: USR-${data.id || 'Unknown'}`;
                        document.getElementById('modalDescription').textContent = data.description || 'No description available';
                    }
                    
                    // Show the modal
                    document.getElementById('reportDetailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching user report details:', error);
                    alert(`Failed to load user report details: ${error.message || 'Unknown error'}`);
                    
                    // Close the modal if there's an error
                    closeReportModal();
                });
        }
        
        // Function to update user report status
        function updateUserStatus(reportId, status) {
            if (confirm(`Are you sure you want to mark this user report as ${status.replace('_', ' ')}?`)) {
                // Send AJAX request to update status
                fetch(`/admin/user-reports/${reportId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to show updated data
                        window.location.reload();
                    } else {
                        alert('Failed to update user report status');
                    }
                })
                .catch(error => {
                    console.error('Error updating user report status:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
        
        // Function to update user report status with notes from the modal
        function updateUserStatusWithNotes(status) {
            if (!currentReportId) return;
            
            const adminNotes = document.getElementById('adminNotes').value;
            
            // Send AJAX request to update status and notes
            fetch(`/admin/user-reports/${currentReportId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status,
                    admin_notes: adminNotes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal and reload the page
                    closeReportModal();
                    window.location.reload();
                } else {
                    alert('Failed to update user report status');
                }
            })
            .catch(error => {
                console.error('Error updating user report:', error);
                alert('An error occurred. Please try again.');
            });
        }
        
        // Function to update only notes for user reports
        function updateUserNotes() {
            if (!currentReportId) return;
            
            const adminNotes = document.getElementById('adminNotes').value;
            
            // Send AJAX request to update notes only
            fetch(`/admin/user-reports/${currentReportId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    admin_notes: adminNotes,
                    status: document.getElementById('modalStatus').textContent.trim().toLowerCase()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal and reload the page
                    alert('Notes updated successfully!');
                    closeReportModal();
                } else {
                    alert('Failed to update notes');
                }
            })
            .catch(error => {
                console.error('Error updating notes:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>