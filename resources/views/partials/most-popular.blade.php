@php
    $popularShops = \App\Models\Shop::withAvg('ratings', 'rating')
        ->where('status', 'active')
        ->orderBy('ratings_avg_rating', 'desc')
        ->take(6)
        ->get();
@endphp

<section class="my-8 px-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Most Popular Shops</h2>
    
    <div class="relative shops-carousel-container">
        <!-- Navigation Buttons -->
        <button class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 carousel-prev-btn {{ count($popularShops) <= 1 ? 'hidden' : 'hidden md:block' }}">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <button class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 carousel-next-btn {{ count($popularShops) <= 1 ? 'hidden' : 'hidden md:block' }}">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        
        <!-- Carousel Track -->
        <div class="overflow-hidden">
            <div class="shops-carousel flex transition-transform duration-500 ease-in-out">
                @forelse($popularShops as $shop)
                <a href="{{ route('booking.show', $shop->id) }}" class="group block h-[28rem] min-w-[calc(100%-1rem)] md:min-w-[calc(50%-1rem)] lg:min-w-[calc(33.333%-1rem)] px-2">
                    <div class="relative rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full">
                        <!-- Full-height Image with Gradient Overlay -->
                        <div class="absolute inset-0 w-full h-full">
                            <img 
                                src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                                alt="{{ $shop->name }}" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                                onerror="this.src='{{ asset('images/default-shop.png') }}'"
                            >
                            <!-- Stronger gradient overlay for readability -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10 opacity-70 group-hover:opacity-90 transition-opacity duration-300"></div>
                        </div>
                        
                        <!-- Shop Type Badge -->
                        <span class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 text-gray-900 text-sm font-medium rounded-full shadow-sm z-10">
                            {{ ucfirst($shop->type) }}
                        </span>
                        
                        @auth
                        <!-- Favorite Button -->
                        <button 
                            class="favorite-btn absolute top-4 right-4 p-2.5 rounded-full bg-white/95 hover:bg-white shadow-sm transition-all duration-200 transform hover:scale-110 focus:outline-none z-10"
                            data-shop-id="{{ $shop->id }}"
                            onclick="toggleFavorite(event, this, {{ $shop->id }})"
                        >
                            <svg class="h-5 w-5 text-gray-400 transition-transform duration-300" 
                                 fill="currentColor" 
                                 viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                        @endauth

                        <!-- Content Container - Now positioned at the bottom over the image -->
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
                                        {{ number_format($shop->ratings_avg_rating, 1) }}
                                    </span>
                                    <span class="ml-1 text-xs text-white/80">({{ $shop->ratings_count }})</span>
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

                            <!-- Book Now Button -->
                            <div class="bg-white/20 group-hover:bg-blue-600 text-center py-3 px-5 rounded-lg text-white border border-white/30 group-hover:border-blue-500 font-medium transition-all duration-300 backdrop-blur-sm">
                                Book Now
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="w-full">
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
        </div>
        
        <!-- Carousel Indicators -->
        <div class="flex justify-center mt-6 space-x-2 {{ count($popularShops) <= 1 ? 'hidden' : '' }}">
            @for ($i = 0; $i < ceil(count($popularShops) / 3); $i++)
                <button class="carousel-indicator w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-index="{{ $i }}"></button>
            @endfor
        </div>
    </div>
</section>

