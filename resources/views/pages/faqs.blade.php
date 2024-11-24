@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 mt-8">Frequently Asked Questions</h1>
    
    <div class="space-y-6">
        <!-- General Questions -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">General Questions</h2>
            
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">What is Pet Care Connect?</h3>
                    <p class="mt-2 text-gray-600">Pet Care Connect is a platform that connects pet owners with professional pet care services, including grooming and veterinary care.</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">How do I book a service?</h3>
                    <p class="mt-2 text-gray-600">Simply create an account, browse available services, select your preferred provider, choose a date and time, and confirm your booking.</p>
                </div>
            </div>
        </div>

        <!-- Booking & Appointments -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">Booking & Appointments</h2>
            
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Can I reschedule my appointment?</h3>
                    <p class="mt-2 text-gray-600">Yes, you can reschedule your appointment up to 6 hours before the scheduled time. You're allowed to reschedule up to two times per week.</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">What is the cancellation policy?</h3>
                    <p class="mt-2 text-gray-600">Cancellations must be made at least 6 hours before your scheduled appointment to avoid any penalties.</p>
                </div>
            </div>
        </div>

        <!-- Payment & Pricing -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">Payment & Pricing</h2>
            
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">How do I pay for services?</h3>
                    <p class="mt-2 text-gray-600">Payments are made directly to the service provider. You can pay using cash or other payment methods accepted by the provider.</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Are there any booking fees?</h3>
                    <p class="mt-2 text-gray-600">No, there are no additional booking fees when you schedule appointments through our platform.</p>
                </div>
            </div>
        </div>

        <!-- Account & Profile -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">Account & Profile</h2>
            
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">How do I update my profile?</h3>
                    <p class="mt-2 text-gray-600">You can update your profile information by logging in and navigating to the Profile section in your account settings.</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">How do I add my pets to my profile?</h3>
                    <p class="mt-2 text-gray-600">In your profile section, you'll find an option to add and manage your pets' information, including their medical history and preferences.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 