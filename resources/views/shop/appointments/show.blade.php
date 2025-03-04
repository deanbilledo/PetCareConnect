@extends('layouts.shop')

@section('content')
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

            const formData = new FormData();
            formData.append('note', noteText);
            if (noteImage) {
                formData.append('note_image', noteImage);
            }
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch(`/appointments/{{ $appointment->id }}/add-note`, {
                    method: 'POST',
                    body: formData
                });

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
                alert('An error occurred while adding the note');
            }
        }
     }">
    <!-- Back Button -->
    <div class="mb-6 mt-8">
        <a href="{{ route('shop.appointments') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Appointments
        </a>
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

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Customer Info -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Customer</h3>
                    <p class="mt-1 text-lg">{{ $appointment->user->name }}</p>
                    <p class="text-gray-600">{{ $appointment->user->email }}</p>
                    <p class="text-gray-600">{{ $appointment->user->phone }}</p>
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
                </div>
                
                <!-- Employee -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Employee Assigned</h3>
                    <p class="mt-1 text-lg">{{ $appointment->employee ? $appointment->employee->name : 'Not assigned' }}</p>
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
                    <button onclick="acceptAppointment({{ $appointment->id }})" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">
                        Accept
                    </button>
                    <button onclick="cancelAppointment({{ $appointment->id }})" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                        Cancel
                    </button>
                @endif
                
                @if($appointment->status === 'accepted')
                    <button onclick="markAsPaid({{ $appointment->id }})" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 {{ $appointment->payment_status === 'paid' ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $appointment->payment_status === 'paid' ? 'disabled' : '' }}>
                        {{ $appointment->payment_status === 'paid' ? 'Already Paid' : 'Mark as Paid' }}
                    </button>
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
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
                            <textarea id="note" 
                                    x-model="noteText"
                                    rows="4" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    placeholder="Enter your note here..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Add Image (optional)</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" 
                                       id="noteImage" 
                                       accept="image/*"
                                       @change="handleImagePreview($event)"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            </div>
                            <!-- Image Preview -->
                            <div x-show="imagePreview" class="mt-2">
                                <img :src="imagePreview" class="max-h-32 rounded">
                            </div>
                        </div>
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

@push('scripts')
<script>
// Remove the old JavaScript functions since we're now using Alpine.js
</script>
@endpush
@endsection 