@extends('layouts.shop')

@section('content')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
        }
        
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid white;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
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
<<<<<<< HEAD
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openEditModal({{ $service->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button onclick="openDiscountModal({{ $service->id }})" class="text-green-600 hover:text-green-900 mr-3">Add Discount</button>
                                    <button onclick="openDeactivateModal({{ $service->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        {{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button onclick="openDeleteModal({{ $service->id }})" class="text-red-600 hover:text-red-900">Delete</button>
=======
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
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
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

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="spinner"></div>
    <p class="text-xl">Processing your request...</p>
    <p class="text-sm mt-2">Please wait, do not close this page.</p>
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
    let isSubmitting = false; // Track submission state

<<<<<<< HEAD
    // Add price validation function
    function validatePriceFields() {
        const basePrice = parseFloat(document.getElementById('base_price').value);
        if (basePrice <= 0) {
            alert('Base price must be greater than 0');
            return false;
        }

        // Validate variable pricing
        const variablePricingRows = document.querySelectorAll('#variablePricingContainer .variable-pricing-row');
        for (const row of variablePricingRows) {
            const price = parseFloat(row.querySelector('input[type="number"]').value);
            if (price <= 0) {
                alert('Variable pricing amounts must be greater than 0');
                return false;
            }
        }

        // Validate add-ons
        const addOnRows = document.querySelectorAll('#addOnsContainer .add-on-row');
        for (const row of addOnRows) {
            const price = parseFloat(row.querySelector('input[type="number"]').value);
            if (price <= 0) {
                alert('Add-on prices must be greater than 0');
                return false;
            }
        }

        return true;
    }

    // Update the form submission handler
=======
    // Function to show loading overlay
    function showLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    // Function to hide loading overlay
    function hideLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    // Initialize form elements only if they exist
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
    const serviceForm = document.getElementById('serviceForm');
    const exoticPetServiceCheckbox = document.getElementById('exotic_pet_service');
    const exoticPetSpeciesSection = document.querySelector('.exotic-species-section');

    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
<<<<<<< HEAD
            // Validate prices before submission
            if (!validatePriceFields()) {
                return;
            }

            // Get all form data
=======
            // Prevent duplicate submissions
            if (isSubmitting) {
                console.log('Form submission in progress, please wait...');
                return;
            }
            
            isSubmitting = true;
            showLoadingOverlay();
            
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
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }

            // Validate that at least one employee is selected
            if (selectedEmployeeIds.length === 0) {
                alert('Please assign at least one employee to this service');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
            const formData = {
                name: document.getElementById('name').value.trim(),
                category: document.getElementById('category').value,
                description: document.getElementById('description').value.trim(),
<<<<<<< HEAD
                pet_types: Array.from(document.querySelectorAll('input[name="pet_types[]"]:checked')).map(cb => cb.value),
                size_ranges: Array.from(document.querySelectorAll('input[name="size_ranges[]"]:checked')).map(cb => cb.value),
                breed_specific: document.querySelector('input[name="breed_specific"]').checked,
                special_requirements: document.getElementById('special_requirements').value.trim(),
                base_price: parseFloat(document.getElementById('base_price').value),
                duration: parseInt(document.getElementById('duration').value),
                variable_pricing: getVariablePricing(),
                add_ons: JSON.stringify(getAddOns())
=======
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
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
            };

            // Validate required fields
            if (!formData.name) {
                alert('Service name is required');
<<<<<<< HEAD
                return;
            }

            if (!formData.category) {
                alert('Please select a category');
                return;
            }

            if (formData.pet_types.length === 0) {
                alert('Please select at least one pet type');
                return;
            }

            if (formData.size_ranges.length === 0) {
                alert('Please select at least one size range');
                return;
            }

            if (isNaN(formData.duration) || formData.duration < 15) {
                alert('Duration must be at least 15 minutes');
                return;
            }

            // Log the data being sent for debugging
            console.log('Sending data:', formData);

            const url = currentServiceId ? `/shop/services/${currentServiceId}` : '/shop/services';
            const method = currentServiceId ? 'PUT' : 'POST';
=======
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            if (!formData.category) {
                alert('Category is required');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            if (isNaN(formData.base_price) || formData.base_price <= 0) {
                alert('Please enter a valid base price');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            if (isNaN(formData.duration) || formData.duration < 15) {
                alert('Please enter a valid duration (minimum 15 minutes)');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }
            if (formData.size_ranges.length === 0) {
                alert('Please select at least one size range');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }

            // Add validation for exotic pet species
            if (formData.exotic_pet_service && formData.exotic_pet_species.length === 0) {
                alert('Please add at least one exotic pet species when exotic pet service is enabled');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }

            const serviceId = document.getElementById('serviceId').value;
            const url = serviceId ? `/shop/services/${serviceId}` : '/shop/services';
            const method = serviceId ? 'PUT' : 'POST';

            // Disable form while submitting
            const submitButton = serviceForm.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = true;
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455

            // Show loading state
            const submitButton = serviceForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
<<<<<<< HEAD
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save service');
                }
                return data;
            })
=======
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
                hideLoadingOverlay();
                isSubmitting = false;
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
            
            // Reset submission state
            isSubmitting = false;
            hideLoadingOverlay();
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
            
            // Reset submission state
            isSubmitting = false;
            hideLoadingOverlay();
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
            
            // Prevent duplicate submissions
            if (isSubmitting) {
                console.log('Form submission in progress, please wait...');
                return;
            }
            
            isSubmitting = true;
            showLoadingOverlay();
            
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
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }

            // Validate dates
            if (new Date(formData.valid_until) <= new Date(formData.valid_from)) {
                alert('Valid until date must be after valid from date');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }

            // Validate percentage discount
            if (formData.discount_type === 'percentage' && formData.discount_value > 100) {
                alert('Percentage discount cannot be more than 100%');
                hideLoadingOverlay();
                isSubmitting = false;
                return;
            }

            // Disable submit button
            const submitButton = discountForm.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = true;

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
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to add discount');
                }
            })
            .catch(error => {
                console.error('Error:', error);
<<<<<<< HEAD
                alert(error.message || 'Failed to save service. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
=======
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: error.message || 'Failed to add discount. Please try again.'
                    }
                }));
                hideLoadingOverlay();
                isSubmitting = false;
                if (submitButton) submitButton.disabled = false;
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
            });
        });
    }

