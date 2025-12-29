<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Member;
use App\Services\SimpananPaymentService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentForm extends Component
{
    use WithFileUploads;

    // Data Master
    public $members;
    public $selectedMemberId = '';
    public $selectedMember = null;
    
    // Unpaid Bills Data
    public $unpaidBills = [];
    public $selectedBills = [];
    
    // Payment Details
    public $paymentMethod = 'CASH';
    public $paymentDate;
    public $referenceNumber = '';
    public $proofAttachment;
    public $notes = '';
    
    // Computed Values
    public $totalAmount = 0;
    public $itemsCount = 0;

    protected $rules = [
        'selectedMemberId' => 'required|exists:members,id',
        'selectedBills' => 'required|array|min:1',
        'paymentMethod' => 'required|in:CASH,TRANSFER,AUTO_DEBIT',
        'paymentDate' => 'required|date',
        'referenceNumber' => 'required_if:paymentMethod,TRANSFER|nullable|string',
        'proofAttachment' => 'required_if:paymentMethod,TRANSFER|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'selectedMemberId.required' => 'Pilih anggota terlebih dahulu',
        'selectedBills.required' => 'Pilih minimal 1 tagihan untuk dibayar',
        'selectedBills.min' => 'Pilih minimal 1 tagihan untuk dibayar',
        'referenceNumber.required_if' => 'Nomor referensi wajib diisi untuk pembayaran transfer',
        'proofAttachment.required_if' => 'Bukti transfer wajib diupload',
        'proofAttachment.mimes' => 'File harus berformat JPG, PNG, atau PDF',
        'proofAttachment.max' => 'Ukuran file maksimal 2MB',
    ];

    public function mount()
    {
        $this->members = Member::select('id', 'name', 'memberNumber', 'unitKerja')
            ->orderBy('name')
            ->get();
        $this->paymentDate = now()->format('Y-m-d');
    }

    /**
     * Load unpaid bills saat pilih anggota
     */
    public function updatedSelectedMemberId($memberId)
    {
        $this->reset(['unpaidBills', 'selectedBills', 'selectedMember']);
        $this->calculateTotal();

        if (!$memberId) return;

        $this->selectedMember = Member::find($memberId);

        $service = app(SimpananPaymentService::class);
        $bills = $service->getUnpaidBills($memberId);

        $this->unpaidBills = $bills->map(function($bill) {
            return [
                'id' => $bill->id,
                'billingMonth' => $bill->billingMonth,
                'billingMonthFormatted' => Carbon::createFromFormat('Y-m', $bill->billingMonth)->translatedFormat('F Y'),
                'type' => $bill->type,
                'typeLabel' => $bill->typeLabel,
                'amount' => $bill->amount,
                'paidAmount' => $bill->paidAmount,
                'remainingAmount' => $bill->remainingAmount,
                'paymentStatus' => $bill->paymentStatus,
            ];
        })->toArray();
    }

    /**
     * Real-time calculate total saat centang bills
     */
    public function updatedSelectedBills()
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->itemsCount = count($this->selectedBills);
        
        $this->totalAmount = collect($this->unpaidBills)
            ->whereIn('id', $this->selectedBills)
            ->sum('remainingAmount');
    }

    /**
     * Toggle semua bills (select all)
     */
    public function toggleAllBills()
    {
        if (count($this->selectedBills) === count($this->unpaidBills)) {
            $this->selectedBills = [];
        } else {
            $this->selectedBills = collect($this->unpaidBills)->pluck('id')->toArray();
        }
        $this->calculateTotal();
    }

    /**
     * Process pembayaran - support multiple bills sekaligus
     */
    public function processPayment()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $service = app(SimpananPaymentService::class);
            $receiptNumbers = [];

            // Process setiap bill yang dipilih
            foreach ($this->selectedBills as $billId) {
                $bill = collect($this->unpaidBills)->firstWhere('id', $billId);
                
                $result = $service->recordPayment([
                    'billId' => $billId,
                    'amount' => $bill['remainingAmount'], // Bayar full sisa tagihan
                    'paymentMethod' => $this->paymentMethod,
                    'paymentDate' => $this->paymentDate,
                    'referenceNumber' => $this->referenceNumber ?: null,
                    'proofAttachment' => $this->proofAttachment,
                    'notes' => $this->notes,
                ]);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }

                $receiptNumbers[] = $result['payment']->receiptNumber;
            }

            DB::commit();

            session()->flash('payment_success', [
                'receipts' => $receiptNumbers,
                'total' => $this->totalAmount,
                'count' => count($receiptNumbers),
                'member' => $this->selectedMember->name,
            ]);

            // Redirect ke kuitansi pertama
            return redirect()->route('admin.payments.receipt', $receiptNumbers[0]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.payment-form')
            ->layout('layouts.admin', [
                'title' => 'Input Pembayaran Simpanan'
            ]);
    }
}
