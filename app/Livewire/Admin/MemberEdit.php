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
    public $monthly_simpanan_wajib;
    public $monthly_sukarela_amount;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:MALE,FEMALE',
            'unitKerja' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'simwa_payment_method' => 'required|in:SALARY_DEDUCTION,MANUAL',
            'sukarela_payment_method' => 'required|in:SALARY_DEDUCTION,MANUAL',
            'monthly_simpanan_wajib' => 'required|numeric|min:0',
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
        $this->monthly_simpanan_wajib = $this->member->monthly_simpanan_wajib ?? 50000;
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
            // Update user name
            $this->member->user->update([
                'name' => $this->name,
            ]);

            // Update member info
            $this->member->update([
                'phone' => $this->phone,
                'gender' => $this->gender,
                'unitKerja' => $this->unitKerja,
                'address' => $this->address,
                'status' => $this->status,
                'simwa_payment_method' => $this->simwa_payment_method,
                'sukarela_payment_method' => $this->sukarela_payment_method,
                'monthly_simpanan_wajib' => $this->monthly_simpanan_wajib,
                'monthly_sukarela_amount' => $this->sukarela_payment_method === 'SALARY_DEDUCTION' 
                    ? $this->monthly_sukarela_amount 
                    : 0,
                'salary_deduction_consent_date' => ($this->simwa_payment_method === 'SALARY_DEDUCTION' || $this->sukarela_payment_method === 'SALARY_DEDUCTION')
                    ? now()->toDateString()
                    : null,
            ]);

            DB::commit();

            session()->flash('success', 'Data member berhasil diperbarui.');

            return redirect()->route('admin.members.show', $this->member->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.member-edit', [
            'unitKerjaList' => $this->unitKerjaList,
        ]);
    }
}
