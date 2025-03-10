<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetVaccination extends Model
{
    protected $fillable = [
        'pet_id',
        'vaccine_name',
        'administered_by',
        'administered_date',
        'next_due_date',
    ];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
} 