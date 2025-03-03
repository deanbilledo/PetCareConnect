@extends('layouts.app')

@section('content')

<section class="grooming-shops my-6 mb-20 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Grooming Shops
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Example Shop 1 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/Josephgroom.svg') }}" alt="Shop 1" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">Joseph's Pets Grooming Services Dog & Cat</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.6</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">W3FJ+7CR, Zamboanga, Zamboanga del Sur</p>
                </div>
            </div>
        </div>

        <!-- Example Shop 2 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/pawsnclaws.svg') }}" alt="Shop 2" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">Paws and Claws</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.9</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
                </div>
            </div>
        </div>

        <!-- Example Shop 3 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/168grooming.svg') }}" alt="Shop 3" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">168 Pet shop</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.3</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">Don Alfaro St, Zamboanga, Zamboanga del Sur</p>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- Grooming Services --}}

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Grooming Services
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Service 1 -->
        <a href="#" class="relative block overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl lg:row-span-2">
            <img src="{{ asset('images/shops/Josephgroom.svg') }}" alt="Shop 1" class="w-full h-80 object-cover">
            <span class="absolute bottom-2 left-2 bg-white bg-opacity-50 text-black text-lg font-semibold px-2 py-1 rounded-full">
                Full Grooming Package
            </span>
        </a>

        <!-- Service 2 -->
        <a href="#" class="relative block overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <img src="{{ asset('images/shops/pawsnclaws.svg') }}" alt="Shop 2" class="w-full h-40 object-cover">
            <span class="absolute bottom-2 left-2 bg-white bg-opacity-50 text-black text-lg font-semibold px-2 py-1 rounded-full">
                Bathing & Styling
            </span>
        </a>

        <!-- Service 3 -->
        <a href="#" class="relative block overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <img src="{{ asset('images/shops/168grooming.svg') }}" alt="Shop 3" class="w-full h-40 object-cover">
            <span class="absolute bottom-2 left-2 bg-white bg-opacity-50 text-black text-lg font-semibold px-2 py-1 rounded-full">
                Nail Trimming & Ear Cleaning
            </span>
        </a>
    </div>
</section>

{{-- Grooming Categories --}}

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Grooming Specialists
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Dog Grooming</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Dog Grooming 1 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/Josephgroom.svg') }}" alt="Category 1" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Dog Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">Joseph's Pets Grooming Services Dog & Cat</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.6</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">W3FJ+7CR, Zamboanga, Zamboanga del Sur</p>
                </div>
            </div>
        </div>

        <!-- Dog Grooming 2 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/pawsnclaws.svg') }}" alt="Category 2" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Dog Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">Paws and Claws</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.9</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
                </div>
            </div>
        </div>

        <!-- Dog Grooming 3 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/168grooming.svg') }}" alt="Category 3" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Dog Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">168 Pet shop</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.3</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">Don Alfaro St, Zamboanga, Zamboanga del Sur</p>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Cat Grooming</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Cat Grooming 1 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/Josephgroom.svg') }}" alt="Category 4" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Cat Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">Joseph's Pets Grooming Services Dog & Cat</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.6</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">W3FJ+7CR, Zamboanga, Zamboanga del Sur</p>
                </div>
            </div>
        </div>

        <!-- Cat Grooming 2 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/pawsnclaws.svg') }}" alt="Category 5" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Cat Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">Paws and Claws</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.9</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
                </div>
            </div>
        </div>

        <!-- Cat Grooming 3 -->
        <div class="rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative h-64">
                <img src="{{ asset('images/shops/168grooming.svg') }}" alt="Category 6" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Cat Grooming
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent text-white">
                    <h3 class="font-semibold text-lg">168 Pet shop</h3>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="ml-1 text-gray-200">4.3</span>
                    </div>
                    <p class="text-gray-200 text-sm mt-1">Don Alfaro St, Zamboanga, Zamboanga del Sur</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('styles')
<style>
    .veterinarian-box {
        margin-bottom: 20px; /* Adjust the value as needed */
    }
    body {
        min-height: 300vh; /* Adjust the value as needed */
    }
</style>
@endsection
