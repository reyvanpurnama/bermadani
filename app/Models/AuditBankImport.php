<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditBankImport extends Model
{
    protected $fillable = [
        'filename',
        'period',
        'transaction_date',
        'transaction_time',
        'keterangan',
        'debet',
        'kredit',
        'saldo',
        'detected_type',
        'detected_category',
        'manual_type',
        'manual_category',
        'manual_description',
        'is_reviewed',
        'is_synced',
        'synced_bank_transaction_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debet' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo' => 'decimal:2',
        'is_reviewed' => 'boolean',
        'is_synced' => 'boolean',
    ];

    // Helper: Get final type (manual override or detected)
    public function getFinalTypeAttribute()
    {
        return $this->manual_type ?? $this->detected_type;
    }

    // Helper: Get final category (manual override or detected)
    public function getFinalCategoryAttribute()
    {
        return $this->manual_category ?? $this->detected_category;
    }

    // Helper: Get final description (manual override or original)
    public function getFinalDescriptionAttribute()
    {
        return $this->manual_description ?? $this->keterangan;
    }

    // Scopes
    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    public function scopeUnsynced($query)
    {
        return $query->where('is_synced', false);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }
}
