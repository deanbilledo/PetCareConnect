<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    protected $fillable = [
        'pet_id',
        'record_type',
        'description',
        'date',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 