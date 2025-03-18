@props(['type' => 'shop', 'id'])

<div class="report-form bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
    <h3 class="text-lg font-semibold mb-4">
        {{ $type === 'shop' ? 'Report Shop' : 'Report User' }}
    </h3>
    
    <form id="{{ $type }}-report-form" 
          class="space-y-4" 
          enctype="multipart/form-data"
          action="{{ $type === 'shop' ? route('shop.report.store') : route('user.report.store') }}" 
          method="POST">
        @csrf
        
        <input type="hidden" name="{{ $type === 'shop' ? 'shop_id' : 'user_id' }}" value="{{ $id }}">
        
        <div>
            <label for="report_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Report Type <span class="text-red-500">*</span>
            </label>
            <select id="report_type" 
                    name="report_type" 
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                    required>
                <option value="">Select a reason</option>
                
                @if($type === 'shop')
                    <option value="inappropriate_content">Inappropriate Content</option>
                    <option value="false_advertising">False Advertising</option>
                    <option value="poor_service">Poor Service Quality</option>
                    <option value="safety_concerns">Safety Concerns</option>
                    <option value="pricing_issues">Pricing Issues</option>
                    <option value="other">Other</option>
                @else
                    <option value="harassment">Harassment</option>
                    <option value="inappropriate_behavior">Inappropriate Behavior</option>
                    <option value="fake_account">Fake Account</option>
                    <option value="scam">Scam Attempt</option>
                    <option value="other">Other</option>
                @endif
            </select>
            <div class="report-type-error text-red-500 text-sm mt-1 hidden">Please select a report type</div>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Description <span class="text-red-500">*</span>
            </label>
            <textarea id="description" 
                      name="description" 
                      rows="4" 
                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                      placeholder="Please provide details about your report..." 
                      required></textarea>
            <div class="description-error text-red-500 text-sm mt-1 hidden">Please provide a detailed description (minimum 10 characters)</div>
        </div>
        
        <!-- Image Upload -->
        <div>
            <label for="evidence_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Upload Evidence (Optional)
            </label>
            <div class="flex items-center justify-center w-full">
                <label for="evidence_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 hover:bg-gray-100">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or GIF (MAX. 2MB)</p>
                    </div>
                    <input id="evidence_image" name="evidence_image" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg, image/gif" />
                </label>
            </div>
            <div id="image-preview-container" class="mt-3 hidden">
                <div class="relative">
                    <img id="image-preview" src="#" alt="Evidence Preview" class="max-h-40 rounded-lg shadow-sm" />
                    <button type="button" id="remove-image" class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="image-error text-red-500 text-sm mt-1 hidden">Please upload a valid image file (max 2MB)</div>
        </div>

        <!-- Form feedback messages -->
        <div id="form-feedback" class="hidden mt-2 p-3 rounded-md"></div>
        
        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition duration-150 ease-in-out">
                Submit Report
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('{{ $type }}-report-form');
    const fileInput = document.getElementById('evidence_image');
    const imagePreview = document.getElementById('image-preview');
    const previewContainer = document.getElementById('image-preview-container');
    const removeButton = document.getElementById('remove-image');
    const formFeedback = document.getElementById('form-feedback');
    
    // Function to show feedback message
    function showFeedback(message, type = 'error') {
        formFeedback.innerHTML = message;
        formFeedback.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
        
        if (type === 'success') {
            formFeedback.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
        } else {
            formFeedback.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        }
    }
    
    // Handle file input change
    fileInput.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            // Validate file type and size
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!validTypes.includes(file.type)) {
                document.querySelector('.image-error').textContent = 'Please upload a valid image file (JPG, PNG, or GIF)';
                document.querySelector('.image-error').classList.remove('hidden');
                fileInput.value = '';
                return;
            }
            
            if (file.size > maxSize) {
                document.querySelector('.image-error').textContent = 'Image size should be less than 2MB';
                document.querySelector('.image-error').classList.remove('hidden');
                fileInput.value = '';
                return;
            }
            
            // Show preview
            document.querySelector('.image-error').classList.add('hidden');
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                } catch (error) {
                    console.error('Error previewing image:', error);
                    document.querySelector('.image-error').textContent = 'Error previewing image';
                    document.querySelector('.image-error').classList.remove('hidden');
                    fileInput.value = '';
                }
            };
            
            reader.onerror = function() {
                console.error('Error reading file');
                document.querySelector('.image-error').textContent = 'Error reading file';
                document.querySelector('.image-error').classList.remove('hidden');
                fileInput.value = '';
            };
            
            try {
                reader.readAsDataURL(file);
            } catch (error) {
                console.error('Error processing file:', error);
                document.querySelector('.image-error').textContent = 'Error processing file';
                document.querySelector('.image-error').classList.remove('hidden');
                fileInput.value = '';
            }
        } else {
            previewContainer.classList.add('hidden');
        }
    });
    
    // Handle remove button click
    removeButton.addEventListener('click', function() {
        fileInput.value = '';
        previewContainer.classList.add('hidden');
    });
    
    // Validate form on submit
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        let isValid = true;
        
        // Reset form feedback
        formFeedback.classList.add('hidden');
        
        // Validate report type
        const reportType = document.getElementById('report_type');
        if (reportType.value === '') {
            document.querySelector('.report-type-error').classList.remove('hidden');
            isValid = false;
        } else {
            document.querySelector('.report-type-error').classList.add('hidden');
        }
        
        // Validate description
        const description = document.getElementById('description');
        if (description.value.trim().length < 10) {
            document.querySelector('.description-error').classList.remove('hidden');
            isValid = false;
        } else {
            document.querySelector('.description-error').classList.add('hidden');
        }
        
        if (!isValid) {
            return false;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        // Get the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Create FormData object to include file
        const formData = new FormData(form);
        
        // Debugging - log formData entries
        console.log('Submitting report with data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? 'File: ' + pair[1].name : pair[1]));
        }
        
        // Send form data via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Report submission response status:', response.status);
            
            // First check if the response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    // Check for authentication error (401)
                    if (response.status === 401) {
                        if (data.redirect) {
                            showFeedback('You must be logged in to submit a report. Redirecting to login page...', 'error');
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000);
                        } else {
                            showFeedback('You must be logged in to submit a report.', 'error');
                        }
                        throw new Error('Authentication required');
                    }
                    
                    if (!response.ok) {
                        // Extract error message from JSON response
                        console.error('Error response:', data);
                        throw new Error(data.message || data.error || 'Error submitting report');
                    }
                    return data;
                });
            } else {
                // Handle non-JSON response (likely an error page)
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return { success: true, message: 'Report submitted successfully' };
            }
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showFeedback('Report submitted successfully! Thank you for your feedback.', 'success');
                
                // Reset form
                form.reset();
                
                // Remove image preview if exists
                if (!previewContainer.classList.contains('hidden')) {
                    previewContainer.classList.add('hidden');
                }
                
                // Close modal after delay
                setTimeout(() => {
                    const modalId = '{{ $type }}' === 'shop' ? 'reportModal' : 'reportUserModal';
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('hidden');
                    }
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to submit report');
            }
        })
        .catch(error => {
            console.error('Error submitting report:', error);
            
            // Show error message
            showFeedback('Error: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        });
    });
});
</script> 