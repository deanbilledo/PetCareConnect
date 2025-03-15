@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Appeal Details</h1>
        <a href="{{ route('admin.support') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Support
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-blue-600">Appeal Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600">Status: 
                    <span class="font-semibold 
                        @if($appeal->status == 'pending') text-yellow-600
                        @elseif($appeal->status == 'approved') text-green-600
                        @else text-red-600 @endif">
                        {{ ucfirst($appeal->status) }}
                    </span>
                </p>
                <p class="text-gray-600">Appeal Date: <span class="font-semibold">{{ $appeal->created_at->format('F d, Y h:i A') }}</span></p>
                @if($appeal->resolved_at)
                <p class="text-gray-600">Resolved Date: <span class="font-semibold">{{ $appeal->resolved_at->format('F d, Y h:i A') }}</span></p>
                @endif
            </div>
            <div>
                <p class="text-gray-600">Appeal Reason:</p>
                <div class="mt-2 p-3 bg-gray-100 rounded">
                    {{ $appeal->reason }}
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-4 text-blue-600">User Report Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600">Report ID: <span class="font-semibold">{{ $report->id }}</span></p>
                <p class="text-gray-600">Reported By: <span class="font-semibold">{{ $report->user->name }}</span></p>
                <p class="text-gray-600">Report Date: <span class="font-semibold">{{ $report->created_at->format('F d, Y h:i A') }}</span></p>
                <p class="text-gray-600">Report Status: 
                    <span class="font-semibold 
                        @if($report->status == 'pending') text-yellow-600
                        @elseif($report->status == 'resolved') text-green-600
                        @elseif($report->status == 'dismissed') text-gray-600
                        @else text-red-600 @endif">
                        {{ ucfirst($report->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-gray-600">Report Reason:</p>
                <div class="mt-2 p-3 bg-gray-100 rounded">
                    {{ $report->reason }}
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-4 text-blue-600">Reported User Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">User Name: <span class="font-semibold">{{ $report->reportedUser->name }}</span></p>
                <p class="text-gray-600">User Email: <span class="font-semibold">{{ $report->reportedUser->email }}</span></p>
                <p class="text-gray-600">Joined Date: <span class="font-semibold">{{ $report->reportedUser->created_at->format('F d, Y') }}</span></p>
                <p class="text-gray-600">Role: <span class="font-semibold">{{ ucfirst($report->reportedUser->role) }}</span></p>
            </div>
        </div>
    </div>

    @if($appeal->status == 'pending')
    <div class="flex justify-end gap-4">
        <form method="POST" action="{{ route('admin.appeals.update', $appeal->id) }}" class="inline" id="approveForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="approved">
            <input type="hidden" name="admin_notes" id="approveNotes">
            <button type="button" onclick="showApproveModal()"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Approve Appeal
            </button>
        </form>
        
        <form method="POST" action="{{ route('admin.appeals.update', $appeal->id) }}" class="inline" id="rejectForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="rejected">
            <input type="hidden" name="admin_notes" id="rejectNotes">
            <button type="button" onclick="showRejectModal()"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Reject Appeal
            </button>
        </form>
    </div>
    @endif

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg w-1/2 p-6">
            <h3 class="text-lg font-bold mb-4">Approve Appeal</h3>
            <p class="mb-4">Please provide notes explaining why you are approving this appeal:</p>
            <textarea id="approveModalNotes" class="w-full p-2 border rounded" rows="4"></textarea>
            <div class="flex justify-end mt-4 gap-2">
                <button onclick="document.getElementById('approveModal').classList.add('hidden')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <button onclick="submitApproval()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Confirm Approval
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg w-1/2 p-6">
            <h3 class="text-lg font-bold mb-4">Reject Appeal</h3>
            <p class="mb-4">Please provide notes explaining why you are rejecting this appeal:</p>
            <textarea id="rejectModalNotes" class="w-full p-2 border rounded" rows="4"></textarea>
            <div class="flex justify-end mt-4 gap-2">
                <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <button onclick="submitRejection()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function showApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
    }
    
    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    
    function submitApproval() {
        const notes = document.getElementById('approveModalNotes').value;
        if (notes.length < 10) {
            alert('Please provide more detailed notes (at least 10 characters).');
            return;
        }
        document.getElementById('approveNotes').value = notes;
        document.getElementById('approveForm').submit();
    }
    
    function submitRejection() {
        const notes = document.getElementById('rejectModalNotes').value;
        if (notes.length < 10) {
            alert('Please provide more detailed notes (at least 10 characters).');
            return;
        }
        document.getElementById('rejectNotes').value = notes;
        document.getElementById('rejectForm').submit();
    }
</script>
@endsection 