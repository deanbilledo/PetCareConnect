@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Shop Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <img 
                    src="{{ Storage::url($shop->image) }}" 
                    alt="{{ $shop->name }}" 
                    class="w-16 h-16 rounded-full object-cover"
                >
                <div>
                    <h1 class="text-2xl font-bold">{{ $shop->name }}</h1>
                    <p class="text-gray-600">{{ ucfirst($shop->type) }} Shop</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    Open
                </span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- Today's Appointments -->
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-blue-800 text-sm font-medium mb-2">Today's Appointments</h3>
                    <p class="text-3xl font-bold text-blue-900">{{ $todayAppointments }}</p>
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
            <div class="bg-green-50 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-green-800 text-sm font-medium mb-2">Total Revenue</h3>
                    <p class="text-3xl font-bold text-green-900">₱{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-green-600 text-sm mt-2">Overall earnings</p>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="bg-yellow-50 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-yellow-800 text-sm font-medium mb-2">Pending Appointments</h3>
                    <p class="text-3xl font-bold text-yellow-900">{{ $pendingAppointments }}</p>
                    <p class="text-yellow-600 text-sm mt-2">Awaiting service</p>
                </div>
            </div>

            <!-- Rating -->
            <div class="bg-purple-50 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <h3 class="text-purple-800 text-sm font-medium mb-2">Shop Rating</h3>
                    <p class="text-3xl font-bold text-purple-900">{{ number_format($shop->rating, 1) }}</p>
                    <p class="text-purple-600 text-sm mt-2">Average customer rating</p>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Recent Appointments</h2>
                <div class="flex gap-2">
                    <select x-data="{ status: 'all' }" 
                            x-model="status" 
                            @change="window.location.href = '{{ route('shop.dashboard') }}?status=' + status"
                            class="border rounded-md px-3 py-1.5 text-sm">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="date" 
                           class="border rounded-md px-3 py-1.5 text-sm"
                           value="{{ request('date') }}"
                           onchange="window.location.href = '{{ route('shop.dashboard') }}?date=' + this.value">
                </div>
            </div>

            <div class="bg-white rounded-lg border overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pet
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $appointment->pet->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->pet->breed }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $appointment->service_type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $appointment->appointment_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </div>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₱{{ number_format($appointment->service_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($appointment->status === 'pending')
                                        <button onclick="showAcceptModal({{ $appointment->id }})" 
                                                class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded-full text-sm font-medium mr-2">
                                            Accept
                                        </button>
                                        <button onclick="showCancelModal({{ $appointment->id }})"
                                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-full text-sm font-medium">
                                            Cancel
                                        </button>
                                    @elseif($appointment->status === 'accepted')
                                        <button onclick="showMarkAsPaidModal({{ $appointment->id }})"
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-full text-sm font-medium">
                                            Mark as Paid
                                        </button>
                                    @elseif($appointment->status === 'completed')
                                        <span class="text-green-600 bg-green-50 px-3 py-1 rounded-full text-sm font-medium">
                                            Completed (Paid)
                                        </span>
                                    @elseif($appointment->status === 'cancelled')
                                        <span class="text-red-600 bg-red-50 px-3 py-1 rounded-full text-sm font-medium">
                                            Cancelled
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No appointments found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t">
                    {{ $appointments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accept Modal -->
<div id="acceptModal" 
     x-data="{ show: false, appointmentId: null }"
     x-show="show"
     @accept-modal.window="show = true; appointmentId = $event.detail.appointmentId"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
     x-cloak>
    <div class="relative mx-auto p-8 border w-[500px] shadow-lg rounded-md bg-white">
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
</script>
@endpush 