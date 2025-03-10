@extends('layouts.shop')

@section('content')
<script>
    // Make appointment action functions globally accessible
    window.viewAppointment = function(id) {
        viewAppointment(id);
    };
    
    window.acceptAppointment = function(id) {
        acceptAppointment(id);
    };
    
    window.cancelAppointment = function(id) {
        cancelAppointment(id);
    };
    
    window.markAsPaid = function(id) {
        markAsPaid(id);
    };
</script>

<div x-data="{
    showFilters: false,
    currentFilter: 'all',
    dateFilter: '',
    serviceTypeFilter: 'all',
    showNoteModal: false,
    currentAppointmentId: null,
    currentShopType: null,
    currentNote: '',
    noteImage: null,
    activeTab: 'appointments',
    dismissNotification: false,
    
    isAppointmentVisible(status, date, serviceType) {
        if (this.currentFilter !== 'all' && status !== this.currentFilter) return false;
        if (this.dateFilter && date !== this.dateFilter) return false;
        if (this.serviceTypeFilter !== 'all' && serviceType !== this.serviceTypeFilter) return false;
        return true;
    },

    openNoteModal(appointmentId, shopType) {
        this.currentAppointmentId = appointmentId;
        this.currentShopType = shopType;
        this.showNoteModal = true;
        this.loadExistingNote();
    },

    async loadExistingNote() {
        const note = await getNoteForAppointment(this.currentAppointmentId);
        this.currentNote = note || '';
    },

    closeNoteModal() {
        this.showNoteModal = false;
        this.currentNote = '';
        this.noteImage = null;
        this.currentAppointmentId = null;
        this.currentShopType = null;
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
    }
}" class="container mx-auto px-4 py-6">
    
    <!-- New Appointment Notification Alert -->
    @if(isset($newAppointments) && $newAppointments > 0)
    <div x-show="!dismissNotification" 
         class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded shadow-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3 flex-1 md:flex md:justify-between">
                <p class="text-sm text-blue-700">
                    You have <span class="font-bold">{{ $newAppointments }}</span> new customer {{ Str::plural('appointment', $newAppointments) }}!
                </p>
                <div class="mt-3 text-sm md:mt-0 md:ml-6">
                    <button @click="dismissNotification = true" 
                            class="text-blue-700 hover:text-blue-600 font-medium">
                        Dismiss
                    </button>
                    <a href="#" class="ml-4 text-blue-700 hover:text-blue-600 font-medium">
                        View all
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Appointments</h1>
        
        <!-- Filter Toggle Button -->
        <button @click="showFilters = !showFilters"
                class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
        </button>
    </div>

    <!-- Filters Section -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="currentFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Service Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                <select x-model="serviceTypeFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Services</option>
                    <option value="veterinary">Veterinary</option>
                    <option value="grooming">Grooming</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" 
                       x-model="dateFilter"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6">
        <nav class="flex space-x-4 border-b">
            <button @click="activeTab = 'appointments'"
                    :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'appointments',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'appointments'}"
                    class="px-3 py-2 text-sm font-medium">
                Appointments
            </button>
            <button @click="activeTab = 'cancellations'"
                    :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'cancellations',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'cancellations'}"
                    class="px-3 py-2 text-sm font-medium">
                Cancellation Requests
                @if($pendingCancellations > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">
                        {{ $pendingCancellations }}
                    </span>
                @endif
            </button>
            <button @click="activeTab = 'reschedules'"
                    :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'reschedules',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'reschedules'}"
                    class="px-3 py-2 text-sm font-medium">
                Reschedule Requests
                @if($pendingReschedules > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">
                        {{ $pendingReschedules }}
                    </span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Regular Appointments Tab -->
    <div x-show="activeTab === 'appointments'" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="space-y-6">
        @include('shop.appointments.partials._appointments_table', ['appointments' => $appointments])
    </div>

    <!-- Cancellation Requests Tab -->
    <div x-show="activeTab === 'cancellations'"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="space-y-6">
        @include('shop.appointments.partials._cancellation_requests_table', ['cancellationRequests' => $cancellationRequests])
    </div>

    <!-- Reschedule Requests Tab -->
    <div x-show="activeTab === 'reschedules'"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="space-y-6">
        @include('shop.appointments.partials._reschedule_requests_table', ['rescheduleRequests' => $rescheduleRequests])
    </div>

    <!-- Note Modal -->
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
                                  class="ml-3 text-sm text-gray-500"></span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-2">
                    <button type="button"
                            @click="saveNote()"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        Save Note
                    </button>
                    <button type="button"
                            @click="closeNoteModal()"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Define all functions first
async function acceptAppointment(id) {
    window.dispatchEvent(new CustomEvent('modal', {
        detail: {
            title: 'Accept Appointment',
            content: 'Are you sure you want to accept this appointment?',
            onConfirm: async () => {
    try {
        const response = await fetch(`/appointments/${id}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        
        const data = await response.json();
        if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Appointment accepted successfully'
                            }
                        }));
            window.location.reload();
        } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'error',
                                message: data.error || 'Failed to accept appointment'
                            }
                        }));
        }
    } catch (error) {
        console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while accepting the appointment'
                        }
                    }));
                }
            }
        }
    }));
}

async function markAsPaid(id) {
    window.dispatchEvent(new CustomEvent('modal', {
        detail: {
            title: 'Mark as Paid',
            content: 'Are you sure you want to mark this appointment as paid?',
            onConfirm: async () => {
    try {
        const response = await fetch(`/appointments/${id}/mark-as-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        
        const data = await response.json();
        if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Appointment marked as paid successfully'
                            }
                        }));
            window.location.reload();
        } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'error',
                                message: data.error || 'Failed to mark appointment as paid'
                            }
                        }));
        }
    } catch (error) {
        console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while updating the appointment'
                        }
                    }));
                }
            }
        }
    }));
}

