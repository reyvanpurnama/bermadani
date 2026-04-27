<?php

namespace App\Services;

use App\Models\CashierShift;
use App\Models\ConsignmentItem;
use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class POSCheckoutService
{
    private const PAYMENT_METHODS = ['CASH', 'TRANSFER', 'CREDIT', 'SUKARELA'];

    public function checkout(
        array $cart,
        ?int $memberId,
        int $userId,
        string $paymentMethod,
        float|int|string|null $cashReceived = 0,
        ?string $note = null,
        ?string $checkoutToken = null
    ): Transaction {
        $checkoutToken = $this->normalizeCheckoutToken($checkoutToken);

        if ($existing = $this->findExistingCheckout($checkoutToken)) {
            return $existing;
        }

        try {
            return DB::transaction(function () use ($cart, $memberId, $userId, $paymentMethod, $cashReceived, $note, $checkoutToken) {
                if ($existing = $this->findExistingCheckout($checkoutToken)) {
                    return $existing;
                }

                if (! in_array($paymentMethod, self::PAYMENT_METHODS, true)) {
                    throw new RuntimeException('Metode pembayaran tidak valid.');
                }

                $user = User::findOrFail($userId);
                $shift = $this->resolveShift($user);
                $lines = $this->buildLockedLines($cart);
                $total = array_sum(array_column($lines, 'subtotal'));

                if ($paymentMethod === 'CASH' && (float) $cashReceived < $total) {
                    throw new RuntimeException('Uang tidak cukup.');
                }

                $member = $this->resolveMember($memberId, $paymentMethod, $total);

                $transaction = Transaction::create([
                    'invoiceNumber' => 'PENDING-'.$checkoutToken,
                    'checkoutToken' => $checkoutToken,
                    'memberId' => $member?->id,
                    'userId' => $user->id,
                    'cashierShiftId' => $shift?->id,
                    'type' => 'SALE',
                    'totalAmount' => $total,
                    'paymentMethod' => $paymentMethod,
                    'status' => 'COMPLETED',
                    'note' => $note,
                    'date' => now(),
                ]);

                $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->update(['invoiceNumber' => $invoiceNumber]);

                foreach ($lines as $line) {
                    /** @var Product $product */
                    $product = $line['product'];

                    TransactionItem::create([
                        'transactionId' => $transaction->id,
                        'productId' => $product->id,
                        'quantity' => $line['quantity'],
                        'unitPrice' => $line['unitPrice'],
                        'totalPrice' => $line['subtotal'],
                        'cogsPerUnit' => $line['cogsPerUnit'],
                        'totalCogs' => $line['totalCogs'],
                        'grossProfit' => $line['grossProfit'],
                    ]);

                    $product->reduceStock(
                        $line['quantity'],
                        'SALE_OUT',
                        'Penjualan '.$invoiceNumber,
                        'SALE',
                        (string) $transaction->id
                    );

                    if ($product->isConsignment) {
                        $this->recordConsignmentSale($product->id, $line['quantity']);
                    }
                }

                if ($member) {
                    if ($paymentMethod === 'SUKARELA') {
                        $member->payWithSukarela($total, 'Belanja '.$invoiceNumber, $transaction->id);
                    }

                    $pointsEarned = (int) floor($total / 1000);
                    if ($pointsEarned > 0) {
                        $member->addPoints($pointsEarned, 'Belanja '.$invoiceNumber, $transaction->id);
                    }

                    $member->recordPurchase($total);
                }

                return $transaction->load(['items.product', 'member', 'user', 'cashierShift']);
            });
        } catch (QueryException $exception) {
            if ($existing = $this->findExistingCheckout($checkoutToken)) {
                return $existing;
            }

            throw $exception;
        }
    }

    private function normalizeCheckoutToken(?string $checkoutToken): string
    {
        $checkoutToken = trim((string) $checkoutToken);

        if ($checkoutToken === '') {
            return (string) Str::uuid();
        }

        return Str::limit($checkoutToken, 64, '');
    }

    private function findExistingCheckout(string $checkoutToken): ?Transaction
    {
        return Transaction::with(['items.product', 'member', 'user', 'cashierShift'])
            ->where('checkoutToken', $checkoutToken)
            ->first();
    }

    private function resolveShift(User $user): ?CashierShift
    {
        if (! $user->isKasir()) {
            return CashierShift::getOpenShift($user->id);
        }

        $shift = CashierShift::where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->lockForUpdate()
            ->first();

        if (! $shift) {
            throw new RuntimeException('Kasir harus check-in sebelum memproses transaksi.');
        }

        return $shift;
    }

    private function resolveMember(?int $memberId, string $paymentMethod, float $total): ?Member
    {
        if (! $memberId) {
            if ($paymentMethod === 'SUKARELA') {
                throw new RuntimeException('Pilih member terlebih dahulu.');
            }

            return null;
        }

        $member = Member::where('status', 'ACTIVE')
            ->lockForUpdate()
            ->find($memberId);

        if (! $member) {
            throw new RuntimeException('Member tidak ditemukan atau tidak aktif.');
        }

        if ($paymentMethod === 'SUKARELA' && (float) $member->simpananSukarela < $total) {
            throw new RuntimeException('Saldo Simpanan Sukarela tidak mencukupi.');
        }

        return $member;
    }

    private function buildLockedLines(array $cart): array
    {
        $quantities = $this->normalizeCartQuantities($cart);
        $lines = [];

        foreach ($quantities as $productId => $quantity) {
            $product = Product::query()
                ->whereKey($productId)
                ->lockForUpdate()
                ->first();

            if (! $product || ! $product->isActive || $product->status !== 'ACTIVE') {
                throw new RuntimeException('Produk tidak tersedia.');
            }

            if ($product->approvalStatus && $product->approvalStatus !== 'APPROVED') {
                throw new RuntimeException('Produk belum disetujui untuk dijual.');
            }

            if ($product->stock < $quantity) {
                throw new RuntimeException('Stok '.$product->name.' tidak mencukupi (sisa: '.$product->stock.').');
            }

            $unitPrice = (float) $product->sellPrice;
            $cogsPerUnit = (float) ($product->avgCost ?? $product->buyPrice ?? 0);
            $subtotal = $unitPrice * $quantity;
            $totalCogs = $cogsPerUnit * $quantity;

            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unitPrice' => $unitPrice,
                'subtotal' => $subtotal,
                'cogsPerUnit' => $cogsPerUnit,
                'totalCogs' => $totalCogs,
                'grossProfit' => $subtotal - $totalCogs,
            ];
        }

        return $lines;
    }

    private function normalizeCartQuantities(array $cart): array
    {
        if ($cart === []) {
            throw new RuntimeException('Keranjang kosong.');
        }

        $quantities = [];

        foreach ($cart as $item) {
            $productId = (int) ($item['productId'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId < 1 || $quantity < 1) {
                throw new RuntimeException('Item keranjang tidak valid.');
            }

            $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;
        }

        ksort($quantities);

        return $quantities;
    }

    private function recordConsignmentSale(int $productId, int $quantity): void
    {
        $remainingQty = $quantity;

        while ($remainingQty > 0) {
            $consignmentItem = ConsignmentItem::whereHas('batch', function ($query) {
                $query->where('status', 'ACTIVE');
            })
                ->where('productId', $productId)
                ->where('remainingQty', '>', 0)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (! $consignmentItem) {
                break;
            }

            $deductQty = min($remainingQty, $consignmentItem->remainingQty);
            $consignmentItem->recordSale($deductQty);
            $remainingQty -= $deductQty;
        }
    }
}
