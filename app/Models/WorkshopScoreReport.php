<?php

namespace App\Models;

use Database\Factories\WorkshopScoreReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopScoreReport extends Model
{
    /** @use HasFactory<WorkshopScoreReportFactory> */
    use HasFactory;

    protected $table = 'workshop_score_reports';

    protected $fillable = [
        'workshop_id',
        'generated_by',
        'pdf_path',
        'generated_at',
        'sent_to_hrd_at',
        'sent_to_director_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'sent_to_hrd_at' => 'datetime',
            'sent_to_director_at' => 'datetime',
        ];
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'generated_by');
    }
}

