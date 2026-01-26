<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Services\MemberService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MemberEdit extends Component
{
    public $memberId;
    public $member;

    // Editable fields
    public $name;
    public $phone;
    public $gender;
    public $unitKerja;
    public $address;
    public $status;

    // Payment preferences
    public $simwa_payment_method;
    public $sukarela_payment_method;
    public $monthly_sukarela_amount;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20', // Changed to nullable
            'gender' => 'required|in:MALE,FEMALE',
            'unitKerja' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'simwa_payment_method' => 'required|in:SALARY_DEDUCTION,MANUAL',
            'sukarela_payment_method' => 'required|in:SALARY_DEDUCTION,MANUAL',
            'monthly_sukarela_amount' => 'required|numeric|min:0',
        ];
    }

    public function mount($id)
    {
        $this->memberId = $id;
        $this->loadMember();
        $this->fillForm();
    }

    public function loadMember()
    {
        $this->member = Member::with('user')->findOrFail($this->memberId);
    }

    public function fillForm()
    {
        $this->name = $this->member->user->name;
        $this->phone = $this->member->phone;
        $this->gender = $this->member->gender;
        $this->unitKerja = $this->member->unitKerja;
        $this->address = $this->member->address;
        $this->status = $this->member->status;

        // Payment preferences
        $this->simwa_payment_method = $this->member->simwa_payment_method ?? 'SALARY_DEDUCTION';
        $this->sukarela_payment_method = $this->member->sukarela_payment_method ?? 'MANUAL';
        $this->monthly_sukarela_amount = $this->member->monthly_sukarela_amount ?? 0;
    }

    public function getUnitKerjaListProperty()
    {
        return DB::table('members')
            ->whereNotNull('unitKerja')
            ->distinct()
            ->pluck('unitKerja')
            ->sort()
            ->values();
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Refresh member model to ensure we have latest
            $this->member->refresh();

            // Update user name
            $this->member->user->update([
                'name' => $this->name,
            ]);

            // Update member info including payment preferences
            $updateData = [
                'phone' => $this->phone,
                'gender' => $this->gender,
                'unitKerja' => $this->unitKerja,
                'address' => $this->address,
                'status' => $this->status,
                'simwa_payment_method' => $this->simwa_payment_method,
                'sukarela_payment_method' => $this->sukarela_payment_method,
                'monthly_sukarela_amount' => $this->sukarela_payment_method === 'SALARY_DEDUCTION'
                    ? $this->monthly_sukarela_amount
                    : 0,
                'salary_deduction_consent_date' => ($this->simwa_payment_method === 'SALARY_DEDUCTION' || $this->sukarela_payment_method === 'SALARY_DEDUCTION')
                    ? now()->toDateString()
                    : null,
            ];

            // Debug log
            \Log::info('MemberEdit Update', [
                'member_id' => $this->member->id,
                'simwa_method' => $this->simwa_payment_method,
                'sukarela_method' => $this->sukarela_payment_method,
            ]);

            $this->member->update($updateData);

            DB::commit();

            // Dispatch toast notification instead of redirect
            $this->dispatch('notify', [
                'message' => 'Data member berhasil diperbarui!',
                'type' => 'success'
            ]);

            // Refresh member data to show updated values
            $this->loadMember();
            $this->fillForm();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('MemberEdit Error: ' . $e->getMessage());

            $this->dispatch('notify', [
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    // Loan Management
    public $loanModalVisible = false;
    public $editingLoanId = null;
    public $loanForm = [
        'amount' => 0,
        'monthlyPayment' => 0,
        'tenor' => 0,
        'paid_installments' => 0,
        'status' => 'ACTIVE',
        'loanSource' => 'BMT_ITQAN',
        'startDate' => null,
    ];

    public function openLoanModal($loanId = null)
    {
        $this->editingLoanId = $loanId;
        $this->loanModalVisible = true;

        if ($loanId) {
            $loan = \App\Models\Loan::find($loanId);
            $this->loanForm = [
                'amount' => $loan->amount,
                'monthlyPayment' => $loan->monthlyPayment,
                'tenor' => $loan->tenor,
                'paid_installments' => $loan->paid_installments,
                'status' => $loan->status,
                'loanSource' => $loan->loanSource,
                'startDate' => $loan->startDate ? $loan->startDate->format('Y-m-d') : null,
            ];
        } else {
            $this->loanForm = [
                'amount' => 0,
                'monthlyPayment' => 0,
                'tenor' => 12,
                'paid_installments' => 0,
                'status' => 'ACTIVE',
                'loanSource' => 'BMT_ITQAN',
                'startDate' => now()->format('Y-m-d'),
            ];
        }
    }

    public function saveLoan()
    {
        $this->validate([
            'loanForm.amount' => 'required|numeric|min:0',
            'loanForm.monthlyPayment' => 'required|numeric|min:0',
            'loanForm.tenor' => 'required|integer|min:1',
            'loanForm.paid_installments' => 'required|integer|min:0',
            'loanForm.status' => 'required|in:ACTIVE,PENDING,COMPLETED,REJECTED,Overdue',
            'loanForm.loanSource' => 'required|in:BERMADANI,BMT_ITQAN',
            'loanForm.startDate' => 'nullable|date',
        ]);

        if ($this->editingLoanId) {
            $loan = \App\Models\Loan::find($this->editingLoanId);
            $loan->update([
                'amount' => $this->loanForm['amount'],
                'monthlyPayment' => $this->loanForm['monthlyPayment'],
                'tenor' => $this->loanForm['tenor'],
                'paid_installments' => $this->loanForm['paid_installments'],
                'status' => $this->loanForm['status'],
                'loanSource' => $this->loanForm['loanSource'],
                'startDate' => $this->loanForm['startDate'],
            ]);
        } else {
            // New Loan
            $this->member->loans()->create([
                'amount' => $this->loanForm['amount'],
                'monthlyPayment' => $this->loanForm['monthlyPayment'],
                'tenor' => $this->loanForm['tenor'],
                'paid_installments' => $this->loanForm['paid_installments'],
                'remainingAmount' => max(0, $this->loanForm['amount'] - ($this->loanForm['monthlyPayment'] * $this->loanForm['paid_installments'])), // Rough estimate
                'status' => $this->loanForm['status'],
                'loanSource' => $this->loanForm['loanSource'],
                'startDate' => $this->loanForm['startDate'] ?? now(),
                'interestRate' => 0,
                'approvedAt' => now(),
                'approvedBy' => auth()->user()->name,
            ]);
        }

        $this->loanModalVisible = false;
        $this->loadMember(); // Reload relations

        $this->dispatch('notify', [
            'message' => 'Data pinjaman berhasil disimpan!',
            'type' => 'success'
        ]);
    }

    public function deleteLoan($loanId)
    {
        $loan = \App\Models\Loan::find($loanId);
        if ($loan) {
            $loan->delete();
            $this->loadMember();
            $this->dispatch('notify', [
                'message' => 'Data pinjaman dihapus!',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.member-edit', [
            'unitKerjaList' => $this->unitKerjaList,
        ]);
    }
}
