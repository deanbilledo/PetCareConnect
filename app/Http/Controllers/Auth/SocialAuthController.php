<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes(['email', 'public_profile'])
            ->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->user();
            
            // Debug info - log the entire user object
            \Log::info('Facebook login user data', [
                'id' => $socialUser->getId(),
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                'token' => $socialUser->token,
                'has_avatar' => !empty($socialUser->getAvatar()),
                'raw_attributes' => $socialUser->getRaw()
            ]);
            
            // Check if user exists with this email
            $user = User::where('email', $socialUser->getEmail())->first();
            
            // Get profile picture URL from Facebook - with high resolution
            $profilePicUrl = $this->getFacebookProfilePicture($socialUser);
            
            if (!$user) {
                // Create new user
                $names = explode(' ', $socialUser->getName());
                $firstName = $names[0] ?? '';
                $lastName = $names[1] ?? '';
                
                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'customer',
                    'status' => 'active',
                ]);
            }
            
            // Download and save profile picture if available
            if ($profilePicUrl) {
                try {
                    // Make the directory if it doesn't exist
                    $directory = 'public/profile-photos';
                    if (!file_exists(storage_path('app/' . $directory))) {
                        mkdir(storage_path('app/' . $directory), 0755, true);
                    }
                    
                    // Generate a unique filename
                    $filename = 'fb_' . $user->id . '_' . time() . '.jpg';
                    
                    // Get image content from Facebook
                    $imageContent = file_get_contents($profilePicUrl);
                    
                    if ($imageContent === false) {
                        throw new \Exception("Failed to download profile image from URL: {$profilePicUrl}");
                    }
                    
                    // Save image to storage
                    $path = $directory . '/' . $filename;
                    $result = \Storage::put($path, $imageContent);
                    
                    if (!$result) {
                        throw new \Exception("Failed to save profile image to path: {$path}");
                    }
                    
                    // Update user profile photo path
                    $user->profile_photo_path = str_replace('public/', '', $path);
                    $user->save();
                    
                    \Log::info('Facebook profile picture saved', [
                        'user_id' => $user->id, 
                        'path' => $user->profile_photo_path,
                        'url' => $profilePicUrl,
                        'image_size' => strlen($imageContent)
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error saving Facebook profile picture: ' . $e->getMessage(), [
                        'url' => $profilePicUrl,
                        'user_id' => $user->id,
                        'exception' => $e
                    ]);
                }
            } else {
                \Log::warning('No Facebook profile picture URL available', [
                    'user_id' => $user->id,
                    'facebook_id' => $socialUser->getId()
                ]);
            }
            
            // Log the user in
            Auth::login($user);
            
            return redirect()->intended('/');
            
        } catch (Exception $e) {
            \Log::error('Facebook login error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')
                ->with('error', 'Facebook login failed. Please try again later.');
        }
    }
    
    /**
     * Get a high-resolution profile picture URL from Facebook
     * 
     * @param $socialUser
     * @return string|null
     */
    private function getFacebookProfilePicture($socialUser)
    {
        try {
            // First try to get a higher resolution picture from the raw data
            $rawUser = $socialUser->getRaw();
            
            // Check if we have picture data in the raw response
            if (isset($rawUser['picture']['data']['url']) && !empty($rawUser['picture']['data']['url'])) {
                return $rawUser['picture']['data']['url'];
            }
            
            // If not available in raw data, try the default avatar
            $avatar = $socialUser->getAvatar();
            
            // If default avatar exists, try to request a larger size by modifying the URL
            if ($avatar) {
                // Facebook avatar URLs often include size parameters - try to get a larger size
                // Example: https://graph.facebook.com/v3.3/123456789/picture?type=normal
                // Change to: https://graph.facebook.com/v3.3/123456789/picture?type=large
                $avatar = str_replace('type=normal', 'type=large', $avatar);
                
                // If it doesn't have the type parameter, add it
                if (strpos($avatar, 'type=') === false) {
                    $avatar .= (strpos($avatar, '?') === false ? '?' : '&') . 'type=large';
                }
                
                // You can also try to get a specific width/height
                // $avatar .= (strpos($avatar, '?') === false ? '?' : '&') . 'width=500&height=500';
                
                return $avatar;
            }
            
            // If nothing else works, try constructing a URL directly
            if ($socialUser->getId()) {
                return 'https://graph.facebook.com/' . $socialUser->getId() . '/picture?type=large';
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::warning('Error getting Facebook profile picture URL: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            // Return basic avatar as fallback
            return $socialUser->getAvatar();
        }
    }
} 