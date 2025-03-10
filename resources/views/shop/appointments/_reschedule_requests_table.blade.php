<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6">
        @if($rescheduleRequests->isEmpty())
            <p class="text-gray-500 text-center py-4">No pending reschedule requests.</p>
        @else
            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">Client</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[18%]">Current Date/Time</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[18%]">Requested Date/Time</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[14%]">Service</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">Reason</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rescheduleRequests as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="{{ asset('images/default-profile.png') }}" 
                                                 alt="{{ $appointment->user->name }}">
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 truncate max-w-[180px]">
                                                {{ $appointment->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 truncate max-w-[180px]">
                                                {{ $appointment->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->appointment_date->format('F j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->requested_date)->format('F j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->requested_date)->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $appointment->requested_service ?: $appointment->service }}
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2">
                                        {{ $appointment->reschedule_reason }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right text-sm font-medium">
                                    <div class="flex gap-2 justify-end">
                                        <button
                                            onclick="approveReschedule({{ $appointment->id }})"
                                            class="text-green-600 hover:text-green-900 bg-green-50 px-2 py-1 rounded-full text-xs font-medium"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            onclick="showDeclineRescheduleModal({{ $appointment->id }})"
                                            class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded-full text-xs font-medium"
                                        >
                                            Decline
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($rescheduleRequests as $appointment)
                    <div class="p-4">
                        <!-- Client Info -->
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full overflow-hidden mr-3">
                                <img class="h-full w-full object-cover" 
                                     src="{{ asset('images/default-profile.png') }}" 
                                     alt="{{ $appointment->user->name }}">
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $appointment->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->user->email }}</div>
                            </div>
                        </div>
                        
                        <!-- Dates Comparison -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-semibold mb-1">Current Date/Time</div>
                                    <div class="text-sm font-medium">
                                        {{ $appointment->appointment_date->format('F j, Y') }}<br>
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-semibold mb-1">Requested Date/Time</div>
                                    <div class="text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($appointment->requested_date)->format('F j, Y') }}<br>
                                        {{ \Carbon\Carbon::parse($appointment->requested_date)->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Service and Reason -->
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Service</div>
                            <div class="text-sm font-medium mb-3">{{ $appointment->requested_service ?: $appointment->service }}</div>
                            
                            <div class="text-sm text-gray-500 mb-1">Reason for Reschedule</div>
                            <div class="text-sm font-medium bg-gray-50 p-3 rounded-lg">{{ $appointment->reschedule_reason }}</div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2 mt-4">
                            <button
                                onclick="approveReschedule({{ $appointment->id }})"
                                class="flex-1 text-center bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm font-medium"
                            >
                                Approve
                            </button>
                            <button
                                onclick="showDeclineRescheduleModal({{ $appointment->id }})"
                                class="flex-1 text-center bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm font-medium"
                            >
                                Decline
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Decline Reschedule Modal -->
<div
    x-data="{ show: false, appointmentId: null }"
    x-show="show"
    x-on:show-decline-reschedule-modal.window="show = true; appointmentId = $event.detail"
    x-on:close-decline-reschedule-modal.window="show = false"
    class="fixed inset-0 z-10 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity"
            aria-hidden="true"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Decline Reschedule Request
                        </h3>
                        <div class="mt-2">
                            <textarea
                                id="rescheduleRejectionReason"
                                rows="3"
                                class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Please provide a reason for declining the reschedule request..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button
                    type="button"
                    x-on:click="declineReschedule(appointmentId)"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Decline
                </button>
                <button
                    type="button"
                    x-on:click="show = false"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDeclineRescheduleModal(appointmentId) {
    window.dispatchEvent(new CustomEvent('show-decline-reschedule-modal', {
        detail: appointmentId
    }));
}

async function approveReschedule(appointmentId) {
    try {
        const response = await fetch(`/appointments/reschedule/${appointmentId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (response.ok) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to approve reschedule request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
    }
}

async function declineReschedule(appointmentId) {
    const reason = document.getElementById('rescheduleRejectionReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for declining the reschedule request');
        return;
    }

    try {
        const response = await fetch(`/appointments/reschedule/${appointmentId}/decline`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason })
        });

        const data = await response.json();

        if (response.ok) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to decline reschedule request');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
    }
}
</script>
@endpush 