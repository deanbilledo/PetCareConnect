@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 mb-20">
    <h1 class="text-3xl font-bold mb-8 mt-8">My Favorite Shops</h1>

    @if($favoriteShops->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <p class="text-gray-600 mt-4">You haven't added any shops to your favorites yet.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out">
                Browse Shops
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favoriteShops as $shop)
                <div class="group block h-[28rem]">
                    <div class="relative rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full">
                        <!-- Full-height Image with Gradient Overlay -->
                        <div class="absolute inset-0 w-full h-full">
                            <img 
                                src="{{ $shop->image_url ? asset($shop->image_url) : asset('images/default-shop.png') }}" 
                                alt="{{ $shop->name }}" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                                onerror="this.src='{{ asset('images/default-shop.png') }}'"
                            >
                            <!-- Stronger gradient overlay for readability -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10 opacity-70 group-hover:opacity-90 transition-opacity duration-300"></div>
                        </div>
                        
                        <!-- Shop Type Badge -->
                        <span class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 text-gray-900 text-sm font-medium rounded-full shadow-sm z-10">
                            {{ ucfirst($shop->type ?? 'Shop') }}
                        </span>
                        
                        <!-- Favorite Button -->
                        <button 
                            class="favorite-btn absolute top-4 right-4 p-2.5 rounded-full bg-white/95 hover:bg-white shadow-sm transition-all duration-200 transform hover:scale-110 focus:outline-none z-10"
                            data-shop-id="{{ $shop->id }}"
                        >
                            <svg class="h-5 w-5 text-red-500" 
                                fill="currentColor" 
                                viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>

                        <!-- Content Container - Positioned at the bottom over the image -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                            <!-- Shop Name and Rating -->
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-xl font-bold text-white group-hover:text-blue-300 transition-colors duration-200 line-clamp-2">
                                    {{ $shop->name }}
                                </h3>
                                <div class="flex items-center bg-yellow-400/90 px-2 py-1 rounded-lg flex-shrink-0 ml-2">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-white">
                                        {{ number_format($shop->ratings_avg_rating ?? 0, 1) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="flex items-start text-white/90 mb-5">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-sm leading-5 line-clamp-2">{{ $shop->address }}</p>
                            </div>

                            <!-- View Details Button -->
                            <a href="{{ route('booking.show', $shop) }}" class="block bg-white/20 hover:bg-blue-600 text-center py-3 px-5 rounded-lg text-white border border-white/30 hover:border-blue-500 font-medium transition-all duration-300 backdrop-blur-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('.favorite-btn').forEach(button => {
    button.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const shopId = this.dataset.shopId;
        const svg = this.querySelector('svg');
        const card = this.closest('.group');
        
        // Add animation class
        svg.classList.add('scale-125');
        
        try {
            const response = await fetch(`/favorites/${shopId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                // Use animation to remove the card
                card.classList.add('opacity-0', 'scale-95');
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.remove();
                    
                    // If no more favorites, reload the page to show empty state
                    if (document.querySelectorAll('.favorite-btn').length === 0) {
                        window.location.reload();
                    }
                }, 500);
            }
        } catch (error) {
            console.error('Error:', error);
            // Remove animation class if there's an error
            svg.classList.remove('scale-125');
        }
    });
});
</script>
@endpush
@endsection 