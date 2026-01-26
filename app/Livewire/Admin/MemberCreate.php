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

    // Step 1: Personal Info
    public $name;
    public $nim;
    public $phone;
    public $gender = 'MALE';
    public $unitKerja;
    public $address;

    // Step 2: Simpanan
    public $simpananPokok = 200000; // Mandatory 200k
    public $simpananWajib = 50000;  // Optional/Default 50k
    public $simpananSukarela = 0;
    public $buktiTransfer;

    protected $queryString = ['currentStep'];

    public function mount()
    {
        $this->currentStep = 1;
        // Defaults are already set
    }

    protected function rules()
    {
        $rules = [];

        // Step 1: Personal info validation
        if ($this->currentStep === 1 || $this->currentStep === 3) {
            $rules['name'] = 'required|string|max:255';
            $rules['phone'] = 'nullable|string|max:20'; // CHANGED: Optional
            $rules['gender'] = 'required|in:MALE,FEMALE';
            $rules['unitKerja'] = 'nullable|string|max:255';
            $rules['address'] = 'nullable|string';
        }

        // Step 2: Simpanan validation
        if ($this->currentStep === 2 || $this->currentStep === 3) {
            $rules['simpananPokok'] = 'required|numeric|min:200000'; // Must be at least 200k
            $rules['simpananWajib'] = 'nullable|numeric|min:0';      // Optional
            $rules['simpananSukarela'] = 'nullable|numeric|min:0';
            $rules['buktiTransfer'] = 'nullable|image|max:2048';
        }

        return $rules;
    }

    public function nextStep()
    {
        try {
            $this->validate();

            if ($this->currentStep < 3) {
                $this->currentStep++;
            } else {
                $this->submit();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
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
        if ($step >= 1 && $step <= 3) {
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

            // Handle Dummy Phone if empty (Quick Add)
            $phoneToUse = $this->phone;
            if (empty($phoneToUse)) {
                $phoneToUse = '000' . time() . rand(10, 99);
            }

            // Prepare data - Auto Email/Pass handled by Service
            $data = [
                'name' => $this->name,
                'phone' => $phoneToUse,
                'gender' => $this->gender,
                'unitKerja' => $this->unitKerja,
                'address' => $this->address,
                'simpananPokok' => $this->simpananPokok,
                'simpananWajib' => $this->simpananWajib,
                'simpananSukarela' => $this->simpananSukarela,
                // 'email' => removed, let service auto-gen ex: [ID]@bermadani.id
                // 'password' => removed, let service default to 'password'
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
