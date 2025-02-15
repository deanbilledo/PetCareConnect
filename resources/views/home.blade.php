@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gray-100 rounded-lg my-6 p-4 lg:p-8 relative overflow-hidden z-0">
        <div class="flex flex-col lg:flex-row justify-between items-center">
            <div class="z-10 mb-8 lg:mb-0 lg:w-1/2 text-left">
                <p class="text-gray-600 mb-2">No need to worry,</p>
                <h2 class="text-4xl font-bold mb-4">We Provide Grooming and Vet Checks</h2>
               
                <div class="relative">
                    <input 
                        type="text" 
                        id="location-search" 
                        placeholder="Pet Services Near You" 
                        class="w-full px-4 py-2 border rounded-full text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <svg class="h-5 w-5 text-gray-400 absolute right-3 top-2.5 cursor-pointer" 
                         id="getCurrentLocation"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div id="search-results" class="absolute w-full mt-1 bg-white rounded-lg shadow-lg hidden z-50"></div>
                </div>
            </div>
            <div class="w-full lg:w-1/2 relative">
                <img src="{{ asset('images/petdog.png') }}" alt="Happy dog" class="w-full h-auto object-cover rounded-lg">
            </div>
        </div>
    </section>

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

    <!-- Add this modal/popup HTML at the end of your content section -->
    <div id="map-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-4 w-11/12 max-w-4xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Nearby Pet Services</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modal-map" class="h-96 w-full rounded-lg border border-gray-300"></div>
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
    document.getElementById('chat-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const messagesContainer = document.getElementById('chat-messages');
        const message = input.value.trim();

        if (!message) return;

        // Add user message
        const userMessageEl = document.createElement('div');
        userMessageEl.innerHTML = `<div class="text-right"><div class="inline-block bg-blue-100 p-2 rounded-lg">${message}</div></div>`;
        messagesContainer.appendChild(userMessageEl);

        // Clear input
        input.value = '';

        try {
            const response = await axios.post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=AIzaSyDtxpM1REw73hZSbUQkOqL5_X-4Q86vC2I already destoyted the api ask dean for a new one the chat bot is temporarly disabled', {
                contents: [{
                    parts: [{ text: message }]
                }]
            });

            const aiResponse = response.data.candidates[0].content.parts[0].text;

            // Add AI message
            const aiMessageEl = document.createElement('div');
            aiMessageEl.innerHTML = `<div class="text-left"><div class="inline-block bg-gray-100 p-2 rounded-lg">${aiResponse}</div></div>`;
            messagesContainer.appendChild(aiMessageEl);

            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } catch (error) {
            console.error('Error:', error);
            const errorEl = document.createElement('div');
            errorEl.innerHTML = `<div class="text-red-500">Error processing request</div>`;
            messagesContainer.appendChild(errorEl);
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
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    console.log('Script starting...');
    
    // Get DOM elements
    const locationButton = document.getElementById('getCurrentLocation');
    const modal = document.getElementById('map-modal');
    const closeModal = document.getElementById('close-modal');

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
                        // ... rest of your success handling code
                    },
                    // Error callback
                    (error) => {
                        console.error('Geolocation error:', error);
                        alert(`Error getting location: ${error.message}`);
                    },
                    // Options
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                console.error('Geolocation not available');
                alert('Geolocation is not supported by your browser');
            }
        });
    }

    let modalMap;
    let markers = [];

    // Initialize modal map when showing
    function initModalMap() {
        try {
            console.log('Initializing map...');
            if (!modalMap) {
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
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            
            const mapInitialized = initModalMap();
            if (!mapInitialized) {
                throw new Error('Failed to initialize map');
            }

            // Force map to refresh after modal is visible
            setTimeout(() => {
                modalMap.invalidateSize();
                console.log('Map size updated');
            }, 250);
        } catch (error) {
            console.error('Error showing modal:', error);
            alert('Error loading map. Please try again.');
        }
    }

    // Get current location and search nearby
    getCurrentLocation.addEventListener('click', () => {
        console.log('Location button clicked');
        
        if (!("geolocation" in navigator)) {
            alert('Geolocation is not supported by your browser');
            return;
        }

        // Show loading state
        getCurrentLocation.classList.add('animate-pulse');
        
        navigator.geolocation.getCurrentPosition(
            async position => {
                try {
                    console.log('Got position:', position);
                    const { latitude, longitude } = position.coords;
                    
                    showModal();
                    
                    if (!modalMap) {
                        throw new Error('Map not initialized');
                    }

                    // Center map on user's location
                    modalMap.setView([latitude, longitude], 13);
                    clearMarkers();
                    
                    // Add user's location marker
                    addMarker(latitude, longitude, 'Your Location');
                    
                    // Search for nearby services
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?` + 
                        new URLSearchParams({
                            format: 'json',
                            q: 'veterinary OR pet grooming',
                            lat: latitude,
                            lon: longitude,
                            bounded: 1,
                            limit: 10
                        })
                    );

                    if (!response.ok) {
                        throw new Error('Failed to fetch nearby places');
                    }

                    const data = await response.json();
                    console.log('Found nearby places:', data);
                    
                    data.forEach(place => {
                        addMarker(place.lat, place.lon, place.display_name);
                    });

                } catch (error) {
                    console.error('Error in location processing:', error);
                    alert('Error finding nearby places. Please try again.');
                } finally {
                    getCurrentLocation.classList.remove('animate-pulse');
                }
            },
            error => {
                console.error('Geolocation error:', error);
                getCurrentLocation.classList.remove('animate-pulse');
                
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
    });

    // Clear existing markers
    function clearMarkers() {
        markers.forEach(marker => modalMap.removeLayer(marker));
        markers = [];
    }

    // Add a marker to the map
    function addMarker(lat, lon, title) {
        try {
            console.log('Adding marker:', { lat, lon, title });
            const marker = L.marker([lat, lon])
                .bindPopup(title)
                .addTo(modalMap);
            markers.push(marker);
        } catch (error) {
            console.error('Error adding marker:', error);
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
    console.log('Get location button:', getCurrentLocation);
</script>
@endpush
