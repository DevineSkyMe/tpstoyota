<?php

namespace App\Models;

use Database\Factories\WorkshopFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Workshop extends Model
{
    /** @use HasFactory<WorkshopFactory> */
    use HasFactory;

    protected $fillable = [
        'year',
        'batch',
        'title',
        'location',
        'quota',
        'status',
        'created_by',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'batch' => 'integer',
            'quota' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(WorkshopParticipant::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(WorkshopEvaluation::class);
    }

    public function scoreReports(): HasMany
    {
        return $this->hasMany(WorkshopScoreReport::class);
    }

    /**
     * Weekly attendance evidence of every participant in this workshop.
     */
    public function evidences(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkshopEvidence::class,
            WorkshopParticipant::class,
            'workshop_id',
            'participant_id'
        );
    }
}

