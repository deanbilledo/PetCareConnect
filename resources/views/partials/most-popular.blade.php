@php
    $popularShops = \App\Models\Shop::withAvg('ratings', 'rating')
        ->where('status', 'active')
        ->orderBy('ratings_avg_rating', 'desc')
        ->take(6)
        ->get();
@endphp

<section class="my-8 px-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Most Popular Shops</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($popularShops as $shop)
        <a href="{{ route('booking.show', $shop->id) }}" class="group block h-[28rem]">
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full flex flex-col">
                <!-- Image Container with Gradient Overlay -->
                <div class="relative w-full h-48 overflow-hidden">
                    <img 
                        src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                        alt="{{ $shop->name }}" 
                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                        onerror="this.src='{{ asset('images/default-shop.png') }}'"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Shop Type Badge -->
                    <span class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 text-gray-900 text-sm font-medium rounded-full shadow-sm">
                        {{ ucfirst($shop->type) }}
                    </span>
                    
                    @auth
                    <!-- Favorite Button -->
                    <button 
                        class="favorite-btn absolute top-4 right-4 p-2.5 rounded-full bg-white/95 hover:bg-white shadow-sm transition-all duration-200 transform hover:scale-105"
                        data-shop-id="{{ $shop->id }}"
                        onclick="toggleFavorite(this, {{ $shop->id }})"
                    >
                        <svg class="h-5 w-5 transition-colors duration-200" 
                             fill="currentColor" 
                             viewBox="0 0 24 24"
                             class="text-gray-400">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>
                    @endauth
                </div>

                <!-- Content Container -->
                <div class="p-5 flex-1 flex flex-col">
                    <!-- Shop Name and Rating -->
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200 line-clamp-2">
                            {{ $shop->name }}
                        </h3>
                        <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg flex-shrink-0 ml-2">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-900">
                                {{ number_format($shop->ratings_avg_rating, 1) }}
                            </span>
                            <span class="ml-1 text-xs text-gray-500">({{ $shop->ratings_count }})</span>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="flex items-start text-gray-500 mb-4">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm leading-5 line-clamp-2">{{ $shop->address }}</p>
                    </div>

                    <!-- Book Now Button - Push to bottom using margin-top:auto -->
                    <div class="mt-auto">
                        <div class="group-hover:bg-blue-600 text-center py-2.5 px-4 rounded-lg bg-gray-100 text-gray-700 group-hover:text-white transition-all duration-200">
                            Book Now
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Popular Shops</h3>
                <p class="mt-1 text-sm text-gray-500">No popular shops available at the moment.</p>
            </div>
        </div>
        @endforelse
    </div>
</section>

@push('scripts')
<script>
async function toggleFavorite(button, shopId) {
    if (!@json(auth()->check())) {
        window.location.href = '{{ route('login') }}';
        return;
    }

    const svg = button.querySelector('svg');
    
    try {
        const response = await fetch(`/favorites/${shopId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            
            if (data.isFavorited) {
                svg.classList.add('text-red-500');
                svg.classList.remove('text-gray-400');
                button.classList.add('bg-red-50');
            } else {
                svg.classList.remove('text-red-500');
                svg.classList.add('text-gray-400');
                button.classList.remove('bg-red-50');
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Check initial favorite status
document.addEventListener('DOMContentLoaded', async () => {
    if (@json(auth()->check())) {
        const buttons = document.querySelectorAll('.favorite-btn');
        for (const button of buttons) {
            const shopId = button.dataset.shopId;
            try {
                const response = await fetch(`/favorites/${shopId}/check`);
                const data = await response.json();
                const svg = button.querySelector('svg');
                
                if (data.isFavorited) {
                    svg.classList.add('text-red-500');
                    svg.classList.remove('text-gray-400');
                    button.classList.add('bg-red-50');
                } else {
                    svg.classList.remove('text-red-500');
                    svg.classList.add('text-gray-400');
                    button.classList.remove('bg-red-50');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
});
</script>
@endpush 