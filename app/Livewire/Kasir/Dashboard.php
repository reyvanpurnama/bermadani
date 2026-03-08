<?php

namespace App\Livewire\Kasir;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $openingCash = 0;
    public $closingCash = 0;
    public $closeNote = '';
    public $showCheckInModal = false;
    public $showCheckOutModal = false;
    public $viewOnlyMode = false;

    public function mount()
    {
        // Check if user has open shift
        $shift = $this->currentShift;
        if (!$shift) {
            $this->showCheckInModal = true;
        }
    }

    public function getCurrentShiftProperty()
    {
        return CashierShift::getOpenShift(Auth::id());
    }

    public function skipCheckIn()
    {
        $this->viewOnlyMode = true;
        $this->showCheckInModal = false;
    }

    public function openCheckInModal()
    {
        $this->showCheckInModal = true;
    }

    public function checkIn()
    {
        $this->validate([
            'openingCash' => 'required|numeric|min:0',
        ]);

        $shift = CashierShift::create([
            'user_id' => Auth::id(),
            'opening_cash' => $this->openingCash,
            'check_in_at' => now(),
            'status' => 'OPEN',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        ActivityLog::log(
            'CHECK_IN',
            'Shift',
            "Kasir " . $user->name . " check-in dengan modal Rp " . number_format($this->openingCash, 0, ',', '.'),
            $shift
        );

        $this->showCheckInModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Check-in berhasil! Selamat bekerja 💪']);
    }

    public function openCheckOutModal()
    {
        $shift = $this->currentShift;
        if ($shift) {
            $shift->calculateSummary();
            $shift->save();
        }
        $this->showCheckOutModal = true;
    }

    public function checkOut()
    {
        $this->validate([
            'closingCash' => 'required|numeric|min:0',
        ]);

        $shift = $this->currentShift;
        if (!$shift) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada shift aktif']);
            return;
        }

        $shift->check_out_at = now();
        $shift->closing_cash = $this->closingCash;
        $shift->note = $this->closeNote;
        $shift->status = 'CLOSED';
        $shift->calculateSummary();
        $shift->save();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        ActivityLog::log(
            'CHECK_OUT',
            'Shift',
            "Kasir " . $user->name . " check-out. Total penjualan: Rp " . number_format($shift->total_sales, 0, ',', '.') . ", Selisih: Rp " . number_format($shift->difference, 0, ',', '.'),
            $shift
        );

        $this->showCheckOutModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Check-out berhasil! Terima kasih atas kerja kerasnya 🙏']);
        
        // Show check-in modal for next shift
        $this->reset(['closingCash', 'closeNote', 'openingCash']);
        $this->showCheckInModal = true;
    }

    public function getTodaySalesProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return 0;

        return Transaction::where('userId', Auth::id())
            ->where('date', '>=', $shift->check_in_at)
            ->where('status', 'COMPLETED')
            ->sum('totalAmount');
    }

    public function getTodayTransactionsCountProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return 0;

        return Transaction::where('userId', Auth::id())
            ->where('date', '>=', $shift->check_in_at)
            ->where('status', 'COMPLETED')
            ->count();
    }

    public function getCashSalesProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return 0;

        return Transaction::where('userId', Auth::id())
            ->where('date', '>=', $shift->check_in_at)
            ->where('status', 'COMPLETED')
            ->where('paymentMethod', 'CASH')
            ->sum('totalAmount');
    }

    public function getDigitalSalesProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return 0;

        return Transaction::where('userId', Auth::id())
            ->where('date', '>=', $shift->check_in_at)
            ->where('status', 'COMPLETED')
            ->whereIn('paymentMethod', ['DEBIT', 'CREDIT', 'QRIS', 'TRANSFER'])
            ->sum('totalAmount');
    }

    public function getExpectedCashProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return 0;

        return $shift->opening_cash + $this->cashSales;
    }

    public function getTransactionsPerHourProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return 0;

        $elapsedMinutes = $shift->check_in_at->diffInMinutes(now());
        if ($elapsedMinutes < 1) return 0;

        return round($this->todayTransactionsCount / ($elapsedMinutes / 60), 1);
    }

    public function getShiftElapsedProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return ['hours' => 0, 'minutes' => 0, 'total_minutes' => 0];

        $totalMinutes = $shift->check_in_at->diffInMinutes(now());
        return [
            'hours'         => intdiv($totalMinutes, 60),
            'minutes'       => $totalMinutes % 60,
            'total_minutes' => $totalMinutes,
        ];
    }

    public function getLowStockProductsProperty()
    {
        return Product::active()
            ->whereColumn('stock', '<=', 'threshold')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
    }

    public function getRecentTransactionsProperty()
    {
        $shift = $this->currentShift;
        if (!$shift) return collect();

        return Transaction::with('member')
            ->where('userId', Auth::id())
            ->where('date', '>=', $shift->check_in_at)
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kasir.dashboard');
    }
}
