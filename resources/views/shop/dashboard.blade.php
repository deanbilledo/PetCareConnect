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
            console.log('Loading note for appointment:', this.currentAppointmentId);
            const response = await fetch(`/appointments/${this.currentAppointmentId}/note`);
            const data = await response.json();
            console.log('Received note data:', data);
            
            if (data.success) {
                this.currentNote = data.note || '';
                this.appointment = data.appointment;
                
               
            } else {
                console.error('Failed to load note:', data.error);
            }
        } catch (error) {
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
        await addNote(this.currentAppointmentId, this.currentShopType, this.currentNote, this.noteImage);
        this.closeNoteModal();
        window.location.reload();
    }
}" class="w-full">
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
        <div class="mt-8 overflow-hidden">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-xl font-semibold">Recent Appointments</h2>
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <select x-data="{ status: 'all' }" 
                            x-model="status" 
                            @change="window.location.href = '{{ route('shop.dashboard') }}?status=' + status"
                            class="border rounded-md px-3 py-1.5 text-sm w-full sm:w-auto">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="date" 
                           class="border rounded-md px-3 py-1.5 text-sm w-full sm:w-auto"
                           value="{{ request('date') }}"
                           onchange="window.location.href = '{{ route('shop.dashboard') }}?date=' + this.value">
                </div>
            </div>

            <!-- Table for larger screens -->
            <div class="hidden md:block overflow-x-auto rounded-lg border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">
                                Customer
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                Pet
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                Service
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                Employee
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                Date & Time
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                Status
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                Price
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="viewAppointment({{ $appointment->id }})">
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
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
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->pet->breed }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $appointment->service_type }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
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
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->appointment_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 text-sm text-gray-900 font-medium">
                                    ₱{{ number_format($appointment->service_price, 2) }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex flex-wrap justify-end gap-2" onclick="event.stopPropagation()">
                                        @if($appointment->status === 'pending')
                                            <button onclick="showAcceptModal({{ $appointment->id }})" 
                                                    class="text-green-600 hover:text-green-900 bg-green-50 px-2 sm:px-3 py-1 rounded-full text-sm font-medium">
                                                Accept
                                            </button>
                                            <button onclick="showCancelModal({{ $appointment->id }})"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 px-2 sm:px-3 py-1 rounded-full text-sm font-medium">
                                                Cancel
                                            </button>
                                        @elseif($appointment->status === 'accepted')
                                            <button onclick="showMarkAsPaidModal({{ $appointment->id }})"
                                                    class="text-blue-600 hover:text-blue-900 bg-blue-50 px-2 sm:px-3 py-1 rounded-full text-sm font-medium">
                                                Mark as Paid
                                            </button>
                                        @elseif($appointment->status === 'completed')
                                            <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                                    class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 sm:px-3 py-1 rounded-full text-sm font-medium">
                                                Notes
                                            </button>
                                        @elseif($appointment->status === 'cancelled')
                                            <span class="text-red-600 bg-red-50 px-2 sm:px-3 py-1 rounded-full text-sm font-medium">
                                                Cancelled
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 sm:px-6 py-4 text-center text-gray-500">
                                    No appointments found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Card view for mobile screens -->
            <div class="md:hidden space-y-4">
                @forelse($appointments as $appointment)
                    <div class="bg-white rounded-lg border shadow-sm p-4" onclick="viewAppointment({{ $appointment->id }})">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $appointment->user->email }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($appointment->status === 'completed') bg-green-100 text-green-800
                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                            <div>
                                <p class="text-gray-500">Pet:</p>
                                <p>{{ $appointment->pet->name }} ({{ $appointment->pet->breed }})</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Service:</p>
                                <p>{{ $appointment->service_type }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Date:</p>
                                <p>{{ $appointment->appointment_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Time:</p>
                                <p>{{ $appointment->appointment_date->format('g:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Price:</p>
                                <p class="font-medium">₱{{ number_format($appointment->service_price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Employee:</p>
                                <p>
                                    @if($appointment->employee)
                                        {{ $appointment->employee->name }}
                                    @else
                                        Not assigned
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex flex-wrap gap-2" onclick="event.stopPropagation()">
                            @if($appointment->status === 'pending')
                                <button onclick="showAcceptModal({{ $appointment->id }})" 
                                        class="flex-1 text-center text-green-600 hover:text-green-900 bg-green-50 px-3 py-2 rounded-lg text-sm font-medium">
                                    Accept
                                </button>
                                <button onclick="showCancelModal({{ $appointment->id }})"
                                        class="flex-1 text-center text-red-600 hover:text-red-900 bg-red-50 px-3 py-2 rounded-lg text-sm font-medium">
                                    Cancel
                                </button>
                            @elseif($appointment->status === 'accepted')
                                <button onclick="showMarkAsPaidModal({{ $appointment->id }})"
                                        class="w-full text-center text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-2 rounded-lg text-sm font-medium">
                                    Mark as Paid
                                </button>
                            @elseif($appointment->status === 'completed')
                                <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                        class="w-full text-center text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-2 rounded-lg text-sm font-medium">
                                    {{ $appointment->shop->type === 'grooming' ? "Groomer's Note" : "Doctor's Note" }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border p-6 text-center text-gray-500">
                        No appointments found
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6 px-4 py-3 border-t bg-white rounded-b-lg">
                {{ $appointments->links() }}
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
    // Navigate to the appointment details page with from=dashboard parameter
    window.location.href = `/shop/appointments/${appointmentId}?from=dashboard`;
}

async function acceptAppointment(appointmentId) {
    try {
        const response = await fetch(`/appointments/${appointmentId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            // Show success message
            alert('Appointment accepted successfully');
            window.location.reload();
        } else {
            alert(data.error || 'Failed to accept appointment');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while accepting the appointment');
    }
}

async function cancelAppointment(appointmentId) {
    try {
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

        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Failed to cancel appointment');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while cancelling the appointment');
    }
}

async function markAsPaid(appointmentId) {
    try {
        const response = await fetch(`/appointments/${appointmentId}/mark-as-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Failed to mark appointment as paid');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the appointment');
    }
}

async function getNoteForAppointment(id) {
    try {
        const response = await fetch(`/appointments/${id}/note`);
        const data = await response.json();
        if (data.success) {
            return {
                note: data.note,
                appointment: data.appointment
            };
        }
        throw new Error(data.error || 'Failed to fetch note');
    } catch (error) {
        console.error('Error:', error);
        return { note: '', appointment: null };
    }
}

function addNote(id, shopType, note, image) {
    return new Promise(async (resolve, reject) => {
        try {
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

            const data = await response.json();
            if (data.success) {
                const noteType = shopType === 'grooming' ? "Groomer's" : "Doctor's";
                showNotification(`${noteType} note saved successfully!`, 'success');
                resolve(data);
            } else {
                throw new Error(data.error || 'Failed to save note');
            }
        } catch (error) {
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
</script>
@endpush 