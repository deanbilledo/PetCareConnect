/**
 * Shop Search Functionality
 * 
 * This script handles the shop/service search functionality on the home page.
 * It allows users to search for shops or services, display results, and toggle
 * between search results and regular content.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Apply custom styling to search dropdown
    const searchDropdown = document.getElementById('search-results');
    if (searchDropdown) {
        // Hide dropdown completely - we'll use the full search results instead
        searchDropdown.style.display = 'none';
    }
    
    // DOM Elements
    const searchInput = document.getElementById('location-search');
    const searchResults = document.getElementById('search-results');
    const searchResultsSection = document.getElementById('search-results-section');
    const searchResultsContainer = document.getElementById('search-results-container');
    const noResultsMessage = document.getElementById('no-results-message');
    const contentSections = document.getElementById('content-sections');
    const clearSearchBtn = document.getElementById('clear-search');
    const shopCardTemplate = document.getElementById('shop-card-template');
    const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
    const viewAllShopsBtn = document.getElementById('view-all-shops');
    
    // Current user location
    let userLatitude = null;
    let userLongitude = null;
    
    // Search delay timer
    let searchTimer = null;
    
    // Flag to track if a search is currently in progress
    let isSearching = false;
    
    /**
     * Initialize event listeners
     */
    function initEventListeners() {
        // Search input event - performs search as user types
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear any existing timer
            if (searchTimer) {
                clearTimeout(searchTimer);
            }
            
            // If query is empty, return to normal view
            if (query === '') {
                clearSearch();
                return;
            }
            
            // Set a timer to delay the search (debounce)
            searchTimer = setTimeout(() => {
                // Go directly to full search results
                performFullSearch(query);
            }, 500); // Increased to 500ms to avoid too many searches while typing
        });
        
        // Handle search submission (pressing Enter)
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query !== '') {
                    performFullSearch(query);
                }
            }
        });
        
        // Get current location button
        getCurrentLocationBtn.addEventListener('click', function() {
            getUserLocation();
        });
        
        // View all shops button
        viewAllShopsBtn.addEventListener('click', function() {
            fetchAllShops();
        });
        
        // Clear search button
        clearSearchBtn.addEventListener('click', function() {
            clearSearch();
        });
    }
    
    /**
     * Perform a full search and display results in the main content area
     * @param {string} query - The search query
     */
    function performFullSearch(query) {
        // If a search is already in progress, don't start another one
        if (isSearching) return;
        
        isSearching = true;
        
        // Show the search results section and hide regular content
        searchResultsSection.classList.remove('hidden');
        contentSections.classList.add('hidden');
        
        // Update the search term display
        document.getElementById('search-term').textContent = query || 'all pet services';
        
        // Clear previous results and show loading state
        searchResultsContainer.innerHTML = `
            <div class="flex items-center justify-center h-64 col-span-full">
                <div class="text-center text-gray-500">
                    <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p>Searching...</p>
                </div>
            </div>
        `;
        
        noResultsMessage.classList.add('hidden');
        
        // Prepare search parameters
        let searchParams = {
            query: query,
            include_services: true
        };
        
        // Add location if available
        if (userLatitude && userLongitude) {
            searchParams.latitude = userLatitude;
            searchParams.longitude = userLongitude;
        }
        
        // Make API call to search
        fetch('/api/shops/search?' + new URLSearchParams(searchParams))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.shops && data.shops.length > 0) {
                    displayFullSearchResults(data.shops);
                    // Initialize filtering
                    initializeFiltering(data.shops);
                } else {
                    showNoResultsMessage();
                }
                isSearching = false;
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResultsContainer.innerHTML = `
                    <div class="col-span-full p-8 bg-red-50 rounded-lg text-center">
                        <p class="text-red-500">There was an error performing your search. Please try again.</p>
                    </div>
                `;
                isSearching = false;
            });
    }
    
    /**
     * Initialize filtering buttons for search results
     * @param {Array} results - Search results containing shops and services
     */
    function initializeFiltering(results) {
        // Get filter buttons
        const filterAll = document.getElementById('filter-all');
        const filterVet = document.getElementById('filter-vet');
        const filterGroom = document.getElementById('filter-groom');
        
        if (!filterAll || !filterVet || !filterGroom) return;
        
        // Set initial counts
        const shops = results.filter(item => item.result_type === 'shop');
        const vetShops = shops.filter(shop => shop.type === 'veterinary');
        const groomShops = shops.filter(shop => shop.type === 'grooming');
        
        // Update button text with counts
        filterAll.textContent = `All (${results.length})`;
        filterVet.textContent = `Veterinary (${vetShops.length})`;
        filterGroom.textContent = `Grooming (${groomShops.length})`;
        
        // Reset all buttons to default state
        const resetButtons = () => {
            filterAll.classList.remove('bg-blue-500', 'text-white');
            filterAll.classList.add('bg-gray-200', 'text-gray-800');
            filterVet.classList.remove('bg-blue-500', 'text-white');
            filterVet.classList.add('bg-gray-200', 'text-gray-800');
            filterGroom.classList.remove('bg-blue-500', 'text-white');
            filterGroom.classList.add('bg-gray-200', 'text-gray-800');
        };
        
        // Click handlers for filters
        filterAll.addEventListener('click', function() {
            resetButtons();
            this.classList.remove('bg-gray-200', 'text-gray-800');
            this.classList.add('bg-blue-500', 'text-white');
            document.querySelectorAll('.shop-card').forEach(card => {
                card.classList.remove('hidden');
            });
        });
        
        filterVet.addEventListener('click', function() {
            resetButtons();
            this.classList.remove('bg-gray-200', 'text-gray-800');
            this.classList.add('bg-blue-500', 'text-white');
            document.querySelectorAll('.shop-card').forEach(card => {
                if (card.dataset.shopType === 'veterinary') {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
        
        filterGroom.addEventListener('click', function() {
            resetButtons();
            this.classList.remove('bg-gray-200', 'text-gray-800');
            this.classList.add('bg-blue-500', 'text-white');
            document.querySelectorAll('.shop-card').forEach(card => {
                if (card.dataset.shopType === 'grooming') {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    }
    
    /**
     * Display full search results in the main content area
     * @param {Array} results - Array of shop and service objects
     */
    function displayFullSearchResults(results) {
        // Clear loading state
        searchResultsContainer.innerHTML = '';
        
        if (results.length === 0) {
            showNoResultsMessage();
            return;
        }
        
        // Separate shops and services
        const shops = results.filter(item => item.result_type === 'shop');
        const services = results.filter(item => item.result_type === 'service');
        
        // Group services by shop for display
        const servicesByShop = {};
        services.forEach(service => {
            if (!servicesByShop[service.shop_id]) {
                servicesByShop[service.shop_id] = [];
            }
            servicesByShop[service.shop_id].push(service);
        });
        
        // First add shop cards for any shop that has matching services
        const processedShopIds = new Set();
        
        // Helper function to create a shop card
        const createShopCard = (shop, matchingServices = []) => {
            // Clone the template
            const shopCard = shopCardTemplate.content.cloneNode(true);
            
            // Set image
            const imgElement = shopCard.querySelector('.shop-image');
            const imagePath = shop.image ? (shop.image.startsWith('http') ? shop.image : '/storage/' + shop.image) : '/images/default-shop.png';
            imgElement.src = imagePath;
            imgElement.alt = shop.name;
            
            // Set shop type badge
            const typeElement = shopCard.querySelector('.shop-type');
            const shopType = shop.type ? shop.type.charAt(0).toUpperCase() + shop.type.slice(1) : '';
            typeElement.textContent = shopType;
            
            // Set shop name
            shopCard.querySelector('.shop-name').textContent = shop.name;
            
            // Set rating value
            const rating = parseFloat(shop.rating) || 0;
            shopCard.querySelector('.rating-value').textContent = `${rating.toFixed(1)}`;
            
            // Set address
            shopCard.querySelector('.shop-address').textContent = shop.address;
            
            // Customize description to highlight matching services if available
            const descElement = shopCard.querySelector('.shop-description');
            if (matchingServices && matchingServices.length > 0) {
                // Show matching services in description
                if (matchingServices.length === 1) {
                    descElement.innerHTML = `<span class="font-semibold text-white">Featured service:</span> ${matchingServices[0].name} (₱${parseFloat(matchingServices[0].base_price).toFixed(2)})`;
                } else {
                    descElement.innerHTML = `<span class="font-semibold text-white">Featured services:</span> ${matchingServices.slice(0, 2).map(s => s.name).join(', ')}${matchingServices.length > 2 ? '...' : ''}`;
                }
                
                // Show the featured tag
                const featuredTag = shopCard.querySelector('.featured-tag');
                if (featuredTag) {
                    featuredTag.classList.remove('hidden');
                }
            } else {
                descElement.textContent = shop.description || 'No description available';
            }
            
            // Set distance if available
            const distanceElement = shopCard.querySelector('.shop-distance');
            if (shop.distance !== undefined) {
                distanceElement.textContent = `${parseFloat(shop.distance).toFixed(1)} km away`;
            } else {
                distanceElement.textContent = '';
            }
            
            // Set link to shop
            const linkElement = shopCard.querySelector('.shop-link');
            if (matchingServices && matchingServices.length > 0) {
                linkElement.href = `/book/${shop.id}?service=${matchingServices[0].id}`;
                linkElement.textContent = 'View Service';
            } else {
                linkElement.href = `/book/${shop.id}`;
                linkElement.textContent = 'Book Now';
            }
            
            // Add data attributes for filtering
            const cardElement = shopCard.querySelector('.shop-card');
            cardElement.dataset.shopType = shop.type;
            cardElement.dataset.shopId = shop.id;
            
            // Add to results container
            searchResultsContainer.appendChild(shopCard);
            
            // Mark this shop as processed
            processedShopIds.add(shop.id);
        };
        
        // First add shops that have matching services
        Object.keys(servicesByShop).forEach(shopId => {
            const matchingServices = servicesByShop[shopId];
            
            // Try to find the shop in our shop results
            let shopData = shops.find(s => s.id == shopId);
            
            // If we don't have full shop data, use data from the first service
            if (!shopData && matchingServices.length > 0) {
                const firstService = matchingServices[0];
                shopData = {
                    id: firstService.shop_id,
                    name: firstService.shop_name,
                    type: firstService.shop_type,
                    address: firstService.shop_address,
                    image: firstService.shop_image,
                    latitude: firstService.latitude,
                    longitude: firstService.longitude,
                    distance: firstService.distance,
                    result_type: 'shop'
                };
            }
            
            if (shopData) {
                createShopCard(shopData, matchingServices);
            }
        });
        
        // Then add remaining shops
        shops.forEach(shop => {
            if (!processedShopIds.has(shop.id)) {
                createShopCard(shop);
            }
        });
    }
    
    /**
     * Show "no results" message
     */
    function showNoResultsMessage() {
        searchResultsContainer.innerHTML = '';
        noResultsMessage.classList.remove('hidden');
        
        // Add event listener to the alternative clear search button
        const clearSearchAlt = document.getElementById('clear-search-alt');
        if (clearSearchAlt) {
            clearSearchAlt.addEventListener('click', clearSearch);
        }
    }
    
    /**
     * Fetch and display all shops
     */
    function fetchAllShops() {
        // Update the search term display
        document.getElementById('search-term').textContent = 'all pet services';
        
        // Show the search results section and hide regular content
        searchResultsSection.classList.remove('hidden');
        contentSections.classList.add('hidden');
        
        // Clear previous results and show loading state
        searchResultsContainer.innerHTML = `
            <div class="flex items-center justify-center h-64 col-span-full">
                <div class="text-center text-gray-500">
                    <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p>Loading all pet services...</p>
                </div>
            </div>
        `;
        
        noResultsMessage.classList.add('hidden');
        
        // Prepare params - include user location if available
        let searchParams = {};
        if (userLatitude && userLongitude) {
            searchParams.latitude = userLatitude;
            searchParams.longitude = userLongitude;
        }
        
        // Make API call to get all shops
        fetch('/api/shops/all?' + new URLSearchParams(searchParams))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add result_type to each shop for consistent handling
                    const shopResults = data.shops.map(shop => {
                        shop.result_type = 'shop';
                        return shop;
                    });
                    
                    displayFullSearchResults(shopResults);
                    initializeFiltering(shopResults);
                    searchInput.value = 'All Pet Services';
                } else {
                    showNoResultsMessage();
                }
            })
            .catch(error => {
                console.error('Error fetching shops:', error);
                searchResultsContainer.innerHTML = `
                    <div class="col-span-full p-8 bg-red-50 rounded-lg text-center">
                        <p class="text-red-500">There was an error loading pet services. Please try again.</p>
                    </div>
                `;
            });
    }
    
    /**
     * Clear search and return to normal view
     */
    function clearSearch() {
        // Only proceed if we're actually in search mode
        if (!searchResultsSection.classList.contains('hidden')) {
            searchInput.value = '';
            searchResultsSection.classList.add('hidden');
            contentSections.classList.remove('hidden');
            isSearching = false;
        } else if (searchInput.value) {
            // Just clear the input if search results aren't showing
            searchInput.value = '';
        }
    }
    
    /**
     * Get user's current location
     */
    function getUserLocation() {
        if (navigator.geolocation) {
            // Show loading indicator in the search input
            searchInput.value = 'Getting your location...';
            searchInput.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                // Success
                function(position) {
                    userLatitude = position.coords.latitude;
                    userLongitude = position.coords.longitude;
                    
                    // Update search placeholder
                    searchInput.value = '';
                    searchInput.placeholder = 'Pet Services Near Your Location';
                    searchInput.disabled = false;
                    
                    // Perform search with location only
                    performFullSearch('');
                },
                // Error
                function(error) {
                    console.error('Error getting location:', error);
                    searchInput.value = '';
                    searchInput.disabled = false;
                    
                    // Show error message based on the error code
                    let errorMsg = 'Unable to retrieve your location.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = 'Location access denied. Please enable location services.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMsg = 'Location request timed out.';
                            break;
                    }
                    
                    alert(errorMsg);
                }
            );
        } else {
            alert('Geolocation is not supported by your browser');
        }
    }
    
    // Initialize the component
    initEventListeners();
});