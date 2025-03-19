@extends('layouts.shop')

@section('content')
@php
    // Get shop employees if not already provided
    if (!isset($shop)) {
        $shop = auth()->user()->shop;
    }
    $employees = $shop->employees ?? \App\Models\Employee::where('shop_id', $shop->id)->get();
@endphp
<div class="container mx-auto px-4 py-6" 
     x-data="{ 
        showNoteModal: false, 
        noteText: '', 
        imagePreview: null,
        handleImagePreview(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        async submitNote() {
            const noteText = this.noteText;
            const noteImage = document.getElementById('noteImage').files[0];
            
            if (!noteText && !noteImage) {
                alert('Please add either a note or an image');
                return;
            }

            // Get the form element
            const form = document.getElementById('noteForm');
            const formData = new FormData(form);
            
            // Make sure the note field has a value (even if empty)
            formData.set('note', noteText || '');
            
            // Ensure we have the image if selected (should already be in the form, but let's make sure)
            if (noteImage) {
                formData.set('note_image', noteImage);
            }

            try {
                const response = await fetch(`{{ route('shop.appointments.add-note', ['appointment' => $appointment->id]) }}`, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    // Handle error responses
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || `Error: ${response.status}`);
                    } else {
                        throw new Error(`Server error: ${response.status}`);
                    }
                }

                const result = await response.json();

                if (result.success) {
                    this.showNoteModal = false;
                    this.noteText = '';
                    this.imagePreview = null;
                    window.location.reload();
                } else {
                    alert(result.error || 'Failed to add note');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(`An error occurred while adding the note: ${error.message}`);
            }
        }
     }">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        @if(request()->has('from') && request()->get('from') === 'dashboard')
            <a href="{{ route('shop.dashboard') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>
        @else
            <a href="{{ route('shop.appointments') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Appointments
            </a>
        @endif
    </div>

    <!-- Appointment Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-teal-600 text-white p-4">
            <h1 class="text-2xl font-bold">Appointment Details</h1>
        </div>

        <!-- Appointment Info -->
        <div class="p-6">
            
            <!-- Status Badge -->
            <div class="mb-6">
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                    @elseif($appointment->status === 'accepted') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ ucfirst($appointment->status) }}
                </span>
                
                @if($appointment->payment_status === 'paid')
                <span class="ml-2 px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Paid
                </span>
                @else
                <span class="ml-2 px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                    Unpaid
                </span>
                @endif
            </div>

            <!-- Cancellation Reason (if applicable) -->
            @if($appointment->status === 'cancelled' && $appointment->cancellation_reason)
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-lg">
                <h3 class="text-sm font-medium text-red-800 mb-2">Cancellation Reason:</h3>
                <p class="text-red-700">{{ $appointment->cancellation_reason }}</p>
                @if($appointment->cancelled_by)
                <p class="mt-2 text-sm text-red-600">Cancelled by: {{ $appointment->cancelled_by === 'shop' ? 'Shop' : 'Customer' }}</p>
                @endif
                @if($appointment->cancelled_at)
                <p class="mt-1 text-sm text-red-600">Cancelled on: {{ $appointment->cancelled_at->format('F j, Y g:i A') }}</p>
                @endif
            </div>
            @endif

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Customer Info -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Customer</h3>
                    <p class="mt-1 text-lg">{{ $appointment->user->name }}</p>
                    <p class="text-gray-600">{{ $appointment->user->email }}</p>
                    <p class="text-gray-600">{{ $appointment->user->phone }}</p>
                    
                    <!-- Report User Button -->
                    <button class="mt-2 text-red-600 hover:text-red-800 flex items-center text-sm" 
                            onclick="reportUser()">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Report User
                    </button>
                </div>
                
                <!-- Date & Time -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date & Time</h3>
                    <p class="mt-1 text-lg">
                        {{ $appointment->appointment_date->format('l, F j, Y') }}<br>
                        {{ $appointment->appointment_date->format('g:i A') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Service Details -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Service</h3>
                    <p class="mt-1 text-lg">{{ $appointment->service_type }}</p>
                    <p class="text-blue-600 font-medium">PHP {{ number_format($appointment->service_price, 2) }}</p>
                    
                    <!-- Additional Notes from Booking -->
                    @if($appointment->notes)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-500">Additional Notes from Booking</h4>
                            <p class="mt-1 text-gray-700 bg-gray-50 p-3 rounded-md">{{ $appointment->notes }}</p>
                        </div>
                    @endif
                </div>
                
                <!-- Employee -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Employee Assigned</h3>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-lg">{{ $appointment->employee ? $appointment->employee->name : 'Not assigned' }}</p>
                        @if($appointment->status === 'pending' || $appointment->status === 'accepted')
                        <button onclick="showReassignEmployeeModal()" 
                                class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Reassign
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Appointment Notes Section -->
            @if($appointment->appointmentNotes && $appointment->appointmentNotes->count() > 0)
            <div class="mt-6 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Appointment Notes ({{ $appointment->appointmentNotes->count() }})</h3>
                <div class="space-y-4">
                    @foreach($appointment->appointmentNotes->sortByDesc('created_at') as $note)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-start">
                            @if($note->image)
                            <div class="mr-4">
                                <img src="{{ Storage::url($note->image) }}" 
                                     alt="Note image" 
                                     class="w-24 h-24 object-cover rounded-lg">
                            </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-gray-700">{{ $note->note }}</p>
                                <p class="text-sm text-gray-500 mt-2">
                                    Added on {{ $note->created_at->format('M d, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            
            @endif
        </div>
    </div>

    <!-- Pet Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-teal-600 text-white p-4">
            <h2 class="text-xl font-bold">Pet Information</h2>
        </div>
        
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="relative">
                    <img src="{{ $appointment->pet->profile_photo_url ?? asset('images/default-pet.png') }}"
                         alt="{{ $appointment->pet->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold">{{ $appointment->pet->name }}</h2>
                    <p class="text-gray-600">{{ $appointment->pet->breed }} • {{ $appointment->pet->type }}</p>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <div class="flex items-center mt-1">
                        @if($appointment->pet->status === 'deceased')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                Deceased
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date of Birth</p>
                    <p class="font-medium">
                        {{ $appointment->pet->date_of_birth ? $appointment->pet->date_of_birth->format('M d, Y') : 'Not set' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Age</p>
                    <p class="font-medium">
                        @php
                            $birthDate = $appointment->pet->date_of_birth;
                            if ($birthDate) {
                                $now = now();
                                $years = (int)$birthDate->diffInYears($now);
                                $totalMonths = (int)$birthDate->diffInMonths($now);
                                $months = (int)($totalMonths % 12);
                                $days = (int)$birthDate->copy()->addMonths($totalMonths)->diffInDays($now);
                                
                                if ($years > 0) {
                                    echo $years . ' ' . \Illuminate\Support\Str::plural('year', $years);
                                    if ($months > 0) {
                                        echo ' and ' . $months . ' ' . \Illuminate\Support\Str::plural('month', $months);
                                    }
                                } else {
                                    if ($months > 0) {
                                        echo $months . ' ' . \Illuminate\Support\Str::plural('month', $months);
                                        if ($days > 0) {
                                            echo ' and ' . $days . ' ' . \Illuminate\Support\Str::plural('day', $days);
                                        }
                                    } else {
                                        $days = (int)$birthDate->diffInDays($now);
                                        echo $days . ' ' . \Illuminate\Support\Str::plural('day', $days);
                                    }
                                }
                                echo ' old';
                            } else {
                                echo 'Not set';
                            }
                        @endphp
                    </p>
                </div>
            </div>

            <!-- Additional Pet Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Weight</p>
                    <p class="font-medium">{{ $appointment->pet->weight ?? 'Not set' }} {{ $appointment->pet->weight ? 'kg' : '' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Size Category</p>
                    <p class="font-medium">{{ \Illuminate\Support\Str::title($appointment->pet->size_category ?? 'Not set') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Color/Markings</p>
                    <p class="font-medium">{{ $appointment->pet->color_markings ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Coat Type</p>
                    <p class="font-medium">{{ $appointment->pet->coat_type ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Type</p>
                    <p class="font-medium">{{ $appointment->pet->type }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Breed</p>
                    <p class="font-medium">{{ $appointment->pet->breed }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Health Records -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-teal-600 text-white p-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">Pet Health Records</h2>
            <a href="{{ route('profile.pets.add-health-record', $appointment->pet->id) }}" 
               class="px-4 py-2 bg-white text-teal-600 rounded-md hover:bg-gray-100 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Health Record
            </a>
        </div>
        
        <div class="p-6">
            @if($appointment->pet->vaccinations->count() > 0 || $appointment->pet->parasiteControls->count() > 0 || $appointment->pet->healthIssues->count() > 0)
                <!-- Tabs -->
                <div x-data="{ activeTab: 'vaccinations' }" class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'vaccinations'" 
                                    :class="{'border-teal-500 text-teal-600': activeTab === 'vaccinations', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'vaccinations'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Vaccinations
                            </button>
                            <button @click="activeTab = 'parasiteControl'" 
                                    :class="{'border-teal-500 text-teal-600': activeTab === 'parasiteControl', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'parasiteControl'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Parasite Control
                            </button>
                            <button @click="activeTab = 'healthIssues'" 
                                    :class="{'border-teal-500 text-teal-600': activeTab === 'healthIssues', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'healthIssues'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Health Issues
                            </button>
                        </nav>
                    </div>
                    
                    <!-- Vaccinations Tab -->
                    <div x-show="activeTab === 'vaccinations'" class="mt-6">
                        @if($appointment->pet->vaccinations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaccine</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Due</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Administered By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($appointment->pet->vaccinations as $vaccination)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $vaccination->vaccine_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $vaccination->administered_date->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $vaccination->next_due_date->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $vaccination->administered_by }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No vaccination records found</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Parasite Control Tab -->
                    <div x-show="activeTab === 'parasiteControl'" class="mt-6" x-cloak>
                        @if($appointment->pet->parasiteControls->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treatment</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Due</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($appointment->pet->parasiteControls as $treatment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $treatment->treatment_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $treatment->treatment_type }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $treatment->treatment_date->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $treatment->next_treatment_date->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No parasite control records found</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Health Issues Tab -->
                    <div x-show="activeTab === 'healthIssues'" class="mt-6" x-cloak>
                        @if($appointment->pet->healthIssues->count() > 0)
                            <div class="space-y-6">
                                @foreach($appointment->pet->healthIssues as $issue)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="font-medium text-lg mb-2">{{ $issue->issue_title }}</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Date Identified</p>
                                                <p>{{ $issue->identified_date->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Treatment/Medication</p>
                                                <p>{{ $issue->treatment }}</p>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Description</p>
                                            <p>{{ $issue->description }}</p>
                                        </div>
                                        @if($issue->vet_notes)
                                            <div>
                                                <p class="text-sm text-gray-500">Veterinary Notes</p>
                                                <p>{{ $issue->vet_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No health issues recorded</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 mb-4">No health records found for this pet</p>
                    <p class="text-gray-700">Click the "Add Health Record" button to add vaccination records, parasite control information, or health issues.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex flex-wrap justify-end gap-3">
                @if($appointment->status === 'pending')
                    <button onclick="showAcceptModal({{ $appointment->id }})" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                        Accept
                    </button>
                    <button onclick="cancelAppointment({{ $appointment->id }})" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                        Cancel
                    </button>
                @endif
                
                @if($appointment->status === 'accepted')
                    <button onclick="showMarkAsPaidModal({{ $appointment->id }})" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 {{ $appointment->payment_status === 'paid' ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $appointment->payment_status === 'paid' ? 'disabled' : '' }}>
                        {{ $appointment->payment_status === 'paid' ? 'Already Paid' : 'Mark as Paid' }}
                    </button>
                @endif
                
                @if($appointment->status === 'completed')
                    <a href="{{ route('appointments.follow-up-form', $appointment) }}" 
                       class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 inline-block">
                        Schedule Follow-up
                    </a>
                @endif
                
                @if($appointment->status === 'accepted' || $appointment->status === 'completed')
                    <button @click="showNoteModal = true"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Add Note
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Accept Confirmation Modal -->
    <div id="acceptModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Accept Appointment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to accept this appointment? This will confirm the booking with the customer.
                    </p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button onclick="hideAcceptModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button id="confirmAcceptBtn"
                            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                        Yes, Accept
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Paid Confirmation Modal -->
    <div id="markAsPaidModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Mark as Paid</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to mark this appointment as paid? This action cannot be undone.
                    </p>
                    <p class="text-sm font-medium text-blue-600 mt-2">
                        Amount: PHP {{ number_format($appointment->service_price, 2) }}
                    </p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button onclick="hideMarkAsPaidModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button id="confirmMarkAsPaidBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Yes, Mark as Paid
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div x-show="showNoteModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         x-cloak
         @click.away="showNoteModal = false">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add Appointment Note</h3>
                <div class="mt-4">
                    <div class="space-y-4">
                        <form id="noteForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div>
                                <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
                                <textarea id="note" 
                                        x-model="noteText"
                                        name="note"
                                        rows="4" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                        placeholder="Enter your note here..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Add Image (optional)</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" 
                                           id="noteImage"
                                           name="note_image" 
                                           accept="image/*"
                                           @change="handleImagePreview($event)"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                </div>
                                <!-- Image Preview -->
                                <div x-show="imagePreview" class="mt-2">
                                    <img :src="imagePreview" class="max-h-32 rounded">
                                </div>
                            </div>
                        </form>
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    @click="showNoteModal = false"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="button" 
                                    @click="submitNote()"
                                    class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                                Save Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report User Modal -->
<div id="reportUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 relative">
            <!-- Close Button -->
            <button onclick="closeReportUserModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Report User</h3>
                <p class="text-sm text-gray-600 mt-1">Please provide details about your concern with this user</p>
            </div>

            <!-- Report Form Component -->
            <x-report-form type="user" :id="$appointment->user->id" />
        </div>
    </div>
</div>

<!-- Reassign Employee Modal -->
<div id="reassignEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 relative">
            <!-- Close Button -->
            <button onclick="closeReassignEmployeeModal()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Reassign Employee</h3>
                <p class="text-sm text-gray-600 mt-1">Select an employee to handle this appointment</p>
            </div>

            <!-- Employee Selection Form -->
            <form id="reassignEmployeeForm" class="space-y-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                
                <!-- Employee Dropdown -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Select Employee</label>
                    <select id="employee_id" name="employee_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select an employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $appointment->employee_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} - {{ $employee->position }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="button"
                        onclick="reassignEmployee()"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                        id="reassignBtn">
                    Reassign
                </button>
                <div id="reassignStatusMessage" class="mt-2 text-center hidden"></div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentAppointmentId = null;

// Function to show toast notification
function showNotificationToast(title, message) {
    // Create the toast element
    const toast = document.createElement('div');
    toast.classList.add('fixed', 'top-4', 'right-4', 'bg-white', 'shadow-lg', 'rounded-lg', 'p-4', 'z-50', 'flex', 'items-start', 'max-w-sm', 'transform', 'transition-all', 'duration-300', 'ease-in-out', 'translate-y-[-100px]', 'opacity-0');
    
    // Set the toast content
    toast.innerHTML = `
        <div class="flex-shrink-0 mr-3">
            <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-2 2m0 0l-2-2m2 2v6" />
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="font-medium text-gray-900">${title}</h3>
            <p class="mt-1 text-sm text-gray-600">${message}</p>
        </div>
        <button class="ml-4 text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    // Add the toast to the document
    document.body.appendChild(toast);
    
    // Animate the toast in
    setTimeout(() => {
        toast.classList.remove('translate-y-[-100px]', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);
    
    // Remove the toast after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-y-[-100px]', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Accept Modal Functions
function showAcceptModal(appointmentId) {
    currentAppointmentId = appointmentId;
    const modal = document.getElementById('acceptModal');
    modal.classList.remove('hidden');
    
    // Add event listener to confirm button
    document.getElementById('confirmAcceptBtn').onclick = function() {
        acceptAppointment(currentAppointmentId);
    };
}

// Reassign Employee Modal Functions
function showReassignEmployeeModal() {
    document.getElementById('reassignEmployeeModal').classList.remove('hidden');
    document.getElementById('reassignStatusMessage').classList.add('hidden');
}

function closeReassignEmployeeModal() {
    document.getElementById('reassignEmployeeModal').classList.add('hidden');
}

function reassignEmployee() {
    const employeeId = document.getElementById('employee_id').value;
    const appointmentId = {{ $appointment->id }};
    const statusMessage = document.getElementById('reassignStatusMessage');
    const reassignBtn = document.getElementById('reassignBtn');
    
    // Validate selection
    if (!employeeId) {
        statusMessage.textContent = 'Please select an employee';
        statusMessage.classList.remove('hidden', 'text-green-500');
        statusMessage.classList.add('text-red-500');
        return;
    }
    
    // Show loading state
    reassignBtn.disabled = true;
    reassignBtn.textContent = 'Reassigning...';
    
    // Send AJAX request
    fetch('/shop/appointments/' + appointmentId + '/reassign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            employee_id: employeeId
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Error reassigning employee');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            statusMessage.textContent = 'Employee reassigned successfully!';
            statusMessage.classList.remove('hidden', 'text-red-500');
            statusMessage.classList.add('text-green-500');
            
            // Show notification toast to user
            showNotificationToast('Appointment Updated', 'The employee for your appointment has been reassigned. A notification has been sent to the customer.');
            
            // Reload the page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to reassign employee');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusMessage.textContent = error.message || 'An error occurred. Please try again.';
        statusMessage.classList.remove('hidden', 'text-green-500');
        statusMessage.classList.add('text-red-500');
    })
    .finally(() => {
        // Reset button state
        reassignBtn.disabled = false;
        reassignBtn.textContent = 'Reassign';
        statusMessage.classList.remove('hidden');
    });
}

function hideAcceptModal() {
    const modal = document.getElementById('acceptModal');
    modal.classList.add('hidden');
    currentAppointmentId = null;
}

// Mark as Paid Modal Functions
function showMarkAsPaidModal(appointmentId) {
    currentAppointmentId = appointmentId;
    const modal = document.getElementById('markAsPaidModal');
    modal.classList.remove('hidden');
    
    // Add event listener to confirm button
    document.getElementById('confirmMarkAsPaidBtn').onclick = function() {
        markAsPaid(currentAppointmentId);
    };
}

function hideMarkAsPaidModal() {
    const modal = document.getElementById('markAsPaidModal');
    modal.classList.add('hidden');
    currentAppointmentId = null;
}

// Updated Mark as Paid Function
async function markAsPaid(appointmentId) {
    try {
        // Disable confirm button and show loading state
        const confirmBtn = document.getElementById('confirmMarkAsPaidBtn');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        const response = await fetch(`{{ route('shop.appointments.add-note', ['appointment' => $appointment->id]) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            // Hide modal first
            hideMarkAsPaidModal();
            // Show success message
            alert('Payment recorded successfully');
            // Reload the page to show updated status
            window.location.reload();
        } else {
            alert(data.error || 'Failed to record payment');
            // Reset button state
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Yes, Mark as Paid';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while recording the payment');
        // Reset button state
        const confirmBtn = document.getElementById('confirmMarkAsPaidBtn');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Yes, Mark as Paid';
    }
}

// Report User Functions
function reportUser() {
    document.getElementById('reportUserModal').classList.remove('hidden');
}

function closeReportUserModal() {
    document.getElementById('reportUserModal').classList.add('hidden');
    // Our new component handles form resetting internally, so we don't need to reset it here
    // Just make sure the modal is hidden
}

// We don't need the old submitUserReport function as our component handles form submission
// The component handles validation and submission via its own event listeners
</script>
@endpush
@endsection 