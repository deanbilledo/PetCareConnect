<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class AppointmentNote extends Model
{
    protected $fillable = [
        'appointment_id',
        'note',
        'image'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($note) {
            Log::info('New appointment note created:', [
                'note_id' => $note->id,
                'appointment_id' => $note->appointment_id,
                'has_image' => !empty($note->image)
            ]);
        });
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
} 