@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Success Message -->
        <div id="success-message" class="hidden bg-green-50 text-green-600 p-4 rounded-lg mb-4">
            Health record added successfully
        </div>

        <!-- Back Button -->
        <div class="mb-6">
            @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                <a href="{{ route('shop.appointments') }}" class="flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Appointments
                </a>
            @else
                <a href="{{ route('profile.pets.health-record', $pet->id) }}" class="flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Health Record
                </a>
            @endif
        </div>

        <h1 class="text-2xl font-bold mb-6">
            @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                Add Health Record for {{ $pet->name }} - {{ auth()->user()->shop->name }}
            @else
                Add Health Record for {{ $pet->name }}
            @endif
        </h1>

        <!-- Health Record Forms -->
        <div class="space-y-8">
            <!-- Vaccination Record Form -->
            <div class="border-b pb-8">
                <h2 class="text-xl font-semibold mb-4">Vaccination Record</h2>
                <div id="vaccination-errors" class="hidden bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                    <ul class="list-disc pl-5"></ul>
                </div>
                <form id="vaccination-form" action="{{ route('profile.pets.vaccination.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                        <input type="hidden" name="redirect_to" value="{{ route('shop.appointments') }}">
                        <input type="hidden" name="administered_by" value="{{ auth()->user()->shop->name }}">
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Name</label>
                        <input type="text" name="vaccine_name" required value="{{ old('vaccine_name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Administered</label>
                        <input type="date" name="administered_date" required value="{{ old('administered_date', now()->format('Y-m-d')) }}"
                               @if(auth()->user()->shop && auth()->user()->shop->status === 'active') readonly @endif
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    @if(!auth()->user()->shop || auth()->user()->shop->status !== 'active')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Administered By</label>
                        <input type="text" name="administered_by" required value="{{ old('administered_by') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                        <input type="date" name="next_due_date" required value="{{ old('next_due_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                            Add Vaccination Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Parasite Control Form -->
            <div class="border-b pb-8">
                <h2 class="text-xl font-semibold mb-4">Parasite Control</h2>
                <div id="parasite-errors" class="hidden bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                    <ul class="list-disc pl-5"></ul>
                </div>
                <form id="parasite-form" action="{{ route('profile.pets.parasite-control.store', $pet->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                        <input type="hidden" name="redirect_to" value="{{ route('shop.appointments') }}">
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Name</label>
                        <input type="text" name="treatment_name" required value="{{ old('treatment_name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Type</label>
                        <select name="treatment_type" required 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Type</option>
                            <option value="Flea" {{ old('treatment_type') == 'Flea' ? 'selected' : '' }}>Flea Treatment</option>
                            <option value="Tick" {{ old('treatment_type') == 'Tick' ? 'selected' : '' }}>Tick Treatment</option>
                            <option value="Worm" {{ old('treatment_type') == 'Worm' ? 'selected' : '' }}>Deworming</option>
                            <option value="Other" {{ old('treatment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Date</label>
                        <input type="date" name="treatment_date" required value="{{ old('treatment_date', now()->format('Y-m-d')) }}"
                               @if(auth()->user()->shop && auth()->user()->shop->status === 'active') readonly @endif
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Treatment Due</label>
                        <input type="date" name="next_treatment_date" required value="{{ old('next_treatment_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                            Add Parasite Control Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Health Issues Form -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Health Issue</h2>
                <div id="health-errors" class="hidden bg-red-50 text-red-500 p-4 rounded-lg mb-4">
                    <ul class="list-disc pl-5"></ul>
                </div>
                <form id="health-issue-form" action="{{ route('profile.pets.health-issue.store', $pet->id) }}" method="POST" class="grid grid-cols-1 gap-4">
                    @csrf
                    @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                        <input type="hidden" name="redirect_to" value="{{ route('shop.appointments') }}">
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issue Title</label>
                        <input type="text" name="issue_title" required value="{{ old('issue_title') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Identified</label>
                        <input type="date" name="identified_date" required value="{{ old('identified_date', now()->format('Y-m-d')) }}"
                               @if(auth()->user()->shop && auth()->user()->shop->status === 'active') readonly @endif
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" required 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Treatment/Medication</label>
                        <input type="text" name="treatment" required value="{{ old('treatment') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    @if(auth()->user()->shop && auth()->user()->shop->status === 'active')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Veterinarian Notes</label>
                        <textarea name="vet_notes" rows="3" required
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('vet_notes') }}</textarea>
                    </div>
                    @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                        <textarea name="vet_notes" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('vet_notes') }}</textarea>
                    </div>
                    @endif
                    <div>
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">
                            Add Health Issue Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = {
        'vaccination-form': {
            errorContainer: document.getElementById('vaccination-errors'),
            errorList: document.getElementById('vaccination-errors').querySelector('ul')
        },
        'parasite-form': {
            errorContainer: document.getElementById('parasite-errors'),
            errorList: document.getElementById('parasite-errors').querySelector('ul')
        },
        'health-issue-form': {
            errorContainer: document.getElementById('health-errors'),
            errorList: document.getElementById('health-errors').querySelector('ul')
        }
    };
    const successMessage = document.getElementById('success-message');

    // Function to display errors
    function displayErrors(formId, errors) {
        const formElements = forms[formId];
        formElements.errorList.innerHTML = '';
        
        Object.values(errors).forEach(error => {
            if (Array.isArray(error)) {
                error.forEach(message => {
                    const li = document.createElement('li');
                    li.textContent = message;
                    formElements.errorList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = error;
                formElements.errorList.appendChild(li);
            }
        });
        
        formElements.errorContainer.classList.remove('hidden');
    }

    // Function to clear errors
    function clearErrors(formId) {
        const formElements = forms[formId];
        formElements.errorList.innerHTML = '';
        formElements.errorContainer.classList.add('hidden');
    }

    // Handle form submissions
    Object.keys(forms).forEach(formId => {
        const form = document.getElementById(formId);
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors(formId);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Show success message
                    successMessage.classList.remove('hidden');
                    form.reset();

                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 3000);
                } else {
                    // Display validation errors
                    if (data.errors) {
                        displayErrors(formId, data.errors);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                displayErrors(formId, {'error': ['An unexpected error occurred. Please try again.']});
            }
        });
    });
});
</script>
@endpush

@endsection