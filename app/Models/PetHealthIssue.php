<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetHealthIssue extends Model
{
    protected $fillable = [
        'pet_id',
        'issue_title',
        'identified_date',
        'description',
        'treatment',
        'vet_notes',
    ];

    protected $casts = [
        'identified_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
} 