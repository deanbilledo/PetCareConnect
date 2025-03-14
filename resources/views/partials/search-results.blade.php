<!-- Search Results Section -->
<section id="search-results-section" class="hidden">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Search Results</h2>
            <div class="flex items-center space-x-4">
                <!-- Filter options -->
                <div class="hidden md:flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-600">Filter by:</label>
                    <div class="inline-flex items-center">
                        <button id="filter-all" class="px-3 py-1 bg-blue-500 text-white text-sm rounded-l hover:bg-blue-600 transition">
                            All
                        </button>
                        <button id="filter-vet" class="px-3 py-1 bg-gray-200 text-gray-800 text-sm hover:bg-gray-300 transition">
                            Veterinary
                        </button>
                        <button id="filter-groom" class="px-3 py-1 bg-gray-200 text-gray-800 text-sm rounded-r hover:bg-gray-300 transition">
                            Grooming
                        </button>
                    </div>
                </div>
                
                <!-- Clear search button -->
                <button id="clear-search" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-times mr-2"></i> Back to Home
                </button>
            </div>
        </div>
        
        <!-- Add a small message showing what was searched for -->
        <div id="search-query-display" class="mb-6 text-gray-600">
            Showing results for "<span id="search-term" class="font-medium"></span>"
        </div>
        
        <!-- Results container -->
        <div id="search-results-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Results will be populated here via JavaScript -->
            <div class="flex items-center justify-center h-64 col-span-full">
                <div class="text-center text-gray-500">
                    <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p>Searching...</p>
                </div>
            </div>
        </div>
        
        <!-- No results message -->
        <div id="no-results-message" class="hidden mt-6 p-8 bg-gray-100 rounded-lg text-center">
            <svg class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Results Found</h3>
            <p class="text-gray-600">We couldn't find any shops or services matching your search.</p>
            <p class="text-gray-600 mt-2">Try adjusting your search terms or explore our categories below.</p>
            <button id="clear-search-alt" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                Return to Homepage
            </button>
        </div>
    </div>
</section>

<!-- Shop Card Template (Hidden) - Updated to match veterinaryshop.blade.php styles -->
<template id="shop-card-template">
    <div class="shop-card group block h-[28rem]">
        <div class="relative rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full">
            <!-- Full-height Image with Gradient Overlay -->
            <div class="absolute inset-0 w-full h-full">
                <img class="shop-image w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300" src="" alt="Shop Image" onerror="this.src='/images/default-shop.png'">
                <!-- Gradient overlay for readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10 opacity-70 group-hover:opacity-90 transition-opacity duration-300"></div>
            </div>
            
            <!-- Shop Type Badge -->
            <span class="shop-type absolute top-4 left-4 px-3 py-1.5 bg-white/95 text-gray-900 text-sm font-medium rounded-full shadow-sm z-10"></span>
            
            <!-- "Featured" tag for services with matching services -->
            <div class="featured-tag absolute top-4 left-4 ml-24 px-3 py-1.5 bg-yellow-400 text-yellow-900 text-sm font-medium rounded-full shadow-sm z-10 hidden">
                Featured Service
            </div>
            
            <!-- Content Container - Positioned at the bottom over the image -->
            <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                <!-- Shop Name and Rating -->
                <div class="flex items-start justify-between mb-4">
                    <h3 class="shop-name text-xl font-bold text-white group-hover:text-blue-300 transition-colors duration-200 line-clamp-2"></h3>
                    <div class="shop-rating flex items-center bg-yellow-400/90 px-2 py-1 rounded-lg flex-shrink-0 ml-2">
                        <div class="stars hidden"></div>
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="rating-value ml-1 text-sm font-medium text-white"></span>
                    </div>
                </div>
                
                <!-- Location with icon -->
                <div class="flex items-start text-white/90 mb-3">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="shop-address text-sm leading-5 line-clamp-2"></p>
                </div>
                
                <!-- Shop description -->
                <p class="shop-description text-white/90 text-sm mb-4 line-clamp-2"></p>
                
                <!-- Distance and link container -->
                <div class="flex justify-between items-center">
                    <span class="shop-distance text-sm text-white font-medium"></span>
                    <a class="shop-link bg-white/20 group-hover:bg-blue-600 text-center py-2 px-4 rounded-lg text-white border border-white/30 group-hover:border-blue-500 font-medium transition-all duration-300 backdrop-blur-sm" href="">View Shop</a>
                </div>
            </div>
        </div>
    </div>
</template> 