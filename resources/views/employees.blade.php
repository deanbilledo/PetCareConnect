<x-layout>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Our Employees</h2>
            
            <div class="mb-6">
                <a href="{{ route('addemployee') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                    Create New Employee
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Employee Card 1 -->
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h5 class="text-xl font-semibold text-gray-900 mb-2">John Doe</h5>
                        <h6 class="text-md text-gray-600 mb-3">Veterinarian</h6>
                        <p class="text-gray-700">Experienced veterinarian with 10+ years of practice in pet care.</p>
                    </div>
                </div>

                <!-- Employee Card 2 -->
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h5 class="text-xl font-semibold text-gray-900 mb-2">Jane Smith</h5>
                        <h6 class="text-md text-gray-600 mb-3">Pet Groomer</h6>
                        <p class="text-gray-700">Certified pet groomer specializing in all breeds of dogs and cats.</p>
                    </div>
                </div>

                <!-- Employee Card 3 -->
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h5 class="text-xl font-semibold text-gray-900 mb-2">Mike Johnson</h5>
                        <h6 class="text-md text-gray-600 mb-3">Veterinary Assistant</h6>
                        <p class="text-gray-700">Dedicated assistant with expertise in animal care and handling.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-layout>