<?php

namespace App\Models;

use Database\Factories\ActivityFollowupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityFollowup extends Model
{
    /** @use HasFactory<ActivityFollowupFactory> */
    use HasFactory;

    protected $table = 'activity_followups';

    protected $fillable = [
        'activity_plan_id',
        'admin_id',
        'followup_note',
        'status',
    ];

    public function activityPlan(): BelongsTo
    {
        return $this->belongsTo(ActivityPlan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'admin_id');
    }
}

