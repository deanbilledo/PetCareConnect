<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopGallery extends Model
{
    protected $fillable = ['shop_id', 'path'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 