@extends('layouts.app')

@section('content')

{{-- Search and Filter Section --}}
<section class="search-filter-section my-8 mt-20 mx-auto max-w-6xl px-4">
    <div class="bg-white rounded-lg shadow-sm p-2">
        <form action="{{ route('grooming') }}" method="GET" id="search-form">
            <div class="relative flex items-center">
                {{-- Store icon --}}
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                
                {{-- Search input --}}
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Pet Services Near You/Search Shop or Services" 
                       class="pl-10 pr-16 py-3 w-full text-sm text-gray-700 bg-gray-50 border-0 rounded-lg focus:ring-0 focus:outline-none"
                       autocomplete="off">
                
                {{-- Location icon button --}}
                <div class="absolute inset-y-0 right-10 flex items-center">
                    <button type="button" class="flex items-center px-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                
                {{-- Filter button --}}
                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <button type="button" id="filter-toggle" class="flex items-center p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="ml-1 text-xs font-medium text-gray-500" id="filter-count">
                            @php
                                $filterCount = 0;
                                if(request('rating')) $filterCount++;
                                if(request('service_type')) $filterCount++;
                                if(request('sort') && request('sort') != 'popular') $filterCount++;
                                echo $filterCount ? "($filterCount)" : "";
                            @endphp
                        </span>
                    </button>
                </div>
            </div>
            
            {{-- Autocomplete Results Dropdown --}}
            <div id="search-results" class="hidden absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg max-h-80 overflow-y-auto"></div>
            
            {{-- Filter dropdown - Initially hidden --}}
            <div id="filter-dropdown" class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-md shadow border border-gray-100 transition-all duration-300 ease-in-out {{ request()->anyFilled(['rating', 'service_type', 'sort']) ? '' : 'hidden' }}">
                {{-- Rating filter --}}
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Minimum Rating</label>
                    <select name="rating" id="rating" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Any Rating</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                    </select>
                </div>
                
                {{-- Service type filter --}}
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                    <select name="service_type" id="service_type" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">All Services</option>
                        <option value="basic" {{ request('service_type') == 'basic' ? 'selected' : '' }}>Basic Grooming</option>
                        <option value="full" {{ request('service_type') == 'full' ? 'selected' : '' }}>Full Service</option>
                        <option value="specialized" {{ request('service_type') == 'specialized' ? 'selected' : '' }}>Specialized</option>
                    </select>
                </div>
                
                {{-- Sort filter --}}
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" id="sort" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>
                </div>
                
                <div class="col-span-1 md:col-span-3 flex justify-center mt-2">
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Apply Filters
                    </button>
                    @if(request()->anyFilled(['search', 'rating', 'service_type', 'sort']))
                        <a href="{{ route('grooming') }}" class="inline-flex items-center ml-4 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Clear All
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

<section class="grooming-shops my-6 mb-20 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Grooming Shops
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @forelse($groomingShops as $shop)
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl cursor-pointer" onclick="window.location.href='{{ route('booking.show', $shop) }}'">
            <div class="relative h-64">
                <img src="{{ $shop->image_url ? asset($shop->image_url) : asset('images/shops/default-shop.svg') }}" alt="{{ $shop->name }}" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming
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
            <p class="text-gray-500">No grooming shops found.</p>
        </div>
        @endforelse
    </div>
</section>

{{-- Grooming Services --}}

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Grooming Services
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        @if(count($groomingServices) > 0)
            <!-- First service - larger display -->
            @php $firstService = $groomingServices->first(); @endphp
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
            @foreach($groomingServices->skip(1)->take(2) as $index => $service)
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
                <p class="text-gray-500">No popular grooming services found.</p>
            </div>
        @endif
    </div>
</section>

