@extends('layouts.shop')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Employees</h1>
        <button type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            Add New Employee
        </button>
    </div>

    <!-- Employees Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Employee Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name=John+Doe" alt="John Doe" class="w-16 h-16 rounded-full">
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">John Doe</h3>
                        <p class="text-sm text-gray-600">Senior Groomer</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <p>📧 john.doe@example.com</p>
                    <p>📱 +63 912 345 6789</p>
                    <p>🕒 Full Time</p>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button class="text-blue-600 hover:text-blue-800">Edit</button>
                    <button class="text-red-600 hover:text-red-800">Remove</button>
                </div>
            </div>
        </div>

        <!-- Employee Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name=Jane+Smith" alt="Jane Smith" class="w-16 h-16 rounded-full">
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Jane Smith</h3>
                        <p class="text-sm text-gray-600">Pet Stylist</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <p>📧 jane.smith@example.com</p>
                    <p>📱 +63 923 456 7890</p>
                    <p>🕒 Part Time</p>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button class="text-blue-600 hover:text-blue-800">Edit</button>
                    <button class="text-red-600 hover:text-red-800">Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal (Hidden by default) -->
<div class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="addEmployeeModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add New Employee</h3>
            <form>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 