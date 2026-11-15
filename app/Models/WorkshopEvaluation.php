<?php

namespace App\Models;

use Database\Factories\WorkshopEvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopEvaluation extends Model
{
    /** @use HasFactory<WorkshopEvaluationFactory> */
    use HasFactory;

    protected $table = 'workshop_evaluations';

    public const PASSING_BENCHMARK = 80.0;

    protected $fillable = [
        'employee_id',
        'workshop_id',
        'participant_id',
        'ws_1',
        'ws_2',
        'ws_3',
        'ws_4',
        'ws_5',
        'ws_6',
        'ws_7',
        'final_decision',
        'notes',
        'evaluated_by',
        'evaluated_by_id',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'ws_1' => 'integer',
            'ws_2' => 'integer',
            'ws_3' => 'integer',
            'ws_4' => 'integer',
            'ws_5' => 'integer',
            'ws_6' => 'integer',
            'ws_7' => 'integer',
            'evaluated_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(WorkshopParticipant::class, 'participant_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'evaluated_by_id');
    }

    /**
     * Average of the seven 1-5 rubric criteria.
     */
    public function getAverageScoreAttribute(): float
    {
        $scores = array_map(fn ($i) => (int) $this->{"ws_{$i}"}, range(1, 7));

        return round(array_sum($scores) / 7, 2);
    }

    /**
     * Composite percentage used by the OMDD dashboard (0-100).
     */
    public function getCompositeScoreAttribute(): float
    {
        return round(($this->average_score / 5) * 100, 1);
    }

    public function getResultLabelAttribute(): string
    {
        return strtolower((string) $this->final_decision) === 'pass' ? 'PASS' : 'FAIL';
    }

    public function isPassed(): bool
    {
        return strtolower((string) $this->final_decision) === 'pass';
    }
}

