<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
        'has_lunch_break',
        'lunch_start',
        'lunch_end'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_open' => 'boolean',
        'has_lunch_break' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'lunch_start' => 'datetime:H:i',
        'lunch_end' => 'datetime:H:i'
    ];

    /**
     * Get the shop that owns the business hours.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 