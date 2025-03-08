@extends('layouts.shop')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .export-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .export-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        min-width: 160px;
        background-color: white;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        z-index: 10;
        border-radius: 0.375rem;
    }
    
    .export-dropdown-content a {
        color: #4B5563;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }
    
    .export-dropdown-content a:hover {
        background-color: #F3F4F6;
    }
    
    .export-dropdown:hover .export-dropdown-content {
        display: block;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Analytics Dashboard</h1>
        
        <!-- Export Dropdown -->
        <div class="export-dropdown">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export Reports</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="export-dropdown-content">
                <a href="{{ route('shop.analytics.export', ['type' => 'pdf']) }}" class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M320 464C328.8 464 336 456.8 336 448V416H384V448C384 483.3 355.3 512 320 512H64C28.65 512 0 483.3 0 448V416H48V448C48 456.8 55.16 464 64 464H320zM256 160C238.3 160 224 145.7 224 128V48H64C55.16 48 48 55.16 48 64V192H0V64C0 28.65 28.65 0 64 0H229.5C246.5 0 262.7 6.743 274.7 18.75L365.3 109.3C377.3 121.3 384 137.5 384 154.5V192H336V160H256zM88 224C118.9 224 144 249.1 144 280C144 310.9 118.9 336 88 336H80V368H88C118.9 368 144 393.1 144 424C144 454.9 118.9 480 88 480H64C55.16 480 48 472.8 48 464V240C48 231.2 55.16 224 64 224H88zM80 272V288H88C92.42 288 96 284.4 96 280C96 275.6 92.42 272 88 272H80zM88 432C92.42 432 96 428.4 96 424C96 419.6 92.42 416 88 416H80V432H88zM192 224C200.8 224 208 231.2 208 240V464C208 472.8 200.8 480 192 480H168C158.3 480 150.3 473.7 148.4 464.1L144 432L139.6 464.1C137.7 473.7 129.7 480 120 480H96C87.16 480 80 472.8 80 464V240C80 231.2 87.16 224 96 224H120C129.7 224 137.7 230.3 139.6 239.9L144 272L148.4 239.9C150.3 230.3 158.3 224 168 224H192zM160 384V272H158.4L147.2 328C145.3 337.7 137.3 344 128 344C118.7 344 110.7 337.7 108.8 328L97.6 272H96V384H160zM256 240C256 231.2 263.2 224 272 224H296C325.7 224 336 252.3 336 280V304C336 331.7 325.7 360 296 360H272C267.6 360 264 363.6 264 368V464C264 472.8 256.8 480 248 480H240C231.2 480 224 472.8 224 464V240C224 231.2 231.2 224 240 224H248C256.8 224 264 231.2 264 240V288H272C277.7 288 280 284.1 280 280V304C280 299.9 277.7 296 272 296H264V360H272C277.7 360 280 356.1 280 352V328C280 323.9 277.7 320 272 320H264V240H256z"/>
                    </svg>
                    Export as PDF
                </a>
                <a href="{{ route('shop.analytics.export', ['type' => 'excel']) }}" class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM155.7 250.2L192 302.1l36.3-51.9c7.6-10.9 22.6-13.5 33.4-5.9s13.5 22.6 5.9 33.4L221.3 344l46.4 66.2c7.6 10.9 5 25.8-5.9 33.4s-25.8 5-33.4-5.9L192 385.8l-36.3 51.9c-7.6 10.9-22.6 13.5-33.4 5.9s-13.5-22.6-5.9-33.4L162.7 344l-46.4-66.2c-7.6-10.9-5-25.8 5.9-33.4s25.8-5 33.4 5.9z"/>
                    </svg>
                    Export as Excel
                </a>
                <a href="{{ route('shop.analytics.export', ['type' => 'csv']) }}" class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM80 224H96c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H96c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm16 96H288c8.8 0 16 7.2 16 16c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16c0-8.8 7.2-16 16-16zm128-160c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16h80zm0 64c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16h80z"/>
                    </svg>
                    Export as CSV
                </a>
            </div>
        </div>
    </div>

    

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Appointments -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Appointments</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalAppointments) }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm {{ $appointmentGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                {{ $appointmentGrowth >= 0 ? '↑' : '↓' }} {{ abs(round($appointmentGrowth)) }}% from last month
            </p>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs(round($revenueGrowth)) }}% from last month
            </p>
        </div>

        <!-- Customers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Customers</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</h3>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm {{ $customerGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                {{ $customerGrowth >= 0 ? '↑' : '↓' }} {{ abs(round($customerGrowth)) }}% from last month
            </p>
        </div>

        <!-- Services -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Services</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $activeServices }}</h3>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm {{ $servicesGrowth >= 0 ? 'text-green-600' : 'text-yellow-600' }} mt-2">
                @if($servicesGrowth > 0)
                    ↑ {{ abs(round($servicesGrowth)) }}% from last month
                @elseif($servicesGrowth < 0)
                    ↓ {{ abs(round($servicesGrowth)) }}% from last month
                @else
                    No change from last month
                @endif
            </p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Revenue Overview</h3>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Appointments Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Appointments Overview</h3>
            <div class="h-80">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Service Booking Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Most/Least Booked Services -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Service Popularity</h3>
            <div class="h-80">
                <canvas id="servicePopularityChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-medium text-green-800 mb-2">Most Popular Service</h4>
                    @if(isset($mostBookedService))
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-full mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $mostBookedService->name }}</p>
                                <p class="text-sm text-gray-600">{{ $mostBookedService->count }} bookings</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No data available</p>
                    @endif
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <h4 class="font-medium text-red-800 mb-2">Least Popular Service</h4>
                    @if(isset($leastBookedService))
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-full mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $leastBookedService->name }}</p>
                                <p class="text-sm text-gray-600">{{ $leastBookedService->count }} bookings</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Peak Booking Hours -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Peak Booking Hours</h3>
            <div class="h-80">
                <canvas id="peakHoursChart"></canvas>
            </div>
            <div class="mt-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-medium text-blue-800 mb-2">Busiest Time of Day</h4>
                    @if(isset($peakBookingHour))
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-full mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ date('g:i A', strtotime($peakBookingHour->hour . ':00')) }} - {{ date('g:i A', strtotime($peakBookingHour->hour . ':59')) }}
                                </p>
                                <p class="text-sm text-gray-600">{{ $peakBookingHour->count }} bookings</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Day of Week Popularity -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Day of Week Popularity</h3>
        <div class="h-60">
            <canvas id="dayOfWeekChart"></canvas>
        </div>
    </div>

    <!-- Paid Appointments Table -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Paid Appointments</h3>
            <div class="text-green-600 font-semibold">
                Total Revenue: ₱{{ number_format($paidRevenue, 2) }}
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paidAppointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $appointment->pet->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $appointment->service_type }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($appointment->employee)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full object-cover" 
                                                 src="{{ $appointment->employee->profile_photo_url }}" 
                                                 alt="{{ $appointment->employee->name }}">
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $appointment->employee->name }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->appointment_date->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600">₱{{ number_format($appointment->service_price, 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    Paid 
                                    @if($appointment->paid_at)
                                        @if(is_string($appointment->paid_at))
                                            {{ \Carbon\Carbon::parse($appointment->paid_at)->diffForHumans() }}
                                        @else
                                            {{ $appointment->paid_at->diffForHumans() }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No paid appointments found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
        <div class="space-y-4">
            @forelse($recentActivity as $activity)
                <div class="flex items-center justify-between py-3 border-b">
                    <div class="flex items-center">
                        <div class="p-2 {{ $activity->status === 'completed' ? 'bg-green-100' : 'bg-blue-100' }} rounded-full mr-4">
                            <svg class="w-4 h-4 {{ $activity->status === 'completed' ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($activity->status === 'completed')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                @endif
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">
                                {{ $activity->status === 'completed' ? 'Service Completed' : 'New Appointment' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $activity->user->name }} - {{ $activity->service_type }}
                                @if($activity->employee)
                                    ({{ $activity->employee->name }})
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="text-center text-gray-500">No recent activity</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = {
    labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
    datasets: [{
        label: 'Revenue (PHP)',
        data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true
    }]
};

new Chart(revenueCtx, {
    type: 'line',
    data: revenueData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Appointments Chart
const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
const appointmentData = {
    labels: {!! json_encode($monthlyAppointments->pluck('month')) !!},
    datasets: [{
        label: 'Completed',
        data: {!! json_encode($monthlyAppointments->pluck('completed')) !!},
        backgroundColor: 'rgba(34, 197, 94, 0.5)',
        borderColor: 'rgb(34, 197, 94)',
        borderWidth: 1
    }, {
        label: 'Pending',
        data: {!! json_encode($monthlyAppointments->pluck('pending')) !!},
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 1
    }]
};

new Chart(appointmentsCtx, {
    type: 'bar',
    data: appointmentData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: true
            },
            x: {
                stacked: true
            }
        }
    }
});

