<x-layout>
    
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">Billing & Payments</h1>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Payment Methods</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-center mb-2">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="ml-2 text-lg font-medium">Credit Card</span>
                    </div>
                    <p class="text-gray-600">We accept all major credit cards</p>
                </div>

                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-center mb-2">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                        <span class="ml-2 text-lg font-medium">Bank Transfer</span>
                    </div>
                    <p class="text-gray-600">Direct bank transfer available</p>
                </div>

                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-center mb-2">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-2 text-lg font-medium">PayPal</span>
                    </div>
                    <p class="text-gray-600">Safe and secure PayPal payments</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Billing Information</h2>
            <div class="prose max-w-none">
                <p class="text-gray-600 mb-4">
                    Our billing system is secure and transparent. Here's what you need to know:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li>All payments are processed securely</li>
                    <li>Invoices are sent monthly</li>
                    <li>Multiple payment options available</li>
                    <li>24/7 billing support</li>
                    <li>Detailed transaction history provided</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-gray-600">
                Have questions about billing? Contact our support team at
                <a href="mailto:support@petcare.com" class="text-blue-500 hover:text-blue-600">support@petcare.com</a>
            </p>
        </div>
    </div>
</div>

</x-layout>