<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Pet Care Connect') }}</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="anonymous"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin="anonymous"></script>
   
    <style>
        [x-cloak] { 
            display: none !important; 
        }
    </style>
    @yield('styles')
    @stack('scripts')
</head>
<body class="bg-gray-100 font-[Poppins] min-h-screen min-h-screen flex flex-col">
    @php
        $showSidebar = auth()->check() && 
                      auth()->user()->shop && 
                      session()->has('shop_mode') && 
                      session('shop_mode') === true &&
                      !request()->routeIs('home') &&
                      !request()->is('/');
    @endphp

    <!-- Fixed Header -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        @include('partials.header')
    </div>

    <!-- Main Content with Sidebar -->
    <div class="pt-16"> <!-- Add padding-top for fixed header -->
        @if($showSidebar)
            <!-- Fixed Sidebar -->
            <div class="fixed left-0 top-16 bottom-0 w-56 bg-white shadow-md overflow-y-auto">
                @include('partials.sidebar')
            </div>
            <!-- Main Content with margin for sidebar -->
            <div class="ml-56 ">
                <main class="p-6 ">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        @else
            <!-- Main Content without sidebar -->
            <div>
                <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-10" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
            <!-- Only show footer in non-shop mode -->
            @include('partials.footer')
        @endif
    </div>

    @if(session()->has('mode_switch'))
    <script>
        if (performance && performance.navigation.type === performance.navigation.TYPE_BACK_FORWARD) {
            location.reload(true);
        }
    </script>
    @endif
</body>
</html> 