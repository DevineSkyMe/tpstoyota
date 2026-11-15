<?php

namespace App\Models;

use Database\Factories\BatchStatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchStat extends Model
{
    /** @use HasFactory<BatchStatFactory> */
    use HasFactory;

    protected $table = 'batch_stats';

    protected $fillable = [
        'batch_name',
        'participant_count',
        'kp3_count',
        'kp4_count',
        'kp3_percent',
        'kp4_percent',
    ];

    protected function casts(): array
    {
        return [
            'participant_count' => 'integer',
            'kp3_count' => 'integer',
            'kp4_count' => 'integer',
        ];
    }
}

