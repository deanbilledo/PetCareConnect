@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gray-100 rounded-lg my-6 p-4 lg:p-8 relative overflow-hidden z-0">
        <div class="flex flex-col lg:flex-row justify-between items-center">
            <div class="z-10 mb-8 lg:mb-0 lg:w-1/2 text-left">
                <p class="text-gray-600 mb-2">No need to worry,</p>
                <h2 class="text-4xl font-bold mb-4">We Provide Grooming and Vet Checks</h2>
               
                <div class="relative">
                    <div class="relative flex items-center">
                        <!-- Map Icon to View All Shops -->
                        <svg class="h-5 w-5 text-gray-400 absolute left-3 cursor-pointer hover:text-blue-500 transition-colors duration-200" 
                             id="view-all-shops"
                             title="View All Pet Services"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    <input 
                        type="text" 
                        id="location-search" 
                        placeholder="Pet Services Near You/Search Shop or Services" 
                        class="w-full px-12 py-2 border rounded-full text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <svg class="h-5 w-5 text-gray-400 absolute right-3 cursor-pointer hover:text-blue-500 transition-colors duration-200" 
                         id="getCurrentLocation"
                             title="Use Your Current Location"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    </div>
                    <!-- We've removed the dropdown as we're going directly to full search results -->
                </div>
            </div>
            <div class="w-full lg:w-1/2 relative">
                <img src="{{ asset('images/petdog2.png') }}" alt="Happy dog" class="w-full h-auto object-cover rounded-lg">
            </div>
        </div>
    </section>

    <!-- Search Results Section -->
    @include('partials.search-results')

    <!-- All the sections below will be hidden when showing search results -->
    <div id="content-sections">
    <!-- Most Popular Section -->
    @include('partials.most-popular', ['popularShops' => $popularShops])

    
    <!-- Services Section -->
    @include('partials.services', ['services' => $services])

    <!-- Veterinaries Section -->
    @include('partials.veterinaryshop')

    <!-- Grooming Section -->
    @include('partials.groomingshop')

    <!-- Pet Care Tips Section -->
    <section class="bg-green-50 rounded-lg my-6 p-4 lg:p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-3">Pet Care Tips & Guidelines</h2>
            <p class="text-gray-600">Expert advice to keep your furry friends healthy and happy</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Diet Tips Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3z M16 8h.01M8 8h.01M8 16h.01M16 16h.01M12 12h.01"/>
                    </svg>
                    <h3 class="text-xl font-semibold">Healthy Diet Guide</h3>
                </div>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Feed age-appropriate pet food
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Maintain regular feeding schedule
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Provide fresh water daily
                    </li>
                </ul>
            </div>

            <!-- Grooming Tips Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                    </svg>
                    <h3 class="text-xl font-semibold">Grooming Routines</h3>
                </div>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Brush coat regularly
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Trim nails monthly
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Regular teeth cleaning
                    </li>
                </ul>
            </div>

            <!-- Health Guidelines Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <h3 class="text-xl font-semibold">Health Tips</h3>
                </div>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Regular vet check-ups
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Keep vaccinations updated
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-2">•</span>
                        Daily exercise routine
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('pet-care-tips') }}" class="inline-flex items-center text-green-600 hover:text-green-700">
                View more pet care tips
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- Join as Partner Section -->
    <section class="bg-blue-50 rounded-lg my-6 p-4 lg:p-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="lg:w-1/2 mb-6 lg:mb-0">
                <h2 class="text-3xl font-bold mb-4">Join Our Platform as a Partner</h2>
                <p class="text-gray-600 mb-4">Are you a veterinary clinic or grooming shop owner? Partner with us to reach more pet owners and grow your business.</p>
                
                <!-- Add subscription info box -->
                <div class="bg-white/80 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold text-blue-800">Start with a 30-Day Free Trial</span>
                    </div>
                    <p class="text-sm text-blue-700 mb-4">
                        After trial period, continue growing your business for just ₱299/month.
                    </p>
                </div>
                
                <ul class="mb-6">
                    <li class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Reach more customers
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Manage appointments easily
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Grow your business
                    </li>
                </ul>
                <button 
                    onclick="window.location.href='{{ route('shop.pre.register') }}'"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-full transition duration-300 ease-in-out">
                    Become a Partner
                </button>
            </div>
            <div class="lg:w-1/2">
                <img src="{{ asset('images/partner.png') }}" alt="Partner with us" class="w-3/4 h-auto rounded-lg mx-auto">
            </div>
        </div>
    </section>

        </div> <!-- End of content-sections div -->

    <!-- Add this modal/popup HTML at the end of your content section -->
    <div id="map-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-11/12 max-w-5xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Nearby Pet Services
                </h3>
                
                <div class="flex items-center space-x-3">
                    <!-- Service Type Filter -->
                    <div class="flex items-center">
                        <label class="mr-2 text-sm font-medium text-gray-700">Show:</label>
                        <select id="service-type-filter" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="all">All Services</option>
                            <option value="veterinary">Veterinary Only</option>
                            <option value="grooming">Grooming Only</option>
                        </select>
                    </div>
                    
                    <button id="close-modal" class="text-gray-500 hover:text-gray-700 focus:outline-none p-1 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            </div>
            
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Map container -->
                <div class="md:w-3/4">
                    <div id="modal-map" class="h-[450px] w-full rounded-lg border border-gray-200 shadow-md"></div>
                </div>
                
                <!-- Info panel -->
                <div class="md:w-1/4 flex flex-col">
                    <!-- Legend -->
                    <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                        <h4 class="font-medium text-gray-900 mb-2">Map Legend</h4>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-xs text-white border-2 border-white mr-2"></div>
                                <span class="text-sm">Your Location</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center text-xs text-white border-2 border-white mr-2">V</div>
                                <span class="text-sm">Veterinary Services</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-xs text-white border-2 border-white mr-2">G</div>
                                <span class="text-sm">Grooming Services</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected shop info (will be filled dynamically) -->
                    <div id="selected-shop-info" class="mt-4 bg-white rounded-lg p-4 border border-gray-200 shadow-sm flex-grow">
                        <p class="text-gray-500 text-sm italic text-center">Select a shop marker to view details</p>
                    </div>
                    
                    <!-- Total count of visible shops -->
                    <div class="mt-4 bg-blue-50 text-blue-800 p-3 rounded-lg text-sm">
                        <strong id="visible-shops-count">0</strong> pet service(s) found in this area
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Chatbot Section -->
<div class="fixed bottom-4 right-4 z-50">
    <input type="checkbox" id="chat-toggle" class="hidden peer">
    
    <!-- Chat Window -->
    <div class="hidden peer-checked:block w-96 h-[32rem] bg-white shadow-lg rounded-lg border flex flex-col absolute bottom-16 right-0">
        <div class="bg-blue-600 text-white p-4 rounded-t-lg flex justify-between items-center">
            <span class="font-bold text-lg">Daena AI Assistant</span>
            <label for="chat-toggle" class="text-white hover:bg-blue-700 rounded-full p-2 cursor-pointer text-xl">
                ×
            </label>
        </div>
        
        <!-- Quick Prompt Buttons -->
        <div class="flex gap-2 p-3 bg-gray-50 border-b overflow-x-auto">
            <button onclick="sendQuickPrompt('Please introduce yourself')" 
                class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm whitespace-nowrap">Introduce</button>
            <button onclick="sendQuickPrompt('What can you help me with?')" 
                class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm whitespace-nowrap">Help</button>
            <button onclick="clearChat()" 
                class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm whitespace-nowrap">Clear</button>
        </div>
        
        <!-- Message List -->
        <div id="chat-messages" class="flex-grow overflow-y-auto p-4 space-y-3 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-blue-100" style="max-height: 24rem;">
            <!-- System message -->
            <div class="text-sm text-gray-500 italic">Chat initialized with llama3.2 model</div>
        </div>

        <!-- Thinking Indicator -->
        <div id="thinking-indicator" class="hidden px-4 py-2 text-sm text-gray-500 italic">
            Daena is thinking...
        </div>
        
        <!-- Message Input -->
        <form id="chat-form" class="p-4 border-t flex flex-col gap-2">
            <div class="flex gap-2 items-end">
                <textarea 
                    id="chat-input"
                    rows="1"
                    placeholder="Type a message..."
                    class="flex-grow p-2 border rounded-lg resize-y overflow-auto focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
                <button 
                    id="send-message"
                    type="submit"
                    class="h-[42px] bg-blue-600 text-white px-4 rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0"
                >
                    Send
                </button>
            </div>
        </form>
    </div>
    
    <!-- Chat Head Button -->
    <label 
        for="chat-toggle"
        class="bg-blue-600 text-white px-4 py-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors cursor-pointer">
        Daena AI
    </label>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('location-search');
        const locationButton = document.getElementById('getCurrentLocation');
        const searchResults = document.getElementById('search-results');
        const modal = document.getElementById('map-modal');
        const closeModal = document.getElementById('close-modal');
        
        // Declare these variables in the global scope so they're accessible to all functions
        let modalMap = null;
        let markers = [];
        
        // Log elements to check if they're found correctly
        console.log('Search input:', searchInput);
        console.log('Location button:', locationButton);
        console.log('Search results:', searchResults);
        console.log('Modal:', modal);
        console.log('Close modal button:', closeModal);
        
        // Initialize modal map when showing
        function initModalMap() {
            try {
                console.log('Initializing map...');
                if (!modalMap) {
                    if (!document.getElementById('modal-map')) {
                        console.error('Modal map element not found!');
                        return false;
                    }
                    
                    modalMap = L.map('modal-map', {
                        center: [0, 0],
                        zoom: 13
                    });
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(modalMap);
                    
                    console.log('Map initialized successfully');
                }
                return true;
            } catch (error) {
                console.error('Error initializing map:', error);
                return false;
            }
        }
        
        // Show modal and initialize map
        function showModal() {
            try {
                console.log('Showing modal...');
                if (!modal) {
                    console.error('Modal element not found!');
                    return;
                }
                
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
                
                // Wait a moment for the modal to be visible in the DOM
                setTimeout(() => {
                    const mapInitialized = initModalMap();
                    if (!mapInitialized) {
                        throw new Error('Failed to initialize map');
                    }
                    
                    // Force map to refresh after modal is visible
                    setTimeout(() => {
                        if (modalMap) {
                            modalMap.invalidateSize();
                            console.log('Map size updated');
                            
                            // Update the shop count
                            updateVisibleShopsCount();
                        }
                    }, 250);
                }, 100);
                
                // Set up service type filter
                const serviceTypeFilter = document.getElementById('service-type-filter');
                if (serviceTypeFilter) {
                    serviceTypeFilter.addEventListener('change', function() {
                        const selectedType = this.value;
                        
                        markers.forEach(marker => {
                            // Skip user location marker
                            if (!marker.options.shopType) return;
                            
                            if (selectedType === 'all' || marker.options.shopType === selectedType) {
                                // Show marker
                                marker.getElement().classList.remove('marker-hidden');
                                marker.getElement().style.display = '';
                            } else {
                                // Hide marker
                                marker.getElement().classList.add('marker-hidden');
                                marker.getElement().style.display = 'none';
                            }
                        });
                        
                        // Update count after filtering
                        updateVisibleShopsCount();
                    });
                }
            } catch (error) {
                console.error('Error showing modal:', error);
                alert('Error loading map. Please try again.');
            }
        }
        
        // Clear existing markers
        function clearMarkers() {
            markers.forEach(marker => {
                if (modalMap && marker) {
                    modalMap.removeLayer(marker);
                }
            });
            markers = [];
        }
        
        // Add event listeners
        if (searchInput) {
            searchInput.addEventListener('input', debounce(handleSearch, 500));
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
        e.preventDefault();
                    handleSearch(e);
                }
            });
        }
        
        if (closeModal) {
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            });
        }
        
        // Debounce function to prevent too many API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Handle search input
        function handleSearch(e) {
            const query = searchInput.value.trim();
            
            if (query.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }
            
            // Show loading state in search results
            searchResults.innerHTML = `
                <div class="p-3 text-center text-gray-500">
                    <svg class="animate-spin h-5 w-5 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Searching...</span>
                </div>
            `;
            searchResults.classList.remove('hidden');
            
            // Use OpenStreetMap's Nominatim API to geocode the text query
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to geocode location');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.length > 0) {
                        const { lat, lon: lng } = data[0];
                        console.log('Geocoded to:', lat, lng);
                        
                        // Now fetch nearby shops based on geocoded location
                        return fetch(`/shops/search-location?latitude=${lat}&longitude=${lng}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Failed to fetch nearby shops');
                                }
                                return response.json();
                            })
                            .then(shopsData => {
                                // Add "Use current location" option at the top
                                let resultsHTML = `
                                    <div class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                                        <div class="flex items-center font-medium text-blue-500">
                                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Use my current location
                                        </div>
                                    </div>
                                    <div class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200" data-lat="${lat}" data-lng="${lng}">
                                        <div class="font-medium">${data[0].display_name || query}</div>
                                        <div class="text-sm text-gray-600">View all shops near this location</div>
                                    </div>
                                `;
                                
                                // Add nearby shops to the dropdown
                                if (shopsData.success && shopsData.shops.length > 0) {
                                    resultsHTML += `<div class="p-2 border-b border-gray-200"><div class="text-xs font-semibold text-gray-500 uppercase">Nearby Shops</div></div>`;
                                    
                                    shopsData.shops.slice(0, 5).forEach(shop => {
                                        const distanceKm = Math.round(shop.distance * 10) / 10;
                                        resultsHTML += `
                                            <div class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200" data-shop-id="${shop.id}">
                                                <div class="flex items-center justify-between">
                                                    <div class="font-medium">${shop.name}</div>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-${shop.type === 'veterinary' ? 'red-100 text-red-800' : 'green-100 text-green-800'}">
                                                        ${shop.type.charAt(0).toUpperCase() + shop.type.slice(1)}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <div class="text-sm text-gray-600 truncate max-w-xs">${shop.address}</div>
                                                    <div class="text-xs text-gray-500">${distanceKm} km</div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                    
                                    // Add "View more" option if there are more shops
                                    if (shopsData.shops.length > 5) {
                                        resultsHTML += `
                                            <div class="p-2 hover:bg-gray-100 cursor-pointer text-center text-blue-600" data-lat="${lat}" data-lng="${lng}">
                                                View all ${shopsData.shops.length} shops on map
                                            </div>
                                        `;
                                    }
                                } else {
                                    resultsHTML += `
                                        <div class="p-3 text-center text-gray-500">
                                            No pet services found near this location
                                        </div>
                                    `;
                                }
                                
                                searchResults.innerHTML = resultsHTML;
                                searchResults.classList.remove('hidden');
                                
                                // Add click handlers for the results
                                const useLocationOption = searchResults.querySelector('.text-blue-500').closest('div');
                                if (useLocationOption) {
                                    useLocationOption.addEventListener('click', function() {
                                        getCurrentUserLocation();
                                        searchResults.classList.add('hidden');
                                    });
                                }
                                
                                // Add handlers for viewing all shops near a location
                                const viewAllOptions = searchResults.querySelectorAll('[data-lat][data-lng]');
                                viewAllOptions.forEach(option => {
                                    option.addEventListener('click', function() {
                                        const lat = parseFloat(this.getAttribute('data-lat'));
                                        const lng = parseFloat(this.getAttribute('data-lng'));
                                        
                                        // Use the geocodeAndSearch function but pass in the coordinates directly
                                        geocodeAndSearchWithCoords(query, lat, lng);
                                        searchResults.classList.add('hidden');
                                    });
                                });
                                
                                // Add handlers for individual shop options
                                const shopOptions = searchResults.querySelectorAll('[data-shop-id]');
                                shopOptions.forEach(option => {
                                    option.addEventListener('click', function() {
                                        const shopId = this.getAttribute('data-shop-id');
                                        window.location.href = `/book/${shopId}`;
                                    });
                                });
                            });
                    } else {
                        // No geocoding results found
                        searchResults.innerHTML = `
                            <div class="p-2 hover:bg-gray-100 cursor-pointer">
                                <div class="flex items-center font-medium text-blue-500">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Use my current location
                                </div>
                            </div>
                            <div class="p-3 text-center text-gray-500">
                                No results found. Try a different search.
                            </div>
                        `;
                        
                        // Add click handler for current location option
                        const useLocationOption = searchResults.querySelector('.text-blue-500').closest('div');
                        if (useLocationOption) {
                            useLocationOption.addEventListener('click', function() {
                                getCurrentUserLocation();
                                searchResults.classList.add('hidden');
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error in search:', error);
                    searchResults.innerHTML = `
                        <div class="p-2 hover:bg-gray-100 cursor-pointer">
                            <div class="flex items-center font-medium text-blue-500">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Use my current location
                            </div>
                        </div>
                        <div class="p-3 text-center text-gray-500">
                            Error searching. Please try again.
                        </div>
                    `;
                    
                    // Add click handler for current location option
                    const useLocationOption = searchResults.querySelector('.text-blue-500').closest('div');
                    if (useLocationOption) {
                        useLocationOption.addEventListener('click', function() {
                            getCurrentUserLocation();
                            searchResults.classList.add('hidden');
                        });
                    }
                });
        }
        
        // Get current user location
        function getCurrentUserLocation() {
            if ("geolocation" in navigator) {
                locationButton.classList.add('animate-pulse');
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        try {
                            console.log('Position obtained:', position);
                            const { latitude, longitude } = position.coords;
                            
                            // Show the modal first
                            showModal();
                            
                            // Give the map time to initialize
                            setTimeout(() => {
                                if (!modalMap) {
                                    console.error('Map not initialized after showing modal');
                                    alert('Error initializing map. Please try again.');
                                    locationButton.classList.remove('animate-pulse');
                                    return;
                                }
                                
                                // Center map on user's location
                                modalMap.setView([latitude, longitude], 13);
                                clearMarkers();
                                
                                // Add user's location marker
                                const userIcon = L.divIcon({
                                    html: `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white"></div>`,
                                    className: 'user-location-marker',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                });
                                
                                const userMarker = L.marker([latitude, longitude], { icon: userIcon })
                                    .addTo(modalMap)
                                    .bindPopup('<strong>Your Location</strong>')
                                    .openPopup();
                                
                                markers.push(userMarker);
                                
                                // Fetch nearby shops
                                fetch(`/shops/search-location?latitude=${latitude}&longitude=${longitude}`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Failed to fetch nearby shops');
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Found nearby shops:', data);
                                        locationButton.classList.remove('animate-pulse');
                                        
                                        if (data.success && data.shops.length > 0) {
                                            // Update shop count
                                            const visibleShopsCount = document.getElementById('visible-shops-count');
                                            if (visibleShopsCount) {
                                                visibleShopsCount.textContent = data.shops.length;
                                            }
                                            
                                            // Add shop markers to the map
                                            data.shops.forEach(shop => {
                                                addShopMarker(shop, latitude, longitude);
                                            });
                                        } else {
                                            // No shops found
                                            alert('No pet services found nearby. Try a different location or increase search radius.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching shops:', error);
                                        locationButton.classList.remove('animate-pulse');
                                        alert('Error finding nearby shops. Please try again.');
                                    });
                            }, 500); // Wait longer for map to initialize
                        } catch (error) {
                            console.error('Error in location processing:', error);
                            locationButton.classList.remove('animate-pulse');
                            alert('Error finding nearby places. Please try again.');
                        }
                    },
                    function(error) {
                        console.error('Geolocation error:', error);
                        locationButton.classList.remove('animate-pulse');
                        
                        let errorMessage = 'Error getting your location. ';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Please enable location access in your browser settings.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Location information is unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Location request timed out.';
                                break;
                            default:
                                errorMessage += 'An unknown error occurred.';
                        }
                        alert(errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser");
            }
        }

        // Show shops on the map
        function showShopsOnMap(userLat, userLng, shops) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            
            // Initialize modal map if needed
            if (!modalMap) {
                modalMap = L.map('modal-map').setView([userLat, userLng], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(modalMap);
            } else {
                modalMap.setView([userLat, userLng], 13);
                // Clear existing markers
                markers.forEach(marker => modalMap.removeLayer(marker));
                markers = [];
            }
            
            // Force map to refresh after modal is visible
            setTimeout(() => {
                modalMap.invalidateSize();
            }, 250);
            
            // Add user marker
            const userIcon = L.divIcon({
                html: `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white"></div>`,
                className: 'user-location-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            
            const userMarker = L.marker([userLat, userLng], { icon: userIcon })
                .addTo(modalMap)
                .bindPopup('<strong>Your Location</strong>')
                .openPopup();
            
            markers.push(userMarker);
            
            // Add shop markers
            shops.forEach(shop => {
                const shopIcon = L.divIcon({
                    html: `<div class="w-6 h-6 ${shop.type === 'veterinary' ? 'bg-red-500' : 'bg-green-500'} rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white">
                        ${shop.type === 'veterinary' ? 'V' : 'G'}
                    </div>`,
                    className: 'shop-marker',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                const distanceKm = Math.round(shop.distance * 10) / 10;
                
                const popupContent = `
                    <div class="shop-popup max-w-xs">
                        <div class="bg-${shop.type === 'veterinary' ? 'red-100' : 'green-100'} px-3 py-2 rounded-t-lg">
                            <div class="font-bold text-lg">${shop.name}</div>
                            <div class="text-gray-600 text-xs">${shop.address}</div>
                        </div>
                        <div class="p-3">
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${shop.type === 'veterinary' ? 'red-100 text-red-800' : 'green-100 text-green-800'}">
                                    ${shop.type.charAt(0).toUpperCase() + shop.type.slice(1)}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">${distanceKm} km away</span>
                            </div>
                            <a href="/book/${shop.id}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white !text-white font-semibold text-sm rounded hover:bg-blue-700 transition-colors shadow-sm">
                                Book now
                            </a>
                        </div>
                    </div>
                `;
                
                const shopMarker = L.marker([shop.latitude, shop.longitude], { icon: shopIcon, shopType: shop.type })
                    .addTo(modalMap)
                    .bindPopup(popupContent);
                    
                // Add click event to show shop details in the info panel
                shopMarker.on('click', function() {
                    showShopDetails(shop, distanceKm);
                });
                
                markers.push(shopMarker);
            });
        }
        
        // Add click handler to the location button
        if (locationButton) {
            locationButton.addEventListener('click', function(e) {
                e.preventDefault();
                getCurrentUserLocation();
            });
        }

        // Add click handler for view all shops button
        const viewAllShopsButton = document.getElementById('view-all-shops');
        if (viewAllShopsButton) {
            viewAllShopsButton.addEventListener('click', function(e) {
                e.preventDefault();
                fetchAllShops();
            });
        }

        // Function to fetch and display all shops
        function fetchAllShops() {
            // Show loading state
            viewAllShopsButton.classList.add('opacity-75', 'cursor-wait');
            viewAllShopsButton.disabled = true;
            
            fetch('/shops/all')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset button state
                    viewAllShopsButton.classList.remove('opacity-75', 'cursor-wait');
                    viewAllShopsButton.disabled = false;
                    
                    if (data.success && data.shops.length > 0) {
                        showAllShopsOnMap(data.shops);
                    } else {
                        alert('No pet services found in our database.');
                    }
                })
                .catch(error => {
                    // Reset button state
                    viewAllShopsButton.classList.remove('opacity-75', 'cursor-wait');
                    viewAllShopsButton.disabled = false;
                    
                    console.error('Error fetching shops:', error);
                    alert('Error loading pet services. Please try again.');
                });
        }

        // Function to display all shops on the map
        function showAllShopsOnMap(shops) {
            // Show the modal
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            
            // Initialize the map if needed
            if (!modalMap) {
                // Default center point for Philippines if no shops
                let centerLat = 12.8797;
                let centerLng = 121.7740;
                
                // If shops exist, center on first shop
                if (shops.length > 0) {
                    centerLat = parseFloat(shops[0].latitude);
                    centerLng = parseFloat(shops[0].longitude);
                }
                
                modalMap = L.map('modal-map').setView([centerLat, centerLng], 6);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(modalMap);
            } else {
                // Clear any existing markers
                clearMarkers();
                
                // Default center and zoom for Philippines
                modalMap.setView([12.8797, 121.7740], 6);
            }
            
            // Force map to refresh after modal is visible
            setTimeout(() => {
                modalMap.invalidateSize();
            }, 250);
            
            // Create bounds to fit all markers
            const bounds = L.latLngBounds();
            
            // Add shop markers
            shops.forEach(shop => {
                // Check if lat/lng are valid numbers
                const lat = parseFloat(shop.latitude);
                const lng = parseFloat(shop.longitude);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.warn(`Invalid coordinates for shop ${shop.id} - ${shop.name}`);
                    return;
                }
                
                const shopIcon = L.divIcon({
                    html: `<div class="w-6 h-6 ${shop.type === 'veterinary' ? 'bg-red-500' : 'bg-green-500'} rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white">
                        ${shop.type === 'veterinary' ? 'V' : 'G'}
                    </div>`,
                    className: 'shop-marker',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                const distanceKm = Math.round(shop.distance * 10) / 10;
                
                const popupContent = `
                    <div class="shop-popup max-w-xs">
                        <div class="bg-${shop.type === 'veterinary' ? 'red-100' : 'green-100'} px-3 py-2 rounded-t-lg">
                            <div class="font-bold text-lg">${shop.name}</div>
                            <div class="text-gray-600 text-xs">${shop.address}</div>
                        </div>
                        <div class="p-3">
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${shop.type === 'veterinary' ? 'red-100 text-red-800' : 'green-100 text-green-800'}">
                                    ${shop.type.charAt(0).toUpperCase() + shop.type.slice(1)}
                                </span>
                                <span class="ml-2 text-xs text-gray-500">${distanceKm} km away</span>
                            </div>
                            <a href="/book/${shop.id}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white !text-white font-semibold text-sm rounded hover:bg-blue-700 transition-colors shadow-sm">
                                Book now
                            </a>
                        </div>
                    </div>
                `;
                
                const shopMarker = L.marker([lat, lng], { icon: shopIcon })
                    .addTo(modalMap)
                    .bindPopup(popupContent);
                
                // Add click event to show shop details in the info panel
                shopMarker.on('click', function() {
                    showShopDetails(shop, distanceKm);
                });
                
                // Add to markers array
                markers.push(shopMarker);
                
                // Extend bounds to include this marker
                bounds.extend([lat, lng]);
            });
            
            // If we have markers, fit the map to show all of them
            if (markers.length > 0) {
                modalMap.fitBounds(bounds, {
                    padding: [50, 50], // Add padding around the bounds
                    maxZoom: 13 // Don't zoom in too far
                });
            }
        }

        // Function to handle shop details display in the side panel
        function showShopDetails(shop, distance) {
            const selectedShopInfo = document.getElementById('selected-shop-info');
            if (!selectedShopInfo) {
                console.warn('Selected shop info element not found');
                return;
            }
            
            try {
                const isVeterinary = shop.type === 'veterinary';
                const ratingStars = generateRatingStars(shop.ratings_avg_rating || 0);
                
                selectedShopInfo.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-start justify-between">
                            <h3 class="font-bold text-gray-900 truncate">${shop.name}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${isVeterinary ? 'red-100 text-red-800' : 'green-100 text-green-800'}">
                                ${shop.type.charAt(0).toUpperCase() + shop.type.slice(1)}
                            </span>
                        </div>
                        
                        <p class="text-xs text-gray-600 border-b pb-2">${shop.address}</p>
                        
                        <div class="flex justify-between text-sm">
                            <div>
                                <div class="text-yellow-400">${ratingStars}</div>
                                <div class="text-xs text-gray-500">${shop.ratings_count || 0} reviews</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium">${distance} km</div>
                                <div class="text-xs text-gray-500">from your location</div>
                            </div>
                        </div>
                        
                        ${shop.description ? `<p class="text-sm text-gray-600 mt-2">${shop.description}</p>` : ''}
                        
                        <div class="flex justify-center pt-2">
                            <a href="/book/${shop.id}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Book Appointment
                            </a>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error showing shop details:', error);
                selectedShopInfo.innerHTML = '<p class="text-gray-500 text-sm italic text-center">Error loading shop details</p>';
            }
        }
        
        // Generate rating stars based on rating value
        function generateRatingStars(rating) {
            const fullStars = Math.floor(rating);
            const halfStar = rating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
            
            let starsHtml = '';
            
            // Full stars
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<svg class="w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
            }
            
            // Half star
            if (halfStar) {
                starsHtml += '<svg class="w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="inset(0 50% 0 0)"></path></svg>';
            }
            
            // Empty stars
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<svg class="w-4 h-4 inline-block text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
            }
            
            return starsHtml;
        }
        
        // Function to update the visible shops count
        function updateVisibleShopsCount() {
            try {
                const visibleShopsCount = document.getElementById('visible-shops-count');
                if (!visibleShopsCount) {
                    console.warn('Visible shops count element not found');
                    return;
                }
                
                // Count visible markers (excluding user location marker)
                let count = 0;
                markers.forEach(marker => {
                    if (marker.options && marker.options.shopType && marker._icon && !marker._icon.classList.contains('marker-hidden')) {
                        count++;
                    }
                });
                
                visibleShopsCount.textContent = count;
            } catch (error) {
                console.error('Error updating visible shops count:', error);
            }
        }

        // Add this function to handle adding shop markers to the map
        function addShopMarker(shop, userLat, userLng) {
            // Check if modalMap is initialized
            if (!modalMap) {
                console.error('Cannot add marker - map not initialized');
                return null;
            }
            
            // Calculate distance if not provided
            let distanceKm = shop.distance;
            if (!distanceKm && userLat && userLng) {
                // Calculate rough distance using Haversine formula
                const R = 6371; // Earth's radius in km
                const dLat = (shop.latitude - userLat) * Math.PI / 180;
                const dLon = (shop.longitude - userLng) * Math.PI / 180;
                const a = 
                    Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(userLat * Math.PI / 180) * Math.cos(shop.latitude * Math.PI / 180) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                distanceKm = R * c;
            }
            
            // Round distance to 1 decimal place
            distanceKm = Math.round(distanceKm * 10) / 10;
            
            // Create shop icon
            const shopIcon = L.divIcon({
                html: `<div class="w-6 h-6 ${shop.type === 'veterinary' ? 'bg-red-500' : 'bg-green-500'} rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white">
                    ${shop.type === 'veterinary' ? 'V' : 'G'}
                </div>`,
                className: 'shop-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            
            // Create popup content
            const popupContent = `
                <div class="shop-popup max-w-xs">
                    <div class="bg-${shop.type === 'veterinary' ? 'red-100' : 'green-100'} px-3 py-2 rounded-t-lg">
                        <div class="font-bold text-lg">${shop.name}</div>
                        <div class="text-gray-600 text-xs">${shop.address}</div>
                    </div>
                    <div class="p-3">
                        <div class="flex items-center mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${shop.type === 'veterinary' ? 'red-100 text-red-800' : 'green-100 text-green-800'}">
                                ${shop.type.charAt(0).toUpperCase() + shop.type.slice(1)}
                            </span>
                            <span class="ml-2 text-xs text-gray-500">${distanceKm} km away</span>
                        </div>
                        <a href="/book/${shop.id}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white !text-white font-semibold text-sm rounded hover:bg-blue-700 transition-colors shadow-sm">
                            Book now
                        </a>
                    </div>
                </div>
            `;
            
            // Add marker to map
            const shopMarker = L.marker([shop.latitude, shop.longitude], { 
                icon: shopIcon,
                shopType: shop.type 
            })
            .addTo(modalMap)
            .bindPopup(popupContent);
            
            // Add click event to show shop details in the info panel
            if (typeof showShopDetails === 'function') {
                shopMarker.on('click', function() {
                    showShopDetails(shop, distanceKm);
                });
            }
            
            // Add to markers array
            markers.push(shopMarker);
            
            return shopMarker;
        }

        // Add this function to handle direct coordinate searches
        function geocodeAndSearchWithCoords(query, lat, lng) {
            console.log('Searching with coordinates:', lat, lng);
            
            // Show map and search for shops nearby
            showModal();
            
            // Give the map time to initialize
            setTimeout(() => {
                if (!modalMap) {
                    console.error('Map not initialized after showing modal');
                    alert('Error loading map. Please try again.');
                    return;
                }
                
                // Center map on geocoded location
                modalMap.setView([lat, lng], 13);
                clearMarkers();
                
                // Add marker for the search location
                const searchIcon = L.divIcon({
                    html: `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white"></div>`,
                    className: 'search-location-marker',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                const searchMarker = L.marker([lat, lng], { icon: searchIcon })
                    .addTo(modalMap)
                    .bindPopup(`<strong>Search Location:</strong><br>${query}`)
                    .openPopup();
                
                markers.push(searchMarker);
                
                // Search for shops near the geocoded location
                fetch(`/shops/search-location?latitude=${lat}&longitude=${lng}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch nearby shops');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Found nearby shops:', data);
                        
                        if (data.success && data.shops.length > 0) {
                            // Update visible shop count
                            const visibleShopsCount = document.getElementById('visible-shops-count');
                            if (visibleShopsCount) {
                                visibleShopsCount.textContent = data.shops.length;
                            }
                            
                            // Clear previous selection info
                            const selectedShopInfo = document.getElementById('selected-shop-info');
                            if (selectedShopInfo) {
                                selectedShopInfo.innerHTML = '<p class="text-gray-500 text-sm italic text-center">Select a shop marker to view details</p>';
                            }
                            
                            // Add shop markers to the map
                            const bounds = L.latLngBounds();
                            bounds.extend([lat, lng]); // Include search location in bounds
                            
                            data.shops.forEach(shop => {
                                const shopLat = parseFloat(shop.latitude);
                                const shopLng = parseFloat(shop.longitude);
                                
                                if (isNaN(shopLat) || isNaN(shopLng)) {
                                    console.warn(`Invalid coordinates for shop ${shop.id} - ${shop.name}`);
                                    return;
                                }
                                
                                // Add shop marker using addShopMarker function
                                addShopMarker(shop, lat, lng);
                                
                                // Extend bounds to include this shop
                                bounds.extend([shopLat, shopLng]);
                            });
                            
                            // Fit the map to show all markers with some padding
                            if (markers.length > 1) {
                                modalMap.fitBounds(bounds, {
                                    padding: [50, 50],
                                    maxZoom: 15
                                });
                            }
                        } else {
                            alert('No pet services found near this location');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching shops:', error);
                        alert('Error finding nearby shops. Please try again.');
                    });
            }, 500); // Wait for map to initialize
        }
    });
</script>
    

    
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #map-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }
    #modal-map {
        z-index: 10000;
        width: 100%;
        height: 400px;
        background-color: #f0f0f0;
    }
    
    /* Fix for button text color in Leaflet popups */
    .leaflet-popup-content .shop-popup a.text-white {
        color: white !important;
    }
    
    /* Ensure all buttons in popups have white text */
    .leaflet-popup-content a.bg-blue-600,
    .leaflet-popup-content a.bg-blue-700 {
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    console.log('Script starting...');
    
    // Get DOM elements
    locationButton

    // Check if elements exist
    console.log('Location button:', locationButton);
    console.log('Modal:', modal);
    console.log('Close button:', closeModal);

    if (!locationButton) {
        console.error('Location button not found!');
    }

    // Update the click event listener
    if (locationButton) {
        locationButton.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Location button clicked');
            
            if ("geolocation" in navigator) {
                console.log('Geolocation is available');
                
                // Test geolocation
                navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
                    console.log('Geolocation permission status:', result.state);
                });
        
        navigator.geolocation.getCurrentPosition(
                    // Success callback
                    (position) => {
                        console.log('Position obtained:', position);
                    const { latitude, longitude } = position.coords;
                    
                        // Show the modal
                    showModal();
                    
                        // Center map on user's location and search for nearby shops
                        if (modalMap) {
                    modalMap.setView([latitude, longitude], 13);
                    clearMarkers();
                    
                    // Add user's location marker
                            const userIcon = L.divIcon({
                                html: `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white"></div>`,
                                className: 'user-location-marker',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            });
                            
                            const userMarker = L.marker([latitude, longitude], { icon: userIcon })
                                .addTo(modalMap)
                                .bindPopup('<strong>Your Location</strong>')
                                .openPopup();
                            
                            markers.push(userMarker);
                            
                            // Fetch nearby shops
                            fetch(`/shops/search-location?latitude=${latitude}&longitude=${longitude}`)
                                .then(response => {
                    if (!response.ok) {
                                        throw new Error('Failed to fetch nearby shops');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Found nearby shops:', data);
                                    
                                    if (data.success && data.shops.length > 0) {
                                        // Update shop count
                                        const visibleShopsCount = document.getElementById('visible-shops-count');
                                        if (visibleShopsCount) {
                                            visibleShopsCount.textContent = data.shops.length;
                                        }
                                        
                                        // Add shop markers to the map
                                        data.shops.forEach(shop => {
                                            addShopMarker(shop, latitude, longitude);
                                        });
                                    } else {
                                        // No shops found
                                        alert('No pet services found nearby. Try a different location or increase search radius.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching shops:', error);
                                    alert('Error finding nearby shops. Please try again.');
                                });
                        } else {
                            alert('Map not initialized. Please try again.');
                        }
                    },
                    // Error callback
                    (error) => {
                console.error('Geolocation error:', error);
                        locationButton.classList.remove('animate-pulse');
                
                let errorMessage = 'Error getting your location. ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Please enable location access in your browser settings.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred.';
                }
                alert(errorMessage);
            },
                    // Options
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
            } else {
                console.error('Geolocation not available');
                alert('Geolocation is not supported by your browser');
            }
        });
    }
    
    // Geocode a text location and search for shops nearby
    async function geocodeAndSearch(query) {
        try {
            console.log('Geocoding:', query);
            
            // Use OpenStreetMap's Nominatim API to geocode the text query
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
            
            if (!response.ok) {
                throw new Error('Failed to geocode location');
            }
            
            const data = await response.json();
            
            if (data.length > 0) {
                const { lat, lon: lng } = data[0];
                console.log('Geocoded to:', lat, lng);
                
                // Show map and search for shops nearby
                showModal();
                
                if (!modalMap) {
                    throw new Error('Map not initialized');
                }
                
                // Center map on geocoded location
                modalMap.setView([lat, lng], 13);
                clearMarkers();
                
                // Add marker for the search location
                const searchIcon = L.divIcon({
                    html: `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white"></div>`,
                    className: 'search-location-marker',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                const searchMarker = L.marker([lat, lng], { icon: searchIcon })
                    .addTo(modalMap)
                    .bindPopup(`<strong>Search Location:</strong><br>${query}`)
                    .openPopup();
                
                markers.push(searchMarker);
                
                // Search for shops near the geocoded location
                const shopsResponse = await fetch(`/shops/search-location?latitude=${lat}&longitude=${lng}`);
                
                if (!shopsResponse.ok) {
                    throw new Error('Failed to fetch nearby shops');
                }
                
                const shopsData = await shopsResponse.json();
                console.log('Found nearby shops:', shopsData);
                
                if (shopsData.success && shopsData.shops.length > 0) {
                    // Add shop markers (using existing code pattern)
                    shopsData.shops.forEach(shop => {
                        const shopIcon = L.divIcon({
                            html: `<div class="w-6 h-6 ${shop.type === 'veterinary' ? 'bg-red-500' : 'bg-green-500'} rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white">
                                ${shop.type === 'veterinary' ? 'V' : 'G'}
                            </div>`,
                            className: 'shop-marker',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        });
                        
                        const distanceKm = Math.round(shop.distance * 10) / 10;
                        
                        const popupContent = `
                            <div class="shop-popup max-w-xs">
                                <div class="bg-${shop.type === 'veterinary' ? 'red-100' : 'green-100'} px-3 py-2 rounded-t-lg">
                                    <div class="font-bold text-lg">${shop.name}</div>
                                    <div class="text-gray-600 text-xs">${shop.address}</div>
                                </div>
                                <div class="p-3">
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${shop.type === 'veterinary' ? 'red-100 text-red-800' : 'green-100 text-green-800'}">
                                            ${shop.type.charAt(0).toUpperCase() + shop.type.slice(1)}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">${distanceKm} km away</span>
                                    </div>
                                    <a href="/book/${shop.id}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white !text-white font-semibold text-sm rounded hover:bg-blue-700 transition-colors shadow-sm">
                                        Book now
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        const shopMarker = L.marker([shop.latitude, shop.longitude], { icon: shopIcon })
                            .addTo(modalMap)
                            .bindPopup(popupContent);
                        
                        // Add click event to show shop details in the info panel
                        shopMarker.on('click', function() {
                            showShopDetails(shop, distanceKm);
                        });
                        
                        markers.push(shopMarker);
                    });
                } else {
                    alert('No pet services found near this location.');
                }
                
            } else {
                alert('Could not find location. Please try a different search term.');
            }
        } catch (error) {
            console.error('Error geocoding or searching:', error);
            alert('Error searching location. Please try again.');
        }
    }

    // Initialize modal map when showing
    function initModalMap() {
        try {
            console.log('Initializing map...');
            if (!modalMap) {
                if (!document.getElementById('modal-map')) {
                    console.error('Modal map element not found!');
                    return false;
                }
                
                modalMap = L.map('modal-map', {
                    center: [0, 0],
                    zoom: 13
                });
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(modalMap);
                
                console.log('Map initialized successfully');
            }
            return true;
        } catch (error) {
            console.error('Error initializing map:', error);
            return false;
        }
    }

    // Close modal handlers
    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
    });

    // Initialize debugging
    console.log('Map script loaded');
    console.log('Modal element:', modal);
    console.log('Close button:', closeModal);
    console.log('Get location button:', locationButton);
</script>
@endpush
