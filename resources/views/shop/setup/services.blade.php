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
            <form method="POST" 
                  action="{{ route('shop.setup.services.store') }}" 
                  x-data="{ 
                      services: [{
                          name: '',
                          category: '',
                          description: '',
                          pet_types: [],
                          size_ranges: [],
                          breedSpecific: false,
                          specialRequirements: '',
                          base_price: '',
                          duration: 15,
                          variablePricing: [],
                          addOns: [],
                          exoticPetService: false,
                          exoticPetSpecies: []
                      }],
                      errors: {},
                      validatePrice(value, fieldName, serviceIndex) {
                          const price = parseFloat(value);
                          if (isNaN(price) || price <= 0) {
                              this.errors[`${fieldName}_${serviceIndex}`] = 'Price must be greater than 0';
                              return false;
                          }
                          delete this.errors[`${fieldName}_${serviceIndex}`];
                          return true;
                      },
                      validateForm() {
                          this.errors = {};
                          let isValid = true;

                          this.services.forEach((service, index) => {
                              // Validate base price
                              if (!this.validatePrice(service.base_price, 'base_price', index)) {
                                  isValid = false;
                              }

                              // Validate variable pricing
                              service.variablePricing.forEach((pricing, priceIndex) => {
                                  if (!this.validatePrice(pricing.price, `variable_price_${priceIndex}`, index)) {
                                      isValid = false;
                                  }
                              });

                              // Validate add-ons
                              service.addOns.forEach((addOn, addOnIndex) => {
                                  if (!this.validatePrice(addOn.price, `addon_price_${addOnIndex}`, index)) {
                                      isValid = false;
                                  }
                              });

                              // Validate required fields
                              if (!service.name.trim()) {
                                  this.errors[`name_${index}`] = 'Service name is required';
                                  isValid = false;
                              }
                              if (!service.category) {
                                  this.errors[`category_${index}`] = 'Category is required';
                                  isValid = false;
                              }
                              if (service.pet_types.length === 0) {
                                  this.errors[`pet_types_${index}`] = 'At least one pet type must be selected';
                                  isValid = false;
                              }
                              if (service.size_ranges.length === 0) {
                                  this.errors[`size_ranges_${index}`] = 'At least one size range must be selected';
                                  isValid = false;
                              }
                          });

                          return isValid;
                      },
                      addService() {
                          this.services.push({
                              name: '',
                              category: '',
                              description: '',
                              pet_types: [],
                              size_ranges: [],
                              breedSpecific: false,
                              specialRequirements: '',
                              base_price: '',
                              duration: 15,
                              variablePricing: [],
                              addOns: [],
                              exoticPetService: false,
                              exoticPetSpecies: []
                          });
                      },
                      removeService(index) {
                          if (this.services.length > 1) {
                              this.services.splice(index, 1);
                          }
                      },
                      addVariablePrice(serviceIndex) {
                          if (!this.services[serviceIndex].variablePricing) {
                              this.services[serviceIndex].variablePricing = [];
                          }
                          this.services[serviceIndex].variablePricing.push({ size: '', price: '' });
                      },
                      addAddOn(serviceIndex) {
                          if (!this.services[serviceIndex].addOns) {
                              this.services[serviceIndex].addOns = [];
                          }
                          this.services[serviceIndex].addOns.push({ name: '', price: '' });
                      }
                  }">
                @csrf

                <div class="px-8 py-6" @submit.prevent="if(validateForm()) $el.submit()">
                    <!-- Service Templates -->
                    <template x-for="(service, index) in services" :key="index">
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg relative">
                            <!-- Remove Button -->
                            <button type="button" 
                                    @click="removeService(index)"
                                    x-show="services.length > 1"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Basic Service Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Service Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Service Name</label>
                                        <input type="text" 
                                               x-model="service.name"
                                               :name="'services[' + index + '][name]'"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Service Category</label>
                                        <select x-model="service.category"
                                                :name="'services[' + index + '][category]'"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                required>
                                            <option value="">Select Category</option>
                                            <option value="grooming">Grooming</option>
                                            <option value="veterinary">Veterinary</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Service Description</label>
                                    <textarea x-model="service.description"
                                            :name="'services[' + index + '][description]'"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            <!-- Service Specifications -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Service Specifications</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Pet Types Supported</label>
                                        <div class="mt-2 space-y-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.pet_types" 
                                                       value="dogs"
                                                       :name="'services[' + index + '][pet_types][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Dogs</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.pet_types" 
                                                       value="cats"
                                                       :name="'services[' + index + '][pet_types][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Cats</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.pet_types" 
                                                       value="birds"
                                                       :name="'services[' + index + '][pet_types][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Birds</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.pet_types" 
                                                       value="rabbits"
                                                       :name="'services[' + index + '][pet_types][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Rabbits</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Size/Weight Ranges</label>
                                        <div class="mt-2 space-y-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.size_ranges" 
                                                       value="small"
                                                       :name="'services[' + index + '][size_ranges][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Small (0-15 kg)</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.size_ranges" 
                                                       value="medium"
                                                       :name="'services[' + index + '][size_ranges][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Medium (15-30 kg)</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="service.size_ranges" 
                                                       value="large"
                                                       :name="'services[' + index + '][size_ranges][]'"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <span class="ml-2">Large (30+ kg)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               x-model="service.exoticPetService"
                                               :name="'services[' + index + '][exoticPetService]'"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <span class="ml-2">Exotic Pet Service</span>
                                    </label>
                                    <p class="mt-1 text-sm text-gray-500">Check this if this service is available for exotic pets (e.g., reptiles, amphibians, small mammals, etc.)</p>
                                </div>

                                <!-- Exotic Pet Species Section -->
                                <div x-show="service.exoticPetService" class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Exotic Pet Species</label>
                                    <div class="mt-2 space-y-2">
                                        <template x-for="(species, speciesIndex) in service.exoticPetSpecies || []" :key="speciesIndex">
                                            <div class="flex items-center space-x-2">
                                                <input type="text" 
                                                       x-model="service.exoticPetSpecies[speciesIndex]"
                                                       :name="'services[' + index + '][exoticPetSpecies][]'"
                                                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                       placeholder="Enter species name">
                                                <button type="button" 
                                                        @click="service.exoticPetSpecies.splice(speciesIndex, 1)"
                                                        class="text-red-600 hover:text-red-800">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button"
                                                @click="service.exoticPetSpecies = service.exoticPetSpecies || []; service.exoticPetSpecies.push('')"
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Add Species
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Special Requirements</label>
                                    <textarea x-model="service.specialRequirements"
                                            :name="'services[' + index + '][specialRequirements]'"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Enter any prerequisites or special requirements..."></textarea>
                                </div>
                            </div>

                            <!-- Pricing Details -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Base Price (₱)</label>
                                        <input type="number" 
                                               x-model="service.base_price"
                                               :name="'services[' + index + '][base_price]'"
                                               min="0.01"
                                               step="0.01"
                                               @input="validatePrice($event.target.value, 'base_price', index)"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                        <div x-show="errors[`base_price_${index}`]" 
                                             x-text="errors[`base_price_${index}`]"
                                             class="mt-1 text-sm text-red-600"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                                        <input type="number" 
                                               x-model="service.duration"
                                               :name="'services[' + index + '][duration]'"
                                               min="15"
                                               step="15"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               required>
                                    </div>
                                </div>

                                <!-- Variable Pricing -->
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium text-gray-700">Variable Pricing</label>
                                        <button type="button"
                                                @click="addVariablePrice(index)"
                                                class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Add Price Variation
                                        </button>
                                    </div>
                                    <template x-for="(price, priceIndex) in service.variablePricing" :key="priceIndex">
                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                            <select x-model="price.size"
                                                    :name="`services[${index}][variable_pricing][${priceIndex}][size]`"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Select Size</option>
                                                <option value="small">Small</option>
                                                <option value="medium">Medium</option>
                                                <option value="large">Large</option>
                                            </select>
                                            <input type="number"
                                                   x-model="price.price"
                                                   :name="`services[${index}][variable_pricing][${priceIndex}][price]`"
                                                   placeholder="Price"
                                                   min="0.01"
                                                   step="0.01"
                                                   @input="validatePrice($event.target.value, `variable_price_${priceIndex}`, index)"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <div x-show="errors[`variable_price_${priceIndex}_${index}`]" 
                                                 x-text="errors[`variable_price_${priceIndex}_${index}`]"
                                                 class="mt-1 text-sm text-red-600"></div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Add-Ons -->
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium text-gray-700">Add-Ons</label>
                                        <button type="button"
                                                @click="addAddOn(index)"
                                                class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Add New Add-On
                                        </button>
                                    </div>
                                    <template x-for="(addOn, addOnIndex) in service.addOns" :key="addOnIndex">
                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                            <input type="text"
                                                   x-model="addOn.name"
                                                   :name="'services[' + index + '][addOns][' + addOnIndex + '][name]'"
                                                   placeholder="Add-on name"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <input type="number"
                                                   x-model="addOn.price"
                                                   :name="`services[${index}][addOns][${addOnIndex}][price]`"
                                                   placeholder="Price"
                                                   min="0.01"
                                                   step="0.01"
                                                   @input="validatePrice($event.target.value, `addon_price_${addOnIndex}`, index)"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <div x-show="errors[`addon_price_${addOnIndex}_${index}`]" 
                                                 x-text="errors[`addon_price_${addOnIndex}_${index}`]"
                                                 class="mt-1 text-sm text-red-600"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Add Service Button -->
                    <button type="button"
                            @click="addService"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="Object.keys(errors).length > 0">
                        Continue
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        services: Array.from(document.querySelectorAll('.service-item')).map((service, index) => ({
                name: formData.get(`services[${index}][name]`),
                category: formData.get(`services[${index}][category]`),
                description: formData.get(`services[${index}][description]`),
                pet_types: Array.from(document.querySelectorAll(`input[name="services[${index}][pet_types][]"]:checked`))
                    .map(input => input.value),
                size_ranges: Array.from(document.querySelectorAll(`input[name="services[${index}][size_ranges][]"]:checked`))
                    .map(input => input.value),
                exotic_pet_service: formData.get(`services[${index}][exotic_pet_service]`) === 'on',
                exotic_pet_species: formData.get(`services[${index}][exotic_pet_species]`) ? Array.from(formData.getAll(`services[${index}][exotic_pet_species][]`)) : [],
                special_requirements: formData.get(`services[${index}][special_requirements]`),
                base_price: parseFloat(formData.get(`services[${index}][base_price]`)),
                duration: parseInt(formData.get(`services[${index}][duration]`)),
                variable_pricing: getVariablePricing(index),
                add_ons: getAddOns(index)
        }))
    };

    // Add class to service item div
    document.querySelector('[x-for="(service, index) in services"]').classList.add('service-item');

    // Submit form normally instead of using fetch
    this.submit();
});

function getVariablePricing(index) {
    const container = document.getElementById(`variablePricingContainer_${index}`);
    if (!container) return [];
    
    return Array.from(container.children).map(row => ({
        size: row.querySelector('select').value,
        price: parseFloat(row.querySelector('input[type="number"]').value)
    })).filter(item => item.size && item.price);
}

function getAddOns(index) {
    const container = document.getElementById(`addOnsContainer_${index}`);
    if (!container) return [];
    
    return Array.from(container.children).map(row => ({
        name: row.querySelector('input[type="text"]').value,
        price: parseFloat(row.querySelector('input[type="number"]').value)
    })).filter(item => item.name && item.price);
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
