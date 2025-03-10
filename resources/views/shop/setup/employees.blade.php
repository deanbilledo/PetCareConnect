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
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border">
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
                                <button @click="confirmDelete({{ $employee->id }})" 
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

                <!-- Employee Modal -->
                <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50">
                    <div class="fixed inset-0 bg-black opacity-50"></div>
                    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg z-10 relative">
                        <!-- Loading Overlay -->
                        <div x-show="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-20 rounded-lg">
                            <div class="text-center">
                                <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-blue-600" x-text="editingEmployee ? 'Updating employee...' : 'Adding employee...'"></p>
                            </div>
                        </div>
                        
                        <h2 class="text-xl font-bold mb-4" x-text="editingEmployee ? 'Edit Employee' : 'Add New Employee'"></h2>
                        <form @submit.prevent="saveEmployee">
                            @csrf
                            
                            <!-- Error Summary -->
                            <div x-show="Object.keys(validationErrors).length > 0" class="mb-4 p-4 bg-red-50 border border-red-400 rounded text-red-800">
                                <p class="font-semibold mb-2">Please correct the following errors:</p>
                                <ul class="list-disc pl-5 space-y-1 text-sm">
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
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                       :class="{'border-red-500': hasError('name')}"
                                       id="name" 
                                       type="text" 
                                       x-model="formData.name"
                                       required>
                                <span x-show="hasError('name')" x-text="getError('name')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                    Email
                                </label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                       :class="{'border-red-500': hasError('email')}"
                                       id="email" 
                                       type="email" 
                                       x-model="formData.email"
                                       required>
                                <span x-show="hasError('email')" x-text="getError('email')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                    Phone
                                </label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                       :class="{'border-red-500': hasError('phone')}"
                                       id="phone" 
                                       type="text" 
                                       x-model="formData.phone"
                                       required>
                                <span x-show="hasError('phone')" x-text="getError('phone')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                                    Position
                                </label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
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
                                <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
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
                                       class="w-full">
                                <span x-show="hasError('profile_photo')" x-text="getError('profile_photo')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                                    Bio
                                </label>
                                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                          :class="{'border-red-500': hasError('bio')}"
                                          id="bio" 
                                          x-model="formData.bio"
                                          rows="3"></textarea>
                                <span x-show="hasError('bio')" x-text="getError('bio')" class="text-red-500 text-xs mt-1 block"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                        :disabled="isLoading">
                                    <span x-show="!isLoading" x-text="editingEmployee ? 'Update Employee' : 'Add Employee'"></span>
                                    <span x-show="isLoading" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="editingEmployee ? 'Updating...' : 'Adding...'"></span>
                                    </span>
                                </button>
                                <button @click="closeModal" type="button" class="text-gray-600 hover:text-gray-800" :disabled="isLoading">
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

        hasError(field) {
            return Object.keys(this.validationErrors).includes(field);
        },

        getError(field) {
            return this.hasError(field) ? this.validationErrors[field][0] : '';
        },

        openAddModal() {
            this.editingEmployee = null;
            this.resetForm();
            this.isLoading = false;
            this.validationErrors = {};
            this.showModal = true;
        },

        closeModal() {
            if (this.isLoading) return; // Prevent closing while submitting
            this.showModal = false;
            this.resetForm();
            this.isLoading = false;
            this.validationErrors = {};
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
        },

        handlePhotoUpload(event) {
            this.formData.profile_photo = event.target.files[0];
        },

        async editEmployee(id) {
            try {
                this.validationErrors = {};
                const response = await fetch(`/shop/setup/employees/${id}`);
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
                }
            } catch (error) {
                console.error('Error fetching employee:', error);
                alert('Failed to load employee data');
            }
        },

        async saveEmployee() {
            if (this.isLoading) return; // Prevent multiple submissions
            
            this.isLoading = true; // Set loading state
            this.validationErrors = {}; // Clear previous errors
            
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
                    window.location.reload();
                } else {
                    if (data.errors) {
                        // Store validation errors for display in form
                        this.validationErrors = data.errors;
                        console.error('Validation errors:', data.errors);
                    } else {
                        throw new Error(data.message || 'Failed to save employee');
                    }
                }
            } catch (error) {
                console.error('Error saving employee:', error);
                alert(error.message || 'Failed to save employee');
            } finally {
                this.isLoading = false; // Reset loading state regardless of outcome
            }
        },

        async confirmDelete(id) {
            if (!confirm('Are you sure you want to remove this employee?')) {
                return;
            }
            
            this.isLoading = true; // Set loading state

            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');

                const response = await fetch(`/shop/setup/employees/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete employee');
                }

                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to delete employee');
                }
            } catch (error) {
                console.error('Error deleting employee:', error);
                alert(error.message || 'Failed to delete employee');
                this.isLoading = false; // Reset loading state on error
            }
        }
    }
}
</script>
@endpush
@endsection 