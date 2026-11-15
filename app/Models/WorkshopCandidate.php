<?php

namespace App\Models;

use Database\Factories\WorkshopCandidateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkshopCandidate extends Model
{
    /** @use HasFactory<WorkshopCandidateFactory> */
    use HasFactory;

    protected $table = 'workshop_candidates';

    protected $fillable = [
        'employee_id',
        'recommended_by',
        'target_tps_level',
        'tlc_reason',
        'tlc_status',
        'tlc_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'tlc_reviewed_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'recommended_by');
    }

    public function hrdReview(): HasOne
    {
        return $this->hasOne(WorkshopHrdReview::class, 'candidate_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(WorkshopParticipant::class, 'candidate_id');
    }
}

