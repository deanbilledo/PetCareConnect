@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 text-center">
    <h1 class="text-4xl font-bold mb-6 mt-8">Privacy Policy</h1>
    <div class="prose max-w-none mx-auto">
        <p class="text-gray-600">Last updated: {{ date('F d, Y') }}</p>
        
        <h2 class="text-2xl font-semibold mt-6 mb-4">1. Information We Collect</h2>
        <p class="text-gray-700">We collect information that you provide directly to us, including:</p>
        <ul class="list-disc list-inside text-left mx-auto inline-block">
            <li>Name and contact information</li>
            <li>Account credentials</li>
            <li>Pet information</li>
            <li>Payment information</li>
            <li>Communication preferences</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-6 mb-4">2. How We Use Your Information</h2>
        <p class="text-gray-700">We use the information we collect to:</p>
        <ul class="list-disc list-inside text-left mx-auto inline-block">
            <li>Provide and maintain our services</li>
            <li>Process your transactions</li>
            <li>Send you service-related notifications</li>
            <li>Improve our services</li>
            <li>Protect against fraud and abuse</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-6 mb-4">3. Information Sharing</h2>
        <p class="text-gray-700">We may share your information with:</p>
        <ul class="list-disc list-inside text-left mx-auto inline-block">
            <li>Service providers you choose to book with</li>
            <li>Payment processors</li>
            <li>Legal authorities when required by law</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-6 mb-4">4. Data Security</h2>
        <p class="text-gray-700">We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.</p>

        <h2 class="text-2xl font-semibold mt-6 mb-4">5. Your Rights</h2>
        <p class="text-gray-700">You have the right to:</p>
        <ul class="list-disc list-inside text-left mx-auto inline-block">
            <li>Access your personal information</li>
            <li>Correct inaccurate information</li>
            <li>Request deletion of your information</li>
            <li>Opt-out of marketing communications</li>
        </ul>

        <h2 class="text-2xl font-semibold mt-6 mb-4">6. Changes to This Policy</h2>
        <p class="text-gray-700">We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>

        <h2 class="text-2xl font-semibold mt-6 mb-4">7. Contact Us</h2>
        <p class="text-gray-700">If you have any questions about this Privacy Policy, please contact us at:</p>
        <ul class="list-none text-left mx-auto inline-block">
            <li>Email: <a href="mailto:privacy@petcareconnect.com" class="text-blue-500">privacy@petcareconnect.com</a></li>
            <li>Phone: <a href="tel:+639456789087" class="text-blue-500">+(63)09-456-789087</a></li>
        </ul>
    </div>
</div>
@endsection