<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\POSCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class POSOfflineSyncController extends Controller
{
    public function syncBatch(Request $request): JsonResponse
    {
        $request->validate([
            'transactions' => 'required|array|min:1',
            'transactions.*.cart' => 'required|array|min:1',
            'transactions.*.paymentMethod' => 'required|string',
            'transactions.*.cashReceived' => 'required|numeric',
            'transactions.*.offlineToken' => 'required|string',
        ]);

        $synced = [];
        $errors = [];
        $checkoutService = app(POSCheckoutService::class);

        foreach ($request->input('transactions') as $rawTrx) {
            try {
                DB::beginTransaction();

                // Normalize cart items to ensure 'productId' key exists
                $cart = array_map(function ($item) {
                    return [
                        'productId' => (int) ($item['productId'] ?? $item['id'] ?? 0),
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'price' => (float) ($item['price'] ?? $item['unitPrice'] ?? 0),
                    ];
                }, $rawTrx['cart']);

                $memberId = !empty($rawTrx['memberId']) ? (int) $rawTrx['memberId'] : null;
                $userId = Auth::id() ?? (int) ($rawTrx['userId'] ?? 1);
                $paymentMethod = $rawTrx['paymentMethod'] ?? 'CASH';
                $cashReceived = (float) ($rawTrx['cashReceived'] ?? 0);
                $note = trim(($rawTrx['note'] ?? '') . ' [OFFLINE SYNC]');
                $token = $rawTrx['offlineToken'];

                $transaction = $checkoutService->checkout(
                    cart: $cart,
                    memberId: $memberId,
                    userId: $userId,
                    paymentMethod: $paymentMethod,
                    cashReceived: $cashReceived,
                    note: $note,
                    checkoutToken: $token
                );

                if ($transaction->wasRecentlyCreated) {
                    ActivityLog::log(
                        'CREATE',
                        'Transaction',
                        "POS Offline Sync {$transaction->invoiceNumber} sebesar Rp " . number_format($transaction->totalAmount, 0, ',', '.'),
                        $transaction,
                        null,
                        [
                            'invoiceNumber' => $transaction->invoiceNumber,
                            'totalAmount' => $transaction->totalAmount,
                            'offlineToken' => $token,
                        ]
                    );
                }

                DB::commit();

                $synced[] = [
                    'offlineToken' => $token,
                    'invoiceNumber' => $transaction->invoiceNumber,
                    'id' => $transaction->id,
                    'status' => 'SUCCESS',
                ];

            } catch (Throwable $e) {
                DB::rollBack();
                Log::error('POS Offline Sync Error: ' . $e->getMessage(), ['trx' => $rawTrx]);

                $errors[] = [
                    'offlineToken' => $rawTrx['offlineToken'] ?? 'UNKNOWN',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status' => 'completed',
            'syncedCount' => count($synced),
            'errorCount' => count($errors),
            'synced' => $synced,
            'errors' => $errors,
        ]);
    }
}
