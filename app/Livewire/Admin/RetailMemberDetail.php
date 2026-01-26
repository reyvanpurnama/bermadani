<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RetailMemberDetail extends Component
{
    public $memberId;
    public $member;

    // Editable Profile Fields
    public $name;
    public $phone;
    public $gender;
    public $unitKerja;
    public $address;
    public $status; // ACTIVE, INACTIVE

    // Payment Preferences for Saldo Bermadani (Sukarela)
    public $sukarela_payment_method; // MANUAL or SALARY_DEDUCTION
    public $monthly_sukarela_amount;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'gender' => 'required|in:MALE,FEMALE',
        'unitKerja' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
        'sukarela_payment_method' => 'nullable|in:SALARY_DEDUCTION,MANUAL',
        'monthly_sukarela_amount' => 'nullable|numeric|min:0',
    ];

    public function mount($memberId)
    {
        $this->memberId = $memberId;
        $this->loadMember();
    }

    public function loadMember()
    {
        $this->member = Member::with('user')->findOrFail($this->memberId);

        // Populate Form
        $this->name = $this->member->user->name;
        $this->phone = $this->member->phone;
        $this->gender = $this->member->gender;
        $this->unitKerja = $this->member->unitKerja;
        $this->address = $this->member->address;
        $this->status = $this->member->status;

        $this->sukarela_payment_method = $this->member->sukarela_payment_method;
        $this->monthly_sukarela_amount = $this->member->monthly_sukarela_amount ?? 0;
    }

    public function updateProfile()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $this->member->user->update(['name' => $this->name]);

            $this->member->update([
                'phone' => $this->phone,
                'gender' => $this->gender,
                'unitKerja' => $this->unitKerja,
                'address' => $this->address,
                'status' => $this->status,
                'sukarela_payment_method' => $this->sukarela_payment_method ?: null,
                'monthly_sukarela_amount' => ($this->sukarela_payment_method === 'SALARY_DEDUCTION')
                    ? ($this->monthly_sukarela_amount ?: 0)
                    : 0,
                // Auto consent date if using salary deduction
                'salary_deduction_consent_date' => ($this->sukarela_payment_method === 'SALARY_DEDUCTION')
                    ? now()->toDateString()
                    : $this->member->salary_deduction_consent_date,
            ]);

            DB::commit();

            $this->dispatch('notify', [
                'message' => 'Profil member retail berhasil diperbarui.',
                'type' => 'success'
            ]);

            $this->loadMember(); // Refresh

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
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

    public function render()
    {
        return view('livewire.admin.retail-member-detail', [
            'unitKerjaList' => $this->unitKerjaList,
        ])->layout('layouts.admin');
    }
}
