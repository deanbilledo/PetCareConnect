/**
 * Loading Screen Utility Functions
 * 
 * Provides easy-to-use functions for showing and hiding loading screens
 * throughout the application.
 */

// Global loading overlay functions
function showGlobalLoading() {
    const overlay = document.getElementById('globalLoadingOverlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Prevent scrolling
    }
}

function hideGlobalLoading() {
    const overlay = document.getElementById('globalLoadingOverlay');
    if (overlay) {
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Show loading with a promise
function withLoading(promise) {
    showGlobalLoading();
    return promise
        .then(result => {
            hideGlobalLoading();
            return result;
        })
        .catch(error => {
            hideGlobalLoading();
            throw error;
        });
}

// Add loading to any link navigation
function initLoadingOnNavigation() {
    document.addEventListener('DOMContentLoaded', () => {
        const links = document.querySelectorAll('a:not([data-no-loading])');
        
        links.forEach(link => {
            // Skip links that are anchors, have target="_blank", or are javascript: links
            if (link.getAttribute('href')?.startsWith('#') || 
                link.getAttribute('target') === '_blank' ||
                link.getAttribute('href')?.startsWith('javascript:')) {
                return;
            }
            
            link.addEventListener('click', function(event) {
                // Skip if modifier keys were pressed
                if (event.ctrlKey || event.metaKey || event.shiftKey) {
                    return;
                }
                
                // Check if the link goes to a different page
                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin && 
                    url.pathname !== window.location.pathname) {
                    showGlobalLoading();
                }
            });
        });
        
        // Also add loading on form submissions
        const forms = document.querySelectorAll('form:not([data-no-loading])');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                showGlobalLoading();
            });
        });
    });
}

// Initialize page transition loading
initLoadingOnNavigation(); 