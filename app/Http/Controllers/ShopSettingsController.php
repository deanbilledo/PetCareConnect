<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\OperatingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ShopSettingsController extends Controller
{
    /**
     * Update the shop profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|string' // Base64 image will be handled separately
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $shop = auth()->user()->shop;
            
            // Handle logo upload if it's a new base64 image
            if ($request->filled('logo') && strpos($request->logo, 'data:image') === 0) {
                $image = $request->logo;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                
                $imageName = 'shop_' . $shop->id . '_' . time() . '.png';
                Storage::disk('public')->put('shop_logos/' . $imageName, base64_decode($image));
                
                // Delete old logo if exists
                if ($shop->logo_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $shop->logo_url));
                }
                
                $shop->logo_url = '/storage/shop_logos/' . $imageName;
            }

            $shop->update([
                'name' => $request->name,
                'description' => $request->description,
                'contact_email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    /**
     * Update the shop's operating hours.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHours(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hours' => 'required|array',
                'hours.*.day' => 'required|integer|between:0,6',
                'hours.*.is_open' => 'required|boolean',
                'hours.*.open_time' => 'nullable|required_if:hours.*.is_open,true|date_format:H:i:s',
                'hours.*.close_time' => 'nullable|required_if:hours.*.is_open,true|date_format:H:i:s|after:hours.*.open_time',
                'hours.*.has_lunch_break' => 'boolean',
                'hours.*.lunch_start' => 'nullable|required_if:hours.*.has_lunch_break,true|date_format:H:i:s',
                'hours.*.lunch_end' => 'nullable|required_if:hours.*.has_lunch_break,true|date_format:H:i:s|after:hours.*.lunch_start'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed: ' . json_encode($validator->errors()));
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $shop = auth()->user()->shop;
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Delete existing hours
                OperatingHour::where('shop_id', $shop->id)->delete();
                
                // Create new hours
                foreach ($request->hours as $hour) {
                    OperatingHour::create([
                        'shop_id' => $shop->id,
                        'day' => $hour['day'],
                        'is_open' => $hour['is_open'],
                        'open_time' => $hour['is_open'] ? $hour['open_time'] : null,
                        'close_time' => $hour['is_open'] ? $hour['close_time'] : null,
                        'has_lunch_break' => $hour['has_lunch_break'] ?? false,
                        'lunch_start' => ($hour['is_open'] && ($hour['has_lunch_break'] ?? false)) ? $hour['lunch_start'] : null,
                        'lunch_end' => ($hour['is_open'] && ($hour['has_lunch_break'] ?? false)) ? $hour['lunch_end'] : null
                    ]);
                }
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Operating hours updated successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error updating operating hours: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update operating hours. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the shop's notification preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'required|boolean',
            'sms_notifications' => 'required|boolean',
            'daily_summary' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $shop = auth()->user()->shop;
            
            $shop->update([
                'email_notifications' => $request->email_notifications,
                'sms_notifications' => $request->sms_notifications,
                'daily_summary' => $request->daily_summary
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification preferences'
            ], 500);
        }
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password'
            ], 500);
        }
    }
} 