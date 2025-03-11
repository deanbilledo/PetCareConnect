@extends(session('shop_mode') ? 'layouts.shop' : 'layouts.app')

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
        <a href="{{ url()->previous() }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back 
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" 
                           name="contact_email" 
                           value="{{ old('contact_email', $shop->contact_email) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 @error('contact_email') border-red-500 @enderror">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

    <!-- Photo Gallery Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Photo Gallery</h2>
                <!-- Add Photo Button -->
                <form action="{{ route('shop.gallery.upload') }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="inline">
                    @csrf
                    <label class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Photo
                        <input type="file" 
                               name="gallery_photo" 
                               accept="image/*" 
                               class="hidden"
                               onchange="this.form.submit()">
                    </label>
                </form>
            </div>
            
            <!-- Gallery Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($shop->gallery as $image)
                    @if($image->image_path)
                        <div class="relative group">
                            <img src="{{ Storage::disk('public')->exists($image->image_path) ? asset('storage/' . $image->image_path) : asset('images/default-shop.png') }}" 
                                 alt="Gallery Image" 
                                 class="w-full h-48 object-cover rounded-lg transition-transform duration-300 group-hover:scale-105"
                                 onerror="this.src='{{ asset('images/default-shop.png') }}'">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-opacity duration-300 rounded-lg"></div>
                            <!-- Delete Button -->
                            <button onclick="deleteGalleryPhoto({{ $image->id }})"
                                    class="absolute top-2 right-2 hidden group-hover:block p-1 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                @empty
                    <div class="col-span-3 text-center py-8">
                        <p class="text-gray-500">No gallery images available</p>
                    </div>
                @endforelse
            </div>

            @error('gallery_photo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Operating Hours Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8" 
         x-data="{ 
             isEditing: false,
             days: [
                 @foreach($shop->operatingHours->sortBy('day') as $hour)
                 {
                     name: '{{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$hour->day] }}',
                     day: {{ $hour->day }},
                     is_open: {{ $hour->is_open ? 'true' : 'false' }},
                     open_time: '{{ $hour->open_time }}',
                     close_time: '{{ $hour->close_time }}',
                     has_lunch_break: {{ $hour->has_lunch_break ? 'true' : 'false' }},
                     lunch_start: '{{ $hour->lunch_start }}',
                     lunch_end: '{{ $hour->lunch_end }}'
                 },
                 @endforeach
             ]
         }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Operating Hours</h2>
            <button type="button" 
                    @click="isEditing = !isEditing"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md"
                    :class="isEditing ? 'text-gray-600 hover:text-gray-800' : 'text-blue-600 hover:text-blue-800'">
                <span x-text="isEditing ? 'Cancel' : 'Edit Hours'"></span>
            </button>
        </div>

        <!-- Display Operating Hours -->
        <div x-show="!isEditing" class="space-y-4">
            <template x-for="day in days" :key="day.day">
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <span class="font-medium" x-text="day.name"></span>
                    <template x-if="day.is_open">
                        <div class="text-gray-600">
                            <span x-text="day.open_time"></span> - <span x-text="day.close_time"></span>
                            <template x-if="day.has_lunch_break">
                                <span class="ml-2 text-gray-500">
                                    (Lunch: <span x-text="day.lunch_start"></span> - <span x-text="day.lunch_end"></span>)
                                </span>
                            </template>
                        </div>
                    </template>
                    <template x-if="!day.is_open">
                        <span class="text-gray-500">Closed</span>
                    </template>
                </div>
            </template>
        </div>

        <!-- Edit Operating Hours -->
        <div x-show="isEditing" class="space-y-6">
            <template x-for="(day, index) in days" :key="index">
                <div class="flex flex-col space-y-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="font-medium" x-text="day.name"></span>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   x-model="day.is_open"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Open</span>
                        </label>
                    </div>

                    <div x-show="day.is_open" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                                <input type="time" 
                                       x-model="day.open_time"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                                <input type="time" 
                                       x-model="day.close_time"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <label class="inline-flex items-center mb-4">
                                <input type="checkbox" 
                                       x-model="day.has_lunch_break"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Include Lunch Break</span>
                            </label>

                            <div x-show="day.has_lunch_break" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lunch Break Start</label>
                                    <input type="time" 
                                           x-model="day.lunch_start"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="day.has_lunch_break">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lunch Break End</label>
                                    <input type="time" 
                                           x-model="day.lunch_end"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           :required="day.has_lunch_break">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Save Button -->
            <div class="flex justify-end pt-4">
                <button type="button"
                        @click="updateHours"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div id="galleryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>
        
        <!-- Modal Content -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="relative max-w-4xl w-full">
                <!-- Close Button -->
                <button onclick="closeGalleryModal()" 
                        class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Image Container -->
                <div class="relative">
                    <!-- Previous Button -->
                    <button onclick="changeImage(-1)" 
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <!-- Image -->
                    <img id="modalImage" 
                         src="" 
                         alt="Gallery Image" 
                         class="w-full h-auto max-h-[80vh] object-contain rounded-lg">

                    <!-- Next Button -->
                    <button onclick="changeImage(1)" 
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteGalleryPhoto(photoId) {
    if (!confirm('Are you sure you want to delete this photo?')) {
        return;
    }

    fetch(`{{ route('shop.gallery.delete', '') }}/${photoId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to delete photo. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete photo. Please try again.');
    });
}
</script>
@endsection 