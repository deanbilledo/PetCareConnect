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
                    <button 
                        class="favorite-btn absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300"
                        data-shop-id="{{ $shop->id }}"
                        data-favorited="{{ auth()->check() && auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'true' : 'false' }}"
                    >
                        <svg class="h-6 w-6 {{ auth()->check() && auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'text-red-500' : '' }}" 
                             fill="{{ auth()->check() && auth()->user()->favorites()->where('shop_id', $shop->id)->exists() ? 'currentColor' : 'none' }}" 
                             viewBox="0 0 24 24" 
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg">{{ $shop->name }}</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-600">{{ number_format($shop->ratings_avg_rating ?? 0, 1) }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mt-1">{{ $shop->address }}</p>
                </div>
            </div>
        </a>
        @empty
            <p class="text-gray-500">No popular shops found.</p>
        @endforelse
    </div>
</section> 

@push('scripts')
<script>
document.querySelectorAll('.favorite-btn').forEach(button => {
    button.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!@json(auth()->check())) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const shopId = this.dataset.shopId;
        try {
            const response = await fetch(`/favorites/${shopId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                const svg = this.querySelector('svg');
                
                if (data.isFavorited) {
                    svg.classList.add('text-red-500');
                    svg.setAttribute('fill', 'currentColor');
                } else {
                    svg.classList.remove('text-red-500');
                    svg.setAttribute('fill', 'none');
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endpush 