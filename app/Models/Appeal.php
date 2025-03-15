<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Appeal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason',
        'evidence_path',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the appealed shop report.
     */
    public function shopReport()
    {
        return $this->belongsTo(ShopReport::class, 'appealable_id')
            ->where('appealable_type', ShopReport::class);
    }

    /**
     * Get the appealed user report.
     */
    public function userReport()
    {
        return $this->belongsTo(UserReport::class, 'appealable_id')
            ->where('appealable_type', UserReport::class);
    }

    /**
     * Get the parent appealable model (shop report or user report).
     */
    public function appealable()
    {
        return $this->morphTo();
    }

    /**
     * Get the URL for the evidence document.
     *
     * @return string|null
     */
    public function getEvidenceUrl()
    {
        if (!$this->evidence_path) {
            return null;
        }

        return Storage::url($this->evidence_path);
    }

    /**
     * Determine if the appeal has evidence.
     *
     * @return bool
     */
    public function hasEvidence()
    {
        return !is_null($this->evidence_path);
    }

    /**
     * Determine if the appeal is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Determine if the appeal is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Determine if the appeal is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
