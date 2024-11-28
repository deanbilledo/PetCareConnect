<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'day',
        'is_open',
        'open_time',
        'close_time'
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 