// Service Popularity Chart
const serviceCtx = document.getElementById('servicePopularityChart').getContext('2d');
const serviceData = {
    labels: {!! json_encode($serviceBookingCounts->pluck('name')) !!},
    datasets: [{
        label: 'Number of Bookings',
        data: {!! json_encode($serviceBookingCounts->pluck('count')) !!},
        backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)',
            'rgba(83, 102, 255, 0.7)',
            'rgba(40, 159, 64, 0.7)',
            'rgba(210, 199, 199, 0.7)',
        ],
        borderColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 206, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
            'rgb(199, 199, 199)',
            'rgb(83, 102, 255)',
            'rgb(40, 159, 64)',
            'rgb(210, 199, 199)',
        ],
        borderWidth: 1
    }]
};

new Chart(serviceCtx, {
    type: 'bar',
    data: serviceData,
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.x + ' bookings';
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Bookings'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Services'
                }
            }
        }
    }
});

// Peak Hours Chart
const hoursCtx = document.getElementById('peakHoursChart').getContext('2d');
const hourLabels = {!! json_encode($hourlyBookings->pluck('hour_label')) !!};
const hourData = {!! json_encode($hourlyBookings->pluck('count')) !!};

const peakHoursData = {
    labels: hourLabels,
    datasets: [{
        label: 'Bookings per Hour',
        data: hourData,
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 1,
        tension: 0.1,
        fill: true
    }]
};

new Chart(hoursCtx, {
    type: 'line',
    data: peakHoursData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Bookings'
                },
                ticks: {
                    precision: 0
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Hour of Day'
                }
            }
        }
    }
});

// Day of Week Chart
const dowCtx = document.getElementById('dayOfWeekChart').getContext('2d');
const dowData = {
    labels: {!! json_encode($dayOfWeekBookings->pluck('day')) !!},
    datasets: [{
        label: 'Bookings by Day of Week',
        data: {!! json_encode($dayOfWeekBookings->pluck('count')) !!},
        backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(99, 255, 132, 0.7)',
        ],
        borderColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 206, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
            'rgb(99, 255, 132)',
        ],
        borderWidth: 1
    }]
};

new Chart(dowCtx, {
    type: 'pie',
    data: dowData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} bookings (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>
@endpush 