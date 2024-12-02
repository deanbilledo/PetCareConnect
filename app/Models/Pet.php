<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'breed',
        'size_category',
        'weight',
        'color_markings',
        'coat_type',
        'date_of_birth',
        'profile_photo_path',
        'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'weight' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? Storage::disk('public')->url($this->profile_photo_path)
            : asset('images/default-pet.png');
    }
} 