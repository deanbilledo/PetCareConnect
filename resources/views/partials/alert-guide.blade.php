{{-- 
    Alert Guide - How to Use Toast Notifications and Modal Popups
    This file serves as a reference for developers on how to use the new toast and modal components
--}}

{{-- 
    TOAST NOTIFICATIONS
    ------------------
    Toast notifications are displayed in the top-right corner of the screen automatically 
    for session flash messages ('success', 'error', 'info', 'warning').
    
    You can also trigger them manually from JavaScript using the window.dispatchEvent method.
--}}

{{-- Example of manually triggering a toast from a button click --}}
<button onclick="showSuccessToast('Operation completed successfully!')" 
        class="px-4 py-2 bg-green-600 text-white rounded-md">
    Show Success Toast
</button>

<button onclick="showErrorToast('Something went wrong!')" 
        class="px-4 py-2 bg-red-600 text-white rounded-md">
    Show Error Toast
</button>

<button onclick="showInfoToast('Here is some information')" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md">
    Show Info Toast
</button>

<button onclick="showWarningToast('Be careful!')" 
        class="px-4 py-2 bg-yellow-600 text-white rounded-md">
    Show Warning Toast
</button>

{{-- 
    MODAL POPUPS 
    -----------
    Modal popups can be triggered using the window.dispatchEvent method.
    You can pass a title, content, and an optional onConfirm callback function.
--}}

{{-- Example of triggering a modal from a button click --}}
<button onclick="showConfirmModal('Confirm Action', 'Are you sure you want to proceed with this action?', function() { console.log('Action confirmed!'); })" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md">
    Show Confirm Modal
</button>

<button onclick="showInfoModal('Information', 'This is some important information you should know.')" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md">
    Show Info Modal
</button>

{{-- JavaScript to handle the toast and modal functions --}}
<script>
    // Toast notification helper functions
    function showSuccessToast(message, timeout = 5000) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: {
                type: 'success',
                message: message,
                timeout: timeout
            }
        }));
    }
    
    function showErrorToast(message, timeout = 5000) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: {
                type: 'error',
                message: message,
                timeout: timeout
            }
        }));
    }
    
    function showInfoToast(message, timeout = 5000) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: {
                type: 'info',
                message: message,
                timeout: timeout
            }
        }));
    }
    
    function showWarningToast(message, timeout = 5000) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: {
                type: 'warning',
                message: message,
                timeout: timeout
            }
        }));
    }
    
    // Modal popup helper functions
    function showConfirmModal(title, content, onConfirm) {
        window.dispatchEvent(new CustomEvent('modal', {
            detail: {
                title: title,
                content: content,
                onConfirm: onConfirm
            }
        }));
    }
    
    function showInfoModal(title, content) {
        window.dispatchEvent(new CustomEvent('modal', {
            detail: {
                title: title,
                content: content
            }
        }));
    }
    
    // Advanced usage: Modal with HTML content
    function showHtmlModal(title, htmlContent, onConfirm) {
        window.dispatchEvent(new CustomEvent('modal', {
            detail: {
                title: title,
                content: htmlContent,
                onConfirm: onConfirm
            }
        }));
    }
</script> 