@extends('layouts.shop')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Reviews Header -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Shop Reviews</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage and view all your customer reviews</p>
                    </div>

                    <!-- Reviews Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-blue-600">4.8</div>
                            <div class="text-sm text-gray-600">Average Rating</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-blue-600">127</div>
                            <div class="text-sm text-gray-600">Total Reviews</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-green-600">92%</div>
                            <div class="text-sm text-gray-600">Positive Reviews</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-yellow-600">24</div>
                            <div class="text-sm text-gray-600">New This Month</div>
                        </div>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Rating Distribution</h3>
                        <div class="space-y-2">
                            @foreach(range(5, 1) as $rating)
                                <div class="flex items-center">
                                    <span class="w-12 text-sm text-gray-600">{{ $rating }} star</span>
                                    <div class="flex-1 mx-4">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ rand(10, 90) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="w-12 text-sm text-gray-600">{{ rand(10, 50) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Reviews -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Recent Reviews</h3>
                        <div class="space-y-4">
                            @foreach(range(1, 5) as $index)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                            <div class="ml-4">
                                                <div class="font-medium">Customer Name</div>
                                                <div class="text-sm text-gray-500">{{ now()->subDays(rand(1, 30))->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            @foreach(range(1, 5) as $star)
                                                <svg class="w-5 h-5 {{ $star <= rand(3, 5) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endforeach
                                        </div>
                                    </div>
                                    <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 