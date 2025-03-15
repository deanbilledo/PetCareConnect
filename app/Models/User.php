<?php

namespace App\Models;

use App\Traits\NotifiesWithEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory;
    use NotifiesWithEmail;
    
    // Only include the original trait but override its methods
    use Notifiable {
        notify as laravelNotify;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'profile_photo_path',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        try {
            // If no profile photo path is set, return default
            if (empty($this->profile_photo_path)) {
                return asset('images/default-profile.png');
            }

            // Check if profile photo starts with http or https (external URL like Facebook)
            if (str_starts_with($this->profile_photo_path, 'http://') || 
                str_starts_with($this->profile_photo_path, 'https://')) {
                return $this->profile_photo_path;
            }

            // If the file exists in storage, return its URL
            if (Storage::disk('public')->exists($this->profile_photo_path)) {
                return Storage::disk('public')->url($this->profile_photo_path);
            }

            // Log a warning if the file doesn't exist but should
            \Log::warning('Profile photo file does not exist', [
                'user_id' => $this->id,
                'photo_path' => $this->profile_photo_path
            ]);

            return asset('images/default-profile.png');
        } catch (\Exception $e) {
            \Log::error('Error in getProfilePhotoUrlAttribute: ' . $e->getMessage(), [
                'user_id' => $this->id,
                'photo_path' => $this->profile_photo_path,
                'exception' => $e
            ]);
            return asset('images/default-profile.png');
        }
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function employeeShop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function isShopOwner()
    {
        return $this->role === 'shop_owner';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteShops()
    {
        return $this->belongsToMany(Shop::class, 'favorites');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all notifications for the user.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Original notify method - renamed to createNotification
     * Create a new notification for the user.
     */
    public function createNotification($type, $title, $message, $actionUrl = null, $actionText = null, $icon = null)
    {
        return $this->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'icon' => $icon,
            'status' => 'unread'
        ]);
    }
    
    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance)
    {
        // For Laravel's built-in notifications (objects)
        if (is_object($instance)) {
            // Just use Laravel's built-in notification system
            return $this->laravelNotify($instance);
        }
        
        // For our custom notification system (strings and parameters)
        return $this->createNotification(...func_get_args());
    }
    
    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @param  \Illuminate\Notifications\Notification|null  $notification
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        if ($driver === 'mail' || $driver === 'testmail') {
            return $this->email;
        }
        
        if ($driver === 'database') {
            return $this->notifications();
        }
        
        $method = 'routeNotificationFor'.ucfirst($driver);
        
        if (method_exists($this, $method)) {
            return $this->$method($notification);
        }
        
        return null;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }
    
    /**
     * Route notifications for the database channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForDatabase($notification)
    {
        return $this->notifications();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications()->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }
}