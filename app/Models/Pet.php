<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'species',
        'breed',
        'size_category',
        'weight',
        'color_markings',
        'coat_type',
        'date_of_birth',
        'status',
        'death_date',
        'death_reason',
        'profile_photo_path',
        'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'death_date' => 'date',
        'weight' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? Storage::disk('public')->url($this->profile_photo_path)
            : asset('images/default-pet.png');
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(PetVaccination::class);
    }

    public function parasiteControls(): HasMany
    {
        return $this->hasMany(PetParasiteControl::class);
    }

    public function healthIssues(): HasMany
    {
        return $this->hasMany(PetHealthIssue::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getSizeCategoryAttribute($value)
    {
        return strtolower($value);
    }

    public function isDeceased(): bool
    {
        return $this->status === 'deceased';
    }

    public function markAsDeceased($deathDate, $reason = null)
    {
        $this->update([
            'status' => 'deceased',
            'death_date' => $deathDate,
            'death_reason' => $reason,
        ]);
    }
} 