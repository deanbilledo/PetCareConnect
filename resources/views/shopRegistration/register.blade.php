@php
use Illuminate\Support\Facades\Log;
@endphp

<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name', 'Pet Care Connect') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- OpenStreetMap CSS and JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
        
        <!-- Add this in the <head> section, after the other style imports -->
        <style>
            /* Progress container styles */
            .progress-container {
                @apply relative w-full max-w-xs mx-auto py-4;
            }

            /* Vertical line */
            .progress-line {
                @apply absolute left-1/2 top-0 bottom-0 w-0.5 bg-gray-200 -translate-x-1/2;
                z-index: 0;
            }

            /* Active progress line */
            .progress-line-active {
                @apply absolute left-1/2 top-0 w-0.5 bg-indigo-600 -translate-x-1/2 transition-all duration-500;
                z-index: 1;
            }

            /* Steps container */
            .steps-container {
                @apply relative flex flex-col space-y-8;
            }

            /* Step item */
            .step-item {
                @apply flex flex-col items-center relative z-10;
            }

            /* Step number */
            .step-number {
                @apply w-8 h-8 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center text-sm font-medium text-gray-500 mb-2;
                transition: all 0.3s ease;
            }

            /* Step label */
            .step-label {
                @apply text-sm text-gray-500 font-medium;
                transition: all 0.3s ease;
            }

            /* Active state */
            .step-item.active .step-number {
                @apply border-indigo-600 bg-indigo-600 text-white;
            }

            .step-item.active .step-label {
                @apply text-indigo-600 font-semibold;
            }

            /* Completed state */
            .step-item.completed .step-number {
                @apply border-indigo-600 bg-indigo-600 text-white;
            }

            .step-item.completed .step-label {
                @apply text-indigo-600;
            }
        </style>
    </head>
    <body class="bg-gray-100 font-[Poppins]">
        <div class="min-h-screen bg-custom-bg flex items-center justify-center p-4" 
             x-data="{ 
                 isStep4: false,
                 currentStep: 1,
                 progress: 0,
                 showMap: false,
                 map: null,
                 marker: null,
                 address: '',
                 latitude: '',
                 longitude: '',
                 addressSuggestions: [],
                 addressSearchTimeout: null,
                 addressLoading: false,
                 selectedAddressIndex: -1,
                 mapSearchQuery: '',
                 mapSearchResults: [],
                 mapSearchLoading: false,
                 formData: {
                     shop_name: '',
                     shop_type: '',
                     phone: '',
                     tin: '',
                     vat_status: '',
                     bir_certificate: '',
                     shop_image: null,
                     shop_image_preview: null
                 },
                 updateProgress() {
                     this.progress = ((this.currentStep - 1) / 3) * 100;
                 },
                 handleAddressInput(e) {
                     const query = e.target.value.trim();
                     
                     // Clear any existing timeout
                     if (this.addressSearchTimeout) {
                         clearTimeout(this.addressSearchTimeout);
                     }
                     
                     // Reset selected index
                     this.selectedAddressIndex = -1;
                     
                     // Don't search for short queries
                     if (query.length < 3) {
                         this.addressSuggestions = [];
                         return;
                     }
                     
                     this.addressLoading = true;
                     
                     // Debounce the search request
                     this.addressSearchTimeout = setTimeout(() => {
                         fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                             .then(response => response.json())
                             .then(data => {
                                 this.addressSuggestions = data;
                                 this.addressLoading = false;
                             })
                             .catch(error => {
                                 console.error('Error searching addresses:', error);
                                 this.addressLoading = false;
                             });
                     }, 500);
                 },
                 selectNextAddress() {
                     if (this.addressSuggestions.length === 0) return;
                     this.selectedAddressIndex = (this.selectedAddressIndex + 1) % this.addressSuggestions.length;
                 },
                 selectPrevAddress() {
                     if (this.addressSuggestions.length === 0) return;
                     this.selectedAddressIndex = (this.selectedAddressIndex - 1 + this.addressSuggestions.length) % this.addressSuggestions.length;
                 },
                 selectCurrentAddress() {
                     if (this.selectedAddressIndex >= 0 && this.addressSuggestions.length > 0) {
                         this.selectAddress(this.addressSuggestions[this.selectedAddressIndex]);
                     }
                 },
                 selectAddress(suggestion) {
                     console.log('selectAddress called with:', suggestion);
                     
                     // Make sure suggestion has the required properties
                     if (!suggestion || !suggestion.display_name || !suggestion.lat || !suggestion.lon) {
                         console.error('Invalid suggestion object:', suggestion);
                         return;
                     }
                     
                     this.address = suggestion.display_name;
                     this.latitude = suggestion.lat;
                     this.longitude = suggestion.lon;
                     this.addressSuggestions = [];
                     
                     console.log('Updated address to:', this.address);
                     console.log('Updated coordinates to:', this.latitude, this.longitude);
                     
                     // Update map if it's open
                     if (this.map && this.showMap) {
                         const latLng = [parseFloat(suggestion.lat), parseFloat(suggestion.lon)];
                         this.map.setView(latLng, 16);
                         
                         if (this.marker) {
                             this.marker.setLatLng(latLng);
                         } else {
                             this.marker = L.marker(latLng).addTo(this.map);
                         }
                     }
                 },
                 nextStep() {
                     // Validate current step
                     const errors = validateStep(this.currentStep, this.formData);
                     
                     if (errors.length > 0) {
                         showErrors(errors);
                         return; // Don't proceed if there are errors
                     }
                     
                     // Remove any existing error messages since validation passed
                     const existingErrors = document.querySelector('.validation-errors');
                     if (existingErrors) existingErrors.remove();
                     
                     // Proceed to next step
                     if (this.currentStep < 4) {
                         this.currentStep++;
                         this.updateProgress();
                     }
                 },
                 prevStep() {
                     if (this.currentStep > 1) {
                         this.currentStep--;
                         this.updateProgress();
                     }
                 },
                 initMap() {
                     this.$nextTick(() => {
                         if (!this.map) {
                             console.log('Initializing map...');
                             try {
                                 // Wait for the modal to be fully visible
                                 setTimeout(() => {
                                     this.map = L.map('map').setView([6.9214, 122.0790], 13);
                                     
                                     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                         attribution: '© OpenStreetMap contributors'
                                     }).addTo(this.map);

                                     // Add geocoder control
                                     const geocoder = L.Control.geocoder({
                                         defaultMarkGeocode: false
                                     })
                                     .on('markgeocode', (e) => {
                                         const { center, name } = e.geocode;
                                         if (this.marker) {
                                             this.marker.setLatLng(center);
                                         } else {
                                             this.marker = L.marker(center).addTo(this.map);
                                         }
                                         this.map.setView(center, 16);
                                         this.latitude = center.lat;
                                         this.longitude = center.lng;
                                         this.address = name;
                                     })
                                     .addTo(this.map);

                                     // Handle map clicks
                                     this.map.on('click', (e) => {
                                         const { lat, lng } = e.latlng;
                                         if (this.marker) {
                                             this.marker.setLatLng([lat, lng]);
                                         } else {
                                             this.marker = L.marker([lat, lng]).addTo(this.map);
                                         }
                                         this.latitude = lat;
                                         this.longitude = lng;
                                         
                                         // Reverse geocode the clicked location
                                         fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                                             .then(response => response.json())
                                             .then(data => {
                                                 this.address = data.display_name;
                                             });
                                     });

                                     // Force map to update its size
                                     this.map.invalidateSize();
                                 }, 100);
                             } catch (error) {
                                 console.error('Error initializing map:', error);
                             }
                         } else {
                             // If map already exists, just update its size
                             this.map.invalidateSize();
                         }
                     });
                 },
                 confirmLocation() {
                     console.log('Confirming location with address:', this.address);
                     
                     // Force update the address input field
                     setTimeout(() => {
                         if (document.getElementById('address')) {
                             document.getElementById('address').value = this.address;
                             document.getElementById('address').dispatchEvent(new Event('input'));
                             console.log('Address input updated to:', document.getElementById('address').value);
                         }
                     }, 10);
                     
                     this.showMap = false;
                 },
                 getCurrentLocation() {
                     if (!navigator.geolocation) {
                         alert('Geolocation is not supported by your browser');
                         return;
                     }
                     
                     // Show loading indicator
                     this.addressLoading = true;
                     this.addressSuggestions = [{ display_name: 'Locating...' }];
                     
                     navigator.geolocation.getCurrentPosition(
                         // Success callback
                         (position) => {
                             const { latitude, longitude } = position.coords;
                             this.latitude = latitude;
                             this.longitude = longitude;
                             
                             // Perform reverse geocoding to get address
                             fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                                 .then(response => response.json())
                                 .then(data => {
                                     this.address = data.display_name;
                                     this.addressSuggestions = [];
                                     this.addressLoading = false;
                                     
                                     // Update map if it's open
                                     if (this.map && this.showMap) {
                                         this.map.setView([latitude, longitude], 16);
                                         
                                         if (this.marker) {
                                             this.marker.setLatLng([latitude, longitude]);
                                         } else {
                                             this.marker = L.marker([latitude, longitude]).addTo(this.map);
                                         }
                                     }
                                 })
                                 .catch(error => {
                                     console.error('Error in reverse geocoding:', error);
                                     this.addressSuggestions = [];
                                     this.addressLoading = false;
                                     alert('Error getting address from coordinates. Please try again or enter manually.');
                                 });
                         },
                         // Error callback
                         (error) => {
                             this.addressSuggestions = [];
                             this.addressLoading = false;
                             
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
                             timeout: 10000,
                             maximumAge: 0
                         }
                     );
                 },
                 // New map search methods
                 handleMapSearch() {
                     // Clear previous results
                     this.mapSearchResults = [];
                     
                     // Don't search if query is too short
                     if (!this.mapSearchQuery || this.mapSearchQuery.length < 3) return;
                     
                     // Cancel previous timeout
                     if (this.mapSearchTimeout) clearTimeout(this.mapSearchTimeout);
                     
                     // Set new timeout for debounce
                     this.mapSearchTimeout = setTimeout(() => {
                         this.searchMap();
                     }, 500);
                 },
                 
                 async searchMap() {
                     if (!this.mapSearchQuery || this.mapSearchQuery.length < 3) return;
                     
                     this.mapSearchLoading = true;
                     
                     try {
                         const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.mapSearchQuery)}&limit=5`);
                         const data = await response.json();
                         this.mapSearchResults = data;
                     } catch (error) {
                         console.error('Error searching location:', error);
                     } finally {
                         this.mapSearchLoading = false;
                     }
                 },
                 
                 selectMapSearchResult(result) {
                     console.log('Map search result selected:', result);
                     this.mapSearchResults = [];
                     this.mapSearchQuery = result.display_name;
                     
                     const lat = parseFloat(result.lat);
                     const lng = parseFloat(result.lon);
                     
                     // Update the map view
                     this.map.setView([lat, lng], 16);
                     
                     // Update the marker
                     if (this.marker) {
                         this.marker.setLatLng([lat, lng]);
                     } else {
                         this.marker = L.marker([lat, lng]).addTo(this.map);
                     }
                     
                     // Update address and coordinates
                     this.latitude = lat;
                     this.longitude = lng;
                     this.address = result.display_name;
                     
                     // Force update the address input field
                     setTimeout(() => {
                         document.getElementById('address').value = result.display_name;
                         document.getElementById('address').dispatchEvent(new Event('input'));
                     }, 10);
                     
                     console.log('Updated address to:', this.address);
                 },
                 
                 getMapCurrentLocation() {
                     if (!navigator.geolocation) {
                         alert('Geolocation is not supported by your browser');
                         return;
                     }
                     
                     navigator.geolocation.getCurrentPosition(
                         (position) => {
                             console.log('Got current position:', position);
                             const { latitude, longitude } = position.coords;
                             
                             // Update the map view
                             this.map.setView([latitude, longitude], 16);
                             
                             // Update the marker
                             if (this.marker) {
                                 this.marker.setLatLng([latitude, longitude]);
                             } else {
                                 this.marker = L.marker([latitude, longitude]).addTo(this.map);
                             }
                             
                             // Update address and coordinates
                             this.latitude = latitude;
                             this.longitude = longitude;
                             
                             // Reverse geocode to get address
                             fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                                 .then(response => response.json())
                                 .then(data => {
                                     this.address = data.display_name;
                                     this.mapSearchQuery = data.display_name;
                                     
                                     // Force update the address field
                                     setTimeout(() => {
                                         if (document.getElementById('address')) {
                                             document.getElementById('address').value = data.display_name;
                                             document.getElementById('address').dispatchEvent(new Event('input'));
                                         }
                                     }, 10);
                                     
                                     console.log('Updated address to:', this.address);
                                 })
                                 .catch(error => {
                                     console.error('Error fetching address:', error);
                                     alert('Could not retrieve address from your location. The coordinates have been saved.');
                                 });
                         },
                         (error) => {
                             alert('Error getting location: ' + error.message);
                         }
                     );
                 }
             }"
             x-init="$watch('currentStep', value => { 
                 isStep4 = value === 4;
                 updateProgress();
             })">
            <!-- Add logo outside the white box -->
            <a href="{{ route('home') }}" class="absolute top-4 left-4">
                <img src="{{ asset('images/logo.png') }}" alt="Pet Care Connect Logo" class="w-32 sm:w-40">
            </a>

            <!-- Update the white container box classes -->
            <div class="w-full space-y-8 px-4 sm:px-8 py-8 sm:py-10 bg-white rounded-3xl shadow-xl transition-all duration-300"
                 :class="{ 'max-w-md': !isStep4, 'max-w-6xl': isStep4 }">
                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-1xl sm:text-3xl font-bold">Register Your Shop</h2>
                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Complete the steps below</p>
                </div>

                <!-- Wrap everything in a parent Alpine component -->
                <div class="w-full max-w-3xl mx-auto">
                    <!-- Progress Steps -->
                    <div class="mb-24">
                        <div class="flex justify-between relative">
                            <!-- Background Line -->
                            <div class="absolute top-4 left-0 right-0 h-[1px] bg-gray-200"></div>
                            
                            <!-- Active Line -->
                            <div class="absolute top-4 left-0 h-[1px] bg-indigo-600 transition-all duration-500"
                                 :style="`width: ${progress}%`"></div>
                            
                            <!-- Steps -->
                            <div class="relative flex justify-between w-full px-8">
                                <!-- Step 1 -->
                                <div class="relative flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full border transition-colors duration-300 flex items-center justify-center bg-white z-10 text-sm"
                                         :class="currentStep >= 1 ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span x-text="currentStep > 1 ? '✓' : '1'"></span>
                                    </div>
                                    <div class="absolute mt-16 text-xs font-medium text-center w-max"
                                         :class="currentStep >= 1 ? 'text-indigo-600' : 'text-gray-500'">
                                        Basic Info
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="relative flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full border transition-colors duration-300 flex items-center justify-center bg-white z-10 text-sm"
                                         :class="currentStep >= 2 ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span x-text="currentStep > 2 ? '✓' : '2'"></span>
                                    </div>
                                    <div class="absolute mt-16 text-xs font-medium text-center w-max"
                                         :class="currentStep >= 2 ? 'text-indigo-600' : 'text-gray-500'">
                                        Contact
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="relative flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full border transition-colors duration-300 flex items-center justify-center bg-white z-10 text-sm"
                                         :class="currentStep >= 3 ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span x-text="currentStep > 3 ? '✓' : '3'"></span>
                                    </div>
                                    <div class="absolute mt-16 text-xs font-medium text-center w-max"
                                         :class="currentStep >= 3 ? 'text-indigo-600' : 'text-gray-500'">
                                        Services
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div class="relative flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full border transition-colors duration-300 flex items-center justify-center bg-white z-10 text-sm"
                                         :class="currentStep >= 4 ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span x-text="currentStep > 4 ? '✓' : '4'"></span>
                                    </div>
                                    <div class="absolute mt-16 text-xs font-medium text-center w-max"
                                         :class="currentStep >= 4 ? 'text-indigo-600' : 'text-gray-500'">
                                        Review
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            @foreach ($errors->all() as $error)
                                <span class="block sm:inline">{{ $error }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form method="POST" 
                        action="{{ route('shop.register') }}" 
                        class="space-y-6" 
                        enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Step 1: Basic Information -->
                        <div x-show="currentStep === 1">
                            <div class="space-y-4">
                                <!-- Shop Image Upload -->
                                <div class="space-y-2">
                                    <label class="block text-sm text-gray-700">Shop Profile Image</label>
                                    <div class="flex items-center space-x-2">
                                        <!-- Image Preview -->
                                        <div class="relative w-24 h-24 rounded-lg overflow-hidden bg-gray-100">
                                            <template x-if="formData.shop_image_preview">
                                                <img :src="formData.shop_image_preview"
                                                     class="w-full h-full object-cover"
                                                     alt="Shop preview">
                                            </template>
                                            <template x-if="!formData.shop_image_preview">
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Upload Button -->
                                        <div class="flex-1">
                                            <input type="file" 
                                                id="shop_image"
                                                name="shop_image" 
                                                accept="image/*"
                                                @change="const file = $event.target.files[0];
                                                        if (file) {
                                                            formData.shop_image = file;
                                                            const reader = new FileReader();
                                                            reader.onload = (e) => {
                                                                formData.shop_image_preview = e.target.result;
                                                            };
                                                            reader.readAsDataURL(file);
                                                        }"
                                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-custom-blue file:text-white hover:file:bg-blue-600">
                                            <p class="mt-1 text-xs text-gray-500">
                                                Recommended: Square image, minimum 500x500 pixels (Max 2MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Existing fields -->
                                <div>
                                    <input type="text" 
                                        name="shop_name" 
                                        placeholder="Shop Name"
                                        x-model="formData.shop_name"
                                        class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                                </div>
                                <div>
                                    <select name="shop_type" 
                                            x-model="formData.shop_type"
                                            class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                                        <option value="">Select Shop Type</option>
                                        <option value="veterinary">Veterinary Clinic</option>
                                        <option value="grooming">Grooming Shop</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Contact Information -->
                        <div x-show="currentStep === 2">
                            <!-- Add hidden fields for coordinates -->
                            <input type="hidden" name="latitude" x-model="latitude">
                            <input type="hidden" name="longitude" x-model="longitude">
                            <input type="hidden" name="address" x-model="address">
                            
                            <div class="space-y-4">
                                <div>
                                    <input type="tel" 
                                        name="phone" 
                                        placeholder="Contact Number"
                                        x-model="formData.phone"
                                        class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                                </div>
                                <div class="relative">
                                    <div class="flex items-center">
                                        <input type="text" 
                                            name="address" 
                                            id="address"
                                            placeholder="Shop Address"
                                            x-model="address"
                                            @input="handleAddressInput"
                                            @keydown.arrow-down.prevent="selectNextAddress"
                                            @keydown.arrow-up.prevent="selectPrevAddress"
                                            @keydown.enter.prevent="selectCurrentAddress"
                                            @keydown.escape="addressSuggestions = []"
                                            @blur="setTimeout(() => { addressSuggestions = [] }, 200)"
                                            class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                                        <div class="absolute right-0 flex items-center">
                                        <button type="button" 
                                                    @click="getCurrentLocation"
                                                    class="p-2 text-gray-400 hover:text-gray-600 mr-1"
                                                    title="Use Current Location">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </button>
                                            <button type="button" 
                                                    @click="showMap = true"
                                                    class="p-2 text-gray-400 hover:text-gray-600 mr-2"
                                                    title="Open Map">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Address Suggestions Dropdown -->
                                    <div x-show="addressSuggestions.length > 0" 
                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md overflow-hidden border border-gray-300 max-h-60 overflow-y-auto">
                                        <template x-for="(suggestion, index) in addressSuggestions" :key="index">
                                            <div 
                                                class="p-2.5 cursor-pointer hover:bg-gray-100"
                                                :class="{ 'bg-gray-100': selectedAddressIndex === index }"
                                                @mouseenter="selectedAddressIndex = index"
                                                @mousedown.prevent="
                                                    console.log('Suggestion clicked:', suggestion);
                                                    $event.target.closest('form').querySelector('#address').value = suggestion.display_name;
                                                    address = suggestion.display_name;
                                                    latitude = suggestion.lat;
                                                    longitude = suggestion.lon;
                                                    
                                                    // Force update the input value directly
                                                    setTimeout(() => {
                                                        document.getElementById('address').value = suggestion.display_name;
                                                        document.getElementById('address').dispatchEvent(new Event('input'));
                                                    }, 10);
                                                    
                                                    addressSuggestions = [];
                                                    console.log('Updated address to:', address);
                                                "
                                            >
                                                <p class="text-sm font-medium" x-text="suggestion.display_name"></p>
                                            </div>
                                        </template>
                                        
                                        <!-- Loading Indicator -->
                                        <div x-show="addressLoading" class="p-2.5 text-center text-sm text-gray-500">
                                            <svg class="animate-spin h-5 w-5 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Searching...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Map Modal -->
                            <div x-show="showMap" 
                                class="fixed inset-0 z-50 overflow-y-auto" 
                                aria-labelledby="map-modal" 
                                role="dialog" 
                                aria-modal="true"
                                x-init="$watch('showMap', value => { if(value) { initMap() } })">
                                <!-- Backdrop -->
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40"></div>

                                <!-- Modal Panel -->
                                <div class="fixed inset-0 z-50 overflow-y-auto">
                                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                                            <div class="absolute right-0 top-0 hidden pt-4 pr-4 sm:block z-10">
                                                <button type="button" 
                                                        @click="showMap = false"
                                                        class="rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-custom-blue">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Header -->
                                            <div class="bg-custom-blue px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-medium leading-6 text-white">
                                                        Select Shop Location
                                                    </h3>
                                                </div>
                                            </div>

                                            <!-- Search Bar -->
                                            <div class="px-4 pt-4 pb-2 sm:px-6">
                                                <div class="relative">
                                                    <div class="flex items-center">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                            </svg>
                                                        </div>
                                                        <input 
                                                            type="text" 
                                                            id="map-search" 
                                                            placeholder="Search for a location..." 
                                                            x-model="mapSearchQuery" 
                                                            @input="handleMapSearch"
                                                            @keydown.enter.prevent="searchMap"
                                                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-custom-blue focus:border-custom-blue text-sm"
                                                        >
                                                        <div class="absolute inset-y-0 right-0 flex items-center">
                                                            <button 
                                                                type="button" 
                                                                @click="searchMap"
                                                                class="p-2 text-gray-400 hover:text-gray-600">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                </svg>
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                @click="getMapCurrentLocation"
                                                                class="p-2 text-gray-400 hover:text-gray-600 mr-1"
                                                                title="Use Current Location">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Map Search Results Dropdown -->
                                                    <div x-show="mapSearchResults.length > 0" 
                                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md overflow-hidden border border-gray-300 max-h-60 overflow-y-auto">
                                                        <template x-for="(result, index) in mapSearchResults" :key="index">
                                                            <div 
                                                                @click="selectMapSearchResult(result)"
                                                                class="p-2.5 cursor-pointer hover:bg-gray-100 border-b border-gray-100">
                                                                <div class="flex items-start">
                                                                    <svg class="h-5 w-5 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                    </svg>
                                                                    <p class="text-sm font-medium" x-text="result.display_name"></p>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        
                                                        <!-- Loading Indicator -->
                                                        <div x-show="mapSearchLoading" class="p-2.5 text-center text-sm text-gray-500">
                                                            <svg class="animate-spin h-5 w-5 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            <span>Searching...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">
                                                    Drag marker or click on map to adjust location precisely
                                                </p>
                                            </div>

                                            <!-- Map Container -->
                                            <div class="px-4 py-2 sm:px-6">
                                                <div id="map" class="h-[400px] w-full rounded-lg z-40 border border-gray-300 shadow-inner"></div>
                                            </div>

                                            <!-- Location Info -->
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200">
                                                <div class="mb-2">
                                                    <p class="text-sm font-medium text-gray-700">Selected Location:</p>
                                                    <p class="text-sm text-gray-600 truncate" x-text="address || 'No location selected'"></p>
                                                </div>
                                                <div class="flex items-center text-xs text-gray-500 space-x-4">
                                                    <div>
                                                        <span class="font-medium">Latitude:</span> <span x-text="latitude ? parseFloat(latitude).toFixed(6) : 'N/A'"></span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Longitude:</span> <span x-text="longitude ? parseFloat(longitude).toFixed(6) : 'N/A'"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="button"
                                                        @click="confirmLocation"
                                                        class="inline-flex w-full justify-center rounded-md bg-custom-blue px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 sm:ml-3 sm:w-auto">
                                                    Confirm Location
                                                </button>
                                                <button type="button"
                                                        @click="showMap = false"
                                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Services -->
                        <div x-show="currentStep === 3">
                            <div class="space-y-4">
                                <!-- Business Information -->
                                <div>
                                    <input type="text" 
                                        name="tin" 
                                        placeholder="Tax Identification Number (TIN)"
                                        x-model="formData.tin"
                                        class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-sm text-gray-700">Value Added Tax Registration Status</label>
                                    <div class="flex gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="vat_status" value="registered" class="form-radio text-custom-blue">
                                            <span class="ml-2">VAT Registered</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="vat_status" value="non_registered" class="form-radio text-custom-blue">
                                            <span class="ml-2">Non-VAT Registered</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm text-gray-700">BIR Certificate of Registration</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="file" 
                                            name="bir_certificate" 
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-custom-blue file:text-white hover:file:bg-blue-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review -->
                        <div x-show="currentStep === 4" class="min-h-[600px] w-full">
                            <div class="space-y-6">
                                <!-- Basic Information Review -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Shop Name</p>
                                            <p class="mt-2 text-lg" x-text="formData.shop_name"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Shop Type</p>
                                            <p class="mt-2 text-lg" x-text="formData.shop_type === 'veterinary' ? 'Veterinary Clinic' : 'Grooming Shop'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Shop Image</p>
                                            <template x-if="formData.shop_image_preview">
                                                <img :src="formData.shop_image_preview" 
                                                     class="mt-2 h-24 w-24 object-cover rounded-lg border border-gray-200"
                                                     alt="Shop preview">
                                            </template>
                                            <template x-if="!formData.shop_image_preview">
                                                <p class="mt-2 text-lg text-gray-400">No image uploaded</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Review -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Contact Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Phone Number</p>
                                            <p class="mt-2 text-lg" x-text="formData.phone"></p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-sm font-medium text-gray-500">Address</p>
                                            <p class="mt-2 text-lg" x-text="address"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Business Information Review -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Business Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">TIN</p>
                                            <p class="mt-2 text-lg" x-text="formData.tin"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">VAT Status</p>
                                            <p class="mt-2 text-lg" x-text="formData.vat_status === 'registered' ? 'VAT Registered' : 'Non-VAT Registered'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">BIR Certificate</p>
                                            <p class="mt-2 text-lg" x-text="formData.bir_certificate ? 'Uploaded' : 'Not uploaded'"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <label class="flex items-start">
                                        <input type="checkbox" 
                                               name="terms" 
                                               class="form-checkbox h-5 w-5 text-custom-blue mt-1">
                                        <span class="ml-3 text-base text-gray-600 flex-1">
                                               I confirm that all the information provided is accurate and I agree to the 
                                            <a href="{{ route('terms') }}" class="text-custom-blue hover:underline" target="_blank">Terms and Conditions</a>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between gap-4 mt-6">
                            <button type="button" 
                                    x-show="currentStep > 1"
                                    @click="prevStep()"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-blue">
                                Back
                            </button>
                            
                            <button type="button" 
                                    x-show="currentStep < 4"
                                    @click="nextStep()"
                                    class="ml-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-custom-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-blue">
                                Continue
                            </button>

                            <button type="submit" 
                                    x-show="currentStep === 4"
                                    class="ml-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-custom-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-blue">
                                Complete Registration
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help Text -->
                <p class="text-center text-xs sm:text-sm text-gray-600 mt-4">
                    Need help? 
                    <a href="#" class="text-custom-blue hover:underline">
                        Contact Support
                    </a>
                </p>
            </div>
        </div>

        <script>
            function getStepName(step) {
                const steps = {
                    1: 'Basic Info',
                    2: 'Contact',
                    3: 'Services',
                    4: 'Review'
                };
                return steps[step] || '';
            }

            document.addEventListener('DOMContentLoaded', function() {
                console.log('Leaflet loaded:', typeof L !== 'undefined');
            });

            // Add this validation function before the appointmentsData function
            function validateStep(step, formData) {
                const errors = [];
                
                switch(step) {
                    case 1:
                        if (!formData.shop_name.trim()) errors.push("Shop Name is required");
                        if (!formData.shop_type) errors.push("Shop Type is required");
                        break;
                        
                    case 2:
                        const phoneInput = document.querySelector('input[name="phone"]');
                        const addressInput = document.querySelector('input[name="address"]');
                        
                        if (!phoneInput.value.trim()) errors.push("Contact Number is required");
                        if (!addressInput.value.trim()) errors.push("Shop Address is required");
                        break;
                        
                    case 3:
                        const tinInput = document.querySelector('input[name="tin"]');
                        const vatStatus = document.querySelector('input[name="vat_status"]:checked');
                        const birCertificate = document.querySelector('input[name="bir_certificate"]');
                        
                        if (!tinInput.value.trim()) errors.push("TIN is required");
                        if (!vatStatus) errors.push("VAT Registration Status is required");
                        if (!birCertificate.files.length) errors.push("BIR Certificate is required");
                        break;
                }
                
                return errors;
            }

            function showErrors(errors) {
                // Remove any existing error messages
                const existingErrors = document.querySelector('.validation-errors');
                if (existingErrors) existingErrors.remove();
                
                // Create and show new error messages
                if (errors.length > 0) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-errors bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                    
                    const errorList = document.createElement('ul');
                    errorList.className = 'list-disc pl-4';
                    
                    errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                    
                    errorDiv.appendChild(errorList);
                    
                    // Insert error messages at the top of the form
                    const form = document.querySelector('form');
                    form.insertBefore(errorDiv, form.firstChild);
                }
            }
        </script>
    </body>
</html>    