@push('scripts')
<script>
async function toggleFavorite(event, button, shopId) {
    // Prevent the default action (navigation)
    event.preventDefault();
    event.stopPropagation();
    
    if (!@json(auth()->check())) {
        window.location.href = '{{ route('login') }}';
        return;
    }

    const svg = button.querySelector('svg');
    
    // Add immediate visual feedback with animation
    button.classList.add('animate-favorite-click');
    setTimeout(() => button.classList.remove('animate-favorite-click'), 300);
    
    try {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Setup form data for submission
        const formData = new FormData();
        formData.append('_token', token);
        
        const response = await fetch(`/favorites/${shopId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (response.ok) {
            const data = await response.json();
            
            if (data.isFavorited) {
                svg.classList.add('text-red-500', 'animate-favorite-pop');
                svg.classList.remove('text-gray-400');
                button.classList.add('bg-red-50');
                setTimeout(() => svg.classList.remove('animate-favorite-pop'), 500);
            } else {
                svg.classList.remove('text-red-500', 'animate-favorite-pop');
                svg.classList.add('text-gray-400', 'animate-favorite-unpop');
                button.classList.remove('bg-red-50');
                setTimeout(() => svg.classList.remove('animate-favorite-unpop'), 500);
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Check initial favorite status and setup animations
document.addEventListener('DOMContentLoaded', async () => {
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes favoriteClick {
            0% { transform: scale(1); }
            50% { transform: scale(0.85); }
            100% { transform: scale(1); }
        }
        
        @keyframes favoritePop {
            0% { transform: scale(1); }
            50% { transform: scale(1.35); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
        
        @keyframes favoriteUnpop {
            0% { transform: scale(1); }
            30% { transform: scale(0.8); }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .animate-favorite-click {
            animation: favoriteClick 300ms ease-in-out;
        }
        
        .animate-favorite-pop {
            animation: favoritePop 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .animate-favorite-unpop {
            animation: favoriteUnpop 500ms ease-in-out;
        }
        
        .favorite-btn:hover svg {
            transform: scale(1.1);
        }
    `;
    document.head.appendChild(style);

    // Existing favorite status check
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
                console.error('Error checking favorite status:', error);
            }
        }
    }

    // Carousel functionality
    const carousel = document.querySelector('.shops-carousel');
    const prevBtn = document.querySelector('.carousel-prev-btn');
    const nextBtn = document.querySelector('.carousel-next-btn');
    const indicators = document.querySelectorAll('.carousel-indicator');
    const shopItems = document.querySelectorAll('.shops-carousel > a');
    let currentSlide = 0;
    let slideInterval;
    let slidesPerView = getSlidesPerView();
    let totalSlides = shopItems.length;
    let isTransitioning = false;
    
    // Check if we have enough items to need a carousel
    const shouldEnableCarousel = totalSlides > 1;
    
    // Clone first set of items and append to end for infinite loop
    function setupInfiniteLoop() {
        // Skip if we don't need a carousel
        if (!shouldEnableCarousel) return;
        
        // Get number of items to clone (equal to slidesPerView)
        const itemsToClone = Math.min(slidesPerView, shopItems.length);
        
        // Clone the first few items and append to the end
        for (let i = 0; i < itemsToClone; i++) {
            const clone = shopItems[i].cloneNode(true);
            carousel.appendChild(clone);
        }
    }
    
    // Set active indicator
    function updateIndicators() {
        // Skip if we don't need a carousel
        if (!shouldEnableCarousel) return;
        
        const activeGroup = Math.floor(currentSlide / slidesPerView) % Math.ceil(totalSlides / slidesPerView);
        
        indicators.forEach((indicator, index) => {
            if (index === activeGroup) {
                indicator.classList.add('bg-blue-500');
                indicator.classList.remove('bg-gray-300');
            } else {
                indicator.classList.remove('bg-blue-500');
                indicator.classList.add('bg-gray-300');
            }
        });
    }
    
    // Get slides per view based on screen size
    function getSlidesPerView() {
        if (window.innerWidth >= 1024) {
            return 3; // Desktop - 3 shops per slide
        } else if (window.innerWidth >= 768) {
            return 2; // Tablet - 2 shops per slide
        } else {
            return 1; // Mobile - 1 shop per slide
        }
    }
    
    // Handle slide transition with infinite loop
    function goToSlide(index, instant = false) {
        // Skip if we don't need a carousel
        if (!shouldEnableCarousel) return;
        
        if (isTransitioning) return;
        
        if (instant) {
            carousel.style.transition = 'none';
        } else {
            carousel.style.transition = 'transform 500ms ease-in-out';
        }
        
        currentSlide = index;
        const slideWidth = shopItems[0].offsetWidth;
        const moveX = currentSlide * slideWidth;
        
        carousel.style.transform = `translateX(-${moveX}px)`;
        updateIndicators();
        
        // Check if we need to reset position (infinite loop)
        if (!instant && currentSlide >= totalSlides) {
            isTransitioning = true;
            
            // After transition completes, instantly jump back to first slide
            setTimeout(() => {
                carousel.style.transition = 'none';
                currentSlide = 0;
                carousel.style.transform = `translateX(0)`;
                isTransitioning = false;
                
                // Re-enable transition after repositioning
                setTimeout(() => {
                    carousel.style.transition = 'transform 500ms ease-in-out';
                }, 50);
            }, 500);
        }
    }
    
    // Start automatic sliding - one shop at a time with infinite loop
    function startSlideInterval() {
        // Skip if we don't need a carousel
        if (!shouldEnableCarousel) return;
        
        slideInterval = setInterval(() => {
            goToSlide(currentSlide + 1);
        }, 12000);
    }
    
    // Reset interval when manually changing slides
    function resetInterval() {
        // Skip if we don't need a carousel
        if (!shouldEnableCarousel) return;
        
        clearInterval(slideInterval);
        startSlideInterval();
    }
    
    // Event listeners for buttons
    if (prevBtn && shouldEnableCarousel) {
        prevBtn.addEventListener('click', () => {
            if (currentSlide === 0) {
                // Handle going backwards from first slide
                goToSlide(totalSlides - 1, true);
                setTimeout(() => {
                    goToSlide(totalSlides - 1);
                }, 50);
            } else {
                goToSlide(currentSlide - 1);
            }
            resetInterval();
        });
    }
    
    if (nextBtn && shouldEnableCarousel) {
        nextBtn.addEventListener('click', () => {
            goToSlide(currentSlide + 1);
            resetInterval();
        });
    }
    
    // Event listeners for indicators
    if (shouldEnableCarousel) {
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                goToSlide(index * slidesPerView);
                resetInterval();
            });
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', () => {
        // Skip if we don't need a carousel
        if (!shouldEnableCarousel) return;
        
        const newSlidesPerView = getSlidesPerView();
        if (newSlidesPerView !== slidesPerView) {
            slidesPerView = newSlidesPerView;
            goToSlide(currentSlide, true);
        }
    });
    
    // Initialize carousel only if we have more than one shop
    if (shouldEnableCarousel) {
        setupInfiniteLoop();
        updateIndicators();
        startSlideInterval();
        
        // Make first indicator active
        if (indicators.length > 0) {
            indicators[0].classList.add('bg-blue-500');
            indicators[0].classList.remove('bg-gray-300');
        }
    } else {
        // For single shop, make sure it's centered and remove any transformation
        if (carousel) {
            carousel.style.justifyContent = 'center';
            carousel.style.transform = 'none';
        }
    }
});
</script>
@endpush 