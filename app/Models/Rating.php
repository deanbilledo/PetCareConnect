<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'rating',
        'comment'
    ];

    protected $with = ['user'];

    protected $casts = [
        'rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'first_name' => 'Deleted',
            'last_name' => 'User',
            'profile_photo_path' => null
        ]);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 