async function cancelAppointment(id) {
    console.log('cancelAppointment called for ID:', id);
    
    // Create HTML content for the cancel appointment modal with a reason input
    const modalContent = `
        <div>
            <p class="mb-4">Please provide a reason for cancellation:</p>
            <textarea id="cancellationReason" rows="3" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" placeholder="Enter cancellation reason..."></textarea>
        </div>
    `;
    
    // Use Promise to handle the modal interaction more reliably
    new Promise((resolve) => {
        window.dispatchEvent(new CustomEvent('modal', {
            detail: {
                title: 'Cancel Appointment',
                content: modalContent,
                onConfirm: () => resolve(document.getElementById('cancellationReason').value)
            }
        }));
    }).then(async (reason) => {
        if (!reason || !reason.trim()) {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'warning',
                    message: 'Please provide a reason for cancellation'
                }
            }));
            return;
        }
        
        try {
            console.log('Sending cancellation request for appointment:', id);
            console.log('Cancellation reason:', reason);
            
            // Get the CSRF token from the meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
        const response = await fetch(`/appointments/${id}/shop-cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            });
            
            console.log('Cancel response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
        
        const data = await response.json();
            console.log('Cancel response data:', data);
            
        if (data.success) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: 'Appointment cancelled successfully'
                    }
                }));
                // Reload the page after a short delay to show the toast
                setTimeout(() => {
            window.location.reload();
                }, 1000);
        } else {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: data.error || 'Failed to cancel appointment'
                    }
                }));
        }
    } catch (error) {
            console.error('Error cancelling appointment:', error);
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'error',
                    message: 'An error occurred while cancelling the appointment'
                }
            }));
        }
    });
}

async function approveCancellation(id) {
    window.dispatchEvent(new CustomEvent('modal', {
        detail: {
            title: 'Approve Cancellation',
            content: 'Are you sure you want to approve this cancellation request?',
            onConfirm: async () => {
    try {
        const response = await fetch(`/appointments/cancellation/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        
        const data = await response.json();
        if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Cancellation request approved successfully'
                            }
                        }));
            window.location.reload();
        } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'error',
                                message: data.error || 'Failed to approve cancellation request'
                            }
                        }));
        }
    } catch (error) {
        console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while approving the cancellation request'
                        }
                    }));
                }
            }
        }
    }));
}

