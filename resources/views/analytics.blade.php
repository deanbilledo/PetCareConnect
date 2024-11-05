<x-layout>
    
<div class="container mx-auto px-4 py-8">
    <!-- Analytics Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Shop Analytics Dashboard</h1>
        <p class="text-gray-600">Performance metrics for the last 30 days</p>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm">Total Sales</h3>
            <p class="text-2xl font-bold text-green-600">$12,849</p>
            <span class="text-green-500 text-sm">↑ 12% vs last month</span>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm">Orders</h3>
            <p class="text-2xl font-bold">284</p>
            <span class="text-green-500 text-sm">↑ 8% vs last month</span>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm">Customers</h3>
            <p class="text-2xl font-bold">156</p>
            <span class="text-red-500 text-sm">↓ 3% vs last month</span>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm">Avg. Order Value</h3>
            <p class="text-2xl font-bold">$45.24</p>
            <span class="text-green-500 text-sm">↑ 4% vs last month</span>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Sales Overview</h3>
            <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                <p class="text-gray-500">Sales Chart Placeholder</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Top Products</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Dog Food Premium</span>
                    <span class="font-semibold">$2,450</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Cat Litter Box</span>
                    <span class="font-semibold">$1,840</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Pet Toys Bundle</span>
                    <span class="font-semibold">$1,550</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4">#12345</td>
                            <td class="px-6 py-4">John Doe</td>
                            <td class="px-6 py-4">Dog Food, Toys</td>
                            <td class="px-6 py-4">$85.00</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">#12344</td>
                            <td class="px-6 py-4">Jane Smith</td>
                            <td class="px-6 py-4">Cat Litter, Food Bowl</td>
                            <td class="px-6 py-4">$45.00</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Processing</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</x-layout>