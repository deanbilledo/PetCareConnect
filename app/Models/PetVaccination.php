<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetVaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'vaccine_name',
        'veterinarian',
        'date',
        'next_due_date'
    ];

    protected $casts = [
        'date' => 'date',
        'next_due_date' => 'date'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 