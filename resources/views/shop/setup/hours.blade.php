@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Set Operating Hours</h1>
                <p class="mt-2 text-sm text-gray-600">Define when your shop is open for business.</p>
            </div>

            <!-- Hours Form -->
            <form method="POST" 
                  action="{{ route('shop.setup.hours.store') }}"
                  x-data="{ 
                      days: [
                          { name: 'Sunday', day: 0, is_open: false, open_time: '09:00', close_time: '17:00' },
                          { name: 'Monday', day: 1, is_open: true, open_time: '09:00', close_time: '17:00' },
                          { name: 'Tuesday', day: 2, is_open: true, open_time: '09:00', close_time: '17:00' },
                          { name: 'Wednesday', day: 3, is_open: true, open_time: '09:00', close_time: '17:00' },
                          { name: 'Thursday', day: 4, is_open: true, open_time: '09:00', close_time: '17:00' },
                          { name: 'Friday', day: 5, is_open: true, open_time: '09:00', close_time: '17:00' },
                          { name: 'Saturday', day: 6, is_open: true, open_time: '09:00', close_time: '17:00' }
                      ]
                  }">
                @csrf

                <div class="px-8 py-6 space-y-6">
                    <template x-for="(day, index) in days" :key="index">
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <input type="hidden" :name="'hours[' + index + '][day]'" :value="day.day">
                            
                            <div class="w-1/4">
                                <span class="font-medium" x-text="day.name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" :name="'hours[' + index + '][is_open]'" :value="day.is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="day.is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>

                            <div class="flex-1 grid grid-cols-2 gap-4" x-show="day.is_open">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="day.open_time"
                                           :name="'hours[' + index + '][open_time]'"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="day.is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="day.close_time"
                                           :name="'hours[' + index + '][close_time]'"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="day.is_open">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <a href="{{ route('shop.setup.services') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Complete Setup
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 