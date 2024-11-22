<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Pet Care Connect') }} - Shop Dashboard</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('styles')
</head>
<body class="bg-gray-100 font-[Poppins] min-h-screen flex flex-col">
    <!-- Fixed Header -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        @include('partials.header')
    </div>

    <!-- Main Content with Sidebar -->
    <div class="pt-16 flex"> <!-- Add padding-top for fixed header -->
        <!-- Fixed Sidebar -->
        <div class="fixed left-0 top-16 bottom-0 w-56 bg-white shadow-md overflow-y-auto">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="ml-56 flex-1">
            <main class="p-6">
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

    @stack('scripts')
</body>
</html> 