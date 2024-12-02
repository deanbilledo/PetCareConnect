<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'category',
        'description',
        'pet_types',
        'size_ranges',
        'breed_specific',
        'special_requirements',
        'base_price',
        'duration',
        'variable_pricing',
        'add_ons',
        'status'
    ];

    protected $casts = [
        'pet_types' => 'array',
        'size_ranges' => 'array',
        'breed_specific' => 'boolean',
        'base_price' => 'decimal:2',
        'duration' => 'integer',
        'variable_pricing' => 'array',
        'add_ons' => 'array'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getPriceForSize($size)
    {
        if (empty($this->variable_pricing)) {
            return $this->base_price;
        }

        $size = strtolower($size);
        $pricing = collect($this->variable_pricing)->first(function($price) use ($size) {
            return strtolower($price['size']) === $size;
        });

        return $pricing ? (float) $pricing['price'] : $this->base_price;
    }
} 
