@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center px-4 py-2 text-gray-700 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-all">
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
                        MON-SAT 8:30 AM - 5:00 PM
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
                        MON-SAT 8:30 AM - 5:00 PM
                    </span>
                </div>
            </div>

            <!-- Booking Button -->
            @auth
                <button onclick="window.location.href='{{ route('booking.process', $shop) }}'" 
                        class="w-full bg-blue-500 text-white py-4 rounded-xl text-lg font-medium hover:bg-blue-600 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                    Book Now
                </button>
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
                    <button class="flex-1 py-3 px-6 rounded-xl bg-white border-2 border-blue-500 text-blue-500 font-medium hover:bg-blue-50 transition-colors">
                        Dog Services
                    </button>
                    <button class="flex-1 py-3 px-6 rounded-xl bg-blue-500 text-white font-medium hover:bg-blue-600 transition-colors">
                        Cat Services
                    </button>
                </div>

                <!-- Services Grid -->
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach(['Full Grooming Service' => 1499, 'Basic Bath Package' => 749, 'Nail Trimming' => 199, 'Ear Cleaning' => 499] as $service => $price)
                    <div class="p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group cursor-pointer">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-lg group-hover:text-blue-600 transition-colors">{{ $service }}</h3>
                            <span class="text-lg font-bold text-blue-600">₱{{ number_format($price) }}</span>
                        </div>
                        <p class="text-gray-600">Professional pet care service</p>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Veterinary Services Grid -->
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach([
                        'General Check-up' => ['price' => 800, 'desc' => 'Complete Physical Examination'],
                        'Vaccination' => ['price' => 1500, 'desc' => 'Core Vaccines Available'],
                        'Deworming' => ['price' => 500, 'desc' => 'Internal Parasite Treatment'],
                        'Laboratory Tests' => ['price' => 2000, 'desc' => 'Blood Work, Urinalysis, etc.']
                    ] as $service => $details)
                    <div class="p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group cursor-pointer">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-lg group-hover:text-blue-600 transition-colors">{{ $service }}</h3>
                            <span class="text-lg font-bold text-blue-600">₱{{ number_format($details['price']) }}</span>
                        </div>
                        <p class="text-gray-600">{{ $details['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Reviews Tab -->
        <div x-show="activeTab === 'reviews'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="p-6">
            <!-- Keep existing reviews section but with enhanced styling -->
            @auth
                <div class="mb-8 border-b pb-8">
                    <h3 class="text-xl font-semibold mb-6">Write a Review</h3>
                    <form action="{{ route('shop.review', $shop) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-center space-x-1" x-data="{ rating: 0 }">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        @click="rating = {{ $i }}" 
                                        class="text-3xl focus:outline-none transition-colors"
                                        :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                    ★
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating">
                        </div>

                        <textarea name="comment" 
                                  rows="4" 
                                  class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                  placeholder="Share your experience..."></textarea>

                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-3 bg-blue-500 text-white rounded-xl font-medium hover:bg-blue-600 transition-colors">
                            Submit Review
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-blue-50 rounded-xl p-6 mb-8 text-center">
                    <p class="text-blue-800">Please <a href="{{ route('login') }}" class="font-medium underline">login</a> to write a review.</p>
                </div>
            @endauth

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($shop->ratings as $rating)
                    <div class="p-6 bg-gray-50 rounded-xl">
                        <div class="flex items-center mb-4">
                            <img src="{{ $rating->user->profile_photo_url }}" 
                                 alt="{{ $rating->user->name }}" 
                                 class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-medium text-lg">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</h4>
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400 mr-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span>{{ $i <= $rating->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                    <span class="text-gray-500 text-sm">{{ $rating->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700">{{ $rating->comment }}</p>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        <p class="text-lg">No reviews yet</p>
                        <p class="text-sm">Be the first to review this shop!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

