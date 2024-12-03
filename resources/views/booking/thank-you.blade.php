@php
use Illuminate\Support\Facades\Log;
@endphp
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 mt-8">
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
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-8">
            <!-- Appointment Acknowledgement Button -->
            <button type="button" 
                    onclick="window.location.href='{{ route('booking.receipt.download', ['shop' => $shop]) }}'"
                    class="w-full sm:w-[200px] inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <span>Acknowledgement</span>
            </button>

            <!-- Back to Home Button -->
            <button type="button"
                    onclick="window.location.href='{{ route('home') }}'"
                    class="w-full sm:w-[200px] inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                <span>Back to Home</span>
            </button>
            
            <!-- View Appointments Button -->
            <button type="button"
                    onclick="window.location.href='{{ route('appointments.index') }}'"
                    class="w-full sm:w-[200px] inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <span>View Appointments</span>
            </button>
        </div>
    </div>
</div>
@endsection 