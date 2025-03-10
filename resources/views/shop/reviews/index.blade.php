@extends('layouts.shop')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Reviews Header -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Shop Reviews</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage and view all your customer reviews</p>
                    </div>

                    <!-- Reviews Stats -->
                    @php
                        $shopRatings = $shop->ratings;
                        $avgRating = $shopRatings->avg('rating') ?? 0;
                        $totalReviews = $shopRatings->count();
                        $positiveReviews = $shopRatings->where('rating', '>=', 4)->count();
                        $positivePercentage = $totalReviews > 0 ? ($positiveReviews / $totalReviews * 100) : 0;
                        $newThisMonth = $shopRatings->where('created_at', '>=', now()->startOfMonth())->count();
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-blue-600">{{ number_format($avgRating, 1) }}</div>
                            <div class="text-sm text-gray-600">Average Rating</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-blue-600">{{ $totalReviews }}</div>
                            <div class="text-sm text-gray-600">Total Reviews</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-green-600">{{ number_format($positivePercentage, 0) }}%</div>
                            <div class="text-sm text-gray-600">Positive Reviews</div>
                        </div>
                        <div class="bg-white p-6 rounded-lg border">
                            <div class="text-4xl font-bold text-yellow-600">{{ $newThisMonth }}</div>
                            <div class="text-sm text-gray-600">New This Month</div>
                        </div>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Rating Distribution</h3>
                        <div class="space-y-2">
                            @foreach(range(5, 1) as $rating)
                                @php
                                    $ratingCount = $shopRatings->where('rating', $rating)->count();
                                    $percentage = $totalReviews > 0 ? ($ratingCount / $totalReviews * 100) : 0;
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-16 text-sm text-gray-600">{{ $rating }} stars</div>
                                    <div class="flex-1 h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="w-16 text-sm text-gray-600">{{ $ratingCount }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold">Recent Reviews</h3>
                        @forelse($ratings as $rating)
                            <div class="bg-white rounded-lg shadow p-6">
                                <!-- User Info and Rating -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <img src="{{ $rating->user->profile_photo_path ? asset('storage/' . $rating->user->profile_photo_path) : asset('images/default-profile.png') }}" 
                                             alt="Profile" 
                                             class="w-10 h-10 rounded-full object-cover mr-3">
                                        <div>
                                            <div class="font-medium">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</div>
                                            <div class="flex items-center">
                                                <div class="text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating->rating)
                                                            <span class="text-2xl">★</span>
                                                        @else
                                                            <span class="text-2xl text-gray-300">★</span>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-gray-500 text-sm ml-2">{{ $rating->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Services -->
                                @if($rating->appointment && $rating->appointment->services->isNotEmpty())
                                    <div class="mb-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($rating->appointment->services as $service)
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                                    {{ $service->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Review Text -->
                                @if($rating->review)
                                    <div class="mb-4">
                                        <p class="text-gray-700">{{ $rating->review }}</p>
                                    </div>
                                @endif

                                <!-- Employee Rating -->
                                @if($rating->appointment && $rating->appointment->employee && $rating->appointment->employee->staffRatings()->where('appointment_id', $rating->appointment_id)->exists())
                                    @php
                                        $staffRating = $rating->appointment->employee->staffRatings()
                                            ->where('appointment_id', $rating->appointment_id)
                                            ->first();
                                    @endphp
                                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center mb-2">
                                            <img src="{{ $rating->appointment->employee->profile_photo_url }}" 
                                                 alt="{{ $rating->appointment->employee->name }}" 
                                                 class="w-8 h-8 rounded-full object-cover mr-2">
                                            <div>
                                                <p class="font-medium text-sm">{{ $rating->appointment->employee->name }}</p>
                                                <div class="flex items-center">
                                                    <div class="text-yellow-400">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $staffRating->rating)
                                                                <span class="text-lg">★</span>
                                                            @else
                                                                <span class="text-lg text-gray-300">★</span>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($staffRating->review)
                                            <p class="text-gray-700 text-sm mt-2">{{ $staffRating->review }}</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Shop Comment Section -->
                                <div class="mt-4 border-t pt-4">
                                    @if($rating->shop_comment)
                                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                            <p class="text-sm font-medium text-blue-800 mb-1">Shop's Response:</p>
                                            <p class="text-gray-700">{{ $rating->shop_comment }}</p>
                                        </div>
                                    @endif

                                    <form action="{{ route('shop.reviews.comment', $rating->id) }}" method="POST" class="mt-2">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                   name="shop_comment" 
                                                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                                   placeholder="Add a response to this review..."
                                                   value="{{ $rating->shop_comment }}">
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                {{ $rating->shop_comment ? 'Update Response' : 'Add Response' }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500 text-center py-8">
                                No reviews yet.
                            </div>
                        @endforelse

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $ratings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 