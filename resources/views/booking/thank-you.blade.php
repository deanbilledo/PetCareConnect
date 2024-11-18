@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Success Icon - Fixed size -->
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
                    <span class="font-medium">{{ session('booking_details.shop_name') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ session('booking_details.date') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="font-medium">{{ session('booking_details.time') }}</span>
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
        <div class="space-x-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Back to Home
            </a>
            <a href="{{ route('appointments.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                View Appointments
            </a>
        </div>
    </div>
</div>
@endsection 