<!-- Cancel Appointment Modal -->
<div x-show="showCancelModal" 
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
     x-cloak>
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to cancel this appointment?
                </p>
                <div class="mt-4">
                    <label for="cancelReason" class="block text-sm font-medium text-gray-700 text-left">
                        Reason for Cancellation
                    </label>
                    <textarea
                        x-ref="cancelReason"
                        id="cancelReason"
                        name="reason"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Please provide a reason for cancellation"></textarea>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button
                    @click="handleCancel"
                    class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Yes, Cancel Appointment
                </button>
                <button
                    @click="showCancelModal = false"
                    class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    No, Keep Appointment
                </button>
            </div>
        </div>
    </div>
</div> 