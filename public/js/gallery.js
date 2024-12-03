document.addEventListener('DOMContentLoaded', function() {
    // Gallery functionality
    const galleryImages = [
        '/images/gallery/grooming1.jpg',
        '/images/gallery/grooming2.jpg',
        '/images/gallery/grooming3.jpg'
    ];
    let currentImageIndex = 0;

    // Make functions globally available
    window.openGalleryModal = function(index) {
        currentImageIndex = index;
        const modalImage = document.getElementById('modalImage');
        modalImage.src = galleryImages[index];
        document.getElementById('galleryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    window.closeGalleryModal = function() {
        document.getElementById('galleryModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    window.changeImage = function(direction) {
        currentImageIndex = (currentImageIndex + direction + galleryImages.length) % galleryImages.length;
        const modalImage = document.getElementById('modalImage');
        modalImage.src = galleryImages[currentImageIndex];
    }

    // Initialize event listeners
    const galleryModal = document.getElementById('galleryModal');
    if (galleryModal) {
        galleryModal.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('fixed')) {
                closeGalleryModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (galleryModal.classList.contains('hidden')) return;
            
            switch(e.key) {
                case 'ArrowLeft':
                    changeImage(-1);
                    break;
                case 'ArrowRight':
                    changeImage(1);
                    break;
                case 'Escape':
                    closeGalleryModal();
                    break;
            }
        });

        galleryImages.forEach(src => {
            const img = new Image();
            img.src = src;
        });
    }
}); 