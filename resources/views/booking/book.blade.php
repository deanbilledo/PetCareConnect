@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center px-4 py-2 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Shop Header -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="relative h-96">
            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                 alt="{{ $shop->name }}" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"></div>
            
            <!-- Status Badges -->
            <div class="absolute top-6 left-6 flex flex-wrap gap-3">
                <span class="px-4 py-2 rounded-full text-sm font-medium {{ $shop->is_open ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }} shadow-lg backdrop-blur-sm">
                    {{ $shop->is_open ? 'Open Now' : 'Closed' }}
                </span>
                <span class="px-4 py-2 rounded-full bg-white/90 text-gray-800 text-sm font-medium shadow-lg backdrop-blur-sm">
                    {{ ucfirst($shop->type) }}
                </span>
            </div>
            
            @auth
            <!-- Favorite Button -->
            <div class="absolute top-6 right-6">
                <button 
                    type="button"
                    class="favorite-btn px-4 py-2 rounded-full bg-white/90 text-gray-800 hover:bg-white shadow-lg backdrop-blur-sm flex items-center gap-2 transition-all duration-200 transform hover:scale-105"
                    data-shop-id="{{ $shop->id }}"
                    data-is-favorited="{{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'true' : 'false' }}"
                >
                    <svg class="h-5 w-5 {{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'text-red-500' : 'text-gray-400' }} transition-transform duration-300" 
                         fill="currentColor" 
                         viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span>{{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'Favorited' : 'Add to favorites' }}</span>
                </button>
            </div>
            @endauth

            <!-- Shop Info Overlay -->
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                <h1 class="text-4xl font-bold mb-3">{{ $shop->name }}</h1>
                <div class="flex items-center gap-6 mb-4">
                    <div class="flex items-center">
                        <div class="flex text-yellow-400 text-xl mr-2">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($shop->ratings_avg_rating ?? 0))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span class="text-white/90">({{ $shop->ratings_count ?? 0 }} reviews)</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 text-white/90">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @php
                            $days = [
                                0 => 'SUN',
                                1 => 'MON',
                                2 => 'TUE',
                                3 => 'WED',
                                4 => 'THU',
                                5 => 'FRI',
                                6 => 'SAT'
                            ];
                            
                            $operatingHours = $shop->operatingHours()
                                ->orderBy('day')
                                ->get()
                                ->groupBy(function($hour) {
                                    return $hour->is_open ? 
                                        ($hour->open_time . ' - ' . $hour->close_time . 
                                        ($hour->has_lunch_break ? '|' . $hour->lunch_start . '-' . $hour->lunch_end : '')) : 
                                        'closed';
                                });

                            $output = [];
                            
                            foreach ($operatingHours as $schedule => $hours) {
                                if ($schedule === 'closed') continue;
                                
                                $dayNumbers = $hours->pluck('day')->toArray();
                                $dayRanges = [];
                                $start = $prev = $dayNumbers[0];
                                
                                for ($i = 1; $i < count($dayNumbers); $i++) {
                                    if ($dayNumbers[$i] !== $prev + 1) {
                                        $dayRanges[] = $start === $prev ? 
                                            $days[$start] : 
                                            $days[$start] . '-' . $days[$prev];
                                        $start = $dayNumbers[$i];
                                    }
                                    $prev = $dayNumbers[$i];
                                }
                                
                                $dayRanges[] = $start === $prev ? 
                                    $days[$start] : 
                                    $days[$start] . '-' . $days[$prev];
                                
                                foreach ($dayRanges as $range) {
                                    $times = explode('|', $schedule);
                                    $operatingTime = explode(' - ', $times[0]);
                                    $output[] = $range . ' ' . 
                                        date('g:i A', strtotime($operatingTime[0])) . ' - ' . 
                                        date('g:i A', strtotime($operatingTime[1])) .
                                        (isset($times[1]) ? ' (Lunch: ' . 
                                            date('g:i A', strtotime(explode('-', $times[1])[0])) . ' - ' . 
                                            date('g:i A', strtotime(explode('-', $times[1])[1])) . ')' : '');
                                }
                            }
                            
                            if (!empty($output)) {
                                echo implode('<br>', $output);
                            } else {
                                echo 'Hours not available';
                            }
                        @endphp
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $shop->address }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Description -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">About {{ $shop->name }}</h2>
            <div class="prose max-w-none">
                <p class="text-gray-600">{{ $shop->description ?: 'No description available.' }}</p>
            </div>
            @if($shop->contact_email)
            <div class="mt-4 flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>Email: {{ $shop->contact_email }}</span>
            </div>
            @endif
            @if($shop->contact_number)
            <div class="mt-2 flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>Phone: {{ $shop->contact_number }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Photo Gallery Section -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Photo Gallery</h2>
            
            <!-- Gallery Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($shop->gallery as $image)
                    <div class="relative group cursor-pointer overflow-hidden rounded-lg shadow-sm hover:shadow-md transition-all duration-300" 
                         onclick="openGalleryModal({{ $loop->index }})">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="Gallery Image {{ $loop->iteration }}" 
                             class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <span class="text-white text-sm font-medium">View Image</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-500 mt-4">No gallery images available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div id="galleryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/90 backdrop-blur-sm transition-opacity duration-300">
        <!-- Modal Content -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Close Button -->
            <button onclick="closeGalleryModal()" 
                    class="absolute top-4 right-4 text-white/80 hover:text-white z-10 bg-black/30 backdrop-blur-sm p-2 rounded-full transition-all hover:bg-black/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Image Container -->
            <div class="relative max-w-5xl w-full">
                <!-- Image Counter -->
                <div class="absolute top-4 left-4 bg-black/30 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm z-10">
                    <span id="imageCounter"></span>
                </div>

                <!-- Previous Button -->
                <button onclick="changeImage(-1)" 
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white/80 hover:text-white bg-black/30 backdrop-blur-sm p-3 rounded-full transition-all hover:bg-black/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Image -->
                <div class="relative overflow-hidden rounded-lg shadow-2xl">
                    <img id="modalImage" 
                        src="" 
                        alt="Gallery Image" 
                        class="w-full h-auto max-h-[80vh] object-contain transition-opacity duration-300 opacity-0">
                </div>

                <!-- Next Button -->
                <button onclick="changeImage(1)" 
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white/80 hover:text-white bg-black/30 backdrop-blur-sm p-3 rounded-full transition-all hover:bg-black/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Booking Section -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <!-- Shop Status and Actions -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-6">
                    @if($shop->is_open)
                        <span class="text-green-500 font-medium flex items-center">
                            <span class="flex h-3 w-3 relative mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Currently Open
                        </span>
                    @else
                        <span class="text-red-500 font-medium flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            Currently Closed
                        </span>
                    @endif
                    
                    <button class="text-gray-600 hover:text-red-500 flex items-center" 
                            onclick="reportShop()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Report Shop
                    </button>
                </div>

                <!-- Quick Info -->
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @php
                            $days = [
                                0 => 'SUN',
                                1 => 'MON',
                                2 => 'TUE',
                                3 => 'WED',
                                4 => 'THU',
                                5 => 'FRI',
                                6 => 'SAT'
                            ];
                            
                            $operatingHours = $shop->operatingHours()
                                ->orderBy('day')
                                ->get()
                                ->groupBy(function($hour) {
                                    return $hour->is_open ? 
                                        ($hour->open_time . ' - ' . $hour->close_time . 
                                        ($hour->has_lunch_break ? '|' . $hour->lunch_start . '-' . $hour->lunch_end : '')) : 
                                        'closed';
                                });

                            $output = [];
                            
                            foreach ($operatingHours as $schedule => $hours) {
                                if ($schedule === 'closed') continue;
                                
                                $dayNumbers = $hours->pluck('day')->toArray();
                                $dayRanges = [];
                                $start = $prev = $dayNumbers[0];
                                
                                for ($i = 1; $i < count($dayNumbers); $i++) {
                                    if ($dayNumbers[$i] !== $prev + 1) {
                                        $dayRanges[] = $start === $prev ? 
                                            $days[$start] : 
                                            $days[$start] . '-' . $days[$prev];
                                        $start = $dayNumbers[$i];
                                    }
                                    $prev = $dayNumbers[$i];
                                }
                                
                                $dayRanges[] = $start === $prev ? 
                                    $days[$start] : 
                                    $days[$start] . '-' . $days[$prev];
                                
                                foreach ($dayRanges as $range) {
                                    $times = explode('|', $schedule);
                                    $operatingTime = explode(' - ', $times[0]);
                                    $output[] = $range . ' ' . 
                                        date('g:i A', strtotime($operatingTime[0])) . ' - ' . 
                                        date('g:i A', strtotime($operatingTime[1])) .
                                        (isset($times[1]) ? ' (Lunch: ' . 
                                            date('g:i A', strtotime(explode('-', $times[1])[0])) . ' - ' . 
                                            date('g:i A', strtotime(explode('-', $times[1])[1])) . ')' : '');
                                }
                            }
                            
                            if (!empty($output)) {
                                echo implode('<br>', $output);
                            } else {
                                echo 'Hours not available';
                            }
                        @endphp
                    </span>
                </div>
            </div>

            <!-- Booking Button -->
            @auth
                @php
                    $hasPets = auth()->user()->pets()->exists();
                @endphp

                @if($hasPets)
                    <button onclick="window.location.href='{{ route('booking.process', $shop) }}'" 
                            class="w-full bg-blue-500 text-white py-4 rounded-xl text-lg font-medium hover:bg-blue-600 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                        Book Now
                    </button>
                @else
                    <div class="space-y-4">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        You need to add at least one pet before booking an appointment.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <button onclick="window.location.href='{{ route('profile.pets.index') }}'" 
                                class="w-full bg-blue-500 text-white py-4 rounded-xl text-lg font-medium hover:bg-blue-600 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                            Add a Pet
                        </button>
                    </div>
                @endif
            @else
                <div class="space-y-4">
                    <button onclick="window.location.href='{{ route('login') }}'" 
                            class="w-full bg-blue-500 text-white py-4 rounded-xl text-lg font-medium hover:bg-blue-600 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                        Login to Book
                    </button>
                    <p class="text-center text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-600 font-medium">Register here</a>
                    </p>
                </div>
            @endauth
        </div>
    </div>

    <!-- Services and Reviews Tabs -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data="{ activeTab: 'services' }">
        <!-- Tab Navigation -->
        <div class="flex border-b">
            <button @click="activeTab = 'services'" 
                    :class="{'border-b-2 border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'services'}"
                    class="flex-1 py-4 px-6 text-lg font-medium transition duration-200">
                Services
            </button>
            <button @click="activeTab = 'reviews'" 
                    :class="{'border-b-2 border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'reviews'}"
                    class="flex-1 py-4 px-6 text-lg font-medium transition duration-200">
                Reviews
            </button>
        </div>

        <!-- Services Tab -->
        <div x-show="activeTab === 'services'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="p-6">
            @if($shop->type === 'grooming')
                <!-- Pet Type Toggle -->
                <div class="flex gap-4 mb-8">
                    <button onclick="filterServices('dog')" 
                            id="dogButton"
                            class="flex-1 py-3 px-6 rounded-xl bg-blue-500 text-white font-medium hover:bg-blue-600 transition-colors">
                        Dog Services
                    </button>
                    <button onclick="filterServices('cat')"
                            id="catButton"
                            class="flex-1 py-3 px-6 rounded-xl bg-white border-2 border-blue-500 text-blue-500 font-medium hover:bg-blue-50 transition-colors">
                        Cat Services
                    </button>
                </div>
            @endif

                <!-- Services Grid -->
                <div class="grid gap-4 sm:grid-cols-2">
                @forelse($shop->services()->where('status', 'active')->get() as $service)
                    <div class="p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group cursor-pointer service-card"
                         data-pet-types="{{ json_encode($service->pet_types) }}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-lg group-hover:text-blue-600 transition-colors">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $service->duration }} minutes</p>
                            </div>
                            <span class="text-lg font-bold text-blue-600">₱{{ number_format($service->base_price, 2) }}</span>
                        </div>
                        <p class="text-gray-600">{{ $service->description ?: 'Professional pet care service' }}</p>
                        
                        @if($service->variable_pricing)
                            <div class="mt-3 text-sm">
                                <p class="font-medium text-gray-700">Price varies by size:</p>
                                <ul class="list-disc list-inside text-gray-600 mt-1">
                                    @foreach($service->variable_pricing as $pricing)
                                        <li>{{ ucfirst($pricing['size']) }}: ₱{{ number_format($pricing['price'], 2) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($service->add_ons && count($service->add_ons) > 0)
                            <div class="mt-3 text-sm">
                                <p class="font-medium text-gray-700">Available Add-ons:</p>
                                <ul class="list-disc list-inside text-gray-600 mt-1">
                                    @foreach($service->add_ons as $addon)
                                        <li>{{ $addon['name'] }}: ₱{{ number_format($addon['price'], 2) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-gray-500">
                        <p class="text-lg">No services available at the moment</p>
                    </div>
                @endforelse
                </div>

            <script>
                function filterServices(petType) {
                    // Update button styles
                    const dogButton = document.getElementById('dogButton');
                    const catButton = document.getElementById('catButton');
                    
                    if (petType === 'dog') {
                        dogButton.classList.remove('bg-white', 'border-2', 'border-blue-500', 'text-blue-500');
                        dogButton.classList.add('bg-blue-500', 'text-white');
                        catButton.classList.remove('bg-blue-500', 'text-white');
                        catButton.classList.add('bg-white', 'border-2', 'border-blue-500', 'text-blue-500');
                    } else {
                        catButton.classList.remove('bg-white', 'border-2', 'border-blue-500', 'text-blue-500');
                        catButton.classList.add('bg-blue-500', 'text-white');
                        dogButton.classList.remove('bg-blue-500', 'text-white');
                        dogButton.classList.add('bg-white', 'border-2', 'border-blue-500', 'text-blue-500');
                    }

                    // Filter services
                    const services = document.querySelectorAll('.service-card');
                    services.forEach(service => {
                        const petTypes = JSON.parse(service.dataset.petTypes);
                        const showService = petTypes.some(type => 
                            type.toLowerCase().includes(petType.toLowerCase())
                        );
                        service.style.display = showService ? 'block' : 'none';
                    });
                }

                // Initialize with dog services selected for grooming shops
                @if($shop->type === 'grooming')
                    document.addEventListener('DOMContentLoaded', () => {
                        filterServices('dog');
                    });
            @endif
            </script>
        </div>

        <!-- Reviews Tab -->
        <div x-show="activeTab === 'reviews'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="p-6">
            <!-- Reviews Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Reviews</h2>
                
                @auth
                    @php
                        $hasCompletedAppointment = auth()->user()->appointments()
                            ->where('shop_id', $shop->id)
                            ->where('status', 'completed')
                            ->exists();
                    @endphp

                    @if($hasCompletedAppointment)
                        <!-- Review Form -->
                        <form action="{{ route('shops.review', $shop) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex flex-row-reverse justify-end space-x-2 space-x-reverse">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" class="hidden peer" required>
                                        <label for="rating{{ $i }}" 
                                               class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400 peer-hover:text-yellow-400 hover:peer-hover:text-yellow-400">
                                            ★
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                                <textarea id="review" 
                                          name="review" 
                                          rows="4" 
                                          required
                                          minlength="10"
                                          placeholder="Tell us about your experience (minimum 10 characters)"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('review') border-red-500 @enderror">{{ old('review') }}</textarea>
                                @error('review')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-colors">
                                Submit Review
                            </button>
                        </form>

                        @if(session('success'))
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                                {{ session('error') }}
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-center">
                            <p class="text-gray-600">You can only leave a review after completing an appointment with this shop.</p>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-center">
                        <p class="text-gray-600">Please <a href="{{ route('login') }}" class="text-blue-500 hover:underline">login</a> to leave a review.</p>
                    </div>
                @endauth

                <!-- Existing Reviews -->
                <div class="space-y-4">
                    @forelse($shop->ratings()->with(['user', 'appointment.employee', 'appointment.services'])->latest()->get() as $rating)
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <!-- User Info and Shop Rating -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <img src="{{ $rating->user->profile_photo_path ? asset('storage/' . $rating->user->profile_photo_path) : asset('images/default-profile.png') }}" 
                                         alt="Profile" 
                                         class="w-10 h-10 rounded-full object-cover mr-3">
                                    <div>
                                        <div class="font-medium">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</div>
                                        <div class="flex items-center">
                                            <div class="text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $rating->rating)
                                                        <span class="text-2xl">★</span>
                                                    @else
                                                        <span class="text-2xl text-gray-300">★</span>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-gray-500 text-sm ml-2">{{ $rating->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Services -->
                            @if($rating->appointment && $rating->appointment->services->isNotEmpty())
                                <div class="mb-3 bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-600 mb-2">Services:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($rating->appointment->services as $service)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $service->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Shop Review -->
                            <div class="mb-4">
                                <p class="text-gray-700">{{ $rating->review }}</p>
                            </div>

                            <!-- Shop Response -->
                            @if($rating->shop_comment)
                                <div class="mt-4 bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-start space-x-2">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}"
                                                 alt="{{ $shop->name }}"
                                                 class="w-8 h-8 rounded-full object-cover">
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-blue-800">Shop's Response:</p>
                                            <p class="text-sm text-gray-700 mt-1">{{ $rating->shop_comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Employee Rating -->
                            @if($rating->appointment && $rating->appointment->employee && $rating->appointment->employee->staffRatings()->where('appointment_id', $rating->appointment_id)->exists())
                                @php
                                    $staffRating = $rating->appointment->employee->staffRatings()
                                        ->where('appointment_id', $rating->appointment_id)
                                        ->first();
                                @endphp
                                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <img src="{{ $rating->appointment->employee->profile_photo_url }}" 
                                             alt="{{ $rating->appointment->employee->name }}" 
                                             class="w-8 h-8 rounded-full object-cover mr-2">
                                        <div>
                                            <p class="font-medium text-sm">{{ $rating->appointment->employee->name }}</p>
                                            <div class="flex items-center">
                                                <div class="text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $staffRating->rating)
                                                            <span class="text-lg">★</span>
                                                        @else
                                                            <span class="text-lg text-gray-300">★</span>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($staffRating->review)
                                        <p class="text-sm text-gray-600 mt-2">{{ $staffRating->review }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <p class="text-lg">No reviews yet</p>
                            @if($hasCompletedAppointment ?? false)
                                <p class="text-sm">Be the first to review this shop!</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Report Shop Modal -->
    <div id="reportModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal Content -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6 relative">
                <!-- Close Button -->
                <button onclick="closeReportModal()" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Modal Header -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Report Shop</h3>
                    <p class="text-sm text-gray-600 mt-1">Please provide details about your concern</p>
                </div>

                <!-- Report Form Component -->
                <x-report-form type="shop" :id="$shop->id" />
            </div>
        </div>
    </div>

    <script>
        function reportShop() {
            try {
                const reportModal = document.getElementById('reportModal');
                if (reportModal) {
                    reportModal.classList.remove('hidden');
                } else {
                    console.error('Report modal element not found');
                }
            } catch (error) {
                console.error('Error showing report modal:', error);
            }
        }

        function closeReportModal() {
            try {
                const modal = document.getElementById('reportModal');
                if (modal) {
                    modal.classList.add('hidden');
                    
                    // Reset the form to clear any previous data or error messages
                    const form = document.getElementById('shop-report-form');
                    if (form) {
                        form.reset();
                        
                        // Hide error messages
                        const errorElements = form.querySelectorAll('.text-red-500:not(.hidden)');
                        errorElements.forEach(el => el.classList.add('hidden'));
                        
                        // Hide image preview if it exists
                        const imagePreview = document.getElementById('image-preview-container');
                        if (imagePreview && !imagePreview.classList.contains('hidden')) {
                            imagePreview.classList.add('hidden');
                        }
                        
                        // Hide form feedback if visible
                        const formFeedback = document.getElementById('form-feedback');
                        if (formFeedback && !formFeedback.classList.contains('hidden')) {
                            formFeedback.classList.add('hidden');
                        }
                    }
                } else {
                    console.error('Report modal element not found when trying to close');
                }
            } catch (error) {
                console.error('Error closing report modal:', error);
            }
        }

        // Store gallery images data
        const galleryImages = @json($shop->gallery->map(function($image) {
            return [
                'url' => asset('storage/' . $image->image_path),
                'alt' => 'Gallery Image'
            ];
        }));
        
        let currentImageIndex = 0;

        function openGalleryModal(index) {
            currentImageIndex = index;
            const modal = document.getElementById('galleryModal');
            
            modal.classList.remove('hidden');
            // Small delay to allow for fade-in effect
            setTimeout(() => {
                updateModalImage();
            }, 50);
            
            // Add keyboard navigation
            document.addEventListener('keydown', handleKeyNavigation);
        }

        function closeGalleryModal() {
            const modal = document.getElementById('galleryModal');
            const modalImage = document.getElementById('modalImage');
            
            // Fade out effect
            modalImage.classList.add('opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                // Remove keyboard event listener when modal is closed
                document.removeEventListener('keydown', handleKeyNavigation);
            }, 300);
        }

        function changeImage(direction) {
            // Fade out current image
            const modalImage = document.getElementById('modalImage');
            modalImage.classList.add('opacity-0');
            
            setTimeout(() => {
                currentImageIndex = (currentImageIndex + direction + galleryImages.length) % galleryImages.length;
                updateModalImage();
            }, 200);
        }

        function handleKeyNavigation(e) {
            if (e.key === 'ArrowLeft') {
                changeImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeImage(1);
            } else if (e.key === 'Escape') {
                closeGalleryModal();
            }
        }

        function updateModalImage() {
            const modalImage = document.getElementById('modalImage');
            const imageCounter = document.getElementById('imageCounter');
            
            // Update image source
            modalImage.src = galleryImages[currentImageIndex].url;
            modalImage.alt = galleryImages[currentImageIndex].alt;
            
            // Update counter
            imageCounter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
            
            // Add loading event to ensure image is loaded before showing
            modalImage.onload = function() {
                modalImage.classList.remove('opacity-0');
            };
        }

        // Close modal when clicking outside the image
        document.getElementById('galleryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGalleryModal();
            }
        });
    </script>

    @push('scripts')
    <script src="{{ asset('js/favorite-handler.js') }}"></script>
    <script>
        // Update favorite button text when its state changes
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteBtn = document.querySelector('.favorite-btn');
            if (favoriteBtn) {
                // Create a MutationObserver to watch for attribute changes
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'data-is-favorited') {
                            const isFavorited = favoriteBtn.getAttribute('data-is-favorited') === 'true';
                            const textSpan = favoriteBtn.querySelector('span');
                            if (textSpan) {
                                textSpan.textContent = isFavorited ? 'Favorited' : 'Add to favorites';
                            }
                        }
                    });
                });
                
                // Start observing the button for attribute changes
                observer.observe(favoriteBtn, { attributes: true });
            }
        });
    </script>
    @endpush

@endsection

