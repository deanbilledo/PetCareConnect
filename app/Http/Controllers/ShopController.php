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



    /**

     * Search for shops near a given location

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function searchByLocation(Request $request)

    {

        try {

            $latitude = $request->input('latitude');

            $longitude = $request->input('longitude');

            $type = $request->input('type'); // optional filter by shop type

            $radius = $request->input('radius', 10); // km, default 10km

            

            // Validate inputs

            if (!$latitude || !$longitude) {

                return response()->json(['error' => 'Location is required'], 400);

            }

            

            // Earth radius in kilometers

            $earthRadius = 6371;

            

            // Haversine formula to calculate distance

            $shops = Shop::selectRaw("

                    id, name, type, phone, description, address, 

                    image, latitude, longitude, status,

                    ($earthRadius * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 

                    [$latitude, $longitude, $latitude])

                ->where('status', 'active')

                ->when($type, function($query, $type) {

                    return $query->where('type', $type);

                })

                ->having('distance', '<=', $radius)

                ->orderBy('distance')

                ->limit(15)

                ->get();

                

            return response()->json([

                'success' => true,

                'shops' => $shops,

            ]);

        } catch (\Exception $e) {

            Log::error('Error in searchByLocation: ' . $e->getMessage());

            Log::error($e->getTraceAsString());

            return response()->json(['error' => 'Failed to search shops by location'], 500);

        }

    }



    /**
     * Get all active shops for map display
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllShops()
    {
        try {
            $shops = Shop::select('id', 'name', 'type', 'phone', 'description', 'address', 
                               'image', 'latitude', 'longitude', 'status')
                ->where('status', 'active')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();
            
            return response()->json([
                'success' => true,
                'shops' => $shops
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllShops: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch shops'], 500);
        }
    }

} 


