<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.member')]
class Profile extends Component
{
    public $member;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $unitKerja;
    // public $birthDate; // Column not exists in production

    // Password change
    public $currentPassword = '';
    public $newPassword = '';
    public $newPassword_confirmation = '';

    // Simpanan Configuration
    public $monthly_simpanan_wajib;
    public $simwa_payment_method;
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
            $this->monthly_simpanan_wajib = $this->member->monthly_simpanan_wajib;
            $this->simwa_payment_method = $this->member->simwa_payment_method;
            $this->monthly_sukarela_amount = $this->member->monthly_sukarela_amount;
            $this->sukarela_payment_method = $this->member->sukarela_payment_method;
        }
    }

    public function updateProfile()
    {
        $this->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'unitKerja' => 'required|string|max:255',
        ]);

        $this->member->update([
            'phone' => $this->phone,
            'address' => $this->address,
            'unitKerja' => $this->unitKerja,
        ]);

        session()->flash('success', 'Profil berhasil diperbarui!');
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

    public function updateSimpananSettings()
    {
        $this->validate([
            'monthly_simpanan_wajib' => 'required|numeric|min:10000',
            'simwa_payment_method' => 'required|in:SALARY_DEDUCTION,MANUAL,AUTO_DEBIT',
            'monthly_sukarela_amount' => 'required|numeric|min:0',
            'sukarela_payment_method' => 'required|in:SALARY_DEDUCTION,MANUAL,AUTO_DEBIT',
        ]);

        $this->member->update([
            'monthly_simpanan_wajib' => $this->monthly_simpanan_wajib,
            'simwa_payment_method' => $this->simwa_payment_method,
            'monthly_sukarela_amount' => $this->monthly_sukarela_amount,
            'sukarela_payment_method' => $this->sukarela_payment_method,
        ]);

        session()->flash('success', 'Pengaturan simpanan berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.member.profile');
    }
}
