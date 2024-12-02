@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('profile.index') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Profile
        </a>
    </div>

    <!-- Pet Profile Header -->
    <div class="flex flex-col items-center mb-8">
        <div class="relative inline-block">
            <img src="{{ $pet->profile_photo_url ?? asset('images/default-pet.png') }}" 
                 alt="Pet Photo" 
                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                 onerror="this.src='{{ asset('images/default-pet.png') }}'">
            <form action="{{ route('profile.pets.update-photo', $pet) }}" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                @csrf
                <label for="pet_photo" class="cursor-pointer bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 border border-gray-200 flex items-center justify-center w-8 h-8">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input type="file" id="pet_photo" name="pet_photo" class="hidden" onchange="this.form.submit()">
            </form>
        </div>
        <h1 class="text-3xl font-bold mt-4">{{ $pet->name }}</h1>
        <p class="text-gray-600">{{ $pet->breed }} • {{ $pet->type }}</p>
    </div>

    <!-- Basic Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Basic Information</h2>
            <button class="text-teal-500 hover:text-teal-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Date of Birth</p>
                <p class="font-medium">
                    {{ $pet->date_of_birth ? $pet->date_of_birth->format('M d, Y') : 'Not set' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Age</p>
                <p class="font-medium">
                    @php
                        $birthDate = $pet->date_of_birth;
                        if ($birthDate) {
                            $now = now();
                            $years = (int)$birthDate->diffInYears($now);
                            $totalMonths = (int)$birthDate->diffInMonths($now);
                            $months = (int)($totalMonths % 12);  // Get remaining months after years
                            $days = (int)$birthDate->copy()->addMonths($totalMonths)->diffInDays($now);
                            
                            if ($years >= 1) {
                                echo $years . ' ' . Str::plural('year', $years);
                                if ($months > 0) {
                                    echo ' and ' . $months . ' ' . Str::plural('month', $months);
                                }
                                echo ' old';
                            } else {
                                if ($months > 0) {
                                    echo $months . ' ' . Str::plural('month', $months);
                                    if ($days > 0) {
                                        echo ' and ' . $days . ' ' . Str::plural('day', $days);
                                    }
                                    echo ' old';
                                } else {
                                    $days = (int)$birthDate->diffInDays($now);
                                    echo $days . ' ' . Str::plural('day', $days) . ' old';
                                }
                            }
                        } else {
                            echo 'Not set';
                        }
                    @endphp
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Weight</p>
                <p class="font-medium">{{ $pet->weight ?? 'Not set' }} {{ $pet->weight ? 'kg' : '' }}</p>
            </div>
        </div>

        <!-- Additional Pet Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <p class="text-sm text-gray-600">Size Category</p>
                <p class="font-medium">{{ $pet->size_category ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Color/Markings</p>
                <p class="font-medium">{{ $pet->color_markings ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Coat Type</p>
                <p class="font-medium">{{ $pet->coat_type ?? 'Not set' }}</p>
            </div>
        </div>
    </div>

    <!-- Health Records Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Health Records</h2>
            <div class="flex gap-4">
                <a href="{{ route('profile.pets.health-record', $pet) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    View Health Record
                </a>
                <a href="{{ route('profile.pets.add-health-record', $pet) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    Add Health Record
                </a>
            </div>
        </div>

        <!-- Vaccination History -->
        <div class="mb-8">
            <h3 class="text-lg font-medium mb-4">Vaccination History</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <div>
                        <p class="font-medium">Anti-rabies</p>
                        <p class="text-sm text-gray-600">Administered by: Dr. Smith</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Date: May 10, 2023</p>
                        <p class="text-sm text-teal-600">Next due: May 10, 2024</p>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">DHPP</p>
                        <p class="text-sm text-gray-600">Administered by: Dr. Johnson</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Date: Jun 15, 2023</p>
                        <p class="text-sm text-teal-600">Next due: Jun 15, 2024</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parasite Control -->
        <div class="mb-8">
            <h3 class="text-lg font-medium mb-4">Parasite Control</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">Frontline Plus</p>
                        <p class="text-sm text-gray-600">Flea and Tick Prevention</p>
                    </div>
                    <p class="text-sm text-gray-600">Last treatment: Mar 15, 2024</p>
                </div>
            </div>
        </div>

        <!-- Health Issues -->
        <div>
            <h3 class="text-lg font-medium mb-4">Health Issues</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="border-b pb-4">
                    <div class="flex justify-between items-start mb-2">
                        <p class="font-medium">Skin Allergies</p>
                        <p class="text-sm text-gray-600">Feb 15, 2024</p>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Seasonal allergies causing skin irritation</p>
                    <p class="text-sm"><span class="text-gray-600">Treatment:</span> Prescribed antihistamines</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Visits Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Recent Visits</h2>
        </div>
        <div class="space-y-4">
            <div class="flex items-center space-x-4 border-b pb-4">
                <img src="{{ asset('images/shops/shop1.png') }}" alt="Paws & Claws" 
                     class="w-16 h-16 object-cover rounded-lg">
                <div class="flex-1">
                    <h3 class="font-medium">Paws & Claws</h3>
                    <p class="text-sm text-gray-600">Regular Fur Care</p>
                    <p class="text-sm text-gray-500">Mar 25, 2024</p>
                </div>
                <div class="text-yellow-400 flex items-center">
                    <span>★</span>
                    <span class="ml-1 text-gray-600">4.5</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 