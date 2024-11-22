<nav class="h-full bg-white" x-data="{ currentFragment: window.location.hash }">
    
    <div class="p-4 border-b"></div>

    <!-- Navigation Links -->
    <div class="py-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('shop.dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('shop.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Appointments -->
        <a href="{{ route('shop.appointments') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('shop.appointments') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="font-medium">Appointments</span>
        </a>

        <!-- Services -->
        <a href="{{ route('shop.services') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('shop.services') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="font-medium">Services</span>
        </a>

        <!-- Employees -->
        <a href="{{ route('shop.employees') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('shop.employees') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="font-medium">Employees</span>
        </a>

        <!-- Analytics -->
        <a href="{{ route('shop.analytics') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('shop.analytics') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="font-medium">Analytics</span>
        </a>

        <!-- Settings Section -->
        <div class="space-y-1">
            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Settings</p>
            
            <!-- Shop Profile Settings -->
            <a href="{{ route('shop.settings') }}#shop-profile" 
               class="flex items-center px-4 py-2 text-sm transition-colors"
               :class="{ 'bg-blue-50 text-blue-600': currentFragment === '#shop-profile' || currentFragment === '', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': currentFragment !== '#shop-profile' && currentFragment !== '' }">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Shop Profile</span>
            </a>

            <!-- Business Hours -->
            <a href="{{ route('shop.settings') }}#business-hours" 
               class="flex items-center px-4 py-2 text-sm transition-colors"
               :class="{ 'bg-blue-50 text-blue-600': currentFragment === '#business-hours', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': currentFragment !== '#business-hours' }">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Business Hours</span>
            </a>

            <!-- Notifications -->
            <a href="{{ route('shop.settings') }}#notifications" 
               class="flex items-center px-4 py-2 text-sm transition-colors"
               :class="{ 'bg-blue-50 text-blue-600': currentFragment === '#notifications', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': currentFragment !== '#notifications' }">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Notifications</span>
            </a>

            <!-- Security -->
            <a href="{{ route('shop.settings') }}#security" 
               class="flex items-center px-4 py-2 text-sm transition-colors"
               :class="{ 'bg-blue-50 text-blue-600': currentFragment === '#security', 'text-gray-600 hover:bg-gray-50 hover:text-gray-900': currentFragment !== '#security' }">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Security</span>
            </a>
        </div>

        <!-- Divider -->
        <div class="border-t my-4"></div>

        <!-- Switch to Customer Mode -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
            <a href="{{ route('switch.to.customer') }}" 
               class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span class="font-medium">Switch to Customer Mode</span>
            </a>
        </div>
    </div>

    <!-- Add event listener for hash changes -->
    <script>
        window.addEventListener('hashchange', function() {
            Alpine.store('currentFragment', window.location.hash);
        });
    </script>
</nav> 