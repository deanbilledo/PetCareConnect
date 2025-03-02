@extends('layouts.app')

@section('content')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
@endpush

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
                <div class="w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-1/12 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Base Price</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Duration</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Pet Types</th>
                                <th scope="col" class="w-2/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Variable Pricing</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Add-ons</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Exotic Pets</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Assigned Employees</th>
                                <th scope="col" class="w-1/12 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="w-1/12 relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($services as $service)
                            <tr>
                                <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $service->name }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500">{{ ucfirst($service->category) }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500">₱{{ number_format($service->base_price, 2) }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500">{{ $service->duration }} mins</td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                    <div class="flex flex-wrap gap-1">
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
                                    @if($service->variable_pricing)
                                        @foreach($service->variable_pricing as $pricing)
                                            <div class="mb-1">
                                                <span class="font-medium">{{ ucfirst($pricing['size']) }}:</span>
                                                ₱{{ number_format($pricing['price'], 2) }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500">
                                    @if($service->add_ons)
                                        @foreach($service->add_ons as $addon)
                                            <div class="mb-1">
                                                <span class="font-medium">{{ $addon['name'] }}:</span>
                                                ₱{{ number_format($addon['price'], 2) }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">None</span>
                                    @endif
                                </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        @if($service->exotic_pet_service)
                                        <div class="flex flex-wrap gap-1">
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
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                    <div class="flex flex-wrap gap-1">
                                            @foreach($service->employees as $employee)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $employee->name }}
                                                </span>
                                            @endforeach
                                            @if($service->employees->isEmpty())
                                                <span class="text-gray-500">No employees assigned</span>
                                            @endif
                                        </div>
                                    </td>
                                <td class="px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end space-x-2">
                                            <button onclick="openEditModal({{ $service->id }})" 
                                                class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <button onclick="openDiscountModal({{ $service->id }})" 
                                                class="text-green-600 hover:text-green-900">Add Discount</button>
                                            <button onclick="openDeactivateModal({{ $service->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-900">
                                        {{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                            <button onclick="openDeleteModal({{ $service->id }})" 
                                                class="text-red-600 hover:text-red-900">Delete</button>
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
                                <span class="ml-2 font-medium text-gray-700">Exotic Pet Service</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">Check this if this service is available for exotic pets (e.g., reptiles, amphibians, small mammals, etc.)</p>
                        </div>
                        <div class="exotic-species-section mt-4" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exotic Pet Species</label>
                            <select name="exotic_pet_species[]" 
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    multiple>
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
                                    <option value="tarantula">Tarantula</option>
                                    <option value="scorpion">Scorpion</option>
                                </optgroup>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Select the exotic pet species you can provide services for</p>
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

                    <!-- Employee Assignment Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Assign Employees</h4>
                        <p class="text-sm text-gray-600 mb-4">Select employees who can perform this service</p>
                        
                        <div class="space-y-3">
                            @foreach($employees as $employee)
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" 
                                       name="employee_ids[]" 
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
                                            <a href="{{ route('shop.employees.index') }}" class="font-medium underline text-yellow-600 hover:text-yellow-500">
                                                Add employees first
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentServiceId = null;

    // Initialize form elements only if they exist
    const serviceForm = document.getElementById('serviceForm');
    const exoticPetServiceCheckbox = document.getElementById('exotic_pet_service');
    const exoticPetSpeciesSection = document.querySelector('.exotic-species-section');

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

            // Get selected employee IDs
            const selectedEmployeeIds = Array.from(document.querySelectorAll('input[name="employee_ids[]"]:checked')).map(cb => cb.value);

            // Validate that at least one pet type is selected
            if (selectedPetTypes.length === 0) {
                alert('Please select at least one pet type');
                return;
            }

            // Validate that at least one employee is selected
            if (selectedEmployeeIds.length === 0) {
                alert('Please assign at least one employee to this service');
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
                exotic_pet_species: isExoticService ? document.querySelector('select[name="exotic_pet_species[]"]').tomselect.getValue() : [],
                special_requirements: document.getElementById('special_requirements')?.value || '',
                variable_pricing: getVariablePricing(),
                add_ons: getAddOns(),
                employee_ids: selectedEmployeeIds
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
            
            // Clear TomSelect values when unchecking
            const select = document.querySelector('select[name="exotic_pet_species[]"]');
            if (select && select.tomselect && !this.checked) {
                select.tomselect.clear();
            }
        });
    }

    // Helper function to get species icon
    function getSpeciesIcon(species) {
        const icons = {
            // Reptiles
            'snake': '🐍',
            'lizard': '🦎',
            'turtle': '🐢',
            'iguana': '🦎',
            'gecko': '🦎',
            'bearded_dragon': '🦎',
            // Small Mammals
            'hamster': '🐹',
            'gerbil': '🐹',
            'ferret': '🦡',
            'guinea_pig': '🐹',
            'chinchilla': '🐹',
            'hedgehog': '🦔',
            'sugar_glider': '🦝',
            // Birds
            'parrot': '🦜',
            'cockatiel': '🦜',
            'macaw': '🦜',
            'parakeet': '🦜',
            'lovebird': '🦜',
            // Others
            'tarantula': '🕷️',
            'scorpion': '🦂'
        };
        return icons[species] || '🐾';
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
        
        // Reset exotic pet section
        const exoticPetServiceCheckbox = document.getElementById('exotic_pet_service');
        const exoticPetSpeciesSection = document.querySelector('.exotic-species-section');
        if (exoticPetServiceCheckbox) exoticPetServiceCheckbox.checked = false;
        if (exoticPetSpeciesSection) exoticPetSpeciesSection.style.display = 'none';
        
        // Initialize TomSelect for exotic pet species
        const select = document.querySelector('select[name="exotic_pet_species[]"]');
        if (select) {
            if (select.tomselect) {
                select.tomselect.destroy();
            }
            new TomSelect(select, {
                plugins: ['remove_button'],
                maxItems: null,
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                render: {
                    item: function(data, escape) {
                        return '<div class="py-1">' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        return '<div class="py-2 px-3">' + escape(data.text) + '</div>';
                    },
                    optgroup_header: function(data, escape) {
                        return '<div class="py-2 px-3 font-medium text-gray-700 bg-gray-100">' + escape(data.label) + '</div>';
                    }
                },
                onItemAdd: function() {
                    this.setTextboxValue('');
                    this.refreshOptions(false);
                }
            });
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
        const form = document.getElementById('serviceForm');
        const modal = document.getElementById('serviceModal');
        
        if (!form || !modal) {
            console.error('Required modal elements not found');
            return;
        }
        
        // Set modal title for Edit
        document.getElementById('modalTitle').textContent = 'Edit Service';
        
        // Set the service ID
        document.getElementById('serviceId').value = serviceId;
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) submitButton.disabled = true;
        
        // Debug: Log the request URL and headers
        const url = `/shop/services/${serviceId}`;
        console.log('Fetching service data from:', url);
        
        // Fetch service data with CSRF token
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response:', text);
                    throw new Error(`Network response was not ok: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (!data) {
                throw new Error('No data received');
            }

            // Extract the actual service data from the response
            const serviceData = data.data || data;

            // Fill in basic service information
            form.querySelector('input[name="name"]').value = serviceData.name || '';
            form.querySelector('select[name="category"]').value = serviceData.category || '';
            form.querySelector('textarea[name="description"]').value = serviceData.description || '';
            form.querySelector('input[name="base_price"]').value = serviceData.base_price || '';
            form.querySelector('input[name="duration"]').value = serviceData.duration || '';
            
            // Handle pet types
            const petTypeCheckboxes = form.querySelectorAll('input[name="pet_types[]"]');
            petTypeCheckboxes.forEach(checkbox => {
                checkbox.checked = serviceData.pet_types && serviceData.pet_types.includes(checkbox.value);
            });
            
            // Handle size ranges
            const sizeRangeCheckboxes = form.querySelectorAll('input[name="size_ranges[]"]');
            sizeRangeCheckboxes.forEach(checkbox => {
                checkbox.checked = serviceData.size_ranges && serviceData.size_ranges.includes(checkbox.value);
            });

            // Handle exotic pet service
            const exoticPetServiceCheckbox = form.querySelector('input[name="exotic_pet_service"]');
            const exoticPetSpeciesSection = document.querySelector('.exotic-species-section');
            
            if (exoticPetServiceCheckbox && exoticPetSpeciesSection) {
                exoticPetServiceCheckbox.checked = serviceData.exotic_pet_service || false;
                exoticPetSpeciesSection.style.display = serviceData.exotic_pet_service ? 'block' : 'none';
                
                // Initialize TomSelect for exotic pet species
                const select = form.querySelector('select[name="exotic_pet_species[]"]');
                if (select) {
                    // Destroy existing instance if it exists
                    if (select.tomselect) {
                        select.tomselect.destroy();
                    }

                    // Create new TomSelect instance
                    const tomSelect = new TomSelect(select, {
                        plugins: ['remove_button'],
                        maxItems: null,
                        valueField: 'value',
                        labelField: 'text',
                        searchField: ['text'],
                        render: {
                            item: function(data, escape) {
                                return '<div class="py-1">' + escape(data.text) + '</div>';
                            },
                            option: function(data, escape) {
                                return '<div class="py-2 px-3">' + escape(data.text) + '</div>';
                            },
                            optgroup_header: function(data, escape) {
                                return '<div class="py-2 px-3 font-medium text-gray-700 bg-gray-100">' + escape(data.label) + '</div>';
                            }
                        },
                        onItemAdd: function() {
                            this.setTextboxValue('');
                            this.refreshOptions(false);
                        }
                    });
                    
                    // Set the selected values if they exist
                    if (serviceData.exotic_pet_species && Array.isArray(serviceData.exotic_pet_species)) {
                        tomSelect.setValue(serviceData.exotic_pet_species);
                    }
                }
            }
            
            // Handle variable pricing
            const variablePricingContainer = document.getElementById('variablePricingContainer');
            if (variablePricingContainer) {
                variablePricingContainer.innerHTML = '';
                if (serviceData.variable_pricing && Array.isArray(serviceData.variable_pricing)) {
                    serviceData.variable_pricing.forEach((pricing, index) => {
                        addVariablePricing(); // Add a new row
                        const row = variablePricingContainer.children[index];
                        if (row) {
                            row.querySelector('select').value = pricing.size || '';
                            row.querySelector('input[type="number"]').value = pricing.price || '';
                        }
                    });
                }
            }

            // Handle add-ons
            const addOnsContainer = document.getElementById('addOnsContainer');
            if (addOnsContainer) {
                addOnsContainer.innerHTML = '';
                if (serviceData.add_ons && Array.isArray(serviceData.add_ons)) {
                    serviceData.add_ons.forEach(addOn => {
                        addAddOn(addOn.name || '', addOn.price || '');
                    });
                }
            }

            // Handle employee assignments
            const employeeCheckboxes = form.querySelectorAll('input[name="employee_ids[]"]');
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = serviceData.employee_ids && serviceData.employee_ids.includes(parseInt(checkbox.value));
            });
            
            // Show the modal
            modal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching service data:', error);
            alert('Failed to load service data. Please try again. Error: ' + error.message);
        })
        .finally(() => {
            // Re-enable submit button
            if (submitButton) submitButton.disabled = false;
        });
    };

    // Add modal close function
    window.closeServiceModal = function() {
        const modal = document.getElementById('serviceModal');
        if (modal) {
            modal.classList.add('hidden');
            const form = document.getElementById('serviceForm');
            if (form) form.reset();
            document.getElementById('serviceId').value = '';
            
            // Reset containers
            const variablePricingContainer = document.getElementById('variablePricingContainer');
            const addOnsContainer = document.getElementById('addOnsContainer');
            if (variablePricingContainer) variablePricingContainer.innerHTML = '';
            if (addOnsContainer) addOnsContainer.innerHTML = '';
            
            // Reset exotic pet section
            const exoticPetServiceCheckbox = document.getElementById('exotic_pet_service');
            const exoticPetSpeciesSection = document.querySelector('.exotic-species-section');
            if (exoticPetServiceCheckbox) exoticPetServiceCheckbox.checked = false;
            if (exoticPetSpeciesSection) exoticPetSpeciesSection.style.display = 'none';
        }
    };

    // Add click handler for modal backdrop
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('serviceModal');
        const modalContent = modal?.querySelector('.relative');
        if (modal && !modal.classList.contains('hidden') && modalContent && !modalContent.contains(event.target) && event.target.closest('.fixed.inset-0.bg-black')) {
            closeServiceModal();
        }
    });

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

    // Add the missing addVariablePricing function
    window.addVariablePricing = function() {
        const container = document.getElementById('variablePricingContainer');
        if (!container) return;

        const newRow = document.createElement('div');
        newRow.className = 'variable-pricing-row flex items-center space-x-2 mb-2';
        newRow.innerHTML = `
            <select class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
                <option value="extra_large">Extra Large</option>
            </select>
            <input type="number" 
                   step="0.01" 
                   min="0" 
                   placeholder="Price" 
                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button type="button" 
                    onclick="this.closest('.variable-pricing-row').remove()" 
                    class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        container.appendChild(newRow);
    };

    // Add the missing addAddOn function
    window.addAddOn = function(name = '', price = '') {
        const container = document.getElementById('addOnsContainer');
        if (!container) return;

        const newRow = document.createElement('div');
        newRow.className = 'add-on-row flex items-center space-x-2 mb-2';
        newRow.innerHTML = `
            <input type="text" 
                   value="${name}"
                   placeholder="Add-on Name"
                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <input type="number" 
                   value="${price}"
                   step="0.01" 
                   min="0" 
                   placeholder="Price" 
                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button type="button" 
                    onclick="this.closest('.add-on-row').remove()" 
                    class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        container.appendChild(newRow);
    };

    function getAddOns() {
        const container = document.getElementById('addOnsContainer');
        if (!container) return [];

        const rows = container.querySelectorAll('.add-on-row');
        return Array.from(rows).map(row => ({
            name: row.querySelector('input[type="text"]').value.trim(),
            price: parseFloat(row.querySelector('input[type="number"]').value)
        })).filter(item => item.name && !isNaN(item.price));
    }

    // Add deactivate modal functions
    window.openDeactivateModal = function(serviceId) {
        const modal = document.getElementById('deactivateModal');
        if (modal) {
            document.getElementById('serviceIdToDeactivate').value = serviceId;
            modal.classList.remove('hidden');
        }
    };

    window.closeDeactivateModal = function() {
        const modal = document.getElementById('deactivateModal');
        if (modal) {
            modal.classList.add('hidden');
            document.getElementById('serviceIdToDeactivate').value = '';
        }
    };

    window.confirmToggleStatus = function() {
        const serviceId = document.getElementById('serviceIdToDeactivate').value;
        if (!serviceId) return;

        fetch(`/shop/services/${serviceId}/status`, {
            method: 'PUT',
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
                throw new Error(data.message || 'Failed to update service status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update service status. Please try again.');
        })
        .finally(() => {
            closeDeactivateModal();
        });
    };

    // Add delete modal functions
    window.openDeleteModal = function(serviceId) {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            document.getElementById('serviceIdToDelete').value = serviceId;
            modal.classList.remove('hidden');
        }
    };

    window.closeDeleteModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('hidden');
            document.getElementById('serviceIdToDelete').value = '';
        }
    };

    window.confirmDelete = function() {
        const serviceId = document.getElementById('serviceIdToDelete').value;
        if (!serviceId) return;

        fetch(`/shop/services/${serviceId}`, {
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
                throw new Error(data.message || 'Failed to delete service');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete service. Please try again.');
        })
        .finally(() => {
            closeDeleteModal();
        });
    };

    // Add discount modal functions
    window.openDiscountModal = function(serviceId) {
        const modal = document.getElementById('discountModal');
        if (modal) {
            document.getElementById('serviceIdForDiscount').value = serviceId;
            
            // Reset form
            const form = document.getElementById('discountForm');
            if (form) form.reset();
            
            // Set default dates
            const now = new Date();
            document.getElementById('validFrom').value = now.toISOString().slice(0, 16);
            const nextMonth = new Date(now.setMonth(now.getMonth() + 1));
            document.getElementById('validUntil').value = nextMonth.toISOString().slice(0, 16);
            
            modal.classList.remove('hidden');
        }
    };

    window.closeDiscountModal = function() {
        const modal = document.getElementById('discountModal');
        if (modal) {
            modal.classList.add('hidden');
            document.getElementById('serviceIdForDiscount').value = '';
            const form = document.getElementById('discountForm');
            if (form) form.reset();
        }
    };

    window.generateVoucherCode = function() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('voucherCode').value = code;
    };

    // Add discount form submission handler
    const discountForm = document.getElementById('discountForm');
    if (discountForm) {
        discountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const serviceId = document.getElementById('serviceIdForDiscount').value;
            const formData = {
                discount_type: document.getElementById('discountType').value,
                discount_value: parseFloat(document.getElementById('discountValue').value),
                voucher_code: document.getElementById('voucherCode').value,
                valid_from: document.getElementById('validFrom').value,
                valid_until: document.getElementById('validUntil').value,
                description: document.getElementById('discountDescription').value
            };

            // Validate discount value
            if (isNaN(formData.discount_value) || formData.discount_value <= 0) {
                alert('Please enter a valid discount value');
                return;
            }

            // Validate dates
            if (new Date(formData.valid_until) <= new Date(formData.valid_from)) {
                alert('Valid until date must be after valid from date');
                return;
            }

            // Validate percentage discount
            if (formData.discount_type === 'percentage' && formData.discount_value > 100) {
                alert('Percentage discount cannot be more than 100%');
                return;
            }

            fetch(`/shop/services/${serviceId}/discounts`, {
                method: 'POST',
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
                    throw new Error(data.message || 'Failed to add discount');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to add discount. Please try again.');
            });
        });
    }

    // Update discount help text based on type
    const discountType = document.getElementById('discountType');
    if (discountType) {
        discountType.addEventListener('change', function() {
            const helpText = document.getElementById('discountHelp');
            if (helpText) {
                helpText.textContent = this.value === 'percentage' 
                    ? 'Enter percentage (0-100)' 
                    : 'Enter fixed amount';
            }
        });
    }
});
</script>
@endpush 