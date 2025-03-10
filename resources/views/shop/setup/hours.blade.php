@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12 mt-8">
    <div class="max-w-3xl mx-auto px-2 sm:px-6 lg:px-8">
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
                          { name: 'Sunday', day: 0, is_open: false, open_time: '09:00', close_time: '17:00', has_lunch_break: false, lunch_start: '12:00', lunch_end: '13:00' },
                          { name: 'Monday', day: 1, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                          { name: 'Tuesday', day: 2, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                          { name: 'Wednesday', day: 3, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                          { name: 'Thursday', day: 4, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                          { name: 'Friday', day: 5, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                          { name: 'Saturday', day: 6, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' }
                      ]
                  }"
                  x-init="() => {
                      // Ensure Alpine initializes only once
                      console.log('Alpine initialized');
                  }">
                @csrf

                <div class="px-8 py-6 space-y-6">
                    <!-- SUNDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[0][day]" :value="days[0].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[0].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[0][is_open]" :value="days[0].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[0].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[0].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[0].open_time"
                                           name="hours[0][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[0].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[0].close_time"
                                           name="hours[0][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[0].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[0][has_lunch_break]" :value="days[0].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[0].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[0].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[0].lunch_start"
                                               name="hours[0][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[0].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[0].lunch_end"
                                               name="hours[0][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[0].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MONDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[1][day]" :value="days[1].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[1].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[1][is_open]" :value="days[1].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[1].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[1].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[1].open_time"
                                           name="hours[1][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[1].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[1].close_time"
                                           name="hours[1][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[1].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[1][has_lunch_break]" :value="days[1].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[1].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[1].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[1].lunch_start"
                                               name="hours[1][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[1].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[1].lunch_end"
                                               name="hours[1][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[1].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TUESDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[2][day]" :value="days[2].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[2].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[2][is_open]" :value="days[2].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[2].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[2].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[2].open_time"
                                           name="hours[2][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[2].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[2].close_time"
                                           name="hours[2][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[2].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[2][has_lunch_break]" :value="days[2].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[2].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[2].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[2].lunch_start"
                                               name="hours[2][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[2].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[2].lunch_end"
                                               name="hours[2][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[2].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- WEDNESDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[3][day]" :value="days[3].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[3].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[3][is_open]" :value="days[3].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[3].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[3].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[3].open_time"
                                           name="hours[3][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[3].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[3].close_time"
                                           name="hours[3][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[3].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[3][has_lunch_break]" :value="days[3].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[3].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[3].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[3].lunch_start"
                                               name="hours[3][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[3].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[3].lunch_end"
                                               name="hours[3][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[3].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- THURSDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[4][day]" :value="days[4].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[4].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[4][is_open]" :value="days[4].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[4].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[4].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[4].open_time"
                                           name="hours[4][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[4].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[4].close_time"
                                           name="hours[4][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[4].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[4][has_lunch_break]" :value="days[4].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[4].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[4].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[4].lunch_start"
                                               name="hours[4][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[4].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[4].lunch_end"
                                               name="hours[4][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[4].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FRIDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[5][day]" :value="days[5].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[5].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[5][is_open]" :value="days[5].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[5].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[5].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[5].open_time"
                                           name="hours[5][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[5].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[5].close_time"
                                           name="hours[5][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[5].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[5][has_lunch_break]" :value="days[5].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[5].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[5].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[5].lunch_start"
                                               name="hours[5][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[5].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[5].lunch_end"
                                               name="hours[5][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[5].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SATURDAY -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <input type="hidden" name="hours[6][day]" :value="days[6].day">
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-1/4">
                                <span class="font-medium" x-text="days[6].name"></span>
                            </div>

                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="hours[6][is_open]" :value="days[6].is_open ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[6].is_open"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Open</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="days[6].is_open">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                    <input type="time" 
                                           x-model="days[6].open_time"
                                           name="hours[6][open_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[6].is_open">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                    <input type="time" 
                                           x-model="days[6].close_time"
                                           name="hours[6][close_time]"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="days[6].is_open">
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <label class="inline-flex items-center mb-4">
                                    <input type="hidden" name="hours[6][has_lunch_break]" :value="days[6].has_lunch_break ? 1 : 0">
                                    <input type="checkbox" 
                                           x-model="days[6].has_lunch_break"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                                </label>

                                <div x-show="days[6].has_lunch_break" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                        <input type="time" 
                                               x-model="days[6].lunch_start"
                                               name="hours[6][lunch_start]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[6].has_lunch_break">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                        <input type="time" 
                                               x-model="days[6].lunch_end"
                                               name="hours[6][lunch_end]"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               :required="days[6].has_lunch_break">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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