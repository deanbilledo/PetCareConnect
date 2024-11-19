<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'phone',
        'description',
        'address',
        'latitude',
        'longitude',
        'image',
        'tin',
        'vat_status',
        'bir_certificate',
        'rating',
        'terms_accepted'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:1',
        'terms_accepted' => 'boolean'
    ];

    protected $appends = ['ratings_avg_rating', 'ratings_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class)->with('user')->orderBy('created_at', 'desc');
    }

    public function getRatingsAvgRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }

    public function getIsOpenAttribute()
    {
        return true;
    }
} 