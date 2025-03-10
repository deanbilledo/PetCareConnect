@extends('layouts.app')

@section('content')

<section class="grooming-shops my-6 mb-20 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Veterinary Clinics
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @forelse($veterinaryShops as $shop)
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl cursor-pointer" onclick="window.location.href='{{ route('booking.show', $shop) }}'">
            <div class="relative h-64">
                <img src="{{ $shop->image_url ? asset($shop->image_url) : asset('images/shops/default-shop.svg') }}" alt="{{ $shop->name }}" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Veterinary Clinic
                </span>
                @auth
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300 favorite-toggle" data-shop-id="{{ $shop->id }}" onclick="event.stopPropagation();">
                    <svg class="h-6 w-6 {{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'text-red-500' : 'text-white' }}" fill="{{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                @endauth
                @guest
                <a href="{{ route('login') }}" class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300" onclick="event.stopPropagation();">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>
                @endguest
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">{{ $shop->name }}</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($shop->ratings_avg_rating))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </span>
                        <span class="ml-1 text-gray-200">{{ number_format($shop->ratings_avg_rating, 1) }}</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">{{ $shop->address }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-8">
            <p class="text-gray-500">No veterinary clinics found.</p>
        </div>
        @endforelse
    </div>
</section>


{{-- Veterinary Services --}}

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Veterinary Services
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        @if(count($veterinaryServices) > 0)
            <!-- First service - larger display -->
            @php $firstService = $veterinaryServices->first(); @endphp
            <a href="{{ route('booking.show', $firstService->shop) }}" class="relative block overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl lg:row-span-2">
                <img src="{{ $firstService->shop->image_url ? asset($firstService->shop->image_url) : asset('images/shops/default-shop.svg') }}" alt="{{ $firstService->name }}" class="w-full h-80 object-cover">
                <div class="absolute top-2 right-2 bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                    #1 Most Booked
                </div>
            <span class="absolute bottom-2 left-2 bg-white bg-opacity-50 text-black text-lg font-semibold px-2 py-1 rounded-full">
                    {{ $firstService->name }}
                </span>
                <span class="absolute bottom-2 right-2 bg-green-500 bg-opacity-75 text-white text-sm font-semibold px-2 py-1 rounded-full">
                    {{ $firstService->shop->name }}
            </span>
        </a>

            <!-- Other services -->
            @foreach($veterinaryServices->skip(1)->take(2) as $index => $service)
                <a href="{{ route('booking.show', $service->shop) }}" class="relative block overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
                    <img src="{{ $service->shop->image_url ? asset($service->shop->image_url) : asset('images/shops/default-shop.svg') }}" alt="{{ $service->name }}" class="w-full h-40 object-cover">
                    <div class="absolute top-2 right-2 bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                        #{{ $index + 2 }} Most Booked
                    </div>
            <span class="absolute bottom-2 left-2 bg-white bg-opacity-50 text-black text-lg font-semibold px-2 py-1 rounded-full">
                        {{ $service->name }}
            </span>
                    <span class="absolute bottom-2 right-2 bg-green-500 bg-opacity-75 text-white text-sm font-semibold px-2 py-1 rounded-full">
                        {{ $service->shop->name }}
            </span>
        </a>
            @endforeach
        @else
            <!-- Fallback for when no services are found -->
            <div class="col-span-2 text-center py-8">
                <p class="text-gray-500">No popular veterinary services found.</p>
            </div>
        @endif
    </div>
</section>

{{-- All Veterinary Clinics --}}

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        All Veterinary Clinics
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @forelse($veterinaryShops as $shop)
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4 cursor-pointer" onclick="window.location.href='{{ route('booking.show', $shop) }}'">
            <div class="relative h-64">
                <img src="{{ $shop->image_url ? asset($shop->image_url) : asset('images/shops/default-shop.svg') }}" alt="{{ $shop->name }}" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Veterinary Clinic
                </span>
                @auth
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300 favorite-toggle" data-shop-id="{{ $shop->id }}" onclick="event.stopPropagation();">
                    <svg class="h-6 w-6 {{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'text-red-500' : 'text-white' }}" fill="{{ auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                @endauth
                @guest
                <a href="{{ route('login') }}" class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300" onclick="event.stopPropagation();">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>
                @endguest
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">{{ $shop->name }}</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($shop->ratings_avg_rating))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                </span>
                        <span class="ml-1 text-gray-200">{{ number_format($shop->ratings_avg_rating, 1) }}</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">{{ $shop->address }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-8">
            <p class="text-gray-500">No veterinary clinics found.</p>
        </div>
        @endforelse
    </div>
