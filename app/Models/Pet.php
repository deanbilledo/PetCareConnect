<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'species',
        'breed',
        'date_of_birth',
        'weight',
        'size_category',
        'color_markings',
        'coat_type',
        'profile_photo',
        'death_date',
        'death_reason',
        'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'datetime',
        'death_date' => 'datetime'
    ];

    protected $appends = [
        'profile_photo_url'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return asset('images/default-pet.png');
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
        return !is_null($this->death_date);
    }

    public function markAsDeceased($deathDate, $reason = null)
    {
        $this->update([
            'death_date' => $deathDate,
            'death_reason' => $reason,
        ]);
    }
} 