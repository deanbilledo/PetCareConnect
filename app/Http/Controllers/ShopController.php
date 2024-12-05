<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Rating;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    public function index()
    {
        $veterinaryShops = Shop::veterinary()
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->latest()
            ->take(6)
            ->get();

        $groomingShops = Shop::grooming()
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->latest()
            ->take(6)
            ->get();

        $popularShops = Shop::active()
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderByDesc('ratings_count')
            ->take(6)
            ->get();

        return view('home', compact('veterinaryShops', 'groomingShops', 'popularShops'));
    }

    public function groomingShops()
    {
        $groomingShops = Shop::grooming()
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate(12);

        return view('shops.grooming', compact('groomingShops'));
    }

    public function veterinaryShops()
    {
        $veterinaryShops = Shop::veterinary()
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate(12);

        return view('shops.veterinary', compact('veterinaryShops'));
    }

    public function submitReview(Request $request, Shop $shop)
    {
        try {
            Log::info('Starting review submission...');
            
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10'
            ]);

            // Check if user has already reviewed this shop
            $existingRating = Rating::where('user_id', auth()->id())
                                  ->where('shop_id', $shop->id)
                                  ->first();

            if ($existingRating) {
                // Update existing rating
                $existingRating->update([
                    'rating' => $request->rating,
                    'comment' => $request->comment
                ]);
                $message = 'Your review has been updated!';
            } else {
                // Create new rating
                Rating::create([
                    'user_id' => auth()->id(),
                    'shop_id' => $shop->id,
                    'rating' => $request->rating,
                    'comment' => $request->comment
                ]);
                $message = 'Thank you for your review!';
            }

            // Update shop's average rating
            $shop->rating = $shop->ratings()->avg('rating');
            $shop->save();

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error in submitReview: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'There was an error submitting your review. Please try again.');
        }
    }
} 


