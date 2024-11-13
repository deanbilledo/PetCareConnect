@extends('layouts.app')

@section('content')

<section class="grooming-shops my-6">
    <h2 class="relative text-2xl font-semibold mb-4 text-center">
        Most Popular Veterinary Clinics
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Example Shop 1 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/shops/Casadepero.svg') }}" alt="Shop 1" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Veterinary Clinic
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Shop 1</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.5</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">123 Main Street, City</p>
            </div>
        </div>

        <!-- Example Shop 2 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/shops/Pawssible Solutions Veterinary Clinic.svg') }}" alt="Shop 2" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Veterinary Clinic
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Shop 2</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.7</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">456 Oak Avenue, City</p>
            </div>
        </div>

        <!-- Example Shop 3 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/shops/waltacvet.svg') }}" alt="Shop 3" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Veterinary Clinic
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Shop 3</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.8</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">789 Pine Road, City</p>
            </div>
        </div>
    </div>
</section>

@endsection

@section('styles')
<style>
    .grooming-shops h2::before,
    .grooming-shops h2::after {
        content: '';
        display: block;
        width: 30%;
        height: 1px;
        background-color: #e70000; /* Tailwind's gray-300 */
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
    .grooming-shops h2::before {
        left: 0;
    }
    .grooming-shops h2::after {
        right: 0;
    }
</style>
@endsection
