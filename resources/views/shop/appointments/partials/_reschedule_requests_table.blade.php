@if($rescheduleRequests->isEmpty())
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <p class="text-gray-600">No reschedule requests found</p>
    </div>
@else
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Debug Info -->
        @if(config('app.debug'))
            <div class="bg-gray-100 p-4 text-sm">
                <p>Debug Info:</p>
                <p>Number of requests: {{ $rescheduleRequests->count() }}</p>
                <p>Request statuses: {{ $rescheduleRequests->pluck('status')->implode(', ') }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Original Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rescheduleRequests as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ $appointment->user->profile_photo_url ?? asset('images/default-profile.png') }}"
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $appointment->appointment_date->format('M j, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $appointment->appointment_date->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($appointment->requested_date)->format('M j, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($appointment->requested_date)->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $appointment->requested_service ?: $appointment->service_type }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $appointment->pet->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $appointment->reschedule_reason }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-3">
                                    <button
                                        onclick="approveReschedule({{ $appointment->id }})"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        onclick="showDeclineRescheduleModal({{ $appointment->id }})"
                                        class="text-red-600 hover:text-red-900"
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
    </div>
@endif

<!-- Decline Modal -->
<div x-data="{ show: false, appointmentId: null }"
     x-show="show"
     x-on:show-decline-reschedule-modal.window="show = true; appointmentId = $event.detail"
     class="fixed inset-0 z-10 overflow-y-auto"
     style="display: none;">
    <!-- Modal content -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Decline Reschedule Request</h3>
                <textarea
                    id="rescheduleRejectionReason"
                    rows="3"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="Please provide a reason for declining..."
                ></textarea>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button
                    type="button"
                    @click="declineReschedule(appointmentId)"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Decline
                </button>
                <button
                    type="button"
                    @click="show = false"
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
    if (!confirm('Are you sure you want to approve this reschedule request?')) return;
    
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