<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportSnapshot extends Model
{
    protected $fillable = [
        'month',
        'year',
        'data',
        'status',
        'executed_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function executedBy()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
