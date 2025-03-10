@php
use Illuminate\Support\Str;
@endphp
@extends(session('shop_mode') ? 'layouts.shop' : 'layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="anonymous"/>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('home') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    <!-- Profile Header -->
    <div class="flex flex-col items-center mb-8">
        <div class="relative inline-block">
            <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('images/default-profile.png') }}" 
                 alt="Profile Photo" 
                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                 onerror="this.src='{{ asset('images/default-profile.png') }}'">

            <form action="{{ route('profile.update-photo') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  id="profile-photo-form"
                  class="absolute bottom-0 right-0">
                @csrf
                <label for="profile_photo" 
                       class="cursor-pointer bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 border border-gray-200 flex items-center justify-center w-8 h-8">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input type="file" 
                       id="profile_photo" 
                       name="profile_photo" 
                       class="hidden" 
                       accept="image/*"
                       onchange="this.form.submit()"> <!-- Add direct submit on change -->
            </form>
        </div>
        <h1 class="mt-4 text-2xl font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h1>
    </div>

    <!-- Add this right after the profile header section -->
    @if(auth()->user()->shop)
    <div class="bg-white rounded-lg shadow-md p-4 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-gray-700">Account Mode:</span>
                <div class="relative" x-data="{ mode: 'customer' }">
                    <button @click="mode = mode === 'customer' ? 'shop' : 'customer'" 
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                            :class="mode === 'shop' ? 'bg-teal-500' : 'bg-gray-200'"
                            @click="$nextTick(() => { 
                                if (mode === 'shop') {
                                    window.location.href = '{{ route('shop.profile') }}';
                                }
                            })">
                        <span class="sr-only">Toggle shop mode</span>
                        <span aria-hidden="true" 
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="mode === 'shop' ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                </div>
                <span class="text-sm text-gray-500" x-text="mode === 'shop' ? 'Shop Mode' : 'Customer Mode'"></span>
            </div>
            <a href="{{ route('shop.profile') }}" class="text-teal-500 hover:text-teal-600 text-sm">
                Manage Shop Profile
            </a>
        </div>
    </div>
    @endif

    <!-- Personal Info Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Personal Info</h2>
            <button type="submit" form="personal-info-form" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                Save Changes
            </button>
        </div>
        <form id="personal-info-form" action="{{ route('profile.update-info') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" value="{{ $user->first_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" value="{{ $user->last_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
            </div>
        </form>
    </div>

    <!-- Location Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8 relative" 
         x-data="{ 
            isEditing: false,
            map: null,
            marker: null,
            async initMap() {
                // Wait for the editing state to be true and the map container to be visible
                await this.$nextTick();
                
                // Add a small delay to ensure DOM is ready
                setTimeout(() => {
                    const mapContainer = document.getElementById('map');
                    if (!mapContainer || this.map) return;

                    try {
                        this.map = L.map('map').setView([8.1479, 123.8370], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(this.map);

                        // Force a map refresh
                            this.map.invalidateSize();

                        // Add click event to map
                        this.map.on('click', (e) => {
                            if (this.marker) {
                                this.map.removeLayer(this.marker);
                            }
                            this.marker = L.marker(e.latlng).addTo(this.map);
                            this.updateAddressFromLatLng(e.latlng.lat, e.latlng.lng);
                        });
                    } catch (error) {
                        console.error('Map initialization error:', error);
                    }
                }, 250); // Added delay of 250ms
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
                    
                    // Initialize map if it hasn't been initialized yet
                    if (!this.map) {
                        await this.initMap();
                    }
                    
                    if (this.marker) {
                        this.map.removeLayer(this.marker);
                    }
                    
                    this.map.setView([latitude, longitude], 16);
                    this.marker = L.marker([latitude, longitude]).addTo(this.map);
                    
                    await this.updateAddressFromLatLng(latitude, longitude);
                } catch (error) {
                    alert('Error getting location: ' + error.message);
                }
            },
            async updateAddressFromLatLng(lat, lng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
                    const data = await response.json();
                    document.getElementById('address-input').value = data.display_name;
                } catch (error) {
                    console.error('Error fetching address:', error);
                }
            }
         }"
         x-init="$watch('isEditing', value => { if (value) initMap() })">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Location</h2>
            <button type="button" 
                    @click="isEditing = !isEditing" 
                    class="text-teal-500 hover:text-teal-600">
                <span x-text="isEditing ? 'Cancel' : 'Edit'"></span>
            </button>
        </div>
        
        <!-- Location Display -->
        <div x-show="!isEditing" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="min-h-[24px]">
            <p class="text-gray-600">{{ $user->address ?? 'No address set' }}</p>
        </div>

        <!-- Location Edit Form -->
        <div x-show="isEditing"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-y-0"
             x-transition:enter-end="opacity-100 transform scale-y-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-y-100"
             x-transition:leave-end="opacity-0 transform scale-y-0">
            <form action="{{ route('profile.update-location') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" 
                                   id="address-input"
                                   name="address" 
                                   value="{{ $user->address }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                        <div class="flex items-end">
                            <button type="button" 
                                    @click="getCurrentLocation()"
                                    class="h-10 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="map" class="h-64 rounded-lg border border-gray-300"></div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="isEditing = false" 
                                class="text-teal-500 hover:text-teal-600">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Registered Pets Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8 relative" 
         x-data="{ 
            showAddForm: false,
            editingPet: null,
            toggleEdit(petId) {
                this.editingPet = this.editingPet === petId ? null : petId;
            }
         }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Registered Pets</h2>
            <div class="flex items-center space-x-4">
                <input type="text" placeholder="Filter" class="border rounded-md px-3 py-1">
                <button @click="showAddForm = !showAddForm" 
                        class="bg-teal-500 text-white p-2 rounded-full hover:bg-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Add Pet Form -->
        <div x-show="showAddForm" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-y-0"
             x-transition:enter-end="opacity-100 transform scale-y-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-y-100"
             x-transition:leave-end="opacity-0 transform scale-y-0"
             class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200"
             x-data="{ petType: '' }">
            
            @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('profile.pets.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pet Name</label>
                        <input type="text" name="name" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" required 
                                x-model="petType"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Type</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Exotic">Exotic</option>
                        </select>
                    </div>
                    <!-- Add species field for exotic pets -->
                    <div x-show="petType === 'Exotic'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Species</label>
                        <select name="species" 
                                x-bind:required="petType === 'Exotic'"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Species</option>
                            <optgroup label="Reptiles">
                                <option value="snake">Snake</option>
                                <option value="lizard">Lizard</option>
                                <option value="turtle">Turtle</option>
                                <option value="iguana">Iguana</option>
                                <option value="gecko">Gecko</option>
                                <option value="bearded_dragon">Bearded Dragon</option>
                            </optgroup>
                            <optgroup label="Small Mammals">
                                <option value="hamster">Hamster</option>
                                <option value="gerbil">Gerbil</option>
                                <option value="ferret">Ferret</option>
                                <option value="guinea_pig">Guinea Pig</option>
                                <option value="chinchilla">Chinchilla</option>
                                <option value="hedgehog">Hedgehog</option>
                                <option value="sugar_glider">Sugar Glider</option>
                            </optgroup>
                            <optgroup label="Birds">
                                <option value="parrot">Parrot</option>
                                <option value="cockatiel">Cockatiel</option>
                                <option value="macaw">Macaw</option>
                                <option value="parakeet">Parakeet</option>
                                <option value="lovebird">Lovebird</option>
                            </optgroup>
                            <optgroup label="Others">
                                <option value="tarantula">Tarantula</option>
                                <option value="scorpion">Scorpion</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Breed</label>
                        <input type="text" name="breed" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Size Category</label>
                        <select name="size_category" required 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Size</option>
                            <option value="Small">Small (0-15 kg)</option>
                            <option value="Medium">Medium (15-30 kg)</option>
                            <option value="Large">Large (30+ kg)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" 
                               name="weight" 
                               step="0.1" 
                               required 
                               min="0.1"
                               oninput="validateWeight(this)"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        <span class="text-red-500 text-xs hidden" id="weight-error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color/Markings</label>
                        <input type="text" name="color_markings" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                               placeholder="e.g., Brown with white chest">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coat Type</label>
                        <select name="coat_type" required 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Coat Type</option>
                            <option value="Short">Short</option>
                            <option value="Medium">Medium</option>
                            <option value="Long">Long</option>
                            <option value="Curly">Curly</option>
                            <option value="Double">Double-coated</option>
                            <option value="Hairless">Hairless</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                               max="{{ date('Y-m-d') }}"
                               x-data
                               x-init="$el.max = new Date().toISOString().split('T')[0]">
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            @click="showAddForm = false" 
                            class="text-teal-500 hover:text-teal-600">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600">
                        Add Pet
                    </button>
                </div>
            </form>
        </div>

        <!-- Pets Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="pb-4 w-[15%]">Name</th>
                        <th class="pb-4 w-[10%]">Type</th>
                        <th class="pb-4 w-[15%]">Breed</th>
                        <th class="pb-4 w-[10%]">Weight</th>
                        <th class="pb-4 w-[10%]">Size</th>
                        <th class="pb-4 w-[40%]">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pets as $pet)
                        <tr class="border-t" :class="{ 'bg-gray-50': editingPet === {{ $pet->id }} }">
                            <td class="py-4">{{ $pet->name }}</td>
                            <td class="py-4">{{ $pet->type }}</td>
                            <td class="py-4">{{ $pet->breed }}</td>
                            <td class="py-4">{{ $pet->weight }} kg</td>
                            <td class="py-4">{{ $pet->size_category }}</td>
                            <td class="py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Primary Actions Group -->
                                    <div class="flex items-center gap-1">
                                        <button @click="toggleEdit({{ $pet->id }})" 
                                                class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>

                                        <form id="delete-pet-form-{{ $pet->id }}" 
                                              action="{{ route('profile.pets.delete', $pet) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    @click="$dispatch('open-delete-modal', { id: {{ $pet->id }}, name: '{{ $pet->name }}' })"
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Divider -->
                                    <div class="h-4 w-px bg-gray-300"></div>

                                    <!-- Secondary Actions Group -->
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('profile.pets.details', ['pet' => $pet->id]) }}" 
                                           class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Details
                                        </a>

                                        <a href="{{ route('profile.pets.health-record', ['pet' => $pet->id]) }}" 
                                           class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Health
                                        </a>

                                        <a href="{{ route('profile.pets.user-add-health-record', ['pet' => $pet->id]) }}" 
                                           class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Add Record
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Form Row -->
                        <tr x-show="editingPet === {{ $pet->id }}"
                            x-cloak
                            class="border-t">
                            <td colspan="6" class="py-4">
                                <form action="{{ route('profile.pets.update', $pet) }}" method="POST" class="p-4 bg-gray-50 rounded-lg"
                                      x-data="{ petType: '{{ $pet->type }}' }">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Pet Name</label>
                                            <input type="text" name="name" value="{{ $pet->name }}" required 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                            <select name="type" required 
                                                    x-model="petType"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                                <option value="Dog" {{ $pet->type == 'Dog' ? 'selected' : '' }}>Dog</option>
                                                <option value="Cat" {{ $pet->type == 'Cat' ? 'selected' : '' }}>Cat</option>
                                                <option value="Bird" {{ $pet->type == 'Bird' ? 'selected' : '' }}>Bird</option>
                                                <option value="Exotic" {{ $pet->type == 'Exotic' ? 'selected' : '' }}>Exotic</option>
                                            </select>
                                        </div>
                                        <!-- Add species field for exotic pets -->
                                        <div x-show="petType === 'Exotic'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Species</label>
                                            <select name="species" 
                                                    x-bind:required="petType === 'Exotic'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                                <option value="">Select Species</option>
                                                <optgroup label="Reptiles">
                                                    <option value="snake" {{ $pet->species == 'snake' ? 'selected' : '' }}>Snake</option>
                                                    <option value="lizard" {{ $pet->species == 'lizard' ? 'selected' : '' }}>Lizard</option>
                                                    <option value="turtle" {{ $pet->species == 'turtle' ? 'selected' : '' }}>Turtle</option>
                                                    <option value="iguana" {{ $pet->species == 'iguana' ? 'selected' : '' }}>Iguana</option>
                                                    <option value="gecko" {{ $pet->species == 'gecko' ? 'selected' : '' }}>Gecko</option>
                                                    <option value="bearded_dragon" {{ $pet->species == 'bearded_dragon' ? 'selected' : '' }}>Bearded Dragon</option>
                                                </optgroup>
                                                <optgroup label="Small Mammals">
                                                    <option value="hamster" {{ $pet->species == 'hamster' ? 'selected' : '' }}>Hamster</option>
                                                    <option value="gerbil" {{ $pet->species == 'gerbil' ? 'selected' : '' }}>Gerbil</option>
                                                    <option value="ferret" {{ $pet->species == 'ferret' ? 'selected' : '' }}>Ferret</option>
                                                    <option value="guinea_pig" {{ $pet->species == 'guinea_pig' ? 'selected' : '' }}>Guinea Pig</option>
                                                    <option value="chinchilla" {{ $pet->species == 'chinchilla' ? 'selected' : '' }}>Chinchilla</option>
                                                    <option value="hedgehog" {{ $pet->species == 'hedgehog' ? 'selected' : '' }}>Hedgehog</option>
                                                    <option value="sugar_glider" {{ $pet->species == 'sugar_glider' ? 'selected' : '' }}>Sugar Glider</option>
                                                </optgroup>
                                                <optgroup label="Birds">
                                                    <option value="parrot" {{ $pet->species == 'parrot' ? 'selected' : '' }}>Parrot</option>
                                                    <option value="cockatiel" {{ $pet->species == 'cockatiel' ? 'selected' : '' }}>Cockatiel</option>
                                                    <option value="macaw" {{ $pet->species == 'macaw' ? 'selected' : '' }}>Macaw</option>
                                                    <option value="parakeet" {{ $pet->species == 'parakeet' ? 'selected' : '' }}>Parakeet</option>
                                                    <option value="lovebird" {{ $pet->species == 'lovebird' ? 'selected' : '' }}>Lovebird</option>
                                                </optgroup>
                                                <optgroup label="Others">
                                                    <option value="tarantula" {{ $pet->species == 'tarantula' ? 'selected' : '' }}>Tarantula</option>
                                                    <option value="scorpion" {{ $pet->species == 'scorpion' ? 'selected' : '' }}>Scorpion</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Breed</label>
                                            <input type="text" name="breed" value="{{ $pet->breed }}" required 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Size Category</label>
                                            <select name="size_category" required 
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                                <option value="Small" {{ $pet->size_category == 'Small' ? 'selected' : '' }}>Small (0-15 kg)</option>
                                                <option value="Medium" {{ $pet->size_category == 'Medium' ? 'selected' : '' }}>Medium (15-30 kg)</option>
                                                <option value="Large" {{ $pet->size_category == 'Large' ? 'selected' : '' }}>Large (30+ kg)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                            <input type="number" name="weight" value="{{ $pet->weight }}" step="0.1" required 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Color/Markings</label>
                                            <input type="text" name="color_markings" value="{{ $pet->color_markings }}" required 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Coat Type</label>
                                            <select name="coat_type" required 
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                                <option value="Short" {{ $pet->coat_type == 'Short' ? 'selected' : '' }}>Short</option>
                                                <option value="Medium" {{ $pet->coat_type == 'Medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="Long" {{ $pet->coat_type == 'Long' ? 'selected' : '' }}>Long</option>
                                                <option value="Curly" {{ $pet->coat_type == 'Curly' ? 'selected' : '' }}>Curly</option>
                                                <option value="Double" {{ $pet->coat_type == 'Double' ? 'selected' : '' }}>Double-coated</option>
                                                <option value="Hairless" {{ $pet->coat_type == 'Hairless' ? 'selected' : '' }}>Hairless</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                            <input type="date" name="date_of_birth" value="{{ $pet->date_of_birth?->format('Y-m-d') }}" required 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                                   max="{{ date('Y-m-d') }}"
                                                   x-data
                                                   x-init="$el.max = new Date().toISOString().split('T')[0]">
                                        </div>
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" 
                                                @click="editingPet = null" 
                                                class="text-gray-600 hover:text-gray-800">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">
                                No pets registered yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Transactions Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Recent Transactions</h2>
            <input type="text" placeholder="Filter" class="border rounded-md px-3 py-1">
        </div>
        <div class="space-y-4">
            @forelse($recentTransactions as $transaction)
                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ $transaction['service'] }}</span>
                        <span class="text-sm text-gray-500">{{ $transaction['shop_name'] }}</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="font-medium text-green-600">₱{{ number_format($transaction['amount'], 2) }}</span>
                        <span class="text-sm text-gray-500">{{ $transaction['date'] }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    No transactions yet
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Visits Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Recent Visits</h2>
            <input type="text" placeholder="Filter" class="border rounded-md px-3 py-1">
        </div>
        <div class="space-y-4">
            @forelse($recentVisits as $visit)
                <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <img src="{{ $visit['image'] }}" 
                         alt="{{ $visit['name'] }}" 
                         class="w-16 h-16 object-cover rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold">{{ $visit['name'] }}</h3>
                        <div class="flex items-center text-yellow-400">
                            <span class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($visit['rating']))
                                        <span>★</span>
                                    @else
                                        <span class="text-gray-300">★</span>
                                    @endif
                                @endfor
                            </span>
                            <span class="ml-1 text-gray-600">{{ $visit['rating'] }}</span>
                        </div>
                        <p class="text-gray-600 text-sm">{{ $visit['address'] }}</p>
                        <p class="text-gray-500 text-xs mt-1">Last visit: {{ $visit['last_visit'] }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    No visits yet
                </div>
            @endforelse
        </div>
    </div>

    <!-- Delete Pet Modal -->
    <div x-data="deleteModal()"
         @open-delete-modal.window="open($event.detail)"
         x-show="isOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <!-- Modal Backdrop -->
         <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
         
         <!-- Modal Content -->
         <div class="flex items-center justify-center min-h-screen p-4">
             <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto"
                  @click.away="isOpen = false"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 transform scale-95"
                  x-transition:enter-end="opacity-100 transform scale-100"
                  x-transition:leave="transition ease-in duration-200"
                  x-transition:leave-start="opacity-100 transform scale-100"
                  x-transition:leave-end="opacity-0 transform scale-95">
                 
                 <!-- Modal Header -->
                 <div class="bg-red-50 rounded-t-lg px-6 py-4 flex items-center justify-between">
                     <h3 class="text-lg font-medium text-red-900">Delete Pet</h3>
                     <button @click="isOpen = false" class="text-red-900 hover:text-red-700">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                         </svg>
                     </button>
                 </div>
                 
                 <!-- Modal Body -->
                 <div class="px-6 py-4">
                     <div class="flex items-center space-x-4 mb-4">
                         <div class="bg-red-100 rounded-full p-2">
                             <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                       d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                             </svg>
                         </div>
                         <div>
                             <h4 class="text-lg font-medium text-gray-900">Confirm Deletion</h4>
                             <p class="text-sm text-gray-500">Are you sure you want to delete <span x-text="petName" class="font-medium"></span>? This action cannot be undone.</p>
                         </div>
                     </div>
                 </div>
                 
                 <!-- Modal Footer -->
                 <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                     <button @click="isOpen = false"
                             class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                         Cancel
                     </button>
                     <button @click="deletePet()"
                             class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                         Delete Pet
                     </button>
                 </div>
             </div>
         </div>
     </div>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePhotoInput = document.getElementById('profile_photo');
    const form = document.getElementById('profile-photo-form');
    const label = form.querySelector('label');

    profilePhotoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Show loading state
            const originalContent = label.innerHTML;
            label.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;

            // Submit form
            form.submit();
        }
    });
});