async function declineCancellation(id) {
    // Create HTML content for the modal with a reason input
    const modalContent = `
        <div>
            <p class="mb-4">Please provide a reason for declining the cancellation:</p>
            <textarea id="declineReason" rows="3" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" placeholder="Enter reason..."></textarea>
        </div>
    `;
    
    window.dispatchEvent(new CustomEvent('modal', {
        detail: {
            title: 'Decline Cancellation',
            content: modalContent,
            onConfirm: async () => {
                const reason = document.getElementById('declineReason').value;
                if (!reason.trim()) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'warning',
                            message: 'Please provide a reason for declining'
                        }
                    }));
                    return;
                }
    
    try {
        const response = await fetch(`/appointments/cancellation/${id}/decline`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ reason })
        });
        
        const data = await response.json();
        if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Cancellation request declined'
                            }
                        }));
            window.location.reload();
        } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'error',
                                message: data.error || 'Failed to decline cancellation request'
                            }
                        }));
        }
    } catch (error) {
        console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while declining the cancellation request'
                        }
                    }));
                }
            }
        }
    }));
}

async function approveReschedule(id) {
    window.dispatchEvent(new CustomEvent('modal', {
        detail: {
            title: 'Approve Reschedule',
            content: 'Are you sure you want to approve this reschedule request?',
            onConfirm: async () => {
    try {
        const response = await fetch(`/appointments/reschedule/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        
        const data = await response.json();
        if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Reschedule request approved successfully'
                            }
                        }));
            window.location.reload();
        } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'error',
                                message: data.error || 'Failed to approve reschedule request'
                            }
                        }));
        }
    } catch (error) {
        console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while approving the reschedule request'
                        }
                    }));
                }
            }
        }
    }));
}

async function declineReschedule(id) {
    // Create HTML content for the modal with a reason input
    const modalContent = `
        <div>
            <p class="mb-4">Please provide a reason for declining the reschedule:</p>
            <textarea id="declineRescheduleReason" rows="3" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" placeholder="Enter reason..."></textarea>
        </div>
    `;
    
    window.dispatchEvent(new CustomEvent('modal', {
        detail: {
            title: 'Decline Reschedule',
            content: modalContent,
            onConfirm: async () => {
                const reason = document.getElementById('declineRescheduleReason').value;
                if (!reason.trim()) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'warning',
                            message: 'Please provide a reason for declining'
                        }
                    }));
                    return;
                }
    
    try {
        const response = await fetch(`/appointments/reschedule/${id}/decline`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ reason })
        });
        
        const data = await response.json();
        if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Reschedule request declined'
                            }
                        }));
            window.location.reload();
        } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'error',
                                message: data.error || 'Failed to decline reschedule request'
                            }
                        }));
        }
    } catch (error) {
        console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'An error occurred while declining the reschedule request'
                        }
                    }));
                }
            }
        }
    }));
}

async function getNoteForAppointment(id) {
    try {
        const response = await fetch(`/appointments/${id}/note`);
        const data = await response.json();
        return data.note;
    } catch (error) {
        console.error('Error:', error);
        return '';
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
                // Show success notification
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
    window.dispatchEvent(new CustomEvent('toast', {
        detail: {
            type: type,
            message: message
        }
    }));
}

// Now make the appointment action functions globally available
window.acceptAppointment = acceptAppointment;
window.cancelAppointment = cancelAppointment;
window.markAsPaid = markAsPaid;
window.viewAppointment = viewAppointment;
window.approveCancellation = approveCancellation;
window.declineCancellation = declineCancellation;
window.approveReschedule = approveReschedule;
window.declineReschedule = declineReschedule;
</script>
@endpush
@endsection 