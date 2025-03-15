@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-6">Test Report Notifications</h1>
    
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">Report Under Review Notification</h2>
        
        <div class="border rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 mt-1">
                    <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-900">Your report is under review</h3>
                    <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">Your report for Pet Salon is now under review by our admin team.</p>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>2 hours ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="text-xl font-semibold mb-4">Report Resolved Notification</h2>
        
        <div class="border rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 mt-1">
                    <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-900">Your report has been resolved</h3>
                    <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">Your report for Pet Salon has been resolved by our admin team.

Explanation from admin: After investigation, we have identified that the salon did not follow our guidelines for pet care. We have issued a warning and provided additional training to the staff. The salon has also offered a full refund for your visit and a complimentary service on your next appointment.</p>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>1 hour ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="text-xl font-semibold mb-4">Report Dismissed Notification</h2>
        
        <div class="border rounded-lg p-4">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 mt-1">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-900">Your report has been dismissed</h3>
                    <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">Your report about user John Doe has been dismissed by our admin team.

Explanation from admin: After a thorough review of your report and the communications between you and the reported user, we could not find evidence of policy violations. The disagreement appears to be a misunderstanding rather than a breach of our terms of service.</p>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>30 minutes ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mb-4">
        <a href="{{ route('admin.support') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Back to Support
        </a>
    </div>
</div>
@endsection 