<?php

namespace App\Livewire\Membership;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.membership')]
class Transfer extends Component
{
    const MIN_TRANSFER = 10000;
    const MAX_PER_TRANSACTION = 5000000;
    const MAX_PER_DAY = 10000000;

    public $member;
    public $step = 1;
    public $recipientNumber = '';
    public $recipientMember = null;
    public $amount = '';
    public $notes = '';
    public $password = '';
    public $showBalance = true;
    public $transferResult = null;
    public $todayTransferred = 0;
    public $recentRecipients = [];

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();

        if ($this->member) {
            $this->calculateTodayTransferred();
            $this->loadRecentRecipients();
        }
    }

    public function loadRecentRecipients()
    {
        $transactions = SimpananTransaction::where('memberId', $this->member->id)
            ->where('transactionType', 'TRANSFER_OUT')
            ->where('status', 'APPROVED')
            ->whereNotNull('relatedMemberId')
            ->with('relatedMember')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $this->recentRecipients = $transactions->pluck('relatedMember')
            ->unique('id')
            ->take(10)
            ->values()
            ->all();
    }

    public function selectRecipient($memberId)
    {
        $member = Member::find($memberId);
        if ($member) {
            $this->recipientNumber = $member->nomorAnggota;
            $this->searchRecipient();
        }
    }

    public function calculateTodayTransferred()
    {
        $this->todayTransferred = SimpananTransaction::where('memberId', $this->member->id)
            ->where('transactionType', 'TRANSFER_OUT')
            ->where('status', 'APPROVED')
            ->whereDate('created_at', today())
            ->sum('amount');
    }

    public function toggleBalance()
    {
        $this->showBalance = !$this->showBalance;
    }

    public function searchRecipient()
    {
        $this->resetErrorBag();

        if (empty($this->recipientNumber)) {
            $this->addError('recipientNumber', 'Masukkan nomor anggota tujuan');
            return;
        }

        if ($this->recipientNumber === $this->member->nomorAnggota) {
            $this->addError('recipientNumber', 'Tidak dapat transfer ke diri sendiri');
            return;
        }

        $this->recipientMember = Member::where('nomorAnggota', $this->recipientNumber)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$this->recipientMember) {
            $this->addError('recipientNumber', 'Nomor anggota tidak ditemukan atau tidak aktif');
            $this->recipientMember = null;
        }
    }

    public function clearRecipient()
    {
        $this->recipientNumber = '';
        $this->recipientMember = null;
    }

    public function proceedToConfirm()
    {
        $this->resetErrorBag();

        if (!$this->recipientMember) {
            $this->addError('recipientNumber', 'Pilih penerima terlebih dahulu');
            return;
        }

        $amount = $this->parseAmount($this->amount);

        if ($amount < self::MIN_TRANSFER) {
            $this->addError('amount', 'Minimal transfer Rp ' . number_format(self::MIN_TRANSFER, 0, ',', '.'));
            return;
        }

        if ($amount > self::MAX_PER_TRANSACTION) {
            $this->addError('amount', 'Maksimal transfer per transaksi Rp ' . number_format(self::MAX_PER_TRANSACTION, 0, ',', '.'));
            return;
        }

        $this->calculateTodayTransferred();
        $remainingDaily = self::MAX_PER_DAY - $this->todayTransferred;

        if ($amount > $remainingDaily) {
            $this->addError('amount', 'Melebihi limit harian. Sisa limit: Rp ' . number_format($remainingDaily, 0, ',', '.'));
            return;
        }

        if ($amount > $this->member->simpananSukarela) {
            $this->addError('amount', 'Saldo simpanan sukarela tidak mencukupi');
            return;
        }

        $this->step = 2;
    }

    public function backToForm()
    {
        $this->step = 1;
        $this->password = '';
    }

    public function executeTransfer()
    {
        $this->resetErrorBag();

        $user = auth()->user();
        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password salah');
            return;
        }

        $amount = $this->parseAmount($this->amount);

        $this->member->refresh();
        if ($amount > $this->member->simpananSukarela) {
            $this->addError('password', 'Saldo tidak mencukupi. Silakan coba lagi.');
            return;
        }

        $transferRef = 'TRF' . date('Ymd') . Str::upper(Str::random(8));

        DB::beginTransaction();
        try {
            $senderBalanceAfter = $this->member->simpananSukarela - $amount;
            $this->member->update(['simpananSukarela' => $senderBalanceAfter]);

            SimpananTransaction::create([
                'memberId' => $this->member->id,
                'relatedMemberId' => $this->recipientMember->id,
                'type' => 'SUKARELA',
                'transactionType' => 'TRANSFER_OUT',
                'amount' => $amount,
                'balanceAfter' => $senderBalanceAfter,
                'notes' => $this->notes ?: 'Transfer ke ' . $this->recipientMember->name,
                'processedBy' => auth()->id(),
                'status' => 'APPROVED',
                'approvedBy' => auth()->id(),
                'approvedAt' => now(),
                'transferReference' => $transferRef,
            ]);

            $recipientBalanceAfter = $this->recipientMember->simpananSukarela + $amount;
            $this->recipientMember->update(['simpananSukarela' => $recipientBalanceAfter]);

            SimpananTransaction::create([
                'memberId' => $this->recipientMember->id,
                'relatedMemberId' => $this->member->id,
                'type' => 'SUKARELA',
                'transactionType' => 'TRANSFER_IN',
                'amount' => $amount,
                'balanceAfter' => $recipientBalanceAfter,
                'notes' => $this->notes ?: 'Transfer dari ' . $this->member->name,
                'processedBy' => auth()->id(),
                'status' => 'APPROVED',
                'approvedBy' => auth()->id(),
                'approvedAt' => now(),
                'transferReference' => $transferRef,
                'isRead' => false,
            ]);

            DB::commit();

            $this->transferResult = [
                'reference' => $transferRef,
                'amount' => $amount,
                'recipient' => [
                    'name' => $this->recipientMember->name,
                    'nomorAnggota' => $this->recipientMember->nomorAnggota,
                ],
                'senderBalanceAfter' => $senderBalanceAfter,
                'timestamp' => now()->format('d M Y, H:i'),
            ];

            $this->member->refresh();
            $this->step = 3;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('password', 'Transfer gagal. Silakan coba lagi.');
            report($e);
        }
    }

    public function newTransfer()
    {
        $this->reset(['step', 'recipientNumber', 'recipientMember', 'amount', 'notes', 'password', 'transferResult']);
        $this->step = 1;
        $this->calculateTodayTransferred();
        $this->loadRecentRecipients();
    }

    private function parseAmount($value): float
    {
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        return (float) $cleaned;
    }

    public function render()
    {
        return view('livewire.membership.transfer');
    }
}
