<?php

namespace App\Domains\Koperasi\Models;
use App\Shared\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatSession extends Model
{
    use HasFactory;

    // Status constants for wizard flow
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_CONFIGURED = 'CONFIGURED';
    const STATUS_MEMBERS_LOCKED = 'MEMBERS_LOCKED';
    const STATUS_FINALIZED = 'FINALIZED';
    const STATUS_DISBURSING = 'DISBURSING';
    const STATUS_COMPLETED = 'COMPLETED';

    const VALID_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_CONFIGURED,
        self::STATUS_MEMBERS_LOCKED,
        self::STATUS_FINALIZED,
        self::STATUS_DISBURSING,
        self::STATUS_COMPLETED,
    ];

    // Allowed status transitions
    const STATUS_TRANSITIONS = [
        self::STATUS_DRAFT => [self::STATUS_CONFIGURED],
        self::STATUS_CONFIGURED => [self::STATUS_DRAFT, self::STATUS_MEMBERS_LOCKED],
        self::STATUS_MEMBERS_LOCKED => [self::STATUS_CONFIGURED, self::STATUS_FINALIZED],
        self::STATUS_FINALIZED => [self::STATUS_MEMBERS_LOCKED, self::STATUS_DISBURSING],
        self::STATUS_DISBURSING => [self::STATUS_FINALIZED, self::STATUS_COMPLETED],
        self::STATUS_COMPLETED => [self::STATUS_DISBURSING],
    ];

    protected $fillable = [
        'year',
        'event_date',
        'title',
        'total_net_profit',
        'member_allocation_percentage',
        'total_member_shu',
        'total_simpanan_wajib_snapshot',
        'cadangan_percentage',
        'jasa_simpanan_percentage',
        'jasa_usaha_percentage',
        'pengurus_percentage',
        'dana_sosial_percentage',
        'status',
        'notes',
        'created_by',
        'finalized_by',
        'finalized_at',
        'join_date_cutoff',
        'excluded_member_ids',
        'included_member_ids',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_net_profit' => 'decimal:2',
        'member_allocation_percentage' => 'decimal:2',
        'total_member_shu' => 'decimal:2',
        'total_simpanan_wajib_snapshot' => 'decimal:2',
        'cadangan_percentage' => 'decimal:2',
        'jasa_simpanan_percentage' => 'decimal:2',
        'jasa_usaha_percentage' => 'decimal:2',
        'pengurus_percentage' => 'decimal:2',
        'dana_sosial_percentage' => 'decimal:2',
        'join_date_cutoff' => 'date',
        'finalized_at' => 'datetime',
        'excluded_member_ids' => 'array',
        'included_member_ids' => 'array',
    ];

    public function distributions()
    {
        return $this->hasMany(MemberShuDistribution::class, 'rat_session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    /**
     * Check if transition to target status is allowed.
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        $allowed = self::STATUS_TRANSITIONS[$this->status] ?? [];
        return in_array($targetStatus, $allowed);
    }

    /**
     * Transition to a new status with validation.
     */
    public function transitionTo(string $targetStatus): bool
    {
        if (!$this->canTransitionTo($targetStatus)) {
            return false;
        }

        $updates = ['status' => $targetStatus];

        if ($targetStatus === self::STATUS_FINALIZED) {
            $updates['finalized_by'] = auth()->id();
            $updates['finalized_at'] = now();
        }

        $this->update($updates);
        return true;
    }

    /**
     * Check if session is in an editable state (not yet finalized).
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_CONFIGURED,
            self::STATUS_MEMBERS_LOCKED,
        ]);
    }

    /**
     * Check if session has been finalized or beyond.
     */
    public function isFinalized(): bool
    {
        return in_array($this->status, [
            self::STATUS_FINALIZED,
            self::STATUS_DISBURSING,
            self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Get the current wizard step number (1-4).
     */
    public function getWizardStepAttribute(): int
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 1,
            self::STATUS_CONFIGURED => 2,
            self::STATUS_MEMBERS_LOCKED => 3,
            self::STATUS_FINALIZED, self::STATUS_DISBURSING, self::STATUS_COMPLETED => 4,
            default => 1,
        };
    }

    /**
     * Get total allocation percentage (should sum to 100%).
     */
    public function getTotalAllocationPercentageAttribute(): float
    {
        return (float) $this->cadangan_percentage
            + (float) $this->jasa_simpanan_percentage
            + (float) $this->jasa_usaha_percentage
            + (float) $this->pengurus_percentage
            + (float) $this->dana_sosial_percentage;
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft (Persiapan)',
            self::STATUS_CONFIGURED => 'Terkonfigurasi',
            self::STATUS_MEMBERS_LOCKED => 'Anggota Terkunci',
            self::STATUS_FINALIZED => 'Disahkan & Dipublish',
            self::STATUS_DISBURSING => 'Proses Pencairan',
            self::STATUS_COMPLETED => 'Selesai',
            default => $this->status,
        };
    }
}
