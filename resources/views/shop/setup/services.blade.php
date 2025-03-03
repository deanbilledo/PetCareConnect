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
            <form method="POST" action="{{ route('shop.setup.services') }}" id="servicesForm">
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
                                            <select name="services[0][exotic_pet_species][]" 
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    multiple>
                                                <optgroup label="Reptiles">
                                                    <option value="snake">Snake</option>
                                                    <option value="lizard">Lizard</option>
                                                    <option value="turtle">Turtle</option>
                                                    <option value="iguana">Iguana</option>
                                                    <option value="gecko">Gecko</option>
                                                </optgroup>
                                                <optgroup label="Small Mammals">
                                                    <option value="hamster">Hamster</option>
                                                    <option value="gerbil">Gerbil</option>
                                                    <option value="ferret">Ferret</option>
                                                    <option value="guinea_pig">Guinea Pig</option>
                                                    <option value="chinchilla">Chinchilla</option>
                                                </optgroup>
                                                <optgroup label="Birds">
                                                    <option value="parrot">Parrot</option>
                                                    <option value="cockatiel">Cockatiel</option>
                                                    <option value="macaw">Macaw</option>
                                                    <option value="parakeet">Parakeet</option>
                                                    <option value="lovebird">Lovebird</option>
                                                </optgroup>
                                                <optgroup label="Others">
                                                    <option value="hedgehog">Hedgehog</option>
                                                    <option value="sugar_glider">Sugar Glider</option>
                                                    <option value="bearded_dragon">Bearded Dragon</option>
                                                    <option value="tarantula">Tarantula</option>
                                                    <option value="scorpion">Scorpion</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
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

                                <!-- Variable Pricing -->
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-gray-700 text-sm font-bold">Variable Pricing</label>
                                        <button type="button" 
                                                onclick="addVariablePricing(0)" 
                                                class="text-sm text-blue-600 hover:text-blue-800">
                                            + Add Price
                                        </button>
                                    </div>
                                    <div class="variable-pricing-container"></div>
                                </div>

                                <!-- Add-ons -->
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-gray-700 text-sm font-bold">Add-ons</label>
                                        <button type="button" 
                                                onclick="addAddOn(0)" 
                                                class="text-sm text-blue-600 hover:text-blue-800">
                                            + Add Service
                                        </button>
                                    </div>
                                    <div class="add-ons-container"></div>
                                </div>
                            </div>

                            <!-- Employee Assignment Section -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-800 mb-4">Assign Employees</h4>
                                <p class="text-sm text-gray-600 mb-4">Select employees who can perform this service</p>
                                
                                <div class="space-y-3">
                                    @foreach($employees as $employee)
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" 
                                               name="services[0][employee_ids][]" 
                                               value="{{ $employee->id }}"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <div>
                                            <span class="text-gray-700">{{ $employee->name }}</span>
                                            <span class="text-sm text-gray-500 ml-2">({{ $employee->position }})</span>
                                        </div>
                                    </label>
                                    @endforeach

                                    @if(count($employees) === 0)
                                    <div class="text-yellow-600 bg-yellow-50 p-4 rounded-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm">
                                                    No employees found. 
                                                    <a href="{{ route('shop.setup.employees') }}" class="font-medium underline text-yellow-600 hover:text-yellow-500">
                                                        Add employees first
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
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
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('servicesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        let errors = [];
        const services = document.querySelectorAll('.service-item');
        
        services.forEach((service, serviceIndex) => {
            // Update name attributes to use correct array indices
            service.querySelectorAll('[name*="services[0]"]').forEach(input => {
                const newName = input.getAttribute('name').replace('services[0]', `services[${serviceIndex}]`);
                input.setAttribute('name', newName);
            });

            // Basic Information Validation
            const serviceName = service.querySelector(`input[name="services[${serviceIndex}][name]"]`);
            const serviceCategory = service.querySelector(`select[name="services[${serviceIndex}][category]"]`);
            const petTypes = service.querySelectorAll(`input[name="services[${serviceIndex}][pet_types][]"]:checked`);
            const sizeRanges = service.querySelectorAll(`input[name="services[${serviceIndex}][size_ranges][]"]:checked`);
            const basePrice = service.querySelector(`input[name="services[${serviceIndex}][base_price]"]`);
            const duration = service.querySelector(`input[name="services[${serviceIndex}][duration]"]`);

            // Reset error states
            [serviceName, serviceCategory, basePrice, duration].forEach(field => {
                if (field) {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-300');
                }
            });

            // Validate required fields
            if (!serviceName?.value?.trim()) {
                isValid = false;
                serviceName?.classList.add('border-red-500');
                errors.push(`Service #${serviceIndex + 1}: Service Name is required`);
            }

            if (!serviceCategory?.value) {
                isValid = false;
                serviceCategory?.classList.add('border-red-500');
                errors.push(`Service #${serviceIndex + 1}: Category is required`);
            }

            if (petTypes.length === 0) {
                isValid = false;
                errors.push(`Service #${serviceIndex + 1}: At least one Pet Type must be selected`);
            }

            if (sizeRanges.length === 0) {
                isValid = false;
                errors.push(`Service #${serviceIndex + 1}: At least one Size Range must be selected`);
            }

            if (!basePrice?.value || parseFloat(basePrice.value) <= 0) {
                isValid = false;
                basePrice?.classList.add('border-red-500');
                errors.push(`Service #${serviceIndex + 1}: Base Price is required and must be greater than 0`);
            }

            if (!duration?.value || parseInt(duration.value) < 15) {
                isValid = false;
                duration?.classList.add('border-red-500');
                errors.push(`Service #${serviceIndex + 1}: Duration is required and must be at least 15 minutes`);
            }

            // Exotic Pet Service Validation
            const exoticPetCheckbox = service.querySelector(`input[name="services[${serviceIndex}][exotic_pet_service]"]`);
            if (exoticPetCheckbox?.checked) {
                const select = service.querySelector(`select[name="services[${serviceIndex}][exotic_pet_species][]"]`);
                if (select && select.tomselect) {
                    const selectedValues = select.tomselect.getValue();
                    if (!selectedValues.length) {
                        isValid = false;
                        errors.push(`Service #${serviceIndex + 1}: Please enter at least one exotic pet species`);
                    }
                }
            }
        });

        // Display errors if any
        const errorContainer = document.querySelector('.validation-errors');
        if (errors.length > 0) {
            let errorHtml = `
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">Please fix the following errors:</p>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                ${errors.map(error => `<li>${error}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            
            if (errorContainer) {
                errorContainer.innerHTML = errorHtml;
            } else {
                const newErrorContainer = document.createElement('div');
                newErrorContainer.className = 'validation-errors';
                newErrorContainer.innerHTML = errorHtml;
                const form = document.getElementById('servicesForm');
                form.insertBefore(newErrorContainer, form.firstChild);
            }
            
            return false;
        }
        
        if (errorContainer) {
            errorContainer.remove();
        }
        
        if (isValid) {
            this.submit();
        }
    });

    // Handle exotic pet service checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.matches('.exotic-pet-checkbox')) {
            const section = e.target.closest('.service-item').querySelector('.exotic-species-section');
            const select = section.querySelector('select');
            
            if (section) {
                section.style.display = e.target.checked ? 'block' : 'none';
                if (select && select.tomselect) {
                    if (!e.target.checked) {
                        select.tomselect.clear();
                    }
                    select.tomselect.setValue(select.tomselect.getValue());
                }
            }
        }
    });

    // Initialize remove buttons
    updateRemoveButtons();

    // Initialize Tom Select for existing exotic pet species select
    initializeExoticPetSelect(document.querySelector('select[name*="exotic_pet_species"]'));
});

