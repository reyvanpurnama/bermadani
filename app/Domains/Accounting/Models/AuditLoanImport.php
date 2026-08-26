<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLoanImport extends Model
{
    protected $fillable = [
        'filename',
        'period',
        'raw_name',
        'raw_uraian',
        'pokok_amount',
        'jasa_amount',
        'total_amount',
        'matched_member_id',
        'status',
        'notes',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'matched_member_id');
    }
}
