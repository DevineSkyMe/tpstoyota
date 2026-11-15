<?php

namespace App\Models;

use Database\Factories\ActivityProgressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityProgress extends Model
{
    /** @use HasFactory<ActivityProgressFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'activity_progress';

    protected $fillable = [
        'activity_plan_id',
        'submitted_by',
        'status_update',
        'description',
        'progress_pct',
        'approval_status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'progress_pct' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function activityPlan(): BelongsTo
    {
        return $this->belongsTo(ActivityPlan::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'reviewed_by');
    }

    public function getApprovalLabelAttribute(): string
    {
        return match ($this->approval_status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu ACC',
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset($this->image_path) : null;
    }

    public function isPending(): bool
    {
        return $this->approval_status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === self::STATUS_REJECTED;
    }
}
