@extends('layouts.app')

@section('content')

<section class="grooming-shops my-6 mb-20">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Grooming
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Example Shop 1 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/shops/josep.svg') }}" alt="Shop 1" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming Shop
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
                    Grooming Shop
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
                    Grooming Shop
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

<section class="grooming-shops my-6 mb-10">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Most Popular Groomers
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Example Shop 1 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl ">
            <div class="relative">
                <img src="{{ asset('images/shops/Casadepero.svg') }}" alt="Shop 1" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Grooming Shop
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
                    Grooming Shop
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
                    Grooming Shop
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

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        You may need 
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Example Shop 1 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl lg:row-span-2">
            <div class="relative">
                <img src="{{ asset('images/shops/Casadepero.svg') }}" alt="Shop 1" class="w-full h-80 object-cover"> <!-- Increased height -->
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

<section class="grooming-shops my-6 mt-40">
    <h2 class="relative text-2xl font-semibold mb-4 text-center flex items-center justify-center">
        <span class="flex-grow border-t border-black mx-2"></span>
        Categories
        <span class="flex-grow border-t border-black mx-2"></span>
    </h2>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Veterinaries</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Example Category 1 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4">
            <div class="relative">
                <img src="{{ asset('images/categories/category1.svg') }}" alt="Category 1" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Category
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Category 1</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.5</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Description of Category 1</p>
            </div>
        </div>

        <!-- Example Category 2 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4">
            <div class="relative">
                <img src="{{ asset('images/categories/category2.svg') }}" alt="Category 2" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Category
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Category 2</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.7</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Description of Category 2</p>
            </div>
        </div>

        <!-- Example Category 3 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl mb-4">
            <div class="relative">
                <img src="{{ asset('images/categories/category3.svg') }}" alt="Category 3" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Category
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Category 3</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.8</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Description of Category 3</p>
            </div>
        </div>
    </div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Grooming</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Example Category 4 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/categories/category4.svg') }}" alt="Category 4" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Category
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Category 4</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.6</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Description of Category 4</p>
            </div>
        </div>

        <!-- Example Category 5 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/categories/category5.svg') }}" alt="Category 5" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Category
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Category 5</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.9</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Description of Category 5</p>
            </div>
        </div>

        <!-- Example Category 6 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl">
            <div class="relative">
                <img src="{{ asset('images/categories/category6.svg') }}" alt="Category 6" class="w-full h-40 object-cover">
                <span class="absolute top-2 left-2 bg-white text-black text-xs font-semibold px-2 py-1 rounded-full">
                    Category
                </span>
                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg">Category 6</h3>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-1 text-gray-600">4.3</span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Description of Category 6</p>
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