<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ShopReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'shop_id',
        'report_type',
        'description',
        'status',
        'admin_notes',
        'resolved_at',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shop that is being reported.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the URL for the evidence image.
     *
     * @return string|null
     */
    public function getImageUrl()
    {
        if (!$this->image_path) {
            return null;
        }
        
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the appeal for this report.
     */
    public function appeal(): MorphOne
    {
        return $this->morphOne(Appeal::class, 'appealable');
    }
}
