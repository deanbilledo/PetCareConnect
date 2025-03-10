<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopGallery extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_gallery';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'image_path',
        'display_order'
    ];

    /**
     * Get the shop that owns the gallery image.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 