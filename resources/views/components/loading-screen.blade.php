@props([
    'fullScreen' => false,
    'message' => 'Loading...',
    'subMessage' => 'Please wait while we process your request',
    'size' => 'md' // sm, md, lg, xl, 2xl options for GIF size
])

@php
    $containerClasses = $fullScreen 
        ? 'fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-90' 
        : 'flex items-center justify-center p-6 rounded-lg';
    
    $sizeClasses = [
        'sm' => 'w-16 h-16',
        'md' => 'w-32 h-32',
        'lg' => 'w-48 h-48',
        'xl' => 'w-64 h-64',
        '2xl' => 'w-96 h-96',
    ][$size] ?? 'w-32 h-32';
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    <div class="text-center">
        <div class="flex flex-col items-center justify-center">
            <img src="{{ asset('images/loadingscreen.gif') }}" alt="Loading" class="{{ $sizeClasses }} mx-auto mb-4">
            <div class="bg-gray-50 px-8 py-4 rounded-lg shadow-inner w-full max-w-md mx-auto">
                <h3 class="text-lg font-medium text-gray-800 mb-2">{{ $message }}</h3>
                <p class="text-gray-600 text-sm sm:text-base mb-1">{{ $subMessage }}</p>
                
                @if($slot->isNotEmpty())
                    <div class="mt-3">
                        {{ $slot }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 