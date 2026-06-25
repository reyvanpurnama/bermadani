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
    public $simpananPokokOption = 'LUNAS'; // Default lunas
    public $simpananWajib = 50000;  // Optional/Default 50k
    public $simpananSukarela = 0;
    public $buktiTransfer;

    protected $queryString = ['currentStep'];

    public function mount()
    {
        $this->currentStep = 1;
        $this->simpananPokokOption = 'LUNAS';
    }

    public function updatedSimpananPokokOption($value)
    {
        if ($value === 'CICIL_4X') {
            $this->simpananPokok = 50000;
        } else {
            $this->simpananPokok = 200000;
        }
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
            $minPokok = $this->simpananPokokOption === 'CICIL_4X' ? 50000 : 200000;
            $rules['simpananPokok'] = 'required|numeric|min:' . $minPokok;
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
            // Fix: unitKerja cannot be null in DB, use '-' as default
            $data = [
                'name' => $this->name,
                'phone' => $phoneToUse,
                'gender' => $this->gender,
                'unitKerja' => $this->unitKerja ?: '-', // Default to strip if empty
                'address' => $this->address ?: '-',     // Default to strip if empty
                'simpananPokok' => $this->simpananPokok,
                'simpananWajib' => $this->simpananWajib,
                'simpananSukarela' => $this->simpananSukarela,
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
            $memberKoperasi = $result['memberKoperasi'];

            // If choosing installment, create remaining 3 bills
            if ($this->simpananPokokOption === 'CICIL_4X') {
                $startMonth = now();
                for ($i = 2; $i <= 4; $i++) {
                    $nextMonth = $startMonth->copy()->addMonths($i - 1);
                    \App\Models\SimpananTransaction::create([
                        'memberId' => $memberKoperasi->id,
                        'type' => 'POKOK',
                        'transactionType' => 'SETOR',
                        'amount' => 50000,
                        'paidAmount' => 0,
                        'billingMonth' => $nextMonth->format('Y-m'),
                        'billStatus' => 'APPROVED',
                        'status' => 'APPROVED',
                        'balanceAfter' => 0,
                        'notes' => "Cicilan Simpanan Pokok Ke-{$i} dari 4",
                        'processedBy' => auth()->id() ?? 1,
                    ]);
                }
            }

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
