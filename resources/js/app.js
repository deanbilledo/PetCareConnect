import './bootstrap';
import '../css/app.css';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Mobile menu functionality
document.addEventListener('alpine:init', () => {
    Alpine.data('mobileMenu', () => ({
        isOpen: false,
        toggleMenu() {
            this.isOpen = !this.isOpen;
        },
        closeMenu() {
            this.isOpen = false;
        }
    }));
});

// Wait for jQuery to be loaded
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }
});
