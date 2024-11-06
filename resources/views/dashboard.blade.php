<x-layout>
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" 
                   class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Search...">
            <div class="absolute right-4 top-2.5 flex space-x-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Customer Appointments Table -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Customer Appointments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @for($i = 0; $i < 6; $i++)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">Dean Wright Blends</td>
                        <td class="px-6 py-4 whitespace-nowrap">Full Groom</td>
                        <td class="px-6 py-4 whitespace-nowrap">$500</td>
                        <td class="px-6 py-4 whitespace-nowrap">9:00 am</td>
                        <td class="px-6 py-4 whitespace-nowrap">10/7/2024</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($i % 3 == 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                            @elseif($i % 3 == 1)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Cancelled
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Alexandra Dolantos</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a 1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <button class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6 p-6">

    <!-- Statistics Cards -->
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

</div>
    
</div>
</x-layout>