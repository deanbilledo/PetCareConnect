@extends('layouts.shop')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Analytics Dashboard</h1>

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