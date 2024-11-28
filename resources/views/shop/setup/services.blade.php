@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Set Up Your Services</h1>
                <p class="mt-2 text-sm text-gray-600">Add the services you offer to your customers.</p>
            </div>

            <!-- Services Form -->
            <form method="POST" 
                  action="{{ route('shop.setup.services.store') }}" 
                  x-data="{ 
                      services: [{ name: '', price: '', duration: '', description: '' }],
                      addService() {
                          this.services.push({ name: '', price: '', duration: '', description: '' });
                      },
                      removeService(index) {
                          if (this.services.length > 1) {
                              this.services.splice(index, 1);
                          }
                      }
                  }">
                @csrf

                <div class="px-8 py-6">
                    <!-- Service Templates -->
                    <template x-for="(service, index) in services" :key="index">
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg relative">
                            <!-- Remove Button -->
                            <button type="button" 
                                    @click="removeService(index)"
                                    x-show="services.length > 1"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Service Fields -->
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Service Name</label>
                                    <input type="text" 
                                           x-model="service.name"
                                           :name="'services[' + index + '][name]'"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           required>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Price (₱)</label>
                                        <input type="number" 
                                               x-model="service.price"
                                               :name="'services[' + index + '][price]'"
                                               min="0"
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                                        <input type="number" 
                                               x-model="service.duration"
                                               :name="'services[' + index + '][duration]'"
                                               min="15"
                                               step="15"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea x-model="service.description"
                                            :name="'services[' + index + '][description]'"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Add Service Button -->
                    <button type="button"
                            @click="addService"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Another Service
                    </button>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <a href="{{ route('shop.setup.welcome') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Continue
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 