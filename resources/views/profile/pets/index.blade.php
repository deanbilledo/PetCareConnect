@extends(session('shop_mode') ? 'layouts.shop' : 'layouts.app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
    .pet-card {
        transition: all 0.3s ease;
    }
    .pet-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .badge-green {
        @apply bg-green-100 text-green-800;
    }
    .badge-yellow {
        @apply bg-yellow-100 text-yellow-800;
    }
    .badge-blue {
        @apply bg-blue-100 text-blue-800;
    }
    .badge-red {
        @apply bg-red-100 text-red-800;
    }
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    .slide-down {
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        0% { 
            opacity: 0;
            transform: translateY(-10px);
        }
        100% { 
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 mt-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">My Pet Companions</h1>
                    <p class="text-gray-500 mt-1">Manage your pets' profiles and health records</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search pets..." 
                               class="border rounded-lg pl-10 pr-4 py-2 focus:border-teal-500 focus:ring-teal-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button onclick="openAddPetModal()" 
                            class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 shadow-sm transition duration-150 ease-in-out flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Pet
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-teal-500">
                <div class="flex items-center">
                    <div class="bg-teal-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-400 text-sm">Total Pets</h2>
                        <p class="text-2xl font-bold text-gray-900">{{ count($pets) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-400 text-sm">Avg. Health Score</h2>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $pets->count() > 0 ? round($pets->avg('health_score')) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-400 text-sm">Upcoming Appointments</h2>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pet Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($pets as $pet)
                <div class="relative h-[30rem] group rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 pet-card">
                    <!-- Background Image with Gradient Overlay -->
                    <div class="absolute inset-0">
                        <img src="{{ $pet->profile_photo_url }}" 
                             alt="{{ $pet->name }}" 
                             class="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent opacity-80"></div>
                    </div>
                    
                    <!-- Status indicator dot -->
                    <div class="absolute top-4 left-4 z-10">
                        <span class="flex h-3 w-3">
                            <span class="{{ $pet->health_score >= 80 ? 'bg-green-500' : ($pet->health_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }} animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                            <span class="{{ $pet->health_score >= 80 ? 'bg-green-500' : ($pet->health_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }} relative inline-flex rounded-full h-3 w-3"></span>
                        </span>
                    </div>
                    
                    <!-- Context menu -->
                    <div class="absolute top-4 right-4 z-10">
                        <div class="dropdown relative">
                            <button class="text-white opacity-70 hover:opacity-100 bg-black bg-opacity-30 hover:bg-opacity-50 rounded-full p-1.5 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 bg-white rounded-lg shadow-lg overflow-hidden hidden group-hover:block w-48 z-50 border border-gray-100">
                                <a href="{{ route('profile.pets.details', $pet) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Schedule Appointment
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove Pet
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Type Badge -->
                    <div class="absolute top-4 right-16">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                             {{ $pet->type == 'Dog' ? 'bg-blue-600/80 text-white' : 
                                ($pet->type == 'Cat' ? 'bg-green-600/80 text-white' : 'bg-yellow-600/80 text-white') }}">
                            {{ $pet->type }}
                        </span>
                    </div>
                    
                    <!-- Content Container - All overlay on image -->
                    <div class="absolute inset-0 flex flex-col justify-between p-6">
                        <div>
                            <!-- Empty top space for badges and buttons -->
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Pet Name and Info -->
                            <div>
                                <h3 class="text-white text-2xl font-bold mb-1">{{ $pet->name }}</h3>
                                <div class="flex items-center text-white/80 text-sm mb-2">
                                    <span>{{ $pet->breed }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $pet->date_of_birth->age }} years old</span>
                                </div>
                            </div>

                            <!-- Pet Details -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-white/10 p-2 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-white/60 uppercase">Weight</span>
                                        <span class="font-medium text-white">{{ $pet->weight }} kg</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="bg-white/10 p-2 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-white/60 uppercase">Size</span>
                                        <span class="font-medium text-white">{{ Str::title($pet->size_category) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Health Status Indicators -->
                            <div class="space-y-3 mb-4">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-white/90 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            Vaccination Status
                                        </span>
                                        <span class="text-sm font-medium px-2 py-0.5 rounded-full {{ $pet->vaccination_percentage >= 70 ? 'bg-green-500/80 text-white' : 'bg-yellow-500/80 text-white' }}">
                                            {{ $pet->vaccination_percentage }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-white/20 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $pet->vaccination_percentage >= 70 ? 'bg-green-400' : 'bg-yellow-400' }} h-1.5 rounded-full progress-bar" 
                                             style="width: {{ $pet->vaccination_percentage }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-white/90 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            Health Score
                                        </span>
                                        <span class="text-sm font-medium px-2 py-0.5 rounded-full 
                                              {{ $pet->health_score >= 80 ? 'bg-green-500/80 text-white' : 
                                                ($pet->health_score >= 60 ? 'bg-yellow-500/80 text-white' : 'bg-red-500/80 text-white') }}">
                                            {{ $pet->health_score }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-white/20 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $pet->health_score >= 80 ? 'bg-green-400' : 
                                                    ($pet->health_score >= 60 ? 'bg-yellow-400' : 'bg-red-400') }} h-1.5 rounded-full progress-bar" 
                                             style="width: {{ $pet->health_score }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Chips -->
                            <div class="flex flex-wrap gap-2 mb-5">
                                @if($pet->is_microchipped)
                                    <span class="bg-white/10 text-white text-xs px-2 py-1 rounded-full flex items-center backdrop-blur-sm">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                        Microchipped
                                    </span>
                                @endif
                                @if($pet->is_neutered)
                                    <span class="bg-white/10 text-white text-xs px-2 py-1 rounded-full flex items-center backdrop-blur-sm">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Neutered
                                    </span>
                                @endif
                                @if(isset($pet->upcoming_appointment) && $pet->upcoming_appointment)
                                    <span class="bg-white/10 text-white text-xs px-2 py-1 rounded-full flex items-center backdrop-blur-sm">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Upcoming Appt
                                    </span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                <a href="{{ route('profile.pets.details', $pet) }}" 
                                   class="flex-1 bg-white/10 backdrop-blur-sm text-white px-4 py-2.5 rounded-lg hover:bg-white/20 transition duration-150 ease-in-out text-center flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Profile
                                </a>
                                <a href="{{ route('profile.pets.health-record', $pet) }}" 
                                   class="flex-1 bg-teal-500/90 text-white px-4 py-2.5 rounded-lg hover:bg-teal-600 transition duration-150 ease-in-out text-center flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Health Record
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                        <img src="{{ asset('images/empty-pets.svg') }}" alt="No pets" class="w-32 h-32 mx-auto mb-4 opacity-75" 
                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Ik00LjMxOCA2LjMxOGE0LjUgNC41IDAgMDAwIDYuMzY0TDEyIDIwLjM2NGw3LjY4Mi03LjY4MmE0LjUgNC41IDAgMDAtNi4zNjQtNi4zNjRMMTIgNy42MzZsLTEuMzE4LTEuMzE4YTQuNSA0LjUgMCAwMC02LjM2NCAweiIgc3Ryb2tlPSIjOWNhM2FmIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PC9wYXRoPjwvc3ZnPg=='">
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">No pets yet</h3>
                        <p class="mt-1 text-gray-500 max-w-sm mx-auto">Add your pets to manage their profiles, track their health, and schedule appointments.</p>
                        <div class="mt-6">
                            <button onclick="openAddPetModal()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-teal-500 hover:bg-teal-600 transition duration-150 ease-in-out">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add New Pet
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Pet Modal -->
<div id="add-pet-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden z-50" x-data="{ petType: '' }">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full fade-in">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Add New Pet</h3>
                    <button onclick="closeAddPetModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg slide-down">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg slide-down">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('profile.pets.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Pet Information Header -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-lg font-semibold text-gray-900">Pet Information</h4>
                        <p class="text-sm text-gray-500">Please provide basic information about your pet</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                            <input type="text" id="name" name="name" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Pet Type</label>
                            <select id="type" name="type" required 
                                    x-model="petType"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                                <option value="">Select Type</option>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                                <option value="Bird">Bird</option>
                                <option value="Exotic">Exotic</option>
                            </select>
                        </div>

                        <div x-show="petType === 'Exotic'" class="slide-down">
                            <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                            <select id="species" name="species" 
                                    x-bind:required="petType === 'Exotic'"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                                <option value="">Select Species</option>
                                <optgroup label="Reptiles">
                                    <option value="snake">Snake</option>
                                    <option value="lizard">Lizard</option>
                                    <option value="turtle">Turtle</option>
                                    <option value="iguana">Iguana</option>
                                    <option value="gecko">Gecko</option>
                                    <option value="bearded_dragon">Bearded Dragon</option>
                                </optgroup>
                                <optgroup label="Small Mammals">
                                    <option value="hamster">Hamster</option>
                                    <option value="gerbil">Gerbil</option>
                                    <option value="ferret">Ferret</option>
                                    <option value="guinea_pig">Guinea Pig</option>
                                    <option value="chinchilla">Chinchilla</option>
                                    <option value="hedgehog">Hedgehog</option>
                                    <option value="sugar_glider">Sugar Glider</option>
                                </optgroup>
                                <optgroup label="Birds">
                                    <option value="parrot">Parrot</option>
                                    <option value="cockatiel">Cockatiel</option>
                                    <option value="macaw">Macaw</option>
                                    <option value="parakeet">Parakeet</option>
                                    <option value="lovebird">Lovebird</option>
                                </optgroup>
                                <optgroup label="Others">
                                    <option value="tarantula">Tarantula</option>
                                    <option value="scorpion">Scorpion</option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                            <input type="text" id="breed" name="breed" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                        </div>

                        <div>
                            <label for="size_category" class="block text-sm font-medium text-gray-700">Size Category</label>
                            <select id="size_category" name="size_category" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                                <option value="">Select Size</option>
                                <option value="Small">Small (0-15 kg)</option>
                                <option value="Medium">Medium (15-30 kg)</option>
                                <option value="Large">Large (30+ kg)</option>
                            </select>
                        </div>

                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                            <div class="relative mt-1">
                                <input type="number" id="weight" name="weight" step="0.1" required
                                       min="0.1" max="100"
                                       oninput="validateWeight(this)"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">kg</span>
                                </div>
                            </div>
                            <span class="text-red-500 text-xs hidden mt-1" id="weight-error"></span>
                        </div>

                        <div>
                            <label for="color_markings" class="block text-sm font-medium text-gray-700">Color/Markings</label>
                            <input type="text" id="color_markings" name="color_markings" required
                                   placeholder="e.g., Brown with white chest"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                        </div>

                        <div>
                            <label for="coat_type" class="block text-sm font-medium text-gray-700">Coat Type</label>
                            <select id="coat_type" name="coat_type" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                                <option value="">Select Coat Type</option>
                                <option value="Short">Short</option>
                                <option value="Medium">Medium</option>
                                <option value="Long">Long</option>
                                <option value="Curly">Curly</option>
                                <option value="Double">Double-coated</option>
                                <option value="Hairless">Hairless</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" required max="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                                   x-data
                                   x-init="$el.max = new Date().toISOString().split('T')[0]">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button" onclick="closeAddPetModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-medium hover:bg-teal-600 transition-colors shadow-sm">
                            Add Pet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal functionality
        const addPetModal = document.getElementById('add-pet-modal');
        
        if (addPetModal) {
            // Close modal when clicking outside
            addPetModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddPetModal();
                }
            });
        }
        
        // Initialize TomSelect for species select
        const speciesSelect = document.querySelector('select[name="species"]');
        if (speciesSelect) {
            new TomSelect(speciesSelect, {
                plugins: ['remove_button'],
                maxItems: 1,
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                render: {
                    item: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        return '<div class="d-flex flex-column">' +
                               '<span class="font-weight-bold">' + escape(data.text) + '</span>' +
                               '</div>';
                    }
                }
            });
        }

        // Handle pet type change
        const petTypeSelect = document.querySelector('select[name="type"]');
        if (petTypeSelect) {
            petTypeSelect.addEventListener('change', function() {
                const speciesField = document.querySelector('div[x-show="petType === \'Exotic\'"]');
                if (speciesField) {
                    if (this.value === 'Exotic') {
                        speciesField.style.display = 'block';
                        if (speciesSelect.tomselect) {
                            speciesSelect.tomselect.enable();
                        }
                    } else {
                        speciesField.style.display = 'none';
                        if (speciesSelect.tomselect) {
                            speciesSelect.tomselect.clear();
                            speciesSelect.tomselect.disable();
                        }
                    }
                }
            });
        }
        
        // Add form validation
        const petForm = document.querySelector('form');
        if (petForm) {
            if (petForm.querySelector('input[name="weight"]')) {
                petForm.addEventListener('submit', function(e) {
                    const weightInput = this.querySelector('input[name="weight"]');
                    if (!validateWeight(weightInput)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        }
        
        // Animate progress bars on page load
        const progressBars = document.querySelectorAll('.progress-bar');
        if (progressBars.length > 0) {
            setTimeout(() => {
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 300);
        }
    });

    function openAddPetModal() {
        const modal = document.getElementById('add-pet-modal');
        if (modal) {
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            
            // Focus on the first form field
            setTimeout(() => {
                const firstInput = modal.querySelector('input[name="name"]');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 100);
        }
    }

    function closeAddPetModal() {
        const modal = document.getElementById('add-pet-modal');
        if (modal) {
            document.body.style.overflow = '';
            modal.classList.add('hidden');
            
            // Reset form fields
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
            
            // Clear error messages
            const errorSpan = document.getElementById('weight-error');
            if (errorSpan) {
                errorSpan.classList.add('hidden');
            }
        }
    }

    function validateWeight(input) {
        const weightError = document.getElementById('weight-error');
        const submitButton = input.closest('form').querySelector('button[type="submit"]');
        
        if (input.value <= 0) {
            weightError.textContent = 'Weight must be greater than 0 kg';
            weightError.classList.remove('hidden');
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        }
        
        if (input.value > 100) {
            weightError.textContent = 'Please enter a valid weight (less than 100 kg)';
            weightError.classList.remove('hidden');
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        }
        
        weightError.classList.add('hidden');
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        return true;
    }
    
    // Handle escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddPetModal();
        }
    });
</script>
@endpush
@endsection 