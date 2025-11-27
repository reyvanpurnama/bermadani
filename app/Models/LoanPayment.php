<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    protected $fillable = [
        'loanId',
        'amount',
        'paymentDate',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paymentDate' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loanId');
    }
}
