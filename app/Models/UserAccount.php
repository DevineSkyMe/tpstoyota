<?php

namespace App\Models;

use Database\Factories\UserAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserAccount extends Authenticatable
{
    /** @use HasFactory<UserAccountFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_HRD = 'hrd';
    public const ROLE_OMDD = 'omdd';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_KARYAWAN = 'karyawan';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_HRD,
        self::ROLE_OMDD,
        self::ROLE_MANAGER,
        self::ROLE_KARYAWAN,
    ];

    protected $table = 'user_accounts';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'employee_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isHrd(): bool
    {
        return $this->role === self::ROLE_HRD;
    }

    public function isOmdd(): bool
    {
        return $this->role === self::ROLE_OMDD;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isKaryawan(): bool
    {
        return $this->role === self::ROLE_KARYAWAN;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->employee?->name ?: $this->username;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_HRD => 'HRD',
            self::ROLE_OMDD => 'OMDD Assessor',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_KARYAWAN => 'Karyawan',
            default => ucfirst((string) $this->role),
        };
    }

    public function activityFollowups(): HasMany
    {
        return $this->hasMany(ActivityFollowup::class, 'admin_id');
    }

    public function createdWorkshops(): HasMany
    {
        return $this->hasMany(Workshop::class, 'created_by');
    }

    public function recommendedCandidates(): HasMany
    {
        return $this->hasMany(WorkshopCandidate::class, 'recommended_by');
    }

    public function hrdReviews(): HasMany
    {
        return $this->hasMany(WorkshopHrdReview::class, 'hrd_id');
    }

    public function confirmedParticipants(): HasMany
    {
        return $this->hasMany(WorkshopParticipant::class, 'confirmed_by');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(WorkshopEvaluation::class, 'evaluated_by_id');
    }

    public function reviewedEvidences(): HasMany
    {
        return $this->hasMany(WorkshopEvidence::class, 'reviewed_by');
    }

    public function generatedScoreReports(): HasMany
    {
        return $this->hasMany(WorkshopScoreReport::class, 'generated_by');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
