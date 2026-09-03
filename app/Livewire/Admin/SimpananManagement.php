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

    // Mode Audit & Quick Edit Admin
    public $auditMode = false;
    public $showEditPeriodModal = false;
    public $editPeriodKey = '';
    public $editPeriodMonthName = '';
    public $editPeriodType = 'WAJIB';
    public $editPeriodAmount = 50000;
    public $editPeriodNotes = '';

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

    // === Mode Audit Admin Methods ===
    public function toggleAuditMode(): void
    {
        $this->auditMode = !$this->auditMode;
        $statusText = $this->auditMode ? 'AKTIF' : 'NONAKTIF';
        session()->flash('message', "Mode Edit & Audit Admin sekarang {$statusText}. Klik kartu bulan untuk ubah status/nominal.");
    }

    public function quickToggleWajibPaid(string $periodKey, string $monthName): void
    {
        try {
            $amount = $this->member->monthly_simpanan_wajib > 0 ? (float)$this->member->monthly_simpanan_wajib : 50000;

            // Add transaction with billingMonth
            $memberService = app(MemberService::class);
            $trx = $memberService->addSimpanan(
                $this->memberId,
                'WAJIB',
                $amount,
                "Koreksi Audit Admin: Setor Wajib - {$monthName}",
                null,
                Auth::id()
            );

            // Update billingMonth on transaction if column exists
            DB::table('simpanan_transactions')
                ->where('id', $trx->id)
                ->update(['billingMonth' => $periodKey]);

            // Update bill status if bill exists
            DB::table('simpanan_bills')
                ->where('member_id', $this->memberId)
                ->where('billingMonth', $periodKey)
                ->where('type', 'WAJIB')
                ->update([
                    'paymentStatus' => 'PAID',
                    'paidAmount' => $amount,
                    'remainingAmount' => 0,
                    'paidAt' => now(),
                ]);

            $this->recalculateMemberBalances();
            $this->loadMember();
            $this->refreshUnpaidBills();

            session()->flash('message', "Bulan {$monthName} berhasil ditandai LUNAS (Rp " . number_format($amount, 0, ',', '.') . ").");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses setoran: ' . $e->getMessage());
        }
    }

    public function quickToggleWajibUnpaid(string $periodKey, string $monthName): void
    {
        try {
            // Find transactions for this period
            $txs = DB::table('simpanan_transactions')
                ->where('member_id', $this->memberId)
                ->where(function ($q) use ($periodKey, $monthName) {
                    $q->where('billingMonth', $periodKey)
                      ->orWhere(function ($sub) use ($monthName) {
                          $sub->where('type', 'WAJIB')
                              ->where('notes', 'like', "%{$monthName}%");
                      });
                })
                ->get();

            foreach ($txs as $tx) {
                DB::table('simpanan_transactions')->where('id', $tx->id)->delete();
            }

            // Reset bill status if bill exists
            DB::table('simpanan_bills')
                ->where('member_id', $this->memberId)
                ->where('billingMonth', $periodKey)
                ->where('type', 'WAJIB')
                ->update([
                    'paymentStatus' => 'UNPAID',
                    'paidAmount' => 0,
                    'remainingAmount' => DB::raw('amount'),
                    'paidAt' => null,
                ]);

            $this->recalculateMemberBalances();
            $this->loadMember();
            $this->refreshUnpaidBills();

            session()->flash('message', "Bulan {$monthName} berhasil ditandai BELUM BAYAR.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membatalkan setoran: ' . $e->getMessage());
        }
    }

    public function openEditPeriodModal(string $periodKey, string $monthName, string $type = 'WAJIB'): void
    {
        $this->editPeriodKey = $periodKey;
        $this->editPeriodMonthName = $monthName;
        $this->editPeriodType = $type;

        // Fetch current paid amount if exists
        $tx = DB::table('simpanan_transactions')
            ->where('member_id', $this->memberId)
            ->where(function ($q) use ($periodKey, $monthName, $type) {
                $q->where('billingMonth', $periodKey)
                  ->orWhere(function ($sub) use ($monthName, $type) {
                      $sub->where('type', $type)
                          ->where('notes', 'like', "%{$monthName}%");
                  });
            })
            ->first();

        $this->editPeriodAmount = $tx ? (float)$tx->amount : ($type === 'WAJIB' ? 50000 : 100000);
        $this->editPeriodNotes = $tx ? ($tx->notes ?? '') : "Koreksi Audit Admin: Setor {$type} - {$monthName}";
        $this->showEditPeriodModal = true;
    }

    public function closeEditPeriodModal(): void
    {
        $this->showEditPeriodModal = false;
        $this->reset(['editPeriodKey', 'editPeriodMonthName', 'editPeriodAmount', 'editPeriodNotes']);
    }

    public function saveEditPeriod(): void
    {
        $this->validate([
            'editPeriodAmount' => 'required|numeric|min:0',
        ]);

        try {
            $periodKey = $this->editPeriodKey;
            $monthName = $this->editPeriodMonthName;
            $type = $this->editPeriodType;
            $newAmount = (float) $this->editPeriodAmount;

            // Delete previous transactions for this period
            DB::table('simpanan_transactions')
                ->where('member_id', $this->memberId)
                ->where(function ($q) use ($periodKey, $monthName, $type) {
                    $q->where('billingMonth', $periodKey)
                      ->orWhere(function ($sub) use ($monthName, $type) {
                          $sub->where('type', $type)
                              ->where('notes', 'like', "%{$monthName}%");
                      });
                })
                ->delete();

            if ($newAmount > 0) {
                $memberService = app(MemberService::class);
                $trx = $memberService->addSimpanan(
                    $this->memberId,
                    $type,
                    $newAmount,
                    $this->editPeriodNotes ?: "Koreksi Audit Admin: Setor {$type} - {$monthName}",
                    null,
                    Auth::id()
                );

                DB::table('simpanan_transactions')
                    ->where('id', $trx->id)
                    ->update(['billingMonth' => $periodKey]);

                // Update bill if exists
                DB::table('simpanan_bills')
                    ->where('member_id', $this->memberId)
                    ->where('billingMonth', $periodKey)
                    ->where('type', $type)
                    ->update([
                        'paymentStatus' => 'PAID',
                        'paidAmount' => $newAmount,
                        'remainingAmount' => 0,
                        'paidAt' => now(),
                    ]);
            } else {
                // Set bill to unpaid if 0
                DB::table('simpanan_bills')
                    ->where('member_id', $this->memberId)
                    ->where('billingMonth', $periodKey)
                    ->where('type', $type)
                    ->update([
                        'paymentStatus' => 'UNPAID',
                        'paidAmount' => 0,
                        'remainingAmount' => DB::raw('amount'),
                        'paidAt' => null,
                    ]);
            }

            $this->recalculateMemberBalances();
            $this->loadMember();
            $this->refreshUnpaidBills();
            $this->closeEditPeriodModal();

            session()->flash('message', "Nominal setoran {$type} bulan {$monthName} berhasil diperbarui menjadi Rp " . number_format($newAmount, 0, ',', '.') . ".");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui nominal setoran: ' . $e->getMessage());
        }
    }

    public function recalculateMemberBalances(): void
    {
        try {
            $sumPokok = (float) DB::table('simpanan_transactions')
                ->where('member_id', $this->memberId)
                ->where('type', 'POKOK')
                ->sum('amount');

            $sumWajib = (float) DB::table('simpanan_transactions')
                ->where('member_id', $this->memberId)
                ->where('type', 'WAJIB')
                ->sum('amount');

            $sumSukarelaMasuk = (float) DB::table('simpanan_transactions')
                ->where('member_id', $this->memberId)
                ->where('type', 'SUKARELA')
                ->sum('amount');

            $sumSukarelaKeluar = (float) DB::table('simpanan_transactions')
                ->where('member_id', $this->memberId)
                ->where('type', 'TARIK_SUKARELA')
                ->sum('amount');

            $sumSukarela = max(0, $sumSukarelaMasuk - $sumSukarelaKeluar);

            // Also check payroll imports if any
            $importWajib = (float) DB::table('payroll_audit_loan_imports')
                ->where('member_id', $this->memberId)
                ->where('raw_uraian', 'like', '%WAJIB%')
                ->sum('amount');

            if ($importWajib > 0 && $sumWajib == 0) {
                $sumWajib += $importWajib;
            }

            DB::table('members')
                ->where('id', $this->memberId)
                ->update([
                    'simpananPokok' => $sumPokok,
                    'simpananWajib' => $sumWajib,
                    'simpananSukarela' => $sumSukarela,
                ]);

            $this->loadMember();
            session()->flash('message', '🎉 Total Saldo DB Anggota berhasil disinkronkan & dihitung ulang!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghitung ulang saldo: ' . $e->getMessage());
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
