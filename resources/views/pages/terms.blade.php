@extends('layouts.app')

@section('content')
<div class="relative bg-gradient-to-b from-blue-50/30 to-white pb-16">
    <!-- Top Wave Decoration -->
    <div class="absolute top-0 left-0 right-0 h-24 overflow-hidden -z-10">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="absolute w-full h-auto">
            <path fill="#4FB6C4" fill-opacity="0.15" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,186.7C960,192,1056,224,1152,218.7C1248,213,1344,171,1392,149.3L1440,128L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>

    <div class="container mx-auto px-4 pt-24 pb-8 relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-block p-3 bg-coral-100 rounded-full mb-4 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Terms of Service</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <!-- Table of Contents (Sticky) -->
        <div class="lg:flex gap-8 max-w-6xl mx-auto">
            <div class="lg:w-1/4">
                <div class="sticky top-24 bg-white rounded-xl shadow-sm p-5 border border-gray-100 mb-6">
                    <h3 class="font-semibold text-lg mb-3 text-coral-700">Contents</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#section-1" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">1</span>
                            Acceptance of Terms
                        </a></li>
                        <li><a href="#section-2" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">2</span>
                            General Use
                        </a></li>
                        <li><a href="#section-3" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">3</span>
                            Registration and Account Security
                        </a></li>
                        <li><a href="#section-4" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">4</span>
                            Booking and Appointments
                        </a></li>
                        <li><a href="#section-5" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">5</span>
                            Payment Terms
                        </a></li>
                        <li><a href="#section-6" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">6</span>
                            Account Termination
                        </a></li>
                        <li><a href="#section-7" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">7</span>
                            User Information
                        </a></li>
                        <li><a href="#section-8" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">8</span>
                            Changes to Terms
                        </a></li>
                        <li><a href="#section-9" class="text-gray-700 hover:text-coral-600 flex items-center">
                            <span class="bg-coral-100 text-coral-600 rounded-full w-5 h-5 inline-flex items-center justify-center mr-2 text-xs">9</span>
                            Contact Information
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
                            These Terms of Use govern your access and use of the Pet Care Connect Platform operated by Alternation Company. 
                            By accessing or using the Platform and its services, you agree to be bound by these terms.
                        </p>
                    </div>

                    <!-- Section 1 -->
                    <div id="section-1" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">1. Acceptance of Terms</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">These Terms of Use ("Terms") govern your access and use of the Pet Central Connect Platform (the "Platform") operated by Alternation Company ("We," "Us," or "Our"), which includes the website at www.petcentralconnect.com.ph and any mobile applications or mobile versions we provide. By accessing or using the Platform and its services, you represent that:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>You are at least 18 years old.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>You have read, understood, and agree to be bound by these Terms and Our Privacy Policy.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div id="section-2" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">2. General Use</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">By using the Platform, you agree to:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Use the Platform only for its intended and lawful purposes.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Ensure that all information or data you provide is accurate.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Maintain the confidentiality of your account information and password.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Not attempt to interrupt or harm the Platform's operations.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Not impersonate any person or entity.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Not use or upload harmful components or malicious software.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 3 -->
                    <div id="section-3" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">3. Registration and Account Security</h2>
                        </div>
                        <div class="pl-14">
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Account creation is required to access the Platform.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>You must provide accurate and complete registration information.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>You are responsible for all activity under your account.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-coral-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>We may request credential updates at any time.</span>
                                </li>
                            </ul>

                            <div class="bg-gray-50 p-4 rounded-lg mt-4 border border-gray-200">
                                <div class="flex items-start">
                                    <svg class="h-6 w-6 text-coral-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-700 text-sm">
                                        <strong class="text-coral-700">Security Tip:</strong> Never share your account credentials with others and use a strong, unique password to protect your account.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4 -->
                    <div id="section-4" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">4. Booking and Appointments</h2>
                        </div>
                        <div class="pl-14">
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>You may book grooming and veterinary services through the Platform.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Appointments may be rescheduled up to two times per week.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Cancellations must be made at least 6 hours before the appointment.</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-teal-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Violation of booking policies may result in account suspension.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 5 -->
                    <div id="section-5" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">5. Payment Terms</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700">All payments for services must be made directly from customers to shops. Additional fees will be displayed during booking. Shops must use the platform's payment tracking system for transaction records.</p>
                        </div>
                    </div>

                    <!-- Section 6 -->
                    <div id="section-6" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">6. Account Termination</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700">We reserve the right to suspend or terminate your account at any time, for any reason. You remain liable for charges incurred before termination.</p>
                        </div>
                    </div>

                    <!-- Section 7 -->
                    <div id="section-7" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">7. User Information</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700">Except for Personal Data governed by the Data Privacy Act of 2012, any submitted content is considered non-confidential and non-proprietary.</p>
                        </div>
                    </div>

                    <!-- Section 8 -->
                    <div id="section-8" class="p-6 border-b border-gray-100">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">8. Changes to Terms</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700">We may revise these Terms at any time without prior notice. Continued use of the Platform constitutes acceptance of revised Terms.</p>
                        </div>
                    </div>

                    <!-- Section 9 -->
                    <div id="section-9" class="p-6">
                        <div class="flex mb-4">
                            <div class="w-10 h-10 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-900">9. Contact Information</h2>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 mb-3">For questions or concerns, please refer to our FAQ section or Contact Us page.</p>
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                                <div class="flex items-center mb-3">
                                    <svg class="h-5 w-5 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-700">Email: <a href="mailto:support@petcareconnect.com" class="text-teal-600 hover:underline">support@petcareconnect.com</a></span>
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
                link.classList.remove('text-coral-600', 'font-medium');
                link.classList.add('text-gray-700');
            });
            
            // Add active class to current link
            if (activeSection) {
                const activeLink = document.querySelector(`a[href="#${activeSection}"]`);
                if (activeLink) {
                    activeLink.classList.remove('text-gray-700');
                    activeLink.classList.add('text-coral-600', 'font-medium');
                }
            }
        }
        
        window.addEventListener('scroll', highlightActiveSection);
        highlightActiveSection(); // Run once on page load
    });
</script>
@endpush 