/**
 * Favorite Button Handler
 * Provides instant visual feedback and AJAX functionality for favorite buttons
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add styles for animations
    addFavoriteAnimationStyles();
    
    // Setup event listeners for all favorite buttons
    setupFavoriteButtons();
});

/**
 * Add animation styles to the document head
 */
function addFavoriteAnimationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes favoriteClick {
            0% { transform: scale(1); }
            50% { transform: scale(0.85); }
            100% { transform: scale(1); }
        }
        
        @keyframes favoritePop {
            0% { transform: scale(1); }
            50% { transform: scale(1.35); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
        
        @keyframes favoriteUnpop {
            0% { transform: scale(1); }
            30% { transform: scale(0.8); }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .animate-favorite-click {
            animation: favoriteClick 300ms ease-in-out;
        }
        
        .animate-favorite-pop {
            animation: favoritePop 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .animate-favorite-unpop {
            animation: favoriteUnpop 500ms ease-in-out;
        }
        
        .favorite-btn:hover svg {
            transform: scale(1.1);
        }
    `;
    document.head.appendChild(style);
}

/**
 * Set up event listeners for all favorite buttons on the page
 */
function setupFavoriteButtons() {
    // Find all favorite buttons
    const buttons = document.querySelectorAll('.favorite-btn');
    
    // Add event listeners to each button
    buttons.forEach(button => {
        // Remove any existing onclick attribute and handlers
        const shopId = button.getAttribute('data-shop-id');
        if (shopId) {
            button.removeAttribute('onclick');
            
            // Add new event listener
            button.addEventListener('click', handleFavoriteClick, true);
            
            // Mark that this button has a listener
            button._hasClickListener = true;
        }
    });
    
    // Global delegate for dynamically added buttons
    document.addEventListener('click', function(e) {
        try {
            const favoriteBtn = e.target.closest('.favorite-btn');
            if (favoriteBtn && !favoriteBtn._hasClickListener) {
                e.preventDefault();
                e.stopPropagation();
                
                const shopId = favoriteBtn.getAttribute('data-shop-id');
                if (shopId) {
                    // Add permanent listener for future clicks
                    favoriteBtn.addEventListener('click', handleFavoriteClick, true);
                    favoriteBtn._hasClickListener = true;
                    
                    // Handle this click
                    handleFavoriteButtonAction(favoriteBtn, shopId);
                }
            }
        } catch (error) {
            console.error('Error in global click handler:', error);
        }
    }, true);
}

/**
 * Handle click events on favorite buttons
 * This separates event handling from the actual toggle functionality
 * @param {Event} e - The click event
 */
function handleFavoriteClick(e) {
    try {
        e.preventDefault();
        e.stopPropagation();
        
        const button = this;
        const shopId = button.getAttribute('data-shop-id');
        
        if (shopId) {
            handleFavoriteButtonAction(button, shopId);
        }
        
        return false;
    } catch (error) {
        console.error('Error handling favorite click:', error);
        return false;
    }
}

/**
 * Core function to handle favorite button action
 * Separates the actual functionality from event handling
 * @param {HTMLElement} button - The favorite button element
 * @param {number|string} shopId - The ID of the shop
 */
function handleFavoriteButtonAction(button, shopId) {
    if (!button || !shopId) {
        console.error('Missing button or shopId');
        return;
    }
    
    // Ensure button is a DOM element
    if (!(button instanceof Element)) {
        console.error('Button parameter must be a DOM element');
        return;
    }
    
    // Get current state and SVG element
    const svg = button.querySelector('svg');
    if (!svg) {
        console.error('SVG element not found in button');
        return;
    }
    
    const isFavorited = button.getAttribute('data-is-favorited') === 'true';
    
    // Add click animation
    button.classList.add('animate-favorite-click');
    setTimeout(() => button.classList.remove('animate-favorite-click'), 300);
    
    // Immediately toggle visual state for instant feedback
    if (isFavorited) {
        // Unfavorite immediately
        svg.classList.remove('text-red-500');
        svg.classList.add('text-gray-400', 'animate-favorite-unpop');
        button.classList.remove('bg-red-50');
        button.setAttribute('data-is-favorited', 'false');
    } else {
        // Favorite immediately
        svg.classList.add('text-red-500', 'animate-favorite-pop');
        svg.classList.remove('text-gray-400');
        button.classList.add('bg-red-50');
        button.setAttribute('data-is-favorited', 'true');
    }
    
    // Send AJAX request
    sendFavoriteRequest(button, shopId, isFavorited);
}

/**
 * Send an AJAX request to update favorite status
 * @param {HTMLElement} button - The favorite button element
 * @param {number|string} shopId - The ID of the shop
 * @param {boolean} previousState - The previous favorite state
 */
function sendFavoriteRequest(button, shopId, previousState) {
    // Get CSRF token
    const tokenElement = document.querySelector('meta[name="csrf-token"]');
    if (!tokenElement) {
        console.error('CSRF token not found. Make sure you have the meta tag in your layout.');
        showNotification('Authentication error', 'error');
        revertFavoriteState(button, previousState);
        return;
    }
    
    const token = tokenElement.getAttribute('content');
    
    // Create and configure XHR request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/favorites/${shopId}`, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Cache-Control', 'no-cache, no-store');
    
    // Set up response handler
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            const svg = button.querySelector('svg');
            if (!svg) return;
            
            if (xhr.status === 200 || xhr.status === 201) {
                try {
                    // First check if the response is actually JSON
                    const contentType = xhr.getResponseHeader('Content-Type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server did not return JSON. Received: ' + contentType);
                    }
                    
                    // Empty response check
                    if (!xhr.responseText.trim()) {
                        throw new Error('Empty response from server');
                    }
                    
                    // Parse server response
                    const response = JSON.parse(xhr.responseText);
                    console.log('Server response:', response);
                    
                    // If server response doesn't match the current UI state, update the UI
                    if (response.isFavorited !== (button.getAttribute('data-is-favorited') === 'true')) {
                        console.log('Correcting UI state based on server response');
                        if (response.isFavorited) {
                            svg.classList.add('text-red-500');
                            svg.classList.remove('text-gray-400');
                            button.classList.add('bg-red-50');
                            button.setAttribute('data-is-favorited', 'true');
                        } else {
                            svg.classList.remove('text-red-500');
                            svg.classList.add('text-gray-400');
                            button.classList.remove('bg-red-50');
                            button.setAttribute('data-is-favorited', 'false');
                        }
                    }
                    
                    // Remove animation classes
                    setTimeout(() => {
                        svg.classList.remove('animate-favorite-pop', 'animate-favorite-unpop');
                    }, 500);
                    
                } catch (error) {
                    console.error('Error parsing response:', error);
                    console.log('Response text (first 100 chars):', xhr.responseText.substring(0, 100));
                    
                    // If the response starts with <!DOCTYPE or <html, it's HTML instead of JSON
                    if (xhr.responseText.trim().startsWith('<!DOCTYPE') || xhr.responseText.trim().startsWith('<html')) {
                        console.error('Received HTML instead of JSON. You might be redirected to a login page or experiencing a server error.');
                        showNotification('Session expired. Please refresh the page and try again.', 'error');
                    }
                    
                    revertFavoriteState(button, previousState);
                }
            } else {
                console.error('Server error:', xhr.status, xhr.statusText);
                // Check if we were redirected to login
                if (xhr.responseURL && xhr.responseURL.includes('/login')) {
                    showNotification('Please log in to favorite shops', 'error');
                } else {
                    showNotification('Failed to update favorite status', 'error');
                }
                revertFavoriteState(button, previousState);
            }
        }
    };
    
    // Handle network errors
    xhr.onerror = function() {
        console.error('Network error occurred');
        revertFavoriteState(button, previousState);
        showNotification('Network error. Please try again.', 'error');
    };
    
    // Send the request with proper CSRF token format for Laravel
    xhr.send('_token=' + encodeURIComponent(token));
}

