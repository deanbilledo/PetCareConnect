@extends('layouts.shop')

@section('content')
<div x-data="employeeManager()" class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Employees</h1>
        <button @click="openAddModal()" type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            Add New Employee
        </button>
    </div>

    <!-- Employee Modal -->
    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg z-10">
            <h2 class="text-xl font-bold mb-4" x-text="editingEmployee ? 'Edit Employee' : 'Add New Employee'"></h2>
            <form @submit.prevent="saveEmployee">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="name" 
                           type="text" 
                           x-model="formData.name"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="email" 
                           type="email" 
                           x-model="formData.email"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                        Phone
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="phone" 
                           type="text" 
                           x-model="formData.phone"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                        Position
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="position" 
                           type="text" 
                           x-model="formData.position"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="employment_type">
                        Employment Type
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="employment_type"
                            x-model="formData.employment_type"
                            required>
                        <option value="full-time">Full Time</option>
                        <option value="part-time">Part Time</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_photo">
                        Profile Photo
                    </label>
                    <input type="file" 
                           id="profile_photo" 
                           @change="handlePhotoUpload"
                           accept="image/*"
                           class="w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                        Bio
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                              id="bio" 
                              x-model="formData.bio"
                              rows="3"></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <span x-text="editingEmployee ? 'Update Employee' : 'Add Employee'"></span>
                    </button>
                    <button @click="closeModal" type="button" class="text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($employees as $employee)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                    <p>🕒 {{ ucfirst($employee->employment_type) }}</p>
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
</div>

@push('scripts')
<script>
function employeeManager() {
    return {
        showModal: false,
        editingEmployee: null,
        formData: {
            name: '',
            email: '',
            phone: '',
            position: '',
            employment_type: 'full-time',
            profile_photo: null,
            bio: ''
        },

        openAddModal() {
            this.editingEmployee = null;
            this.resetForm();
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
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
        },

        handlePhotoUpload(event) {
            this.formData.profile_photo = event.target.files[0];
        },

        async editEmployee(id) {
            try {
                const response = await fetch(`/shop/employees/${id}`);
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
            try {
                const formData = new FormData();
                Object.keys(this.formData).forEach(key => {
                    if (this.formData[key] !== null) {
                        formData.append(key, this.formData[key]);
                    }
                });

                const url = this.editingEmployee 
                    ? `/shop/employees/${this.editingEmployee}` 
                    : '/shop/employees';
                
                if (this.editingEmployee) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: 'POST', // Always use POST, let Laravel handle method spoofing
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to save employee');
                }

                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to save employee');
                }
            } catch (error) {
                console.error('Error saving employee:', error);
                alert(error.message || 'Failed to save employee');
            }
        },

        async confirmDelete(id) {
            if (!confirm('Are you sure you want to remove this employee?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');

                const response = await fetch(`/shop/employees/${id}`, {
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
            }
        }
    }
}
</script>
@endpush
@endsection