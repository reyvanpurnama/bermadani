<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRegistrationRequest;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Show supplier registration form
     */
    public function showRegistrationForm()
    {
        return view('supplier.register');
    }

    /**
     * Show pending approval page for supplier
     */
    public function pending()
    {
        $supplier = auth('supplier')->user();
        
        if (!$supplier instanceof \App\Models\Supplier) {
            return redirect()->route('login')
                ->with('error', 'Akses ditolak. Silakan login sebagai supplier.');
        }

        // Jika sudah approved, redirect ke dashboard supplier
        if ($supplier->status === 'APPROVED' || $supplier->status === 'ACTIVE') {
            return redirect()->route('supplier.dashboard')
                ->with('success', 'Akun Anda sudah aktif!');
        }

        // Jika rejected, tampilkan halaman error atau pending dengan info rejected
        if ($supplier->status === 'REJECTED') {
            return view('supplier.pending', compact('supplier'))
                ->with('error', 'Pendaftaran Anda ditolak. Alasan: ' . ($supplier->rejectedReason ?? 'Tidak disebutkan'));
        }

        return view('supplier.pending', compact('supplier'));
    }

    /**
     * Handle supplier registration
     */
    public function register(SupplierRegistrationRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Handle file upload for payment proof
            if ($request->hasFile('registrationPaymentProof')) {
                $file = $request->file('registrationPaymentProof');
                $filename = 'payment_proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payment-proofs', $filename, 'public');
                $data['registrationPaymentProof'] = $path;
                $data['registrationPaymentStatus'] = 'PENDING_VERIFICATION';
            } else {
                $data['registrationPaymentStatus'] = 'UNPAID';
            }
            
            $supplier = $this->supplierService->register($data);
            
            // Auto-login supplier dengan guard 'supplier'
            Auth::guard('supplier')->login($supplier);
            $request->session()->regenerate();

            return redirect()
                ->route('supplier.pending')
                ->with('success', 'Pendaftaran berhasil! Bukti pembayaran Anda sedang diverifikasi oleh admin.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve supplier (Admin only)
     */
    public function approve(Request $request, $id)
    {
        try {
            /** @var int $adminId */
            $adminId = Auth::id();
            $supplier = $this->supplierService->approve($id, $adminId);

            return redirect()
                ->back()
                ->with('success', 'Supplier berhasil disetujui. Email: ' . $supplier->email);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject supplier (Admin only)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $supplier = $this->supplierService->reject($id, $request->reason);

            return redirect()
                ->back()
                ->with('success', 'Supplier ditolak dengan alasan: ' . $request->reason);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Suspend supplier (Admin only)
     */
    public function suspend(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $supplier = $this->supplierService->suspend($id, $request->reason);

            return redirect()
                ->back()
                ->with('success', 'Supplier berhasil disuspend.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Activate supplier (Admin only)
     */
    public function activate($id)
    {
        try {
            $supplier = $this->supplierService->activate($id);

            return redirect()
                ->back()
                ->with('success', 'Supplier berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Verify registration payment (Admin only)
     */
    public function verifyPayment($id)
    {
        try {
            $supplier = \App\Models\Supplier::findOrFail($id);
            
            $supplier->update([
                'registrationPaymentStatus' => 'VERIFIED',
                'registrationPaymentVerifiedAt' => now(),
                'registrationPaymentVerifiedBy' => Auth::id(),
                'status' => 'PENDING', // Ubah ke PENDING untuk menunggu approval data
            ]);

            return redirect()
                ->back()
                ->with('success', 'Pembayaran berhasil diverifikasi! Supplier sekarang menunggu approval data.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject registration payment (Admin only)
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $supplier = \App\Models\Supplier::findOrFail($id);
            
            $supplier->update([
                'registrationPaymentStatus' => 'REJECTED',
                'rejectedReason' => $request->reason,
                'status' => 'REJECTED',
            ]);

            return redirect()
                ->back()
                ->with('success', 'Pembayaran ditolak dengan alasan: ' . $request->reason);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