/**
 * Revert the favorite button to its original state if the request fails
 * @param {HTMLElement} button - The favorite button element
 * @param {boolean} originalState - The original favorite state
 */
function revertFavoriteState(button, originalState) {
    const svg = button.querySelector('svg');
    if (!svg) return;
    
    if (originalState) {
        // Revert to favorited state
        svg.classList.add('text-red-500');
        svg.classList.remove('text-gray-400');
        button.classList.add('bg-red-50');
        button.setAttribute('data-is-favorited', 'true');
    } else {
        // Revert to unfavorited state
        svg.classList.remove('text-red-500');
        svg.classList.add('text-gray-400');
        button.classList.remove('bg-red-50');
        button.setAttribute('data-is-favorited', 'false');
    }
    
    // Remove animation classes
    svg.classList.remove('animate-favorite-pop', 'animate-favorite-unpop');
}

/**
 * Show a notification to the user
 * @param {string} message - The notification message
 * @param {string} type - The notification type (success/error)
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded shadow-lg ${
        type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// For backward compatibility with any code that might be calling toggleFavorite directly
function toggleFavorite(buttonOrEvent, shopIdOrButton) {
    // Skip event handling entirely and just call the core action function
    let button, shopId;
    
    // Determine if first param is a button
    if (buttonOrEvent instanceof Element) {
        button = buttonOrEvent;
        shopId = shopIdOrButton;
    } else {
        // For any other case, just try to use second param as button
        button = shopIdOrButton;
        // Try to get shopId from button attribute
        if (button instanceof Element) {
            shopId = button.getAttribute('data-shop-id');
        }
    }
    
    if (button && shopId) {
        handleFavoriteButtonAction(button, shopId);
    } else {
        console.error('Could not determine button and shopId for toggleFavorite call');
    }
    
    return false;
} 