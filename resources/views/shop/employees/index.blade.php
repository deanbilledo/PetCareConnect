@extends('layouts.shop')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endsection

@section('content')
<div x-data="employeeManager()" class="container mx-auto px-4 py-6">
    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button @click="currentTab = 'list'" 
                    :class="{'border-blue-500 text-blue-600': currentTab === 'list',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'list'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Employees List
            </button>
            <button @click="currentTab = 'schedule'" 
                    :class="{'border-blue-500 text-blue-600': currentTab === 'schedule',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentTab !== 'schedule'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Schedule & Availability
            </button>
        </nav>
    </div>

    <!-- Employees List Tab -->
    <div x-show="currentTab === 'list'">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Employees</h1>
            <button @click="openAddModal()" type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                Add New Employee
            </button>
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

                        <!-- Services Section -->
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Assigned Services</h4>
                            @if($employee->services && $employee->services->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($employee->services as $service)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $service->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">No services assigned</p>
                            @endif
                        </div>
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

    <!-- Schedule & Availability Tab -->
    <div x-show="currentTab === 'schedule'" x-cloak>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-12 gap-6">
                <!-- Calendar Section (9 columns) -->
                <div class="col-span-9">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold">Work Schedule</h2>
                            <p class="text-sm text-gray-600">Manage shifts and view appointments</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <select x-model="selectedEmployee" @change="loadEvents" class="rounded-md border-gray-300">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                            <div class="flex space-x-2">
                                <button @click="calendarView = 'timeGridWeek'" 
                                        :class="{'bg-blue-500 text-white': calendarView === 'timeGridWeek', 'bg-gray-100': calendarView !== 'timeGridWeek'}"
                                        class="px-3 py-1 rounded">
                                    Week
                                </button>
                                <button @click="calendarView = 'dayGridMonth'" 
                                        :class="{'bg-blue-500 text-white': calendarView === 'dayGridMonth', 'bg-gray-100': calendarView !== 'dayGridMonth'}"
                                        class="px-3 py-1 rounded">
                                    Month
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="calendar" class="min-h-[600px]"></div>
                </div>

                <!-- Sidebar (3 columns) -->
                <div class="col-span-3 border-l pl-6">
                    <!-- Employee Availability Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Availability Settings</h3>
                        <div class="space-y-4">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $index => $day)
                            <div class="border-b pb-2">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium">{{ $day }}</span>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               data-day="{{ strtolower($day) }}"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                               checked>
                                        <span class="ml-2 text-sm text-gray-600">Available</span>
                                    </label>
                                </div>
                                <div class="flex space-x-2 items-center text-sm">
                                    <select data-day="{{ strtolower($day) }}" 
                                            data-type="start"
                                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach(range(6, 22) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-gray-500">to</span>
                                    <select data-day="{{ strtolower($day) }}" 
                                            data-type="end"
                                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach(range(6, 22) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" 
                                                    {{ $hour === 17 ? 'selected' : '' }}>
                                                {{ sprintf('%02d:00', $hour) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button @click="saveAvailability()" 
                                class="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Availability
                        </button>
                    </div>

                    <!-- Time Off Requests Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Time Off Requests</h3>
                        <button @click="showTimeOffModal = true" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Request Time Off
                        </button>
                        <div class="mt-4 space-y-3">
                            <template x-for="request in timeOffRequests" :key="request.id">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                    <div>
                                        <p class="text-sm font-medium" x-text="request.dates"></p>
                                        <p class="text-xs text-gray-500" x-text="request.status"></p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <template x-if="request.status === 'pending'">
                                            <button @click="approveTimeOff(request.id)" 
                                                    class="text-xs text-green-600 hover:text-green-800">
                                                Approve
                                            </button>
                                        </template>
                                        <button @click="deleteTimeOff(request.id)" 
                                                class="text-xs text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <!-- Time Off Request Modal -->
    <div x-show="showTimeOffModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white rounded-lg p-8 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">Request Time Off</h3>
            <form @submit.prevent="submitTimeOffRequest">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee</label>
                        <select x-model="timeOffForm.employee_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" x-model="timeOffForm.start_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" x-model="timeOffForm.end_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea x-model="timeOffForm.reason" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showTimeOffModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Event Modal -->
    <div x-show="showEventModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="eventModalTitle"></h3>
                    
                    <form @submit.prevent="saveEvent">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" x-model="eventData.title" class="mt-1 block w-full rounded-md border-gray-300" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start</label>
                                <input type="datetime-local" x-model="eventData.start" class="mt-1 block w-full rounded-md border-gray-300" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End</label>
                                <input type="datetime-local" x-model="eventData.end" class="mt-1 block w-full rounded-md border-gray-300" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select x-model="eventData.employee_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select x-model="eventData.type" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="shift">Shift</option>
                                <option value="time_off">Time Off</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea x-model="eventData.notes" class="mt-1 block w-full rounded-md border-gray-300" rows="3"></textarea>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="saveEvent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button @click="closeEventModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
function employeeManager() {
    return {
        currentTab: 'list',
        showModal: false,
        showTimeOffModal: false,
        editingEmployee: null,
        selectedEmployee: '',
        calendarView: 'timeGridWeek',
        timeOffRequests: [],
        timeOffForm: {
            employee_id: '',
            start_date: '',
            end_date: '',
            reason: ''
        },
        calendar: null,
        formData: {
            name: '',
            email: '',
            phone: '',
            position: '',
            employment_type: 'full-time',
            profile_photo: null,
            bio: ''
        },
        showEventModal: false,
        eventModalTitle: 'Add Event',
        eventData: {
            id: null,
            title: '',
            start: '',
            end: '',
            employee_id: '',
            type: 'shift',
            notes: ''
        },

        init() {
            if (this.currentTab === 'schedule') {
                this.$nextTick(() => {
                    if (!this.calendar && document.getElementById('calendar')) {
                        this.initializeCalendar();
                    }
                });
            }

            this.$watch('currentTab', (value) => {
                if (value === 'schedule') {
                    this.$nextTick(() => {
                        if (!this.calendar && document.getElementById('calendar')) {
                            this.initializeCalendar();
                        }
                    });
                }
            });

            this.$watch('calendarView', (value) => {
                if (this.calendar) {
                    this.calendar.changeView(value);
                }
            });
        },

        initializeCalendar() {
            if (this.calendar) {
                this.calendar.destroy();
            }

            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: this.calendarView,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                selectable: true,
                editable: true,
                eventClick: this.handleEventClick.bind(this),
                select: this.handleDateSelect.bind(this),
                eventDrop: this.handleEventDrop.bind(this),
                eventResize: this.handleEventResize.bind(this),
                events: (info, successCallback, failureCallback) => this.loadEvents(info, successCallback),
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                slotDuration: '00:30:00',
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                }
            });

            this.calendar.render();
        },

        loadEvents(info, successCallback) {
            const params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr,
                employee_id: this.selectedEmployee || '',
                shop_id: document.querySelector('meta[name="shop-id"]').content
            });

            fetch(`/shop/schedule/events?${params}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && Array.isArray(data.events)) {
                        const formattedEvents = data.events.map(event => ({
                            id: event.id,
                            title: event.title || 'Untitled Event',
                            start: new Date(event.start).toISOString(),
                            end: new Date(event.end).toISOString(),
                            color: event.type === 'time_off' ? '#ff4d4d' : '#4299e1',
                            employee_id: event.employee_id,
                            type: event.type || 'shift',
                            status: event.status || 'active',
                            extendedProps: {
                                employee_id: event.employee_id,
                                type: event.type || 'shift',
                                notes: event.notes || ''
                            }
                        }));
                        
                        if (typeof successCallback === 'function') {
                            successCallback(formattedEvents);
                        } else if (this.calendar) {
                            this.calendar.removeAllEvents();
                            this.calendar.addEventSource(formattedEvents);
                        }
                    } else {
                        console.error('Invalid events data format:', data);
                        if (typeof successCallback === 'function') {
                            successCallback([]);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    if (typeof successCallback === 'function') {
                        successCallback([]);
                    }
                });
        },

        handleDateSelect(info) {
            const start = info.start ? this.formatDateTimeForInput(info.start) : '';
            const end = info.end ? this.formatDateTimeForInput(info.end) : '';
            
            this.eventData = {
                id: null,
                title: '',
                start: start,
                end: end,
                employee_id: this.selectedEmployee || '',
                type: 'shift',
                notes: ''
            };
            this.eventModalTitle = 'Add Event';
            this.showEventModal = true;
        },

        handleEventClick(info) {
            const start = info.event.start ? this.formatDateTimeForInput(info.event.start) : '';
            const end = info.event.end ? this.formatDateTimeForInput(info.event.end) : '';
            
            this.eventData = {
                id: info.event.id,
                title: info.event.title,
                start: start,
                end: end,
                employee_id: info.event.extendedProps.employee_id || '',
                type: info.event.extendedProps.type || 'shift',
                notes: info.event.extendedProps.notes || ''
            };
            this.eventModalTitle = 'Edit Event';
            this.showEventModal = true;
        },

        formatDateTimeForInput(date) {
            if (!(date instanceof Date)) {
                date = new Date(date);
            }
            // Ensure the date is valid
            if (isNaN(date.getTime())) {
                console.error('Invalid date:', date);
                return '';
            }
            // Format as YYYY-MM-DDTHH:mm
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        handleEventDrop(info) {
            this.updateEvent(info.event);
        },

        handleEventResize(info) {
            this.updateEvent(info.event);
        },

        updateEvent(event) {
            fetch(`/shop/schedule/events/${event.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    start: event.startStr,
                    end: event.endStr
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    this.calendar.refetchEvents();
                }
            })
            .catch(error => {
                console.error('Error updating event:', error);
                this.calendar.refetchEvents();
            });
        },

        saveEvent() {
            if (!this.eventData.title || !this.eventData.start || !this.eventData.end || !this.eventData.employee_id) {
                alert('Please fill in all required fields');
                return;
            }

            const eventData = {
                ...this.eventData,
                start: new Date(this.eventData.start).toISOString(),
                end: new Date(this.eventData.end).toISOString(),
                shop_id: document.querySelector('meta[name="shop-id"]').content
            };

            const url = eventData.id 
                ? `/shop/schedule/events/${eventData.id}`
                : '/shop/schedule/events';
            
            const method = eventData.id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(eventData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.calendar.refetchEvents();
                    this.closeEventModal();
                } else {
                    throw new Error(data.error || 'Failed to save event');
                }
            })
            .catch(error => {
                console.error('Error saving event:', error);
                alert('Failed to save event: ' + error.message);
            });
        },

        closeEventModal() {
            this.showEventModal = false;
            this.eventData = {
                id: null,
                title: '',
                start: '',
                end: '',
                employee_id: '',
                type: 'shift',
                notes: ''
            };
        },

        async submitTimeOffRequest() {
            try {
                const response = await fetch('/shop/schedule/time-off', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.timeOffForm)
                });

                if (!response.ok) {
                    throw new Error('Failed to submit time off request');
                }

                this.showTimeOffModal = false;
                this.timeOffForm = {
                    employee_id: '',
                    start_date: '',
                    end_date: '',
                    reason: ''
                };
                this.loadEvents();
            } catch (error) {
                console.error('Error submitting time off request:', error);
                alert('Failed to submit time off request');
            }
        },

        async saveAvailability() {
            const availability = {};
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'].forEach(day => {
                const available = document.querySelector(`input[type="checkbox"][data-day="${day}"]`).checked;
                const start = document.querySelector(`select[data-day="${day}"][data-type="start"]`).value;
                const end = document.querySelector(`select[data-day="${day}"][data-type="end"]`).value;
                
                availability[day] = {
                    available,
                    start,
                    end
                };
            });

            try {
                const response = await fetch(`/shop/employees/${this.selectedEmployee}/availability`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ availability })
                });

                if (!response.ok) {
                    throw new Error('Failed to save availability');
                }

                alert('Availability saved successfully');
            } catch (error) {
                console.error('Error saving availability:', error);
                alert('Failed to save availability');
            }
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