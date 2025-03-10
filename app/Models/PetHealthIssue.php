<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetHealthIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'issue_title',
        'identified_date',
        'description',
        'treatment',
        'vet_notes',
        'is_resolved',
        'resolved_date'
    ];

    protected $casts = [
        'identified_date' => 'datetime',
        'resolved_date' => 'datetime',
        'is_resolved' => 'boolean'
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
} 