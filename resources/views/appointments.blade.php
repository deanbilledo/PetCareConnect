<x-layout>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Appointments Schedule</h2>
            </div>
            
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fees</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Calendar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">John Doe</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Grooming</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$50</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10:00 AM</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-20</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Dr. Smith</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <svg class="h-6 w-6 inline-block text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Appointments</h2>
            </div>
            
            <div class="p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <a href="{{ route('addapointment') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                        Create New Appointment
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-15</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10:00 AM</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Max</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Grooming</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Scheduled
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-16</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2:30 PM</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bella</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Checkup</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        Confirmed
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Finished Appointments -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Finished Appointments</h3>
                <select class="text-sm border rounded-md px-2 py-1">
                    <option>this week</option>
                </select>
            </div>
            <div class="text-2xl font-bold mb-4">183</div>
            <div class="h-32 w-full bg-gradient-to-r from-pink-100 to-pink-200 rounded-lg relative">
                <!-- Pink line chart representation -->
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 50" preserveAspectRatio="none">
                    <path d="M0 40 L20 35 L40 25 L60 30 L80 20 L100 25" 
                          fill="none" 
                          stroke="#EC4899" 
                          stroke-width="2"/>
                </svg>
            </div>
        </div>

        <!-- Pending Appointments -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Pending Appointments</h3>
                <select class="text-sm border rounded-md px-2 py-1">
                    <option>this week</option>
                </select>
            </div>
            <div class="text-2xl font-bold mb-4">81</div>
            <div class="h-32 w-full bg-gradient-to-r from-orange-100 to-orange-200 rounded-lg relative">
                <!-- Orange line chart representation -->
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 50" preserveAspectRatio="none">
                    <path d="M0 35 L20 30 L40 25 L60 20 L80 20 L100 20" 
                          fill="none" 
                          stroke="#F97316" 
                          stroke-width="2"/>
                </svg>
            </div>
        </div>

        <!-- Total Appointments -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Total Appointments</h3>
                <select class="text-sm border rounded-md px-2 py-1">
                    <option>this week</option>
                </select>
            </div>
            <div class="text-2xl font-bold mb-4">$5800</div>
            <div class="h-32 w-full bg-gradient-to-r from-green-100 to-green-200 rounded-lg relative">
                <!-- Green line chart representation -->
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 50" preserveAspectRatio="none">
                    <path d="M0 30 L20 20 L40 35 L60 25 L80 20 L100 15" 
                          fill="none" 
                          stroke="#22C55E" 
                          stroke-width="2"/>
                </svg>
            </div>
        </div>
    </div>
    
</x-layout>
