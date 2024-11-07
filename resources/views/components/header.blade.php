<!-- Header Component -->
<div class="header p-3 flex justify-between items-center 
     fixed top-0 left-64 w-[calc(100%-254px)] bg-gray-100 header-container"
     style="z-index: 40;">
    <div class="logo">
        <!-- Logo content -->
    </div>
    <div class="user-profile flex items-center">
        <div class="notification-bell relative mr-4">
            <a href="#" id="notification-bell" class="relative">
                <img src="https://img.icons8.com/ios-filled/50/000000/bell.png" 
                     alt="Notification Bell" 
                     class="bell-icon w-6 h-6">
                <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center ">
                    
                </span>
            </a>
            <div id="notification-dropdown" 
                 class="dropdown-content absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg hidden"
                 style="z-index: 45;">
                <div class="notification-item p-4 border-b border-gray-200 hover:bg-gray-50">
                    <p class="text-sm text-gray-800">New appointment request</p>
                    <button onclick="showDetails('appointment')" 
                            class="text-blue-500 text-sm mt-1 hover:text-blue-600">
                        Details
                    </button>
                </div>
                <div class="notification-item p-4 border-b border-gray-200 hover:bg-gray-50">
                    <p class="text-sm text-gray-800">New message from client</p>
                    <button onclick="showDetails('message')" 
                            class="text-blue-500 text-sm mt-1 hover:text-blue-600">
                        Details
                    </button>
                </div>
                <div class="notification-item p-4 hover:bg-gray-50">
                    <p class="text-sm text-gray-800">Reminder: Pet vaccination</p>
                    <button onclick="showDetails('reminder')" 
                            class="text-blue-500 text-sm mt-1 hover:text-blue-600">
                        Details
                    </button>
                </div>
            </div>
        </div>
        <a href="{{ route('shopprofile') }}" class="relative">
            <img src="https://i.pravatar.cc/150?img=1" 
                 alt="User Picture" 
                 class="user-pic w-10 h-10 rounded-full border-2 border-gray-200">
        </a>
    </div>
</div>

<style>
/* Base styles for header */
.header-container {
    transition: opacity 0.3s ease;
}

/* Style for when modal/form is active */
body.modal-active .header-container {
    opacity: 0.1;
    pointer-events: none;
}

/* Notification dropdown refined styles */
.dropdown-content {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(229, 231, 235, 1);
}

.notification-item {
    transition: background-color 0.2s ease;
}

/* Ensure modal/form has higher z-index */
.modal, .popup-form {
    z-index: 50;
}

/* Animation for dropdown */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-content:not(.hidden) {
    animation: fadeIn 0.2s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show notification details
    window.showDetails = function(type) {
        alert('Showing details for: ' + type);
    };

    // Handle notification bell click
    const notificationBell = document.getElementById('notification-bell');
    const notificationDropdown = document.getElementById('notification-dropdown');
    
    notificationBell.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notificationDropdown.contains(event.target) && 
            !notificationBell.contains(event.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });

    // Handle modal/form visibility
    const modalObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.target.classList.contains('modal') || 
                mutation.target.classList.contains('popup-form')) {
                const isVisible = !mutation.target.classList.contains('hidden');
                document.body.classList.toggle('modal-active', isVisible);
            }
        });
    });

    // Observe modal/form elements for visibility changes
    document.querySelectorAll('.modal, .popup-form').forEach(function(element) {
        modalObserver.observe(element, {
            attributes: true,
            attributeFilter: ['class']
        });
    });

    // Handle ESC key to close dropdown
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            notificationDropdown.classList.add('hidden');
        }
    });
});
</script>