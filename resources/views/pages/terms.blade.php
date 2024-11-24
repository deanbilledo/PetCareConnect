@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 mt-8">Terms of Service</h1>
    <div class="prose max-w-none">
        <p>Last updated: {{ date('F d, Y') }}</p>
        
        <h2 class="text-xl font-semibold mt-6 mb-4">1. Acceptance of Terms</h2>
        <p>These Terms of Use ("Terms") govern your access and use of the Pet Central Connect Platform (the "Platform") operated by Alternation Company ("We," "Us," or "Our"), which includes the website at www.petcentralconnect.com.ph and any mobile applications or mobile versions we provide. By accessing or using the Platform and its services, you represent that:</p>
        <ul>
            <li>You are at least 18 years old.</li>
            <li>You have read, understood, and agree to be bound by these Terms and Our Privacy Policy.</li>
        </ul>
        
        <h2 class="text-xl font-semibold mt-6 mb-4">2. General Use</h2>
        <p>By using the Platform, you agree to:</p>
        <ul>
            <li>Use the Platform only for its intended and lawful purposes.</li>
            <li>Ensure that all information or data you provide is accurate.</li>
            <li>Maintain the confidentiality of your account information and password.</li>
            <li>Not attempt to interrupt or harm the Platform's operations.</li>
            <li>Not impersonate any person or entity.</li>
            <li>Not use or upload harmful components or malicious software.</li>
        </ul>

        <h2 class="text-xl font-semibold mt-6 mb-4">3. Registration and Account Security</h2>
        <ul>
            <li>Account creation is required to access the Platform.</li>
            <li>You must provide accurate and complete registration information.</li>
            <li>You are responsible for all activity under your account.</li>
            <li>We may request credential updates at any time.</li>
        </ul>

        <h2 class="text-xl font-semibold mt-6 mb-4">4. Booking and Appointments</h2>
        <ul>
            <li>You may book grooming and veterinary services through the Platform.</li>
            <li>Appointments may be rescheduled up to two times per week.</li>
            <li>Cancellations must be made at least 6 hours before the appointment.</li>
            <li>Violation of booking policies may result in account suspension.</li>
        </ul>

        <h2 class="text-xl font-semibold mt-6 mb-4">5. Payment Terms</h2>
        <p>All payments for services must be made directly from customers to shops. Additional fees will be displayed during booking. Shops must use the platform's payment tracking system for transaction records.</p>

        <h2 class="text-xl font-semibold mt-6 mb-4">6. Account Termination</h2>
        <p>We reserve the right to suspend or terminate your account at any time, for any reason. You remain liable for charges incurred before termination.</p>

        <h2 class="text-xl font-semibold mt-6 mb-4">7. User Information</h2>
        <p>Except for Personal Data governed by the Data Privacy Act of 2012, any submitted content is considered non-confidential and non-proprietary.</p>

        <h2 class="text-xl font-semibold mt-6 mb-4">8. Changes to Terms</h2>
        <p>We may revise these Terms at any time without prior notice. Continued use of the Platform constitutes acceptance of revised Terms.</p>

        <h2 class="text-xl font-semibold mt-6 mb-4">9. Contact Information</h2>
        <p>For questions or concerns, please refer to our FAQ section or Contact Us page.</p>
    </div>
</div>
@endsection 