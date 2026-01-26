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

    // Step 1: Account (always new user)
    public $accountType = 'new';
    public $email;
    public $password = '12345678';

    // Step 2: Personal Info
    public $name;
    public $nim;
    public $phone;
    public $gender = 'MALE';
    public $unitKerja;
    public $address;

    // Step 3: Simpanan
    public $simpananPokok = 100000;
    public $simpananWajib = 50000;
    public $simpananSukarela = 0;
    public $buktiTransfer;

    protected $queryString = ['currentStep'];

    public function mount()
    {
        $this->currentStep = 1;
    }

    protected function rules()
    {
        $rules = [];

        // Step 1: Account validation (always new user)
        if ($this->currentStep === 1 || $this->currentStep === 4) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:6';
        }

        // Step 2: Personal info validation
        if ($this->currentStep === 2 || $this->currentStep === 4) {
            $rules['name'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['gender'] = 'required|in:MALE,FEMALE';
            $rules['unitKerja'] = 'nullable|string|max:255';
            $rules['address'] = 'nullable|string';
        }

        // Step 3: Simpanan validation
        if ($this->currentStep === 3 || $this->currentStep === 4) {
            $rules['simpananPokok'] = 'required|numeric|min:0';
            $rules['simpananWajib'] = 'required|numeric|min:0';
            $rules['simpananSukarela'] = 'nullable|numeric|min:0';
            $rules['buktiTransfer'] = 'nullable|image|max:2048';
        }

        return $rules;
    }

    public function nextStep()
    {
        try {
            $this->validate();

            if ($this->currentStep < 4) {
                $this->currentStep++;
            } else {
                $this->submit();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw untuk tampilkan error di UI
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
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
        if ($step >= 1 && $step <= 4) {
            $this->currentStep = $step;
        }
    }

    public function getTotalSimpananProperty()
    {
        return ($this->simpananPokok ?? 0) + ($this->simpananWajib ?? 0) + ($this->simpananSukarela ?? 0);
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
        try {
            $this->validate();
            
            DB::beginTransaction();

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
                'email' => $this->email,
                'password' => $this->password,
                'createNewUser' => true,
            ];

            // Handle bukti transfer upload
            if ($this->buktiTransfer) {
                $path = $this->buktiTransfer->store('bukti-simpanan', 'public');
                $data['buktiPokokPath'] = $path;
                $data['buktiWajibPath'] = $path;
                $data['buktiSukarelaPath'] = $path;
            }

            // Create member
            $result = $memberService->createMember($data);

            DB::commit();

            $message = 'Member berhasil didaftarkan dengan nomor anggota: ' . $result['memberKoperasi']->nomorAnggota;
            
            if (isset($result['memberMinimarket']) && $result['memberMinimarket']) {
                $message .= ' dan Member Minimarket: ' . $result['memberMinimarket']->memberNumber;
            }
            
            session()->flash('message', $message);
            
            return $this->redirect(route('admin.members.show', $result['memberKoperasi']->id));

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.member-create', [
            'unitKerjaList' => $this->unitKerjaList,
            'totalSimpanan' => $this->totalSimpanan,
        ]);
    }
}
