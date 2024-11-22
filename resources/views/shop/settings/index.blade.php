@extends('layouts.shop')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Shop Settings</h1>

    <!-- Shop Profile Section -->
    <div id="shop-profile" class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Shop Profile</h2>
            <span class="text-sm text-green-600 hidden" id="profile-saved">
                Changes saved successfully
            </span>
        </div>
        <form class="space-y-6">
            <div class="flex items-center space-x-6">
                <div class="relative group">
                    <div class="h-32 w-32 rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200 hover:border-blue-500 transition-colors duration-150">
                        <img src="https://via.placeholder.com/150" alt="Shop logo" class="h-full w-full object-cover">
                    </div>
                    <button type="button" class="absolute bottom-2 right-2 p-2 bg-white rounded-full shadow-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                    <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Business Hours Section -->
    <div id="business-hours" class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Business Hours</h2>
            <span class="text-sm text-green-600 hidden" id="hours-saved">
                Hours updated successfully
            </span>
        </div>
        <div class="space-y-4">
            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                <span class="text-sm font-medium text-gray-700 w-28">{{ $day }}</span>
                <div class="flex items-center space-x-4">
                    <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(6, 22) as $hour)
                            <option>{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</option>
                        @endforeach
                    </select>
                    <span>to</span>
                    <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(6, 22) as $hour)
                            <option>{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</option>
                        @endforeach
                    </select>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Closed</span>
                    </label>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Update Hours
            </button>
        </div>
    </div>

    <!-- Notifications Section -->
    <div id="notifications" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Notification Preferences</h2>
        <div class="space-y-4">
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Email notifications for new appointments</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">SMS notifications for new appointments</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Daily summary email</span>
            </label>
        </div>
    </div>

    <!-- Security Section -->
    <div id="security" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Security Settings</h2>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Simplified script without scroll tracking -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove all scroll-related JavaScript since we no longer need it
});
</script>
@endsection 