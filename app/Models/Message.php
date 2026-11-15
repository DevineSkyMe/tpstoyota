<?php

namespace App\Models;

use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'related_type',
        'related_id',
        'message',
        'attachment_path',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'related_id' => 'integer',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'receiver_id');
    }
}

