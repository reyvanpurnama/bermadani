<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupplierService
{
    /**
     * Register new supplier
     * 
     * @param array $data
     * @return Supplier
     * @throws \Exception
     */
    public function register(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            // Generate unique supplier code
            $code = $this->generateSupplierCode();

            // Create user account for supplier
            $user = User::create([
                'name' => $data['ownerName'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'SUPPLIER',
                'isActive' => false, // Inactive until approved
            ]);

            // Create supplier record
            $supplier = Supplier::create([
                'code' => $code,
                'ownerName' => $data['ownerName'],
                'businessName' => $data['businessName'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'description' => $data['description'] ?? null,
                'productCategory' => $data['productCategory'] ?? null,
                'password' => $data['password'], // Will be hashed by model mutator
                'status' => 'PENDING', // Default status
                'monthlyFee' => 25000, // Default fee
                'maxActiveProducts' => 10, // Default limit
                'currentActiveProducts' => 0,
                'paymentGraceDays' => 7,
                'isPaymentActive' => false,
                'isSuspendedForPayment' => false,
                'isActive' => false,
            ]);

            // Link user to supplier (optional: add userId column to suppliers table)
            // Or use email as the link

            return $supplier;
        });
    }

    /**
     * Generate unique supplier code
     * Format: SUP-YYYYMMDD-XXX
     * 
     * @return string
     */
    private function generateSupplierCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "SUP-{$date}-";

        // Get last supplier code for today
        $lastSupplier = Supplier::where('code', 'LIKE', "{$prefix}%")
            ->latest('id')
            ->first();

        if ($lastSupplier) {
            // Extract number and increment
            $lastNumber = (int) substr($lastSupplier->code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Pad with zeros (001, 002, etc.)
        $number = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return $prefix . $number;
    }

    /**
     * Approve supplier and activate account
     * 
     * @param int $supplierId
     * @param int $approvedBy
     * @return Supplier
     */
    public function approve(int $supplierId, int $approvedBy): Supplier
    {
        $supplier = Supplier::findOrFail($supplierId);

        // Update supplier status
        $supplier->update([
            'status' => 'APPROVED_PENDING_PAYMENT',
            'approvedAt' => now(),
            'approvedById' => $approvedBy,
        ]);

        // Activate user account
        User::where('email', $supplier->email)->update([
            'isActive' => true,
        ]);

        return $supplier->fresh();
    }

    /**
     * Reject supplier registration
     * 
     * @param int $supplierId
     * @param string $reason
     * @return Supplier
     */
    public function reject(int $supplierId, string $reason): Supplier
    {
        $supplier = Supplier::findOrFail($supplierId);

        $supplier->update([
            'status' => 'REJECTED',
            'rejectedReason' => $reason,
        ]);

        return $supplier->fresh();
    }

    /**
     * Suspend supplier
     * 
     * @param int $supplierId
     * @param string|null $reason
     * @return Supplier
     */
    public function suspend(int $supplierId, ?string $reason = null): Supplier
    {
        $supplier = Supplier::findOrFail($supplierId);

        $supplier->update([
            'status' => 'SUSPENDED',
            'isSuspendedForPayment' => true,
            'suspendedAt' => now(),
            'suspensionReason' => $reason,
        ]);

        // Deactivate user account
        User::where('email', $supplier->email)->update([
            'isActive' => false,
        ]);

        return $supplier->fresh();
    }

    /**
     * Activate supplier (unsuspend)
     * 
     * @param int $supplierId
     * @return Supplier
     */
    public function activate(int $supplierId): Supplier
    {
        $supplier = Supplier::findOrFail($supplierId);

        $supplier->update([
            'status' => 'ACTIVE',
            'isActive' => true,
            'isSuspendedForPayment' => false,
            'suspendedAt' => null,
            'suspensionReason' => null,
        ]);

        // Activate user account
        User::where('email', $supplier->email)->update([
            'isActive' => true,
        ]);

        return $supplier->fresh();
    }
}
