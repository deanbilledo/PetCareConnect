<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateProfilePhoto(Request $request)
    {
        try {
            $request->validate([
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $user = auth()->user();
            \Log::info('Updating profile photo for user: ' . $user->id);

            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Generate unique filename
                $fileName = time() . '.' . $request->profile_photo->extension();
                
                // Store with specific path
                $path = $request->profile_photo->storeAs(
                    'profile-photos',
                    $fileName,
                    'public'
                );

                \Log::info('New profile photo stored at: ' . $path);

                // Update user with new photo path
                $user->update([
                    'profile_photo_path' => $path
                ]);

                // Log the URL that will be used
                \Log::info('Profile photo URL will be: ' . url('storage/' . $path));

                return back()->with('success', 'Profile photo updated successfully');
            }

            return back()->with('error', 'No photo uploaded');
        } catch (\Exception $e) {
            \Log::error('Error updating profile photo: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Failed to update profile photo: ' . $e->getMessage());
        }
    }
} 