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

        return view('groomVetLandingPage.petlandingpage');

    }



    public function groomingShops()

    {

        return view('shops.grooming');

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


