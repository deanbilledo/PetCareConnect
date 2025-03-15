@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="text-center mb-8">
            <svg class="h-16 w-16 text-blue-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-2xl font-bold mb-2">Appeal Already Submitted</h1>
            <p class="text-gray-600 dark:text-gray-400">You have already submitted an appeal for this report.</p>
        </div>
        
        <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg mb-6">
            <h2 class="font-semibold text-lg mb-4">Your Appeal Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status:</p>
                    <p class="font-medium">
                        @if($existingAppeal->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Pending Review
                            </span>
                        @elseif($existingAppeal->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Approved
                            </span>
                        @elseif($existingAppeal->status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Rejected
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Submitted On:</p>
                    <p class="font-medium">{{ $existingAppeal->created_at->format('M d, Y, h:i A') }}</p>
                </div>
                
                @if($existingAppeal->resolved_at)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Resolved On:</p>
                    <p class="font-medium">{{ $existingAppeal->resolved_at->format('M d, Y, h:i A') }}</p>
                </div>
                @endif
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Your Appeal Reason:</p>
                <p class="font-medium mt-1 p-2 bg-white dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">{{ $existingAppeal->reason }}</p>
            </div>
            
            @if($existingAppeal->evidence_path)
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Your Evidence:</p>
                @php
                    $extension = pathinfo($existingAppeal->evidence_path, PATHINFO_EXTENSION);
                    $isPdf = strtolower($extension) === 'pdf';
                @endphp
                
                @if($isPdf)
                    <a href="{{ $existingAppeal->getEvidenceUrl() }}" target="_blank" class="inline-flex items-center mt-2 text-blue-600 hover:text-blue-800">
                        <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        View PDF Document
                    </a>
                @else
                    <img src="{{ $existingAppeal->getEvidenceUrl() }}" alt="Appeal Evidence" class="mt-2 max-w-md rounded-lg border border-gray-200 dark:border-gray-700">
                @endif
            </div>
            @endif
            
            @if($existingAppeal->admin_notes && $existingAppeal->status !== 'pending')
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Admin Response:</p>
                <p class="font-medium mt-1 p-2 bg-white dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">{{ $existingAppeal->admin_notes }}</p>
            </div>
            @endif
        </div>
        
        <div class="flex justify-center">
            <a href="{{ route('notifications.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded">
                Return to Notifications
            </a>
        </div>
    </div>
</div>
@endsection 