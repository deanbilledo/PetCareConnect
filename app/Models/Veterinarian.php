<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veterinarian extends Model
{
    // Other model properties and methods

    // Define the relationship with ratings
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'veterinarian_id'); // Assuming 'veterinarian_id' is the foreign key
    }
}



