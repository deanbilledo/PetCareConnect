<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Login - Pet Care Connect</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Import Poppins font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        /* Apply Poppins to the entire body */
        body, html {
            font-family: 'Poppins', sans-serif;
            height: 100%;
        }

        .flex {
            display: flex;
        }

        .min-h-screen {
            min-height: 100vh;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        .flex-1 {
            flex: 1 1 0%;
        }

        .relative {
            position: relative;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .absolute {
            position: absolute;
        }

        .top-4 {
            top: 1rem;
        }

        .left-4 {
            left: 1rem;
        }

        .z-20 {
            z-index: 20;
        }

        .w-48 {
            width: 12rem;
        }

        .inset-0 {
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .items-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .-z-10 {
            z-index: -10;
        }

        .w-96 {
            width: 24rem;
        }

        .h-96 {
            height: 24rem;
        }

        .bg-blue-300 {
            background-color: #93c5fd;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .z-10 {
            z-index: 10;
        }

        .bottom-20 {
            bottom: 5rem;
        }

        .-left-20 {
            left: -5rem;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .space-x-2 > * + * {
            margin-left: 0.5rem;
        }

        .w-6 {
            width: 1.5rem;
        }

        .h-6 {
            height: 1.5rem;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .-bottom-10 {
            bottom: -2.5rem;
        }

        .-right-20 {
            right: -5rem;
        }

        .max-w-md {
            max-width: 28rem;
        }

        .space-y-8 > * + * {
            margin-top: 2rem;
        }

        .px-8 {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .text-center {
            text-align: center;
        }

        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .space-x-4 > * + * {
            margin-left: 1rem;
        }

        .border {
            border-width: 1px;
        }

        .border-gray-300 {
            border-color: #d1d5db;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .font-medium {
            font-weight: 500;
        }

        .text-gray-700 {
            color: #374151;
        }

        .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
        }

        .w-full {
            width: 100%;
        }

        .border-t {
            border-top-width: 1px;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .space-y-6 > * + * {
            margin-top: 1.5rem;
        }

        .px-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .placeholder-gray-400::placeholder {
            color: #9ca3af;
        }

        .focus\:outline-none:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .focus\:ring-blue-500:focus {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgba(59, 130, 246, var(--tw-ring-opacity));
        }

        .focus\:border-blue-500:focus {
            --tw-border-opacity: 1;
            border-color: rgba(59, 130, 246, var(--tw-border-opacity));
        }

        .inset-y-0 {
            top: 0;
            bottom: 0;
        }

        .right-0 {
            right: 0;
        }

        .pr-3 {
            padding-right: 0.75rem;
        }

        .h-4 {
            height: 1rem;
        }

        .w-4 {
            width: 1rem;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .text-red-500 {
            color: #ef4444;
        }

        .hover\:underline:hover {
            text-decoration: underline;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .border-transparent {
            border-color: transparent;
        }

        .text-white {
            color: #ffffff;
        }

        .bg-blue-500 {
            background-color: #3b82f6;
        }

        .hover\:bg-blue-600:hover {
            background-color: #2563eb;
        }

        .focus\:ring-2:focus {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .focus\:ring-offset-2:focus {
            --tw-ring-offset-width: 2px;
        }

        /* Custom styles for specific elements */
        #togglePassword i {
            color: #9ca3af;
        }

        #remember-me:focus {
            --tw-ring-color: rgba(59, 130, 246, var(--tw-ring-opacity));
        }

        /* Add any additional custom styles here if needed */
    </style>

<body class="flex flex-col lg:flex-row min-h-screen bg-custom-bg font-[Poppins]">
    <div class="hidden lg:flex flex-1 relative overflow-hidden bg-custom-bg min-h-[50vh] lg:min-h-screen items-center justify-center">
        <div class="absolute top-4 left-4 z-20">
            <a href="NA-Index.php">
                <img src="https://via.placeholder.com/150" alt="Pet Care Connect Logo" class="w-32 md:w-48">
            </a>
        </div>
        <div class="relative w-full h-full flex items-center justify-center pl-24 md:pl-32 lg:pl-40 pb-16 md:pb-24 lg:pb-32">
            <img src="https://via.placeholder.com/400" alt="Cute kitten" class="relative z-10 w-[400px] md:w-[600px] lg:w-[700px] xl:w-[800px] object-contain ml-16 md:ml-24 lg:ml-32">
        </div>
    </div>
    <div class="flex-1 flex items-center justify-center bg-custom-bg p-4 lg:p-8">
        <div class="w-full max-w-md space-y-8 px-8 py-10 bg-white rounded-3xl shadow-xl">
            <div class="text-center">
                <h2 class="text-3xl font-bold">Welcome Back!</h2>
                <p class="text-gray-600 mt-2">Login into your account</p>
            </div>
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($expired_message)): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($expired_message); ?></span>
                </div>
            <?php endif; ?>
            <div class="flex space-x-4">
                <button onclick="loginWithGoogle()" class="flex-1 flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <img src="https://via.placeholder.com/20" alt="Google logo" class="w-5 h-5 mr-2">
                    Google
                </button>
                <button onclick="loginWithFacebook()" class="flex-1 flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <img src="https://via.placeholder.com/20" alt="Facebook logo" class="w-5 h-5 mr-2">
                    Facebook
                </button>
            </div>
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-gray-300"></span>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <input type="email" name="email" placeholder="Email" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                </div>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="Password" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-custom-blue focus:border-custom-blue">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember-me" name="remember_me" class="h-4 w-4 text-custom-blue focus:ring-custom-blue border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>
                    <a href="forgot_password.php" class="text-sm text-red-500 hover:underline">
                        Forgot Password?
                    </a>
                </div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-custom-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-blue">
                    Log In
                </button>
            </form>
            <p class="text-center text-sm text-gray-600">
                Don't have an account?
                <a href="signup.php" class="text-custom-blue hover:underline">
                    Sign up!
                </a>
            </p>
            <p class="text-center text-xs text-gray-500 mt-4">
                By logging in, you agree to our 
                <a href="terms.php" class="text-custom-blue hover:underline">Terms of Service</a> and 
                <a href="privacy.php" class="text-custom-blue hover:underline">Privacy Policy</a>.
            </p>
        </div>
    </div>
    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function loginWithGoogle() {
            // Implement Google login
            alert('Google login not implemented yet');
        }

        function loginWithFacebook() {
            // Implement Facebook login
            alert('Facebook login not implemented yet');
        }
    </script>
</body>
</html>
