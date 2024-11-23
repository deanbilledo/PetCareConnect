@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Settings Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800">Settings</h2>
        </div>

        <!-- Settings Content -->
        <div class="p-6">
            <!-- Settings Tabs -->
            <div class="border-b border-gray-200" x-data="{ activeTab: 'account' }">
                <nav class="flex flex-wrap gap-x-8 -mb-px">
                    <button @click="activeTab = 'account'"
                            :class="{
                                'border-blue-500 text-blue-600': activeTab === 'account',
                                'border-transparent text-gray-500': activeTab !== 'account'
                            }"
                            class="inline-flex items-center px-3 py-2.5 border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out hover:text-gray-700 hover:border-gray-300">
                        <span class="whitespace-nowrap">Account Settings</span>
                    </button>
                    <button @click="activeTab = 'notifications'"
                            :class="{
                                'border-blue-500 text-blue-600': activeTab === 'notifications',
                                'border-transparent text-gray-500': activeTab !== 'notifications'
                            }"
                            class="inline-flex items-center px-3 py-2.5 border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out hover:text-gray-700 hover:border-gray-300">
                        <span class="whitespace-nowrap">Notifications</span>
                    </button>
                    <button @click="activeTab = 'privacy'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'privacy',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'privacy'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Privacy & Security
                    </button>
                </nav>
            </div>

            <!-- Account Settings Tab -->
            <div x-show="activeTab === 'account'" class="py-6">
                <div class="space-y-6">
                    <!-- Email Settings -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Email Settings</h3>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Email Address</p>
                                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <button class="text-sm text-blue-600 hover:text-blue-500">Change</button>
                            </div>
                        </div>
                    </div>

                    <!-- Password Settings -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Password Settings</h3>
                        <div class="mt-4">
                            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                Change Password
                            </button>
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-red-600">Delete Account</h3>
                        <p class="mt-1 text-sm text-gray-500">Once you delete your account, it cannot be undone.</p>
                        <div class="mt-4">
                            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-600 bg-red-100 hover:bg-red-200">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div x-show="activeTab === 'notifications'" class="py-6">
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Notification Preferences</h3>
                    <div class="space-y-4">
                        <!-- Email Notifications -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-700">Email Notifications</label>
                                <p class="text-sm text-gray-500">Receive email updates about your appointments</p>
                            </div>
                        </div>

                        <!-- SMS Notifications -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-700">SMS Notifications</label>
                                <p class="text-sm text-gray-500">Receive text messages about your appointments</p>
                            </div>
                        </div>

                        <!-- Marketing Communications -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-700">Marketing Communications</label>
                                <p class="text-sm text-gray-500">Receive updates about special offers and news</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy & Security Tab -->
            <div x-show="activeTab === 'privacy'" class="py-6">
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Privacy Settings</h3>
                    <div class="space-y-4">
                        <!-- Profile Visibility -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label class="text-sm font-medium text-gray-700">Profile Visibility</label>
                                <p class="text-sm text-gray-500">Make your profile visible to other users</p>
                            </div>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="pt-6 border-t border-gray-200">
                            <h4 class="text-md font-medium text-gray-900">Two-Factor Authentication</h4>
                            <p class="mt-1 text-sm text-gray-500">Add an extra layer of security to your account</p>
                            <div class="mt-4">
                                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                    Enable 2FA
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
