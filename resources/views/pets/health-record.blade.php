@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </button>
    </div>

    <!-- Pet Info Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center space-x-4">
            <img src="{{ $pet->profile_photo_url ?? asset('images/default-pet.png') }}" 
                 alt="Pet Photo" 
                 class="w-20 h-20 rounded-full object-cover">
            <div>
                <h1 class="text-2xl font-bold">{{ $pet->name }}'s Health Record</h1>
                <p class="text-gray-600">{{ $pet->breed }} • {{ $pet->type }}</p>
            </div>
        </div>
    </div>

    <!-- Health Records Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Vaccination Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Vaccination History</h2>
                <span class="text-sm text-gray-500">Last Updated: Mar 15, 2024</span>
            </div>
            <div class="space-y-4">
                <!-- Vaccination Entry -->
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Anti-rabies</span>
                        <span class="text-green-600 text-sm">Up to date</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Administered: May 10, 2023</p>
                        <p>Next Due: May 10, 2024</p>
                        <p>Veterinarian: Dr. Smith</p>
                    </div>
                </div>

                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">DHPP</span>
                        <span class="text-green-600 text-sm">Up to date</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Administered: Jun 15, 2023</p>
                        <p>Next Due: Jun 15, 2024</p>
                        <p>Veterinarian: Dr. Johnson</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Medical History</h2>
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Routine Checkup</span>
                        <span class="text-gray-500 text-sm">Mar 1, 2024</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">General health examination</p>
                    <div class="text-sm text-gray-600">
                        <p>Weight: 12.5 kg</p>
                        <p>Temperature: 38.5°C</p>
                        <p>Notes: Healthy condition, no concerns</p>
                    </div>
                </div>

                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Skin Condition</span>
                        <span class="text-gray-500 text-sm">Feb 15, 2024</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Seasonal allergies causing skin irritation</p>
                    <div class="text-sm text-gray-600">
                        <p>Symptoms: Itching, redness</p>
                        <p>Treatment: Prescribed antihistamines</p>
                        <p>Follow-up: None required</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parasite Prevention -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Parasite Prevention</h2>
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Frontline Plus</span>
                        <span class="text-green-600 text-sm">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Last Applied: Mar 15, 2024</p>
                        <p>Next Due: Apr 15, 2024</p>
                        <p>Type: Flea and Tick Prevention</p>
                    </div>
                </div>

                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Heartgard Plus</span>
                        <span class="text-green-600 text-sm">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Last Given: Mar 1, 2024</p>
                        <p>Next Due: Apr 1, 2024</p>
                        <p>Type: Heartworm Prevention</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allergies and Conditions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Allergies & Chronic Conditions</h2>
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Seasonal Allergies</span>
                        <span class="text-yellow-600 text-sm">Managed</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Diagnosed: Jan 2024</p>
                        <p>Triggers: Pollen, Grass</p>
                        <p>Current Treatment: Antihistamines as needed</p>
                    </div>
                </div>

                <div class="border-b pb-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">Food Sensitivity</span>
                        <span class="text-yellow-600 text-sm">Managed</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Diagnosed: Dec 2023</p>
                        <p>Triggers: Chicken products</p>
                        <p>Management: Special diet plan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 