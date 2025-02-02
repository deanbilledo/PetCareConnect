@php
    $popularShops = \App\Models\Shop::withAvg('ratings', 'rating')
        ->where('status', 'active')
        ->orderBy('ratings_avg_rating', 'desc')
        ->take(6)
        ->get();
@endphp

<section class="my-6">
    <h2 class="text-2xl font-semibold mb-4">Most Popular</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @forelse($popularShops as $shop)
        <a href="{{ route('booking.show', $shop->id) }}" class="block transition duration-300 ease-in-out transform hover:-translate-y-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl">
                <div class="relative">
                    <img 
                        src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                        alt="{{ $shop->name }}" 
                        class="w-full h-40 object-cover"
                        onerror="this.src='{{ asset('images/default-shop.png') }}'"
                    >
                    <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                        {{ ucfirst($shop->type) }}
                    </span>
                    @auth
                <button 
                    class="favorite-btn absolute top-2 right-2 p-2 rounded-full bg-white bg-opacity-75 hover:bg-opacity-100 transition-all duration-200"
                    data-shop-id="{{ $shop->id }}"
                    onclick="toggleFavorite(this, {{ $shop->id }})"
                >
                    <svg class="h-6 w-6 transition-transform duration-300 ease-in-out" 
                         :class="{'scale-125': isFavorited}"
                         fill="currentColor" 
                         viewBox="0 0 24 24"
                         class="text-gray-400">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
                @endauth
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg">{{ $shop->name }}</h3>
                    <div class="flex items-center mt-1">
                        <div class="flex text-yellow-400">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($shop->ratings_avg_rating))
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-1 text-gray-600">
                            {{ number_format($shop->ratings_avg_rating, 1) }}
                            <span class="text-gray-400 text-sm">({{ $shop->ratings_count }})</span>
                        </span>
                    </div>
                    <p class="text-gray-600 text-sm mt-1">{{ $shop->address }}</p>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-8">
            <p class="text-gray-500">No popular shops available at the moment.</p>
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
            
            // Add animation classes
            if (data.isFavorited) {
                svg.classList.add('text-red-500', 'scale-125');
                svg.classList.remove('text-gray-400');
            } else {
                svg.classList.remove('text-red-500', 'scale-125');
                svg.classList.add('text-gray-400');
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
                } else {
                    svg.classList.remove('text-red-500');
                    svg.classList.add('text-gray-400');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
});
</script>
@endpush 