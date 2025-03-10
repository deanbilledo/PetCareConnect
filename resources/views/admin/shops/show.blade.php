@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $shop->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.shops.edit', $shop) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Shop
                </a>
            </div>
        </div>

        <!-- Shop Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Shop Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $shop->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Phone</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $shop->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Address</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $shop->address }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status</label>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $shop->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($shop->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($shop->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Statistics</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Appointments</h3>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $shop->appointments_count ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Rating</h3>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format($shop->average_rating ?? 0, 1) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</h3>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                            ₱{{ number_format($shop->total_revenue ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Services Offered</h3>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $shop->services_count ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Services</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($shop->services as $service)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $service->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">₱{{ number_format($service->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $service->duration }} minutes</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 