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
                                <div class="flex space-x-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" class="hidden peer" required>
                                        <label for="rating{{ $i }}" 
                                               class="cursor-pointer text-2xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400">
                                            ★
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                                <textarea id="comment" 
                                          name="comment" 
                                          rows="4" 
                                          required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-colors">
                                Submit Review
                            </button>
                        </form>
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
                    @forelse($shop->ratings()->with('user')->latest()->get() as $rating)
                        <div class="border-b pb-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <img src="{{ $rating->user->profile_photo_path ? asset('storage/' . $rating->user->profile_photo_path) : asset('images/default-profile.png') }}" 
                                         alt="Profile" 
                                         class="w-10 h-10 rounded-full object-cover mr-3">
                                    <div>
                                        <div class="font-medium">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</div>
                                        <div class="flex items-center">
                                            <div class="text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span>{{ $i <= $rating->rating ? '★' : '☆' }}</span>
                                                @endfor
                                            </div>
                                            <span class="text-gray-500 text-sm ml-2">{{ $rating->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700">{{ $rating->comment }}</p>
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
@endsection

