@extends('layouts.shop')

@section('content')
<div x-data="{
    showFilters: false,
    currentFilter: 'all',
    dateFilter: '',
    showNoteModal: false,
    currentAppointmentId: null,
    currentShopType: null,
    currentNote: '',
    noteImage: null,
    
    isAppointmentVisible(status, date) {
        if (this.currentFilter !== 'all' && status !== this.currentFilter) return false;
        if (this.dateFilter && date !== this.dateFilter) return false;
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

            <!-- Date Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" 
                       x-model="dateFilter"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
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

    <!-- Appointments Table -->
    <div class="space-y-6">
        @forelse($appointments as $date => $dayAppointments)
            <div x-show="dateFilter === '' || dateFilter === '{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}'"
                 class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h2 class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dayAppointments as $appointment)
                                <tr x-show="isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}')"
                                    class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                     src="{{ $appointment->user->profile_photo_path ? asset('storage/' . $appointment->user->profile_photo_path) : asset('images/default-profile.png') }}"
                                                     alt="{{ $appointment->user->name }}"
                                                     onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $appointment->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $appointment->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex flex-col">
                                            <span>{{ $appointment->pet->name }}</span>
                                            <button onclick="event.stopPropagation(); window.location.href='{{ route('profile.pets.health-record', $appointment->pet) }}'"
                                                    class="text-xs text-blue-600 hover:text-blue-800 mt-1 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Health Record
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $appointment->service_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        PHP {{ number_format($appointment->service_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($appointment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            @if($appointment->status === 'pending')
                                                <button onclick="window.location.href='{{ route('appointments.show', $appointment) }}'"
                                                        class="text-blue-600 hover:text-blue-800">
                                                    View
                                                </button>
                                                <button onclick="acceptAppointment({{ $appointment->id }})"
                                                        class="text-green-600 hover:text-green-800">
                                                    Accept
                                                </button>
                                                <button onclick="cancelAppointment({{ $appointment->id }})"
                                                        class="text-red-600 hover:text-red-800">
                                                    Cancel
                                                </button>
                                            @elseif($appointment->status === 'accepted')
                                                <button onclick="markAsPaid({{ $appointment->id }})"
                                                        class="text-green-600 hover:text-green-800">
                                                    Mark as Paid
                                                </button>
                                            @elseif($appointment->status === 'completed')
                                                <div class="flex space-x-2">
                                                    @if($appointment->payment_status === 'paid')
                                                        <a href="{{ route('appointments.official-receipt.download', $appointment) }}"
                                                           class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                                            </svg>
                                                            Receipt
                                                        </a>
                                                    @endif
                                                    <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                                            class="text-indigo-600 hover:text-indigo-800 inline-flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        {{ $appointment->shop->type === 'grooming' ? "Groomer's Note" : "Doctor's Note" }}
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-600">No appointments found</p>
            </div>
        @endforelse
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