</section>

@endsection

@push('styles')
<style>
    .veterinarian-box {
        margin-bottom: 20px; /* Adjust the value as needed */
    }
    body {
        min-height: 300vh; /* Adjust the value as needed */
    }
    
    /* Favorite button animations */
    .favorite-toggle {
        transition: transform 0.2s ease;
    }
    
    .favorite-toggle:hover {
        transform: scale(1.1);
    }
    
    .favorite-pulse {
        animation: pulse 0.4s ease-in-out;
    }
    
    .favorite-pop {
        animation: pop 0.4s ease-out;
    }
    
    .favorite-shrink {
        animation: shrink 0.3s ease-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    @keyframes pop {
        0% { transform: scale(0.8); }
        40% { transform: scale(1.2); }
        80% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }
    
    @keyframes shrink {
        0% { transform: scale(1); }
        40% { transform: scale(0.75); }
        100% { transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add favorite form - will be used for all favorite buttons
        const favoriteForm = document.createElement('form');
        favoriteForm.method = 'POST';
        favoriteForm.style.display = 'none';
        
        // Create CSRF token input
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        // Create shop_id input (will be set dynamically)
        const shopInput = document.createElement('input');
        shopInput.type = 'hidden';
        shopInput.name = 'shop_id';
        
        // Add inputs to form
        favoriteForm.appendChild(csrfInput);
        favoriteForm.appendChild(shopInput);
        
        // Add form to document
        document.body.appendChild(favoriteForm);
        
        // Add event listeners to all favorite toggle buttons
        const favoriteButtons = document.querySelectorAll('.favorite-toggle');
        
        favoriteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent the shop card click event
                
                const shopId = this.getAttribute('data-shop-id');
                const icon = this.querySelector('svg');
                
                // Add pulse animation class
                this.classList.add('favorite-pulse');
                
                // Set the action and shop_id for the form
                favoriteForm.action = '/favorites/' + shopId;
                shopInput.value = shopId;
                
                // Create and append a temporary iframe for the response
                const iframe = document.createElement('iframe');
                iframe.name = 'favorite-target-' + Date.now();
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
                
                // Set the form target to the iframe
                favoriteForm.target = iframe.name;
                
                // When the iframe loads, process the result
                iframe.onload = function() {
                    try {
                        // Try to get the response
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        const responseText = iframeDoc.body.innerText;
                        
                        // Parse the JSON response if possible
                        try {
                            const data = JSON.parse(responseText);
                            
                            if (data.isFavorited) {
                                // Add favorite
                                icon.classList.add('text-red-500');
                                icon.setAttribute('fill', 'currentColor');
                                icon.classList.add('favorite-pop');
                            } else {
                                // Remove favorite
                                icon.classList.remove('text-red-500');
                                icon.setAttribute('fill', 'none');
                                icon.classList.add('favorite-shrink');
                            }
                        } catch (jsonErr) {
                            console.error('Error parsing response:', jsonErr);
                            // Toggle the heart visually anyway based on current state
                            if (icon.classList.contains('text-red-500')) {
                                icon.classList.remove('text-red-500');
                                icon.setAttribute('fill', 'none');
                            } else {
                                icon.classList.add('text-red-500');
                                icon.setAttribute('fill', 'currentColor');
                            }
                        }
                    } catch (iframeErr) {
                        console.error('Error accessing iframe:', iframeErr);
                    }
                    
                    // Clean up
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        button.classList.remove('favorite-pulse');
                        icon.classList.remove('favorite-pop');
                        icon.classList.remove('favorite-shrink');
                    }, 500);
                };
                
                // Submit the form
                favoriteForm.submit();
            });
        });
    });
</script>
@endpush
