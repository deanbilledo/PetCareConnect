<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Pet Care Connect') }} - Reset Password</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div x-data="resetPasswordForm()" 
             x-init="init()" 
             x-cloak
             class="max-w-md w-full transform transition-all duration-300"
             :class="{'translate-y-0 opacity-100': showForm, 'translate-y-4 opacity-0': !showForm}">
            
            <div class="bg-white py-8 px-4 shadow-md rounded-lg sm:px-10">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-12 mx-auto">
                </div>
                
                <!-- Title and Description -->
                <div class="text-center mb-6 transform transition-all duration-500 delay-150"
                     :class="{'translate-y-0 opacity-100': showForm, 'translate-y-4 opacity-0': !showForm}">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ __('Reset Password') }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Enter your email address to receive a password reset link
                    </p>
                </div>

                <!-- Success Message -->
                @if (session('status'))
                    <div x-data="{ show: false }" 
                         x-init="setTimeout(() => show = true, 300)"
                         class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md flex items-start transform transition-all duration-500"
                         :class="{'scale-100 opacity-100': show, 'scale-95 opacity-0': !show}">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" 
                      action="{{ route('password.email') }}" 
                      x-on:submit="loading = true"
                      class="space-y-6 transform transition-all duration-500 delay-300"
                      :class="{'translate-y-0 opacity-100': showForm, 'translate-y-4 opacity-0': !showForm}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            {{ __('Email Address') }}
                        </label>
                        <div class="mt-1 relative">
                            <input id="email" 
                                   type="email" 
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   placeholder="your.email@example.com"
                                   x-on:input="validateEmail($event.target.value)"
                                   x-on:keydown.enter.prevent="if(emailValid) $el.form.submit()"
                                   autofocus>
                            
                            <!-- Email Validation Icon -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" 
                                 x-show="emailValid" 
                                 x-cloak 
                                 x-transition>
                                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150"
                                :class="{'opacity-75': loading || !emailValid}"
                                :disabled="loading || !emailValid">
                            <!-- Loading Spinner -->
                            <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <!-- Email Icon (when not loading) -->
                            <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </form>

                <!-- Back to login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" 
                       class="text-sm font-medium text-blue-600 hover:text-blue-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Login
                    </a>
                </div>
            </div>

            <!-- Footer Copyright -->
            <div class="mt-4 text-center transform transition-all duration-700 delay-450" 
                 :class="{'translate-y-0 opacity-100': showForm, 'translate-y-4 opacity-0': !showForm}">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        function resetPasswordForm() {
            return {
                showForm: false,
                loading: false,
                emailValid: false,
                init() {
                    setTimeout(() => {
                        this.showForm = true;
                    }, 100);
                },
                validateEmail(email) {
                    // Simple validation for UI feedback
                    this.emailValid = email.length > 5 && email.includes('@') && email.includes('.');
                }
            };
        }
    </script>
</body>
</html>