{{-- All Grooming Shops Section --}}
<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        All Grooming Shops
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @forelse($groomingShops as $shop)
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4 cursor-pointer" onclick="window.location.href='{{ route('booking.show', $shop) }}'">
            <div class="relative h-64">
                <img src="{{ $shop->image_url ? asset($shop->image_url) : asset('images/shops/default-shop.svg') }}" alt="{{ $shop->name }}" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming
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
            <p class="text-gray-500">No grooming shops found.</p>
        </div>
        @endforelse
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter dropdown toggle
        const filterToggle = document.getElementById('filter-toggle');
        const filterDropdown = document.getElementById('filter-dropdown');
        
        filterToggle.addEventListener('click', function() {
            filterDropdown.classList.toggle('hidden');
        });
        
        // Update filters on change
        const filterInputs = document.querySelectorAll('#rating, #service_type, #sort');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Count active filters for the badge
                let activeFilters = 0;
                if (document.getElementById('rating').value) activeFilters++;
                if (document.getElementById('service_type').value) activeFilters++;
                if (document.getElementById('sort').value && document.getElementById('sort').value !== 'popular') activeFilters++;
                
                // Update the filter count badge
                const filterCount = document.getElementById('filter-count');
                filterCount.textContent = activeFilters > 0 ? `(${activeFilters})` : '';
            });
        });

        // Live search functionality
        const searchInput = document.getElementById('search');
        const searchResults = document.getElementById('search-results');
        let debounceTimer;
        
        // Handle search input changes
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timer
            clearTimeout(debounceTimer);
            
            // Hide results if query is empty
            if (query === '') {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            
            // Debounce the API call to avoid excessive requests
            debounceTimer = setTimeout(() => {
                fetchSearchResults(query);
            }, 300);
        });
        
        // Handle clicks outside the search results to close it
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
        
        // Fetch search results via AJAX
        function fetchSearchResults(query) {
            // Don't search for very short queries
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            // Get current filter values
            const rating = document.getElementById('rating').value;
            const serviceType = document.getElementById('service_type').value;
            
            fetch(`/api/search/grooming-shops?query=${encodeURIComponent(query)}&rating=${rating}&service_type=${serviceType}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    // Fallback to direct form submission if API fails
                    searchResults.classList.add('hidden');
                });
        }
        
        // Display search results in dropdown
        function displaySearchResults(data) {
            // Clear previous results
            searchResults.innerHTML = '';
            
            if (data.length === 0) {
                searchResults.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No results found</div>';
                searchResults.classList.remove('hidden');
                return;
            }
            
            // Create results list
            const resultsList = document.createElement('div');
            resultsList.className = 'py-1';
            
            // Add each shop to the results
            data.forEach(shop => {
                const resultItem = document.createElement('a');
                resultItem.href = `/book/${shop.id}`;
                resultItem.className = 'block px-4 py-2 hover:bg-gray-100 transition duration-150 ease-in-out';
                
                // Build result content with image and shop details
                const stars = '★'.repeat(Math.round(shop.rating || 0)) + '☆'.repeat(5 - Math.round(shop.rating || 0));
                
                resultItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                            <img src="${shop.image_url || '/images/shops/default-shop.svg'}" alt="${shop.name}" class="h-full w-full object-cover">
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">${shop.name}</p>
                            <div class="flex items-center">
                                <span class="text-xs text-yellow-400">${stars}</span>
                                <span class="ml-1 text-xs text-gray-500">${(shop.rating || 0).toFixed(1)}</span>
                            </div>
                            <p class="text-xs text-gray-500 truncate">${shop.address || ''}</p>
                        </div>
                    </div>
                `;
                
                resultsList.appendChild(resultItem);
            });
            
            // View all results option
            const viewAllItem = document.createElement('div');
            viewAllItem.className = 'border-t border-gray-100 mt-1 pt-1';
            viewAllItem.innerHTML = `
                <button type="submit" class="block w-full text-center px-4 py-2 text-sm text-indigo-600 font-medium hover:bg-gray-50">
                    View all results
                </button>
            `;
            
            resultsList.appendChild(viewAllItem);
            searchResults.appendChild(resultsList);
            searchResults.classList.remove('hidden');
        }

        // Favorites functionality
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

@endsection

