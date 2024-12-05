<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'pet_id',
        'service_type',
        'service_price',
        'appointment_date',
        'status',
        'notes',
        'cancellation_reason',
        'accepted_at',
        'reschedule_reason'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'service_price' => 'decimal:2',
        'accepted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
} 