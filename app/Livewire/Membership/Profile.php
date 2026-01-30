<?php

namespace App\Livewire\Membership;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.membership')]
class Profile extends Component
{
    public $member;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $unitKerja;

    // Password change
    public $currentPassword = '';
    public $newPassword = '';
    public $newPassword_confirmation = '';

    // Simpanan Configuration (Retail only uses Sukarela)
    public $monthly_sukarela_amount;
    public $sukarela_payment_method;

    public function mount()
    {
        $user = auth()->user();
        $this->member = Member::where('userId', $user->id)->first();

        if ($this->member) {
            $this->name = $this->member->name;
            $this->email = $this->member->email;
            $this->phone = $this->member->phone;
            $this->address = $this->member->address;
            $this->unitKerja = $this->member->unitKerja;

            // Initialize Financial Settings
            $this->monthly_sukarela_amount = $this->member->monthly_sukarela_amount;
            $this->sukarela_payment_method = $this->member->sukarela_payment_method;
        }
    }

    public function updateProfile()
    {
        $this->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'unitKerja' => 'nullable|string|max:255',
        ]);

        $this->member->update([
            'phone' => $this->phone,
            'address' => $this->address,
            'unitKerja' => $this->unitKerja,
        ]);

        session()->flash('success', 'Profil berhasil diperbarui!');
    }

    public function updateSimpananSettings()
    {
        $this->validate([
            'monthly_sukarela_amount' => 'nullable|numeric|min:0',
            'sukarela_payment_method' => 'nullable|in:SALARY_DEDUCTION,MANUAL',
        ]);

        // Logic: specific to retail member simplicity
        $this->member->update([
            'sukarela_payment_method' => $this->sukarela_payment_method,
            'monthly_sukarela_amount' => ($this->sukarela_payment_method === 'SALARY_DEDUCTION')
                ? ($this->monthly_sukarela_amount ?? 0)
                : 0,
            'salary_deduction_consent_date' => ($this->sukarela_payment_method === 'SALARY_DEDUCTION')
                ? now()
                : $this->member->salary_deduction_consent_date,
        ]);

        session()->flash('success', 'Konfigurasi simpanan berhasil disimpan!');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Password saat ini salah');
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPassword_confirmation']);
        session()->flash('success', 'Password berhasil diubah!');
    }

    public function render()
    {
        return view('livewire.membership.profile');
    }
}
