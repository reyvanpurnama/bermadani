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
    public $search = '';
    public $member_id;
    public $loanSource = 'BERMADANI';
    public $amount;
    public $tenor;
    public $monthlyPayment;
    public $interestRate = 0;
    public $simwa_amount = 0;
    
    public $startDate;
    public $purpose;
    public $description;

    public function mount()
    {
        // Default start date is next month's 1st day (typical payroll cut off)
        $this->startDate = now()->addMonth()->startOfMonth()->format('Y-m-d');
    }

    public function calculateMonthly()
    {
        if (!empty($this->amount) && !empty($this->tenor) && $this->tenor > 0) {
            $baseAmount = floatval(str_replace(['.', ','], ['', '.'], $this->amount));
            $interest = floatval($this->interestRate ?? 0);
            
            $totalAmount = $baseAmount + ($baseAmount * ($interest / 100));
            $monthly = $totalAmount / $this->tenor;
            
            // Add BMT ITQAN simwa if applicable
            $simwa = floatval(str_replace(['.', ','], ['', '.'], $this->simwa_amount ?? 0));
            
            $this->monthlyPayment = round($monthly + $simwa);
        }
    }

    public function updatedAmount() { $this->calculateMonthly(); }
    public function updatedTenor() { $this->calculateMonthly(); }
    public function updatedInterestRate() { $this->calculateMonthly(); }
    public function updatedSimwaAmount() { $this->calculateMonthly(); }

    public function selectMember($id)
    {
        $this->member_id = $id;
        $this->search = Member::find($id)->name;
    }

    public function createLoan()
    {
        $this->validate([
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
            $cleanAmount = floatval(str_replace(['.', ','], ['', '.'], $this->amount));
            $cleanMonthly = floatval(str_replace(['.', ','], ['', '.'], $this->monthlyPayment));
            $cleanSimwa = floatval(str_replace(['.', ','], ['', '.'], $this->simwa_amount ?? 0));
            $cleanInterest = floatval($this->interestRate ?? 0);

            // Total hutang yg hrs dibayar (pokok + bunga)
            $totalDebt = $cleanAmount + ($cleanAmount * ($cleanInterest / 100));

            $loan = Loan::create([
                'member_id' => $this->member_id,
                'amount' => $cleanAmount,
                'interestRate' => $cleanInterest,
                'tenor' => $this->tenor,
                'monthlyPayment' => $cleanMonthly,
                'simwa_amount' => $cleanSimwa,
                'remainingAmount' => $totalDebt, // Sisa hutang dicatat termasuk margin admin
                'status' => 'ACTIVE',
                'loanSource' => $this->loanSource,
                'purpose' => $this->purpose,
                'description' => $this->description,
                'startDate' => $this->startDate,
                'endDate' => Carbon::parse($this->startDate)->addMonths($this->tenor)->format('Y-m-d'),
                'approvedAt' => now(),
                'approvedBy' => auth()->id(),
                'paid_installments' => 0,
            ]);

            DB::commit();

            session()->flash('success', 'Pinjaman ' . $this->loanSource . ' berhasil ditambahkan dan langsung aktif!');
            return redirect()->route('admin.loans');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membuat pinjaman: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $members = collect();
        if (strlen($this->search) >= 2 && empty($this->member_id)) {
            $members = Member::where('status', 'ACTIVE')
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('nomorAnggota', 'like', '%' . $this->search . '%');
                })->take(5)->get();
        }

        return view('livewire.admin.loan-create', [
            'members' => $members
        ]);
    }
}
