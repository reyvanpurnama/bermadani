<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class SupplierManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $showRejectModal = false;
    public $showSuspendModal = false;
    public $showRejectPaymentModal = false;
    public $showDetailModal = false;
    public $selectedSupplierId = null;
    public $selectedSupplier = null;
    public $rejectReason = '';
    public $suspendReason = '';
    public $rejectPaymentReason = '';

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterStatus']);
    }

    public function openDetailModal($supplierId)
    {
        $this->selectedSupplier = Supplier::with('products')->findOrFail($supplierId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedSupplier = null;
    }

    public function openRejectModal($supplierId)
    {
        $this->selectedSupplierId = $supplierId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedSupplierId = null;
        $this->rejectReason = '';
    }

    public function openSuspendModal($supplierId)
    {
        $this->selectedSupplierId = $supplierId;
        $this->suspendReason = '';
        $this->showSuspendModal = true;
    }

    public function closeSuspendModal()
    {
        $this->showSuspendModal = false;
        $this->selectedSupplierId = null;
        $this->suspendReason = '';
    }

    public function openRejectPaymentModal($supplierId)
    {
        $this->selectedSupplierId = $supplierId;
        $this->rejectPaymentReason = '';
        $this->showRejectPaymentModal = true;
    }

    public function closeRejectPaymentModal()
    {
        $this->showRejectPaymentModal = false;
        $this->selectedSupplierId = null;
        $this->rejectPaymentReason = '';
    }

    public function verifyPayment($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            
            $supplier->update([
                'registrationPaymentStatus' => 'VERIFIED',
                'registrationPaymentVerifiedAt' => now(),
                'registrationPaymentVerifiedBy' => Auth::id(),
                'status' => 'PENDING', // Ubah ke PENDING untuk menunggu approval data
            ]);

            session()->flash('success', 'Pembayaran berhasil diverifikasi! Supplier sekarang menunggu approval data.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPayment()
    {
        $this->validate([
            'rejectPaymentReason' => 'required|string|max:500',
        ]);

        try {
            $supplier = Supplier::findOrFail($this->selectedSupplierId);
            
            $supplier->update([
                'registrationPaymentStatus' => 'REJECTED',
                'rejectedReason' => $this->rejectPaymentReason,
                'status' => 'REJECTED',
            ]);

            session()->flash('success', 'Pembayaran ditolak.');
            $this->closeRejectPaymentModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            
            $supplier->update([
                'status' => 'ACTIVE',
                'isActive' => true,
                'isPaymentActive' => true,
                'approvedAt' => now(),
                'approvedById' => Auth::id(),
            ]);

            // Activate user account (if linked)
            \App\Models\User::where('email', $supplier->email)->update([
                'isActive' => true,
            ]);

            session()->flash('success', 'Supplier berhasil disetujui dan diaktifkan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject()
    {
        $this->validate([
            'rejectReason' => 'required|string|max:500',
        ]);

        try {
            $supplier = Supplier::findOrFail($this->selectedSupplierId);
            
            $supplier->update([
                'status' => 'REJECTED',
                'rejectedReason' => $this->rejectReason,
            ]);

            session()->flash('success', 'Supplier ditolak.');
            $this->closeRejectModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function suspend()
    {
        $this->validate([
            'suspendReason' => 'nullable|string|max:500',
        ]);

        try {
            $supplier = Supplier::findOrFail($this->selectedSupplierId);
            
            $supplier->update([
                'status' => 'SUSPENDED',
                'isSuspendedForPayment' => true,
                'suspendedAt' => now(),
                'suspensionReason' => $this->suspendReason,
            ]);

            // Deactivate user account
            \App\Models\User::where('email', $supplier->email)->update([
                'isActive' => false,
            ]);

            session()->flash('success', 'Supplier berhasil disuspend.');
            $this->closeSuspendModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function activate($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            
            $supplier->update([
                'status' => 'ACTIVE',
                'isActive' => true,
                'isSuspendedForPayment' => false,
                'suspendedAt' => null,
                'suspensionReason' => null,
            ]);

            // Activate user account
            \App\Models\User::where('email', $supplier->email)->update([
                'isActive' => true,
            ]);

            session()->flash('success', 'Supplier berhasil diaktifkan kembali!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getSuppliersProperty()
    {
        return Supplier::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('businessName', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('ownerName', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('code', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($query) => $query->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    public function getStatsProperty()
    {
        return [
            'total' => Supplier::count(),
            'pending' => Supplier::where('status', 'PENDING')->count(),
            'active' => Supplier::where('status', 'ACTIVE')->count(),
            'suspended' => Supplier::where('status', 'SUSPENDED')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.supplier-management', [
            'suppliers' => $this->suppliers,
            'stats' => $this->stats,
        ]);
    }
}
