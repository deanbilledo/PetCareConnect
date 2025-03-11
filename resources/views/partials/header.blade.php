<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Remove Shop Mode Mobile Toggle Button -->
        
        <!-- Logo with proper spacing -->
        <div class="flex-shrink-0 p-2 sm:p-4 w-32 sm:w-56">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Pet Care Connect Logo" class="h-auto w-full max-w-[120px] sm:max-w-[200px] transition-all duration-300">
            </a>
        </div>

        <!-- Modern Centered Navigation - Only show in customer mode -->
        @if(!session('shop_mode'))
            <nav class="hidden lg:flex flex-1 justify-center">
                <div class="flex items-center space-x-12" x-data="{ showLoginPrompt: false }">
                    <a href="{{ route('home') }}" 
                       class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                        <span class="text-base tracking-wide font-medium {{ request()->routeIs('home') ? 'nav-active' : 'text-gray-700 hover:text-gray-900' }}">Home</span>
                    </a>

                    @auth
                        <a href="{{ route('appointments.index') }}" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                            <span class="text-base tracking-wide {{ request()->routeIs('appointments*') ? 'nav-active' : 'text-gray-700 hover:text-gray-900' }}">Appointments</span>
                        </a>
                    @else
                        <button @click="showLoginPrompt = true" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                            <span class="text-base tracking-wide text-gray-700 hover:text-gray-900">Appointments</span>
                        </button>

                        <!-- Login Prompt Modal -->
                        <div x-show="showLoginPrompt" 
                             x-cloak
                             class="fixed inset-0 z-[60] overflow-y-auto"
                             role="dialog"
                             aria-modal="true">
                            <!-- Backdrop -->
                            <div x-show="showLoginPrompt"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                            <!-- Modal Panel -->
                            <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
                                <div x-show="showLoginPrompt"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     @click.away="showLoginPrompt = false"
                                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full max-w-sm mx-auto">
                                    
                                    <!-- Close button -->
                                    <div class="absolute right-0 top-0 pr-4 pt-4">
                                        <button @click="showLoginPrompt = false" type="button" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Close</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal content -->
                                    <div class="p-6">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                                </svg>
                                            </div>
                                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Login Required</h3>
                                            <p class="mt-2 text-sm text-gray-500">Please login or create an account to access appointments.</p>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="mt-6 flex flex-col gap-2">
                                            <a href="{{ route('login') }}" 
                                               class="w-full inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none">
                                                Login
                                            </a>
                                            <a href="{{ route('register') }}" 
                                               class="w-full inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                                Register
                                            </a>
                                            <button @click="showLoginPrompt = false" 
                                                    class="w-full inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endauth

                    <a href="{{ route('grooming') }}" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                        <span class="text-base tracking-wide {{ request()->routeIs('grooming') ? 'nav-active' : 'text-gray-700 hover:text-gray-900' }}">Grooming</span>
                    </a>

                    <a href="{{ route('petlandingpage') }}" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                        <span class="text-base tracking-wide {{ request()->routeIs('petlandingpage') ? 'nav-active' : 'text-gray-700 hover:text-gray-900' }}">Pet Clinics</span>
                    </a>
                </div>
            </nav>
        @endif

        <div class="flex items-center space-x-2">
            @auth
                <!-- Notification Button -->
                <div class="relative" x-data="{ 
                    showNotifications: false,
                    unreadCount: {{ auth()->user()->notifications()->unread()->count() }},
                    unreadAppointmentCount: {{ auth()->user()->notifications()->where('type', 'appointment')->where('status', 'unread')->count() }},
                    showAppointmentNotificationsOnly: false,
                    
                    toggleAppointmentFilter() {
                        this.showAppointmentNotificationsOnly = !this.showAppointmentNotificationsOnly;
                    },
                    
                    markAllAsRead() {
                        fetch('{{ route('notifications.markAllAsRead') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.unreadCount = 0;
                            this.unreadAppointmentCount = 0;
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.classList.remove('border-l-4', 'border-blue-500', 'bg-blue-50');
                            });
                        });
                    },
                    markAsRead(id) {
                        fetch(`/notifications/${id}/mark-as-read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.unreadCount = data.unread_count;
                            // Recalculate appointment notifications
                            const notificationElement = document.querySelector(`#notification-${id}`);
                            if (notificationElement && notificationElement.classList.contains('appointment-notification')) {
                                this.unreadAppointmentCount--;
                            }
                            
                            const notificationItem = document.querySelector(`#notification-${id}`);
                            if (notificationItem) {
                                notificationItem.classList.remove('border-l-4', 'border-blue-500', 'bg-blue-50');
                            }
                        });
                    },
                    init() {
                        // Add keyboard shortcut to toggle notifications (Ctrl+N) for laptop/desktop users
                        window.addEventListener('keydown', (e) => {
                            // Only handle Ctrl+N (or Cmd+N on Mac)
                            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                                e.preventDefault(); // Prevent browser default behavior
                                this.showNotifications = !this.showNotifications;
                                
                                // Focus on the first notification item for keyboard navigation
                                if (this.showNotifications) {
                                    setTimeout(() => {
                                        const firstNotification = document.querySelector('.notification-item');
                                        if (firstNotification) {
                                            firstNotification.focus();
                                        }
                                    }, 100);
                                }
                            }
                            
                            // Add arrow key navigation for notifications when popup is open
                            if (this.showNotifications && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
                                e.preventDefault();
                                const notifications = document.querySelectorAll('.notification-item');
                                const currentFocused = document.activeElement;
                                
                                // Find the index of the currently focused notification
                                let currentIndex = -1;
                                notifications.forEach((item, index) => {
                                    if (item === currentFocused) {
                                        currentIndex = index;
                                    }
                                });
                                
                                // Calculate the next index based on arrow key
                                let nextIndex;
                                if (e.key === 'ArrowDown') {
                                    nextIndex = currentIndex < notifications.length - 1 ? currentIndex + 1 : 0;
                                } else {
                                    nextIndex = currentIndex > 0 ? currentIndex - 1 : notifications.length - 1;
                                }
                                
                                // Focus the next notification
                                if (notifications[nextIndex]) {
                                    notifications[nextIndex].focus();
                                }
                            }
                        });
                    }
                }" 
                @click.away="showNotifications = false">
                    <button @click="showNotifications = !showNotifications" 
                            class="mr-2 sm:mr-4 relative p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:p-2 lg:transition-all lg:duration-200"
                            :class="{'lg:bg-gray-100': showNotifications}"
                            title="Notifications (Ctrl+N)">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-500" 
                             :class="{'text-blue-600': showNotifications && unreadCount > 0}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Dynamic Notification Badge -->
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-1 -left-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 lg:h-5 lg:w-5 flex items-center justify-center" x-text="unreadCount">
                            </span>
                        </template>
                        
                        <!-- Appointment Notification Badge -->
                        <template x-if="unreadAppointmentCount > 0">
                            <span class="absolute top-1 -right-1 bg-blue-500 border-2 border-white text-white text-xs rounded-full h-2 w-2 sm:h-3 sm:w-3 lg:h-3 lg:w-3 flex items-center justify-center">
                            </span>
                        </template>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="showNotifications"
                         x-cloak
                         @keydown.escape.window="showNotifications = false"
                         class="fixed inset-x-0 top-[60px] mx-auto sm:absolute sm:right-0 sm:left-auto sm:top-auto sm:inset-x-auto sm:mt-2 w-full sm:w-[450px] md:w-[550px] lg:w-[650px] xl:w-[750px] bg-white rounded-none sm:rounded-lg shadow-xl py-2 z-50 max-h-[90vh] sm:max-h-[80vh] md:max-h-[70vh] lg:max-h-[600px] flex flex-col"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-y-1 sm:scale-95"
                         x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 transform translate-y-1 sm:scale-95"
                         style="max-width: 90vw; right: 0;"
                         >
                        
                        <!-- Mobile close button - only visible on small screens -->
                        <div class="sm:hidden absolute top-2 right-2">
                            <button @click="showNotifications = false" 
                                    class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Notification Header -->
                        <div class="px-3 sm:px-5 md:px-6 lg:px-8 py-3 sm:py-4 border-b border-gray-200 flex-shrink-0">
                            <div class="flex justify-between items-center">
                                <h3 class="text-sm md:text-base lg:text-lg font-semibold text-gray-900">Notifications</h3>
                                <div class="flex space-x-2 sm:space-x-3">
                                    <button @click="toggleAppointmentFilter()" 
                                            x-show="unreadAppointmentCount > 0"
                                            class="text-xs md:text-sm text-blue-500 hover:text-blue-600 px-2 py-1 rounded-full active:bg-blue-50"
                                            :class="{'font-bold underline': showAppointmentNotificationsOnly}">
                                        <span x-text="showAppointmentNotificationsOnly ? 'Show All' : 'Appointments'"></span>
                                    </button>
                                    <button @click="markAllAsRead()" 
                                            x-show="unreadCount > 0"
                                            class="text-xs md:text-sm text-blue-500 hover:text-blue-600 px-2 py-1 rounded-full active:bg-blue-50">
                                        Mark all read
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Items -->
                        <div class="overflow-y-auto flex-grow overscroll-contain lg:scrollbar-thin lg:scrollbar-thumb-gray-300 lg:scrollbar-track-gray-100">
                            @php
                                $hasNewAppointments = false;
                                $isShopOwner = auth()->user()->isShopOwner() || auth()->user()->shop()->exists();
                                
                                // Check if there are any new appointment notifications
                                foreach(auth()->user()->notifications()->latest()->take(5)->get() as $notification) {
                                    // Only show the "You have new appointment requests" banner for shop owners
                                    // Customers will see individual notifications but not the banner
                                    if ($notification->type === 'appointment' && $notification->status === 'unread' && $isShopOwner) {
                                        $hasNewAppointments = true;
                                        break;
                                    }
                                }
                            @endphp

                            @if($hasNewAppointments)
                                <div class="px-4 sm:px-5 md:px-6 lg:px-8 py-3 bg-blue-50 border-l-4 border-blue-500">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3 sm:ml-4">
                                            <p class="text-sm sm:text-base font-medium text-blue-800">New Appointments!</p>
                                            <p class="mt-1 text-xs sm:text-sm text-blue-600">You have new appointment requests</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                <div id="notification-{{ $notification->id }}"
                                     x-show="!showAppointmentNotificationsOnly || '{{ $notification->type }}' === 'appointment'"
                                     class="notification-item px-4 sm:px-5 md:px-6 lg:px-8 py-3 sm:py-4 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-150 {{ $notification->status === 'unread' ? 'border-l-4 border-blue-500' : '' }} {{ $notification->type === 'appointment' && $notification->status === 'unread' ? 'bg-blue-50' : '' }} {{ $notification->type === 'appointment' ? 'appointment-notification' : '' }}"
                                     tabindex="0"
                                     @keydown.enter="
                                        @if($notification->action_url)
                                            window.location.href = '{{ $notification->action_url }}';
                                            markAsRead('{{ $notification->id }}');
                                        @elseif($notification->status === 'unread')
                                            markAsRead('{{ $notification->id }}');
                                        @endif
                                     ">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            {!! $notification->getIconHtml() !!}
                                        </div>
                                        <div class="ml-3 sm:ml-4 w-0 flex-1">
                                            <p class="text-sm md:text-base font-medium text-gray-900">{{ $notification->title }}</p>
                                            <p class="mt-1 text-sm text-gray-500 line-clamp-3 lg:line-clamp-none">{{ $notification->message }}</p>
                                            <div class="mt-1 flex justify-between items-center">
                                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                                @if($notification->action_url)
                                                    <a href="{{ $notification->action_url }}" 
                                                       @click="markAsRead('{{ $notification->id }}')"
                                                       class="ml-4 inline-flex items-center text-xs font-medium text-blue-600 hover:text-blue-500 py-1 px-2 rounded-full active:bg-blue-50 lg:py-1.5 lg:px-3 lg:text-sm lg:hover:bg-blue-50 lg:transition-colors lg:duration-150">
                                                        {{ $notification->action_text ?? 'View' }} <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        @if($notification->status === 'unread')
                                            <button @click="markAsRead('{{ $notification->id }}')" 
                                                    class="ml-2 p-2 text-gray-400 hover:text-gray-500 rounded-full active:bg-gray-100 lg:hover:bg-gray-100 lg:focus:ring-2 lg:focus:ring-blue-300 lg:focus:outline-none"
                                                    title="Mark as read">
                                                <span class="sr-only">Mark as read</span>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 sm:px-6 md:px-8 lg:px-10 py-6 md:py-8 lg:py-10 text-sm md:text-base text-gray-500 text-center">
                                    <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 lg:h-20 lg:w-20 text-gray-300 mb-2 sm:mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <p class="text-base md:text-lg lg:text-xl">No notifications to display</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Notification Footer -->
                        <div class="px-4 sm:px-5 md:px-6 lg:px-8 py-3 sm:py-4 border-t border-gray-200 flex-shrink-0 bg-gray-50">
                            <a href="{{ route('notifications.index') }}" 
                               class="flex items-center justify-center text-sm md:text-base text-blue-600 hover:text-blue-500 py-1 px-4 rounded-md active:bg-blue-50 transition-colors duration-150 lg:py-2 lg:hover:bg-blue-50 lg:focus:ring-2 lg:focus:ring-blue-300 lg:focus:outline-none">
                                <span>View all notifications</span>
                                <svg class="ml-1 h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <!-- Profile Image -->
                        <img src="{{ auth()->user()->profile_photo_url }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="h-8 w-8 rounded-full object-cover"
                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                        <span class="text-gray-700">{{ auth()->user()->first_name }}</span>
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                        
                        <!-- Mode Switch Section -->
                        @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                        <div class="px-4 py-2">
                            <p class="text-xs text-gray-500 mb-2">Current Mode</p>
                            <div class="space-y-2">
                                <form action="{{ route('shop.mode.customer') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center space-x-2 text-sm text-gray-700 hover:text-custom-blue">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Customer Mode</span>
                                        @if(!session('shop_mode'))
                                            <span class="ml-auto">
                                                <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </button>
                                </form>
                                <a href="{{ route('shop.dashboard') }}" 
                                   class="flex items-center space-x-2 text-sm text-gray-700 hover:text-custom-blue">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>Shop Mode</span>
                                    @if(session('shop_mode'))
                                        <span class="ml-auto">
                                            <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                        @elseif(auth()->user()->shop && auth()->user()->shop->status === 'pending')
                        <div class="px-4 py-2">
                            <p class="text-xs text-yellow-600">Shop registration pending approval</p>
                        </div>
                        @elseif(auth()->user()->shop && auth()->user()->shop->status === 'suspended')
                        <div class="px-4 py-2">
                            <p class="text-xs text-red-600">Shop access suspended</p>
                        </div>
                        @endif

                        <!-- User Actions Section -->
                        <div class="py-1" x-data="{ activeSection: null }">
                            <!-- Mobile Navigation Links (Only visible on mobile) -->
                            <div class="lg:hidden">
                                <!-- Navigation Section Toggle -->
                                <button @click="activeSection = activeSection === 'navigation' ? null : 'navigation'" 
                                        class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                        <span>Navigation Menu</span>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400 transform transition-transform duration-200" 
                                         :class="{ 'rotate-180': activeSection === 'navigation' }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- Navigation Links -->
                                <div x-show="activeSection === 'navigation'"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform translate-y-0"
                                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                                     class="px-2 py-2 space-y-1">
                                    
                                    <!-- SHOP MODE: Only shown when in shop mode -->
                                    @if(session('shop_mode'))
                                        <a href="{{ route('shop.dashboard') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('shop.dashboard') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            Dashboard
                                        </a>
                                        
                                        <a href="{{ route('shop.appointments') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('shop.appointments') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Appointments
                                        </a>
                                        
                                        <a href="{{ route('shop.services') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('shop.services') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            Services
                                        </a>
                                        
                                        <a href="{{ route('shop.employees.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('shop.employees.*') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            Employees
                                        </a>
                                        
                                        <a href="{{ route('shop.analytics') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('shop.analytics') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            Analytics
                                        </a>
                                        
                                        <a href="{{ route('shop.reviews') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('shop.reviews') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                            Reviews
                                        </a>

                                        <!-- Switch to Customer Mode -->
                                        <form action="{{ route('shop.mode.customer') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                </svg>
                                                Switch to Customer Mode
                                            </button>
                                        </form>
                                    @else
                                        <!-- REGULAR MODE: Standard navigation links -->
                                        <a href="{{ route('home') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('home') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            Home
                                        </a>

                                        @auth
                                            <a href="{{ route('appointments.index') }}" 
                                               class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('appointments*') ? 'bg-gray-100' : '' }}">
                                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Appointments
                                            </a>
                                        @else
                                            <button @click="open = false; showLoginPrompt = true" 
                                                    class="flex w-full items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Appointments
                                            </button>
                                        @endauth

                                        <a href="{{ route('grooming') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('grooming') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            Grooming
                                        </a>

                                        <a href="{{ route('petlandingpage') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('petlandingpage') ? 'bg-gray-100' : '' }}">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            Pet Clinics
                                        </a>
                                    @endif
                                </div>
                                <div class="border-t border-gray-100 my-2"></div>
                            </div>

                            <!-- Profile Section Toggle -->
                            <button @click="activeSection = activeSection === 'profile' ? null : 'profile'" 
                                    class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Profile Menu</span>
                                </div>
                                <svg class="h-4 w-4 text-gray-400 transform transition-transform duration-200" 
                                     :class="{ 'rotate-180': activeSection === 'profile' }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Profile Links -->
                            <div x-show="activeSection === 'profile'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="px-2 py-2 space-y-1">
                                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>
                                <a href="{{ route('profile.pets.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Pet Dashboard
                            </a>
                                <a href="{{ route('profile.pets.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6l-4.5 4.5M19 6l-4.5 4.5M8.5 12h7M8.5 15h7M8.5 18h7"/>
                                </svg>
                                My Pets
                            </a>
                                <a href="{{ route('settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Settings
                            </a>
                                <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                My Favorites
                            </a>
                        </div>

                        <!-- Logout Section -->
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-gray-900 rounded-md transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login
                </a>
                <a href="{{ route('register') }}" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-gray-900 rounded-md transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Sign Up
                </a>
            @endauth
        </div>
    </div>
</header>