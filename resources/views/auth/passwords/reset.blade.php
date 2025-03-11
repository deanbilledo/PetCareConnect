@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-teal-400 px-6 py-8 text-center">
            <h2 class="text-2xl font-bold text-white">
                {{ __('Reset Password') }}
            </h2>
            <p class="mt-2 text-white text-opacity-90 text-sm">
                Enter your new password below to complete the reset process
            </p>
        </div>

        <!-- Form -->
        <div class="px-6 py-8">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Address -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Email Address') }}
                    </label>
                    <input id="email" 
                           type="email" 
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 transition duration-150 @error('email') border-red-500 @enderror" 
                           name="email" 
                           value="{{ $email ?? old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('New Password') }}
                    </label>
                    <input id="password" 
                           type="password" 
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 transition duration-150 @error('password') border-red-500 @enderror" 
                           name="password" 
                           required 
                           autocomplete="new-password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Password must be at least 8 characters long
                    </p>
                </div>

                <!-- Confirm Password -->
                <div class="mb-8">
                    <label for="password-confirm" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Confirm Password') }}
                    </label>
                    <input id="password-confirm" 
                           type="password" 
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 transition duration-150" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Login
            </a>
        </div>
    </div>

    <!-- App Logo -->
    <div class="mt-8 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-10 mx-auto">
        <p class="mt-3 text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</div>
@endsection
