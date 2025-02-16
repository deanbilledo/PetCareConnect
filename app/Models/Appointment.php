<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'pet_id',
        'employee_id',
        'requested_employee_id',
        'service_type',
        'service_price',
        'appointment_date',
        'status',
        'notes',
        'cancellation_reason',
        'cancelled_by',
        'reschedule_reason',
        'last_reschedule_at',
        'reschedule_count',
        'payment_status',
        'paid_at',
        'accepted_at',
        'note_image',
        'requested_date',
        'reschedule_approved_at',
        'reschedule_rejected_at',
        'reschedule_rejection_reason',
        'service_id',
        'has_rating'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'service_price' => 'decimal:2',
        'accepted_at' => 'datetime',
        'requested_date' => 'datetime'
    ];

    protected $appends = ['duration'];

    public function getDurationAttribute()
    {
        // Default duration if no service is found
        return $this->service()->exists() ? $this->service->duration : 30;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'appointment_services')
                    ->withPivot('price')
                    ->withTimestamps();
    }
} 