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
} 