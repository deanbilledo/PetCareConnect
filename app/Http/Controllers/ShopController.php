<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Rating;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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



    /**

     * Check if a shop is currently open based on its operating hours

     *

     * @param Shop $shop

     * @return bool

     */

    public function isShopOpen(Shop $shop)

    {

        try {

            // Get current day and time

            $now = Carbon::now();

            $currentDayOfWeek = $now->dayOfWeek; // 0 (Sunday) through 6 (Saturday)

            

            // Find the operating hours for today

            $todayHours = $shop->operatingHours()

                            ->where('day', $currentDayOfWeek)

                            ->first();

            

            // If no hours set for today or shop is marked as closed today

            if (!$todayHours || !$todayHours->is_open) {

                return false;

            }

            

            // Convert times to Carbon instances for comparison

            $openTime = Carbon::createFromTimeString($todayHours->open_time);

            $closeTime = Carbon::createFromTimeString($todayHours->close_time);

            

            // Check if current time is within operating hours

            $currentTimeOfDay = Carbon::createFromTimeString($now->format('H:i:s'));

            

            // If current time is before opening time or after closing time

            if ($currentTimeOfDay->lt($openTime) || $currentTimeOfDay->gt($closeTime)) {

                return false;

            }

            

            // Check if it's during lunch break

            if ($todayHours->has_lunch_break) {

                $lunchStart = Carbon::createFromTimeString($todayHours->lunch_start);

                $lunchEnd = Carbon::createFromTimeString($todayHours->lunch_end);

                

                // If current time is during lunch break

                if ($currentTimeOfDay->gte($lunchStart) && $currentTimeOfDay->lte($lunchEnd)) {

                    return false;

                }

            }

            

            // If we reached here, the shop is open

            return true;

        } catch (\Exception $e) {

            Log::error('Error checking if shop is open: ' . $e->getMessage());

            // Default to closed in case of any errors

            return false;

        }

    }

    

    /**

     * Get shop details with dynamic open status

     *

     * @param Shop $shop

     * @return Shop

     */

    public function getShopWithOpenStatus(Shop $shop)

    {

        // Add dynamic is_open property based on operating hours

        $shop->setAttribute('is_open', $this->isShopOpen($shop));

        return $shop;

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

     * Search for shops and services near a given location

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

            $query = $request->input('query'); // text search query

            $includeServices = $request->input('include_services', true); // include services in results

            

            // Start building the shop query

            $shopsQuery = Shop::query()->where('status', 'active');

            

            // Apply text search if query is provided

            if ($query) {

                $shopsQuery->where(function($q) use ($query) {

                    $q->where('name', 'like', "%{$query}%")

                      ->orWhere('description', 'like', "%{$query}%")

                      ->orWhere('address', 'like', "%{$query}%");

                });

            }

            

            // Apply type filter if provided

            if ($type) {

                $shopsQuery->where('type', $type);

            }

            

            // If location is provided, calculate distance

            if ($latitude && $longitude) {

                // Earth radius in kilometers

                $earthRadius = 6371;

                

                // Haversine formula to calculate distance

                $shopsQuery->selectRaw("

                        id, name, type, phone, description, address, 

                        image, latitude, longitude, status, rating,

                        ($earthRadius * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 

                        [$latitude, $longitude, $latitude])

                    ->whereNotNull('latitude')

                    ->whereNotNull('longitude')

                    ->having('distance', '<=', $radius)

                    ->orderBy('distance');

            } else {

                // If no location, just select the fields without distance

                $shopsQuery->select('id', 'name', 'type', 'phone', 'description', 'address', 

                               'image', 'latitude', 'longitude', 'status', 'rating')

                     ->orderBy('rating', 'desc');

            }

            

            // Get the shop results (limited to 10 if including services)

            $limit = $includeServices ? 10 : 20;

            $shops = $shopsQuery->limit($limit)->get();

            

            // Add dynamic is_open status and result_type field

            $shops->transform(function($shop) {

                $shop->is_open = $this->isShopOpen($shop);

                $shop->result_type = 'shop';

                return $shop;

            });

            

            // Prepare the final results array

            $results = $shops->toArray();

            

            // If we should include services and have a search query

            if ($includeServices && $query) {

                // Get shop IDs to limit service search to these shops

                $shopIds = $shops->pluck('id')->toArray();

                

                // Create a service query that matches the search term

                $servicesQuery = \App\Models\Service::query()

                    ->where('status', 'active')

                    ->where(function($q) use ($query) {

                        $q->where('name', 'like', "%{$query}%")

                          ->orWhere('description', 'like', "%{$query}%")

                          ->orWhere('category', 'like', "%{$query}%");

                    });

                

                // If we have shops nearby, prioritize their services

                if (!empty($shopIds)) {

                    $servicesQuery->whereIn('shop_id', $shopIds);

                }

                

                // Get services with their shop data

                $services = $servicesQuery->with('shop')

                    ->orderBy('name')

                    ->limit(10)

                    ->get();

                

                // Transform services to include necessary data

                $serviceResults = $services->map(function($service) use ($latitude, $longitude) {

                    // Calculate distance if we have coordinates

                    $distance = null;

                    if ($latitude && $longitude && $service->shop->latitude && $service->shop->longitude) {

                        $earthRadius = 6371;

                        $latFrom = deg2rad($latitude);

                        $lonFrom = deg2rad($longitude);

                        $latTo = deg2rad($service->shop->latitude);

                        $lonTo = deg2rad($service->shop->longitude);

                        

                        $latDelta = $latTo - $latFrom;

                        $lonDelta = $lonTo - $lonFrom;

                        

                        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + 

                            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                        $distance = $angle * $earthRadius;

                    }

                    

                    // Add dynamic is_open property

                    $isOpen = $this->isShopOpen($service->shop);

                    

                    return [

                        'id' => $service->id,

                        'shop_id' => $service->shop_id,

                        'name' => $service->name,

                        'description' => $service->description,

                        'category' => $service->category,

                        'base_price' => $service->base_price,

                        'duration' => $service->duration,

                        'result_type' => 'service',

                        'shop_name' => $service->shop->name,

                        'shop_type' => $service->shop->type,

                        'shop_address' => $service->shop->address,

                        'shop_image' => $service->shop->image,

                        'latitude' => $service->shop->latitude,

                        'longitude' => $service->shop->longitude,

                        'distance' => $distance,

                        'is_open' => $isOpen

                    ];

                });

                

                // Add services to results

                $results = array_merge($results, $serviceResults->toArray());

                

                // Sort by distance if available

                if ($latitude && $longitude) {

                    usort($results, function($a, $b) {

                        $distA = $a['distance'] ?? PHP_FLOAT_MAX;

                        $distB = $b['distance'] ?? PHP_FLOAT_MAX;

                        return $distA <=> $distB;

                    });

                }

            }

            

            return response()->json([

                'success' => true,

                'shops' => $results,

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllShops(Request $request)
    {
        try {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            
            // Start building the shop query
            $shopsQuery = Shop::query()
                ->where('status', 'active')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude');
            
            // If location is provided, calculate distance
            if ($latitude && $longitude) {
                // Earth radius in kilometers
                $earthRadius = 6371;
                
                // Haversine formula to calculate distance
                $shopsQuery->selectRaw("
                        id, name, type, phone, description, address, 
                        image, latitude, longitude, status, rating,
                        ($earthRadius * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 
                        [$latitude, $longitude, $latitude])
                    ->orderBy('distance');
            } else {
                // If no location, just select the fields without distance
                $shopsQuery->select('id', 'name', 'type', 'phone', 'description', 'address', 
                               'image', 'latitude', 'longitude', 'status', 'rating')
                     ->orderBy('rating', 'desc');
            }
            
            $shops = $shopsQuery->get();
            
            // Add dynamic open status
            $shops->transform(function($shop) {
                $shop->is_open = $this->isShopOpen($shop);
                return $shop;
            });
            
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


