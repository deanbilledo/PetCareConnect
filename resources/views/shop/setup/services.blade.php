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

            <!-- Informational Banner -->
            <div class="mx-8 mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <span class="font-medium">Get Started:</span> Add up to 3 services for now. You can always add more later in your shop services management.
                        </p>
                    </div>
                </div>
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
                                        <label class="block text-gray-700 text-sm font-bold mb-2">
                                            Pet Types <span class="text-red-500">*</span>
                                        </label>
                                        <div class="space-y-2 pet-type-container">
                                            @foreach(['dogs', 'cats', 'birds', 'rabbits'] as $type)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                           name="services[0][pet_types][]" 
                                                           value="{{ $type }}" 
                                                           class="pet-type-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2">{{ ucfirst($type) }}</span>
                                            </label>
                                            @endforeach
                                            <div class="pet-type-error hidden mt-1 text-sm text-red-600">
                                                Please select at least one pet type
                                            </div>
                                        </div>
                                    </div> 
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">
                                            Size Ranges <span class="text-red-500">*</span>
                                            <span class="text-xs font-normal ml-1">(Small: 0-10kg, Medium: 15-30kg, Large: 30+kg)</span>
                                        </label>
                                        <div class="space-y-2 size-range-container">
                                            @foreach(['small', 'medium', 'large'] as $size)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                           name="services[0][size_ranges][]" 
                                                           value="{{ $size }}" 
                                                       class="size-range-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2">{{ ucfirst($size) }}</span>
                                            </label>
                                            @endforeach
                                            <div class="size-range-error hidden mt-1 text-sm text-red-600">
                                                Please select at least one size range
                                            </div>
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
                                        <div class="flex items-center">
                                            <select name="services[0][exotic_pet_species][]" 
                                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline exotic-pet-select"
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
                            id="addServiceButton"
                            onclick="addService()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Another Service
                    </button>
                    <p id="serviceLimit" class="mt-2 text-sm text-gray-500 hidden">
                        You've reached the limit of 3 services for now. You can add more services later in your shop management.
                    </p>
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
    // Initialize the checkboxes for the first service
    initializeRequiredCheckboxes();
    
    // Check initial service count and update UI accordingly
    checkServiceLimit();
    
    // Add service function will need to initialize checkboxes for new services
    const originalAddService = window.addService;
    window.addService = function() {
        originalAddService();
        // Initialize is handled in the modified addService function
    };
    
    // Use delegated event handler for exotic pet checkboxes to handle all services including future ones
    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches('input[name*="exotic_pet_service"]')) {
            const serviceItem = e.target.closest('.service-item');
            const section = serviceItem.querySelector('.exotic-species-section');
            
            if (section) {
                section.style.display = e.target.checked ? 'block' : 'none';
                
                if (e.target.checked) {
                    // Clean up the container before initializing Tom Select
                    const selectElement = section.querySelector('select[name*="exotic_pet_species"]');
                    if (selectElement) {
                        // Remove any existing Tom Select elements
                        const container = selectElement.closest('.exotic-species-container');
                        if (container) {
                            // Remove any Tom Select wrappers
                            container.querySelectorAll('.ts-wrapper').forEach(el => el.remove());
                            
                            // Remove any extraneous input fields
                            container.querySelectorAll('input:not([type="hidden"])').forEach(input => {
                                if (input !== selectElement) {
                                    input.remove();
                                }
                            });
                        }
                        
                        // Ensure the select is visible
                        selectElement.style.display = '';
                        
                        // Initialize Tom Select
                        setTimeout(() => {
                            initializeExoticPetSelect(selectElement);
                        }, 10);
                    }
                }
            }
        }
    });
    
    // Initialize Tom Select for the first service
    const firstServiceExoticPetSelect = document.querySelector('.service-item:first-child select[name*="exotic_pet_species"]');
    initializeExoticPetSelect(firstServiceExoticPetSelect);
    
    // Handle form submission
    document.getElementById('servicesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        let errors = [];
        const services = document.querySelectorAll('.service-item');
        
        // Clear all previous error states
        document.querySelectorAll('.pet-type-error, .size-range-error').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Re-index services to ensure continuous numeric indices (0, 1, 2, etc.)
        services.forEach((service, serviceIndex) => {
            // Set the data-index attribute for proper reference
            service.setAttribute('data-index', serviceIndex);
            
            // Update name attributes to use correct array indices for all form elements
            service.querySelectorAll('[name*="services["]').forEach(input => {
                const newName = input.getAttribute('name').replace(/services\[\d+\]/, `services[${serviceIndex}]`);
                input.setAttribute('name', newName);
            });
        });
        
        // First pass - validate all services without modifying the DOM
        services.forEach((service, serviceIndex) => {
            // Basic validation
            const serviceName = service.querySelector(`input[name="services[${serviceIndex}][name]"]`);
            const serviceCategory = service.querySelector(`select[name="services[${serviceIndex}][category]"]`);
            const petTypesCheckboxes = service.querySelectorAll(`input[name="services[${serviceIndex}][pet_types][]"]:checked`);
            const sizeRangesCheckboxes = service.querySelectorAll(`input[name="services[${serviceIndex}][size_ranges][]"]:checked`);
            
            // Reset error states
            [serviceName, serviceCategory].forEach(field => {
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

            // Validate pet types
            if (petTypesCheckboxes.length === 0) {
                isValid = false;
                errors.push(`Service #${serviceIndex + 1}: At least one Pet Type must be selected`);
                
                // Show the error message for this service
                const petTypeError = service.querySelector('.pet-type-error');
                if (petTypeError) {
                    petTypeError.classList.remove('hidden');
                }
                
                // Add red border to pet type container
                const petTypeContainer = service.querySelector('.pet-type-container');
                if (petTypeContainer) {
                    petTypeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
                }
                
                // Scroll to the error if this is the first one
                if (errors.length === 1) {
                    service.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            // Validate size ranges
            if (sizeRangesCheckboxes.length === 0) {
                isValid = false;
                errors.push(`Service #${serviceIndex + 1}: At least one Size Range must be selected`);
                
                // Show the error message for this service
                const sizeRangeError = service.querySelector('.size-range-error');
                if (sizeRangeError) {
                    sizeRangeError.classList.remove('hidden');
                }
                
                // Add red border to size range container
                const sizeRangeContainer = service.querySelector('.size-range-container');
                if (sizeRangeContainer) {
                    sizeRangeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
                }
                
                // Scroll to the error if this is the first one
                if (errors.length === 1 && !petTypesCheckboxes.length === 0) {
                    service.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            // Validate price and duration
            const basePrice = service.querySelector(`input[name="services[${serviceIndex}][base_price]"]`);
            const duration = service.querySelector(`input[name="services[${serviceIndex}][duration]"]`);

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

        // If the form is valid, submit it normally
        if (isValid) {
            // Log the form data for debugging
            console.log('Submitting form with services', services.length);
            
            // Create the final FormData from the form
            const form = document.getElementById('servicesForm');
            
            // Check pet types and size ranges have values before submitting
            services.forEach((service, serviceIndex) => {
                const petTypeCheckboxes = service.querySelectorAll(`input[name="services[${serviceIndex}][pet_types][]"]:checked`);
                if (petTypeCheckboxes.length === 0) {
                    // If no pet types selected, add a hidden field for default values
                    const hiddenInput1 = document.createElement('input');
                    hiddenInput1.type = 'hidden';
                    hiddenInput1.name = `services[${serviceIndex}][pet_types][]`;
                    hiddenInput1.value = 'dogs';
                    form.appendChild(hiddenInput1);
                    
                    const hiddenInput2 = document.createElement('input');
                    hiddenInput2.type = 'hidden';
                    hiddenInput2.name = `services[${serviceIndex}][pet_types][]`;
                    hiddenInput2.value = 'cats';
                    form.appendChild(hiddenInput2);
                }
                
                const sizeRangeCheckboxes = service.querySelectorAll(`input[name="services[${serviceIndex}][size_ranges][]"]:checked`);
                if (sizeRangeCheckboxes.length === 0) {
                    // If no size ranges selected, add hidden fields for default values
                    const hiddenInput1 = document.createElement('input');
                    hiddenInput1.type = 'hidden';
                    hiddenInput1.name = `services[${serviceIndex}][size_ranges][]`;
                    hiddenInput1.value = 'small';
                    form.appendChild(hiddenInput1);
                    
                    const hiddenInput2 = document.createElement('input');
                    hiddenInput2.type = 'hidden';
                    hiddenInput2.name = `services[${serviceIndex}][size_ranges][]`;
                    hiddenInput2.value = 'medium';
                    form.appendChild(hiddenInput2);
                    
                    const hiddenInput3 = document.createElement('input');
                    hiddenInput3.type = 'hidden';
                    hiddenInput3.name = `services[${serviceIndex}][size_ranges][]`;
                    hiddenInput3.value = 'large';
                    form.appendChild(hiddenInput3);
                }
            });
            
            form.submit();
            return;
        }

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
        } else if (errorContainer) {
            errorContainer.remove();
        }
    });
    
    // Helper function to add a hidden input to a form
    function addHiddenInput(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    // Function to initialize required checkboxes
    function initializeRequiredCheckboxes() {
        // For all services in the form
        document.querySelectorAll('.service-item').forEach((service, serviceIndex) => {
            // Set the proper data-index attribute
            service.setAttribute('data-index', serviceIndex);
            
            // Update all name attributes to ensure correct indexing
            service.querySelectorAll('[name*="services["]').forEach(input => {
                const newName = input.getAttribute('name').replace(/services\[\d+\]/, `services[${serviceIndex}]`);
                input.setAttribute('name', newName);
            });
            
            // Ensure pet_types and size_ranges checkboxes have string values
            service.querySelectorAll('input[name*="pet_types"], input[name*="size_ranges"]').forEach(checkbox => {
                // Add a custom attribute to store the string value
                checkbox.setAttribute('data-string-value', checkbox.value);
                
                // Add change event to ensure string values
                checkbox.addEventListener('change', function() {
                    // This ensures the value is always treated as a string
                    this.value = this.getAttribute('data-string-value');
                });
                
                // Force set the string value
                checkbox.value = checkbox.getAttribute('data-string-value');
            });
            
            // Handle pet type checkboxes
            const petTypeContainer = service.querySelector('.pet-type-container');
            if (petTypeContainer) {
                const petTypeCheckboxes = petTypeContainer.querySelectorAll('.pet-type-checkbox');
                const petTypeError = petTypeContainer.querySelector('.pet-type-error');
                
                // If no checkboxes are selected, show error on page load
                const anyChecked = Array.from(petTypeCheckboxes).some(cb => cb.checked);
                if (!anyChecked) {
                    petTypeError.classList.remove('hidden');
                    petTypeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
                } else {
                    petTypeError.classList.add('hidden');
                    petTypeContainer.classList.remove('border', 'border-red-500', 'rounded-md', 'p-2');
                }
                
                petTypeCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const anyChecked = Array.from(petTypeCheckboxes).some(cb => cb.checked);
                        if (anyChecked) {
                            petTypeError.classList.add('hidden');
                            petTypeContainer.classList.remove('border', 'border-red-500', 'rounded-md', 'p-2');
                        } else {
                            petTypeError.classList.remove('hidden');
                            petTypeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
                        }
                    });
                });
            }
            
            // Handle size range checkboxes
            const sizeRangeContainer = service.querySelector('.size-range-container');
            if (sizeRangeContainer) {
                const sizeRangeCheckboxes = sizeRangeContainer.querySelectorAll('.size-range-checkbox');
                const sizeRangeError = sizeRangeContainer.querySelector('.size-range-error');
                
                // If no checkboxes are selected, show error on page load
                const anyChecked = Array.from(sizeRangeCheckboxes).some(cb => cb.checked);
                if (!anyChecked) {
                    sizeRangeError.classList.remove('hidden');
                    sizeRangeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
                } else {
                    sizeRangeError.classList.add('hidden');
                    sizeRangeContainer.classList.remove('border', 'border-red-500', 'rounded-md', 'p-2');
                }
                
                sizeRangeCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const anyChecked = Array.from(sizeRangeCheckboxes).some(cb => cb.checked);
                        if (anyChecked) {
                            sizeRangeError.classList.add('hidden');
                            sizeRangeContainer.classList.remove('border', 'border-red-500', 'rounded-md', 'p-2');
                        } else {
                            sizeRangeError.classList.remove('hidden');
                            sizeRangeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
                        }
                    });
                });
            }
        });
    }

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
    
    // Check if we've reached the limit of 3 services
    if (serviceCount >= 3) {
        // Show the limit message
        document.getElementById('serviceLimit').classList.remove('hidden');
        
        // Disable the add service button
        const addButton = document.getElementById('addServiceButton');
        addButton.disabled = true;
        addButton.classList.add('opacity-50', 'cursor-not-allowed');
        addButton.classList.remove('hover:bg-gray-50');
        
        return; // Don't add more services
    }
    
    const template = container.children[0].cloneNode(true);
    
    // Set data-index attribute for proper DOM reference
    template.setAttribute('data-index', serviceCount);
    
    // Update all name attributes with new index
    template.querySelectorAll('[name]').forEach(input => {
        const newName = input.name.replace(/services\[\d+\]/, `services[${serviceCount}]`);
        input.name = newName;
        
        if (input.tagName === 'SELECT') {
            // Remove existing Tom Select instance if any
            if (input.tomselect) {
                input.tomselect.destroy();
            }
            input.selectedIndex = -1;
        } else if (input.type !== 'checkbox' && input.type !== 'radio') {
            // Clear value for non-checkbox/radio inputs
            input.value = '';
        }
    });

    // Clear checkboxes
    template.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        
        // For pet types and size ranges, ensure the input has a value attribute
        if (cb.name.includes('pet_types') || cb.name.includes('size_ranges')) {
            const originalValue = cb.value;
            cb.setAttribute('data-original-value', originalValue);
        }
    });

    // Update onclick handlers for dynamic elements
    const variablePricingButton = template.querySelector('button[onclick*="addVariablePricing"]');
    if (variablePricingButton) {
        variablePricingButton.setAttribute('onclick', `addVariablePricing(${serviceCount})`);
    }
    
    const addOnButton = template.querySelector('button[onclick*="addAddOn"]');
    if (addOnButton) {
        addOnButton.setAttribute('onclick', `addAddOn(${serviceCount})`);
    }

    // Reset exotic species section
    const exoticSection = template.querySelector('.exotic-species-section');
    if (exoticSection) {
        exoticSection.style.display = 'none';
        
        // Get the select element
        const exoticSelect = exoticSection.querySelector('select[name*="exotic_pet_species"]');
        if (exoticSelect) {
            // Check if there are any leftover Tom Select elements and remove them
            const container = exoticSelect.closest('.exotic-species-container');
            if (container) {
                // Remove any existing Tom Select elements
                container.querySelectorAll('.ts-wrapper').forEach(el => el.remove());
                
                // Remove any extraneous input fields
                container.querySelectorAll('input:not([type="hidden"])').forEach(input => {
                    if (input !== exoticSelect) {
                        input.remove();
                    }
                });
            }
            
            // Ensure the select element itself is clean and visible
            exoticSelect.style.display = '';
        }
    }

    // Reset variable pricing and add-ons containers
    template.querySelector('.variable-pricing-container').innerHTML = '';
    template.querySelector('.add-ons-container').innerHTML = '';

    // Add a notification for required fields in the new service
    const notification = document.createElement('div');
    notification.className = 'bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4';
    notification.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">New Service Added</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Remember to select at least one Pet Type and one Size Range for this service.</p>
                </div>
                <div class="mt-4">
                    <div class="-mx-2 -my-1.5 flex">
                        <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.parentElement.remove()" class="px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Dismiss
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    template.insertBefore(notification, template.firstChild);

    // Highlight required fields for the new service
    const petTypeContainer = template.querySelector('.pet-type-container');
    const sizeRangeContainer = template.querySelector('.size-range-container');

    if (petTypeContainer) {
        const petTypeError = petTypeContainer.querySelector('.pet-type-error');
        if (petTypeError) {
            petTypeError.classList.remove('hidden');
        }
        petTypeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
    }

    if (sizeRangeContainer) {
        const sizeRangeError = sizeRangeContainer.querySelector('.size-range-error');
        if (sizeRangeError) {
            sizeRangeError.classList.remove('hidden');
        }
        sizeRangeContainer.classList.add('border', 'border-red-500', 'rounded-md', 'p-2');
    }
    
    // Add to container
    container.appendChild(template);
    
    // Re-init exotic pet features for the new service
    // 1. Get the exotic pet checkbox for this service
    const exoticPetCheckbox = template.querySelector('input[name*="exotic_pet_service"]');
    if (exoticPetCheckbox) {
        // 2. Add change event listener directly to this checkbox
        exoticPetCheckbox.addEventListener('change', function() {
            const serviceItem = this.closest('.service-item');
            const section = serviceItem.querySelector('.exotic-species-section');
            if (section) {
                section.style.display = this.checked ? 'block' : 'none';
                
                // Re-initialize the select when shown
                if (this.checked) {
                    const selectElement = section.querySelector('select[name*="exotic_pet_species"]');
                    initializeExoticPetSelect(selectElement);
                }
            }
        });
    }
    
    // Initialize Tom Select for the new service's exotic pet select
    initializeExoticPetSelect(template.querySelector('select[name*="exotic_pet_species"]'));
    
    updateRemoveButtons();
    
    // Scroll to the new service to make it visible
    template.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Re-init the required checkbox handling for this new service
    initializeRequiredCheckboxes();
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
        
        // Re-enable the add service button if we're now below the limit
        const container = document.getElementById('servicesContainer');
        const serviceCount = container.children.length;
        
        if (serviceCount < 3) {
            // Hide the limit message
            document.getElementById('serviceLimit').classList.add('hidden');
            
            // Enable the add button
            const addButton = document.getElementById('addServiceButton');
            addButton.disabled = false;
            addButton.classList.remove('opacity-50', 'cursor-not-allowed');
            addButton.classList.add('hover:bg-gray-50');
        }
    }
}

