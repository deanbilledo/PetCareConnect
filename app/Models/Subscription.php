<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'status',
        'trial_starts_at',
        'trial_ends_at',
        'subscription_ends_at',
        'subscription_starts_at',
        'amount',
        'reference_number',
        'payment_screenshot',
        'payment_status',
    ];

    protected $casts = [
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'subscription_starts_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 