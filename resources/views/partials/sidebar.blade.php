<!-- Desktop-only sidebar -->
<div class="relative h-screen bg-gray-50 overflow-x-hidden">
    <!-- Sidebar content -->
    <nav x-data="{ 
            currentFragment: window.location.hash, 
            appointmentNotifications: 0,
            refreshAppointmentNotifications() {
                fetch('{{ route('notifications.index') }}?type=appointment', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.appointmentNotifications = data.unread_count || 0;
                })
                .catch(error => console.error('Error fetching notifications:', error));
            }
         }"
         :class="{'w-64 md:w-56': !$store.sidebar.collapsed, 'w-20': $store.sidebar.collapsed}"
         class="h-screen sticky top-0 bg-gray-50 border-r transition-all duration-300 ease-in-out overflow-x-hidden flex flex-col"
         x-init="
            refreshAppointmentNotifications();
            
            // Listen for new appointment notifications
            window.addEventListener('appointment-notification-received', () => {
                refreshAppointmentNotifications();
            });
            
            // Listen for notification read events
            window.addEventListener('appointment-notification-read', () => {
                refreshAppointmentNotifications();
            });
            
            // Refresh every minute to keep updated
            setInterval(() => refreshAppointmentNotifications(), 60000);
         ">
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
            
        <!-- MAIN header section -->
        <div class="px-6 pt-4" :class="{ 'text-center': $store.sidebar.collapsed }">
                <div class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">
                    <span x-show="!$store.sidebar.collapsed">MAIN</span>
                    <span x-show="$store.sidebar.collapsed" class="text-[9px]">MAIN</span>
                </div>
                <div class="w-10 h-0.5 bg-gray-200 rounded-full" :class="{ 'mx-auto': $store.sidebar.collapsed }"></div>
            </div>
            
        <!-- Navigation Links with improved visual feedback -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden py-4">
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
                <div class="relative">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
                    <!-- Notification Badge -->
                    <div x-show="appointmentNotifications > 0" 
                         class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
                         :class="{ 'h-4 w-4 -right-1': appointmentNotifications < 10, 'h-5 px-1 -right-2': appointmentNotifications >= 10 }">
                        <span x-text="appointmentNotifications < 10 ? appointmentNotifications : '9+'"></span>
                    </div>
                </div>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Appointments</span>
                <!-- Notification Badge for uncollapsed state -->
                <div x-show="!$store.sidebar.collapsed && appointmentNotifications > 0" 
                     class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 px-1.5 flex items-center justify-center">
                    <span x-text="appointmentNotifications < 100 ? appointmentNotifications : '99+'"></span>
                </div>
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

            <!-- MANAGEMENT header section -->
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

            <!-- Payments -->
            <a href="{{ route('shop.payments') }}" 
                class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.payments') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
                :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Payments</span>
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

            <!-- Subscriptions -->
            <a href="{{ route('shop.subscriptions') }}" 
                class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.subscriptions') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
                :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 104 0V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9a2 2 0 10-4 0v5a2 2 0 104 0V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Subscriptions</span>
            </a>

            <!-- Settings -->
            <a href="{{ route('shop.settings') }}" 
                class="group flex items-center px-6 py-3 mb-1 transition-all duration-150 ease-in-out {{ request()->routeIs('shop.settings') ? 'text-blue-600 border-r-4 border-blue-600 bg-blue-50/50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }}"
                :class="{ 'justify-center': $store.sidebar.collapsed }">
                <svg class="h-6 w-6 flex-shrink-0" :class="{ 'mr-3': !$store.sidebar.collapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="font-medium" x-show="!$store.sidebar.collapsed">Settings</span>
            </a>
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
                
                // Adjust content container to full viewport height
                document.querySelectorAll('.fixed.inset-y-0').forEach(el => {
                    el.classList.add('bg-gray-50');
                    el.classList.add('min-h-screen');
                });
            }
        });
        
        // Ensure background color on initial load
        document.querySelectorAll('.fixed.inset-y-0').forEach(el => {
            el.classList.add('bg-gray-50');
            el.classList.add('min-h-screen');
        });
        
        // Apply collapsed sidebar style on initial load
        const mainContent = document.querySelector('.ml-56');
        if (mainContent) {
            mainContent.classList.add('ml-20');
            mainContent.classList.remove('ml-56');
        }
        
        // Ensure sidebar height adjusts to screen size
        function adjustSidebarHeight() {
            const sidebarHeight = window.innerHeight;
            const sidebar = document.querySelector('.sidebar-container');
            if (sidebar) {
                sidebar.style.height = `${sidebarHeight}px`;
            }
        }
        
        // Initial adjustment and on resize
        adjustSidebarHeight();
        window.addEventListener('resize', adjustSidebarHeight);
    });
</script> 