<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shop-id" content="{{ auth()->user()->shop->id }}">
    
    <title>{{ config('app.name', 'Pet Care Connect') }} - Shop Dashboard</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Custom CSS to fix sidebar behavior */
        .sidebar-container {
            transition: all 0.3s ease-in-out;
        }
        
        @media (max-width: 768px) {
            /* Hide sidebar on mobile completely */
            .sidebar-container {
                display: none !important;
            }
            
            /* Full width content on mobile */
            .content-area-wrapper {
                width: 100% !important;
                margin-left: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
        
        /* Main content container max-width */
        .main-container {
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
            position: relative;
        }
        
        /* Content area centering */
        .content-area-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        /* Improve scrolling experience on mobile */
        html, body {
            position: relative;
            overflow-x: hidden;
            touch-action: manipulation;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Screen size specific adjustments */
        @media (min-width: 1600px) {
            .main-container {
                max-width: 1600px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body class="font-sans text-gray-800 bg-gray-100 antialiased" x-data="{ sidebarOpen: false }">
    <!-- Toast Notifications -->
    <div 
        x-data="{
            toasts: [],
            toastCount: 0,
            add(toast) {
                const id = Date.now() + this.toastCount++;
                const newToast = {...toast, id };
                this.toasts.unshift(newToast);
                setTimeout(() => {
                    this.remove(newToast.id);
                }, toast.timeout || 5000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(toast => toast.id !== id);
            }
        }"
        @toast.window="add($event.detail)"
        class="fixed top-4 right-4 z-50 flex flex-col space-y-4 w-full max-w-xs">
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-full opacity-0"
                :class="{
                    'bg-green-100 border-green-400 text-green-700': toast.type === 'success',
                    'bg-red-100 border-red-400 text-red-700': toast.type === 'error',
                    'bg-blue-100 border-blue-400 text-blue-700': toast.type === 'info',
                    'bg-yellow-100 border-yellow-400 text-yellow-700': toast.type === 'warning'
                }"
                class="border-l-4 p-4 rounded shadow-md flex justify-between items-start">
                <div class="flex">
                    <div class="flex-shrink-0 mr-3">
                        <template x-if="toast.type === 'success'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </template>
                    </div>
                    <span x-text="toast.message"></span>
                </div>
                <button @click="remove(toast.id)" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
    
    <!-- Modal Component -->
    <div 
        x-data="{ 
            show: false, 
            title: '', 
            content: '', 
            onConfirm: null
        }"
        x-show="show"
        x-cloak
        @modal.window="
            show = true;
            title = $event.detail.title;
            content = $event.detail.content;
            onConfirm = $event.detail.onConfirm || function(){};
        "
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50"
            @click="show = false"
        ></div>
        
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 relative"
            @click.away="show = false"
        >
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" x-text="title"></h3>
            </div>
            <div class="p-6">
                <div class="text-gray-700" x-html="content"></div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                <button 
                    @click="show = false"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
                >
                    Cancel
                </button>
                <button 
                    @click="onConfirm(); show = false"
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                >
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Fixed Header -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <div class="main-container">
            @include('partials.header')
        </div>
    </div>

    <!-- Main Content with Sidebar -->
    <div class="pt-16 flex flex-col w-full" x-data="{ sidebarOpen: false }" x-init="$watch('sidebarOpen', value => document.body.classList.toggle('overflow-hidden', value && window.innerWidth < 768))">
        <!-- Mobile Hamburger Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="md:hidden fixed top-4 left-4 z-30 p-2 bg-gray-50 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150" 
                aria-label="Toggle sidebar">
            <svg x-show="!sidebarOpen" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="sidebarOpen" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <!-- Sidebar overlay for mobile -->
        <div x-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden transition-opacity" 
            x-transition:enter="transition ease-out duration-200" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition ease-in duration-150" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0">
        </div>
        
        <div class="flex-1 flex">
            <!-- Fixed Sidebar container -->
            <div class="bg-gray-50 overflow-y-auto sidebar-container transition-all duration-300 shrink-0 h-[calc(100vh-4rem)] hidden md:block"
                :class="{
                    'fixed left-0 top-16 bottom-0 z-45': true,
                    'w-64 md:w-56': !$store.sidebar.collapsed, 
                    'w-20': $store.sidebar.collapsed
                }">
                @include('partials.sidebar')
            </div>

            <!-- Main Content Area Wrapper for Centering -->
            <div class="flex-1 transition-all duration-300 content-area-wrapper"
                :class="{
                    'ml-56': !$store.sidebar.collapsed && window.innerWidth >= 768,
                    'ml-20': $store.sidebar.collapsed && window.innerWidth >= 768,
                    'ml-0': window.innerWidth < 768,
                    'main-content-shifted': sidebarOpen && window.innerWidth < 768
                }">
                <!-- Main Content Container for Max Width -->
                <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    <main class="py-4 sm:py-6 md:pt-16">
                        <!-- Mobile header spacer - only visible on mobile -->
                        <div class="h-16 md:hidden"></div>
                        
                        @if(session('success'))
                            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize Session Messages as Toast Notifications -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                collapsed: true,
                toggle() {
                    this.collapsed = !this.collapsed;
                    
                    // Only update sidebar width on desktop
                    if (window.innerWidth >= 768) {
                        const mainContent = document.querySelector('.content-area-wrapper');
                        if (mainContent) {
                            mainContent.classList.toggle('ml-20', this.collapsed);
                            mainContent.classList.toggle('ml-56', !this.collapsed);
                        }
                        
                        // Make sure layout container has the right background color
                        document.querySelectorAll('.fixed.inset-y-0').forEach(el => {
                            el.classList.add('bg-gray-50');
                        });
                    }
                }
            });
            
            // Ensure background color on initial load
            document.querySelectorAll('.fixed.inset-y-0').forEach(el => {
                el.classList.add('bg-gray-50');
            });
            
            // Apply collapsed sidebar style on initial load
            if (window.innerWidth >= 768) {
                const mainContent = document.querySelector('.content-area-wrapper');
                if (mainContent) {
                    mainContent.classList.add('ml-20');
                    mainContent.classList.remove('ml-56');
                }
            }
            
            // Handle mobile-specific initialization
            const handleResize = () => {
                const isMobile = window.innerWidth < 768;
                
                // Mobile specific adjustments can go here
                document.querySelectorAll('.content-area-wrapper').forEach(el => {
                    el.classList.toggle('mobile-view', isMobile);
                });
            };
            
            // Run on init and add listener for window resize
            handleResize();
            window.addEventListener('resize', handleResize);
            
            @if(session('success'))
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: '{{ session('success') }}'
                    }
                }));
            @endif
            
            @if(session('error'))
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: '{{ session('error') }}'
                    }
                }));
            @endif
            
            @if(session('info'))
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'info',
                        message: '{{ session('info') }}'
                    }
                }));
            @endif
            
            @if(session('warning'))
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'warning',
                        message: '{{ session('warning') }}'
                    }
                }));
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html> 