<<<<<<< HEAD
    // Update getVariablePricing function to better handle validation
    window.getVariablePricing = function() {
        const container = document.getElementById('variablePricingContainer');
        const pricing = Array.from(container.children).map(row => {
            const size = row.querySelector('select').value;
            const price = parseFloat(row.querySelector('input[type="number"]').value);
            
            if (!size || isNaN(price) || price <= 0) {
                return null;
            }
            
            return { size, price };
        }).filter(item => item !== null);

        return pricing;
    }

    // Update getAddOns function to ensure proper format
    window.getAddOns = function() {
        const container = document.getElementById('addOnsContainer');
        if (!container) return [];

        const addOns = Array.from(container.children).map(row => {
            const name = row.querySelector('input[type="text"]').value.trim();
            const price = parseFloat(row.querySelector('input[type="number"]').value);
            
            if (!name || isNaN(price) || price <= 0) {
                return null;
            }
            
            return { name, price };
        }).filter(item => item !== null);

        return addOns;
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
                <input type="number" step="0.01" min="0.01" name="variable_pricing[${index}][price]" placeholder="Price" 
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700"
                       oninput="this.value = this.value <= 0 ? '' : this.value">
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
                <input type="number" step="0.01" min="0.01" name="add_ons[${index}][price]" placeholder="Price" 
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700"
                       oninput="this.value = this.value <= 0 ? '' : this.value">
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
        
        fetch(`/shop/services/${serviceId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load service');
                }

                const service = data.data;

                // Clear existing variable pricing and add-ons
                document.getElementById('variablePricingContainer').innerHTML = '';
                document.getElementById('addOnsContainer').innerHTML = '';

                // Populate basic fields
                document.getElementById('name').value = service.name || '';
                document.getElementById('category').value = service.category || '';
                document.getElementById('description').value = service.description || '';
                document.getElementById('base_price').value = service.base_price || '';
                document.getElementById('duration').value = service.duration || '';
                document.getElementById('special_requirements').value = service.special_requirements || '';

                // Set pet types
                document.querySelectorAll('input[name="pet_types[]"]').forEach(cb => {
                    cb.checked = (service.pet_types || []).includes(cb.value);
                });

                // Set size ranges
                document.querySelectorAll('input[name="size_ranges[]"]').forEach(cb => {
                    cb.checked = (service.size_ranges || []).includes(cb.value);
                });

                // Set breed specific
                document.querySelector('input[name="breed_specific"]').checked = service.breed_specific || false;

                // Add variable pricing rows
                if (service.variable_pricing && Array.isArray(service.variable_pricing)) {
                    service.variable_pricing.forEach(pricing => {
                        addVariablePricing();
                        const lastRow = document.querySelector('#variablePricingContainer .variable-pricing-row:last-child');
                        if (lastRow) {
                            lastRow.querySelector('select').value = pricing.size;
                            lastRow.querySelector('input[type="number"]').value = pricing.price;
                        }
                    });
                }

                // Add add-ons rows
                if (service.add_ons && Array.isArray(service.add_ons)) {
                    service.add_ons.forEach(addon => {
                        addAddOn();
                        const lastRow = document.querySelector('#addOnsContainer .add-on-row:last-child');
                        if (lastRow) {
                            lastRow.querySelector('input[type="text"]').value = addon.name;
                            lastRow.querySelector('input[type="number"]').value = addon.price;
                        }
                    });
                }

                // Show modal
                document.getElementById('serviceModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching service:', error);
                alert('Failed to load service details. Please try again.');
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
=======
    // Update discount help text based on type
    const discountType = document.getElementById('discountType');
    if (discountType) {
        discountType.addEventListener('change', function() {
            const helpText = document.getElementById('discountHelp');
            if (helpText) {
                helpText.textContent = this.value === 'percentage' 
                    ? 'Enter percentage (0-100)' 
                    : 'Enter fixed amount';
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
            }
        });
    }
<<<<<<< HEAD

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

    window.openDiscountModal = function(serviceId) {
        document.getElementById('serviceIdForDiscount').value = serviceId;
        document.getElementById('discountModal').classList.remove('hidden');
        
        // Set default dates
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        document.getElementById('validFrom').value = now.toISOString().slice(0, 16);
        document.getElementById('validUntil').value = tomorrow.toISOString().slice(0, 16);
    }

    window.closeDiscountModal = function() {
        document.getElementById('discountModal').classList.add('hidden');
        document.getElementById('discountForm').reset();
    }

    // Add event listener for discount type change
    document.getElementById('discountType').addEventListener('change', function(e) {
        const helpText = document.getElementById('discountHelp');
        if (e.target.value === 'percentage') {
            document.getElementById('discountValue').max = 100;
            helpText.textContent = 'Enter percentage (0-100)';
        } else {
            document.getElementById('discountValue').removeAttribute('max');
            helpText.textContent = 'Enter fixed amount';
        }
    });

    // Add event listener for discount form submission
    document.getElementById('discountForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const serviceId = document.getElementById('serviceIdForDiscount').value;
        const formData = {
            service_id: serviceId,
            discount_type: document.getElementById('discountType').value,
            discount_value: document.getElementById('discountValue').value,
            valid_from: document.getElementById('validFrom').value,
            valid_until: document.getElementById('validUntil').value,
            description: document.getElementById('discountDescription').value
        };

        // Add your API call here to save the discount
        console.log('Discount data:', formData);
        alert('Discount added successfully!'); // Replace with actual API call
        closeDiscountModal();
    });

    // Close modal when clicking outside
    document.getElementById('discountModal').addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('fixed')) {
            closeDiscountModal();
        }
    });

    window.openDeactivateModal = function(serviceId) {
        document.getElementById('serviceIdToDeactivate').value = serviceId;
        document.getElementById('deactivateModal').classList.remove('hidden');
    }

    window.closeDeactivateModal = function() {
        document.getElementById('deactivateModal').classList.add('hidden');
        document.getElementById('serviceIdToDeactivate').value = '';
    }

    window.confirmToggleStatus = function() {
        const serviceId = document.getElementById('serviceIdToDeactivate').value;
        const button = document.querySelector(`button[onclick="openDeactivateModal(${serviceId})"]`);
        
        if (button) {
            button.disabled = true;
        }

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
                throw new Error(data.message || 'Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update service status. Please try again.');
            if (button) {
                button.disabled = false;
            }
        });
    }

    window.openDeleteModal = function(serviceId) {
        document.getElementById('serviceIdToDelete').value = serviceId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    window.closeDeleteModal = function() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('serviceIdToDelete').value = '';
    }

    window.confirmDelete = function() {
        const serviceId = document.getElementById('serviceIdToDelete').value;
        const button = document.querySelector(`button[onclick="openDeleteModal(${serviceId})"]`);
        
        if (button) {
            button.disabled = true;
        }

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
                throw new Error(data.message || 'Failed to delete service');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete service. Please try again.');
            if (button) {
                button.disabled = false;
            }
        });
    }

    // Add click handlers to close modals when clicking outside
    document.getElementById('deactivateModal').addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('fixed')) {
            closeDeactivateModal();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('fixed')) {
            closeDeleteModal();
        }
    });
=======
>>>>>>> 18c55ca6c082561862df75df4fcb286b2bb43455
});
</script>
@endpush 