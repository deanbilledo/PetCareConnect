<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'is_open' => 'boolean'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // Custom accessors to ensure proper time format
    public function getOpenTimeAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function getCloseTimeAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    // Custom mutators to ensure proper time storage
    public function setOpenTimeAttribute($value)
    {
        $this->attributes['open_time'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setCloseTimeAttribute($value)
    {
        $this->attributes['close_time'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }
} 