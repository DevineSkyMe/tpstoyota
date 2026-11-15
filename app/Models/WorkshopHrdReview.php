<?php

namespace App\Models;

use Database\Factories\WorkshopHrdReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopHrdReview extends Model
{
    /** @use HasFactory<WorkshopHrdReviewFactory> */
    use HasFactory;

    protected $table = 'workshop_hrd_reviews';

    protected $fillable = [
        'candidate_id',
        'hrd_id',
        'hrd_status',
        'hrd_note',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(WorkshopCandidate::class, 'candidate_id');
    }

    public function hrd(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'hrd_id');
    }
}

