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