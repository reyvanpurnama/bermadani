<?php

namespace App\Services;

use App\Models\Member;
use App\Models\SimpananPayment;
use App\Models\SimpananTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SimpananPaymentService
{
    /**
     * Record a new payment for a bill
     */
    public function recordPayment(array $data)
    {
        DB::beginTransaction();
        
        try {
            $bill = SimpananTransaction::findOrFail($data['billId']);
            
            // Validate bill can accept payment
            if ($bill->billStatus === 'CANCELLED') {
                throw new \Exception('Tidak dapat mencatat pembayaran untuk tagihan yang dibatalkan');
            }
            
            if ($bill->billStatus === 'DRAFT') {
                throw new \Exception('Tagihan harus disetujui terlebih dahulu sebelum menerima pembayaran');
            }
            
            // Validate payment amount
            $remainingAmount = $bill->remainingAmount;
            if ($data['amount'] > $remainingAmount) {
                throw new \Exception("Jumlah pembayaran (Rp " . number_format($data['amount'], 0, ',', '.') . ") melebihi sisa tagihan (Rp " . number_format($remainingAmount, 0, ',', '.') . ")");
            }
            
            // Handle proof attachment if provided
            $proofPath = null;
            if (isset($data['proofAttachment'])) {
                $proofPath = $data['proofAttachment']->store('simpanan-payments', 'public');
            }
            
            // Create payment record
            $payment = SimpananPayment::create([
                'billId' => $bill->id,
                'memberId' => $bill->memberId,
                'amount' => $data['amount'],
                'paymentMethod' => $data['paymentMethod'],
                'paymentDate' => $data['paymentDate'],
                'referenceNumber' => $data['referenceNumber'] ?? null,
                'receiptNumber' => SimpananPayment::generateReceiptNumber(),
                'notes' => $data['notes'] ?? null,
                'proofAttachment' => $proofPath,
                'processedBy' => auth()->id(),
            ]);
            
            // Update bill's paid amount
            $newPaidAmount = $bill->paidAmount + $data['amount'];
            $bill->update(['paidAmount' => $newPaidAmount]);
            
            // If fully paid, update member's simpanan balance
            if ($bill->paymentStatus === 'PAID') {
                $member = $bill->member;
                
                // Update the appropriate simpanan field based on type
                switch ($bill->type) {
                    case 'POKOK':
                        $member->increment('simpananPokok', $bill->amount);
                        break;
                    case 'WAJIB':
                        $member->increment('simpananWajib', $bill->amount);
                        break;
                    case 'SUKARELA':
                        $member->increment('simpananSukarela', $bill->amount);
                        break;
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'payment' => $payment,
                'bill' => $bill->fresh(),
                'message' => 'Pembayaran berhasil dicatat. Nomor Kuitansi: ' . $payment->receiptNumber,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if transaction failed
            if (isset($proofPath) && $proofPath) {
                Storage::disk('public')->delete($proofPath);
            }
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Get unpaid bills for a member
     */
    public function getUnpaidBills($memberId, $type = null)
    {
        $query = SimpananTransaction::where('memberId', $memberId)
            ->where('billStatus', 'APPROVED')
            ->where('transactionType', 'SETOR')
            ->whereColumn('paidAmount', '<', 'amount')
            ->orderBy('billingMonth', 'asc');
            
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->with('payments')->get();
    }
    
    /**
     * Get payment history for a member
     */
    public function getPaymentHistory($memberId, $limit = null)
    {
        $query = SimpananPayment::where('memberId', $memberId)
            ->with(['bill', 'processor'])
            ->orderBy('paymentDate', 'desc');
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }
    
    /**
     * Generate payment receipt data
     */
    public function getReceiptData($paymentId)
    {
        $payment = SimpananPayment::with(['bill.member', 'processor'])
            ->findOrFail($paymentId);
            
        return [
            'payment' => $payment,
            'member' => $payment->member,
            'bill' => $payment->bill,
            'processor' => $payment->processor,
        ];
    }
}
