<!-- Desktop-only sidebar -->
<div class="relative h-full bg-gray-50">
    <!-- Sidebar content -->
    <nav x-data="{ currentFragment: window.location.hash }"
         :class="{'w-64': !$store.sidebar.collapsed, 'w-20': $store.sidebar.collapsed}"
         class="h-full bg-gray-50 border-r transition-all duration-300 ease-in-out overflow-hidden">
        <!-- Collapse sidebar button - desktop only -->
        <button @click="$store.sidebar.toggle()" 
                    class="flex items-center justify-center w-full py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors text-base border-t font-medium mt-10">
                <svg x-show="!$store.sidebar.collapsed" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <svg x-show="$store.sidebar.collapsed" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
                <span x-show="!$store.sidebar.collapsed" class="ml-2 font-medium">Collapse</span>
            </button>
        <!-- Navigation Links with improved visual feedback -->
        <div class="py-6 overflow-y-auto h-[calc(100%-6rem)] mt-4">
            <div class="px-6 mb-6" :class="{ 'text-center': $store.sidebar.collapsed }">
                <div class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">
                    <span x-show="!$store.sidebar.collapsed">MAIN</span>
                    <span x-show="$store.sidebar.collapsed" class="text-[9px]">MAIN</span>
                </div>
                <div class="w-10 h-0.5 bg-gray-200 rounded-full" :class="{ 'mx-auto': $store.sidebar.collapsed }"></div>
            </div>
            
        <!-- Dashboard -->
        <a href="{{ route('shop.dashboard') }}" 
               class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.dashboard') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
               :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Dashboard</span>
        </a>

        <!-- Appointments -->
        <a href="{{ route('shop.appointments') }}" 
               class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.appointments') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
               :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Appointments</span>
        </a>

        <!-- Services -->
        <a href="{{ route('shop.services') }}" 
               class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.services') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
               :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Services</span>
        </a>

            <div class="px-6 my-6" :class="{ 'text-center': $store.sidebar.collapsed }">
                <div class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">
                    <span x-show="!$store.sidebar.collapsed">MANAGEMENT</span>
                    <span x-show="$store.sidebar.collapsed" class="text-[9px]">MANAGE</span>
                </div>
                <div class="w-10 h-0.5 bg-gray-200 rounded-full" :class="{ 'mx-auto': $store.sidebar.collapsed }"></div>
            </div>

        <!-- Employees -->
        <a href="{{ route('shop.employees.index') }}" 
               class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.employees.*') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
               :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Employees</span>
        </a>

        <!-- Analytics -->
        <a href="{{ route('shop.analytics') }}" 
               class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.analytics') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
               :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Analytics</span>
        </a>

        <!-- Reviews -->
        <a href="{{ route('shop.reviews') }}" 
               class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.reviews') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
               :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Reviews</span>
            </a>
    </div>

        <!-- Switch to Customer Mode with improved styling -->
        <div class="absolute bottom-0 left-0 right-0 border-t bg-gray-50">
            <form action="{{ route('shop.mode.customer') }}" method="POST" class="p-3">
            @csrf
            <button type="submit" 
                        class="w-full flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all duration-150 ease-in-out"
                        :class="{ 'px-2': $store.sidebar.collapsed }">
                    <svg class="h-5 w-5 flex-shrink-0" :class="{ 'mr-2': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="font-medium" x-show="!$store.sidebar.collapsed">Switch to Customer Mode</span>
                </button>
            </form>

            
        </div>
    </nav>
</div>

<!-- Initialize Alpine.js store for sidebar state -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('sidebar', {
            collapsed: true,
            toggle() {
                this.collapsed = !this.collapsed;
                // Update parent container to accommodate sidebar width
                const mainContent = document.querySelector('.ml-56');
                if (mainContent) {
                    mainContent.classList.toggle('ml-20', this.collapsed);
                    mainContent.classList.toggle('ml-56', !this.collapsed);
                }
                
                // Make sure layout container has the right background color
                document.querySelectorAll('.fixed.inset-y-0').forEach(el => {
                    el.classList.add('bg-gray-50');
                });
            }
        });
        
        // Ensure background color on initial load
        document.querySelectorAll('.fixed.inset-y-0').forEach(el => {
            el.classList.add('bg-gray-50');
        });
        
        // Apply collapsed sidebar style on initial load
        const mainContent = document.querySelector('.ml-56');
        if (mainContent) {
            mainContent.classList.add('ml-20');
            mainContent.classList.remove('ml-56');
        }
    });
</script> 