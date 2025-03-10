@extends('layouts.shop')

@section('content')
<div class="container mx-auto px-4 py-6" 
    x-data="shopSettings({{ 
        json_encode([
            'profile' => [
                'name' => auth()->user()->shop->name ?? '',
                'description' => auth()->user()->shop->description ?? '',
                'email' => auth()->user()->shop->contact_email ?? '',
                'phone' => auth()->user()->shop->phone ?? '',
                'address' => auth()->user()->shop->address ?? '',
                'logo' => auth()->user()->shop->logo_url ?? asset('images/default-shop.png')
            ],
            'notifications' => [
                'email_notifications' => auth()->user()->shop->email_notifications ?? false,
                'sms_notifications' => auth()->user()->shop->sms_notifications ?? false,
                'daily_summary' => auth()->user()->shop->daily_summary ?? false
            ],
            'operating_hours' => auth()->user()->shop->operatingHours->map(function($hour) {
                return [
                    'day' => $hour->day,
                    'is_open' => (bool) $hour->is_open,
                    'open_time' => $hour->open_time ? substr($hour->open_time, 0, 5) : '09:00',
                    'close_time' => $hour->close_time ? substr($hour->close_time, 0, 5) : '17:00',
                    'has_lunch_break' => (bool) $hour->has_lunch_break,
                    'lunch_start' => $hour->lunch_start ? substr($hour->lunch_start, 0, 5) : '12:00',
                    'lunch_end' => $hour->lunch_end ? substr($hour->lunch_end, 0, 5) : '13:00',
                    'name' => match($hour->day) {
                        0 => 'Sunday',
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        default => 'Unknown'
                    }
                ];
            })->toArray()
        ])
    }})">
    <h1 class="text-2xl font-bold mb-6">Shop Settings</h1>

    <!-- Shop Profile Section -->
    <div id="shop-profile" class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Shop Profile</h2>
            <span class="text-sm text-green-600 hidden" id="profile-saved">
                Changes saved successfully
            </span>
        </div>
        <form class="space-y-6" @submit.prevent="updateProfile">
            <div class="flex items-center space-x-6">
                <div class="relative group">
                    <div class="h-32 w-32 rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200 hover:border-blue-500 transition-colors duration-150">
                        <img :src="profile.logo" alt="Shop logo" class="h-full w-full object-cover">
                    </div>
                    <button type="button" class="absolute bottom-2 right-2 p-2 bg-white rounded-full shadow-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                    <input type="text" x-model="profile.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea x-model="profile.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" x-model="profile.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" x-model="profile.phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" x-model="profile.address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Business Hours Section -->
    <div id="business-hours" class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Business Hours</h2>
            <span class="text-sm text-green-600 hidden" id="hours-saved">
                Hours updated successfully
            </span>
        </div>
        
        <div class="space-y-4">
            <template x-for="(day, index) in hours.days" :key="index">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <span class="font-medium" x-text="day.name"></span>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   x-model="day.is_open"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Open</span>
                        </label>
                    </div>

                    <div x-show="day.is_open">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                <input type="time" 
                                       x-model="day.open_time"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                <input type="time" 
                                       x-model="day.close_time"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <label class="inline-flex items-center mb-4">
                                <input type="checkbox" 
                                       x-model="day.has_lunch_break"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                    </label>

                            <div x-show="day.has_lunch_break" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                    <input type="time" 
                                           x-model="day.lunch_start"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="day.has_lunch_break">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                    <input type="time" 
                                           x-model="day.lunch_end"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="day.has_lunch_break">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="mt-6">
            <button type="button" 
                    @click="updateHours"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Update Hours
            </button>
        </div>
    </div>

    <!-- Notifications Section -->
    <div id="notifications" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Notification Preferences</h2>
            <span class="text-sm text-green-600 hidden" id="notifications-saved">
                Preferences saved successfully
            </span>
        </div>
        <div class="space-y-4">
            <label class="flex items-center">
                <input type="checkbox" 
                       x-model="notifications.email_notifications"
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Email notifications for new appointments</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox"
                       x-model="notifications.sms_notifications"
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">SMS notifications for new appointments</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox"
                       x-model="notifications.daily_summary"
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Daily summary email</span>
            </label>
        </div>
        <div class="mt-6">
            <button type="button"
                    @click="updateNotifications"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Save Preferences
            </button>
        </div>
    </div>

        <!-- Security Section -->
    <div id="security" class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Security Settings</h2>
            <span class="text-sm text-green-600 hidden" id="security-saved">
                Password updated successfully
            </span>
        </div>
        <form class="space-y-6" @submit.prevent="updatePassword">
            <div>
                <label class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" 
                       x-model="security.current_password"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password"
                       x-model="security.new_password"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password"
                       x-model="security.confirm_password"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function shopSettings(initialData) {
    return {
        profile: {
            ...initialData.profile,
            logo: initialData.profile.logo || asset('images/default-shop.png')
        },
        notifications: initialData.notifications,
        security: {
            current_password: '',
            new_password: '',
            confirm_password: ''
        },
        hours: {
            days: initialData.operating_hours.length ? initialData.operating_hours : [
                { name: 'Monday', day: 1, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                { name: 'Tuesday', day: 2, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                { name: 'Wednesday', day: 3, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                { name: 'Thursday', day: 4, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                { name: 'Friday', day: 5, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                { name: 'Saturday', day: 6, is_open: true, open_time: '09:00', close_time: '17:00', has_lunch_break: true, lunch_start: '12:00', lunch_end: '13:00' },
                { name: 'Sunday', day: 0, is_open: false, open_time: '09:00', close_time: '17:00', has_lunch_break: false, lunch_start: '12:00', lunch_end: '13:00' }
            ]
        },
        showMessage(elementId) {
            const message = document.getElementById(elementId);
            message.classList.remove('hidden');
            setTimeout(() => {
                message.classList.add('hidden');
            }, 3000);
        },
        updateProfile() {
            fetch('/shop/settings/profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.profile)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMessage('profile-saved');
                }
            });
        },
        updateHours() {
            // Format the hours data before sending
            const formattedHours = this.hours.days.map(day => {
                // Ensure time values are in HH:mm:ss format
                let openTime = day.is_open ? day.open_time : null;
                let closeTime = day.is_open ? day.close_time : null;
                let lunchStart = null;
                let lunchEnd = null;

<<<<<<< HEAD
<!-- GCash Payment Modal -->
<div id="gcashModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">GCash Payment Details</h3>
            <button onclick="hideGcashModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
=======
                // Only process lunch break times if the day is open and has lunch break
                if (day.is_open && day.has_lunch_break) {
                    lunchStart = day.lunch_start;
                    lunchEnd = day.lunch_end;
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455

                    // Add seconds to lunch times if needed
                    if (lunchStart && !lunchStart.includes(':')) {
                        lunchStart = lunchStart + ':00';
                    } else if (lunchStart && lunchStart.split(':').length === 2) {
                        lunchStart = lunchStart + ':00';
                    }
                    
                    if (lunchEnd && !lunchEnd.includes(':')) {
                        lunchEnd = lunchEnd + ':00';
                    } else if (lunchEnd && lunchEnd.split(':').length === 2) {
                        lunchEnd = lunchEnd + ':00';
                    }
                }
                
                if (day.is_open) {
                    // Add seconds if they're missing for open/close times
                    if (openTime && !openTime.includes(':')) {
                        openTime = openTime + ':00';
                    } else if (openTime && openTime.split(':').length === 2) {
                        openTime = openTime + ':00';
                    }
                    
                    if (closeTime && !closeTime.includes(':')) {
                        closeTime = closeTime + ':00';
                    } else if (closeTime && closeTime.split(':').length === 2) {
                        closeTime = closeTime + ':00';
                    }
                }

                // Ensure has_lunch_break is always boolean
                const hasLunchBreak = Boolean(day.has_lunch_break);

                return {
                    day: day.day,
                    is_open: Boolean(day.is_open),
                    open_time: openTime,
                    close_time: closeTime,
                    has_lunch_break: hasLunchBreak,
                    lunch_start: lunchStart,
                    lunch_end: lunchEnd
                };
            });

            fetch('/shop/settings/hours', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ hours: formattedHours })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update hours');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    this.showMessage('hours-saved');
                } else {
                    alert(data.message || 'Failed to update hours');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to update hours. Please try again.');
            });
        },
        updateNotifications() {
            fetch('/shop/settings/notifications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.notifications)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMessage('notifications-saved');
                }
            });
        },
        updatePassword() {
            if (this.security.new_password !== this.security.confirm_password) {
                alert('New passwords do not match');
                return;
            }
            fetch('/shop/settings/security', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.security)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMessage('security-saved');
                    this.security.current_password = '';
                    this.security.new_password = '';
                    this.security.confirm_password = '';
                }
            });
        }
    }
}
</script>
@endsection 