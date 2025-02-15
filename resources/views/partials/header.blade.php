<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex-shrink-0 p-4 w-56">
            <a href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Pet Care Connect Logo" class="h-auto w-full max-w-[200px]">
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
                <div class="relative" x-data="{ showNotifications: false }" @click.away="showNotifications = false">
                    <button @click="showNotifications = !showNotifications" 
                            class="mr-4 relative p-1 rounded-full hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Notification Badge -->
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                            3
                        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="showNotifications"
                         x-cloak
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95">
                        
                        <!-- Notification Header -->
                        <div class="px-4 py-2 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                <button class="text-xs text-blue-500 hover:text-blue-600">Mark all as read</button>
                            </div>
                        </div>

                        <!-- Notification Items -->
                        <div class="max-h-64 overflow-y-auto">
                            <!-- Unread Notification -->
                            <div class="px-4 py-3 hover:bg-gray-50 border-l-4 border-blue-500">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">New Appointment Request</p>
                                        <p class="mt-1 text-sm text-gray-500">You have a new appointment request from John Doe</p>
                                        <p class="mt-1 text-xs text-gray-400">2 minutes ago</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Read Notification -->
                            <div class="px-4 py-3 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Appointment Confirmed</p>
                                        <p class="mt-1 text-sm text-gray-500">Your appointment with Pet Grooming has been confirmed</p>
                                        <p class="mt-1 text-xs text-gray-400">1 hour ago</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Another Notification -->
                            <div class="px-4 py-3 hover:bg-gray-50 border-l-4 border-blue-500">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Appointment Reminder</p>
                                        <p class="mt-1 text-sm text-gray-500">Your appointment is scheduled in 1 hour</p>
                                        <p class="mt-1 text-xs text-gray-400">30 minutes ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Footer -->
                        <div class="px-4 py-2 border-t border-gray-200">
                            <a href="#" class="text-sm text-blue-500 hover:text-blue-600 block text-center">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <!-- Profile Image -->
                        <img src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : asset('images/default-profile.png') }}" 
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
                        <div class="py-1">
                            <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>
                            <a href="{{ route('profile.pets.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Pet Dashboard
                            </a>
                            <a href="{{ route('profile.pets.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6l-4.5 4.5M19 6l-4.5 4.5M8.5 12h7M8.5 15h7M8.5 18h7"/>
                                </svg>
                                My Pets
                            </a>
                            <a href="{{ route('settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Settings
                            </a>
                            <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                                <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
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