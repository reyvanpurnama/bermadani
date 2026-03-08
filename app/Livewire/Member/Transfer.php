<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.member')]
class Transfer extends Component
{
    // Transfer settings
    const MIN_TRANSFER = 10000;
    const MAX_PER_TRANSACTION = 5000000;
    const MAX_PER_DAY = 10000000;
    const ADMIN_FEE = 0;

    public $member;
    public $step = 1; // 1: form, 2: confirm, 3: success ==== state awal

    // Form fields
    public $recipientNumber = '';
    public $recipientMember = null;
    public $amount = '';
    public $notes = '';
    public $password = '';

    // Hide/show saldo
    public $showBalance = true;

    // Transfer result
    public $transferResult = null;

    // Daily transfer tracking
    public $todayTransferred = 0;

    // Recent recipients
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
        // Get last 20 transfers to find unique recipients
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

        // Validate recipient
        if (!$this->recipientMember) {
            $this->addError('recipientNumber', 'Pilih penerima terlebih dahulu');
            return;
        }

        // Parse amount
        $amount = $this->parseAmount($this->amount);

        // Validate minimum
        if ($amount < self::MIN_TRANSFER) {
            $this->addError('amount', 'Minimal transfer Rp ' . number_format(self::MIN_TRANSFER, 0, ',', '.'));
            return;
        }

        // Validate maximum per transaction
        if ($amount > self::MAX_PER_TRANSACTION) {
            $this->addError('amount', 'Maksimal transfer per transaksi Rp ' . number_format(self::MAX_PER_TRANSACTION, 0, ',', '.'));
            return;
        }

        // Validate daily limit
        $this->calculateTodayTransferred();
        $remainingDaily = self::MAX_PER_DAY - $this->todayTransferred;

        if ($amount > $remainingDaily) {
            $this->addError('amount', 'Melebihi limit harian. Sisa limit: Rp ' . number_format($remainingDaily, 0, ',', '.'));
            return;
        }

        // Validate balance
        if ($amount > $this->member->simpananSukarela) {
            $this->addError('amount', 'Saldo simpanan sukarela tidak mencukupi');
            return;
        }

        // Proceed to confirm
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

        // Validate password
        $user = auth()->user();
        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password salah');
            return;
        }

        $amount = $this->parseAmount($this->amount);

        // Double check balance (prevent race condition)
        $this->member->refresh();
        if ($amount > $this->member->simpananSukarela) {
            $this->addError('password', 'Saldo tidak mencukupi. Silakan coba lagi.');
            return;
        }

        // Generate transfer reference
        $transferRef = 'TRF' . date('Ymd') . Str::upper(Str::random(8));

        DB::beginTransaction();
        try {
            // Deduct from sender
            $senderBalanceAfter = $this->member->simpananSukarela - $amount;
            $this->member->update(['simpananSukarela' => $senderBalanceAfter]);

            // Record sender transaction (OUT)
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

            // Add to recipient
            $recipientBalanceAfter = $this->recipientMember->simpananSukarela + $amount;
            $this->recipientMember->update(['simpananSukarela' => $recipientBalanceAfter]);

            // Record recipient transaction (IN)
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
                'isRead' => false, // Mark as unread for notification
            ]);

            DB::commit();

            // Store result for success page
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

            // Refresh member data
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
        // Remove formatting (dots, commas, spaces)
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        return (float) $cleaned;
    }

    public function render()
    {
        return view('livewire.member.transfer');
    }
}
