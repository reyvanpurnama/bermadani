<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\MemberService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MemberCreate extends Component
{
    use WithFileUploads;

    // Wizard state
    public $currentStep = 1;

    // Step 1: Account
    public $accountType = 'existing';
    public $existingUserId;
    public $email;
    public $username;
    public $password = '12345678';

    // Step 2: Personal Info
    public $name;
    public $nim;
    public $phone;
    public $gender = 'M';
    public $unitKerja;
    public $address;

    // Step 3: Simpanan
    public $simpananPokok = 100000;
    public $simpananWajib = 50000;
    public $simpananSukarela = 0;
    public $buktiTransfer;

    protected $queryString = ['currentStep'];

    protected function rules()
    {
        $rules = [];

        if ($this->currentStep === 1) {
            if ($this->accountType === 'existing') {
                $rules['existingUserId'] = 'required|exists:users,id';
            } else {
                $rules['email'] = 'required|email|unique:users,email';
                $rules['username'] = 'required|string|min:3|unique:users,username';
                $rules['password'] = 'required|string|min:6';
            }
        }

        if ($this->currentStep === 2) {
            $rules['name'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['gender'] = 'required|in:M,F';
            $rules['unitKerja'] = 'nullable|string|max:255';
            $rules['address'] = 'nullable|string';
        }

        if ($this->currentStep === 3) {
            $rules['simpananPokok'] = 'required|numeric|min:0';
            $rules['simpananWajib'] = 'required|numeric|min:0';
            $rules['simpananSukarela'] = 'nullable|numeric|min:0';
            $rules['buktiTransfer'] = 'nullable|image|max:2048';
        }

        return $rules;
    }

    public function mount()
    {
        $this->currentStep = 1;
    }

    public function updatedAccountType()
    {
        $this->reset(['existingUserId', 'email', 'username']);
    }

    public function nextStep()
    {
        $this->validate();

        if ($this->currentStep < 4) {
            $this->currentStep++;
        } else {
            $this->submit();
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        // Only allow going to visited steps or next step
        if ($step >= 1 && $step <= 4) {
            $this->currentStep = $step;
        }
    }

    public function getTotalSimpananProperty()
    {
        return ($this->simpananPokok ?? 0) + ($this->simpananWajib ?? 0) + ($this->simpananSukarela ?? 0);
    }

    public function getExistingUsersProperty()
    {
        return User::whereDoesntHave('member')
            ->orderBy('name')
            ->get();
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

    public function submit()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $memberService = app(MemberService::class);

            // Prepare data
            $data = [
                'name' => $this->name,
                'phone' => $this->phone,
                'gender' => $this->gender,
                'unitKerja' => $this->unitKerja,
                'address' => $this->address,
                'simpananPokok' => $this->simpananPokok,
                'simpananWajib' => $this->simpananWajib,
                'simpananSukarela' => $this->simpananSukarela,
            ];

            // Handle user
            if ($this->accountType === 'existing') {
                $data['userId'] = $this->existingUserId;
            } else {
                $data['email'] = $this->email;
                $data['username'] = $this->username;
                $data['password'] = $this->password;
            }

            // Handle bukti transfer upload
            if ($this->buktiTransfer) {
                $path = $this->buktiTransfer->store('bukti-simpanan', 'public');
                $data['buktiPath'] = $path;
            }

            // Create member
            $member = $memberService->createMember($data);

            DB::commit();

            session()->flash('message', 'Member berhasil didaftarkan dengan nomor anggota: ' . $member->nomorAnggota);

            return redirect()->route('admin.members.show', $member->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.member-create', [
            'existingUsers' => $this->existingUsers,
            'unitKerjaList' => $this->unitKerjaList,
            'totalSimpanan' => $this->totalSimpanan,
        ]);
    }
}
