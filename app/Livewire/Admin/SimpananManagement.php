<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Services\MemberService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class SimpananManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $memberId;
    public $member;
    public $activeTab = 'wajib';

    // Modals
    public $showWajibModal = false;
    public $showSetorModal = false;
    public $showTarikModal = false;

    // Form inputs
    public $wajibAmount = 50000;
    public $setorAmount;
    public $tarikAmount;
    public $notes;
    public $buktiTransfer;

    protected $queryString = ['activeTab'];

    public function mount($id)
    {
        $this->memberId = $id;
        $this->loadMember();
    }

    public function loadMember()
    {
        $this->member = Member::with('user')->findOrFail($this->memberId);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Simpanan Wajib
    public function openWajibModal()
    {
        $this->showWajibModal = true;
        $this->wajibAmount = 50000; // Default monthly amount
    }

    public function closeWajibModal()
    {
        $this->showWajibModal = false;
        $this->reset(['wajibAmount', 'notes', 'buktiTransfer']);
    }

    public function submitWajib()
    {
        $this->validate([
            'wajibAmount' => 'required|numeric|min:1',
            'buktiTransfer' => 'nullable|image|max:2048',
        ]);

        try {
            $memberService = app(MemberService::class);

            $buktiPath = null;
            if ($this->buktiTransfer) {
                $buktiPath = $this->buktiTransfer->store('bukti-simpanan', 'public');
            }

            $memberService->addSimpanan(
                $this->memberId,
                'WAJIB',
                $this->wajibAmount,
                $this->notes ?? 'Setoran Wajib Bulanan',
                $buktiPath,
                Auth::id()
            );

            $this->loadMember();
            $this->closeWajibModal();

            session()->flash('message', 'Setoran Wajib berhasil dicatat.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Simpanan Sukarela - Setor
    public function openSetorModal()
    {
        $this->showSetorModal = true;
    }

    public function closeSetorModal()
    {
        $this->showSetorModal = false;
        $this->reset(['setorAmount', 'notes', 'buktiTransfer']);
    }

    public function submitSetor()
    {
        $this->validate([
            'setorAmount' => 'required|numeric|min:1',
            'buktiTransfer' => 'nullable|image|max:2048',
        ]);

        try {
            $memberService = app(MemberService::class);

            $buktiPath = null;
            if ($this->buktiTransfer) {
                $buktiPath = $this->buktiTransfer->store('bukti-simpanan', 'public');
            }

            $memberService->addSimpanan(
                $this->memberId,
                'SUKARELA',
                $this->setorAmount,
                $this->notes ?? 'Setoran Sukarela',
                $buktiPath,
                Auth::id()
            );

            $this->loadMember();
            $this->closeSetorModal();

            session()->flash('message', 'Setoran Sukarela berhasil dicatat.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Simpanan Sukarela - Tarik
    public function openTarikModal()
    {
        $this->showTarikModal = true;
    }

    public function closeTarikModal()
    {
        $this->showTarikModal = false;
        $this->reset(['tarikAmount', 'notes']);
    }

    public function submitTarik()
    {
        $this->validate([
            'tarikAmount' => 'required|numeric|min:1|max:' . $this->member->simpananSukarela,
        ], [
            'tarikAmount.max' => 'Jumlah penarikan melebihi saldo tersedia (Rp ' . number_format((float)$this->member->simpananSukarela, 0, ',', '.') . ')',
        ]);

        try {
            $memberService = app(MemberService::class);

            $memberService->withdrawSimpanan(
                $this->memberId,
                $this->tarikAmount,
                $this->notes ?? 'Penarikan Sukarela',
                false // Auto-approve for admin
            );

            $this->loadMember();
            $this->closeTarikModal();

            session()->flash('message', 'Penarikan berhasil diproses.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getWajibTransactionsProperty()
    {
        return SimpananTransaction::where('memberId', $this->memberId)
            ->where('type', 'WAJIB')
            ->with('processor')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getSukarelaTransactionsProperty()
    {
        return SimpananTransaction::where('memberId', $this->memberId)
            ->where('type', 'SUKARELA')
            ->with('processor')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getPokokTransactionsProperty()
    {
        return SimpananTransaction::where('memberId', $this->memberId)
            ->where('type', 'POKOK')
            ->with('processor')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.simpanan-management', [
            'wajibTransactions' => $this->wajibTransactions,
            'sukarelaTransactions' => $this->sukarelaTransactions,
            'pokokTransactions' => $this->pokokTransactions,
        ]);
    }
}
