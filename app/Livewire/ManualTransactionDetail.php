<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\FinancialTransaction;
use Livewire\Component;

class ManualTransactionDetail extends Component
{
    public $transactionId;
    public $transaction;

    public function mount($transactionId)
    {
        $this->transactionId = $transactionId;
        $this->transaction = FinancialTransaction::with('user')->findOrFail($transactionId);
    }

    public function delete()
    {
        // Check permission - only Super Admin, Admin, Developer can delete
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin() && !auth()->user()->isDeveloper()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus transaksi ini.');
            return;
        }

        $typeLabel = $this->transaction->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran';
        $oldData = $this->transaction->toArray();
        
        // Log activity before delete - pass the model while it still exists
        ActivityLog::log(
            'DELETE',
            'ManualTransaction',
            "{$typeLabel} {$this->transaction->category} sebesar Rp " . number_format($this->transaction->amount, 0, ',', '.'),
            $this->transaction,  // Pass model before delete
            $oldData,
            null
        );

        $this->transaction->delete();
        
        session()->flash('success', 'Transaksi berhasil dihapus.');
        return redirect()->route('admin.manual-transaction');
    }

    public function render()
    {
        return view('livewire.manual-transaction-detail');
    }
}
