@extends('layouts.app')

@section('content')
<div class="relative bg-gradient-to-b from-blue-50/50 to-white">
    <!-- Top Wave Decoration -->
    <div class="absolute top-0 left-0 right-0 h-24 overflow-hidden -z-10">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="absolute w-full h-auto">
            <path fill="#4FB6C4" fill-opacity="0.2" d="M0,256L48,240C96,224,192,192,288,181.3C384,171,480,181,576,186.7C672,192,768,192,864,181.3C960,171,1056,149,1152,160C1248,171,1344,213,1392,234.7L1440,256L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>

    <div class="container mx-auto px-4 py-12 pt-24 mb-20 relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-14">
            <div class="inline-block p-3 bg-teal-100 rounded-full mb-4 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Find answers to common questions about Pet Care Connect's services, booking process, and more.</p>
        </div>

        <!-- Search Box -->
        <div class="max-w-xl mx-auto mb-12">
            <div class="flex bg-white rounded-full shadow-sm overflow-hidden border border-gray-200">
                <input type="text" id="faq-search" placeholder="Search for questions..." class="py-3 px-6 w-full focus:outline-none rounded-l-full">
                <button class="bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white px-6 flex items-center justify-center transition duration-200 rounded-r-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
            <!-- Category Tabs -->
            <div class="mb-10 flex flex-wrap justify-center" id="faq-tabs">
                <button class="category-tab active m-2 px-6 py-2.5 rounded-full bg-gradient-to-r from-teal-500 to-teal-600 text-white font-medium transition-all duration-200 hover:shadow-md" data-category="all">All Questions</button>
                <button class="category-tab m-2 px-6 py-2.5 rounded-full bg-white text-gray-700 font-medium transition-all duration-200 hover:shadow-md" data-category="general">General</button>
                <button class="category-tab m-2 px-6 py-2.5 rounded-full bg-white text-gray-700 font-medium transition-all duration-200 hover:shadow-md" data-category="booking">Booking</button>
                <button class="category-tab m-2 px-6 py-2.5 rounded-full bg-white text-gray-700 font-medium transition-all duration-200 hover:shadow-md" data-category="payment">Payment</button>
                <button class="category-tab m-2 px-6 py-2.5 rounded-full bg-white text-gray-700 font-medium transition-all duration-200 hover:shadow-md" data-category="account">Account</button>
            </div>

            <!-- FAQ Accordions -->
            <div class="space-y-4" id="faq-accordions">
                <!-- General Questions -->
                <div class="faq-category" data-category="general">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">What is Pet Care Connect?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">Pet Care Connect is a platform that connects pet owners with professional pet care services, including grooming and veterinary care. Our mission is to make high-quality pet care accessible and convenient for all pet owners.</p>
                                    <p class="text-gray-600 mt-2">We carefully vet all service providers to ensure your pets receive the best care possible.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">How do I book a service?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">Booking a service on Pet Care Connect is simple:</p>
                                    <ol class="list-decimal list-inside text-gray-600 mt-2 space-y-1">
                                        <li>Create an account or log in to your existing account</li>
                                        <li>Browse available services and providers</li>
                                        <li>Select your preferred provider and service</li>
                                        <li>Choose a date and time that works for you</li>
                                        <li>Confirm your booking and wait for confirmation</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking & Appointments -->
                <div class="faq-category" data-category="booking">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Can I reschedule my appointment?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">Yes, you can reschedule your appointment up to 6 hours before the scheduled time. You're allowed to reschedule up to two times per booking to ensure fairness to our service providers.</p>
                                    <p class="text-gray-600 mt-2">To reschedule:</p>
                                    <ul class="list-disc list-inside text-gray-600 mt-1">
                                        <li>Go to "My Appointments" in your account</li>
                                        <li>Find the appointment you wish to reschedule</li>
                                        <li>Click on "Reschedule" and select a new date and time</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">What is the cancellation policy?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">Cancellations must be made at least 6 hours before your scheduled appointment to avoid any penalties. This policy helps ensure that our service providers can manage their schedules effectively.</p>
                                    <div class="mt-3 p-3 bg-coral-50 rounded-lg text-coral-800 text-sm">
                                        <strong>Please note:</strong> Frequent cancellations may affect your ability to book future appointments with certain providers.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment & Pricing -->
                <div class="faq-category" data-category="payment">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">How do I pay for services?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">Payments are made directly to the service provider. Most providers accept the following payment methods:</p>
                                    <div class="grid grid-cols-2 gap-2 mt-3">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-gray-600">Cash</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-gray-600">Credit/Debit Cards</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-gray-600">Mobile Payments</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-gray-600">Bank Transfers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Are there any booking fees?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">No, there are no additional booking fees when you schedule appointments through our platform. What you see is what you pay – the price listed for the service is the final amount.</p>
                                    <div class="mt-3 p-3 bg-teal-50 rounded-lg text-teal-800 text-sm">
                                        <strong>Good to know:</strong> Some premium service providers may have their own service fees, which will be clearly displayed before you confirm your booking.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account & Profile -->
                <div class="faq-category" data-category="account">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">How do I update my profile?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">You can update your profile information by following these steps:</p>
                                    <ol class="list-decimal list-inside text-gray-600 mt-2 space-y-1">
                                        <li>Log in to your account</li>
                                        <li>Click on your profile picture in the top right corner</li>
                                        <li>Select "Profile Settings" from the dropdown menu</li>
                                        <li>Update your information as needed</li>
                                        <li>Click "Save Changes" to confirm your updates</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-md">
                        <div class="faq-item cursor-pointer">
                            <div class="flex justify-between items-center p-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-coral-100 flex items-center justify-center mr-4 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-coral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">How do I add my pets to my profile?</h3>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="faq-chevron h-5 w-5 text-gray-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="faq-content px-6 pb-6 hidden">
                                <div class="pl-12">
                                    <p class="text-gray-600">Adding pets to your profile is easy:</p>
                                    <ol class="list-decimal list-inside text-gray-600 mt-2 space-y-1">
                                        <li>Navigate to your profile section</li>
                                        <li>Click on the "My Pets" tab</li>
                                        <li>Select "Add New Pet"</li>
                                        <li>Fill in your pet's details including:</li>
                                    </ol>
                                    <ul class="list-disc list-inside text-gray-600 mt-2 ml-6 space-y-1">
                                        <li>Name, age, and species</li>
                                        <li>Breed information</li>
                                        <li>Medical history (optional but recommended)</li>
                                        <li>Special care requirements</li>
                                        <li>A photo of your pet</li>
                                    </ul>
                                    <p class="text-gray-600 mt-2">This information helps service providers better understand your pet's needs.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Need More Help Section -->
        <div class="mt-16 bg-gradient-to-r from-teal-600 to-coral-500 rounded-2xl p-8 text-white text-center max-w-4xl mx-auto shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Still have questions?</h3>
            <p class="text-white/90 mb-6">Contact our support team and we'll get back to you as soon as possible.</p>
            <a href="{{ route('home') }}#contact" class="inline-block bg-white text-teal-600 font-medium px-6 py-3 rounded-lg hover:bg-teal-50 transition duration-200 shadow-md">Contact Support</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
    .text-coral-800 {
        color: #DC2626;
    }
    .from-coral-500 {
        --tw-gradient-from: #FF8075;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(255, 128, 117, 0));
    }
    .to-coral-600 {
        --tw-gradient-to: #F96666;
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
    .text-teal-800 {
        color: #285E61;
    }
    .from-teal-500 {
        --tw-gradient-from: #4FB6C4;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(79, 182, 196, 0));
    }
    .to-teal-600 {
        --tw-gradient-to: #39A2B1;
    }
    .from-teal-600 {
        --tw-gradient-from: #39A2B1;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(57, 162, 177, 0));
    }
    .to-teal-700 {
        --tw-gradient-to: #2C7A86;
    }
    .hover\:from-teal-600:hover {
        --tw-gradient-from: #39A2B1;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(57, 162, 177, 0));
    }
    .hover\:to-teal-700:hover {
        --tw-gradient-to: #2C7A86;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ accordion functionality
        document.querySelectorAll('.faq-item').forEach(item => {
            const header = item.querySelector('.flex.justify-between');
            const content = item.querySelector('.faq-content');
            const chevron = item.querySelector('.faq-chevron');
            
            header.addEventListener('click', () => {
                // Toggle content visibility
                content.classList.toggle('hidden');
                // Rotate chevron
                chevron.classList.toggle('rotate-180');
                // Add active styles
                item.parentElement.classList.toggle('shadow-md');
                item.parentElement.classList.toggle('border-teal-200');
            });
        });

        // Category tabs functionality
        const tabs = document.querySelectorAll('.category-tab');
        const categories = document.querySelectorAll('.faq-category');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('active', 'bg-gradient-to-r', 'from-teal-500', 'to-teal-600', 'text-white');
                    t.classList.add('bg-white', 'text-gray-700');
                });
                
                // Add active class to clicked tab
                tab.classList.add('active', 'bg-gradient-to-r', 'from-teal-500', 'to-teal-600', 'text-white');
                tab.classList.remove('bg-white', 'text-gray-700');
                
                const category = tab.getAttribute('data-category');
                
                // Show all categories or filter by selected category
                if (category === 'all') {
                    categories.forEach(cat => {
                        cat.style.display = 'block';
                    });
                } else {
                    categories.forEach(cat => {
                        if (cat.getAttribute('data-category') === category) {
                            cat.style.display = 'block';
                        } else {
                            cat.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('faq-search');
        const faqItems = document.querySelectorAll('.faq-item');
        
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            
            faqItems.forEach(item => {
                const questionText = item.querySelector('h3').textContent.toLowerCase();
                const answerText = item.querySelector('.faq-content').textContent.toLowerCase();
                const faqCard = item.closest('.bg-white');
                
                if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                    faqCard.style.display = 'block';
                } else {
                    faqCard.style.display = 'none';
                }
            });
            
            // Show all categories when searching
            categories.forEach(category => {
                category.style.display = 'block';
            });
            
            // Reset active tab
            tabs.forEach(tab => {
                if (tab.getAttribute('data-category') === 'all') {
                    tab.classList.add('active', 'bg-gradient-to-r', 'from-teal-500', 'to-teal-600', 'text-white');
                    tab.classList.remove('bg-white', 'text-gray-700');
                } else {
                    tab.classList.remove('active', 'bg-gradient-to-r', 'from-teal-500', 'to-teal-600', 'text-white');
                    tab.classList.add('bg-white', 'text-gray-700');
                }
            });
        });
    });
</script>
@endpush 