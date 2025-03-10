@extends('layouts.shop')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment History</h1>
        <p class="text-gray-600">Track and manage all your paid appointments</p>
    </div>

    <!-- Payment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-gray-800">₱{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Paid Appointments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Paid Appointments</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalPaidAppointments }}</h3>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Revenue (30 days) -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Recent Revenue (30 days)</p>
                    <h3 class="text-2xl font-bold text-gray-800">₱{{ number_format($recentRevenue, 2) }}</h3>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 print:hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Filter Payments</h2>
            
            <!-- Export Options -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Export:</span>
                <a href="{{ route('shop.payments') }}?{{ http_build_query(request()->except('_token')) }}&export=csv" class="text-sm text-blue-600 hover:text-blue-800 font-medium">CSV</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('shop.payments') }}?{{ http_build_query(request()->except('_token')) }}&export=pdf" class="text-sm text-blue-600 hover:text-blue-800 font-medium">PDF</a>
                <span class="text-gray-300">|</span>
                <a href="#" onclick="window.print(); return false;" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Print</a>
            </div>
        </div>
        
        <form action="{{ route('shop.payments') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date Range Filter -->
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select id="date_range" name="date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" onchange="toggleCustomDateInputs(this.value)">
                        <option value="all" {{ $dateRange == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="last_year" {{ $dateRange == 'last_year' ? 'selected' : '' }}>Last Year</option>
                        <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div id="custom_date_container" class="{{ $dateRange == 'custom' ? 'flex' : 'hidden' }} space-x-4">
                    <div class="flex-1">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex-1">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Employee Filter -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Service Filter -->
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <select id="service_id" name="service_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('shop.payments') }}" class="mr-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Reset
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Payment History</h2>
        </div>

        @if($groupedAppointments->isEmpty())
            <div class="p-6 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-gray-500">No payment records found for the selected filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                @foreach($groupedAppointments as $month => $appointments)
                    <div class="border-b">
                        <div class="bg-gray-50 px-6 py-3">
                            <h3 class="text-md font-medium text-gray-800">{{ $month }}</h3>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($appointments as $appointment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $appointment->paid_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold text-sm overflow-hidden">
                                                    @if($appointment->user && $appointment->user->profile_photo)
                                                        <img src="{{ asset('storage/'.$appointment->user->profile_photo) }}" alt="{{ $appointment->user->name }}" class="h-10 w-10 object-cover">
                                                    @else
                                                        {{ $appointment->user ? substr($appointment->user->name, 0, 1) : 'U' }}
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->user->name ?? 'Unknown Customer' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $appointment->pet->name ?? 'Unknown Pet' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $appointment->service->name ?? $appointment->service_type ?? 'Unknown Service' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $appointment->employee->name ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ₱{{ number_format($appointment->service_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 actions-column">
                                            <a href="{{ route('shop.appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleCustomDateInputs(value) {
        const customDateContainer = document.getElementById('custom_date_container');
        if (value === 'custom') {
            customDateContainer.classList.remove('hidden');
            customDateContainer.classList.add('flex');
        } else {
            customDateContainer.classList.add('hidden');
            customDateContainer.classList.remove('flex');
        }
    }
</script>
<style>
    @media print {
        body * {
            visibility: visible;
        }
        
        .sidebar-container, 
        .navbar, 
        button, 
        .print\:hidden,
        .actions-column,
        form {
            display: none !important;
        }
        
        .app-content {
            margin-left: 0 !important;
            padding-left: 0 !important;
            width: 100% !important;
        }
        
        .content-area-wrapper {
            margin-left: 0 !important;
            padding-left: 0 !important;
            width: 100% !important;
        }
        
        table {
            width: 100% !important;
        }
    }
</style>
@endpush
@endsection 