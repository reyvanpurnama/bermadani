<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Services\MemberService;
use App\Services\SimpananPaymentService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimpananManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $memberId;
    public $member;
    public $activeTab = 'wajib';

    // Modals
    public $showPokokModal = false;
    public $showWajibModal = false;
    public $showSetorModal = false;
    public $showTarikModal = false;

    // Form inputs
    public $pokokAmount = 200000;
    public $wajibAmount = 50000;
    public $setorAmount;
    public $tarikAmount;
    public $notes;
    public $buktiTransfer;
    public $selectedYear;

    // Bill payment inputs (migrated from PaymentForm)
    public $unpaidBills = [];
    public $selectedBills = [];
    public $paymentMethod = 'CASH';
    public $paymentDate;
    public $referenceNumber = '';
    public $paymentProofAttachment;
    public $paymentNotes = '';
    public $paymentTotalAmount = 0;
    public $paymentItemsCount = 0;

    protected $queryString = ['activeTab'];

    public function mount($id)
    {
        $this->memberId = $id;
        $this->selectedYear = (int) date('Y');
        $this->paymentDate = now()->format('Y-m-d');
        $this->loadMember();
        $this->refreshUnpaidBills();
    }

    public function loadMember()
    {
        $this->member = Member::with('user')->findOrFail($this->memberId);
    }

    public function refreshUnpaidBills(): void
    {
        $service = app(SimpananPaymentService::class);
        $bills = $service->getUnpaidBills($this->memberId);

        $this->unpaidBills = $bills->map(function ($bill) {
            return [
                'id' => $bill->id,
                'billingMonth' => $bill->billingMonth,
                'billingMonthFormatted' => $bill->billingMonth
                    ? Carbon::createFromFormat('Y-m', $bill->billingMonth)->translatedFormat('F Y')
                    : '-',
                'type' => $bill->type,
                'typeLabel' => $bill->typeLabel,
                'amount' => (float) $bill->amount,
                'paidAmount' => (float) $bill->paidAmount,
                'remainingAmount' => (float) $bill->remainingAmount,
                'paymentStatus' => $bill->paymentStatus,
            ];
        })->toArray();

        $validBillIds = collect($this->unpaidBills)->pluck('id')->all();
        $this->selectedBills = collect($this->selectedBills)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => in_array($id, $validBillIds))
            ->values()
            ->all();

        $this->calculatePaymentTotal();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Simpanan Pokok
    public function openPokokModal()
    {
        $this->showPokokModal = true;
        $this->pokokAmount = 200000;
        $this->notes = 'Setoran Simpanan Pokok';
    }

    public function closePokokModal()
    {
        $this->showPokokModal = false;
        $this->reset(['pokokAmount', 'notes', 'buktiTransfer']);
    }

    public function submitPokok()
    {
        $this->validate([
            'pokokAmount' => 'required|numeric|min:1',
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
                'POKOK',
                $this->pokokAmount,
                $this->notes ?? 'Setoran Simpanan Pokok',
                $buktiPath,
                Auth::id()
            );

            $this->loadMember();
            $this->refreshUnpaidBills();
            $this->closePokokModal();

            session()->flash('message', 'Setoran Simpanan Pokok sebesar Rp ' . number_format($this->pokokAmount, 0, ',', '.') . ' berhasil dicatat.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Simpanan Wajib
    public function openWajibModal($monthName = null, $periodKey = null)
    {
        $this->showWajibModal = true;
        $this->wajibAmount = 50000; // Default monthly amount
        if ($monthName) {
            $this->notes = 'Setoran Wajib - ' . $monthName;
        } else {
            $this->notes = 'Setoran Wajib Bulanan';
        }
    }

    public function closeWajibModal()
    {
        $this->showWajibModal = false;
        $this->reset(['wajibAmount', 'notes', 'buktiTransfer']);
    }

    public function changeYear($year)
    {
        $this->selectedYear = (int) $year;
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
            $this->refreshUnpaidBills();
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
            $this->refreshUnpaidBills();
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
            $this->refreshUnpaidBills();
            $this->closeTarikModal();

            session()->flash('message', 'Penarikan berhasil diproses.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatedSelectedBills(): void
    {
        $this->selectedBills = collect($this->selectedBills)
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $this->calculatePaymentTotal();
    }

    public function updatedPaymentMethod($value): void
    {
        if ($value !== 'TRANSFER') {
            $this->referenceNumber = '';
            $this->paymentProofAttachment = null;
        }
    }

    public function toggleAllPaymentBills(): void
    {
        if (count($this->selectedBills) === count($this->unpaidBills)) {
            $this->selectedBills = [];
        } else {
            $this->selectedBills = collect($this->unpaidBills)->pluck('id')->map(fn($id) => (int) $id)->all();
        }

        $this->calculatePaymentTotal();
    }

    private function calculatePaymentTotal(): void
    {
        $this->paymentItemsCount = count($this->selectedBills);
        $this->paymentTotalAmount = collect($this->unpaidBills)
            ->whereIn('id', $this->selectedBills)
            ->sum('remainingAmount');
    }

    private function paymentRules(): array
    {
        return [
            'selectedBills' => 'required|array|min:1',
            'paymentMethod' => 'required|in:CASH,TRANSFER,AUTO_DEBIT',
            'paymentDate' => 'required|date',
            'referenceNumber' => 'required_if:paymentMethod,TRANSFER|nullable|string|max:255',
            'paymentProofAttachment' => 'required_if:paymentMethod,TRANSFER|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'paymentNotes' => 'nullable|string|max:500',
        ];
    }

    public function processBillPayment()
    {
        $this->validate($this->paymentRules(), [
            'selectedBills.required' => 'Pilih minimal 1 tagihan untuk dibayar.',
            'selectedBills.min' => 'Pilih minimal 1 tagihan untuk dibayar.',
            'referenceNumber.required_if' => 'Nomor referensi wajib diisi untuk pembayaran transfer.',
            'paymentProofAttachment.required_if' => 'Bukti transfer wajib diupload.',
            'paymentProofAttachment.mimes' => 'Bukti transfer harus JPG, PNG, atau PDF.',
            'paymentProofAttachment.max' => 'Ukuran bukti transfer maksimal 2MB.',
        ]);

        DB::beginTransaction();

        try {
            $service = app(SimpananPaymentService::class);
            $receiptNumbers = [];

            foreach ($this->selectedBills as $billId) {
                $bill = collect($this->unpaidBills)->firstWhere('id', (int) $billId);
                if (!$bill) {
                    throw new \Exception('Tagihan yang dipilih tidak ditemukan atau sudah berubah.');
                }

                $result = $service->recordPayment([
                    'billId' => $billId,
                    'amount' => $bill['remainingAmount'],
                    'paymentMethod' => $this->paymentMethod,
                    'paymentDate' => $this->paymentDate,
                    'referenceNumber' => $this->referenceNumber ?: null,
                    'proofAttachment' => $this->paymentProofAttachment,
                    'notes' => $this->paymentNotes,
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }

                $receiptNumbers[] = $result['payment']->receiptNumber;
            }

            DB::commit();

            $this->loadMember();
            $this->selectedBills = [];
            $this->referenceNumber = '';
            $this->paymentProofAttachment = null;
            $this->paymentNotes = '';
            $this->refreshUnpaidBills();

            return redirect()->route('admin.payments.receipt', $receiptNumbers[0]);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses pembayaran tagihan: ' . $e->getMessage());
            $this->refreshUnpaidBills();
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

    public function getSimwaGridProperty()
    {
        if (!$this->member) return [];

        $grid = [];
        $today = Carbon::now();
        $joinDate = $this->member->joinDate ? Carbon::parse($this->member->joinDate)->startOfMonth() : $today->startOfMonth();
        $selectedYear = (int) ($this->selectedYear ?? date('Y'));

        $txs = SimpananTransaction::where('memberId', $this->memberId)
            ->where('status', 'APPROVED')
            ->get();

        $imports = DB::table('audit_simwa_imports')
            ->where('matched_member_id', $this->memberId)
            ->get();

        $monthsName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $currentMonthDate = Carbon::createFromDate($selectedYear, $i, 1)->endOfMonth();
            $periodKey = sprintf('%s-%02d', $selectedYear, $i);
            $monthName = $monthsName[$i];

            $hasTx = $txs->filter(function ($t) use ($selectedYear, $i, $periodKey, $monthName) {
                if ($t->billingMonth === $periodKey) return true;

                $dateMatch = ($t->created_at->format('Y') == $selectedYear && $t->created_at->format('n') == $i);
                $notesMatch = !empty($t->notes) && str_contains(strtolower($t->notes), strtolower($monthName)) && (str_contains($t->notes, (string)$selectedYear) || $t->created_at->format('Y') == $selectedYear);

                if ($t->type === 'WAJIB') {
                    return $dateMatch || $notesMatch;
                }

                if ($t->type === 'SUKARELA' && (!empty($t->notes) && (str_contains(strtolower($t->notes), 'payroll') || str_contains(strtolower($t->notes), 'tabungan')))) {
                    return $dateMatch || $notesMatch;
                }

                return false;
            })->first();

            $hasImport = $imports->filter(function ($imp) use ($periodKey) {
                return $imp->period === $periodKey && (
                    str_contains(strtoupper($imp->raw_uraian), 'SIMWA') || 
                    str_contains(strtoupper($imp->raw_uraian), 'TABUNGAN') || 
                    str_contains(strtoupper($imp->raw_uraian), 'SUKARELA')
                );
            })->first();

            $status = 'UNPAID';
            $paidDate = null;
            $paidAmount = 0;

            if ($hasTx) {
                $status = 'PAID';
                $paidDate = $hasTx->created_at->format('d M Y');
                $paidAmount = (float) $hasTx->amount;
            } elseif ($hasImport) {
                $status = 'PAID';
                $paidDate = 'Payroll / Import';
                $paidAmount = (float) $hasImport->amount;
            } elseif ($currentMonthDate->isFuture() && $currentMonthDate->format('Y-m') > $today->format('Y-m')) {
                $status = 'FUTURE';
            } elseif ($currentMonthDate->lt($joinDate)) {
                $status = 'NOT_MEMBER';
            }

            $grid[$i] = [
                'monthNum' => $i,
                'monthName' => Carbon::create()->month($i)->translatedFormat('M'),
                'fullName' => $monthName,
                'periodKey' => $periodKey,
                'status' => $status,
                'paidDate' => $paidDate,
                'paidAmount' => $paidAmount,
            ];
        }

        return $grid;
    }

    public function getAvailableYearsProperty()
    {
        $joinYear = $this->member->joinDate ? (int) Carbon::parse($this->member->joinDate)->format('Y') : (int) date('Y');
        $currentYear = (int) date('Y');
        $years = range(min($joinYear, 2024), max($currentYear + 1, 2026));
        rsort($years);
        return array_values(array_unique($years));
    }

    public function render()
    {
        return view('livewire.admin.simpanan-management', [
            'wajibTransactions' => $this->wajibTransactions,
            'sukarelaTransactions' => $this->sukarelaTransactions,
            'pokokTransactions' => $this->pokokTransactions,
            'simwaGrid' => $this->simwaGrid,
            'availableYears' => $this->availableYears,
        ]);
    }
}
