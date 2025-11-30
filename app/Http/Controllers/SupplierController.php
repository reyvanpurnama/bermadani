<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRegistrationRequest;
use App\Services\SupplierService;
use Illuminate\Http\Request;

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
     * Handle supplier registration
     */
    public function register(SupplierRegistrationRequest $request)
    {
        try {
            $supplier = $this->supplierService->register($request->validated());

            return redirect()
                ->route('supplier.register')
                ->with('success', 'Pendaftaran berhasil! Silakan tunggu konfirmasi dari admin. Anda akan dihubungi melalui email: ' . $supplier->email);
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
            $supplier = $this->supplierService->approve($id, auth()->id());

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
}
