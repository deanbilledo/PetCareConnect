@extends('layouts.shop')

@section('content')
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
async function acceptAppointment(id) {
    if (!confirm('Are you sure you want to accept this appointment?')) return;
    
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
            window.location.reload();
        } else {
            alert(data.error || 'Failed to accept appointment');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while accepting the appointment');
    }
}

async function markAsPaid(id) {
    if (!confirm('Are you sure you want to mark this appointment as paid?')) return;
    
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
            window.location.reload();
        } else {
            alert(data.error || 'Failed to mark appointment as paid');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the appointment');
    }
}

async function cancelAppointment(id) {
    const reason = prompt('Please provide a reason for cancellation:');
    if (!reason) return;
    
    try {
        const response = await fetch(`/appointments/${id}/shop-cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ reason })
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

async function approveCancellation(id) {
    if (!confirm('Are you sure you want to approve this cancellation request?')) return;
    
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
            window.location.reload();
        } else {
            alert(data.error || 'Failed to approve cancellation request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while approving the cancellation request');
    }
}

async function declineCancellation(id) {
    const reason = prompt('Please provide a reason for declining the cancellation:');
    if (!reason) return;
    
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
            window.location.reload();
        } else {
            alert(data.error || 'Failed to decline cancellation request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while declining the cancellation request');
    }
}

async function approveReschedule(id) {
    if (!confirm('Are you sure you want to approve this reschedule request?')) return;
    
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
            window.location.reload();
        } else {
            alert(data.error || 'Failed to approve reschedule request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while approving the reschedule request');
    }
}

async function declineReschedule(id) {
    const reason = prompt('Please provide a reason for declining the reschedule:');
    if (!reason) return;
    
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
            window.location.reload();
        } else {
            alert(data.error || 'Failed to decline reschedule request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while declining the reschedule request');
    }
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
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } transition-opacity duration-500`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}
</script>
@endpush
@endsection 