function initializeExoticPetSelect(selectElement) {
    if (selectElement) {
        // First check if the sibling input field from a previous Tom Select instance exists and remove it
        const parentContainer = selectElement.closest('.exotic-species-container');
        if (parentContainer) {
            // Look for any existing .ts-wrapper elements and remove them
            parentContainer.querySelectorAll('.ts-wrapper').forEach(wrapper => {
                wrapper.remove();
            });
            
            // Look for any extraneous input fields that might have been added by Tom Select
            parentContainer.querySelectorAll('input:not([type="hidden"])').forEach(input => {
                if (input !== selectElement) {
                    input.remove();
                }
            });
        }
        
        // Check if there's an existing Tom Select instance and destroy it
        if (selectElement.tomselect) {
            selectElement.tomselect.destroy();
        }
        
        // Create new Tom Select instance
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

function checkServiceLimit() {
    const container = document.getElementById('servicesContainer');
    const serviceCount = container.children.length;
    
    if (serviceCount >= 3) {
        // Show the limit message
        document.getElementById('serviceLimit').classList.remove('hidden');
        
        // Disable the add service button
        const addButton = document.getElementById('addServiceButton');
        addButton.disabled = true;
        addButton.classList.add('opacity-50', 'cursor-not-allowed');
        addButton.classList.remove('hover:bg-gray-50');
    } else {
        // Hide the limit message
        document.getElementById('serviceLimit').classList.add('hidden');
        
        // Enable the add button
        const addButton = document.getElementById('addServiceButton');
        addButton.disabled = false;
        addButton.classList.remove('opacity-50', 'cursor-not-allowed');
        addButton.classList.add('hover:bg-gray-50');
    }
}
</script>
@endpush 

@if ($errors->any())
    <div class="fixed top-0 right-0 left-0 p-4 bg-red-50 border-b border-red-200 shadow-md z-50">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-start">
            <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                    </div>
                    <div class="mt-4">
                        <div class="-mx-2 -my-1.5 flex">
                            <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.parentElement.remove()" class="px-2 py-1.5 rounded-md text-sm font-medium text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Dismiss
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif 
