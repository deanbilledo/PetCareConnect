<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'shop_id',
        'title',
        'start',
        'end',
        'type',
        'status',
        'notes'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    protected $attributes = [
        'type' => 'shift',
        'status' => 'active'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
} 