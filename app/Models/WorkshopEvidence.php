<?php

namespace App\Models;

use Database\Factories\WorkshopEvidenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopEvidence extends Model
{
    /** @use HasFactory<WorkshopEvidenceFactory> */
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'workshop_evidences';

    protected $fillable = [
        'participant_id',
        'week_no',
        'title',
        'note',
        'file_path',
        'link_url',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'week_no' => 'integer',
            'reviewed_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(WorkshopParticipant::class, 'participant_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'reviewed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Review',
        };
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
