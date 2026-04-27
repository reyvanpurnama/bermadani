<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class LoanCreate extends Component
{
    public \ = '';
    public \;
    public \ = 'BERMADANI';
    public \;
    public \;
    public \;
    public \ = 0;
    public \ = 0;
    
    public \;
    public \;
    public \;

    public function mount()
    {
        // Default start date is next month's 1st day (typical payroll cut off)
        \->startDate = now()->addMonth()->startOfMonth()->format('Y-m-d');
    }

    public function calculateMonthly()
    {
        if (!empty(\->amount) && !empty(\->tenor) && \->tenor > 0) {
            \ = floatval(str_replace(['.', ','], ['', '.'], \->amount));
            \ = floatval(\->interestRate ?? 0);
            
            \ = \ + (\ * (\ / 100));
            \ = \ / \->tenor;
            
            // Add BMT ITQAN simwa if applicable
            \ = floatval(str_replace(['.', ','], ['', '.'], \->simwa_amount ?? 0));
            
            \->monthlyPayment = round(\ + \);
        }
    }

    public function updatedAmount() { \->calculateMonthly(); }
    public function updatedTenor() { \->calculateMonthly(); }
    public function updatedInterestRate() { \->calculateMonthly(); }
    public function updatedSimwaAmount() { \->calculateMonthly(); }

    public function selectMember(\)
    {
        \->member_id = \;
        \->search = Member::find(\)->name;
    }

    public function createLoan()
    {
        \->validate([
            'member_id' => 'required|exists:members,id',
            'loanSource' => 'required|in:BERMADANI,BMT_ITQAN',
            'amount' => 'required|numeric|min:1',
            'tenor' => 'required|integer|min:1',
            'monthlyPayment' => 'required|numeric|min:1',
            'startDate' => 'required|date',
            'purpose' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Bersihkan format titik/koma jika ada
            \ = floatval(str_replace(['.', ','], ['', '.'], \->amount));
            \ = floatval(str_replace(['.', ','], ['', '.'], \->monthlyPayment));
            \ = floatval(str_replace(['.', ','], ['', '.'], \->simwa_amount ?? 0));
            \ = floatval(\->interestRate ?? 0);

            // Total hutang yg hrs dibayar (pokok + bunga)
            \ = \ + (\ * (\ / 100));

            \ = Loan::create([
                'member_id' => \->member_id,
                'amount' => \,
                'interestRate' => \,
                'tenor' => \->tenor,
                'monthlyPayment' => \,
                'simwa_amount' => \,
                'remainingAmount' => \, // Sisa hutang dicatat termasuk margin admin
                'status' => 'ACTIVE',
                'loanSource' => \->loanSource,
                'purpose' => \->purpose,
                'description' => \->description,
                'startDate' => \->startDate,
                'endDate' => Carbon::parse(\->startDate)->addMonths(\->tenor)->format('Y-m-d'),
                'approvedAt' => now(),
                'approvedBy' => auth()->id(),
                'paid_installments' => 0,
            ]);

            DB::commit();

            session()->flash('success', 'Pinjaman ' . \->loanSource . ' berhasil ditambahkan dan langsung aktif!');
            return redirect()->route('admin.loans');

        } catch (\Exception \) {
            DB::rollBack();
            session()->flash('error', 'Gagal membuat pinjaman: ' . \->getMessage());
        }
    }

    public function render()
    {
        \ = collect();
        if (strlen(\->search) >= 2 && empty(\->member_id)) {
            \ = Member::where('status', 'ACTIVE')
                ->where(function(\) {
                    \->where('name', 'like', '%' . \->search . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . \->search . '%');
                })->take(5)->get();
        }

        return view('livewire.admin.loan-create', [
            'members' => \
        ]);
    }
}
