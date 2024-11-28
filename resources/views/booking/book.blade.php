@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Shop Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="relative h-64">
            <img src="{{ $shop->image_url }}" 
                 alt="{{ $shop->name }}" 
                 class="w-full h-full object-cover">
            <div class="absolute top-4 left-4">
                <span class="bg-white text-green-500 px-3 py-1 rounded-full text-sm shadow-sm">
                    {{ $shop->is_open ? 'Open' : 'Closed' }}
                </span>
            </div>
            <!-- Shop type badge -->
            <div class="absolute top-4 right-4">
                <span class="bg-white text-gray-700 px-3 py-1 rounded-full text-sm shadow-sm">
                    {{ ucfirst($shop->type) }}
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold mb-2">{{ $shop->name }}</h1>
                    <div class="flex items-center mb-2">
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($shop->ratings_avg_rating ?? 0))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span class="ml-2 text-gray-600">({{ $shop->ratings_count ?? 0 }} reviews)</span>
                    </div>
                    <p class="text-gray-600">
                        <span class="font-medium">Hours:</span> MON-SAT 8:30 AM - 5:00 PM<br>
                        <span class="font-medium">Address:</span> {{ $shop->address }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <button class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </button>
                    <button class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Services and Reviews Tabs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="border-b">
            <div class="flex" x-data="{ activeTab: 'services' }">
                <button @click="activeTab = 'services'" 
                        :class="{'border-b-2 border-blue-500 text-blue-500': activeTab === 'services'}"
                        class="px-6 py-3 font-medium">Services</button>
                <button @click="activeTab = 'reviews'" 
                        :class="{'border-b-2 border-blue-500 text-blue-500': activeTab === 'reviews'}"
                        class="px-6 py-3 font-medium">Reviews</button>
            </div>
        </div>

        <!-- Services Section -->
        <div x-show="activeTab === 'services'" class="p-6">
            @if($shop->type === 'grooming')
                <!-- Service Type Toggle for Grooming -->
                <div class="flex space-x-4 mb-6">
                    <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200">Dog</button>
                    <button class="px-4 py-2 rounded-full bg-blue-500 text-white">Cat</button>
                </div>

                <!-- Grooming Services List -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Full Grooming Service</h3>
                            <p class="text-sm text-gray-500">Bath, Haircut, Nail Trimming, Ear Cleaning</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 1,499</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Basic Bath Package</h3>
                            <p class="text-sm text-gray-500">Bath and Blow Dry</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 749</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Nail Trimming</h3>
                            <p class="text-sm text-gray-500">Service</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 199</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Ear Cleaning</h3>
                            <p class="text-sm text-gray-500">Service</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 499</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Veterinary Services List -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">General Check-up</h3>
                            <p class="text-sm text-gray-500">Complete Physical Examination</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 800</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Vaccination</h3>
                            <p class="text-sm text-gray-500">Core Vaccines Available</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 1,500</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Deworming</h3>
                            <p class="text-sm text-gray-500">Internal Parasite Treatment</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 500</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">Laboratory Tests</h3>
                            <p class="text-sm text-gray-500">Blood Work, Urinalysis, etc.</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">PHP 2,000</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Reviews Section -->
        <div x-show="activeTab === 'reviews'" class="p-6">
            @php
                \Log::info('Shop ratings count: ' . $shop->ratings->count());
                \Log::info('Shop average rating: ' . $shop->ratings_avg_rating);
            @endphp
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif
            <!-- Add Review Form for Authenticated Users -->
            @auth
                <div class="mb-8 border-b pb-8">
                    <h3 class="text-lg font-semibold mb-4">Write a Review</h3>
                    <form action="{{ route('shop.review', $shop) }}" method="POST" class="space-y-4">
                        @csrf
                        <!-- Star Rating -->
                        <div class="flex items-center space-x-1" x-data="{ rating: 0 }">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        @click="rating = {{ $i }}" 
                                        class="text-2xl focus:outline-none"
                                        :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                    ★
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating">
                        </div>

                        <!-- Review Comment -->
                        <div>
                            <textarea name="comment" 
                                      rows="4" 
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                      placeholder="Share your experience..."></textarea>
                        </div>

                        <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Submit Review
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4 mb-8 text-center">
                    <p class="text-gray-600">Please <a href="{{ route('login') }}" class="text-blue-500 hover:underline">login</a> to write a review.</p>
                </div>
            @endauth

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($shop->ratings as $rating)
                    <div class="border-b pb-6 last:border-b-0">
                        <div class="flex items-center mb-2">
                            <img src="{{ $rating->user->profile_photo_url }}" 
                                 alt="{{ $rating->user->name }}" 
                                 class="w-10 h-10 rounded-full mr-3 object-cover bg-gray-100">
                            <div>
                                <h4 class="font-medium">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</h4>
                                <div class="flex items-center text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $rating->rating ? '★' : '☆' }}</span>
                                    @endfor
                                    <span class="ml-2 text-gray-500 text-sm">{{ $rating->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 mt-2">{{ $rating->comment }}</p>
                    </div>
                @empty
                    <div class="text-center text-gray-500">
                        <p>No reviews yet</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination if needed -->
            @if($shop->ratings->count() > 10)
                <div class="mt-6">
                    {{ $shop->ratings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Rating & Reviews Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Rating & Reviews</h2>
            <div class="flex items-center mb-6">
                <div class="text-4xl font-bold mr-4">{{ number_format($shop->rating, 1) }}</div>
                <div class="flex-grow">
                    <div class="flex items-center text-yellow-400 mb-1">★★★★★</div>
                    <p class="text-sm text-gray-500">{{ $shop->ratings_count ?? 0 }} ratings</p>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($shop->ratings as $rating)
                <div class="border-t pt-4">
                    <div class="flex items-center mb-2">
                        <img src="{{ $rating->user->profile_photo_url }}" 
                             alt="{{ $rating->user->name }}" 
                             class="w-10 h-10 rounded-full mr-3 object-cover">
                        <div>
                            <h3 class="font-medium">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $rating->created_at->format('D, M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $rating->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-gray-700">{{ $rating->comment }}</p>
                </div>
                @empty
                <p class="text-gray-500">No reviews yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Book Now Button (Fixed at bottom) -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4">
        <div class="container mx-auto">
            <div class="flex items-center justify-between mb-3">
                <span class="text-green-500 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <circle cx="10" cy="10" r="8"/>
                    </svg>
                    Currently Open
                </span>
                <button class="text-gray-600 hover:text-red-500 text-sm flex items-center" onclick="reportShop()">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Report Shop
                </button>
            </div>
            @auth
                <button onclick="window.location.href='{{ route('booking.process', $shop) }}'" 
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                    Book Now
                </button>
            @else
                <div class="space-y-3">
                    <button onclick="window.location.href='{{ route('login') }}'" 
                            class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                        Login to Book
                    </button>
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Register here</a>
                    </p>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection

