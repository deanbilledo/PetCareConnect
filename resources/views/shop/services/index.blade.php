@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Manage Services</h2>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Add New Service
                    </button>
                </div>

                <!-- Discount Modal -->
                <div id="discountModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
                    
                    <!-- Modal -->
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
                            <div class="px-6 py-4">
                                <h3 class="text-lg font-medium text-gray-900">Add Discount</h3>
                                <form id="discountForm" class="mt-4">
                                    @csrf
                                    <input type="hidden" id="serviceIdForDiscount">
                                    
                                    <!-- Discount Type -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Discount Type</label>
                                        <select id="discountType" name="discount_type" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="percentage">Percentage (%)</option>
                                            <option value="fixed">Fixed Amount (₱)</option>
                                        </select>
                                    </div>

                                    <!-- Discount Value -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Discount Value</label>
                                        <input type="number" 
                                               id="discountValue" 
                                               name="discount_value" 
                                               min="0" 
                                               step="0.01" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                        <p class="mt-1 text-sm text-gray-500" id="discountHelp">
                                            Enter percentage (0-100) or fixed amount
                                        </p>
                                    </div>

                                    <!-- Add Voucher Code field here -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">
                                            Voucher Code
                                            <span class="text-sm font-normal text-gray-500 ml-1">(Optional)</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                   id="voucherCode" 
                                                   name="voucher_code" 
                                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase"
                                                   placeholder="Enter voucher code">
                                            <button type="button" 
                                                    onclick="generateVoucherCode()" 
                                                    class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                Generate
                                            </button>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Leave empty to not use a voucher code
                                        </p>
                                    </div>

                                    <!-- Valid From -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Valid From</label>
                                        <input type="datetime-local" 
                                               id="validFrom" 
                                               name="valid_from" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                    </div>

                                    <!-- Valid Until -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Valid Until</label>
                                        <input type="datetime-local" 
                                               id="validUntil" 
                                               name="valid_until" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                        <textarea id="discountDescription" 
                                                 name="description" 
                                                 rows="2" 
                                                 class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                 placeholder="Optional description of the discount"></textarea>
                                    </div>

                                    <div class="flex justify-end space-x-2">
                                        <button type="button" 
                                                onclick="closeDiscountModal()" 
                                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Add Discount
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services List -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Base Price</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Duration</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Pet Types</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Exotic Pets</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($services as $service)
                            <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $service->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ ucfirst($service->category) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        ₱{{ number_format($service->base_price, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $service->duration }} mins
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($service->pet_types as $type)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $type === 'Exotic' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $type }}
                                                </span>
                                            @endforeach
                                            @if($service->exotic_pet_service && !empty($service->exotic_pet_species))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Exotic: {{ implode(', ', $service->exotic_pet_species) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        @if($service->exotic_pet_service)
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach($service->exotic_pet_species as $species)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $species }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-500">Not Available</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end space-x-2">
                                            <button onclick="openEditModal({{ $service->id }})" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </button>
                                            <button onclick="openDiscountModal({{ $service->id }})" 
                                                    class="text-green-600 hover:text-green-900">
                                                Add Discount
                                            </button>
                                            <button onclick="openDeactivateModal({{ $service->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-900">
                                        {{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                            <button onclick="openDeleteModal({{ $service->id }})" 
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Service Modal -->
<div id="serviceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-5xl w-full mx-auto">
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add New Service</h3>
                <form id="serviceForm" class="mt-4">
                    @csrf
                    <input type="hidden" id="serviceId">
                    
                    <!-- Basic Service Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Basic Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Service Name</label>
                                <input type="text" id="name" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Category</label>
                                <select id="category" name="category" required class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Category</option>
                                    <option value="grooming">Grooming</option>
                                    <option value="veterinary">Veterinary</option>
                                    <option value="boarding">Boarding</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                            <textarea id="description" name="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
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
                                            <input type="checkbox" name="pet_types[]" value="{{ $type }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                                            <input type="checkbox" name="size_ranges[]" value="{{ $size }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2">{{ ucfirst($size) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       id="exotic_pet_service"
                                       name="exotic_pet_service" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2">Exotic Pet Service</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">Check this if this service is available for exotic pets (e.g., reptiles, amphibians, small mammals, etc.)</p>
                        </div>
                        <div id="exotic_pet_species_section" class="mt-4" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Exotic Pet Species</label>
                            <div id="exotic_pet_species_container" class="mt-2 space-y-2">
                                <!-- Species will be added here dynamically -->
                            </div>
                            <button type="button"
                                    onclick="addExoticPetSpecies()"
                                    class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Species
                            </button>
                        </div>
                    </div>

                    <!-- Pricing Details -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Pricing Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="base_price">Base Price (₱)</label>
                                <input type="number" step="0.01" id="base_price" name="base_price" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="duration">Duration (minutes)</label>
                                <input type="number" id="duration" name="duration" required min="15" step="15" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <!-- Variable Pricing -->
                        <div id="variablePricing" class="mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-gray-700 text-sm font-bold">Variable Pricing</label>
                                <button type="button" onclick="addVariablePricing()" class="text-sm text-blue-600 hover:text-blue-800">+ Add Price</button>
                            </div>
                            <div id="variablePricingContainer"></div>
                        </div>

                        <!-- Add-ons -->
                        <div id="addOns" class="mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-gray-700 text-sm font-bold">Add-ons</label>
                                <button type="button" onclick="addAddOn()" class="text-sm text-blue-600 hover:text-blue-800">+ Add Service</button>
                            </div>
                            <div id="addOnsContainer"></div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeServiceModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Save Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Service Modal -->
<div id="deactivateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Service Status Change</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to change this service's status? This will affect its visibility to customers.</p>
                
                <input type="hidden" id="serviceIdToDeactivate">
                
                <div class="flex justify-end space-x-2">
                    <button onclick="closeDeactivateModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button onclick="confirmToggleStatus()" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Service Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
            <div class="p-6">
                <h3 class="text-lg font-medium text-red-600 mb-4">Delete Service</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to delete this service? This action cannot be undone.</p>
                
                <input type="hidden" id="serviceIdToDelete">
                
                <div class="flex justify-end space-x-2">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
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
    let currentServiceId = null;

    // Initialize form elements only if they exist
    const serviceForm = document.getElementById('serviceForm');
    const exoticPetServiceCheckbox = document.getElementById('exotic_pet_service');
    const exoticPetSpeciesSection = document.getElementById('exotic_pet_species_section');

    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get all selected pet types including 'Exotic' if exotic_pet_service is checked
            const selectedPetTypes = Array.from(document.querySelectorAll('input[name="pet_types[]"]:checked')).map(cb => cb.value);
            const isExoticService = document.getElementById('exotic_pet_service').checked;
            
            // Add 'Exotic' to pet types if exotic service is enabled
            if (isExoticService && !selectedPetTypes.includes('Exotic')) {
                selectedPetTypes.push('Exotic');
            }

            // Validate that at least one pet type is selected
            if (selectedPetTypes.length === 0) {
                alert('Please select at least one pet type');
                return;
            }
            
            const formData = {
                name: document.getElementById('name').value.trim(),
                category: document.getElementById('category').value,
                description: document.getElementById('description').value.trim(),
                pet_types: selectedPetTypes,
                size_ranges: Array.from(document.querySelectorAll('input[name="size_ranges[]"]:checked')).map(cb => cb.value),
                base_price: parseFloat(document.getElementById('base_price').value),
                duration: parseInt(document.getElementById('duration').value),
                exotic_pet_service: isExoticService,
                exotic_pet_species: isExoticService ? Array.from(document.getElementsByName('exotic_pet_species[]'))
                    .map(input => input.value.trim())
                    .filter(value => value !== '') : [],
                special_requirements: document.getElementById('special_requirements')?.value || '',
                variable_pricing: getVariablePricing(),
                add_ons: getAddOns()
            };

            // Validate required fields
            if (!formData.name) {
                alert('Service name is required');
                return;
            }
            if (!formData.category) {
                alert('Category is required');
                return;
            }
            if (isNaN(formData.base_price) || formData.base_price <= 0) {
                alert('Please enter a valid base price');
                return;
            }
            if (isNaN(formData.duration) || formData.duration < 15) {
                alert('Please enter a valid duration (minimum 15 minutes)');
                return;
            }
            if (formData.size_ranges.length === 0) {
                alert('Please select at least one size range');
                return;
            }

            // Add validation for exotic pet species
            if (formData.exotic_pet_service && formData.exotic_pet_species.length === 0) {
                alert('Please add at least one exotic pet species when exotic pet service is enabled');
                return;
            }

            const serviceId = document.getElementById('serviceId').value;
            const url = serviceId ? `/shop/services/${serviceId}` : '/shop/services';
            const method = serviceId ? 'PUT' : 'POST';

            // Disable form while submitting
            const submitButton = serviceForm.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = true;

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to save service');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to save service');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to save service. Please try again.');
            })
            .finally(() => {
                if (submitButton) submitButton.disabled = false;
            });
        });
    }

    // Initialize exotic pet service checkbox listener if elements exist
    if (exoticPetServiceCheckbox && exoticPetSpeciesSection) {
        exoticPetServiceCheckbox.addEventListener('change', function() {
            exoticPetSpeciesSection.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Modal functions
    window.openAddModal = function() {
        const form = document.getElementById('serviceForm');
        const modal = document.getElementById('serviceModal');
        
        if (!form || !modal) {
            console.error('Required modal elements not found');
            return;
        }
        
        // Reset the form
        form.reset();
        
        // Clear the service ID
        document.getElementById('serviceId').value = '';
        
        // Set modal title for Add
        document.getElementById('modalTitle').textContent = 'Add New Service';
        
        // Clear exotic pet species
        const container = document.getElementById('exotic_pet_species_container');
        if (container) {
            container.innerHTML = '';
        }
        
        // Hide exotic pet species section
        const section = document.getElementById('exotic_pet_species_section');
        if (section) {
            section.style.display = 'none';
        }
        
        // Clear variable pricing and add-ons containers
        const variablePricingContainer = document.getElementById('variablePricingContainer');
        const addOnsContainer = document.getElementById('addOnsContainer');
        if (variablePricingContainer) variablePricingContainer.innerHTML = '';
        if (addOnsContainer) addOnsContainer.innerHTML = '';
        
        // Show the modal
        modal.classList.remove('hidden');
    };

    window.openEditModal = function(serviceId) {
        currentServiceId = serviceId;
        const form = document.getElementById('serviceForm');
        const modal = document.getElementById('serviceModal');
        
        if (!form || !modal) {
            console.error('Required modal elements not found');
            return;
        }
        
        form.classList.add('opacity-50', 'pointer-events-none');
        
        fetch(`/shop/services/${serviceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const service = data.data;

                    // Safely set form values
                    const setElementValue = (id, value) => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.value = value;
                        }
                    };

                    // Set basic fields
                    document.getElementById('modalTitle').textContent = 'Edit Service';
                    setElementValue('serviceId', service.id);
                    setElementValue('name', service.name);
                    setElementValue('category', service.category);
                    setElementValue('description', service.description || '');
                    setElementValue('base_price', service.base_price);
                    setElementValue('duration', service.duration);
                    setElementValue('special_requirements', service.special_requirements || '');

                    // Set checkboxes
                    const petTypeCheckboxes = document.querySelectorAll('input[name="pet_types[]"]');
                    if (petTypeCheckboxes.length) {
                        petTypeCheckboxes.forEach(cb => {
                            cb.checked = (service.pet_types || []).includes(cb.value);
                        });
                    }

                    const sizeRangeCheckboxes = document.querySelectorAll('input[name="size_ranges[]"]');
                    if (sizeRangeCheckboxes.length) {
                        sizeRangeCheckboxes.forEach(cb => {
                            cb.checked = (service.size_ranges || []).includes(cb.value);
                        });
                    }

                    // Handle exotic pet service
                    if (exoticPetServiceCheckbox && exoticPetSpeciesSection) {
                        exoticPetServiceCheckbox.checked = service.exotic_pet_service;
                        exoticPetSpeciesSection.style.display = service.exotic_pet_service ? 'block' : 'none';

                        // Clear and populate exotic pet species
                        const container = document.getElementById('exotic_pet_species_container');
                        if (container) {
                            container.innerHTML = '';
                            if (service.exotic_pet_species && service.exotic_pet_species.length > 0) {
                                service.exotic_pet_species.forEach(species => {
                                    addExoticPetSpecies(species);
                                });
                            }
                        }
                    }

                    modal.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load service details');
            })
            .finally(() => {
                form.classList.remove('opacity-50', 'pointer-events-none');
            });
    };

    // Helper function to add exotic pet species with optional initial value
    window.addExoticPetSpecies = function(initialValue = '') {
        const container = document.getElementById('exotic_pet_species_container');
        if (!container) return;

        const newRow = document.createElement('div');
        newRow.className = 'flex items-center space-x-2';
        newRow.innerHTML = `
            <input type="text" 
                   name="exotic_pet_species[]" 
                   value="${initialValue}"
                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   placeholder="Enter species name">
            <button type="button" onclick="removeExoticPetSpecies(this)" class="text-red-600 hover:text-red-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        container.appendChild(newRow);
    };

    window.removeExoticPetSpecies = function(button) {
        if (button && button.closest('.flex')) {
            button.closest('.flex').remove();
        }
    };

    // Add modal close function
    window.closeServiceModal = function() {
        const modal = document.getElementById('serviceModal');
        if (modal) {
            modal.classList.add('hidden');
            // Reset form
            const form = document.getElementById('serviceForm');
            if (form) {
                form.reset();
                // Clear exotic pet species
                const container = document.getElementById('exotic_pet_species_container');
                if (container) {
                    container.innerHTML = '';
                }
                // Hide exotic pet species section
                const section = document.getElementById('exotic_pet_species_section');
                if (section) {
                    section.style.display = 'none';
                }
            }
        }
    };

    // Add these helper functions for variable pricing and add-ons
    function getVariablePricing() {
        const container = document.getElementById('variablePricingContainer');
        if (!container) return [];

        const rows = container.querySelectorAll('.variable-pricing-row');
        return Array.from(rows).map(row => ({
            size: row.querySelector('select').value,
            price: parseFloat(row.querySelector('input[type="number"]').value)
        })).filter(item => item.size && !isNaN(item.price));
    }

    function getAddOns() {
        const container = document.getElementById('addOnsContainer');
        if (!container) return [];

        const rows = container.querySelectorAll('.add-on-row');
        return Array.from(rows).map(row => ({
            name: row.querySelector('input[type="text"]').value.trim(),
            price: parseFloat(row.querySelector('input[type="number"]').value)
        })).filter(item => item.name && !isNaN(item.price));
    }
});
</script>
@endpush 