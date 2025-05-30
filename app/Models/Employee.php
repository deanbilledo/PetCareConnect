<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name',
        'email',
        'phone',
        'position',
        'employment_type',
        'profile_photo',
        'bio',
        'is_active',
        'status'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'employee_service')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function timeOffRequests()
    {
        return $this->hasMany(TimeOffRequest::class);
    }

    public function staffRatings()
    {
        return $this->hasMany(StaffRating::class);
    }

    public function availability()
    {
        return $this->hasMany(EmployeeAvailability::class);
    }
}
