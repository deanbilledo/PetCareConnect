@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Settings Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800">Settings</h2>
        </div>

        <!-- Settings Content -->
        <div class="p-6" x-data="{ activeTab: 'account' }">
            <!-- Settings Tabs -->
            <div class="border-b border-gray-200">
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
                    <button @click="activeTab = 'discounts'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'discounts',
                                    'border-transparent text-gray-500': activeTab !== 'discounts'}"
                            class="inline-flex items-center px-3 py-2.5 border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out hover:text-gray-700 hover:border-gray-300">
                        <span class="whitespace-nowrap">Discount Settings</span>
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

            <!-- Discount Settings Tab -->
            <div x-show="activeTab === 'discounts'" class="py-6" x-data="{ showDiscountModal: false, discountType: '' }">
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Discount Settings</h3>
                    
                    <!-- Discount Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Senior Citizen Card -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">Senior Citizen</h4>
                                    <p class="text-sm text-gray-500 mt-1">20% discount on all services</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Active
                                </span>
                            </div>
                            <div class="mt-4">
                                <button @click="showDiscountModal = true; discountType = 'senior'"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Apply Discount
                                </button>
                            </div>
                        </div>

                        <!-- PWD Card -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">PWD</h4>
                                    <p class="text-sm text-gray-500 mt-1">20% discount on all services</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Active
                                </span>
                            </div>
                            <div class="mt-4">
                                <button @click="showDiscountModal = true; discountType = 'pwd'"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Apply Discount
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Discount History -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Discount History</h4>
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul role="list" class="divide-y divide-gray-200">
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Senior Citizen Discount</p>
                                            <p class="text-sm text-gray-500">ID: SC-123456789</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Applied
                                        </span>
                                    </div>
                                </li>
                                <!-- Add more history items as needed -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Discount Modal -->
                <div x-show="showDiscountModal" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl sm:p-6"
                                 @click.away="showDiscountModal = false">
                                
                                <!-- Modal Header -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" x-text="discountType === 'senior' ? 'Apply Senior Citizen Discount' : 'Apply PWD Discount'"></h3>
                                    <p class="mt-2 text-sm text-gray-500">Please provide the required information to apply the discount.</p>
                                </div>

                                <!-- Modal Form -->
                                <form class="space-y-4">
                                    <!-- ID Number -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700" x-text="discountType === 'senior' ? 'Senior Citizen ID Number' : 'PWD ID Number'"></label>
                                        <input type="text" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                               placeholder="Enter ID number">
                                    </div>

                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <input type="text" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                               placeholder="Enter full name">
                                    </div>

                                    <!-- ID Photo Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ID Photo</label>
                                        <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label class="relative cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:text-blue-500">
                                                        <span>Upload a file</span>
                                                        <input type="file" class="sr-only">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Actions -->
                                    <div class="mt-5 sm:mt-6 flex gap-3">
                                        <button type="button"
                                                class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                                @click="showDiscountModal = false">
                                            Apply Discount
                                        </button>
                                        <button type="button"
                                                class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                @click="showDiscountModal = false">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection