@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8 mt-8">
        <div class="bg-white shadow-xl rounded-lg">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Set Up Your Services</h1>
                <p class="mt-2 text-sm text-gray-600">Add the services you offer to your customers.</p>
            </div>

            <!-- Services Form -->
            <form method="POST" action="{{ route('shop.setup.services.store') }}" id="servicesForm">
                @csrf
                <div class="px-8 py-6">
                    <div id="servicesContainer">
                        <!-- Service template will be repeated here -->
                        <div class="service-item mb-8 p-6 bg-gray-50 rounded-lg">
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-4">Basic Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Service Name</label>
                                        <input type="text" 
                                               name="services[0][name]" 
                                               required 
                                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                                        <select name="services[0][category]" 
                                                required 
                                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">Select Category</option>
                                            <option value="grooming">Grooming</option>
                                            <option value="veterinary">Veterinary</option>
                                            <option value="boarding">Boarding</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                    <textarea name="services[0][description]" 
                                            rows="3"
                                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                </div>
                            </div>

                            <!-- Service Specifications -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-4">Service Specifications</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Pet Types</label>
                                        <div class="space-y-2">
                                            @foreach(['dogs', 'cats', 'birds', 'rabbits'] as $type)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                           name="services[0][pet_types][]" 
                                                           value="{{ $type }}" 
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2">{{ ucfirst($type) }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Size Ranges</label>
                                        <div class="space-y-2">
                                            @foreach(['small', 'medium', 'large'] as $size)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                           name="services[0][size_ranges][]" 
                                                           value="{{ $size }}" 
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2">{{ ucfirst($size) }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Exotic Pet Service -->
                                <div class="mt-4">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="services[0][exotic_pet_service]" 
                                               value="1"
                                               class="exotic-pet-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <span class="ml-2">Exotic Pet Service</span>
                                    </label>
                                </div>

                                <!-- Exotic Pet Species Section -->
                                <div class="exotic-species-section mt-4" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700">Exotic Pet Species</label>
                                    <div class="exotic-species-container mt-2 space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <input type="text" 
                                                   name="services[0][exotic_pet_species][]" 
                                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                   placeholder="Enter species name">
                                            </div>
                                    </div>
                                    <button type="button"
                                            class="add-species-btn mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Add Species
                                    </button>
                                </div>
                            </div>

                            <!-- Pricing Details -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-4">Pricing Details</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Base Price (₱)</label>
                                        <input type="number" 
                                               name="services[0][base_price]" 
                                               step="0.01"
                                               required 
                                               min="0"
                                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Duration (minutes)</label>
                                        <input type="number" 
                                               name="services[0][duration]" 
                                               required 
                                               min="15"
                                               step="15"
                                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                </div>
                                </div>

                            <!-- Remove Service Button -->
                            <div class="flex justify-end">
                                        <button type="button"
                                        class="remove-service text-red-600 hover:text-red-800"
                                        style="display: none;">
                                    Remove Service
                                        </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Service Button -->
                    <button type="button"
                            onclick="addService()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Another Service
                    </button>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <a href="{{ route('shop.setup.welcome') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Back
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle exotic pet service checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.matches('.exotic-pet-checkbox')) {
            const section = e.target.closest('.service-item').querySelector('.exotic-species-section');
            if (section) {
                section.style.display = e.target.checked ? 'block' : 'none';
            }
        }
    });

    // Handle add species buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.add-species-btn')) {
            const container = e.target.closest('.exotic-species-section').querySelector('.exotic-species-container');
            const serviceIndex = e.target.closest('.service-item').getAttribute('data-index');
            addSpeciesField(container, serviceIndex);
        }
    });

    // Initialize remove buttons
    updateRemoveButtons();
});

function addService() {
    const container = document.getElementById('servicesContainer');
    const serviceCount = container.children.length;
    const template = container.children[0].cloneNode(true);
    
    // Update all name attributes with new index
    template.querySelectorAll('[name]').forEach(input => {
        input.name = input.name.replace('[0]', `[${serviceCount}]`);
        input.value = ''; // Clear values
    });

    // Clear checkboxes
    template.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

    // Reset exotic species section
    const exoticSection = template.querySelector('.exotic-species-section');
    if (exoticSection) {
        exoticSection.style.display = 'none';
        const container = exoticSection.querySelector('.exotic-species-container');
        if (container) {
            container.innerHTML = `
                <div class="flex items-center space-x-2">
                    <input type="text" 
                           name="services[${serviceCount}][exotic_pet_species][]" 
                           class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Enter species name">
                </div>
            `;
        }
    }

    // Set data-index attribute
    template.setAttribute('data-index', serviceCount);

    container.appendChild(template);
    updateRemoveButtons();
}

function addSpeciesField(container, serviceIndex) {
    const newField = document.createElement('div');
    newField.className = 'flex items-center space-x-2';
    newField.innerHTML = `
        <input type="text" 
               name="services[${serviceIndex}][exotic_pet_species][]" 
               class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               placeholder="Enter species name">
        <button type="button" onclick="removeSpeciesField(this)" class="text-red-600 hover:text-red-800">
            Remove
        </button>
    `;
    container.appendChild(newField);
}

function removeSpeciesField(button) {
    button.closest('.flex').remove();
}

function updateRemoveButtons() {
    const services = document.querySelectorAll('.service-item');
    services.forEach((service, index) => {
        const removeBtn = service.querySelector('.remove-service');
        if (removeBtn) {
            removeBtn.style.display = services.length > 1 ? 'block' : 'none';
            removeBtn.onclick = () => removeService(service);
        }
    });
}

function removeService(serviceElement) {
    if (confirm('Are you sure you want to remove this service?')) {
        serviceElement.remove();
        updateRemoveButtons();
    }
}
</script>
@endpush 

@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mt-32">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    Please fix the following errors:
                </p>
                <ul class="mt-2 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif 
