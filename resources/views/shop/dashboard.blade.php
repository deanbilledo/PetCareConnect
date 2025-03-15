@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.shop')

@section('content')
<div x-data="{
    showNoteModal: false,
    currentAppointmentId: null,
    currentShopType: null,
    currentNote: '',
    noteImage: null,
    appointment: null,
    
    openNoteModal(appointmentId, shopType) {
        this.currentAppointmentId = appointmentId;
        this.currentShopType = shopType;
        this.showNoteModal = true;
        this.loadExistingNote();
    },

    async loadExistingNote() {
        try {
            // Show loading screen
            showGlobalLoading();
            
            console.log('Loading note for appointment:', this.currentAppointmentId);
            const response = await fetch(`/appointments/${this.currentAppointmentId}/note`);
            const data = await response.json();
            console.log('Received note data:', data);
            
            // Hide loading screen
            hideGlobalLoading();
            
            if (data.success) {
                this.currentNote = data.note || '';
                this.appointment = data.appointment;
                
               
            } else {
                console.error('Failed to load note:', data.error);
            }
        } catch (error) {
            // Hide loading screen in case of error
            hideGlobalLoading();
            
            console.error('Error loading note:', error);
        }
    },

    closeNoteModal() {
        this.showNoteModal = false;
        this.currentNote = '';
        this.noteImage = null;
        this.currentAppointmentId = null;
        this.currentShopType = null;
        this.appointment = null;
    },

    handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            this.noteImage = file;
        }
    },

    async saveNote() {
        // Show loading screen
        showGlobalLoading();
        
        try {
            await addNote(this.currentAppointmentId, this.currentShopType, this.currentNote, this.noteImage);
            this.closeNoteModal();
            window.location.reload();
        } catch (error) {
            // Hide loading screen in case of error - addNote will handle this, but adding as a safety measure
            hideGlobalLoading();
            console.error('Error saving note:', error);
        }
    }
}" class="w-full">
    <!-- Error Message Display -->
    @if(isset($error))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">{{ $error }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <!-- Shop Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('shop.profile') }}" class="block">
                    <img 
                        src="{{ $shop->image_url }}" 
                        alt="{{ $shop->name }}" 
                        class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover hover:opacity-80 transition-opacity cursor-pointer"
                    >
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">{{ $shop->name }}</h1>
                    <p class="text-gray-600">{{ ucfirst($shop->type) }} Shop</p>
                </div>
            </div>
            <div class="flex items-center">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    Open
                </span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Today's Appointments -->
            <div class="bg-blue-50 p-4 sm:p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-blue-800 text-sm font-medium mb-2">Today's Appointments</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-900">{{ $todayAppointments }}</p>
                    <p class="text-blue-600 text-sm mt-2">
                        @if($todayAppointments > 0)
                            Scheduled for today
                        @else
                            No appointments today
                        @endif
                    </p>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-green-50 p-4 sm:p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-green-800 text-sm font-medium mb-2">Total Revenue</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-green-900">₱{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-green-600 text-sm mt-2">Overall earnings</p>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="bg-yellow-50 p-4 sm:p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-yellow-800 text-sm font-medium mb-2">Pending Appointments</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-900">{{ $pendingAppointments }}</p>
                    <p class="text-yellow-600 text-sm mt-2">Awaiting service</p>
                </div>
            </div>

            <!-- Rating -->
            <div class="bg-purple-50 p-4 sm:p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-purple-800 text-sm font-medium mb-2">Shop Rating</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-purple-900">{{ number_format($shop->rating, 1) }}</p>
                    <p class="text-purple-600 text-sm mt-2">Average customer rating</p>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Appointments</h2>
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <select id="statusFilter" 
                            class="border rounded-md px-3 py-1.5 text-sm w-full sm:w-auto focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    
                    <select id="employeeFilter" 
                            class="border rounded-md px-3 py-1.5 text-sm w-full sm:w-auto focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Employees</option>
                        @if(isset($employees) && count($employees) > 0)
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    
                    <div class="flex flex-col sm:flex-row gap-2 items-center w-full sm:w-auto">
                        <div class="relative w-full sm:w-auto">
                            <input type="date" 
                                   id="startDateFilter"
                                   class="border rounded-md px-3 py-1.5 text-sm w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ request('start_date') }}"
                                   placeholder="Start Date">
                            <label for="startDateFilter" class="absolute -top-5 left-0 text-xs text-gray-500">From</label>
                        </div>
                        
                        <div class="relative w-full sm:w-auto">
                            <input type="date" 
                                   id="endDateFilter"
                                   class="border rounded-md px-3 py-1.5 text-sm w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ request('end_date') }}"
                                   placeholder="End Date">
                            <label for="endDateFilter" class="absolute -top-5 left-0 text-xs text-gray-500">To</label>
                        </div>
                    </div>
                    
                    <button id="applyFilters"
                            class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Apply Filters
                    </button>
                    
                    <button id="clearFilters"
                            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-md text-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Clear
                    </button>
                </div>
                </div>
            </div>

            <!-- Table for larger screens -->
                <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[22%]">
                                Customer
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                Pet
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                Service
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                Employee
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[12%]">
                                Date & Time
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[8%]">
                                Status
                            </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[8%]">
                                Price
                            </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($appointments as $appointment)
                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors" onclick="viewAppointment({{ $appointment->id }})">
                                    <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ $appointment->user->profile_photo_path ? asset('storage/' . $appointment->user->profile_photo_path) : asset('images/default-profile.png') }}" 
                                                     alt="{{ $appointment->user->first_name }}"
                                                     onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $appointment->user->first_name }} {{ $appointment->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $appointment->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                    <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->pet->breed }}</div>
                                </td>
                                    <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $appointment->service_type }}</div>
                                </td>
                                    <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($appointment->employee)
                                            <div class="flex-shrink-0 h-8 w-8 mr-2">
                                                <img class="h-8 w-8 rounded-full object-cover" 
                                                     src="{{ $appointment->employee->profile_photo ? asset('storage/' . $appointment->employee->profile_photo) : $appointment->employee->getProfilePhotoUrlAttribute() }}" 
                                                     alt="{{ $appointment->employee->name }}"
                                                     onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $appointment->employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $appointment->employee->position }}</div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Not assigned</span>
                                        @endif
                                    </div>
                                </td>
                                    <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->appointment_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </div>
                                </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    ₱{{ number_format($appointment->service_price, 2) }}
                                </td>
                                    <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                        <div class="flex flex-col gap-2 items-end">
                                        @if($appointment->status === 'pending')
                                            <button onclick="showAcceptModal({{ $appointment->id }})" 
                                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-600 bg-green-50 rounded-full hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Accept
                                            </button>
                                            <button onclick="showCancelModal({{ $appointment->id }})"
                                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 bg-red-50 rounded-full hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Cancel
                                            </button>
                                        @elseif($appointment->status === 'accepted')
                                            <button onclick="showMarkAsPaidModal({{ $appointment->id }})"
                                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 rounded-full hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Mark as Paid
                                            </button>
                                        @elseif($appointment->status === 'completed')
                                            <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-full hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Notes
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-lg font-medium">No appointments found</p>
                                            <p class="text-sm text-gray-400">Try adjusting your filters or check back later</p>
                                        </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Card view for mobile screens -->
                <div class="md:hidden divide-y divide-gray-200">
                @forelse($appointments as $appointment)
                        <div class="p-4 hover:bg-gray-50 cursor-pointer transition-colors" onclick="viewAppointment({{ $appointment->id }})">
                            <!-- Customer and Status -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full overflow-hidden mr-3">
                                        <img class="h-12 w-12 rounded-full object-cover" 
                                             src="{{ $appointment->user->profile_photo_path ? asset('storage/' . $appointment->user->profile_photo_path) : asset('images/default-profile.png') }}" 
                                             alt="{{ $appointment->user->first_name }}"
                                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                    </div>
                            <div>
                                        <div class="font-medium text-gray-900">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->user->email }}</div>
                                    </div>
                            </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($appointment->status === 'completed') bg-green-100 text-green-800
                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                        
                            <!-- Appointment Details -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                    <div class="text-sm text-gray-500">Pet</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $appointment->pet->breed }}</div>
                            </div>
                                
                            <div>
                                    <div class="text-sm text-gray-500">Service</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->service_type }}</div>
                            </div>
                                
                            <div>
                                    <div class="text-sm text-gray-500">Date & Time</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $appointment->appointment_date->format('g:i A') }}</div>
                            </div>
                                
                            <div>
                                    <div class="text-sm text-gray-500">Price</div>
                                    <div class="text-sm font-medium text-gray-900">₱{{ number_format($appointment->service_price, 2) }}</div>
                                </div>
                            </div>

                            <!-- Employee Info -->
                            @if($appointment->employee)
                                <div class="flex items-center mb-4 bg-gray-50 p-3 rounded-lg">
                                    <div class="flex-shrink-0 h-8 w-8 mr-2">
                                        <img class="h-8 w-8 rounded-full object-cover" 
                                             src="{{ $appointment->employee->profile_photo ? asset('storage/' . $appointment->employee->profile_photo) : $appointment->employee->getProfilePhotoUrlAttribute() }}" 
                                             alt="{{ $appointment->employee->name }}"
                                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                            </div>
                            <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $appointment->employee->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $appointment->employee->position }}</div>
                            </div>
                        </div>
                            @endif
                        
                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2 mt-4" onclick="event.stopPropagation()">
                            @if($appointment->status === 'pending')
                                <button onclick="showAcceptModal({{ $appointment->id }})" 
                                            class="flex-1 text-center text-green-600 bg-green-50 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Accept
                                </button>
                                <button onclick="showCancelModal({{ $appointment->id }})"
                                            class="flex-1 text-center text-red-600 bg-red-50 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Cancel
                                </button>
                            @elseif($appointment->status === 'accepted')
                                <button onclick="showMarkAsPaidModal({{ $appointment->id }})"
                                            class="w-full text-center text-blue-600 bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Mark as Paid
                                </button>
                            @elseif($appointment->status === 'completed')
                                <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                            class="w-full text-center text-indigo-600 bg-indigo-50 px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ $appointment->shop->type === 'grooming' ? "Groomer's Note" : "Doctor's Note" }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                        <div class="p-8 text-center text-gray-500 bg-gray-50">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-lg font-medium">No appointments found</p>
                                <p class="text-sm text-gray-400">Try adjusting your filters or check back later</p>
                            </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
                @if($appointments->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                {{ $appointments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Note Modal - Made responsive -->
    <div x-show="showNoteModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Modal Header -->
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" x-text="currentShopType === 'grooming' ? 'Groomer\'s Note' : 'Doctor\'s Note'"></h3>
                </div>
                
                <!-- Modal Content -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Employee Information -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg" x-show="appointment">
                        <template x-if="appointment?.employee">
                            <div class="flex items-center">
                                <img :src="appointment.employee.profile_photo_url || '/images/default-avatar.png'" 
                                     :alt="appointment.employee.name"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-gray-900" x-text="appointment.employee.name"></h4>
                                    <p class="text-sm text-gray-500" x-text="appointment.employee.position"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!appointment?.employee">
                            <div class="text-sm text-gray-500">
                                No employee assigned to this appointment
                            </div>
                        </template>
                    </div>

                    <!-- Note Text Area -->
                    <div class="mb-4">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="note" 
                                x-model="currentNote"
                                rows="4"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                                placeholder="Enter your notes here..."></textarea>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Attach Image
                        </label>
                        <div class="mt-1 flex items-center">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Choose Image
                                </span>
                                <input type="file" 
                                       class="sr-only" 
                                       accept="image/*"
                                       @change="handleImageUpload($event)">
                            </label>
                            <span x-show="noteImage" 
                                  x-text="noteImage?.name"
                                  class="ml-3 text-sm text-gray-500 truncate max-w-[150px]"></span>
                        </div>
                    </div>

                    <!-- Existing Note Information -->
                    <div x-show="appointment?.notes" class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Previous Note</h4>
                        <div class="text-sm text-gray-600" x-text="appointment?.notes"></div>
                        <div x-show="appointment?.note_image" class="mt-2">
                            <img :src="appointment?.note_image" 
                                 alt="Note Image" 
                                 class="max-w-full h-auto rounded-lg">
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <template x-if="appointment?.employee">
                                <div>
                                    <span>Added by: </span>
                                    <span x-text="appointment.employee.name"></span>
                                    <span> on </span>
                                    <span x-text="new Date(appointment.updated_at).toLocaleDateString()"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col sm:flex-row-reverse gap-2">
                    <button type="button"
                            @click="saveNote()"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        Save Note
                    </button>
                    <button type="button"
                            @click="closeNoteModal()"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Responsive modal styles for mobile -->
<style>
@media (max-width: 640px) {
    .fixed.inset-0.z-50.overflow-y-auto {
        padding: 0;
    }
    
    .fixed.inset-0.z-50.overflow-y-auto .max-w-lg {
        max-width: 100%;
        margin: 0;
        height: 100%;
        border-radius: 0;
    }
    
    .fixed.inset-0.z-50.overflow-y-auto .transform {
        transform: none !important;
    }
}
</style>

<!-- Accept Modal - Made responsive -->
<div id="acceptModal" 
     x-data="{ show: false, appointmentId: null }"
     x-show="show"
     @accept-modal.window="show = true; appointmentId = $event.detail.appointmentId"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
     x-cloak>
    <div class="relative mx-auto p-4 sm:p-8 border w-full max-w-md sm:w-[500px] shadow-lg rounded-md bg-white">
        <div class="text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Accept Appointment</h3>
            <div class="mt-2">
                <p class="text-base text-gray-600 mb-6">Are you sure you want to accept this appointment?</p>
                <p class="text-sm text-gray-500 mb-6">
                    By accepting this appointment, you confirm that you can provide the requested service at the scheduled time.
                </p>
            </div>
            <div class="flex flex-col gap-4 mt-8">
                <button @click="acceptAppointment(appointmentId)" 
                        class="w-full px-6 py-2.5 bg-green-600 text-white font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 transition-colors">
                    Yes, accept appointment
                </button>
                <button @click="show = false" 
                        class="w-full px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                    No, go back
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" 
     x-data="{ show: false, appointmentId: null }"
     x-show="show"
     @cancel-modal.window="show = true; appointmentId = $event.detail.appointmentId"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
     x-cloak>
    <div class="relative mx-auto p-8 border w-[500px] shadow-lg rounded-md bg-white">
        <div class="text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Cancel Appointment</h3>
            <div class="mt-2">
                <p class="text-base text-gray-600 mb-6">Are you sure you want to cancel this appointment?</p>
                <div class="mb-6">
                    <label class="block text-left text-sm font-medium text-gray-700 mb-2">
                        Reason for Cancellation:
                    </label>
                    <textarea id="cancelReason"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                            rows="4"></textarea>
                </div>
            </div>
            <div class="flex flex-col gap-4 mt-8">
                <button @click="cancelAppointment(appointmentId)" 
                        class="w-full px-6 py-2.5 bg-red-600 text-white font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors">
                    Yes, cancel appointment
                </button>
                <button @click="show = false" 
                        class="w-full px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                    No, go back
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div id="markAsPaidModal" 
     x-data="{ show: false, appointmentId: null }"
     x-show="show"
     @mark-as-paid-modal.window="show = true; appointmentId = $event.detail.appointmentId"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
     x-cloak>
    <div class="relative mx-auto p-8 border w-[500px] shadow-lg rounded-md bg-white">
        <div class="text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Mark Appointment as Paid</h3>
            <div class="mt-2">
                <p class="text-base text-gray-600 mb-6">
                    Are you sure you want to mark this appointment as paid and completed?
                </p>
                <p class="text-sm text-gray-500 mb-6">
                    This action cannot be undone and will be recorded in your revenue.
                </p>
            </div>
            <div class="flex flex-col gap-4 mt-8">
                <button @click="markAsPaid(appointmentId)" 
                        class="w-full px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors">
                    Yes, mark as paid
                </button>
                <button @click="show = false" 
                        class="w-full px-6 py-2.5 text-gray-600 font-medium hover:text-gray-800 transition-colors">
                    No, go back
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
function showAcceptModal(appointmentId) {
    window.dispatchEvent(new CustomEvent('accept-modal', {
        detail: { appointmentId: appointmentId }
    }));
}

function showCancelModal(appointmentId) {
    window.dispatchEvent(new CustomEvent('cancel-modal', {
        detail: { appointmentId: appointmentId }
    }));
}

function showMarkAsPaidModal(appointmentId) {
    window.dispatchEvent(new CustomEvent('mark-as-paid-modal', {
        detail: { appointmentId: appointmentId }
    }));
}

function viewAppointment(appointmentId) {
    // Show loading screen before navigation
    showGlobalLoading();
    // Navigate to the appointment details page with from=dashboard parameter
    window.location.href = `/shop/appointments/${appointmentId}?from=dashboard`;
}

async function acceptAppointment(appointmentId) {
    try {
        // Show loading screen
        showGlobalLoading();
        
        const response = await fetch(`/appointments/${appointmentId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        // Hide loading screen
        hideGlobalLoading();
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            alert('Appointment accepted successfully');
            window.location.reload();
        } else {
            alert(data.error || 'Failed to accept appointment');
        }
    } catch (error) {
        // Hide loading screen in case of error
        hideGlobalLoading();
        
        console.error('Error:', error);
        alert('An error occurred while accepting the appointment');
    }
}

async function cancelAppointment(appointmentId) {
    try {
        // Show loading screen
        showGlobalLoading();
        
        const reason = document.getElementById('cancelReason').value;
        const response = await fetch(`/appointments/${appointmentId}/shop-cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });

        // Hide loading screen
        hideGlobalLoading();
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Failed to cancel appointment');
        }
    } catch (error) {
        // Hide loading screen in case of error
        hideGlobalLoading();
        
        console.error('Error:', error);
        alert('An error occurred while cancelling the appointment');
    }
}

async function markAsPaid(appointmentId) {
    try {
        // Show loading screen
        showGlobalLoading();
        
        const response = await fetch(`/appointments/${appointmentId}/mark-as-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        // Hide loading screen
        hideGlobalLoading();
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Failed to mark appointment as paid');
        }
    } catch (error) {
        // Hide loading screen in case of error
        hideGlobalLoading();
        
        console.error('Error:', error);
        alert('An error occurred while updating the appointment');
    }
}

async function getNoteForAppointment(id) {
    try {
        // Show loading screen
        showGlobalLoading();
        
        const response = await fetch(`/appointments/${id}/note`);
        
        // Hide loading screen
        hideGlobalLoading();
        
        const data = await response.json();
        if (data.success) {
            return {
                note: data.note,
                appointment: data.appointment
            };
        }
        throw new Error(data.error || 'Failed to fetch note');
    } catch (error) {
        // Hide loading screen in case of error
        hideGlobalLoading();
        
        console.error('Error:', error);
        return { note: '', appointment: null };
    }
}

function addNote(id, shopType, note, image) {
    return new Promise(async (resolve, reject) => {
        try {
            // Show loading screen
            showGlobalLoading();
            
            const formData = new FormData();
            formData.append('note', note);
            if (image) {
                formData.append('image', image);
            }

            const response = await fetch(`/appointments/${id}/add-note`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            // Hide loading screen
            hideGlobalLoading();
            
            const data = await response.json();
            if (data.success) {
                const noteType = shopType === 'grooming' ? "Groomer's" : "Doctor's";
                showNotification(`${noteType} note saved successfully!`, 'success');
                resolve(data);
            } else {
                throw new Error(data.error || 'Failed to save note');
            }
        } catch (error) {
            // Hide loading screen in case of error
            hideGlobalLoading();
            
            console.error('Error:', error);
            showNotification(`Failed to save note: ${error.message}`, 'error');
            reject(error);
        }
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } transition-opacity duration-500 z-50`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}

// Check for screen size changes to adjust UI
window.addEventListener('resize', function() {
    const tableWrapper = document.querySelector('.overflow-x-auto');
    if (tableWrapper) {
        tableWrapper.style.maxWidth = window.innerWidth < 640 ? '100vw' : 'none';
    }
});

// Make sure the loading screen functions are available in this context
document.addEventListener('DOMContentLoaded', () => {
    if (typeof showGlobalLoading !== 'function') {
        console.warn('Loading screen functions not available. Make sure loading-screen.js is loaded properly.');
    }
});

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Apply filters button click handler
    document.getElementById('applyFilters').addEventListener('click', function() {
        applyFilters();
    });
    
    // Clear filters button click handler
    document.getElementById('clearFilters').addEventListener('click', function() {
        clearFilters();
    });
    
    // Initialize date inputs with current values from URL
    initializeFiltersFromUrl();
});

/**
 * Apply all filters and navigate to filtered URL
 */
function applyFilters() {
    // Show loading screen
    showGlobalLoading();
    
    // Get current values
    const status = document.getElementById('statusFilter').value;
    const employeeId = document.getElementById('employeeFilter').value;
    const startDate = document.getElementById('startDateFilter').value;
    const endDate = document.getElementById('endDateFilter').value;
    
    // Build query parameters
    let params = new URLSearchParams();
    
    // Only add parameters that have values and aren't default
    if (status && status !== 'all') {
        params.append('status', status);
    }
    
    if (employeeId && employeeId !== 'all') {
        params.append('employee_id', employeeId);
    }
    
    if (startDate) {
        params.append('start_date', startDate);
    }
    
    if (endDate) {
        params.append('end_date', endDate);
    }
    
    // Navigate to the filtered URL
    const baseUrl = '{{ route('shop.dashboard') }}';
    const queryString = params.toString();
    
    window.location.href = queryString ? `${baseUrl}?${queryString}` : baseUrl;
}

/**
 * Clear all filters and reload the page
 */
function clearFilters() {
    // Show loading screen
    showGlobalLoading();
    
    // Navigate to the base URL without any filters
    window.location.href = '{{ route('shop.dashboard') }}';
}

/**
 * Initialize filter inputs from URL parameters
 */
function initializeFiltersFromUrl() {
    // Parse URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Set status filter
    const status = urlParams.get('status');
    if (status) {
        document.getElementById('statusFilter').value = status;
    }
    
    // Set employee filter
    const employeeId = urlParams.get('employee_id');
    if (employeeId) {
        document.getElementById('employeeFilter').value = employeeId;
    }
    
    // Set date filters
    const startDate = urlParams.get('start_date');
    if (startDate) {
        document.getElementById('startDateFilter').value = startDate;
    }
    
    const endDate = urlParams.get('end_date');
    if (endDate) {
        document.getElementById('endDateFilter').value = endDate;
    }
}
</script>
@endpush 