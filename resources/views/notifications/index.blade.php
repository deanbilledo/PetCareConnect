@extends(session('shop_mode') ? 'layouts.shop' : 'layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ 
    markAllAsRead() {
        try {
            // Try to use the route, but have a fallback direct URL
            const url = @json(
                (function() {
                    try {
                        return route('notifications.markAllAsRead', [], false);
                    } catch (e) {
                        return '/notifications/mark-all-as-read';
                    }
                })()
            ) || '/notifications/mark-all-as-read';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('border-l-4', 'border-blue-500', 'bg-blue-50');
                    const markAsReadButton = item.querySelector('button');
                    if (markAsReadButton) {
                        markAsReadButton.remove();
                    }
                });
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        } catch (e) {
            console.error('Error with notifications route:', e);
        }
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
                    @if($notifications->where('status', 'unread')->count() > 0)
                        <button @click="markAllAsRead()" 
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-500">
                            Mark all as read
                        </button>
                    @endif
                </div>
            </div>

            <!-- Notifications List -->
            <div class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    <div id="notification-{{ $notification->id }}" 
                         class="notification-item p-4 hover:bg-gray-50 {{ $notification->status === 'unread' ? 'border-l-4 border-blue-500 bg-blue-50' : '' }}">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mt-1">
                                {!! $notification->getIconHtml() !!}
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                                        <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $notification->message }}</p>
                                        <div class="mt-2 flex items-center space-x-4">
                                            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" 
                                                   class="text-xs font-medium text-blue-600 hover:text-blue-500">
                                                    {{ $notification->action_text ?? 'View Appointment' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Mark as Read Button -->
                                    @if($notification->status === 'unread')
                                        <button onclick="markAsRead('{{ $notification->id }}')" 
                                                class="ml-2 text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Mark as read</span>
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-4 text-gray-600">No notifications to display</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead(id) {
    try {
        // Use a direct URL to avoid route() errors
        const url = `/notifications/${id}/mark-as-read`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const notification = document.querySelector(`#notification-${id}`);
            if (notification) {
                notification.classList.remove('border-l-4', 'border-blue-500', 'bg-blue-50');
                const markAsReadButton = notification.querySelector('button');
                if (markAsReadButton) {
                    markAsReadButton.remove();
                }
            }
        })
        .catch(error => {
            console.error(`Error marking notification ${id} as read:`, error);
        });
    } catch (e) {
        console.error('Error with notification route:', e);
    }
}
</script>
@endpush
@endsection 