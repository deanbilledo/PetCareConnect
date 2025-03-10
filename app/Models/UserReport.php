<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reporter_id',
        'user_id',
        'report_type',
        'description',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the reporter user.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the reported user.
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