function validateWeight(input) {
    const weightError = input.nextElementSibling;
    const submitButton = input.closest('form').querySelector('button[type="submit"]');
    
    if (input.value <= 0) {
        weightError.textContent = 'Weight must be greater than 0 kg';
        weightError.classList.remove('hidden');
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        return false;
    }
    
    if (input.value > 100) {
        weightError.textContent = 'Please enter a valid weight (less than 100 kg)';
        weightError.classList.remove('hidden');
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        return false;
    }
    
    weightError.classList.add('hidden');
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    return true;
}

// Add form validation
document.querySelectorAll('form').forEach(form => {
    if (form.querySelector('input[name="weight"]')) {
        form.addEventListener('submit', function(e) {
            const weightInput = this.querySelector('input[name="weight"]');
            if (!validateWeight(weightInput)) {
                e.preventDefault();
                return false;
            }
        });
    }
});

function deleteModal() {
    return {
        isOpen: false,
        petId: null,
        petName: '',
        open(data) {
            this.petId = data.id;
            this.petName = data.name;
            this.isOpen = true;
        },
        deletePet() {
            document.getElementById(`delete-pet-form-${this.petId}`).submit();
        }
    }
}
<<<<<<< HEAD
=======

document.addEventListener('DOMContentLoaded', function() {
    // Initialize TomSelect for species select
    const speciesSelect = document.querySelector('select[name="species"]');
    if (speciesSelect) {
        new TomSelect(speciesSelect, {
            plugins: ['remove_button'],
            maxItems: 1,
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            render: {
                item: function(data, escape) {
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    return '<div class="d-flex flex-column">' +
                           '<span class="font-weight-bold">' + escape(data.text) + '</span>' +
                           '</div>';
                }
            }
        });
    }

    // Handle pet type change
    const petTypeSelect = document.querySelector('select[name="type"]');
    if (petTypeSelect) {
        petTypeSelect.addEventListener('change', function() {
            const speciesField = document.querySelector('div[x-show="petType === \'Exotic\'"]');
            if (speciesField) {
                if (this.value === 'Exotic') {
                    speciesField.style.display = 'block';
                    speciesSelect.tomselect.enable();
                } else {
                    speciesField.style.display = 'none';
                    speciesSelect.tomselect.clear();
                    speciesSelect.tomselect.disable();
                }
            }
        });
    }
});
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
</script>
@endpush