@extends('layouts.shop')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    .productivity-card {
        transition: transform 0.3s ease;
    }
    .productivity-card:hover {
        transform: translateY(-5px);
    }
    .rating-stars {
        display: inline-flex;
        align-items: center;
    }
    .rating-stars .star {
        color: #FFC107;
    }
    /* Custom styles for time off events */
    .time-off-event, 
    .fc-event-title:contains('time off') {
        background-color: #ff4d4d !important;
        border-color: #ff4d4d !important;
    }
    .fc-event[title*="time off"] .fc-event-main,
    .fc-event[title*="Time off"] .fc-event-main {
        background-color: #ff4d4d !important;
    }
</style>
@endsection

@section('content')
<div x-data="employeeManager()" class="container mx-auto px-4 py-6">
    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200 overflow-x-auto whitespace-nowrap pb-2">
        <nav class="-mb-px inline-flex space-x-8">
            <button @click="currentTab = 'list'" 
                    :class="{'border-blue-500 text-blue-600': currentTab === 'list',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'list'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Employees List
            </button>
            <button @click="currentTab = 'analytics'" 
                    :class="{'border-blue-500 text-blue-600': currentTab === 'analytics',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'analytics'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Performance Analytics
            </button>
            <button @click="currentTab = 'schedule'" 
                    :class="{'border-blue-500 text-blue-600': currentTab === 'schedule',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'schedule'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Schedule & Availability
            </button>
        </nav>
    </div>

    <!-- Employees List Tab -->
    <div x-show="currentTab === 'list'">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold">Employees</h1>
            <button @click="openAddModal()" type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors self-start md:self-center">
                Add New Employee
            </button>
        </div>

        <!-- Employees Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($employees as $employee)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <img src="{{ $employee->profile_photo_url }}" 
                             alt="{{ $employee->name }}" 
                             class="w-16 h-16 rounded-full object-cover">
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $employee->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $employee->position }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p>📧 {{ $employee->email }}</p>
                        <p>📱 {{ $employee->phone }}</p>
                        <p>🕒 {{ ucfirst($employee->employment_type) }}</p>
                        @if($employee->bio)
                            <p class="text-gray-600 mt-2">{{ $employee->bio }}</p>
                        @endif

                        <!-- Services Section -->
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Assigned Services</h4>
                            @if($employee->services && $employee->services->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($employee->services as $service)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $service->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">No services assigned</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button @click="editEmployee({{ $employee->id }})" 
                                class="text-blue-600 hover:text-blue-800">
                            Edit
                        </button>
                        <button @click="confirmDelete({{ $employee->id }})" 
                                class="text-red-600 hover:text-red-800">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8">
                <p class="text-gray-500">No employees found. Add your first employee to get started!</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Performance Analytics Tab -->
    <div x-show="currentTab === 'analytics'" x-cloak>
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Employee Performance Analytics</h1>
            <p class="text-gray-600">Track employee productivity, completed appointments, and customer ratings</p>
        </div>

        <!-- Date Filter -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="period-filter" class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
                    <select id="period-filter" x-model="analyticsPeriod" @change="loadAnalyticsData()" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
                <div>
                    <label for="service-filter" class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                    <select id="service-filter" x-model="analyticsServiceType" @change="loadAnalyticsData()" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Services</option>
                        <option value="grooming">Grooming Only</option>
                        <option value="veterinary">Veterinary Only</option>
                        <option value="boarding">Boarding Only</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-800">Total Completed</h3>
                <p class="text-3xl font-bold text-blue-600 mt-2" x-text="totalStats.completed">0</p>
                <p class="text-sm text-gray-500 mt-1">appointments completed</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-800">Revenue Generated</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">₱<span x-text="totalStats.revenue.toLocaleString()">0</span></p>
                <p class="text-sm text-gray-500 mt-1">total revenue</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                <h3 class="text-lg font-semibold text-gray-800">Average Rating</h3>
                <p class="text-3xl font-bold text-yellow-600 mt-2" x-text="totalStats.avgRating.toFixed(1)">0.0</p>
                <p class="text-sm text-gray-500 mt-1">average customer rating</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-gray-800">Avg. Completion Time</h3>
                <p class="text-3xl font-bold text-purple-600 mt-2" x-text="totalStats.avgTime">0</p>
                <p class="text-sm text-gray-500 mt-1">minutes per appointment</p>
            </div>
        </div>

        <!-- Performance Comparison Section -->
        <div class="mb-8" x-show="currentTab === 'analytics'">
            <h2 class="text-xl font-semibold mb-4">Employee Performance Comparison</h2>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="h-80 relative" id="chart-container">
                    <div x-show="!employeeAnalytics.length" class="absolute inset-0 flex items-center justify-center">
                        <p class="text-gray-500">No employee data available for comparison</p>
                    </div>
                    <canvas x-ref="chartCanvas" id="comparison-chart" class="h-full w-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Employee Productivity Cards -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Individual Employee Productivity</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="employee in employeeAnalytics" :key="employee.id">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden productivity-card">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <img :src="employee.profile_photo_url" 
                                     :alt="employee.name" 
                                     class="w-16 h-16 rounded-full object-cover">
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="employee.name"></h3>
                                    <p class="text-sm text-gray-600" x-text="employee.position"></p>
                                </div>
                            </div>
                            
                            <!-- Analytics Data -->
                            <div class="space-y-4">
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Completed Appointments:</span>
                                    <span class="font-semibold text-blue-600" x-text="employee.completed"></span>
                                </div>
                                
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Revenue Generated:</span>
                                    <span class="font-semibold text-green-600">₱<span x-text="employee.revenue.toLocaleString()"></span></span>
                                </div>
                                
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-600">Average Rating:</span>
                                    <div class="flex items-center">
                                        <span class="font-semibold text-yellow-600 mr-2" x-text="employee.avg_rating.toFixed(1)"></span>
                                        <div class="rating-stars">
                                            <template x-for="i in 5" :key="i">
                                                <span class="star" x-html="i <= Math.round(employee.avg_rating) ? '★' : '☆'"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Avg. Completion Time:</span>
                                    <span class="font-semibold text-purple-600" x-text="employee.avg_completion_time + ' mins'"></span>
                                </div>
                            </div>
                            
                            <!-- View Details Button -->
                            <button @click="showEmployeeDetails(employee.id)" 
                                    class="mt-4 w-full bg-blue-50 text-blue-600 py-2 rounded-md text-sm font-medium hover:bg-blue-100 transition-colors">
                                View Detailed Stats
                            </button>
                        </div>
                    </div>
                </template>
                
                <!-- Placeholder card if no employees -->
                <div x-show="employeeAnalytics.length === 0" class="col-span-full bg-white p-8 rounded-lg shadow-sm text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-4 text-lg text-gray-500">No employee performance data available.</p>
                    <p class="text-sm text-gray-400 mt-1">Analytics will appear once appointments are completed.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule & Availability Tab -->
    <div x-show="currentTab === 'schedule'" x-cloak>
        <div class="bg-white rounded-lg shadow-md">
            <!-- Header Section with Controls -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Work Schedule</h2>
                        <p class="text-sm text-gray-600">Manage employee shifts and availability</p>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                        <select x-model="selectedEmployee" 
                                @change="loadEvents" 
                                class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        <div class="flex space-x-2">
                            <button @click="calendarView = 'timeGridWeek'" 
                                    :class="{'bg-blue-600 text-white': calendarView === 'timeGridWeek', 
                                            'bg-gray-100 text-gray-700': calendarView !== 'timeGridWeek'}"
                                    class="px-4 py-2 rounded-md transition-colors duration-150">
                                Week
                            </button>
                            <button @click="calendarView = 'dayGridMonth'" 
                                    :class="{'bg-blue-600 text-white': calendarView === 'dayGridMonth', 
                                            'bg-gray-100 text-gray-700': calendarView !== 'dayGridMonth'}"
                                    class="px-4 py-2 rounded-md transition-colors duration-150">
                                Month
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-12 gap-6 p-6">
                <!-- Calendar Section (8 columns) -->
                <div class="col-span-12 lg:col-span-8">
                    <div id="calendar" class="bg-white rounded-lg shadow-sm p-4 min-h-[600px]"></div>
                </div>

                <!-- Sidebar Settings (4 columns) -->
                <div class="col-span-12 lg:col-span-4 space-y-6">
                    <!-- Availability Settings Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Availability Settings</h3>
                        <div x-show="!selectedEmployee" class="text-center py-6 bg-gray-50 rounded-lg">
                            <p class="text-gray-600 mb-2">Please select an employee to manage their availability</p>
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div x-show="selectedEmployee" class="space-y-4">
                            <div x-show="availabilityLoading" class="flex justify-center py-6">
                                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div x-show="!availabilityLoading">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="font-medium text-gray-900">{{ $day }}</span>
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   data-day="{{ strtolower($day) }}"
                                                   class="day-available rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                                   :checked="employeeAvailability['{{ strtolower($day) }}'] ? employeeAvailability['{{ strtolower($day) }}'].is_available : true">
                                            <span class="ml-2 text-sm text-gray-600">Available</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <select data-day="{{ strtolower($day) }}" 
                                                data-type="start"
                                                class="time-select flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            @foreach(range(6, 22) as $hour)
                                                <option value="{{ sprintf('%02d:00', $hour) }}" 
                                                        x-bind:selected="employeeAvailability['{{ strtolower($day) }}'] && employeeAvailability['{{ strtolower($day) }}'].start_time === '{{ sprintf('%02d:00:00', $hour) }}'">
                                                    {{ sprintf('%02d:00', $hour) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-gray-500">to</span>
                                        <select data-day="{{ strtolower($day) }}" 
                                                data-type="end"
                                                class="time-select flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            @foreach(range(6, 22) as $hour)
                                                <option value="{{ sprintf('%02d:00', $hour) }}" 
                                                        x-bind:selected="employeeAvailability['{{ strtolower($day) }}'] && employeeAvailability['{{ strtolower($day) }}'].end_time === '{{ sprintf('%02d:00:00', $hour) }}'">
                                                    {{ sprintf('%02d:00', $hour) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button @click="saveAvailability()" 
                                    class="mt-6 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-150">
                                Save Availability
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Time Off Management - Moved below calendar -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Time Off Management</h3>
                    <button @click="showTimeOffModal = true" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-150">
                        Add Time Off
                    </button>
                </div>
                
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(timeOff, index) in timeOffList" :key="timeOff.id || index">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="timeOff.employee_name || 'Unknown'"></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div x-text="timeOff.start_date ? new Date(timeOff.start_date).toLocaleDateString() : 'Invalid date'"></div>
                                            <div class="text-gray-500" x-text="timeOff.end_date ? 'to ' + new Date(timeOff.end_date).toLocaleDateString() : ''"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                        <button @click="deleteTimeOff(timeOff.id)" 
                                                class="text-red-600 hover:text-red-900 focus:outline-none"
                                                x-show="timeOff.id">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="timeOffList.length === 0">
                                <td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500">
                                    No time off entries found. Click "Add Time Off" to create one.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details Modal -->
    <div x-show="showEmployeeDetailsModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
        <div class="fixed inset-0 bg-black opacity-50" @click="showEmployeeDetailsModal = false"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-xl font-semibold text-gray-900" x-text="selectedEmployee ? selectedEmployee.name + ' - Detailed Performance' : 'Employee Details'"></h3>
                <button @click="showEmployeeDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <template x-if="selectedEmployee">
                    <div>
                        <!-- Employee Info Header -->
                        <div class="flex items-center mb-6">
                            <img :src="selectedEmployee.profile_photo_url" :alt="selectedEmployee.name" class="w-16 h-16 rounded-full object-cover">
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold" x-text="selectedEmployee.name"></h4>
                                <p class="text-gray-600" x-text="selectedEmployee.position"></p>
                            </div>
                            <div class="ml-auto text-right">
                                <div class="text-sm text-gray-600">Overall Rating</div>
                                <div class="flex items-center justify-end mt-1">
                                    <span class="text-2xl font-bold text-yellow-600 mr-2" x-text="selectedEmployee.avg_rating.toFixed(1)"></span>
                                    <div class="rating-stars">
                                        <template x-for="i in 5" :key="i">
                                            <span class="star text-lg" x-html="i <= Math.round(selectedEmployee.avg_rating) ? '★' : '☆'"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm text-blue-800">Completed Appointments</div>
                                <div class="text-2xl font-bold text-blue-600" x-text="selectedEmployee.completed"></div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-800">Revenue Generated</div>
                                <div class="text-2xl font-bold text-green-600">₱<span x-text="selectedEmployee.revenue.toLocaleString()"></span></div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-sm text-purple-800">Avg. Completion Time</div>
                                <div class="text-2xl font-bold text-purple-600" x-text="selectedEmployee.avg_completion_time + ' mins'"></div>
                            </div>
                        </div>

                        <!-- Recent Appointments Section -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold mb-3">Recent Appointments</h4>
                            <div class="bg-white border rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="appointment in selectedEmployee.recent_appointments" :key="appointment.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm" x-text="formatDate(appointment.date)"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm" x-text="appointment.service_type"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">₱<span x-text="appointment.revenue.toLocaleString()"></span></td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <span class="text-yellow-600 mr-1" x-text="appointment.rating ? appointment.rating.toFixed(1) : '-'"></span>
                                                        <div class="rating-stars" x-show="appointment.rating">
                                                            <template x-for="i in 5" :key="i">
                                                                <span class="star text-sm" x-html="i <= Math.round(appointment.rating) ? '★' : '☆'"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="!selectedEmployee.recent_appointments || selectedEmployee.recent_appointments.length === 0">
                                            <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">No recent appointments found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Customer Reviews Section -->
                        <div>
                            <h4 class="text-lg font-semibold mb-3">Customer Reviews</h4>
                            <div class="space-y-4">
                                <template x-for="(review, index) in selectedEmployee.reviews" :key="index">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex justify-between mb-2">
                                            <div class="rating-stars">
                                                <template x-for="i in 5" :key="i">
                                                    <span class="star" x-html="i <= review.rating ? '★' : '☆'"></span>
                                                </template>
                                            </div>
                                            <span class="text-sm text-gray-500" x-text="formatDate(review.date)"></span>
                                        </div>
                                        <p class="text-gray-700" x-text="review.review || 'No comment provided'"></p>
                                        <p class="text-sm text-gray-500 mt-2" x-text="'- ' + review.customer_name"></p>
                                    </div>
                                </template>
                                <div x-show="!selectedEmployee.reviews || selectedEmployee.reviews.length === 0" class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                                    No customer reviews yet
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Employee Modal -->
    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg z-10">
            <h2 class="text-xl font-bold mb-4" x-text="editingEmployee ? 'Edit Employee' : 'Add New Employee'"></h2>
            <form @submit.prevent="saveEmployee">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="name" 
                           type="text" 
                           x-model="formData.name"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="email" 
                           type="email" 
                           x-model="formData.email"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                        Phone
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="phone" 
                           type="text" 
                           x-model="formData.phone"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                        Position
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="position" 
                           type="text" 
                           x-model="formData.position"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="employment_type">
                        Employment Type
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="employment_type"
                            x-model="formData.employment_type"
                            required>
                        <option value="full-time">Full Time</option>
                        <option value="part-time">Part Time</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_photo">
                        Profile Photo
                    </label>
                    <input type="file" 
                           id="profile_photo" 
                           @change="handlePhotoUpload"
                           accept="image/*"
                           class="w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                        Bio
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                              id="bio" 
                              x-model="formData.bio"
                              rows="3"></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <span x-text="editingEmployee ? 'Update Employee' : 'Add Employee'"></span>
                    </button>
                    <button @click="closeModal" type="button" class="text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Time Off Modal -->
    <div x-show="showTimeOffModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg p-8 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">Set Employee Time Off</h3>
            <form @submit.prevent="submitTimeOff">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee</label>
                        <select x-model="timeOffForm.employee_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" x-model="timeOffForm.start_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" x-model="timeOffForm.end_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea x-model="timeOffForm.reason" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Enter reason for time off"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showTimeOffModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Save Time Off
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Event Modal -->
    <div x-show="showEventModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="eventModalTitle"></h3>
                    
                    <form @submit.prevent="saveEvent">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" x-model="eventData.title" class="mt-1 block w-full rounded-md border-gray-300" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start</label>
                                <input type="datetime-local" x-model="eventData.start" class="mt-1 block w-full rounded-md border-gray-300" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End</label>
                                <input type="datetime-local" x-model="eventData.end" class="mt-1 block w-full rounded-md border-gray-300" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select x-model="eventData.employee_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select x-model="eventData.type" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="time_off">Time Off</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea x-model="eventData.notes" class="mt-1 block w-full rounded-md border-gray-300" rows="3"></textarea>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="saveEvent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button @click="closeEventModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('employeeManager', () => ({
        currentTab: 'list',
        showModal: false,
        showTimeOffModal: false,
        editingEmployee: null,
        selectedEmployee: '',
        calendarView: 'timeGridWeek',
        timeOffList: [],
        timeOffForm: {
            employee_id: '',
            start_date: '',
            end_date: '',
            reason: ''
        },
        calendar: null,
        formData: {
            name: '',
            email: '',
            phone: '',
            position: '',
            employment_type: 'full-time',
            profile_photo: null,
            bio: ''
        },
        showEventModal: false,
        eventModalTitle: 'Add Event',
        eventData: {
            id: null,
            title: '',
            start: '',
            end: '',
            employee_id: '',
            type: 'time_off',
            notes: ''
        },
        analyticsPeriod: '30',
        analyticsServiceType: '',
        totalStats: {
            completed: 0,
            revenue: 0,
            avgRating: 0,
            avgTime: 0
        },
        employeeAnalytics: [],
        showEmployeeDetailsModal: false,
        selectedEmployee: null,
        comparisonChart: null,
        availabilityLoading: false,
        employeeAvailability: {},

        init() {
            console.log('Component initialized'); // Debug log
            this.loadTimeOffList(); // Ensure timeOffList is loaded on initialization
            
            // Initialize analytics when the tab is loaded
            if (this.currentTab === 'analytics') {
                this.loadAnalyticsData();
            }
            
            // Initialize schedule calendar
            if (this.currentTab === 'schedule') {
                this.$nextTick(() => {
                    if (!this.calendar && document.getElementById('calendar')) {
                        this.initializeCalendar();
                    }
                });
            }

            // Watch for tab changes
            this.$watch('currentTab', (value) => {
                console.log('Tab changed to:', value); // Debug log
                if (value === 'schedule') {
                    this.$nextTick(() => {
                        if (!this.calendar && document.getElementById('calendar')) {
                            this.initializeCalendar();
                        } else if (this.calendar) {
                            this.calendar.refetchEvents();
                        }
                        // Always reload time off list when switching to schedule tab
                        this.loadTimeOffList();
                    });
                } else if (value === 'analytics') {
                    // Load analytics data when switching to analytics tab
                    this.loadAnalyticsData();
                }
            });

            // Watch for analytics period or service type changes
            this.$watch('analyticsPeriod', () => {
                if (this.currentTab === 'analytics') {
                    this.loadAnalyticsData();
                }
            });

            this.$watch('analyticsServiceType', () => {
                if (this.currentTab === 'analytics') {
                    this.loadAnalyticsData();
                }
            });
            
            // Watch for calendar view changes
            this.$watch('calendarView', (value) => {
                if (this.calendar) {
                    this.calendar.changeView(value);
                    // Force refresh events when view changes
                    setTimeout(() => {
                        this.calendar.refetchEvents();
                    }, 100);
                }
            });
            
            // Watch for selected employee changes
            this.$watch('selectedEmployee', (value) => {
                if (this.calendar) {
                    this.loadEvents();
                }
                
                if (value && this.currentTab === 'schedule') {
                    this.loadEmployeeAvailability();
                } else {
                    this.employeeAvailability = {};
                }
            });
        },

        initializeCalendar() {
            if (this.calendar) {
                this.calendar.destroy();
            }

            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: this.calendarView,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                selectable: true,
                editable: true,
                eventClick: this.handleEventClick.bind(this),
                select: this.handleDateSelect.bind(this),
                eventDrop: this.handleEventDrop.bind(this),
                eventResize: this.handleEventResize.bind(this),
                events: (info, successCallback, failureCallback) => this.loadEvents(info, successCallback),
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                slotDuration: '00:30:00',
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                eventDidMount: function(info) {
                    // Add custom styling to time off events
                    if ((info.event.extendedProps && info.event.extendedProps.type === 'time_off') || 
                        info.event.title.toLowerCase().includes('time off')) {
                        info.el.style.backgroundColor = '#ff4d4d';
                        info.el.style.borderColor = '#ff4d4d';
                        
                        // Add a hover tooltip with reason
                        if (info.event.extendedProps && info.event.extendedProps.notes) {
                            info.el.title = info.event.extendedProps.notes;
                        }
                    }
                }
            });

            this.calendar.render();
        },
        
        initializeComparisonChart() {
            console.log('Chart initialization started');
            
            // Ensure we're on the analytics tab and have data
            if (this.currentTab !== 'analytics') {
                console.log('Not on analytics tab, skipping chart');
                return;
            }
            
            if (!this.employeeAnalytics || this.employeeAnalytics.length === 0) {
                console.log('No employee data available, skipping chart');
                return;
            }
            
            console.log('Employee data available:', this.employeeAnalytics.length, 'employees');
            
            // Clean up any existing chart
            if (this.comparisonChart) {
                console.log('Destroying existing chart');
                this.comparisonChart.destroy();
                this.comparisonChart = null;
            }
            
            // Manually create and insert a new canvas to avoid any potential issues
            const container = document.getElementById('chart-container');
            if (!container) {
                console.error('Chart container not found');
                return;
            }
            
            // Ensure any existing canvas is removed
            const existingCanvas = document.getElementById('comparison-chart');
            if (existingCanvas) {
                existingCanvas.remove();
            }
            
            // Create a fresh canvas
            const canvas = document.createElement('canvas');
            canvas.id = 'comparison-chart';
            canvas.classList.add('h-full', 'w-full');
            container.appendChild(canvas);
            
            try {
                // Get canvas context
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    console.error('Failed to get canvas context');
                    return;
                }
                
                // Log data values for debugging
                const labels = this.employeeAnalytics.map(employee => employee.name);
                const completedData = this.employeeAnalytics.map(employee => employee.completed);
                const revenueData = this.employeeAnalytics.map(employee => employee.revenue);
                const ratingData = this.employeeAnalytics.map(employee => employee.avg_rating);
                
                console.log('Chart data prepared:', {
                    labels,
                    completedData,
                    revenueData,
                    ratingData
                });
                
                // Create chart
                this.comparisonChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Completed Appointments',
                                data: completedData,
                                backgroundColor: 'rgba(66, 153, 225, 0.5)',
                                borderColor: 'rgb(66, 153, 225)',
                                borderWidth: 1
                            },
                            {
                                label: 'Revenue (₱)',
                                data: revenueData,
                                backgroundColor: 'rgba(72, 187, 120, 0.5)',
                                borderColor: 'rgb(72, 187, 120)',
                                borderWidth: 1,
                                hidden: true
                            },
                            {
                                label: 'Average Rating',
                                data: ratingData,
                                backgroundColor: 'rgba(245, 158, 11, 0.5)',
                                borderColor: 'rgb(245, 158, 11)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Employee Performance Metrics Comparison',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (label.includes('Revenue')) {
                                                label += '₱' + context.parsed.y.toLocaleString();
                                            } else if (label.includes('Rating')) {
                                                label += context.parsed.y.toFixed(1) + ' / 5';
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
                
                console.log('Chart successfully initialized');
            } catch (error) {
                console.error('Error creating chart:', error);
            }
        },

        loadEvents(info, successCallback) {
            const params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr,
                employee_id: this.selectedEmployee || '',
                shop_id: document.querySelector('meta[name="shop-id"]').content
            });

            fetch(`/shop/schedule/events?${params}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && Array.isArray(data.events)) {
                        const formattedEvents = data.events.map(event => ({
                            id: event.id,
                            title: event.title || 'Untitled Event',
                            start: new Date(event.start).toISOString(),
                            end: new Date(event.end).toISOString(),
                            color: event.type === 'time_off' || event.title?.toLowerCase().includes('time off') ? '#ff4d4d' : '#4299e1',
                            employee_id: event.employee_id,
                            type: event.type === 'time_off' || event.title?.toLowerCase().includes('time off') ? 'time_off' : (event.type || 'shift'),
                            status: event.status || 'active',
                            extendedProps: {
                                employee_id: event.employee_id,
                                type: event.type === 'time_off' || event.title?.toLowerCase().includes('time off') ? 'time_off' : (event.type || 'shift'),
                                notes: event.notes || event.reason || ''
                            }
                        }));
                        
                        // Add time off entries if they're not already included
                        if (this.timeOffList && this.timeOffList.length > 0) {
                            const timeOffEvents = this.timeOffList
                                .filter(timeOff => {
                                    // Skip if employee filter is active and this is for a different employee
                                    if (this.selectedEmployee && 
                                        timeOff.employee_id && 
                                        timeOff.employee_id != this.selectedEmployee) {
                                        return false;
                                    }
                                    return true;
                                })
                                .map(timeOff => ({
                                    id: `timeoff_${timeOff.id}`,
                                    title: `Time Off - ${timeOff.employee_name}`,
                                    start: new Date(timeOff.start_date).toISOString(),
                                    end: new Date(timeOff.end_date).toISOString(),
                                    color: '#ff4d4d', // Force red color
                                    backgroundColor: '#ff4d4d',
                                    borderColor: '#ff4d4d',
                                    classNames: ['time-off-event'],
                                    allDay: true,
                                    employee_id: timeOff.employee_id,
                                    type: 'time_off',
                                    status: 'active',
                                    extendedProps: {
                                        employee_id: timeOff.employee_id,
                                        type: 'time_off',
                                        notes: timeOff.reason || 'Time off'
                                    }
                                }));
                            
                            // Combine events, avoiding duplicates
                            const eventIds = new Set(formattedEvents.map(e => e.id));
                            for (const event of timeOffEvents) {
                                if (!eventIds.has(event.id)) {
                                    formattedEvents.push(event);
                                }
                            }
                        }
                        
                        if (typeof successCallback === 'function') {
                            successCallback(formattedEvents);
                        } else if (this.calendar) {
                            this.calendar.removeAllEvents();
                            this.calendar.addEventSource(formattedEvents);
                        }
                    } else {
                        console.error('Invalid events data format:', data);
                        if (typeof successCallback === 'function') {
                            successCallback([]);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    if (typeof successCallback === 'function') {
                        successCallback([]);
                    }
                });
        },

        handleDateSelect(info) {
            const start = info.start ? this.formatDateTimeForInput(info.start) : '';
            const end = info.end ? this.formatDateTimeForInput(info.end) : '';
            
            this.eventData = {
                id: null,
                title: '',
                start: start,
                end: end,
                employee_id: this.selectedEmployee || '',
                type: 'time_off',
                notes: ''
            };
            this.eventModalTitle = 'Add Event';
            this.showEventModal = true;
        },

        handleEventClick(info) {
            const start = info.event.start ? this.formatDateTimeForInput(info.event.start) : '';
            const end = info.event.end ? this.formatDateTimeForInput(info.event.end) : '';
            
            this.eventData = {
                id: info.event.id,
                title: info.event.title,
                start: start,
                end: end,
                employee_id: info.event.extendedProps.employee_id || '',
                type: info.event.extendedProps.type || 'time_off',
                notes: info.event.extendedProps.notes || ''
            };
            this.eventModalTitle = 'Edit Event';
            this.showEventModal = true;
        },

        formatDateTimeForInput(date) {
            if (!(date instanceof Date)) {
                date = new Date(date);
            }
            // Ensure the date is valid
            if (isNaN(date.getTime())) {
                console.error('Invalid date:', date);
                return '';
            }
            // Format as YYYY-MM-DDTHH:mm
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        handleEventDrop(info) {
            this.updateEvent(info.event);
        },

        handleEventResize(info) {
            this.updateEvent(info.event);
        },

        updateEvent(event) {
            fetch(`/shop/schedule/events/${event.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    start: event.startStr,
                    end: event.endStr
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    this.calendar.refetchEvents();
                }
            })
            .catch(error => {
                console.error('Error updating event:', error);
                this.calendar.refetchEvents();
            });
        },

        saveEvent() {
            if (!this.eventData.title || !this.eventData.start || !this.eventData.end || !this.eventData.employee_id) {
                alert('Please fill in all required fields');
                return;
            }

            const eventData = {
                ...this.eventData,
                start: new Date(this.eventData.start).toISOString(),
                end: new Date(this.eventData.end).toISOString(),
                shop_id: document.querySelector('meta[name="shop-id"]').content
            };

            const url = eventData.id 
                ? `/shop/schedule/events/${eventData.id}`
                : '/shop/schedule/events';
            
            const method = eventData.id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(eventData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.calendar.refetchEvents();
                    this.closeEventModal();
                } else {
                    throw new Error(data.error || 'Failed to save event');
                }
            })
            .catch(error => {
                console.error('Error saving event:', error);
                alert('Failed to save event: ' + error.message);
            });
        },

        closeEventModal() {
            this.showEventModal = false;
            this.eventData = {
                id: null,
                title: '',
                start: '',
                end: '',
                employee_id: '',
                type: 'time_off',
                notes: ''
            };
        },

        async submitTimeOff() {
            if (!this.timeOffForm.employee_id || !this.timeOffForm.start_date || !this.timeOffForm.end_date) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                // Create a payload with the form data
                const payload = {
                    employee_id: this.timeOffForm.employee_id,
                    start_date: this.timeOffForm.start_date,
                    end_date: this.timeOffForm.end_date,
                    reason: this.timeOffForm.reason || 'Time off',
                    shop_id: document.querySelector('meta[name="shop-id"]')?.content
                };

                console.log('Submitting time off with payload:', payload);

                const response = await fetch('/shop/schedule/time-off', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to set employee time off');
                }

                if (data.success) {
                    // Clear form and close modal
                    this.timeOffForm = {
                        employee_id: '',
                        start_date: '',
                        end_date: '',
                        reason: ''
                    };
                    this.showTimeOffModal = false;
                    
                    // Refresh data
                    this.loadTimeOffList();
                    
                    // Refresh calendar events if calendar exists
                    if (this.calendar) {
                        this.calendar.refetchEvents();
                    }
                    
                    alert('Time off successfully added!');
                } else {
                    throw new Error(data.message || 'Failed to set employee time off');
                }
            } catch (error) {
                console.error('Error setting time off:', error);
                alert(error.message || 'Failed to set employee time off');
            }
        },

        async loadTimeOffList() {
            try {
                // Show loading state if needed
                // this.timeOffLoading = true;

                // Fetch time off data with shop ID as a parameter if available
                const shopId = document.querySelector('meta[name="shop-id"]')?.content;
                const url = shopId 
                    ? `/shop/schedule/time-off?shop_id=${shopId}` 
                    : '/shop/schedule/time-off';
                
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success === false) {
                    throw new Error(data.message || 'Failed to load time off data');
                }

                // Check if we have the expected data structure
                if (!data.timeOff) {
                    console.warn('No timeOff data in response:', data);
                    this.timeOffList = [];
                    return;
                }

                // Map the data to our expected format
                this.timeOffList = Array.isArray(data.timeOff) ? data.timeOff.map(item => {
                    // Get the employee name - handle both object references and simple employee_name fields
                    let employeeName = 'Unknown Employee';
                    if (item.employee && typeof item.employee === 'object' && item.employee.name) {
                        employeeName = item.employee.name;
                    } else if (item.employee_name) {
                        employeeName = item.employee_name;
                    }

                    return {
                        id: item.id,
                        employee_id: item.employee_id || null,
                        employee_name: employeeName,
                        start_date: item.start || item.start_date,
                        end_date: item.end || item.end_date,
                        reason: item.reason || 'Time off'
                    };
                }) : [];

                console.log('Time off data loaded successfully:', this.timeOffList.length, 'entries');
            } catch (error) {
                console.error('Failed to load time off list:', error);
                this.timeOffList = [];
                // Show error notification if needed
                // alert('Failed to load time off data: ' + error.message);
            } finally {
                // Clear loading state if needed
                // this.timeOffLoading = false;
            }
        },

        async deleteTimeOff(id) {
            if (!confirm('Are you sure you want to delete this time off period?')) {
                return;
            }

            try {
                const response = await fetch(`/shop/schedule/time-off/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to delete time off');
                }

                const data = await response.json();
                
                if (data.success) {
                    // Remove the deleted item from the local list to update UI immediately
                    this.timeOffList = this.timeOffList.filter(item => item.id !== id);
                    
                    // Reload the list from server to ensure data consistency
                    this.loadTimeOffList();
                    
                    // Refresh calendar if available
                    if (this.calendar) {
                        this.calendar.refetchEvents();
                    }
                    
                    alert('Time off period has been deleted successfully.');
                } else {
                    throw new Error(data.message || 'Failed to delete time off');
                }
            } catch (error) {
                console.error('Error deleting time off:', error);
                alert('Failed to delete time off: ' + error.message);
            }
        },

        openAddModal() {
            this.editingEmployee = null;
            this.resetForm();
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.formData = {
                name: '',
                email: '',
                phone: '',
                position: '',
                employment_type: 'full-time',
                profile_photo: null,
                bio: ''
            };
        },

        handlePhotoUpload(event) {
            this.formData.profile_photo = event.target.files[0];
        },

        async editEmployee(id) {
            try {
                const response = await fetch(`/shop/employees/${id}`);
                const data = await response.json();
                
                if (data.success) {
                    this.editingEmployee = id;
                    this.formData = {
                        name: data.employee.name,
                        email: data.employee.email,
                        phone: data.employee.phone,
                        position: data.employee.position,
                        employment_type: data.employee.employment_type,
                        bio: data.employee.bio || '',
                        profile_photo: null
                    };
                    this.showModal = true;
                }
            } catch (error) {
                console.error('Error fetching employee:', error);
                alert('Failed to load employee data');
            }
        },

        async saveEmployee() {
            try {
                const formData = new FormData();
                Object.keys(this.formData).forEach(key => {
                    if (this.formData[key] !== null) {
                        formData.append(key, this.formData[key]);
                    }
                });

                const url = this.editingEmployee 
                    ? `/shop/employees/${this.editingEmployee}` 
                    : '/shop/employees';
                
                if (this.editingEmployee) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: 'POST', // Always use POST, let Laravel handle method spoofing
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to save employee');
                }

                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to save employee');
                }
            } catch (error) {
                console.error('Error saving employee:', error);
                alert(error.message || 'Failed to save employee');
            }
        },

        async confirmDelete(id) {
            if (!confirm('Are you sure you want to remove this employee?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');

                const response = await fetch(`/shop/employees/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete employee');
                }

                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to delete employee');
                }
            } catch (error) {
                console.error('Error deleting employee:', error);
                alert(error.message || 'Failed to delete employee');
            }
        },
        
        async loadAnalyticsData() {
            try {
                console.log('Loading analytics data...');
                
                const response = await fetch('/shop/employees/analytics', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        period: this.analyticsPeriod,
                        service_type: this.analyticsServiceType
                    })
                });

                if (!response.ok) {
                    throw new Error(`Failed to load analytics data: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    console.log('Analytics data received successfully');
                    
                    this.totalStats = data.stats.total;
                    this.employeeAnalytics = data.stats.employees;
                    
                    console.log(`Data loaded: ${this.employeeAnalytics.length} employees`);
                    
                    // Initialize chart after data is loaded and we're on the right tab
                    if (this.currentTab === 'analytics') {
                        console.log('On analytics tab, initializing chart');
                        // Give the DOM time to update
                        setTimeout(() => {
                            this.initializeComparisonChart();
                        }, 100);
                    }
                } else {
                    throw new Error(data.error || 'Failed to load analytics data');
                }
            } catch (error) {
                console.error('Error loading analytics data:', error);
                // Default values if data loading fails
                this.totalStats = {
                    completed: 0,
                    revenue: 0,
                    avgRating: 0,
                    avgTime: 0
                };
                this.employeeAnalytics = [];
            }
        },

        showEmployeeDetails(employeeId) {
            // Find the employee in the analytics data
            const employee = this.employeeAnalytics.find(e => e.id === employeeId);
            if (employee) {
                this.selectedEmployee = employee;
                
                // Fetch detailed stats for the employee
                this.fetchEmployeeDetailedStats(employeeId);
                
                this.showEmployeeDetailsModal = true;
            } else {
                console.error('Employee not found:', employeeId);
            }
        },
        
        async fetchEmployeeDetailedStats(employeeId) {
            try {
                const response = await fetch(`/shop/employees/${employeeId}/detailed-stats`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        period: this.analyticsPeriod,
                        service_type: this.analyticsServiceType
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to load detailed employee stats');
                }

                const data = await response.json();
                
                if (data.success) {
                    // Add the detailed data to the selected employee
                    this.selectedEmployee = {
                        ...this.selectedEmployee,
                        recent_appointments: data.details.recent_appointments || [],
                        reviews: data.details.reviews || []
                    };
                } else {
                    throw new Error(data.error || 'Failed to load detailed employee stats');
                }
            } catch (error) {
                console.error('Error loading detailed employee stats:', error);
                // Set default values for detailed stats
                if (this.selectedEmployee) {
                    this.selectedEmployee.recent_appointments = [];
                    this.selectedEmployee.reviews = [];
                }
            }
        },

        formatDate(dateString) {
            try {
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                const date = new Date(dateString);
                if (isNaN(date.getTime())) {
                    return 'Invalid Date';
                }
                return date.toLocaleDateString(undefined, options);
            } catch (e) {
                console.error('Error formatting date:', e);
                return dateString || 'Unknown Date';
            }
        },

        async loadEmployeeAvailability() {
            try {
                this.availabilityLoading = true;
                const response = await fetch(`/shop/employees/${this.selectedEmployee}/availability`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load employee availability');
                }

                const data = await response.json();
                if (data.success) {
                    this.employeeAvailability = data.availability;
                } else {
                    throw new Error(data.error || 'Failed to load employee availability');
                }
            } catch (error) {
                console.error('Error loading employee availability:', error);
                this.employeeAvailability = {};
            } finally {
                this.availabilityLoading = false;
            }
        },

        async saveAvailability() {
            try {
                if (!this.selectedEmployee) {
                    alert('Please select an employee first');
                    return;
                }
                
                // Collect availability data from the form
                const availabilityData = {};
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                
                days.forEach(day => {
                    const isAvailable = document.querySelector(`input[data-day="${day}"].day-available`).checked;
                    const startTime = document.querySelector(`select[data-day="${day}"][data-type="start"]`).value;
                    const endTime = document.querySelector(`select[data-day="${day}"][data-type="end"]`).value;
                    
                    availabilityData[day] = {
                        is_available: isAvailable,
                        start_time: startTime + ':00',
                        end_time: endTime + ':00'
                    };
                });
                
                const response = await fetch(`/shop/employees/${this.selectedEmployee}/availability`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(availabilityData)
                });

                if (!response.ok) {
                    throw new Error('Failed to save employee availability');
                }

                const data = await response.json();
                if (data.success) {
                    alert('Availability saved successfully');
                    this.loadEmployeeAvailability();
                    this.calendar.refetchEvents();
                } else {
                    throw new Error(data.error || 'Failed to save employee availability');
                }
            } catch (error) {
                console.error('Error saving employee availability:', error);
                alert('Failed to save employee availability: ' + error.message);
            }
        }
    }))
});
</script>
@endpush

@endsection