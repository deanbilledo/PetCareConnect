<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'appointment_id',
        'rating',
        'review'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
} 