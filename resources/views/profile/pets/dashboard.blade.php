@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 mt-10">Pet Owner Dashboard</h1>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Pets</p>
                    <p class="text-2xl font-bold">{{ $pets->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Upcoming Appointments</p>
                    <p class="text-2xl font-bold">{{ $upcomingAppointments }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Due Vaccinations</p>
                    <p class="text-2xl font-bold">{{ $dueVaccinations }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Monthly Expenses</p>
                    <p class="text-2xl font-bold">₱{{ number_format($monthlyExpenses, 2) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Column -->
        <div class="space-y-8">
            <!-- Recent Appointments -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Appointments</h2>
                    <a href="{{ route('appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentAppointments as $appointment)
                        <div class="flex items-center justify-between border-b pb-4">
                            <div>
                                <p class="font-medium">{{ $appointment->service_type }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment->pet->name }} • {{ $appointment->shop->name }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->appointment_date->format('M d, Y h:i A') }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full 
                                {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                   'bg-blue-100 text-blue-800') }}">
                                {{ Str::title($appointment->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent appointments</p>
                    @endforelse
                </div>
            </div>

            <!-- Expense Tracking -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Expense Breakdown</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                            <span class="text-sm">Veterinary Care</span>
                        </div>
                        <span class="text-sm font-medium">₱{{ number_format($expenses['veterinary'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                            <span class="text-sm">Grooming</span>
                        </div>
                        <span class="text-sm font-medium">₱{{ number_format($expenses['grooming'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                            <span class="text-sm">Supplies</span>
                        </div>
                        <span class="text-sm font-medium">₱{{ number_format($expenses['supplies'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                            <span class="text-sm">Other</span>
                        </div>
                        <span class="text-sm font-medium">₱{{ number_format($expenses['other'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Health Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Pet Health Insights</h2>
                @foreach($pets as $pet)
                    <div class="border-b last:border-b-0 pb-4 mb-4 last:mb-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <img src="{{ $pet->profile_photo_url }}" alt="{{ $pet->name }}" class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <p class="font-medium">{{ $pet->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $pet->breed }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <p class="text-sm text-gray-600">Vaccinations</p>
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pet->vaccination_percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $pet->vaccination_percentage }}%</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Health Score</p>
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $pet->health_score }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $pet->health_score }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Care Trends -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Care Trends</h2>
                <div class="space-y-4">
                    @foreach($careTrends as $trend)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">{{ $trend['title'] }}</p>
                                <p class="text-sm text-gray-600">{{ $trend['description'] }}</p>
                            </div>
                            <span class="text-sm {{ $trend['change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trend['change'] >= 0 ? '+' : '' }}{{ $trend['change'] }}%
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Upcoming Health Tasks -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Upcoming Health Tasks</h2>
                <div class="space-y-4">
                    @forelse($upcomingTasks as $task)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">{{ $task->title }}</p>
                                <p class="text-sm text-gray-600">{{ $task->pet->name }} • Due {{ $task->due_date->format('M d, Y') }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full 
                                {{ $task->due_date->isPast() ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $task->due_date->isPast() ? 'Overdue' : 'Upcoming' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No upcoming tasks</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 