@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14">
        <div class="bg-white shadow-xl rounded-lg">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Add Your Shop Employees</h1>
                <p class="mt-2 text-sm text-gray-600">Add your team members to manage your shop efficiently.</p>
            </div>

            <div class="p-8" x-data="employeeSetupManager()">
                <!-- Employee Form -->
                <div class="mb-6">
                    <button @click="openAddModal()" type="button"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Add New Employee
                    </button>
                </div>

                <!-- Employee List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($employees as $employee)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border" data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->name }}">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <img src="{{ $employee->profile_photo_url }}" 
                                     alt="{{ $employee->name }}" 
                                     class="w-16 h-16 rounded-full object-cover">
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $employee->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $employee->position }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p>📧 {{ $employee->email }}</p>
                                <p>📱 {{ $employee->phone }}</p>
                                <p>🕒 {{ $employee->employment_type === 'full-time' ? 'Full Time' : 'Part Time' }}</p>
                                @if($employee->bio)
                                    <p class="text-gray-600 mt-2">{{ $employee->bio }}</p>
                                @endif
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button @click="editEmployee({{ $employee->id }})" 
                                        class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </button>
                                <button @click="confirmDelete({{ $employee->id }}); $dispatch('open-modal', 'delete-employee-modal')" 
                                        class="text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-3 text-center py-8">
                        <p class="text-gray-500">No employees found. Add your first employee to get started!</p>
                    </div>
                    @endforelse
                </div>

                <!-- Delete Confirmation Modal -->
                <x-modal id="delete-employee-modal" type="danger" title="Confirm Employee Removal" :show-close-button="false" :close-on-click-outside="false">
                    <div class="text-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 rounded-full p-2 mr-3">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <p class="text-lg font-medium">Are you sure you want to remove this employee?</p>
                        </div>
                        
                        <p class="mb-4" x-text="employeeToDelete && employeeToDelete.name ? 'You are about to remove ' + employeeToDelete.name + ' from your shop. This action cannot be undone.' : 'You are about to remove this employee from your shop. This action cannot be undone.'"></p>
                        
                        <div x-show="isDeleting" class="flex items-center justify-center my-4 text-sm text-blue-600">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Removing employee...</span>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="flex justify-end space-x-3">
                            <button 
                                @click="$dispatch('close-modal', 'delete-employee-modal')" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :disabled="isDeleting"
                            >
                                Cancel
                            </button>
                            <button 
                                @click="deleteEmployee()"
                                class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isDeleting"
                            >
                                Remove Employee
                            </button>
                        </div>
                    </x-slot>
                </x-modal>

                <!-- Employee Modal -->
                <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50"></div>
                    
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
                        <!-- Loading Overlay -->
                        <div x-show="isLoading" class="absolute inset-0 bg-white/75 flex items-center justify-center z-20 rounded-lg">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-blue-600" x-text="editingEmployee ? 'Updating employee...' : 'Adding employee...'"></p>
                            </div>
                        </div>
                        
                        <!-- Network Error Alert -->
                        <div x-show="networkError !== null" x-transition:enter="transition ease-out duration-300" 
                             x-transition:enter-start="opacity-0 transform -translate-y-2" 
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 transform translate-y-0" 
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             :class="{ 
                                'bg-red-50 border-red-400 text-red-700': typeof networkError === 'string',
                                'bg-green-50 border-green-400 text-green-700': networkError !== null && typeof networkError === 'object' && networkError.type === 'success'
                             }"
                             class="mb-4 p-4 border rounded flex items-start">
                            <div class="flex-shrink-0">
                                <svg x-show="typeof networkError === 'string'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <svg x-show="networkError !== null && typeof networkError === 'object' && networkError.type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium" x-text="typeof networkError === 'string' ? 'Error' : 'Success'"></h3>
                                <div class="mt-1 text-sm" x-text="typeof networkError === 'string' ? networkError : (networkError && networkError.message ? networkError.message : '')"></div>
                            </div>
                        </div>
                        
                        <h2 class="text-lg sm:text-xl font-bold mb-4" x-text="editingEmployee ? 'Edit Employee' : 'Add New Employee'"></h2>
                        <form @submit.prevent="saveEmployee">
                            @csrf
                            
                            <!-- Error Summary -->
                            <div x-show="Object.keys(validationErrors).length > 0" class="mb-4 p-3 sm:p-4 bg-red-50 border border-red-400 rounded text-red-800 text-sm">
                                <p class="font-semibold mb-2">Please correct the following errors:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <template x-for="(errors, field) in validationErrors" :key="field">
                                        <li>
                                            <span x-text="field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')"></span>: 
                                            <span x-text="errors[0]"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    Name
                                </label>
                                <div class="relative">
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-10 text-sm sm:text-base" 
                                           :class="{'border-red-500': hasError('name') || (formData.name === '' && formSubmitted)}"
                                           id="name" 
                                           type="text" 
                                           x-model="formData.name"
                                           @blur="formData.name === '' ? (validationErrors.name = ['Name is required']) : (delete validationErrors.name)"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none" 
                                         x-show="formData.name === '' && formSubmitted">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <span x-show="hasError('name')" x-text="getError('name')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                    Email
                                </label>
                                <div class="relative">
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-10 text-sm sm:text-base" 
                                       :class="{'border-red-500': hasError('email') || !isValidEmail(formData.email) && formData.email !== ''}"
                                       id="email" 
                                       type="email" 
                                       x-model="formData.email"
                                       @blur="validateEmail()"
                                       required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none" 
                                         x-show="!isValidEmail(formData.email) && formData.email !== ''">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <span x-show="hasError('email')" x-text="getError('email')" class="text-red-500 text-xs mt-1 block"></span>
                                <span x-show="!isValidEmail(formData.email) && formData.email !== '' && !hasError('email')" class="text-red-500 text-xs mt-1 block">
                                    Please enter a valid email address
                                </span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                    Phone
                                </label>
                                <div class="relative">
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-10 text-sm sm:text-base" 
                                       :class="{'border-red-500': hasError('phone') || !isValidPhone(formData.phone) && formData.phone !== ''}"
                                       id="phone" 
                                       type="text" 
                                       x-model="formData.phone"
                                       @blur="validatePhone()"
                                       required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none" 
                                         x-show="!isValidPhone(formData.phone) && formData.phone !== ''">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <span x-show="hasError('phone')" x-text="getError('phone')" class="text-red-500 text-xs mt-1 block"></span>
                                <span x-show="!isValidPhone(formData.phone) && formData.phone !== '' && !hasError('phone')" class="text-red-500 text-xs mt-1 block">
                                    Please enter a valid phone number (e.g., +1234567890)
                                </span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                                    Position
                                </label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-sm sm:text-base" 
                                       :class="{'border-red-500': hasError('position')}"
                                       id="position" 
                                       type="text" 
                                       x-model="formData.position"
                                       required>
                                <span x-show="hasError('position')" x-text="getError('position')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="employment_type">
                                    Employment Type
                                </label>
                                <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-sm sm:text-base"
                                        :class="{'border-red-500': hasError('employment_type')}"
                                        id="employment_type"
                                        x-model="formData.employment_type"
                                        required>
                                    <option value="full-time">Full Time</option>
                                    <option value="part-time">Part Time</option>
                                </select>
                                <span x-show="hasError('employment_type')" x-text="getError('employment_type')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_photo">
                                    Profile Photo
                                </label>
                                <input type="file" 
                                       id="profile_photo" 
                                       @change="handlePhotoUpload"
                                       accept="image/*"
                                       :class="{'border-red-500': hasError('profile_photo')}"
                                       class="w-full text-sm">
                                <span x-show="hasError('profile_photo')" x-text="getError('profile_photo')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                                    Bio
                                </label>
                                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-sm sm:text-base" 
                                          :class="{'border-red-500': hasError('bio')}"
                                          id="bio" 
                                          x-model="formData.bio"
                                          rows="3"></textarea>
                                <span x-show="hasError('bio')" x-text="getError('bio')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0 sm:space-x-3">
                                <button type="submit" 
                                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base"
                                        :disabled="isLoading || Object.keys(validationErrors).length > 0">
                                    <span x-show="!isLoading" x-text="editingEmployee ? 'Update Employee' : 'Add Employee'"></span>
                                    <span x-show="isLoading" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="editingEmployee ? 'Updating...' : 'Adding...'"></span>
                                    </span>
                                </button>
                                <button @click="closeModal" type="button" class="w-full sm:w-auto text-center text-gray-600 hover:text-gray-800 py-2 px-4 rounded border border-gray-300 sm:border-none" :disabled="isLoading">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="px-8 py-6 border-t border-gray-200 flex justify-between">
                <a href="{{ route('shop.setup.welcome') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
                <a href="{{ route('shop.setup.services') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Next: Services
                    <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function employeeSetupManager() {
    return {
        showModal: false,
        editingEmployee: null,
        isLoading: false,
        validationErrors: {},
        formData: {
            name: '',
            email: '',
            phone: '',
            position: '',
            employment_type: 'full-time',
            profile_photo: null,
            bio: ''
        },
        networkError: null,
        formSubmitted: false,
        employeeToDelete: { id: null, name: '' },
        isDeleting: false,

        hasError(field) {
            return Object.keys(this.validationErrors).includes(field);
        },

        getError(field) {
            return this.hasError(field) ? this.validationErrors[field][0] : '';
        },

        isValidEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return email === '' || re.test(String(email).toLowerCase());
        },

        isValidPhone(phone) {
            // Simple regex for phone validation - can be adjusted based on requirements
            const re = /^[+]?[0-9]{10,15}$/;
            return phone === '' || re.test(String(phone).replace(/\s+/g, ''));
        },

        validateEmail() {
            if (!this.isValidEmail(this.formData.email) && this.formData.email !== '') {
                if (!this.validationErrors.email) {
                    this.validationErrors.email = ["Please enter a valid email address"];
                }
            } else if (this.validationErrors.email && this.validationErrors.email[0] === "Please enter a valid email address") {
                delete this.validationErrors.email;
            }
        },

        validatePhone() {
            if (!this.isValidPhone(this.formData.phone) && this.formData.phone !== '') {
                if (!this.validationErrors.phone) {
                    this.validationErrors.phone = ["Please enter a valid phone number"];
                }
            } else if (this.validationErrors.phone && this.validationErrors.phone[0] === "Please enter a valid phone number") {
                delete this.validationErrors.phone;
            }
        },

        validateForm() {
            let isValid = true;
            this.formSubmitted = true;
            
            // Required fields validation
            ['name', 'email', 'phone', 'position'].forEach(field => {
                if (!this.formData[field]) {
                    this.validationErrors[field] = [`${field.charAt(0).toUpperCase() + field.slice(1)} is required`];
                    isValid = false;
                }
            });
            
            // Email validation
            if (this.formData.email && !this.isValidEmail(this.formData.email)) {
                this.validationErrors.email = ["Please enter a valid email address"];
                isValid = false;
            }
            
            // Phone validation
            if (this.formData.phone && !this.isValidPhone(this.formData.phone)) {
                this.validationErrors.phone = ["Please enter a valid phone number"];
                isValid = false;
            }
            
            return isValid;
        },

        openAddModal() {
            this.editingEmployee = null;
            this.resetForm();
            this.isLoading = false;
            this.validationErrors = {};
            this.networkError = null;
            this.formSubmitted = false;
            this.showModal = true;
        },

        closeModal() {
            if (this.isLoading) return; // Prevent closing while submitting
            this.showModal = false;
            this.resetForm();
            this.isLoading = false;
            this.validationErrors = {};
            this.networkError = null;
            this.formSubmitted = false;
        },

        resetForm() {
            this.formData = {
                name: '',
                email: '',
                phone: '',
                position: '',
                employment_type: 'full-time',
                profile_photo: null,
                bio: ''
            };
            this.validationErrors = {};
            this.networkError = null;
            this.formSubmitted = false;
        },

        handlePhotoUpload(event) {
            this.formData.profile_photo = event.target.files[0];
            
            // Basic file validation
            if (this.formData.profile_photo) {
                const fileSize = this.formData.profile_photo.size / 1024 / 1024; // in MB
                const fileType = this.formData.profile_photo.type;
                
                if (fileSize > 5) {
                    this.validationErrors.profile_photo = ["Profile photo must be less than 5MB"];
                } else if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(fileType)) {
                    this.validationErrors.profile_photo = ["Profile photo must be an image (JPEG, PNG, GIF, or WEBP)"];
                } else if (this.validationErrors.profile_photo) {
                    delete this.validationErrors.profile_photo;
                }
            }
        },

        async editEmployee(id) {
            try {
                this.validationErrors = {};
                this.networkError = null;
                this.isLoading = true;
                
                const response = await fetch(`/shop/setup/employees/${id}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.editingEmployee = id;
                    this.formData = {
                        name: data.employee.name,
                        email: data.employee.email,
                        phone: data.employee.phone,
                        position: data.employee.position,
                        employment_type: data.employee.employment_type,
                        bio: data.employee.bio || '',
                        profile_photo: null
                    };
                    this.showModal = true;
                } else {
                    throw new Error(data.message || 'Failed to load employee data');
                }
            } catch (error) {
                console.error('Error fetching employee:', error);
                this.networkError = `Failed to load employee data: ${error.message}`;
                setTimeout(() => {
                    this.networkError = null;
                }, 5000);
            } finally {
                this.isLoading = false;
            }
        },

        async saveEmployee() {
            if (this.isLoading) return; // Prevent multiple submissions
            
            // Validate form before sending to server
            if (!this.validateForm()) {
                // Scroll to first error
                setTimeout(() => {
                    const firstErrorField = document.querySelector('.border-red-500');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorField.focus();
                    }
                }, 100);
                return;
            }
            
            this.isLoading = true; // Set loading state
            this.networkError = null;
            
            try {
                const formData = new FormData();
                Object.keys(this.formData).forEach(key => {
                    if (this.formData[key] !== null) {
                        formData.append(key, this.formData[key]);
                    }
                });

                const url = this.editingEmployee 
                    ? `/shop/setup/employees/${this.editingEmployee}` 
                    : '/shop/setup/employees';
                
                if (this.editingEmployee) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Show success message briefly before reloading
                    this.networkError = {
                        type: 'success',
                        message: this.editingEmployee ? 'Employee updated successfully!' : 'Employee added successfully!'
                    };
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (data.errors) {
                        // Store validation errors for display in form
                        this.validationErrors = data.errors;
                        console.error('Validation errors:', data.errors);
                        
                        // Scroll to the first error field
                        setTimeout(() => {
                            const firstErrorField = document.querySelector('.border-red-500');
                            if (firstErrorField) {
                                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                firstErrorField.focus();
                            }
                        }, 100);
                    } else {
                        throw new Error(data.message || 'Failed to save employee');
                    }
                }
            } catch (error) {
                console.error('Error saving employee:', error);
                
                // Check for duplicate email error - common SQLSTATE[23000] error for unique constraint violation
                if (error.message && error.message.includes('Duplicate entry') && error.message.includes('email_unique')) {
                    this.validationErrors.email = ['This email address is already in use by another employee.'];
                    
                    // Highlight the email field and scroll to it
                    setTimeout(() => {
                        const emailField = document.querySelector('#email');
                        if (emailField) {
                            emailField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            emailField.focus();
                        }
                    }, 100);
                } else {
                    // General error message
                    this.networkError = `Failed to save employee: ${error.message}`;
                    
                    // Auto-hide the error after 5 seconds
                    setTimeout(() => {
                        this.networkError = null;
                    }, 5000);
                }
            } finally {
                this.isLoading = false; // Reset loading state regardless of outcome
            }
        },

        async confirmDelete(id) {
            const employeeElement = document.querySelector(`[data-employee-id="${id}"]`);
            const employeeName = employeeElement ? employeeElement.getAttribute('data-employee-name') : '';
            this.employeeToDelete = { id, name: employeeName || 'this employee' };
            this.isDeleting = false; // Reset to false initially
        },

        async deleteEmployee() {
            this.isDeleting = true;
            this.networkError = null;

            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`/shop/setup/employees/${this.employeeToDelete.id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Failed to delete employee (HTTP ${response.status})`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Show success message briefly before reloading
                    this.networkError = {
                        type: 'success',
                        message: 'Employee removed successfully!'
                    };
                    
                    // Close the modal
                    this.$dispatch('close-modal', 'delete-employee-modal');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to delete employee');
                }
            } catch (error) {
                console.error('Error deleting employee:', error);
                this.networkError = `Failed to remove employee: ${error.message}`;
                
                // Auto-hide the error after 5 seconds
                setTimeout(() => {
                    this.networkError = null;
                }, 5000);
                this.isDeleting = false; // Reset loading state on error
            }
        }
    }
}
</script>
@endpush
@endsection 