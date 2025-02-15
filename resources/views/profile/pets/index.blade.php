@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6 mt-16">
        <h1 class="text-3xl font-bold">My Pets</h1>
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Filter" class="border rounded-md px-3 py-1">
            <button onclick="openAddPetModal()" 
                    class="bg-teal-500 text-white p-2 rounded-full hover:bg-teal-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Pet Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($pets as $pet)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative h-48">
                    <img src="{{ $pet->profile_photo_url }}" 
                         alt="{{ $pet->name }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                        <h3 class="text-white text-xl font-bold">{{ $pet->name }}</h3>
                        <p class="text-gray-200">{{ $pet->breed }}</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Type</p>
                            <p class="font-medium">{{ $pet->type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Age</p>
                            <p class="font-medium">{{ $pet->date_of_birth->age }} years</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Weight</p>
                            <p class="font-medium">{{ $pet->weight }} kg</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Size</p>
                            <p class="font-medium">{{ Str::title($pet->size_category) }}</p>
                        </div>
                    </div>

                    <!-- Health Status Indicators -->
                    <div class="space-y-2 mb-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Vaccination Status</span>
                                <span class="text-sm font-medium {{ $pet->vaccination_percentage >= 70 ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $pet->vaccination_percentage }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" 
                                     style="width: {{ $pet->vaccination_percentage }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Health Score</span>
                                <span class="text-sm font-medium {{ $pet->health_score >= 70 ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $pet->health_score }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" 
                                     style="width: {{ $pet->health_score }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('profile.pets.details', $pet) }}" 
                           class="flex-1 bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600 text-center">
                            View Details
                        </a>
                        <a href="{{ route('profile.pets.health-record', $pet) }}" 
                           class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-center">
                            Health Records
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12 bg-white rounded-lg shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pets yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first pet.</p>
                    <div class="mt-6">
                        <button onclick="openAddPetModal()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-500 hover:bg-teal-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Pet
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

    <!-- Add Pet Modal -->
    <div id="add-pet-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" x-data>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Add New Pet</h3>
                        <button onclick="closeAddPetModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('profile.pets.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                                <input type="text" id="name" name="name" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Pet Type</label>
                                <select id="type" name="type" required onchange="toggleSpeciesField()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="">Select Type</option>
                                    <option value="Dog">Dog</option>
                                    <option value="Cat">Cat</option>
                                    <option value="Bird">Bird</option>
                                    <option value="Exotic">Exotic</option>
                                </select>
                            </div>

                            <div id="species-field" class="hidden">
                                <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                                <input type="text" id="species" name="species"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                                <input type="text" id="breed" name="breed" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label for="size_category" class="block text-sm font-medium text-gray-700">Size Category</label>
                                <select id="size_category" name="size_category" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                </select>
                            </div>

                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" step="0.1" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label for="color_markings" class="block text-sm font-medium text-gray-700">Color/Markings</label>
                                <input type="text" id="color_markings" name="color_markings" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label for="coat_type" class="block text-sm font-medium text-gray-700">Coat Type</label>
                                <input type="text" id="coat_type" name="coat_type" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" required max="{{ date('Y-m-d') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeAddPetModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-teal-500 text-white rounded-md text-sm font-medium hover:bg-teal-600">
                                Add Pet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modal functionality
            const addPetModal = document.getElementById('add-pet-modal');
            
            if (addPetModal) {
                // Close modal when clicking outside
                addPetModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAddPetModal();
                    }
                });
            }
        });

        function openAddPetModal() {
            const modal = document.getElementById('add-pet-modal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeAddPetModal() {
            const modal = document.getElementById('add-pet-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function toggleSpeciesField() {
            const typeSelect = document.getElementById('type');
            const speciesField = document.getElementById('species-field');
            const speciesInput = document.getElementById('species');

            if (typeSelect && speciesField && speciesInput) {
                if (typeSelect.value === 'Exotic') {
                    speciesField.classList.remove('hidden');
                    speciesInput.required = true;
                } else {
                    speciesField.classList.add('hidden');
                    speciesInput.required = false;
                    speciesInput.value = '';
                }
            }
        }
    </script>
    @endpush
</div>
@endsection 