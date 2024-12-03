@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 mt-8">My Favorite Shops</h1>

    @if($favoriteShops->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-600">You haven't added any shops to your favorites yet.</p>
            <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-600 mt-2 inline-block">
                Browse Shops
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favoriteShops as $shop)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="relative">
                        <img 
                            src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                            alt="{{ $shop->name }}" 
                            class="w-full h-48 object-cover"
                        >
                        <button 
                            class="favorite-btn absolute top-2 right-2 text-red-500 hover:text-red-600"
                            data-shop-id="{{ $shop->id }}"
                        >
                            <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
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
                        <a 
                            href="{{ route('booking.show', $shop->id) }}" 
                            class="mt-3 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
                        >
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<div class="mb-64"></div>
@push('scripts')
<script>
document.querySelectorAll('.favorite-btn').forEach(button => {
    button.addEventListener('click', async function() {
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
                // Remove the shop card from the view
                this.closest('.bg-white').remove();
                
                // If no more favorites, reload the page to show empty state
                if (document.querySelectorAll('.favorite-btn').length === 0) {
                    window.location.reload();
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endpush
@endsection 