function addVariablePricing(serviceIndex) {
    const container = event.target.closest('.service-item').querySelector('.variable-pricing-container');
    
    // Get selected size ranges
    const selectedSizes = Array.from(
        event.target.closest('.service-item').querySelectorAll('input[name*="size_ranges"]:checked')
    ).map(input => input.value);
    
    if (selectedSizes.length === 0) {
        alert('Please select at least one size range before adding variable pricing');
        return;
    }
    
    // Get already used sizes
    const usedSizes = Array.from(
        container.querySelectorAll('select[name*="variable_pricing"][name*="size"]')
    ).map(select => select.value);
    
    // Filter out already used sizes
    const availableSizes = selectedSizes.filter(size => !usedSizes.includes(size));
    
    if (availableSizes.length === 0) {
        alert('All selected sizes already have pricing set');
        return;
    }
    
    const rowIndex = container.children.length;
    const newRow = document.createElement('div');
    newRow.className = 'variable-pricing-row flex items-center space-x-2 mb-2';
    newRow.innerHTML = `
        <select name="services[${serviceIndex}][variable_pricing][${rowIndex}][size]"
                required
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Select Size</option>
            ${availableSizes.map(size => 
                `<option value="${size}">${size.charAt(0).toUpperCase() + size.slice(1)}</option>`
            ).join('')}
        </select>
        <input type="number" 
               name="services[${serviceIndex}][variable_pricing][${rowIndex}][price]"
               required
               step="0.01" 
               min="0" 
               placeholder="Price" 
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <button type="button" 
                onclick="removeVariablePricing(this)" 
                class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newRow);
}

function removeVariablePricing(button) {
    const row = button.closest('.variable-pricing-row');
    const container = row.parentElement;
    row.remove();
    
    // Reindex remaining rows
    container.querySelectorAll('.variable-pricing-row').forEach((row, index) => {
        const sizeSelect = row.querySelector('select[name*="variable_pricing"]');
        const priceInput = row.querySelector('input[name*="variable_pricing"]');
        const serviceIndex = row.closest('.service-item').getAttribute('data-index') || 0;
        
        sizeSelect.name = `services[${serviceIndex}][variable_pricing][${index}][size]`;
        priceInput.name = `services[${serviceIndex}][variable_pricing][${index}][price]`;
    });
}

function addAddOn(serviceIndex) {
    const container = event.target.closest('.service-item').querySelector('.add-ons-container');
    const newRow = document.createElement('div');
    newRow.className = 'add-on-row flex items-center space-x-2 mb-2';
    newRow.innerHTML = `
        <input type="text" 
               name="services[${serviceIndex}][add_ons][][name]"
               placeholder="Add-on Name"
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <input type="number" 
               name="services[${serviceIndex}][add_ons][][price]"
               step="0.01" 
               min="0" 
               placeholder="Price" 
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <button type="button" 
                onclick="removeAddOn(this)" 
                class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newRow);
}

