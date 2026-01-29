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

    public function render()
    {
        return view('livewire.membership.profile');
    }
}
