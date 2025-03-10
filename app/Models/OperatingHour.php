<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OperatingHour extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'operating_hours';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'day',
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
        'close_time' => 'datetime:H:i'
    ];

    /**
     * Get the shop that owns the operating hours.
     */
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

    public function getLunchStartAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function getLunchEndAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setLunchStartAttribute($value)
    {
        $this->attributes['lunch_start'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setLunchEndAttribute($value)
    {
        $this->attributes['lunch_end'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }
} 