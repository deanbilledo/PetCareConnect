<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        // Get user's ratings with shop info
        $ratings = $user->ratings()
            ->with('shop')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($rating) {
                return [
                    'shop_name' => $rating->shop->name,
                    'rating' => $rating->rating,
                    'date' => $rating->created_at->format('M d, Y')
                ];
            });

        // Get user's visits (completed appointments)
        $visits = $user->appointments()
            ->with('shop')
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'shop_name' => $appointment->shop->name,
                    'service_type' => $appointment->service_type,
                    'date' => $appointment->appointment_date->format('M d, Y')
                ];
            });

        return response()->json([
            'name' => $user->name,
            'profile_photo_url' => $user->profile_photo_url,
            'joined_date' => $user->created_at->format('F Y'),
            'ratings' => $ratings,
            'visits' => $visits
        ]);
    }
} 