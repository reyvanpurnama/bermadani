<?php

namespace App\Livewire\Admin\Reports;

use App\Models\FinancialTransaction;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Product;
use App\Models\SimpananTransaction;
use App\Models\Transaction;
use Livewire\Component;

class BalanceSheet extends Component
{
    public $reportDate;

    public function mount()
    {
        $this->reportDate = now()->format('Y-m-d');
    }

    public function getAssetsProperty()
    {
        // 1. Kas & Bank (Estimated)
        // Manual Transactions (Income - Expense)
        $manualIncome = FinancialTransaction::income()->sum('amount');
        $manualExpense = FinancialTransaction::expense()->sum('amount');

        // Retail POS (Total Sales)
        // Note: We might need to subtract COGS if we assume money was spent on restocking, 
        // but restocking is usually 'StockMovement' or 'ManualTransaction' (Expense).
        // For simple Cash view: Just Add Sales Revenue.
        $posRevenue = Transaction::where('status', 'COMPLETED')->sum('totalAmount');

        // Savings (In - Out)
        // Check if we assume 'SimpananTransaction' reflects cash movement.
        // Or diff of Member balances. Let's use Member balances for NOW as "Cash held by coop".
        // Actually, Member Balances = Liability (What we owe them).
        // The Cash equivalent is what we received. 
        // So Cash = Sum(Simpanan Pokok + Wajib + Sukarela) - Withdrawals.
        // This is equal to Total Simpanan Balance.
        $savingsCash = Member::sum('simpananPokok')
            + Member::sum('simpananWajib')
            + Member::sum('simpananSukarela');

        // Loans (Out + In)
        // Disbursed Loans = Cash Out.
        // Repayments = Cash In.
        // Current Outstanding = Amount Disbursed - Repaid.
        // So Cash Flow = -(Outstanding) ... wait.
        // Cash = Initial Capital + Deposits + Income - Expenses - Loans Disbursed.
        // Let's approximate:
        // Cash = (Manual Net) + (POS Revenue) + (Savings Net) - (Loan Outstanding).
        // This is a rough proxy because we don't have a 'Loans Disbursed' ledger easily from just 'Loan' model 
        // without summing all 'Loan::sum(amount)'.
        // Let's try: Loan::sum('amount') is Total Disbursed. Loan::active()->sum('remaining') is Outstanding.
        // Repaid = Total - Remaining.
        // Cash Impact = Repaid - Disbursed = -Remaining.

        $totalLoanDisbursed = Loan::sum('amount'); // This might include historical?
        // Let's stick to Active/Completed loans for safety? 
        // Or just use 'remainingAmount' for Receivables and assume 'Cash' is balancing?
        // No, 'Cash' must be calculated.

        // Revised Cash Calculation:
        // Cash = (Manual Income - Manual Exp) + POS Revenue + Savings Collected - Loans Outstanding (Net flow).
        // Loans Outstanding is (Principal Lent - Principal Paid).
        // So actually Cash = ... - (Principal Lent) + (Principal Paid).
        // Which is exactly: - (Loan Remaining).
        // Wait, interest? Interest is income.
        // If we don't track interest separate, this breaks.
        // Let's simplify: 
        // Cash System = (ManualNet) + (POSRevenue) + (SavingsBalance) - (LoansOutstanding).

        $receivables = Loan::whereIn('status', ['ACTIVE', 'OVERDUE'])->sum('remainingAmount');

        // Inventory Assets
        // Stock * BuyPrice (Cost Basis)
        // Optimized: Calculate in DB or loop. Product::select(DB::raw('sum(stock * buyPrice)'))?
        // SQLite/MySQL compatible:
        $inventoryValues = Product::all()->sum(function ($p) {
            return $p->stock * $p->buyPrice;
        });

        $systemCash = ($manualIncome - $manualExpense) + $posRevenue + $savingsCash - $receivables;

        return [
            'cash' => $systemCash,
            'receivables' => $receivables,
            'inventory' => $inventoryValues,
            // Fixed Assets could be Manual Transaction category? For now 0.
            'fixed_assets' => 0,
            'total' => $systemCash + $receivables + $inventoryValues
        ];
    }

    public function getLiabilitiesProperty()
    {
        // 1. Simpanan Anggota (Liablitas Jangka Pendek)
        // Menurut PSAK 27: Simpanan Sukarela adalah Hutang karena sifatnya cair.
        $sukarela = Member::sum('simpananSukarela');

        // 2. Utang (Maybe to Suppliers? Not tracked yet).

        return [
            'simpanan_sukarela' => $sukarela,
            'total' => $sukarela
        ];
    }

    public function getEquityProperty()
    {
        // 1. Modal Anggota (Ekuitas)
        // Menurut PSAK 27: Simpanan Pokok & Wajib adalah Ekuitas.
        $pokok = Member::sum('simpananPokok');
        $wajib = Member::sum('simpananWajib');

        // 2. Modal Awal / Suntikan Modal (Hibah/Donasi)
        $capital = FinancialTransaction::income()
            ->where('category', 'Suntikan Modal')
            ->sum('amount');

        // 3. SHU (Sisa Hasil Usaha)
        // Assets - Liabilities - (Modal Anggota + Modal Disetor)
        $assets = $this->assets['total'];
        $liabilities = $this->liabilities['total'];
        $totalModal = $pokok + $wajib + $capital;

        $shu = $assets - $liabilities - $totalModal;

        return [
            'simpanan_pokok' => $pokok,
            'simpanan_wajib' => $wajib,
            'capital' => $capital,
            'shu' => $shu,
            'total' => $totalModal + $shu
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports.balance-sheet', [
            'assets' => $this->assets,
            'liabilities' => $this->liabilities,
            'equity' => $this->equity
        ])->layout('layouts.admin');
    }
}
