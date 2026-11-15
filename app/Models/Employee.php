<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'birth_date',
        'position',
        'photo',
        'certificate',
        'current_tps_level',
        'previous_tps_level',
        'tahun_lulus_terakhir',
        'tahun_lulus_saat_ini',
        'last_activity_theme',
        'last_activity_type',
        'batch',
        'registration_year',
        'retirement_year',
        'process_stage',
        'status',
        'plant_id',
        'division_id',
        'department_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registration_year' => 'integer',
            'retirement_year' => 'integer',
        ];
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function userAccount(): HasOne
    {
        return $this->hasOne(UserAccount::class);
    }

    public function activityPlans(): HasMany
    {
        return $this->hasMany(ActivityPlan::class);
    }

    public function workshopCandidates(): HasMany
    {
        return $this->hasMany(WorkshopCandidate::class);
    }

    public function workshopParticipants(): HasMany
    {
        return $this->hasMany(WorkshopParticipant::class);
    }

    public function workshopEvaluations(): HasMany
    {
        return $this->hasMany(WorkshopEvaluation::class);
    }

    /**
     * Canonical TPS level ladder from lowest to highest.
     */
    public const TPS_LEVELS = ['Advance', 'KP1', 'KP2', 'KP3', 'KP4'];

    /**
     * OMDD rubric criteria (columns ws_1..ws_7) with human readable labels.
     */
    public const EVALUATION_CRITERIA = [
        'ws_1' => 'Problem Identification',
        'ws_2' => 'Root Cause Analysis',
        'ws_3' => 'Countermeasure & Yokoten',
        'ws_4' => 'Presentation & Coaching',
        'ws_5' => 'Genba & Safety',
        'ws_6' => 'Standardization',
        'ws_7' => 'Team Leadership',
    ];

    /**
     * Normalize any legacy level spelling to its canonical form.
     */
    public function normalizedTpsLevel(): ?string
    {
        $current = strtoupper(trim((string) $this->current_tps_level));
        $current = str_replace(['TPS ', ' '], '', $current);

        return match ($current) {
            'ADVANCE' => 'Advance',
            'KP1' => 'KP1',
            'KP2' => 'KP2',
            'KP3' => 'KP3',
            'KP4' => 'KP4',
            default => null,
        };
    }

    /**
     * Get the next promotional TPS Level:
     * Advance -> KP1 -> KP2 -> KP3 -> KP4.
     */
    public function getNextTpsLevel(): ?string
    {
        $current = $this->normalizedTpsLevel();

        if ($current === null) {
            return 'Advance';
        }

        $index = array_search($current, self::TPS_LEVELS, true);

        return self::TPS_LEVELS[$index + 1] ?? null; // Already at maximum tier (KP4).
    }

    /**
     * Check if employee is eligible for promotion.
     */
    public function canPromote(): bool
    {
        return $this->getNextTpsLevel() !== null;
    }

    /**
     * Promote employee to next TPS level when passing assessment.
     */
    public function promoteToNextLevel(): bool
    {
        $nextLevel = $this->getNextTpsLevel();
        if (!$nextLevel) {
            return false;
        }

        $this->previous_tps_level = $this->current_tps_level;
        $this->current_tps_level = $nextLevel;
        $this->tahun_lulus_saat_ini = (int) date('Y');
        return $this->save();
    }

    /**
     * Build the spider/radar chart payload: one polygon per workshop batch
     * with the employee's score on each of the seven OMDD criteria.
     *
     * @return array{labels: array<int, string>, datasets: array<int, array{label: string, data: array<int, int>}>}
     */
    public function radarChartData(): array
    {
        $criteriaKeys = array_keys(self::EVALUATION_CRITERIA);

        $evaluations = $this->workshopEvaluations()
            ->with('workshop')
            ->get()
            ->sortBy(function (WorkshopEvaluation $evaluation) {
                $workshop = $evaluation->workshop;

                return $workshop ? ($workshop->year * 100 + $workshop->batch) : 0;
            })
            ->values();

        $datasets = [];
        foreach ($evaluations as $evaluation) {
            $workshop = $evaluation->workshop;

            $datasets[] = [
                'label' => $workshop
                    ? "Batch {$workshop->batch} ({$workshop->year})"
                    : 'Workshop',
                'data' => array_map(fn (string $key) => (int) $evaluation->{$key}, $criteriaKeys),
            ];
        }

        return [
            'labels' => array_values(self::EVALUATION_CRITERIA),
            'datasets' => $datasets,
        ];
    }
}

