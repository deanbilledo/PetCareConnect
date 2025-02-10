<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'employee_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationship with Shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relationship with User (who approved the request)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope for pending requests
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for approved requests
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope for rejected requests
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
} 