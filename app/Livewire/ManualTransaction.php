<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\FinancialTransaction;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManualTransaction extends Component
{
    use WithFileUploads;

    public $type = 'EXPENSE'; // Default pengeluaran
    public $amount;
    public $category;
    public $transactionDate;
    public $description;
    public $proofFile;

    // Categories
    public $expenseCategories = [
        'Biaya Listrik & Air',
        'Beli Perlengkapan (ATK/Plastik)',
        'Gaji Karyawan',
        'Maintenance / Perbaikan',
        'Lainnya',
    ];

    public $incomeCategories = [
        'Omset Penjualan (Historis)',
        'Suntikan Modal',
        'Hibah / Donasi',
        'Pendapatan Lain-lain',
        'Retur Pembelian',
    ];

    public function mount()
    {
        $this->transactionDate = today()->format('Y-m-d');
        $this->category = $this->expenseCategories[0];
    }

    public function updatedType($value)
    {
        // Reset category when type changes
        $this->category = $value === 'INCOME' 
            ? $this->incomeCategories[0] 
            : $this->expenseCategories[0];
    }

    public function save()
    {
        $validated = $this->validate([
            'type' => 'required|in:INCOME,EXPENSE',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'transactionDate' => 'required|date',
            'description' => 'nullable|string|max:500',
            'proofFile' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $proofPath = null;
        if ($this->proofFile) {
            $proofPath = $this->proofFile->store('financial-proofs', 'public');
        }

        $transaction = FinancialTransaction::create([
            'type' => $this->type,
            'amount' => $this->amount,
            'category' => $this->category,
            'transactionDate' => $this->transactionDate,
            'description' => $this->description,
            'proofFile' => $proofPath,
            'userId' => auth()->id(),
        ]);

        // Log activity
        $typeLabel = $this->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran';
        ActivityLog::log(
            'CREATE',
            'ManualTransaction',
            "{$typeLabel} {$this->category} sebesar Rp " . number_format($this->amount, 0, ',', '.'),
            $transaction,
            null,
            $transaction->toArray()
        );

        session()->flash('success', 'Transaksi berhasil disimpan!');
        
        $this->reset(['amount', 'description', 'proofFile']);
        $this->transactionDate = today()->format('Y-m-d');
    }

    public function getRecentTransactionsProperty()
    {
        return FinancialTransaction::with('user')
            ->latest('transactionDate')
            ->latest('id')
            ->limit(5)
            ->get();
    }

    public function getPettyCashProperty()
    {
        // Saldo Kas Kecil = Total Income - Total Expense
        $totalIncome = FinancialTransaction::income()->sum('amount');
        $totalExpense = FinancialTransaction::expense()->sum('amount');
        
        return $totalIncome - $totalExpense;
    }

    public function render()
    {
        return view('livewire.manual-transaction');
    }
}
