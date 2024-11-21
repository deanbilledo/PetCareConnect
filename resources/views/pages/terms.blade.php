@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Terms of Service</h1>
    <div class="prose max-w-none">
        <!-- Add your terms of service content here -->
        <p>Last updated: {{ date('F d, Y') }}</p>
        
        <h2 class="text-xl font-semibold mt-6 mb-4">1. Terms of Use</h2>
        <p>BClients are required to book appointments at least 24 hours in advance to allow providers adequate p reparation time. Cancellations made within 12 hours of the appointment may incur a fee, which will be communicated at the time of booking. Consistent misuse of the platform, such as repeatedly booking and failing to show up for appointments, may result in account suspension or termination.</p>
        
        <!-- Add more sections as needed -->
    </div>
</div>
@endsection 