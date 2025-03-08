@forelse($appointments as $date => $dayAppointments)
    <div x-show="dateFilter === '' || dateFilter === '{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}'"
         class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h2>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[20%]">Customer</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[8%]">Time</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[20%]">Employee</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[15%]">Pet</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[12%]">Service</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[8%]">Price</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[7%]">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[10%]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($dayAppointments as $appointment)
                        <!-- Debug info -->
                        <div x-data="{
                            debug: function() {
                                console.log('Service Type:', '{{ $appointment->service_type }}');
                                console.log('Filter Value:', this.serviceTypeFilter);
                                console.log('Visible:', this.isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}', '{{ $appointment->shop->type }}'));
                            }
                        }" x-init="debug()">
                        </div>
                        <tr x-show="isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}', '{{ $appointment->shop->type }}')"
                            class="hover:bg-gray-50 {{ is_null($appointment->viewed_at) && $appointment->status === 'pending' ? 'bg-blue-50' : '' }}"
                            onclick="viewAppointment({{ $appointment->id }})"
                            style="cursor: pointer;">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ $appointment->user->profile_photo_path ? asset('storage/' . $appointment->user->profile_photo_path) : asset('images/default-profile.png') }}"
                                             alt="{{ $appointment->user->name }}"
                                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-[180px]">
                                            {{ $appointment->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-[180px]">
                                            {{ $appointment->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500">
                                {{ $appointment->appointment_date->format('g:i A') }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex items-center">
                                    @if($appointment->employee)
                                    <div class="flex-shrink-0 h-9 w-9">
                                        <img class="h-9 w-9 rounded-full object-cover"
                                             src="{{ $appointment->employee->profile_photo ? asset('storage/' . $appointment->employee->profile_photo) : $appointment->employee->getProfilePhotoUrlAttribute() }}"
                                             alt="{{ $appointment->employee->name }}"
                                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-[160px]">
                                            {{ $appointment->employee->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-[160px]">
                                            {{ $appointment->employee->position }}
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-sm text-gray-500">Not assigned</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">{{ $appointment->pet->name }}</span>
                                    <button onclick="event.stopPropagation(); window.location.href='{{ route('profile.pets.health-record', $appointment->pet) }}'"
                                            class="text-xs text-blue-600 hover:text-blue-800 mt-1 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Health Record
                                    </button>
                                </div>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-900">
                                {{ $appointment->service_type }}
                            </td>
                            <td class="px-3 py-4 text-sm font-medium text-gray-900">
                                ₱{{ number_format($appointment->service_price, 2) }}
                            </td>
                            <td class="px-3 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex gap-2" onclick="event.stopPropagation()">
                                    @if($appointment->status === 'pending')
                                        <button onclick="acceptAppointment({{ $appointment->id }})"
                                                class="text-green-600 hover:text-green-800 bg-green-50 px-2 py-1 rounded-full text-xs font-medium">
                                            Accept
                                        </button>
                                        <button onclick="window.cancelAppointment({{ $appointment->id }})"
                                                type="button"
                                                class="text-red-600 hover:text-red-800 bg-red-50 px-2 py-1 rounded-full text-xs font-medium">
                                            Cancel
                                        </button>
                                    @elseif($appointment->status === 'accepted')
                                        <button onclick="markAsPaid({{ $appointment->id }})"
                                                class="w-full text-center bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm font-medium">
                                            Mark as Paid
                                        </button>
                                    @elseif($appointment->status === 'completed')
                                        <div class="flex w-full gap-2">
                                            @if($appointment->payment_status === 'paid')
                                                <a href="{{ route('appointments.download-receipt', $appointment) }}"
                                                   class="flex-1 text-center bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm font-medium">
                                                    Receipt
                                                </a>
                                            @endif
                                            <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                                    class="flex-1 text-center bg-indigo-100 text-indigo-800 px-3 py-2 rounded-lg text-sm font-medium">
                                                Note
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
        
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($dayAppointments as $appointment)
                <div x-show="isAppointmentVisible('{{ $appointment->status }}', '{{ $date }}', '{{ $appointment->shop->type }}')"
                     class="p-4 {{ is_null($appointment->viewed_at) && $appointment->status === 'pending' ? 'bg-blue-50' : '' }}"
                     onclick="viewAppointment({{ $appointment->id }})">
                    
                    <!-- Customer and Status -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full overflow-hidden mr-3">
                                <img class="h-full w-full object-cover"
                                     src="{{ $appointment->user->profile_photo_path ? asset('storage/' . $appointment->user->profile_photo_path) : asset('images/default-profile.png') }}"
                                     alt="{{ $appointment->user->name }}"
                                     onerror="this.src='{{ asset('images/default-profile.png') }}'">
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $appointment->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->user->email }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($appointment->status === 'completed') bg-green-100 text-green-800
                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                    
                    <!-- Appointment Details -->
                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                        <div>
                            <div class="text-gray-500">Time</div>
                            <div class="font-medium">{{ $appointment->appointment_date->format('g:i A') }}</div>
                        </div>
                        
                        <div>
                            <div class="text-gray-500">Pet</div>
                            <div class="font-medium">{{ $appointment->pet->name }}</div>
                        </div>
                        
                        <div>
                            <div class="text-gray-500">Service</div>
                            <div class="font-medium">{{ $appointment->service_type }}</div>
                        </div>
                        
                        <div>
                            <div class="text-gray-500">Price</div>
                            <div class="font-medium">₱{{ number_format($appointment->service_price, 2) }}</div>
                        </div>
                        
                        <div class="col-span-2">
                            <div class="text-gray-500">Employee</div>
                            <div class="font-medium mt-1">
                                @if($appointment->employee)
                                    <div class="flex items-center">
                                        <img class="h-6 w-6 rounded-full object-cover mr-2"
                                             src="{{ $appointment->employee->profile_photo ? asset('storage/' . $appointment->employee->profile_photo) : $appointment->employee->getProfilePhotoUrlAttribute() }}"
                                             alt="{{ $appointment->employee->name }}"
                                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                        <span>{{ $appointment->employee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500">Not assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-wrap gap-2 mt-4" onclick="event.stopPropagation()">
                        @if($appointment->status === 'pending')
                            <button onclick="acceptAppointment({{ $appointment->id }})"
                                    class="flex-1 text-center bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm font-medium">
                                Accept
                            </button>
                            <button onclick="window.cancelAppointment({{ $appointment->id }})"
                                    type="button"
                                    class="flex-1 text-center bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm font-medium">
                                Cancel
                            </button>
                        @elseif($appointment->status === 'accepted')
                            <button onclick="markAsPaid({{ $appointment->id }})"
                                    class="w-full text-center bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm font-medium">
                                Mark as Paid
                            </button>
                        @elseif($appointment->status === 'completed')
                            <div class="flex w-full gap-2">
                                @if($appointment->payment_status === 'paid')
                                    <a href="{{ route('appointments.download-receipt', $appointment) }}"
                                        class="flex-1 text-center bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm font-medium">
                                        Receipt
                                    </a>
                                @endif
                                <button @click="openNoteModal({{ $appointment->id }}, '{{ $appointment->shop->type }}')"
                                        class="flex-1 text-center bg-indigo-100 text-indigo-800 px-3 py-2 rounded-lg text-sm font-medium">
                                    {{ $appointment->shop->type === 'grooming' ? "Groomer's Note" : "Doctor's Note" }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <p class="text-gray-600">No appointments found</p>
    </div>
@endforelse

<script>
// Function to mark an appointment as viewed and then navigate to the appointment details
function viewAppointment(appointmentId) {
    // Mark the appointment as viewed using AJAX
    fetch(`/shop/appointments/${appointmentId}/mark-viewed`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        console.log('Appointment marked as viewed:', data);
        // Navigate to the appointment details page
        window.location.href = `{{ route('shop.appointments.show', '') }}/${appointmentId}`;
    })
    .catch(error => {
        console.error('Error marking appointment as viewed:', error);
        // Still navigate to the appointment details page even if the marking failed
        window.location.href = `{{ route('shop.appointments.show', '') }}/${appointmentId}`;
    });
}
</script> 