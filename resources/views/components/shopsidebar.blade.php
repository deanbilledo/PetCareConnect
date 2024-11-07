
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<nav class="min-h-screen w-64 bg-white border-r border-gray-200 flex flex-col fixed top-0 left-0 transition-all duration-300">
    {{-- Logo Section --}}
    <div class="p-4 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center cursor-pointer toggle-sidebar">
                <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800 ml-2 sidebar-text">Pet Care Connect</span>
        </div>
    </div>

    {{-- Navigation Links --}}
    <div class="flex-1 p-2">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}" id="dashboard"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('appointments') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('appointments') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Appointments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employees') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('employees') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Employees</span>
                </a>
            </li>
            <li>
                <a href="{{ route('services') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('services') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Services</span>
                </a>
            </li>
            <li>
                <a href="{{ route('billingandpayments') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('billingandpayments') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Billing and Payments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('analytics') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('analytics') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Analytics</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customers') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('customers') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Customer</span>
                </a>
            </li>
            <li>
                <a href="{{ route('account') }}"
                   class="flex items-center p-2 rounded-lg {{ request()->routeIs('account') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">Account</span>
                </a>
            </li>
        </ul>
    </div>
    </div>

    {{-- Logout Section --}}
    <div class="p-4 border-t border-gray-200 flex-shrink-0">
        <form method="POST" action="">
            @csrf
            <button type="submit" class="flex items-center p-2 w-full rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="ml-3 sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</nav>

<script>
$(document).ready(function() {
    $('.toggle-sidebar').on('click', function() {
        // Toggle navigation width
        $('nav').toggleClass('w-64 w-16');
        
        // Toggle text visibility
        $('.sidebar-text').toggleClass('hidden');
        
        // Adjust padding and margins when collapsed
        if ($('nav').hasClass('w-16')) {
            $('.toggle-sidebar').addClass('mx-auto').removeClass('mr-2');
            $('nav a').addClass('justify-center').removeClass('justify-start');
            $('nav .p-4').addClass('p-2').removeClass('p-4');
            // Expand main content
            $('section').addClass('ml-16').removeClass('ml-64');
            // Adjust header
            $('.header').addClass('left-16 w-[calc(100%-64px)]').removeClass('left-64 w-[calc(100%-254px)]');
        } else {
            $('.toggle-sidebar').removeClass('mx-auto').addClass('mr-2');
            $('nav a').removeClass('justify-center').addClass('justify-start');
            $('nav .p-2').removeClass('p-2').addClass('p-4');
            // Shrink main content
            $('section').removeClass('ml-16').addClass('ml-64');
            // Adjust header
            $('.header').removeClass('left-16 w-[calc(100%-64px)]').addClass('left-64 w-[calc(100%-254px)]');
        }
    });
});
</script>
</body>
</html>