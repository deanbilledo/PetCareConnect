@if($cancellationRequests->isEmpty())
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <p class="text-gray-600">No cancellation requests found</p>
    </div>
@else
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Original Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cancellationRequests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ $request->user->profile_photo_path ? asset('storage/' . $request->user->profile_photo_path) : asset('images/default-profile.png') }}"
                                             alt="{{ $request->user->name }}"
                                             onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $request->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $request->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->appointment_date->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->service_type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->pet->name ?? 'Unknown Pet' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <p class="max-w-xs truncate">{{ $request->cancellation_reason }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php 
                                    $isLastMinute = $request->cancellation_requested_at && 
                                        $request->appointment_date->diffInHours($request->cancellation_requested_at) <= 24;
                                @endphp
                                
                                @if($isLastMinute)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Last Minute
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Standard
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <button onclick="approveCancellation({{ $request->id }})"
                                            class="text-green-600 hover:text-green-800">
                                        Approve
                                    </button>
                                    <button onclick="declineCancellation({{ $request->id }})"
                                            class="text-red-600 hover:text-red-800">
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