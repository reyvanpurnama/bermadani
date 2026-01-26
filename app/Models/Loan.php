<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'member_id',
        'amount',
        'interestRate',
        'tenor',
        'monthlyPayment',
        'remainingAmount',
        'status',
        'loanSource', // BERMADANI or BMT_ITQAN
        'purpose',
        'approvedAt',
        'approvedBy',
        'startDate',
        'endDate',
        'description',
        'paid_installments',
        'account_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interestRate' => 'decimal:2',
        'monthlyPayment' => 'decimal:2',
        'remainingAmount' => 'decimal:2',
        'tenor' => 'integer',
        'approvedAt' => 'datetime',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class, 'loanId');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'OVERDUE');
    }

    public function addPayment($amount, $description = null)
    {
        $payment = $this->payments()->create([
            'amount' => $amount,
            'paymentDate' => now(),
            'description' => $description,
        ]);

        $this->decrement('remainingAmount', $amount);

        if ($this->remainingAmount <= 0) {
            $this->update(['status' => 'COMPLETED']);
        }

        return $payment;
    }

    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'ACTIVE',
            'approvedAt' => now(),
            'approvedBy' => $approvedBy,
        ]);

        return $this;
    }

    public function reject()
    {
        $this->update(['status' => 'REJECTED']);
        return $this;
    }
}
