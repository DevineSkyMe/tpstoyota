<?php

namespace App\Models;

use Database\Factories\ActivityPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityPlan extends Model
{
    /** @use HasFactory<ActivityPlanFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'planned_start',
        'planned_end',
        'actual_start',
        'actual_end',
        'status',
        'progress_pct',
        'pic',
        'notes',
        'batch',
        'image_path',
        'employee_id',
    ];

    protected function casts(): array
    {
        return [
            'planned_start' => 'date',
            'planned_end' => 'date',
            'actual_start' => 'date',
            'actual_end' => 'date',
            'progress_pct' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ActivityProgress::class);
    }

    public function pendingProgressUpdates(): HasMany
    {
        return $this->hasMany(ActivityProgress::class)
            ->where('approval_status', ActivityProgress::STATUS_PENDING);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(ActivityFollowup::class);
    }
}

