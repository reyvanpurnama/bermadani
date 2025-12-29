<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SimpananPayment;

class PaymentReceipt extends Component
{
    public $payment;
    public $relatedPayments = [];
    public $member;
    public $processor;
    public $grandTotal = 0;
    public $totalInWords = '';

    public function mount($receiptNumber)
    {
        $this->payment = SimpananPayment::where('receiptNumber', $receiptNumber)
            ->with(['bill.member', 'processor', 'member'])
            ->firstOrFail();
        
        $this->member = $this->payment->member;
        $this->processor = $this->payment->processor;

        // Load related payments (same batch - same date & member, within 1 minute)
        $this->relatedPayments = SimpananPayment::where('memberId', $this->payment->memberId)
            ->where('paymentDate', $this->payment->paymentDate)
            ->whereBetween('created_at', [
                $this->payment->created_at->copy()->subMinute(),
                $this->payment->created_at->copy()->addMinute()
            ])
            ->with('bill')
            ->get();

        $this->grandTotal = $this->relatedPayments->sum('amount');
        $this->totalInWords = $this->numberToWords($this->grandTotal);
    }

    /**
     * Convert number to Indonesian words
     */
    private function numberToWords($number)
    {
        $number = abs($number);
        $words = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
            'Sepuluh', 'Sebelas'
        ];

        if ($number < 12) {
            return $words[$number];
        } elseif ($number < 20) {
            return $words[$number - 10] . ' Belas';
        } elseif ($number < 100) {
            return $words[floor($number / 10)] . ' Puluh ' . $words[$number % 10];
        } elseif ($number < 200) {
            return 'Seratus ' . $this->numberToWords($number - 100);
        } elseif ($number < 1000) {
            return $words[floor($number / 100)] . ' Ratus ' . $this->numberToWords($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . $this->numberToWords($number - 1000);
        } elseif ($number < 1000000) {
            return $this->numberToWords(floor($number / 1000)) . ' Ribu ' . $this->numberToWords($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->numberToWords(floor($number / 1000000)) . ' Juta ' . $this->numberToWords($number % 1000000);
        }
        
        return trim($number);
    }

    public function render()
    {
        return view('livewire.admin.payment-receipt')
            ->layout('layouts.admin', [
                'title' => 'Kuitansi Pembayaran'
            ]);
    }
}
