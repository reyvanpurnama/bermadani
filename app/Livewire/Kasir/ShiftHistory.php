<?php

namespace App\Livewire\Kasir;

use App\Models\CashierShift;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ShiftHistory extends Component
{
    use WithPagination;

    public $selectedShiftId = null;
    public $showDetailModal = false;
    public $shiftTransactions = [];

    public function getShiftsProperty()
    {
        return CashierShift::where('user_id', Auth::id())
            ->orderBy('check_in_at', 'desc')
            ->paginate(10);
    }

    public function getLifetimeStatsProperty()
    {
        $shifts = CashierShift::where('user_id', Auth::id())->where('status', 'CLOSED')->get();

        $totalMinutes = $shifts->sum(function ($s) {
            return $s->check_in_at && $s->check_out_at
                ? $s->check_in_at->diffInMinutes($s->check_out_at)
                : 0;
        });

        return [
            'total_shifts'       => CashierShift::where('user_id', Auth::id())->count(),
            'total_hours'        => floor($totalMinutes / 60),
            'total_minutes_rem'  => $totalMinutes % 60,
            'total_sales'        => $shifts->sum('total_sales'),
            'total_transactions' => $shifts->sum('total_transactions'),
            'avg_sales_per_shift'=> $shifts->count() > 0 ? $shifts->avg('total_sales') : 0,
            'total_difference'   => $shifts->sum('difference'),
        ];
    }

    public function viewDetail($shiftId)
    {
        $shift = CashierShift::where('user_id', Auth::id())->find($shiftId);
        if (!$shift) return;

        $this->selectedShiftId = $shiftId;

        $query = Transaction::with(['member', 'items.product'])
            ->where('userId', Auth::id())
            ->where('type', 'SALE')
            ->where('status', 'COMPLETED')
            ->where('date', '>=', $shift->check_in_at);

        if ($shift->check_out_at) {
            $query->where('date', '<=', $shift->check_out_at);
        }

        $this->shiftTransactions = $query->orderBy('date', 'desc')->get();
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedShiftId = null;
        $this->shiftTransactions = [];
    }

    public function getSelectedShiftProperty()
    {
        if (!$this->selectedShiftId) return null;
        return CashierShift::where('user_id', Auth::id())->find($this->selectedShiftId);
    }

    public function render()
    {
        return view('livewire.kasir.shift-history', [
            'shifts'        => $this->shifts,
            'lifetimeStats' => $this->lifetimeStats,
        ])->layout('layouts.admin');
    }
}
