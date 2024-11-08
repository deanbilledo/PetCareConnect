<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Care Connect</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50 py-2">
        <div class="flex justify-between items-center px-4 py-2">
        <div class="p-4 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center cursor-pointer toggle-sidebar">
                    <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </div>
                <span class="text-xl font-semibold text-gray-800 ml-2 sidebar-text">Pet Care Connect</span>
            </div>
        </div>
            <div class="flex items-center gap-4">
                <button class="p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <button class="p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
                <img src="https://i.pravatar.cc/150?img=1"  alt="Profile" class="h-8 w-8 rounded-full">
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg pt-16 overflow-y-auto">
        <nav class="px-4 py-6">
            <div class="space-y-6">
                <!-- Navigation Items -->
                <div>
                    <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                    <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Services
                    </a>
                    <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Appointments
                    </a>
                    <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 8.5C2 7.12 3.12 6 4.5 6h15c1.38 0 2.5 1.12 2.5 2.5v7c0 1.38-1.12 2.5-2.5 2.5h-15C3.12 18 2 16.88 2 15.5v-7zM4 8v7h16V8H4zm8 3.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" />
                        </svg>
                        Contact Us
                    </a>
                </div>

                <!-- Categories -->
                <div>
                    <h3 class="px-4 text-sm font-semibold text-gray-500 uppercase tracking-wider">Categories</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Veterinaries
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Grooming
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="px-4 text-sm font-semibold text-gray-500 uppercase tracking-wider">Help Center</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            FAQ
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Report an Abuse
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="px-4 text-sm font-semibold text-gray-500 uppercase tracking-wider">More  From US</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Socials
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Settings
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Terms and Conditions
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 rounded-lg">
                            <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            PrivacyPolicy
                        </a>
                    </div>
                </div>

                

            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 pt-16 min-h-screen">
        <div class="p-6">
            <!-- Profile Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <img src="https://i.pravatar.cc/150?img=1"  alt="Random Image" class="h-20 w-20 rounded-full">
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold">Allen Ibrahim</h2>
                            <button class="text-blue-600 text-sm">Upload Photo</button>
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold">Personal Info</h3>
                        <button class="text-blue-600">Edit</button>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Full Name</p>
                            <p>Allen Ibrahim</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p>Allen@gmail.com</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p>(+63) 953 543 733</p>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold">Location</h3>
                        <button class="text-blue-600">Edit</button>
                    </div>
                    <p>Divisoria Sa may zamboanga, ZAMBOANGA CITY</p>
                </div>
            </div>

            <!-- Registered Pets -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Registered Pets</h2>
                    <button class="text-blue-600">+ Add</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500">
                                <th class="pb-3">Name</th>
                                <th class="pb-3">Type</th>
                                <th class="pb-3">Breed</th>
                                <th class="pb-3">Weight</th>
                                <th class="pb-3">Height</th>
                                <th class="pb-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t">
                                <td class="py-3">Marc</td>
                                <td>Dog</td>
                                <td>Pug</td>
                                <td>6-10kg</td>
                                <td>9-12cm</td>
                                <td class="text-right">
                                    <button class="text-gray-500">Details</button>
                                </td>
                            </tr>
                            <tr class="border-t">
                                <td class="py-3">Manoy</td>
                                <td>Dog</td>
                                <td>Hotdog</td>
                                <td>1-5kg</td>
                                <td>5-8cm</td>
                                <td class="text-right">
                                    <button class="text-gray-500">Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Visits -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Visits</h2>
                    <button class="text-gray-500">Filter</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4">
                        <img src="https://i.pravatar.cc/150?img=1"  alt="Paws and Claws" class="w-full h-40 object-cover rounded-lg mb-3">
                        <h3 class="font-semibold">Paws and Claws</h3>
                        <div class="flex items-center text-yellow-400">
                            <span>5.0</span>
                            <svg class="h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <img src="https://i.pravatar.cc/150?img=1"  alt="Paws and Claws" class="w-full h-40 object-cover rounded-lg mb-3">
                        <h3 class="font-semibold">Paws and Claws</h3>
                        <div class="flex items-center text-yellow-400">
                            <span>5.0</span>
                            <svg class="h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <img src="https://i.pravatar.cc/150?img=1"  alt="Paws and Claws" class="w-full h-40 object-cover rounded-lg mb-3">
                        <h3 class="font-semibold">Paws and Claws</h3>
                        <div class="flex items-center text-yellow-400">
                            <span>5.0</span>
                            <svg class="h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Don Alfaro St, Zamboanga, 7000 Zamboanga del Sur</p>
                    </div>
                </div>
                
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white ml-64">
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Services</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white">Grooming</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">Veterinary</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">Appointments</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Shopping Online</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white
                    ">Pet Supplies</a></li>
                                        <li><a href="#" class="text-gray-300 hover:text-white">Pet Food</a></li>
                                        <li><a href="#" class="text-gray-300 hover:text-white">Accessories</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold mb-4">About Us</h3>
                                    <ul class="space-y-2">
                                        <li><a href="#" class="text-gray-300 hover:text-white">Our Story</a></li>
                                        <li><a href="#" class="text-gray-300 hover:text-white">Team</a></li>
                                        <li><a href="#" class="text-gray-300 hover:text-white">Careers</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                                    <ul class="space-y-2">
                                        <li><a href="#" class="text-gray-300 hover:text-white">Support</a></li>
                                        <li><a href="#" class="text-gray-300 hover:text-white">FAQs</a></li>
                                        <li><a href="#" class="text-gray-300 hover:text-white">Live Chat</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-gray-900 text-center py-4">
                                <p class="text-gray-500 text-sm">&copy; 2023 Pet Care Connect. All rights reserved.</p>
                            </div>
                        </footer>
                    </body>
                    </html>