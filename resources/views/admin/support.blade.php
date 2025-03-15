<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Support - Pet Care Connect Platform Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
                                            @if($report->image_path)
                                                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-image"></i> Evidence
                                                </span>
                                            @endif
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
                                            @if($report->image_path)
                                                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-image"></i> Evidence
                                                </span>
                                            @endif
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
                
                <!-- Appeals Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-105 transition-transform duration-300">
                    <h3 class="text-lg font-semibold mb-4">Appeals</h3>
                    
                    <!-- Filters -->
                    <form action="{{ route('admin.support') }}" method="GET" class="flex mb-4 space-x-4">
                        <input type="text" name="appeal_search" placeholder="Search appeals..." 
                               value="{{ request('appeal_search') }}"
                               class="w-full p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        
                        <select name="appeal_status" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('appeal_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('appeal_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('appeal_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        
                        <select name="appeal_type" class="p-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">Appeal Type</option>
                            <option value="shop" {{ request('appeal_type') == 'shop' ? 'selected' : '' }}>Shop Report</option>
                            <option value="user" {{ request('appeal_type') == 'user' ? 'selected' : '' }}>User Report</option>
                        </select>
                        
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-xl">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                    <!-- Appeals Table -->
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left rounded-tl-xl">Appeal ID</th>
                                    <th class="px-4 py-2 text-left">Report Type</th>
                                    <th class="px-4 py-2 text-left">Report ID</th>
                                    <th class="px-4 py-2 text-left">Submitted By</th>
                                    <th class="px-4 py-2 text-left">Reason</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left rounded-tr-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appeals ?? [] as $appeal)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-4 py-2">APL-{{ $appeal->id }}</td>
                                        <td class="px-4 py-2">
                                            @if($appeal->appealable_type == 'App\\Models\\ShopReport')
                                                <span class="px-2 py-1 bg-purple-200 text-purple-800 rounded-full text-sm">Shop Report</span>
                                            @elseif($appeal->appealable_type == 'App\\Models\\UserReport')
                                                <span class="px-2 py-1 bg-orange-200 text-orange-800 rounded-full text-sm">User Report</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($appeal->appealable_type == 'App\\Models\\ShopReport')
                                                RPT-{{ $appeal->appealable_id }}
                                            @elseif($appeal->appealable_type == 'App\\Models\\UserReport')
                                                USR-{{ $appeal->appealable_id }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($appeal->appealable_type == 'App\\Models\\ShopReport')
                                                {{ $appeal->appealable->shop->user->name ?? 'Unknown' }}
                                            @elseif($appeal->appealable_type == 'App\\Models\\UserReport')
                                                {{ $appeal->appealable->reportedUser->name ?? 'Unknown' }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="inline-block max-w-xs truncate" title="{{ $appeal->reason }}">
                                                {{ Str::limit($appeal->reason, 50) }}
                                            </span>
                                            @if($appeal->evidence_path)
                                                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-image"></i> Evidence
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($appeal->status == 'pending')
                                                <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">Pending</span>
                                            @elseif($appeal->status == 'approved')
                                                <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm">Approved</span>
                                            @elseif($appeal->status == 'rejected')
                                                <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-sm">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $appeal->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-2 flex space-x-2">
                                            @if($appeal->appealable_type == 'App\\Models\\ShopReport')
                                                <a href="#" class="btn btn-sm btn-info" 
                                                   onclick="showAppealDetailsModal('{{ $appeal->id }}')">
                                                    View
                                                </a>
                                            @else
                                                <a href="#" class="btn btn-sm btn-info" 
                                                   onclick="showAppealDetailsModal('{{ $appeal->id }}')">
                                                    View
                                                </a>
                                            @endif
                                            
                                            @if($appeal->status == 'pending')
                                                <form method="POST" action="{{ route('admin.appeals.update', $appeal->id) }}" class="inline" id="approveForm-{{ $appeal->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="button" onclick="showApproveModal({{ $appeal->id }})" 
                                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-lg">
                                                        Approve
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('admin.appeals.update', $appeal->id) }}" class="inline" id="rejectForm-{{ $appeal->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="button" onclick="showRejectModal({{ $appeal->id }})" 
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-lg">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                            No appeals found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $appeals->links() ?? '' }}
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
                            
                            <!-- Evidence Image Section -->
                            <div class="mb-4" id="evidenceImageContainer">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Evidence Image</p>
                                <div id="noImageMessage" class="text-gray-400 italic">No image evidence provided</div>
                                <div id="imageContainer" class="hidden">
                                    <img id="evidenceImage" src="" alt="Evidence Image" class="max-w-full h-auto max-h-80 rounded-lg shadow-md cursor-pointer" onclick="openImageInNewTab(this.src)">
                                </div>
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

                <!-- Resolution Explanation Modal -->
                <div id="resolutionExplanationModal" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Resolution Explanation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Please provide an explanation for resolving this report. This will be sent to the user as a notification.</p>
                                <form id="resolutionExplanationForm">
                                    <input type="hidden" id="reportIdForResolution" name="reportId">
                                    <input type="hidden" id="reportTypeForResolution" name="reportType">
                                    <div class="mb-3">
                                        <label for="resolutionExplanation" class="form-label">Explanation</label>
                                        <textarea class="form-control" id="resolutionExplanation" name="resolutionExplanation" rows="3" required></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dismissal Explanation Modal -->
                <div id="dismissalExplanationModal" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Dismissal Explanation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Please provide an explanation for dismissing this report. This will be sent to the user as a notification.</p>
                                <form id="dismissalExplanationForm">
                                    <input type="hidden" id="reportIdForDismissal" name="reportId">
                                    <input type="hidden" id="reportTypeForDismissal" name="reportType">
                                    <div class="mb-3">
                                        <label for="dismissalExplanation" class="form-label">Explanation</label>
                                        <textarea class="form-control" id="dismissalExplanation" name="dismissalExplanation" rows="3" required></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Notes Modal -->
                <div id="sendNotesModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    
                    <!-- Modal Content -->
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6 relative">
                            <!-- Close Button -->
                            <button onclick="closeSendNotesModal()" 
                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Modal Header -->
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="sendNotesTitle">Send Notes</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Send a notification to the user or shop with your message.</p>
                            </div>

                            <!-- Form -->
                            <form id="sendNotesForm" class="space-y-4">
                                <input type="hidden" id="notesReportId" name="reportId">
                                <input type="hidden" id="notesReportType" name="reportType">
                                
                                <div id="recipientSelectionContainer" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Recipient
                                    </label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio text-blue-600" name="recipient" value="reporter" checked>
                                            <span class="ml-2">Reporter</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio text-blue-600" name="recipient" value="reported">
                                            <span class="ml-2">Reported User</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="notificationTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Notification Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="notificationTitle" name="title" 
                                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" 
                                           placeholder="Enter notification title" required>
                                </div>
                                
                                <div>
                                    <label for="notificationMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Message <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="notificationMessage" name="message" rows="4" 
                                              class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" 
                                              placeholder="Enter your message" required></textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-3 mt-6">
                                    <button type="button" onclick="closeSendNotesModal()" 
                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 bg-purple-500 hover:bg-purple-700 text-white rounded-lg">
                                        Send Notification
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Appeal Approve Modal -->
                <div id="approveAppealModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    
                    <!-- Modal Content -->
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6 relative">
                            <!-- Close Button -->
                            <button onclick="closeApproveModal()" 
                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Modal Header -->
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Approve Appeal</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Approving this appeal will dismiss the associated report. Please provide your reasoning.
                                </p>
                            </div>

                            <!-- Form -->
                            <form id="approveAppealForm" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="approveAppealId" name="appeal_id" value="">
                                <input type="hidden" name="status" value="approved">
                                
                                <div>
                                    <label for="adminNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Admin Notes <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="adminNotes" name="admin_notes" rows="4" 
                                              class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" 
                                              placeholder="Explain why you are approving this appeal" required minlength="10"></textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-3 mt-6">
                                    <button type="button" onclick="closeApproveModal()" 
                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-lg">
                                        Approve Appeal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Appeal Reject Modal -->
                <div id="rejectAppealModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    
                    <!-- Modal Content -->
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6 relative">
                            <!-- Close Button -->
                            <button onclick="closeRejectModal()" 
                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Modal Header -->
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Reject Appeal</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Rejecting this appeal will keep the associated report active. Please provide your reasoning.
                                </p>
                            </div>

                            <!-- Form -->
                            <form id="rejectAppealForm" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="rejectAppealId" name="appeal_id" value="">
                                <input type="hidden" name="status" value="rejected">
                                
                                <div>
                                    <label for="rejectAdminNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Admin Notes <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="rejectAdminNotes" name="admin_notes" rows="4" 
                                              class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" 
                                              placeholder="Explain why you are rejecting this appeal" required minlength="10"></textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-3 mt-6">
                                    <button type="button" onclick="closeRejectModal()" 
                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-lg">
                                        Reject Appeal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Appeal Details Modal -->
                <div id="appealDetailsModal" class="modal fade" tabindex="-1" aria-labelledby="appealDetailsModalLabel" aria-hidden="true">
                    <!-- Modal Content -->
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <!-- Close Button -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="appealDetailsModalLabel">Appeal Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <div class="modal-body">
                                <!-- Appeal Type Info -->
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 mb-4" id="appeal-type-info">
                                    Loading appeal details...
                                </p>
                                
                                <!-- Appeal Content -->
                                <div id="appealDetailsContent" class="space-y-4">
                                    <div class="flex justify-center items-center h-40">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
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
        
        // Initialize Bootstrap modals
        const resolutionModal = new bootstrap.Modal(document.getElementById('resolutionExplanationModal'));
        const dismissalModal = new bootstrap.Modal(document.getElementById('dismissalExplanationModal'));

        // Function to handle opening image in a new tab
        function openImageInNewTab(imageUrl) {
            window.open(imageUrl, '_blank');
        }

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
                        
                        // Handle evidence image
                        if (data.has_image && data.image_url) {
                            document.getElementById('noImageMessage').classList.add('hidden');
                            document.getElementById('imageContainer').classList.remove('hidden');
                            document.getElementById('evidenceImage').src = data.image_url;
                        } else {
                            document.getElementById('noImageMessage').classList.remove('hidden');
                            document.getElementById('imageContainer').classList.add('hidden');
                        }
                        
                        // Generate action buttons based on status
                        const actionsContainer = document.getElementById('modalActions');
                        actionsContainer.innerHTML = '';
                        
                        if (data.status === 'pending') {
                            actionsContainer.innerHTML = `
                                <button onclick="updateStatusWithNotes('under_review')" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Start Review</button>
                                <button onclick="updateStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                                <button onclick="updateStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                                <button onclick="openSendNotesModal('shop')" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Send Notes</button>
                            `;
                        } else if (data.status === 'under_review') {
                            actionsContainer.innerHTML = `
                                <button onclick="updateStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                                <button onclick="updateStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                                <button onclick="openSendNotesModal('shop')" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Send Notes</button>
                            `;
                        } else {
                            // For resolved or dismissed, just allow updating notes and sending notes
                            actionsContainer.innerHTML = `
                                <button onclick="updateNotes()" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update Notes</button>
                                <button onclick="openSendNotesModal('shop')" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Send Notes</button>
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
                // Determine the correct URL based on the report type
                const url = isUserReport 
                    ? `/admin/user-reports/${reportId}/status` 
                    : `/admin/reports/${reportId}/status`;
                
                // Send AJAX request to update status
                fetch(url, {
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
            
            // Determine the correct URL based on the report type
            const url = isUserReport 
                ? `/admin/user-reports/${currentReportId}/status` 
                : `/admin/reports/${currentReportId}/status`;
                
            // Send AJAX request to update status with notes
            fetch(url, {
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
                    
                    // Handle evidence image
                    if (data.has_image && data.image_url) {
                        document.getElementById('noImageMessage').classList.add('hidden');
                        document.getElementById('imageContainer').classList.remove('hidden');
                        document.getElementById('evidenceImage').src = data.image_url;
                    } else {
                        document.getElementById('noImageMessage').classList.remove('hidden');
                        document.getElementById('imageContainer').classList.add('hidden');
                    }
                    
                    // Generate action buttons based on status
                    const actionsContainer = document.getElementById('modalActions');
                    actionsContainer.innerHTML = '';
                    
                    if (data.status === 'pending') {
                        actionsContainer.innerHTML = `
                            <button onclick="updateUserStatusWithNotes('under_review')" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Start Review</button>
                            <button onclick="updateUserStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                            <button onclick="updateUserStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                            <button onclick="openSendNotesModal('user')" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Send Notes</button>
                        `;
                    } else if (data.status === 'under_review') {
                        actionsContainer.innerHTML = `
                            <button onclick="updateUserStatusWithNotes('resolved')" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Resolve</button>
                            <button onclick="updateUserStatusWithNotes('dismissed')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dismiss</button>
                            <button onclick="openSendNotesModal('user')" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Send Notes</button>
                        `;
                    } else {
                        // For resolved or dismissed, just allow updating notes and sending notes
                        actionsContainer.innerHTML = `
                            <button onclick="updateUserNotes()" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update Notes</button>
                            <button onclick="openSendNotesModal('user')" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Send Notes</button>
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

        // Handle status change for shop reports
        $('.shop-report-status').change(function() {
            const reportId = $(this).data('report-id');
            const newStatus = $(this).val();
            const adminNotes = $(`#shop-report-notes-${reportId}`).val();
            
            if (newStatus === 'resolved') {
                // Show the resolution explanation modal
                $('#reportIdForResolution').val(reportId);
                $('#reportTypeForResolution').val('shop');
                resolutionModal.show();
                return;
            } else if (newStatus === 'dismissed') {
                // Show the dismissal explanation modal
                $('#reportIdForDismissal').val(reportId);
                $('#reportTypeForDismissal').val('shop');
                dismissalModal.show();
                return;
            }
            
            updateReportStatus(reportId, newStatus, adminNotes, 'shop');
        });

        // Handle status change for user reports
        $('.user-report-status').change(function() {
            const reportId = $(this).data('report-id');
            const newStatus = $(this).val();
            const adminNotes = $(`#user-report-notes-${reportId}`).val();
            
            if (newStatus === 'resolved') {
                // Show the resolution explanation modal
                $('#reportIdForResolution').val(reportId);
                $('#reportTypeForResolution').val('user');
                resolutionModal.show();
                return;
            } else if (newStatus === 'dismissed') {
                // Show the dismissal explanation modal
                $('#reportIdForDismissal').val(reportId);
                $('#reportTypeForDismissal').val('user');
                dismissalModal.show();
                return;
            }
            
            updateReportStatus(reportId, newStatus, adminNotes, 'user');
        });

        // Handle resolution explanation form submission
        $('#resolutionExplanationForm').submit(function(e) {
            e.preventDefault();
            
            const reportId = $('#reportIdForResolution').val();
            const reportType = $('#reportTypeForResolution').val();
            const explanation = $('#resolutionExplanation').val();
            const adminNotes = reportType === 'shop' 
                ? $(`#shop-report-notes-${reportId}`).val()
                : $(`#user-report-notes-${reportId}`).val();
            
            // Close the modal
            resolutionModal.hide();
            
            // Reset the form
            $('#resolutionExplanation').val('');
            
            // Update the report status with explanation
            updateReportStatus(reportId, 'resolved', adminNotes, reportType, explanation);
        });

        // Handle dismissal explanation form submission
        $('#dismissalExplanationForm').submit(function(e) {
            e.preventDefault();
            
            const reportId = $('#reportIdForDismissal').val();
            const reportType = $('#reportTypeForDismissal').val();
            const explanation = $('#dismissalExplanation').val();
            const adminNotes = reportType === 'shop' 
                ? $(`#shop-report-notes-${reportId}`).val()
                : $(`#user-report-notes-${reportId}`).val();
            
            // Close the modal
            dismissalModal.hide();
            
            // Reset the form
            $('#dismissalExplanation').val('');
            
            // Update the report status with explanation
            updateReportStatus(reportId, 'dismissed', adminNotes, reportType, explanation);
        });

        // Function to update report status
        function updateReportStatus(reportId, status, adminNotes, reportType, explanation = null) {
            const url = reportType === 'shop' 
                ? "{{ route('admin.reports.status.update', ['id' => ':id']) }}".replace(':id', reportId)
                : "{{ route('admin.user-reports.status.update', ['id' => ':id']) }}".replace(':id', reportId);
            
            const data = {
                status: status,
                admin_notes: adminNotes,
                _token: "{{ csrf_token() }}"
            };
            
            if (explanation) {
                data.resolution_explanation = explanation;
            }
            
            $.ajax({
                url: url,
                type: 'PUT',
                data: data,
                success: function(response) {
                    if (response.success) {
                        // Show success toast
                        toastr.success('Report status updated successfully');
                        
                        // Update UI to show the resolved/dismissed date if applicable
                        if (status === 'resolved' || status === 'dismissed') {
                            const today = new Date();
                            const formattedDate = today.toISOString().split('T')[0];
                            
                            if (reportType === 'shop') {
                                $(`#shop-report-resolved-${reportId}`).text(formattedDate);
                            } else {
                                $(`#user-report-resolved-${reportId}`).text(formattedDate);
                            }
                        }
                    } else {
                        toastr.error('Failed to update report status');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while updating the report status');
                }
            });
        }

        // Function to open send notes modal
        function openSendNotesModal(reportType) {
            // Set the current report type
            document.getElementById('notesReportId').value = currentReportId;
            document.getElementById('notesReportType').value = reportType;
            
            // Update the modal title based on report type
            const title = reportType === 'shop' ? 'Send Notes to Shop' : 'Send Notes to User';
            document.getElementById('sendNotesTitle').textContent = title;
            
            // Show/hide recipient selection for user reports
            const recipientContainer = document.getElementById('recipientSelectionContainer');
            if (reportType === 'user') {
                recipientContainer.classList.remove('hidden');
            } else {
                recipientContainer.classList.add('hidden');
            }
            
            // Clear previous inputs
            document.getElementById('notificationTitle').value = '';
            document.getElementById('notificationMessage').value = '';
            
            // Show the modal
            document.getElementById('sendNotesModal').classList.remove('hidden');
        }
        
        // Function to close send notes modal
        function closeSendNotesModal() {
            document.getElementById('sendNotesModal').classList.add('hidden');
        }
        
        // Handle send notes form submission
        document.getElementById('sendNotesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const reportId = document.getElementById('notesReportId').value;
            const reportType = document.getElementById('notesReportType').value;
            const title = document.getElementById('notificationTitle').value;
            const message = document.getElementById('notificationMessage').value;
            
            // Get the selected recipient for user reports
            let data = {
                title: title,
                message: message
            };
            
            // If this is a user report, include the recipient
            if (reportType === 'user') {
                const recipientRadios = document.getElementsByName('recipient');
                for (const radio of recipientRadios) {
                    if (radio.checked) {
                        data.recipient = radio.value;
                        break;
                    }
                }
            }
            
            // Determine the correct endpoint based on report type
            const endpoint = reportType === 'shop' 
                ? `/admin/reports/${reportId}/send-notification` 
                : `/admin/user-reports/${reportId}/send-notification`;
            
            // Send AJAX request to send notification
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    let successMessage = 'Notification sent successfully!';
                    if (reportType === 'shop') {
                        successMessage = 'Notification sent to the user who reported the shop.';
                    } else if (reportType === 'user') {
                        // Get the selected recipient type
                        const recipientRadios = document.getElementsByName('recipient');
                        let recipientType = 'reporter';
                        for (const radio of recipientRadios) {
                            if (radio.checked) {
                                recipientType = radio.value;
                                break;
                            }
                        }
                        
                        if (recipientType === 'reporter') {
                            successMessage = 'Notification sent to the user who reported.';
                        } else {
                            successMessage = 'Notification sent to the reported user.';
                        }
                    }
                    
                    alert(successMessage);
                    
                    // Close the modal
                    closeSendNotesModal();
                } else {
                    alert(data.error || 'Failed to send notification');
                }
            })
            .catch(error => {
                console.error('Error sending notification:', error);
                alert('An error occurred. Please try again.');
            });
        });
        
        // Initialize appeal modals
        let approveAppealModal = null;
        let rejectAppealModal = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Create Bootstrap modal instances for appeals
            approveAppealModal = new bootstrap.Modal(document.getElementById('approveAppealModal'));
            rejectAppealModal = new bootstrap.Modal(document.getElementById('rejectAppealModal'));
            
            // Set up form action URLs for appeal forms
            document.getElementById('approveAppealForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const appealId = document.getElementById('approveAppealId').value;
                this.action = `/admin/appeals/${appealId}/status`;
                this.submit();
            });
            
            document.getElementById('rejectAppealForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const appealId = document.getElementById('rejectAppealId').value;
                this.action = `/admin/appeals/${appealId}/status`;
                this.submit();
            });
        });
        
        // Function to show appeal approval modal
        function showApproveModal(appealId) {
            document.getElementById('approveAppealId').value = appealId;
            document.getElementById('adminNotes').value = '';
            approveAppealModal.show();
        }
        
        // Function to close appeal approval modal
        function closeApproveModal() {
            approveAppealModal.hide();
        }
        
        // Function to show appeal rejection modal
        function showRejectModal(appealId) {
            document.getElementById('rejectAppealId').value = appealId;
            document.getElementById('rejectAdminNotes').value = '';
            rejectAppealModal.show();
        }
        
        // Function to close appeal rejection modal
        function closeRejectModal() {
            rejectAppealModal.hide();
        }

        // Initialize appeals detail modal
        let appealDetailsModal = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Get the modal element
            appealDetailsModal = document.getElementById('appealDetailsModal');
        });
        
        // Function to show appeal details modal
        function showAppealDetailsModal(appealId) {
            // Show loading state
            document.getElementById('appealDetailsContent').innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Show the modal using Bootstrap's method
            const modal = new bootstrap.Modal(document.getElementById('appealDetailsModal'));
            modal.show();
            
            // Fetch appeal details
            fetch(`/admin/appeals/details/${appealId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load appeal details');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Received appeal data:", data); // Debug log
                    
                    if (!data || !data.appeal) {
                        throw new Error('Invalid appeal data structure received');
                    }
                    
                    // Get the appeal data
                    const appeal = data.appeal;
                    
                    // Set default appealable_type if not present
                    if (!appeal.appealable_type) {
                        appeal.appealable_type = data.type === 'shop' 
                            ? 'App\\Models\\ShopReport' 
                            : 'App\\Models\\UserReport';
                    }
                    
                    // Add shop_report or user_report to the appeal object based on type
                    if (data.type === 'shop') {
                        appeal.shop_report = data.report || null;
                        appeal.user_report = null;
                    } else {
                        appeal.shop_report = null;
                        appeal.user_report = data.report || null;
                    }
                    
                    // Update appeal type info
                    document.getElementById('appeal-type-info').textContent = 
                        data.type === 'shop' ? 'Shop Appeal Details' : 'User Appeal Details';
                    
                    // Update modal content with appeal details
                    document.getElementById('appealDetailsContent').innerHTML = formatAppealDetails(appeal);
                })
                .catch(error => {
                    console.error('Error loading appeal details:', error);
                    document.getElementById('appealDetailsContent').innerHTML = `
                        <div class="alert alert-danger">
                            <p class="mb-0">Error loading appeal details: ${error.message}</p>
                        </div>
                    `;
                });
        }
        
        // Function to close appeal details modal
        function closeAppealDetailsModal() {
            // This is now handled by Bootstrap automatically with data-bs-dismiss
            // Modal will be closed by the button with data-bs-dismiss attribute
        }
        
        // Function to format appeal details content
        function formatAppealDetails(appeal) {
            // Get the report data based on appeal type
            const type = appeal.appealable_type === 'App\\Models\\ShopReport' ? 'shop' : 'user';
            const report = type === 'shop' ? appeal.shop_report : appeal.user_report;
            
            // Set status classes for coloring
            let statusClass = 'text-warning';
            if (appeal.status === 'approved') {
                statusClass = 'text-success';
            } else if (appeal.status === 'rejected') {
                statusClass = 'text-danger';
            }
            
            // Check if report exists before accessing its properties
            let reportStatusClass = 'text-warning';
            if (report && report.status) {
                if (report.status === 'closed' || report.status === 'resolved') {
                    reportStatusClass = 'text-success';
                } else if (report.status === 'rejected' || report.status === 'dismissed') {
                    reportStatusClass = 'text-danger';
                }
            }
            
            // Create a safe report object if report is null
            const safeReport = report || {
                id: 'N/A',
                reporter_name: 'Unknown',
                created_at: new Date(),
                status: 'unknown',
                description: 'No report data available'
            };
            
            let html = `
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title text-primary mb-0">Appeal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Status: 
                                    <span class="fw-bold ${statusClass}">
                                        ${appeal.status.charAt(0).toUpperCase() + appeal.status.slice(1)}
                                    </span>
                                </p>
                                <p>Appeal Date: 
                                    <span class="fw-bold">${new Date(appeal.created_at).toLocaleString()}</span>
                                </p>
                                ${appeal.resolved_at ? 
                                    `<p>Resolved Date: 
                                        <span class="fw-bold">${new Date(appeal.resolved_at).toLocaleString()}</span>
                                    </p>` : ''}
                            </div>
                            <div class="col-md-6">
                                <p>Appeal Reason:</p>
                                <div class="p-2 bg-light border rounded">
                                    ${appeal.reason}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Evidence Image Section -->
                        ${appeal.has_evidence ? 
                        `<div class="row mt-3">
                            <div class="col-12">
                                <p class="fw-bold">Evidence Provided:</p>
                                <div class="text-center">
                                    <img src="${appeal.evidence_url}" alt="Appeal Evidence" class="img-fluid rounded shadow-sm" 
                                         style="max-height: 300px; cursor: pointer;" 
                                         onclick="window.open('${appeal.evidence_url}', '_blank')">
                                    <p class="text-muted small mt-1">Click image to view full size</p>
                                </div>
                            </div>
                        </div>` : 
                        `<div class="row mt-3">
                            <div class="col-12">
                                <p class="fw-bold">Evidence:</p>
                                <p class="text-muted fst-italic">No evidence was uploaded with this appeal.</p>
                            </div>
                        </div>`}
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title text-primary mb-0">${type === 'shop' ? 'Shop Report Details' : 'User Report Details'}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Report ID: <span class="fw-bold">${safeReport.id}</span></p>
                                <p>Reported By: <span class="fw-bold">${safeReport.reporter_name}</span></p>
                                <p>Report Date: <span class="fw-bold">${new Date(safeReport.created_at).toLocaleString()}</span></p>
                                <p>Report Status: 
                                    <span class="fw-bold ${reportStatusClass}">
                                        ${safeReport.status ? safeReport.status.charAt(0).toUpperCase() + safeReport.status.slice(1) : 'Unknown'}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>Report Description:</p>
                                <div class="p-2 bg-light border rounded">
                                    ${safeReport.description}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add entity information section based on type
            if (type === 'shop') {
                html += `
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-primary mb-0">Shop Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p>Shop Name: 
                                        <span class="fw-bold">${safeReport.shop ? safeReport.shop.name : 'N/A'}</span>
                                    </p>
                                    <p>Shop Owner: 
                                        <span class="fw-bold">${safeReport.shop && safeReport.shop.owner ? safeReport.shop.owner.name : 'N/A'}</span>
                                    </p>
                                    <p>Shop Email: 
                                        <span class="fw-bold">${safeReport.shop ? safeReport.shop.email : 'N/A'}</span>
                                    </p>
                                    <p>Shop Phone: 
                                        <span class="fw-bold">${safeReport.shop && safeReport.shop.phone ? safeReport.shop.phone : 'N/A'}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-primary mb-0">Reported User Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p>User Name: 
                                        <span class="fw-bold">${safeReport.reported_user ? safeReport.reported_user.name : 'N/A'}</span>
                                    </p>
                                    <p>User Email: 
                                        <span class="fw-bold">${safeReport.reported_user ? safeReport.reported_user.email : 'N/A'}</span>
                                    </p>
                                    <p>Joined Date: 
                                        <span class="fw-bold">${safeReport.reported_user ? new Date(safeReport.reported_user.created_at).toLocaleDateString() : 'N/A'}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Add action buttons if appeal status is pending
            if (appeal.status === 'pending') {
                html += `
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" onclick="showApproveModal(${appeal.id})" 
                                class="btn btn-success">
                            Approve Appeal
                        </button>
                        
                        <button type="button" onclick="showRejectModal(${appeal.id})" 
                                class="btn btn-danger">
                            Reject Appeal
                        </button>
                    </div>
                `;
            }
            
            return html;
        }
        
        // Function to set up action buttons
        function setupActionButtons(appealId) {
            // This function can be used to attach event listeners or update form attributes if needed
            // For now, we're handling this directly in the HTML
        }
    </script>
</body>
</html>