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

            ], [

                'rating.required' => 'Please select a rating.',

                'rating.min' => 'Please select a rating between 1 and 5 stars.',

                'rating.max' => 'Please select a rating between 1 and 5 stars.',

                'comment.required' => 'Please write a review comment.',

                'comment.min' => 'Your review must be at least 10 characters long.'

            ]);



            // Check if user has completed an appointment with this shop

            $hasCompletedAppointment = auth()->user()->appointments()

                ->where('shop_id', $shop->id)

                ->where('status', 'completed')

                ->exists();



            if (!$hasCompletedAppointment) {

                return back()->with('error', 'You can only review shops after completing an appointment.');

            }



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

                $message = 'Your review has been updated successfully!';

            } else {

                // Create new rating

                Rating::create([

                    'user_id' => auth()->id(),

                    'shop_id' => $shop->id,

                    'rating' => $request->rating,

                    'comment' => $request->comment

                ]);

                $message = 'Thank you for your review! It has been submitted successfully.';

            }



            // Update shop's average rating

            $shop->rating = $shop->ratings()->avg('rating');

            $shop->save();



            Log::info('Review submitted successfully', [

                'user_id' => auth()->id(),

                'shop_id' => $shop->id,

                'rating' => $request->rating

            ]);



            return back()->with('success', $message);

        } catch (\Exception $e) {

            Log::error('Error in submitReview: ' . $e->getMessage());

            Log::error($e->getTraceAsString());

            return back()

                ->with('error', 'There was an error submitting your review. Please try again.')

                ->withInput();

        }

    }

} 


