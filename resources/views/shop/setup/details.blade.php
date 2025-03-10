@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12 mt-8">
    <div class="max-w-3xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Shop Details & Gallery</h1>
                <p class="mt-2 text-sm text-gray-600">Tell us more about your shop and add some photos to showcase your work.</p>
            </div>

            <!-- Form -->
            <form method="POST" 
                  action="{{ route('shop.setup.details.store') }}" 
                  enctype="multipart/form-data"
                  class="px-8 py-6 space-y-6">
                @csrf

                <!-- Shop Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Shop Description</label>
                    <div class="mt-1">
                        <textarea id="description" 
                                 name="description" 
                                 rows="4" 
                                 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                 placeholder="Tell customers about your shop, your experience, and what makes your services special...">{{ old('description', auth()->user()->shop->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Email -->
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <div class="mt-1">
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="{{ old('contact_email', auth()->user()->shop->contact_email) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_email') border-red-500 @enderror">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <div class="mt-1">
                        <input type="text" 
                               id="contact_number" 
                               name="contact_number" 
                               value="{{ old('contact_number', auth()->user()->shop->contact_number) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_number') border-red-500 @enderror"
                               placeholder="Enter your contact number">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Photo Gallery -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Photo Gallery</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="gallery-preview">
                        <!-- Upload Button -->
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-colors cursor-pointer">
                            <input type="file" 
                                   name="gallery[]" 
                                   multiple 
                                   accept="image/*" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   onchange="handleGalleryUpload(event)">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-gray-700">Add Photos</span>
                            </div>
                        </div>
                    </div>
                    @error('gallery')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">Upload up to 6 photos showcasing your work. Supported formats: JPG, PNG. Max size: 5MB each.</p>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('shop.setup.welcome') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Next
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleGalleryUpload(event) {
    const files = event.target.files;
    const galleryPreview = document.getElementById('gallery-preview');
    const uploadButton = galleryPreview.firstElementChild;
    
    // Remove existing previews
    Array.from(galleryPreview.children).forEach(child => {
        if (child !== uploadButton) {
            child.remove();
        }
    });
    
    // Add new previews
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative aspect-w-16 aspect-h-9';
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Gallery preview" 
                         class="object-cover rounded-lg">
                    <button type="button"
                            onclick="this.parentElement.remove()"
                            class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                galleryPreview.insertBefore(preview, uploadButton.nextSibling);
            };
            reader.readAsDataURL(file);
        }
    });
}
</script>
@endsection 