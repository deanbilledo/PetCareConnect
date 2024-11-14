<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex-shrink-0 p-4 w-56">
            <a href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Pet Care Connect Logo" class="h-auto w-full max-w-[200px]">
            </a>
        </div>

        <!-- Modern Centered Navigation -->
        <nav class="hidden lg:flex flex-1 justify-center">
            <div class="flex items-center space-x-12">
                <a href="{{ route('home') }}" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                    <span class="text-base tracking-wide font-medium nav-active">Home</span>
                </a>

                <a href="#" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                    <span class="text-base tracking-wide text-gray-700 hover:text-gray-900">Appointments</span>
                </a>

                <a href="#" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                    <span class="text-base tracking-wide text-gray-700 hover:text-gray-900">Grooming</span>
                </a>

                <a href="#" class="group flex items-center py-2 px-4 rounded-md transition-colors duration-200 hover:bg-gray-200">
                    <span class="text-base tracking-wide text-gray-700 hover:text-gray-900">Pet Clinics</span>
                </a>
            </div>
        </nav>

        <div class="flex items-center space-x-2">
            @auth
                <!-- Notification Button -->
                <button class="mr-4">
                    <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <img 
                        src="{{ auth()->user()->profile_photo_url }}" 
                        alt="User profile" 
                        class="h-8 w-8 rounded-full cursor-pointer object-cover" 
                        @click="open = !open"
                    >
                    
                    <div x-show="open" 
                         @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Sign out
                            </button>
                        </form>
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