function removeAddOn(button) {
    button.closest('.add-on-row').remove();
}

function addService() {
    const container = document.getElementById('servicesContainer');
    const serviceCount = container.children.length;
    const template = container.children[0].cloneNode(true);
    
    // Update all name attributes with new index
    template.querySelectorAll('[name]').forEach(input => {
        input.name = input.name.replace('[0]', `[${serviceCount}]`);
        if (input.tagName === 'SELECT') {
            // Remove existing Tom Select instance if any
            if (input.tomselect) {
                input.tomselect.destroy();
            }
            input.selectedIndex = -1;
        } else {
            input.value = '';
        }
    });

    // Clear checkboxes
    template.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

    // Reset exotic species section
    const exoticSection = template.querySelector('.exotic-species-section');
    if (exoticSection) {
        exoticSection.style.display = 'none';
    }

    // Reset variable pricing and add-ons containers
    template.querySelector('.variable-pricing-container').innerHTML = '';
    template.querySelector('.add-ons-container').innerHTML = '';

    // Set data-index attribute
    template.setAttribute('data-index', serviceCount);

    container.appendChild(template);
    
    // Initialize Tom Select for the new service's exotic pet select
    initializeExoticPetSelect(template.querySelector('select[name*="exotic_pet_species"]'));
    
    updateRemoveButtons();
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

function initializeExoticPetSelect(selectElement) {
    if (selectElement) {
        new TomSelect(selectElement, {
            plugins: ['remove_button'],
            maxItems: null,
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
            },
            onItemAdd: function() {
                this.setTextboxValue('');
                this.refreshOptions(false);
            }
        });
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
