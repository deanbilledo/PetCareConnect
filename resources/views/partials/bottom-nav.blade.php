<!-- Mobile Bottom Navigation Bar -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden" 
     x-data="{ 
        activeTab: null,
        showMainDropup: false,
        showManageDropup: false,
        appointmentCount: 0,
        refreshAppointmentCount() {
            // Use the standard notifications endpoint and add a query param for type
            fetch('{{ route('notifications.index') }}?type=appointment', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                cache: 'no-store'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        // First check if response is empty
                        if (!text || text.trim() === '') {
                            console.warn('Empty response received');
                            return { unread_count: 0 };
                        }
                        
                        // Try to clean the response if it contains invalid JSON characters
                        // (like leading commas, BOM marks, etc.)
                        let cleanText = text;
                        // Remove BOM if present
                        cleanText = cleanText.replace(/^\uFEFF/, '');
                        // Remove any leading commas or invalid characters
                        cleanText = cleanText.replace(/^[,\s]+/, '');
                        
                        // Try to parse JSON
                        return JSON.parse(cleanText);
                    } catch (error) {
                        console.error('Error parsing JSON response:', error, 'Response text:', text);
                        throw new Error('Invalid JSON in response');
                    }
                });
            })
            .then(data => {
                this.appointmentCount = data.unread_count || 0;
            })
            .catch(error => {
                console.error('Error fetching appointment count:', error);
                // Set to 0 on error to avoid undefined
                this.appointmentCount = 0;
            });
        },
        safeRefresh() {
            try {
                this.refreshAppointmentCount();
            } catch(e) {
                console.error('Failed to refresh appointment count:', e);
                this.appointmentCount = 0;
            }
        }
     }"
     x-init="
        // Initialize by fetching notification counts - no try-catch here to avoid Alpine.js syntax errors
        refreshAppointmentCount();
        
        // Listen for notification updates
        window.addEventListener('appointment-notification-received', () => {
            refreshAppointmentCount();
        });
        
        // Set up interval to refresh counts - moved try-catch into a method instead
        setInterval(() => refreshAppointmentCount(), 60000);
        
        // Close dropups when clicking outside
        document.addEventListener('click', (e) => {
            if (!$el.contains(e.target)) {
                showMainDropup = false;
                showManageDropup = false;
            }
        });
     ">
    
    <!-- Background overlay when dropup is open -->
    <div x-show="showMainDropup || showManageDropup" 
         class="fixed inset-0 bg-black bg-opacity-25 z-40"
         style="margin-bottom: 4rem;"
         @click="showMainDropup = false; showManageDropup = false"></div>
    
    <!-- Bottom Navigation Tabs -->
    <div class="flex items-center justify-around h-16 relative z-50">
        <!-- Main Tab -->
        <button @click="showMainDropup = !showMainDropup; showManageDropup = false;" 
                class="flex flex-col items-center justify-center w-1/2 h-full focus:outline-none relative"
                :class="{'bg-blue-50 text-blue-600': showMainDropup, 'text-gray-600': !showMainDropup}">
            <div class="relative">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <!-- Notification badge -->
                <template x-if="appointmentCount > 0">
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" 
                          x-text="appointmentCount > 9 ? '9+' : appointmentCount">
                    </span>
                </template>
            </div>
            <span class="text-xs font-medium">Main</span>
            <svg class="h-4 w-4 transition-transform absolute right-4"
                 :class="{'transform rotate-180': showMainDropup}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>
        
        <!-- Manage Tab -->
        <button @click="showManageDropup = !showManageDropup; showMainDropup = false;" 
                class="flex flex-col items-center justify-center w-1/2 h-full focus:outline-none relative"
                :class="{'bg-blue-50 text-blue-600': showManageDropup, 'text-gray-600': !showManageDropup}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            </svg>
            <span class="text-xs font-medium">Manage</span>
            <svg class="h-4 w-4 transition-transform absolute right-4"
                 :class="{'transform rotate-180': showManageDropup}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>
    
    <!-- Main Navigation Dropup -->
    <div x-show="showMainDropup"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-8"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-8"
         class="absolute bottom-16 inset-x-0 bg-white border-t border-gray-200 shadow-lg rounded-t-lg max-h-[70vh] overflow-y-auto z-50">
        <div class="p-2 grid grid-cols-3 gap-1">
            <!-- Dashboard -->
            <a href="{{ route('shop.dashboard') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs">Dashboard</span>
            </a>
            
            <!-- Appointments -->
            <a href="{{ route('shop.appointments') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg relative {{ request()->routeIs('shop.appointments') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <div class="relative">
                    <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <template x-if="appointmentCount > 0">
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" 
                              x-text="appointmentCount > 9 ? '9+' : appointmentCount">
                        </span>
                    </template>
                </div>
                <span class="text-xs">Appointments</span>
            </a>
            
            <!-- Services -->
            <a href="{{ route('shop.services') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.services') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="text-xs">Services</span>
            </a>
        </div>
    </div>
    
    <!-- Manage Navigation Dropup -->
    <div x-show="showManageDropup"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-8"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-8"
         class="absolute bottom-16 inset-x-0 bg-white border-t border-gray-200 shadow-lg rounded-t-lg max-h-[70vh] overflow-y-auto z-50">
        <div class="p-2 grid grid-cols-3 gap-1">
            <!-- Employees -->
            <a href="{{ route('shop.employees.index') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.employees.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-xs">Employees</span>
            </a>
            
            <!-- Payments -->
            <a href="{{ route('shop.payments') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.payments') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-xs">Payments</span>
            </a>
            
            <!-- Analytics -->
            <a href="{{ route('shop.analytics') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.analytics') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-xs">Analytics</span>
            </a>
            
            <!-- Reviews -->
            <a href="{{ route('shop.reviews') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.reviews') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <span class="text-xs">Reviews</span>
            </a>
            
            <!-- Subscriptions -->
            <a href="{{ route('shop.subscriptions.index') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.subscriptions.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 104 0V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9a2 2 0 10-4 0v5a2 2 0 104 0V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-xs">Subscriptions</span>
            </a>
            
            <!-- Settings -->
            <a href="{{ route('shop.settings') }}" 
               class="flex flex-col items-center py-3 px-2 rounded-lg {{ request()->routeIs('shop.settings') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-xs">Settings</span>
            </a>
            
            <!-- Logout -->
            <a href="{{ route('logout') }}" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="flex flex-col items-center py-3 px-2 rounded-lg text-gray-600 hover:bg-gray-100">
                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-xs">Logout</span>
            </a>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</div>

<!-- Add padding to the bottom of the page on mobile to account for the bottom nav -->
<div class="h-16 md:hidden"></div>
