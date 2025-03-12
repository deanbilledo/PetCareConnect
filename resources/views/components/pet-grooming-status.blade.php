@props(['pet'])

<div 
    id="pet-grooming-status-{{ $pet->id }}"
    x-data="petGroomingStatus"
    class="bg-white rounded-lg shadow-md p-4 mb-4"
>
    <div class="flex justify-between items-start mb-3">
        <h3 class="text-lg font-medium text-gray-900">Grooming Status</h3>
        <button 
            @click="isSettingsOpen = !isSettingsOpen"
            class="text-gray-400 hover:text-gray-600"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>
    
    <!-- Loading State -->
    <div x-show="isLoading" class="flex justify-center items-center py-8">
        <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    
    <!-- Status Information -->
    <div x-show="!isLoading && groomingStatus" x-cloak>
        <div x-show="groomingStatus.needs_grooming" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-md">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-800" x-text="groomingStatus.message"></p>
                    <p class="text-xs text-amber-700 mt-1">Regular grooming helps maintain your pet's health and comfort.</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="/book?pet={{ $pet->id }}&type=grooming" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors">
                    Book Grooming Appointment
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        
        <div x-show="!groomingStatus.needs_grooming" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-800" x-text="groomingStatus.message"></p>
                    <p class="text-xs text-green-700 mt-1">
                        <template x-if="groomingStatus.next_recommended_grooming">
                            <span>Next grooming recommended by <span x-text="formatDate(groomingStatus.next_recommended_grooming)"></span></span>
                        </template>
                        <template x-if="!groomingStatus.next_recommended_grooming">
                            <span>Keep up the good work maintaining your pet's grooming schedule!</span>
                        </template>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="text-sm text-gray-600">
            <div class="flex items-center mb-2">
                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Recommended grooming every <span class="font-medium" x-text="daysText"></span></span>
            </div>
            
            <template x-if="groomingStatus.last_grooming_date">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                    </svg>
                    <span>Last grooming: <span class="font-medium" x-text="formatDate(groomingStatus.last_grooming_date)"></span></span>
                </div>
            </template>
        </div>
    </div>
    
    <!-- Grooming Preferences -->
    <div x-show="isSettingsOpen" x-cloak class="mt-4 border-t border-gray-200 pt-4">
        <h4 class="text-sm font-semibold text-gray-900 mb-3">Grooming Reminder Preferences</h4>
        <div class="mb-4">
            <label for="groomingInterval-{{ $pet->id }}" class="block text-sm text-gray-700 mb-1">Remind me every:</label>
            <div class="flex items-center">
                <input 
                    type="range" 
                    id="groomingInterval-{{ $pet->id }}" 
                    x-model="groomingInterval" 
                    min="7" 
                    max="180" 
                    step="1" 
                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <span class="ml-3 w-16 text-sm font-medium text-gray-700" x-text="daysText"></span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>Weekly</span>
                <span>Monthly</span>
                <span>6 Months</span>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button 
                @click="isSettingsOpen = false" 
                class="px-3 py-1.5 text-xs border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
            >
                Cancel
            </button>
            <button 
                @click="updateGroomingPreference()" 
                class="px-3 py-1.5 text-xs border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none flex items-center justify-center min-w-[80px]"
                :disabled="isLoading"
            >
                <template x-if="isLoading">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <template x-if="!isLoading">
                    <span>Save</span>
                </template>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('petGroomingStatus', () => ({
        isLoading: false,
        groomingStatus: null,
        groomingInterval: {{ $pet->grooming_interval ?? 30 }},
        isSettingsOpen: false,
        daysText: '{{ $pet->grooming_interval ?? 30 }} days',
        
        init() {
            this.checkGroomingStatus();
            this.$watch('groomingInterval', (value) => {
                if (value == 1) {
                    this.daysText = '1 day';
                } else {
                    this.daysText = value + ' days';
                }
            });
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },
        
        updateGroomingPreference() {
            this.isLoading = true;
            
            fetch('{{ route('profile.pets.grooming-preference', $pet) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    grooming_interval: this.groomingInterval
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'success',
                            message: data.message
                        }
                    }));
                    
                    this.isSettingsOpen = false;
                    this.checkGroomingStatus();
                } else {
                    throw new Error(data.message || 'Failed to update grooming preference');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: error.message || 'An error occurred while updating grooming preference'
                    }
                }));
            })
            .finally(() => {
                this.isLoading = false;
            });
        },
        
        checkGroomingStatus() {
            this.isLoading = true;
            
            fetch('{{ route('profile.pets.grooming-status', $pet) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.groomingStatus = data;
                    } else {
                        throw new Error(data.message || 'Failed to fetch grooming status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: error.message || 'An error occurred while checking grooming status'
                        }
                    }));
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }));
});
</script> 