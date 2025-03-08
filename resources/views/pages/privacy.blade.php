@extends('layouts.app')

@section('content')
<div class="relative bg-gradient-to-b from-blue-50/30 to-white pb-16">
    <!-- Top Wave Decoration -->
    <div class="absolute top-0 left-0 right-0 h-24 overflow-hidden -z-10">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="absolute w-full h-auto">
            <path fill="#4FB6C4" fill-opacity="0.15" d="M0,256L48,240C96,224,192,192,288,181.3C384,171,480,181,576,186.7C672,192,768,192,864,181.3C960,171,1056,149,1152,160C1248,171,1344,213,1392,234.7L1440,256L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>

    <div class="container mx-auto px-4 pt-24 pb-8 relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-block p-3 bg-teal-100 rounded-full mb-4 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <!-- Table of Contents (Sticky) -->
        <div class="lg:flex gap-8 max-w-6xl mx-auto">
            <div class="lg:w-1/4">
                <div class="sticky top-24 bg-white rounded-xl shadow-sm p-5 border border-gray-100 mb-6">
                    <h3 class="font-semibold text-lg mb-3 text-teal-700">Contents</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#section-1" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">1</span>
                            Information We Collect
                        </a></li>
                        <li><a href="#section-2" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">2</span>
                            How We Use Your Information
                        </a></li>
                        <li><a href="#section-3" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">3</span>
                            Information Sharing
                        </a></li>
                        <li><a href="#section-4" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">4</span>
                            Data Security
                        </a></li>
                        <li><a href="#section-5" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">5</span>
                            Your Rights
                        </a></li>
                        <li><a href="#section-6" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">6</span>
                            Changes to This Policy
                        </a></li>
                        <li><a href="#section-7" class="text-gray-700 hover:text-teal-600 flex items-center">
                            <span class="bg-teal-100 text-teal-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">7</span>
                            Contact Us
                        </a></li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Introduction -->
                    <div class="p-6 border-b border-gray-100">
                        <p class="text-gray-700 leading-relaxed">
                            This Privacy Policy explains how Pet Care Connect collects, uses, and protects your personal information. 
                            We value your privacy and are committed to maintaining the confidentiality of your personal data.
                        </p>
                    </div>

                    <!-- Section 1 -->
                    <div id="section-1" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">1. Information We Collect</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">We collect information that you provide directly to us, including:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Name and contact information</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Account credentials</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Pet information</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Payment information</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Communication preferences</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div id="section-2" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">2. How We Use Your Information</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">We use the information we collect to:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Provide and maintain our services</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Process your transactions</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Send you service-related notifications</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Improve our services</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Protect against fraud and abuse</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 3 -->
                    <div id="section-3" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">3. Information Sharing</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">We may share your information with:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Service providers you choose to book with</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Payment processors</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Legal authorities when required by law</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 4 -->
                    <div id="section-4" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">4. Data Security</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700">We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.</p>
                            
                            <div class="bg-gray-50 p-4 rounded-lg mt-4 border border-gray-200">
                                <div class="flex items-start">
                                    <svg class="h-6 w-6 text-coral-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-700 text-sm">
                                        <strong class="text-coral-700">Security Notice:</strong> While we take steps to secure your data, we recommend using strong, unique passwords and keeping your account information confidential.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5 -->
                    <div id="section-5" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">5. Your Rights</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">You have the right to:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Access your personal information</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Correct inaccurate information</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Request deletion of your information</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Opt-out of marketing communications</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 6 -->
                    <div id="section-6" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">6. Changes to This Policy</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700">We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>
                        </div>
                    </div>

                    <!-- Section 7 -->
                    <div id="section-7" class="p-6">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">7. Contact Us</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">If you have any questions about this Privacy Policy, please contact us at:</p>
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                                <div class="flex items-center mb-3">
                                    <svg class="h-5 w-5 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-700">Email: <a href="mailto:privacy@petcareconnect.com" class="text-teal-600 hover:underline">privacy@petcareconnect.com</a></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-coral-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="text-gray-700">Phone: <a href="tel:+639456789087" class="text-coral-600 hover:underline">+(63)09-456-789087</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom brand colors */
    .bg-coral-50 {
        background-color: #FEF2F2;
    }
    .bg-coral-100 {
        background-color: #FFEAE5;
    }
    .text-coral-500 {
        color: #FF8075;
    }
    .text-coral-600 {
        color: #F96666;
    }
    .text-coral-700 {
        color: #E53E3E;
    }
    .text-coral-800 {
        color: #DC2626;
    }
    .bg-teal-50 {
        background-color: #EFFAF8;
    }
    .bg-teal-100 {
        background-color: #D5F5F0;
    }
    .text-teal-500 {
        color: #4FB6C4;
    }
    .text-teal-600 {
        color: #39A2B1;
    }
    .text-teal-700 {
        color: #2C7A86;
    }
    
    /* Smooth scrolling for anchor links */
    html {
        scroll-behavior: smooth;
    }
    
    /* Active section highlighting */
    :target {
        scroll-margin-top: 100px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Highlight the active section in the table of contents
    document.addEventListener('DOMContentLoaded', function() {
        // Get all section elements
        const sections = document.querySelectorAll('[id^="section-"]');
        const navLinks = document.querySelectorAll('a[href^="#section-"]');
        
        // Function to determine which section is in view
        function highlightActiveSection() {
            let activeSection = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (window.scrollY >= sectionTop - 200 && window.scrollY < sectionTop + sectionHeight - 200) {
                    activeSection = section.getAttribute('id');
                }
            });
            
            // Remove active class from all links
            navLinks.forEach(link => {
                link.classList.remove('text-teal-600', 'font-medium');
                link.classList.add('text-gray-700');
            });
            
            // Add active class to current link
            if (activeSection) {
                const activeLink = document.querySelector(`a[href="#${activeSection}"]`);
                if (activeLink) {
                    activeLink.classList.remove('text-gray-700');
                    activeLink.classList.add('text-teal-600', 'font-medium');
                }
            }
        }
        
        window.addEventListener('scroll', highlightActiveSection);
        highlightActiveSection(); // Run once on page load
    });
</script>
@endpush 