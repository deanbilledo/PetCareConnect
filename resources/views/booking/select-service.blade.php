@php
use Illuminate\Support\Facades\Log;
@endphp
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="mb-4 mt-8">
        <form action="{{ route('booking.process', $shop) }}" method="GET" id="backForm">
            <a href="javascript:void(0)" 
               onclick="document.getElementById('backForm').submit()"
               class="text-gray-600 hover:text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </form>
    </div>

    <!-- Shop Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center">
            <img src="{{ $shop->image ? asset('storage/' . $shop->image) : asset('images/default-shop.png') }}" 
                 alt="{{ $shop->name }}" 
                 class="w-20 h-20 object-cover rounded-lg mr-4">
            <div>
                <h1 class="text-xl font-bold">{{ $shop->name }}</h1>
                <p class="text-gray-600">{{ $shop->address }}</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Appointment Process</h2>
        <div class="flex justify-between items-center relative">
            <!-- Progress Line -->
            <div class="absolute left-0 right-0 top-1/2 h-0.5 bg-gray-200 -z-10">
                <div class="w-1/3 h-full bg-blue-500"></div>
            </div>
            
            <!-- Steps -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-blue-500 font-medium">Select Service</span>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Choose Date</span>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Confirm</span>
            </div>
        </div>
    </div>

    <!-- Service Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Select Services for Your Pets</h2>

        <form action="{{ route('booking.select-datetime', $shop) }}" method="POST">
            @csrf
            
            <!-- Hidden pet IDs -->
            @foreach($pets as $pet)
                <input type="hidden" name="pet_ids[]" value="{{ $pet->id }}">
            @endforeach

            <!-- Hidden appointment type -->
            <input type="hidden" name="appointment_type" value="{{ $bookingData['appointment_type'] ?? request('appointment_type') }}">

            <!-- Service selection for each pet -->
            @foreach($pets as $index => $pet)
                <div class="mb-6 pb-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                    <h3 class="font-medium mb-4">
                        Services for {{ $pet->name }} 
                        ({{ ucfirst($pet->type) }}{{ $pet->type === 'Exotic' ? ' - ' . $pet->species : '' }})
                    </h3>
                    
                    @php
                        $petType = strtolower($pet->type);
                        
                        // Debug log for pet and service data
                        Log::info("Processing services for pet:", [
                            'pet_name' => $pet->name,
                            'pet_type' => $petType,
                            'pet_species' => $pet->species ?? 'N/A'
                        ]);

                        // Filter available services based on pet type
                        $availableServices = $services->filter(function($service) use ($pet, $petType) {
                            // Get service pet types and convert to lowercase
                            $servicePetTypes = collect($service->pet_types)->map(function($type) {
                                return strtolower(trim($type));
                            })->toArray();

                            // Debug log the actual values being compared
                            Log::info("Checking service availability:", [
                                'service_name' => $service->name,
                                'service_pet_types' => $servicePetTypes,
                                'checking_for_pet_type' => $petType,
                                'raw_pet_types' => $service->pet_types,
                                'pet_name' => $pet->name,
                                'pet_type_raw' => $pet->type
                            ]);
                            
                            // For exotic pets
                            if ($petType === 'exotic') {
                                $isAvailable = $service->exotic_pet_service && 
                                             in_array($pet->species, $service->exotic_pet_species ?? []);
                                
                                Log::info("Exotic pet check result:", [
                                    'is_available' => $isAvailable,
                                    'service_name' => $service->name,
                                    'exotic_service' => $service->exotic_pet_service,
                                    'species_match' => in_array($pet->species, $service->exotic_pet_species ?? [])
                                ]);
                                
                                return $isAvailable;
                            }
                            
                            // For regular pets - check if service supports this pet type
                            // Check both singular and plural forms of pet type
                            $singularType = rtrim($petType, 's'); // Remove 's' if present (e.g., "dogs" -> "dog")
                            $pluralType = $petType . 's'; // Add 's' if not present (e.g., "dog" -> "dogs")
                            
                            $isAvailable = in_array($petType, $servicePetTypes) || 
                                         in_array($singularType, $servicePetTypes) || 
                                         in_array($pluralType, $servicePetTypes);
                            
                            Log::info("Regular pet check result:", [
                                'is_available' => $isAvailable,
                                'service_name' => $service->name,
                                'pet_type' => $petType,
                                'singular_type' => $singularType,
                                'plural_type' => $pluralType,
                                'service_pet_types' => $servicePetTypes
                            ]);
                            
                            return $isAvailable;
                        });

                        // Log the final available services
                        Log::info('Final available services for ' . $pet->name . ':', [
                            'pet_type' => $petType,
                            'services_found' => $availableServices->count(),
                            'service_names' => $availableServices->pluck('name')->toArray(),
                            'service_pet_types' => $availableServices->pluck('pet_types')->toArray()
                        ]);
                    @endphp

                    <div class="space-y-4">
                        @if($availableServices->isEmpty())
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-yellow-700">
                                    No services available for {{ ucfirst($petType) }}{{ $petType === 'exotic' ? ' - ' . $pet->species : '' }}. 
                                    Please contact the shop for more information.
                                </p>
                            </div>
                        @else
                            @foreach($availableServices as $service)
                                <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-start">
                                        <input type="radio" 
                                               id="service_{{ $pet->id }}_{{ $service->id }}" 
                                               name="services[{{ $pet->id }}]" 
                                               value="{{ $service->id }}"
                                               required
                                               class="mt-1 mr-3">
                                        <label for="service_{{ $pet->id }}_{{ $service->id }}" class="flex-grow">
                                            <div class="flex justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium">{{ $service->name }}</span>
                                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                                            {{ ucfirst($service->category) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $service->description }}</p>
                                                    <p class="text-sm text-gray-500 mt-2">Duration: {{ $service->duration }} minutes</p>
                                                </div>
                                                <div class="text-right">
                                                    @php
                                                        $petSize = strtolower($pet->size_category ?? 'medium');
                                                        $price = $service->base_price;
                                                        
                                                        if ($service->variable_pricing) {
                                                            foreach ($service->variable_pricing as $pricing) {
                                                                if (strtolower($pricing['size']) === $petSize) {
                                                                    $price = $pricing['price'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <span class="font-medium text-lg">₱{{ number_format($price, 2) }}</span>
                                                    <p class="text-xs text-gray-500">
                                                        @if($service->variable_pricing)
                                                            Price for {{ ucfirst($petSize) }} size
                                                        @else
                                                            Base price
                                                        @endif
                                                    </p>
                                                    
                                                    @if($service->variable_pricing)
                                                        <div class="mt-2 text-xs text-gray-500">
                                                            <span class="font-medium">Other sizes:</span>
                                                            <ul class="mt-1">
                                                                @foreach($service->variable_pricing as $pricing)
                                                                    @if(strtolower($pricing['size']) !== $petSize)
                                                                        <li>{{ ucfirst($pricing['size']) }}: ₱{{ number_format($pricing['price'], 2) }}</li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    @if(!empty($service->add_ons))
                                                        <div class="mt-3 text-xs text-gray-500">
                                                            <span class="font-medium">Available Add-ons:</span>
                                                            <ul class="mt-1">
                                                                @foreach($service->add_ons as $addOn)
                                                                    <li class="flex items-center mt-1">
                                                                        <input type="checkbox" 
                                                                               name="add_ons[{{ $pet->id }}][{{ $service->id }}][]" 
                                                                               value="{{ $addOn['name'] }}"
                                                                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                                        <span>{{ $addOn['name'] }}: ₱{{ number_format($addOn['price'], 2) }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @else
                                                        <div class="mt-3 text-xs text-gray-500">
                                                            <span class="font-medium">Add-ons:</span>
                                                            <span class="ml-1">None available</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Next Button -->
            <div class="mt-6">
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                    Next
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 