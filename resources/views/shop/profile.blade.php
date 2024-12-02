@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="anonymous"/>
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('profile.index') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Profile
        </a>
    </div>

    <!-- Shop Profile Header -->
    <div class="flex flex-col items-center mb-8">
        <div class="relative inline-block">
            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                 alt="{{ $shop->name }}" 
                 class="w-32 h-32 rounded-full object-cover"
                 onerror="this.src='{{ asset('images/default-shop.png') }}'">
            <form action="{{ route('shop.profile.update-image') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="absolute bottom-0 right-0">
                @csrf
                <label for="shop_image" 
                       class="cursor-pointer bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 border border-gray-200 flex items-center justify-center w-8 h-8">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input type="file" 
                       id="shop_image" 
                       name="shop_image" 
                       class="hidden" 
                       onchange="this.form.submit()">
            </form>
        </div>
        <h1 class="text-2xl font-bold mt-6">{{ $shop->name }}</h1>
        <p class="text-gray-600">{{ ucfirst($shop->type) }} Shop</p>
        <div class="flex items-center mt-2">
            <div class="flex text-yellow-400">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $shop->rating)
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <span class="ml-2 text-gray-600">({{ $shop->ratings_count }} reviews)</span>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Shop Information Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Shop Information</h2>
            <button type="submit" form="shop-info-form" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                Save Changes
            </button>
        </div>
        
        <form id="shop-info-form" action="{{ route('shop.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Shop Name</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $shop->name) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" 
                           name="phone" 
                           value="{{ old('phone', $shop->phone) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              rows="4" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('description', $shop->description) }}</textarea>
                </div>
            </div>
        </form>
    </div>

    <!-- Location Section -->
    <div class="bg-white rounded-lg shadow-md p-6" 
         x-data="{ 
            isEditing: false,
            map: null,
            marker: null,
            initMap() {
                if (this.map) {
                    this.map.remove(); // Clean up existing map
                    this.map = null;
                    this.marker = null;
                }
                
                this.$nextTick(() => {
                    const mapContainer = document.getElementById('map');
                    if (!mapContainer) return;

                    try {
                        this.map = L.map('map').setView([{{ $shop->latitude }}, {{ $shop->longitude }}], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(this.map);

                        this.marker = L.marker([{{ $shop->latitude }}, {{ $shop->longitude }}], {
                            draggable: true
                        }).addTo(this.map);

                        this.map.invalidateSize();

                        // Add click event to map
                        this.map.on('click', (e) => {
                            this.marker.setLatLng(e.latlng);
                            this.updateLocationFields(e.latlng);
                        });

                        // Add drag event to marker
                        this.marker.on('dragend', (e) => {
                            this.updateLocationFields(e.target.getLatLng());
                        });
                    } catch (error) {
                        console.error('Map initialization error:', error);
                    }
                });
            },
            cleanup() {
                if (this.map) {
                    this.map.remove();
                    this.map = null;
                    this.marker = null;
                }
            },
            updateLocationFields(latlng) {
                document.getElementById('latitude').value = latlng.lat;
                document.getElementById('longitude').value = latlng.lng;
                this.updateAddressFromLatLng(latlng.lat, latlng.lng);
            },
            async updateAddressFromLatLng(lat, lng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
                    const data = await response.json();
                    document.getElementById('address-input').value = data.display_name;
                } catch (error) {
                    console.error('Error fetching address:', error);
                }
            },
            async getCurrentLocation() {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by your browser');
                    return;
                }

                try {
                    const position = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject);
                    });

                    const { latitude, longitude } = position.coords;
                    
                    this.map.setView([latitude, longitude], 16);
                    this.marker.setLatLng([latitude, longitude]);
                    
                    this.updateLocationFields({ lat: latitude, lng: longitude });
                } catch (error) {
                    alert('Error getting location: ' + error.message);
                }
            }
         }"
         x-init="$watch('isEditing', value => { 
             if (value) { 
                 initMap(); 
             } else { 
                 cleanup(); 
             } 
         })">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Location</h2>
            <button type="button" 
                    @click="isEditing = !isEditing" 
                    class="text-teal-500 hover:text-teal-600">
                <span x-text="isEditing ? 'Cancel' : 'Edit'"></span>
            </button>
        </div>

        <!-- Location Display -->
        <div x-show="!isEditing">
            <p class="text-gray-600">{{ $shop->address }}</p>
        </div>

        <!-- Location Edit Form -->
        <div x-show="isEditing" x-cloak>
            <form action="{{ route('shop.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="flex gap-2">
                        <input type="text" 
                               name="address" 
                               id="address-input"
                               value="{{ old('address', $shop->address) }}" 
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        <button type="button" 
                                @click="getCurrentLocation()"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $shop->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $shop->longitude) }}">

                    <div id="map" class="h-64 rounded-lg border border-gray-300"></div>

                    <div class="flex justify-end space-x-3">
                        <button type="submit" 
                                class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 