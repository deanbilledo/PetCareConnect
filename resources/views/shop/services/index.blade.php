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

                <!-- Services List -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet Types</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($services as $service)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($service->category) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($service->base_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->duration }} mins</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($service->pet_types as $type)
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                {{ ucfirst($type) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openEditModal({{ $service->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button onclick="toggleStatus({{ $service->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        {{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button onclick="deleteService({{ $service->id }})" class="text-red-600 hover:text-red-900">Delete</button>
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

<!-- Add/Edit Service Modal -->
<div id="serviceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-auto">
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
                                <input type="checkbox" name="breed_specific" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2">Breed-Specific Service</span>
                            </label>
                        </div>
                        <div class="mt-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="special_requirements">Special Requirements</label>
                            <textarea id="special_requirements" name="special_requirements" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
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

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentServiceId = null;

    // Form submission
    const serviceForm = document.getElementById('serviceForm');
    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('name').value,
                category: document.getElementById('category').value,
                description: document.getElementById('description').value,
                pet_types: Array.from(document.querySelectorAll('input[name="pet_types[]"]:checked')).map(cb => cb.value),
                size_ranges: Array.from(document.querySelectorAll('input[name="size_ranges[]"]:checked')).map(cb => cb.value),
                breed_specific: document.querySelector('input[name="breed_specific"]').checked,
                special_requirements: document.getElementById('special_requirements').value,
                base_price: parseFloat(document.getElementById('base_price').value),
                duration: parseInt(document.getElementById('duration').value)
            };

            const url = currentServiceId ? `/shop/services/${currentServiceId}` : '/shop/services';
            const method = currentServiceId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to save service');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save service. Please try again.');
            });
        });
    }

    // Helper functions
    window.getVariablePricing = function() {
        const container = document.getElementById('variablePricingContainer');
        return Array.from(container.children).map(row => ({
            size: row.querySelector('select').value,
            price: parseFloat(row.querySelector('input[type="number"]').value)
        })).filter(item => item.size && !isNaN(item.price));
    }

    window.getAddOns = function() {
        const container = document.getElementById('addOnsContainer');
        return Array.from(container.children).map(row => ({
            name: row.querySelector('input[type="text"]').value,
            price: parseFloat(row.querySelector('input[type="number"]').value)
        })).filter(item => item.name && !isNaN(item.price));
    }

    window.addVariablePricing = function() {
        const container = document.getElementById('variablePricingContainer');
        const index = container.children.length;
        const html = `
            <div class="variable-pricing-row grid grid-cols-2 gap-2 mb-2">
                <select name="variable_pricing[${index}][size]" class="shadow border rounded py-2 px-3 text-gray-700">
                    <option value="">Select Size</option>
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
                <input type="number" step="0.01" name="variable_pricing[${index}][price]" placeholder="Price" 
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700">
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    window.addAddOn = function() {
        const container = document.getElementById('addOnsContainer');
        const index = container.children.length;
        const html = `
            <div class="add-on-row grid grid-cols-2 gap-2 mb-2">
                <input type="text" name="add_ons[${index}][name]" placeholder="Add-on Name" 
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700">
                <input type="number" step="0.01" name="add_ons[${index}][price]" placeholder="Price" 
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700">
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // Make functions available globally
    window.openAddModal = function() {
        currentServiceId = null;
        document.getElementById('modalTitle').textContent = 'Add New Service';
        document.getElementById('serviceForm').reset();
        document.getElementById('variablePricingContainer').innerHTML = '';
        document.getElementById('addOnsContainer').innerHTML = '';
        document.getElementById('serviceModal').classList.remove('hidden');
    }

    window.openEditModal = function(serviceId) {
        currentServiceId = serviceId;
        document.getElementById('modalTitle').textContent = 'Edit Service';
        
        // Add loading state
        const form = document.getElementById('serviceForm');
        form.classList.add('opacity-50', 'pointer-events-none');
        
        fetch(`/shop/services/${serviceId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load service');
            }

            const service = data.data;
            console.log('Service data:', service); // Debug log

            // Populate form fields
            document.getElementById('name').value = service.name || '';
            document.getElementById('category').value = service.category || '';
            document.getElementById('description').value = service.description || '';
            document.getElementById('base_price').value = service.base_price || '';
            document.getElementById('duration').value = service.duration || '';
            document.getElementById('special_requirements').value = service.special_requirements || '';

            // Set pet types
            const petTypes = Array.isArray(service.pet_types) ? service.pet_types : [];
            document.querySelectorAll('input[name="pet_types[]"]').forEach(cb => {
                cb.checked = petTypes.includes(cb.value);
            });

            // Set size ranges
            const sizeRanges = Array.isArray(service.size_ranges) ? service.size_ranges : [];
            document.querySelectorAll('input[name="size_ranges[]"]').forEach(cb => {
                cb.checked = sizeRanges.includes(cb.value);
            });

            // Set breed specific
            document.querySelector('input[name="breed_specific"]').checked = !!service.breed_specific;

            // Show modal
            document.getElementById('serviceModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching service:', error);
            alert(`Failed to load service details: ${error.message}`);
        })
        .finally(() => {
            // Remove loading state
            form.classList.remove('opacity-50', 'pointer-events-none');
        });
    }

    window.closeModal = function() {
        document.getElementById('serviceModal').classList.add('hidden');
        document.getElementById('serviceForm').reset();
        document.getElementById('variablePricingContainer').innerHTML = '';
        document.getElementById('addOnsContainer').innerHTML = '';
    }

    // Close modal when clicking outside
    document.getElementById('serviceModal').addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('fixed')) {
            closeModal();
        }
    });

    window.toggleStatus = function(serviceId) {
        if (!confirm('Are you sure you want to change this service\'s status?')) return;

        fetch(`/shop/services/${serviceId}/status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update status. Please try again.');
        });
    }

    window.deleteService = function(serviceId) {
        if (!confirm('Are you sure you want to delete this service?')) return;

        fetch(`/shop/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete service. Please try again.');
        });
    }
});
</script>
@endpush 