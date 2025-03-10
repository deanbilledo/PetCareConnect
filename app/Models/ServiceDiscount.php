<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'discount_type',
        'discount_value',
        'voucher_code',
        'valid_from',
        'valid_until',
        'description',
        'is_active'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function calculateDiscountedPrice($originalPrice)
    {
        if ($this->discount_type === 'percentage') {
            return $originalPrice * (1 - $this->discount_value / 100);
        }
        return max(0, $originalPrice - $this->discount_value);
    }

    public function isValid()
    {
        $now = now();
        return $this->is_active && 
               $now->greaterThanOrEqualTo($this->valid_from) && 
               $now->lessThanOrEqualTo($this->valid_until);
    }
} 