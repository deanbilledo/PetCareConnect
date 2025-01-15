@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Grooming Shops</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Grooming shop cards will go here -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ asset('images/shops/Josephgroom.svg') }}" alt="Joseph's Pets Grooming" class="w-full h-48 object-cover">
            <div class="p-4">
                <h2 class="text-xl font-semibold mb-2">Joseph's Pets Grooming Services</h2>
                <div class="flex items-center mb-2">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.6</span>
                </div>
                <p class="text-gray-600">W3FJ+7CR, Zamboanga, Zamboanga del Sur</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ asset('images/shops/pawsnclaws.svg') }}" alt="Paws and Claws" class="w-full h-48 object-cover">
            <div class="p-4">
                <h2 class="text-xl font-semibold mb-2">Paws and Claws</h2>
                <div class="flex items-center mb-2">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.9</span>
                </div>
                <p class="text-gray-600">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ asset('images/shops/168grooming.svg') }}" alt="168 Pet Shop" class="w-full h-48 object-cover">
            <div class="p-4">
                <h2 class="text-xl font-semibold mb-2">168 Pet Shop</h2>
                <div class="flex items-center mb-2">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.3</span>
                </div>
                <p class="text-gray-600">Don Alfaro St, Zamboanga, Zamboanga del Sur</p>
            </div>
        </div>
    </div>
</div>
@endsection 