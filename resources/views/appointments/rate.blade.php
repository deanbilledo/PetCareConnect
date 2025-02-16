@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Rate Your Experience</h1>
                <a href="{{ route('appointments.index') }}" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <img class="h-12 w-12 rounded-lg object-cover"
                         src="{{ $appointment->shop->image ? asset('storage/' . $appointment->shop->image) : asset('images/default-shop.png') }}"
                         alt="{{ $appointment->shop->name }}">
                </div>
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ $appointment->shop->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $appointment->service_type }} - {{ $appointment->appointment_date->format('F j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Rating Form -->
        <form action="{{ route('appointments.rate', $appointment) }}" method="POST" class="px-6 py-4 space-y-6">
            @csrf
            
            <!-- Shop Rating -->
            <div class="bg-white rounded-lg p-4 border">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Shop Rating</h3>
                <div class="space-y-4">
                    <div x-data="{ rating: 0 }" class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        @click="rating = {{ $i }}; $refs.shopRating.value = {{ $i }}"
                                        class="focus:outline-none transition-colors"
                                        :class="{ 'text-yellow-400': {{ $i }} <= rating, 'text-gray-300': {{ $i }} > rating }">
                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="shop_rating" x-ref="shopRating" required>
                        </div>
                        <span class="text-sm text-gray-600" x-text="rating + ' out of 5'"></span>
                    </div>
                    
                    <div>
                        <label for="shop_review" class="block text-sm font-medium text-gray-700">Review</label>
                        <textarea id="shop_review"
                                  name="shop_review"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Share your experience with the shop..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Employee Rating -->
            <div class="bg-white rounded-lg p-4 border">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex-shrink-0">
                        <img class="h-12 w-12 rounded-full object-cover"
                             src="{{ $appointment->employee->profile_photo_url }}"
                             alt="{{ $appointment->employee->name }}">
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $appointment->employee->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $appointment->employee->position }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div x-data="{ rating: 0 }" class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        @click="rating = {{ $i }}; $refs.staffRating.value = {{ $i }}"
                                        class="focus:outline-none transition-colors"
                                        :class="{ 'text-yellow-400': {{ $i }} <= rating, 'text-gray-300': {{ $i }} > rating }">
                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="staff_rating" x-ref="staffRating" required>
                        </div>
                        <span class="text-sm text-gray-600" x-text="rating + ' out of 5'"></span>
                    </div>
                    
                    <div>
                        <label for="staff_review" class="block text-sm font-medium text-gray-700">Additional Comments</label>
                        <textarea id="staff_review"
                                  name="staff_review"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Share your experience with the employee..."></textarea>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 rounded-lg p-4">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 border-t pt-4">
                <a href="{{ route('appointments.index') }}"
                   class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 