<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'category',
        'description',
        'pet_types',
        'size_ranges',
        'exotic_pet_service',
        'exotic_pet_species',
        'special_requirements',
        'base_price',
        'duration',
        'variable_pricing',
        'add_ons',
        'status'
    ];

    protected $casts = [
        'pet_types' => 'array',
        'size_ranges' => 'array',
        'exotic_pet_species' => 'array',
        'variable_pricing' => 'array',
        'add_ons' => 'array',
        'exotic_pet_service' => 'boolean',
        'base_price' => 'decimal:2'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getPriceForSize($size)
    {
        if (empty($this->variable_pricing)) {
            return $this->base_price;
        }

        $pricing = collect($this->variable_pricing)
            ->firstWhere('size', strtolower($size));

        return $pricing ? (float) $pricing['price'] : $this->base_price;
    }

    public function getVariablePricingAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getAddOnsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getPetTypesAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        return json_decode($value ?? '[]', true);
    }

    public function getSizeRangesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getExoticPetSpeciesAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        return json_decode($value ?? '[]', true);
    }

    public function isAvailableForPet(Pet $pet)
    {
        // Ensure we're working with arrays
        $petTypes = is_array($this->pet_types) ? $this->pet_types : json_decode($this->pet_types ?? '[]', true);
        $exoticSpecies = is_array($this->exotic_pet_species) ? $this->exotic_pet_species : json_decode($this->exotic_pet_species ?? '[]', true);

        // Convert pet types to lowercase for comparison
        $petTypes = array_map(function($type) {
            return Str::lower(trim($type));
        }, $petTypes);

        // Get the pet type in lowercase
        $petType = Str::lower(trim($pet->type));
        
        // Get both singular and plural forms
        $singularType = rtrim($petType, 's'); // Remove 's' if present (e.g., "dogs" -> "dog")
        $pluralType = $petType . 's'; // Add 's' if not present (e.g., "dog" -> "dogs")

        // Debug log
        Log::info('Service availability check:', [
            'service_id' => $this->id,
            'service_name' => $this->name,
            'pet_type' => $petType,
            'singular_type' => $singularType,
            'plural_type' => $pluralType,
            'pet_species' => $pet->species,
            'service_pet_types' => $petTypes,
            'service_exotic' => $this->exotic_pet_service,
            'service_exotic_species' => $exoticSpecies
        ]);

        // For exotic pets
        if ($petType === 'exotic') {
            $isAvailable = $this->exotic_pet_service && 
                          in_array($pet->species, $exoticSpecies);
            
            Log::info('Exotic pet availability result:', [
                'is_available' => $isAvailable,
                'service_name' => $this->name,
                'pet_species' => $pet->species,
                'allowed_species' => $exoticSpecies
            ]);
            
            return $isAvailable;
        }

        // For regular pets - check both singular and plural forms
        $isAvailable = in_array($petType, $petTypes) || 
                      in_array($singularType, $petTypes) || 
                      in_array($pluralType, $petTypes);

        Log::info('Regular pet availability result:', [
            'is_available' => $isAvailable,
            'service_name' => $this->name,
            'pet_type' => $petType,
            'service_pet_types' => $petTypes,
            'matched_type' => $isAvailable ? 
                (in_array($petType, $petTypes) ? $petType : 
                 (in_array($singularType, $petTypes) ? $singularType : $pluralType)) 
                : null
        ]);
        
        return $isAvailable;
    }

    public function getFormattedPetTypesAttribute()
    {
        $types = $this->pet_types ?? [];
        if ($this->exotic_pet_service) {
            $types = array_filter($types, fn($type) => $type !== 'Exotic');
            $exoticTypes = $this->exotic_pet_species ?? [];
            if (!empty($exoticTypes)) {
                $types[] = 'Exotic (' . implode(', ', $exoticTypes) . ')';
            } else {
                $types[] = 'Exotic';
            }
        }
        return $types;
    }
} 
