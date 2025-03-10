<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetParasiteControl extends Model
{
    protected $fillable = [
        'pet_id',
        'treatment_name',
        'treatment_type',
        'treatment_date',
        'next_treatment_date',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'next_treatment_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
} 