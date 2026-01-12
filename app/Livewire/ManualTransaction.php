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

    public $mode = 'recap'; // 'single' or 'recap'
    public $dailyExpense;
    public $dailyCashOnHand;
    public $startCash;

    public function mount()
    {
        // Capture unit from query parameter if present
        $requestUnit = request()->query('unit');
        if (in_array($requestUnit, ['KOPERASI', 'BISNIS'])) {
            $this->unit = $requestUnit;
        }

        $this->transactionDate = today()->format('Y-m-d');
        $this->category = $this->expenseCategories[0];
        $this->startCash = $this->pettyCash; // Capture initial cash (uses current unit)
    }

    public function updatedType($value)
    {
        // Reset category when type changes
        $this->category = $value === 'INCOME'
            ? $this->incomeCategories[0]
            : $this->expenseCategories[0];
    }

    public function updatedMode($value)
    {
        if ($value === 'recap') {
            $this->startCash = $this->pettyCash; // Refresh
            $this->reset(['dailyExpense', 'dailyCashOnHand', 'description']);
        }
    }

    public function getCalculatedIncomeProperty()
    {
        // Formula: Income = (Uang Laci + Pengeluaran) - Saldo Awal
        // Income = (CashOnHand + Expense) - StartCash
        $cashOnHand = (float) str_replace('.', '', $this->dailyCashOnHand ?? 0);
        $expense = (float) str_replace('.', '', $this->dailyExpense ?? 0);

        return ($cashOnHand + $expense) - $this->startCash;
    }

    public function save()
    {
        if ($this->mode === 'recap') {
            $this->saveRecap();
            return;
        }

        $validated = $this->validate([
            'type' => 'required|in:INCOME,EXPENSE',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'transactionDate' => 'required|date',
            'description' => 'nullable|string|max:500',
            'proofFile' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $this->createTransaction(
            $this->type,
            $this->amount,
            $this->category,
            $this->description,
            $this->transactionDate,
            $this->proofFile
        );

        session()->flash('success', 'Transaksi berhasil disimpan!');
        $this->reset(['amount', 'description', 'proofFile']);
    }

    public function saveRecap()
    {
        $this->validate([
            'dailyExpense' => 'required',
            'dailyCashOnHand' => 'required',
            'transactionDate' => 'required|date',
        ]);

        $expenseAmount = (float) str_replace('.', '', $this->dailyExpense);
        $incomeAmount = $this->calculatedIncome;

        // 1. Record Expense (if any)
        if ($expenseAmount > 0) {
            $this->createTransaction(
                'EXPENSE',
                $expenseAmount,
                'Operasional Harian', // Generic category for batch
                "Rekap Pengeluaran Harian:\n" . ($this->description ?? '-'),
                $this->transactionDate,
                $this->proofFile // Attach proof here preferentially
            );
        }

        // 2. Record Income / Adjustment
        if ($incomeAmount > 0) {
            // Positive Revenue
            $this->createTransaction(
                'INCOME',
                $incomeAmount,
                'Omset Penjualan (Harian)',
                "Rekap Pemasukan Harian (Calculated)",
                $this->transactionDate,
                ($expenseAmount <= 0) ? $this->proofFile : null // Attach here if no expense transaction
            );
        } elseif ($incomeAmount < 0) {
            // Negative (Loss/Difference)
            $this->createTransaction(
                'EXPENSE',
                abs($incomeAmount),
                'Selisih Kas (Minus)',
                "Selisih Kas Harian (Calculated)",
                $this->transactionDate,
                ($expenseAmount <= 0) ? $this->proofFile : null
            );
        }

        session()->flash('success', 'Rekap Harian berhasil disimpan! Sistem telah mencatat Pengeluaran & Pemasukan otomatis.');
        $this->reset(['dailyExpense', 'dailyCashOnHand', 'description', 'proofFile']);
        $this->startCash = $this->pettyCash; // Refresh state
    }

    protected function createTransaction($type, $amount, $category, $description, $date, $proof = null)
    {
        $proofPath = null;
        if ($proof) {
            $proofPath = $proof->store('financial-proofs', 'public');
        }

        $transaction = FinancialTransaction::create([
            'type' => $type,
            'amount' => $amount,
            'category' => $category,
            'transactionDate' => $date,
            'description' => $description,
            'proofFile' => $proofPath,
            'userId' => auth()->id(),
        ]);

        // Log activity
        $typeLabel = $type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran';
        ActivityLog::log(
            'CREATE',
            'ManualTransaction',
            "{$typeLabel} {$category} sebesar Rp " . number_format($amount, 0, ',', '.'),
            $transaction,
            null,
            $transaction->toArray()
        );
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
