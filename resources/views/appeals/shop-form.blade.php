@extends(session('shop_mode') ? 'layouts.shop' : 'layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Appeal Report Against Your Shop</h1>
        
        <div class="mb-6 bg-amber-50 dark:bg-amber-900 p-4 rounded-lg">
            <h2 class="font-semibold text-lg mb-2">Report Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Report ID:</p>
                    <p class="font-medium">RPT-{{ $report->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Report Type:</p>
                    <p class="font-medium">{{ ucwords(str_replace('_', ' ', $report->report_type)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status:</p>
                    <p class="font-medium">{{ ucfirst($report->status) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Date Reported:</p>
                    <p class="font-medium">{{ $report->created_at->format('M d, Y, h:i A') }}</p>
                </div>
            </div>
            
            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Report Description:</p>
                <p class="font-medium">{{ $report->description }}</p>
            </div>
            
            @if($report->image_path)
            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Evidence Provided:</p>
                <img src="{{ $report->getImageUrl() }}" alt="Report Evidence" class="mt-2 max-w-md rounded-lg border border-gray-200 dark:border-gray-700">
            </div>
            @endif
        </div>

        <form action="{{ route('shop.report.appeal.submit', ['report' => $report->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900 text-red-600 dark:text-red-200 p-4 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Reason for Appeal <span class="text-red-500">*</span>
                </label>
                <textarea id="reason" name="reason" rows="6" 
                          class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" 
                          placeholder="Explain why you believe this report is incorrect or should be dismissed..." 
                          required>{{ old('reason') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Please provide a detailed explanation (minimum 20 characters).</p>
            </div>
            
            <div>
                <label for="evidence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Supporting Evidence (Optional)
                </label>
                <input type="file" id="evidence" name="evidence" 
                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2">
                <p class="text-sm text-gray-500 mt-1">Accepted file types: JPG, PNG, PDF (max size: 5MB)</p>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="{{ route('notifications.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Submit Appeal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 