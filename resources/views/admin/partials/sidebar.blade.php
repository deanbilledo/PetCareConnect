<aside id="sidebar" class="bg-white text-gray-800 w-64 min-h-screen p-4 transition-all duration-300 ease-in-out transform rounded-r-3xl shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)]">
    <div class="flex items-center mb-12">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-180 h-70">
    </div>
    <nav>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-home mr-3 w-6"></i>Dashboard
        </a>
        <a href="{{ route('admin.shops') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.shops') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-store mr-3 w-6"></i>Shops
        </a>
        <a href="{{ route('admin.users') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.users') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-users mr-3 w-6"></i>Users
        </a>
        <a href="{{ route('admin.services') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.services') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-briefcase mr-3 w-6"></i>Services
        </a>
        <a href="{{ route('admin.payments') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.payments') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-credit-card mr-3 w-6"></i>Payments
        </a>
        <a href="{{ route('admin.reports') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.reports') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-chart-bar mr-3 w-6"></i>Reports
        </a>
        <a href="{{ route('admin.profile') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.profile') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-user mr-3 w-6"></i>Profile
        </a>
        <a href="{{ route('admin.settings') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.settings') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-cog mr-3 w-6"></i>Settings
        </a>
        <a href="{{ route('admin.support') }}" class="flex items-center py-2.5 px-4 rounded-xl transition duration-200 hover:bg-gray-100 mb-1 {{ request()->routeIs('admin.support') ? 'bg-gray-100' : '' }}">
            <i class="fas fa-life-ring mr-3 w-6"></i>Support
        </a>
    </nav>
</aside> 