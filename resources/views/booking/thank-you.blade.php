@php
use Illuminate\Support\Facades\Log;
@endphp
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Success Icon -->
        <div class="mb-8 flex justify-center">
            <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <!-- Thank You Message -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You for Booking!</h1>
        <p class="text-lg text-gray-600 mb-8">
            Your appointment has been successfully scheduled. We'll send you a confirmation email with the details.
        </p>

        <!-- Appointment Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 text-left">
            <h2 class="text-xl font-semibold mb-4">Appointment Details</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Shop:</span>
                    <span class="font-medium">{{ $booking_details['shop_name'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ $booking_details['date'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="font-medium">{{ $booking_details['time'] }}</span>
                </div>
                
                <!-- Services Breakdown -->
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <h3 class="font-medium mb-2">Services Breakdown</h3>
                    @foreach($booking_details['services'] as $service)
                    <div class="flex justify-between text-sm mb-1">
                        <span>
                            {{ $service['pet_name'] }} - {{ $service['service_name'] }}
                            <span class="text-gray-500">({{ ucfirst($service['size']) }})</span>
                        </span>
                        <span>₱{{ number_format($service['price'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
                
                <div class="flex justify-between font-medium border-t border-gray-200 pt-3 mt-3">
                    <span class="text-gray-800">Total Amount:</span>
                    <span>₱{{ number_format($booking_details['total_amount'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Pending
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <!-- Acknowledgement Receipt Button -->
            <a href="{{ route('booking.acknowledgement.download', ['shop' => $shop, 'booking_details' => $booking_details]) }}" 
               class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Download Booking Receipt
            </a>

            <!-- Back to Home Button -->
            <a href="{{ route('home') }}" 
               class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Back to<span class="ml-1">Home</span>
            </a>
            
            <!-- View Appointments Button -->
            <a href="{{ route('appointments.index') }}" 
               class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                View<span class="ml-1">Appointments</span>
            </a>
        </div>
    </div>
</div>
@endsection 