/**
 * Handle Notification Events for PetCareConnect
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Echo for WebSocket/Pusher if available (optional implementation)
    if (typeof Echo !== 'undefined') {
        // Listen for new notifications on private channel
        Echo.private(`notifications.${userId}`)
            .listen('NotificationReceived', (e) => {
                if (e.notification.type === 'appointment') {
                    window.dispatchEvent(new CustomEvent('appointment-notification-received'));
                }
            });
    }

    // Function to mark a notification as read
    window.markNotificationAsRead = function(id) {
        fetch(`/notifications/${id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Notification marked as read') {
                window.dispatchEvent(new CustomEvent('appointment-notification-read'));
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    };

    // Function to mark all notifications as read
    window.markAllNotificationsAsRead = function() {
        fetch('/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'All notifications marked as read') {
                window.dispatchEvent(new CustomEvent('appointment-notification-read'));
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    };
});

// For testing purposes, you can trigger the notification manually
// Uncomment and use in browser console if needed
// window.triggerAppointmentNotification = function() {
//     window.dispatchEvent(new CustomEvent('appointment-notification-received'));
// }; 