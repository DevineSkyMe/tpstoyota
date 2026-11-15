<?php

namespace App\Models;

use Database\Factories\WorkshopParticipantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkshopParticipant extends Model
{
    /** @use HasFactory<WorkshopParticipantFactory> */
    use HasFactory;

    protected $table = 'workshop_participants';

    protected $fillable = [
        'workshop_id',
        'employee_id',
        'candidate_id',
        'confirmed_by',
        'confirmation_status',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(WorkshopCandidate::class, 'candidate_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'confirmed_by');
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(WorkshopEvaluation::class, 'participant_id');
    }

    /**
     * Weekly attendance evidence submitted by this participant.
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(WorkshopEvidence::class, 'participant_id');
    }
}

