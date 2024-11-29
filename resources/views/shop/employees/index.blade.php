@extends('layouts.shop')

@section('content')
<div x-data="{ showModal: false }" class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Employees</h1>
        <button @click="showModal = true" type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            Add New Employee
        </button>
    </div>

    <!-- Add Employee Modal -->
    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg z-10">
            <h2 class="text-xl font-bold mb-4">Add New Employee</h2>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="John Doe">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="john.doe@example.com">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                    Phone
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="phone" type="text" placeholder="+63 912 345 6789">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                    Position
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="position" type="text" placeholder="Senior Groomer">
            </div>
            <div class="flex items-center justify-between">
                <button @click="showModal = false" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Add Employee
                </button>
                <button @click="showModal = false" class="text-gray-600 hover:text-gray-800">Cancel</button>
            </div>
        </div>
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
                    <p>📱 +63 912 345 6789</p>
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
@endsection