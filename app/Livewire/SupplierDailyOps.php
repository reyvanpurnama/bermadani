<?php

namespace App\Livewire;

use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentItemCount;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierPayout;
use App\Models\SupplierPayoutAllocation;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;
use Livewire\Component;

class SupplierDailyOps extends Component
{
    private const INCOME_CATEGORY = 'Omset Supplier Manual (Non-POS)';
    private const EXPENSE_CATEGORY = 'Pembayaran Supplier Manual (Non-POS)';

    public string $tab = 'stock-in';

    // Stok Masuk
    public $stockSupplierId = '';
    public string $stockDate = '';
    public string $stockNote = '';
    public array $stockItems = [];

    // Rekap & Bayar
    public $recapSupplierId = '';
    public string $recapDate = '';
    public string $countNote = '';
    public $payNowAmount = '';
    public string $payoutNote = '';
    public array $countItems = [];

    public function mount(): void
    {
        $this->stockDate = today()->toDateString();
        $this->recapDate = today()->toDateString();
        $this->stockItems = [$this->emptyStockItem()];
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['stock-in', 'recap'], true) ? $tab : 'stock-in';
    }

    public function goToStep(int $step): void
    {
        $this->setTab($step === 2 ? 'recap' : 'stock-in');
    }

    public function updatedStockSupplierId(): void
    {
        $this->stockItems = [$this->emptyStockItem()];
    }

    public function updatedStockItems($value, $key): void
    {
        if (! is_string($key) || ! str_ends_with($key, '.productId')) {
            return;
        }

        $index = (int) explode('.', $key)[0];
        $productId = (int) $value;

        if ($productId <= 0 || ! isset($this->stockItems[$index])) {
            return;
        }

        $product = Product::query()
            ->whereKey($productId)
            ->where('isActive', true)
            ->when($this->stockSupplierId, fn ($query) => $query->where('supplierId', $this->stockSupplierId))
            ->first();

        if (! $product) {
            return;
        }

        $this->stockItems[$index]['supplierPrice'] = (float) $product->buyPrice;
    }

    public function updatedRecapSupplierId(): void
    {
        $this->loadCountItems();
        $this->refreshPayNowDefault();
    }

    public function addStockItem(): void
    {
        $this->stockItems[] = $this->emptyStockItem();
    }

    public function removeStockItem(int $index): void
    {
        if (count($this->stockItems) <= 1) {
            return;
        }

        unset($this->stockItems[$index]);
        $this->stockItems = array_values($this->stockItems);
    }

    public function saveStockIn(): void
    {
        $this->validate([
            'stockSupplierId' => 'required|exists:suppliers,id',
            'stockDate' => 'required|date',
            'stockItems' => 'required|array|min:1',
            'stockItems.*.productId' => 'required|exists:products,id',
            'stockItems.*.qty' => 'required|integer|min:1',
            'stockItems.*.supplierPrice' => 'required|numeric|min:0',
            'stockNote' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () {
                $supplier = Supplier::findOrFail($this->stockSupplierId);
                $receivedAt = Carbon::parse($this->stockDate)->startOfDay()->addSeconds(now()->secondsSinceMidnight());

                $batch = $this->createBatchWithRetry([
                    'supplierId' => $supplier->id,
                    'status' => 'ACTIVE',
                    'receivedAt' => $receivedAt,
                    'note' => $this->stockNote ?: null,
                ]);

                $totalValue = 0;

                foreach ($this->stockItems as $row) {
                    $product = Product::where('supplierId', $supplier->id)
                        ->whereKey($row['productId'])
                        ->where('isActive', true)
                        ->lockForUpdate()
                        ->first();

                    if (! $product) {
                        throw ValidationException::withMessages([
                            'stockItems' => 'Produk tidak valid untuk supplier yang dipilih.',
                        ]);
                    }

                    $qty = (int) $row['qty'];
                    $supplierPrice = (float) $row['supplierPrice'];

                    ConsignmentItem::create([
                        'batchId' => $batch->id,
                        'productId' => $product->id,
                        'initialQty' => $qty,
                        'receivedQty' => $qty,
                        'damagedQty' => 0,
                        'returnedQty' => 0,
                        'soldQty' => 0,
                        'remainingQty' => $qty,
                        'sellPrice' => (float) $product->sellPrice,
                        'supplierPrice' => $supplierPrice,
                    ]);

                    $product->increment('stock', $qty);

                    StockMovement::create([
                        'productId' => $product->id,
                        'movementType' => 'CONSIGNMENT_IN',
                        'quantity' => $qty,
                        'referenceType' => 'CONSIGNMENT_BATCH',
                        'referenceId' => $batch->id,
                        'note' => "Setor stok manual supplier {$supplier->businessName} ({$batch->batchCode})",
                        'occurredAt' => Carbon::parse($this->stockDate),
                    ]);

                    $totalValue += ((float) $product->sellPrice * $qty);
                }

                $batch->update([
                    'totalValue' => $totalValue,
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal simpan stok masuk supplier manual', [
                'supplierId' => $this->stockSupplierId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Proses gagal. Periksa data lalu coba lagi.',
            ]);

            return;
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Stok masuk berhasil disimpan.',
        ]);

        $this->stockItems = [$this->emptyStockItem()];
        $this->stockNote = '';
    }

    public function saveRecapAndPayout(): void
    {
        if (blank($this->payNowAmount)) {
            $this->payNowAmount = 0;
        }

        $this->validate([
            'recapSupplierId' => 'required|exists:suppliers,id',
            'recapDate' => 'required|date',
            'payNowAmount' => 'nullable|numeric|min:0',
            'countNote' => 'nullable|string|max:500',
            'payoutNote' => 'nullable|string|max:500',
            'countItems' => 'array',
            'countItems.*.physicalQty' => 'required|integer|min:0',
        ]);

        $countTime = Carbon::parse($this->recapDate)->startOfDay()->addSeconds(now()->secondsSinceMidnight());

        try {
            DB::transaction(function () use ($countTime) {
                $supplier = Supplier::findOrFail($this->recapSupplierId);
                $batchIds = [];

                $totalSoldDeltaAmount = 0.0;
                $totalPayableDeltaAmount = 0.0;
                $totalMarginDeltaAmount = 0.0;

                foreach ($this->countItems as $idx => $row) {
                    $item = ConsignmentItem::with(['batch', 'product'])
                        ->whereKey($row['itemId'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ((int) $item->batch->supplierId !== (int) $supplier->id) {
                        throw ValidationException::withMessages([
                            "countItems.$idx.physicalQty" => 'Item tidak valid untuk supplier ini.',
                        ]);
                    }

                    $beforeQty = (int) $item->remainingQty;
                    $physicalQty = (int) $row['physicalQty'];

                    if ($physicalQty > $beforeQty) {
                        throw ValidationException::withMessages([
                            "countItems.$idx.physicalQty" => "Stok fisik {$item->product->name} tidak boleh lebih besar dari stok tercatat ({$beforeQty}).",
                        ]);
                    }

                    $soldDeltaQty = $beforeQty - $physicalQty;
                    $soldDeltaAmount = $soldDeltaQty * (float) $item->sellPrice;
                    $payableDeltaAmount = $soldDeltaQty * (float) $item->supplierPrice;
                    $marginDeltaAmount = $soldDeltaAmount - $payableDeltaAmount;

                    if ($soldDeltaQty > 0) {
                        $product = Product::whereKey($item->productId)->lockForUpdate()->firstOrFail();

                        if ((int) $product->stock < $soldDeltaQty) {
                            throw ValidationException::withMessages([
                                "countItems.$idx.physicalQty" => "Stok produk {$product->name} tidak cukup untuk dipotong ({$product->stock}).",
                            ]);
                        }

                        $product->decrement('stock', $soldDeltaQty);

                        StockMovement::create([
                            'productId' => $product->id,
                            'movementType' => 'SALE_OUT',
                            'quantity' => -$soldDeltaQty,
                            'referenceType' => 'CONSIGNMENT_BATCH',
                            'referenceId' => $item->batchId,
                            'note' => "Penjualan manual supplier ({$item->batch->batchCode})",
                            'occurredAt' => $countTime,
                        ]);
                    }

                    $item->update([
                        'soldQty' => (int) $item->soldQty + $soldDeltaQty,
                        'remainingQty' => $physicalQty,
                    ]);

                    ConsignmentItemCount::create([
                        'consignmentItemId' => $item->id,
                        'batchId' => $item->batchId,
                        'supplierId' => $supplier->id,
                        'productId' => $item->productId,
                        'userId' => Auth::id(),
                        'beforeQty' => $beforeQty,
                        'physicalQty' => $physicalQty,
                        'soldDeltaQty' => $soldDeltaQty,
                        'soldDeltaAmount' => $soldDeltaAmount,
                        'payableDeltaAmount' => $payableDeltaAmount,
                        'marginDeltaAmount' => $marginDeltaAmount,
                        'countedAt' => $countTime,
                        'note' => $this->countNote ?: null,
                    ]);

                    $totalSoldDeltaAmount += $soldDeltaAmount;
                    $totalPayableDeltaAmount += $payableDeltaAmount;
                    $totalMarginDeltaAmount += $marginDeltaAmount;
                    $batchIds[$item->batchId] = true;
                }

                if ($totalSoldDeltaAmount > 0) {
                    FinancialTransaction::create([
                        'type' => 'INCOME',
                        'category' => self::INCOME_CATEGORY,
                        'amount' => $totalSoldDeltaAmount,
                        'transactionDate' => $this->recapDate,
                        'description' => "Rekap manual supplier {$supplier->businessName}. "
                            . "Omzet: Rp " . number_format($totalSoldDeltaAmount, 0, ',', '.')
                            . ", Hak supplier: Rp " . number_format($totalPayableDeltaAmount, 0, ',', '.')
                            . ", Margin: Rp " . number_format($totalMarginDeltaAmount, 0, ',', '.'),
                        'userId' => Auth::id(),
                    ]);
                }

                foreach (array_keys($batchIds) as $batchId) {
                    $batch = ConsignmentBatch::with('items')->lockForUpdate()->find($batchId);
                    if (! $batch) {
                        continue;
                    }
                    $batch->recalculateTotals();
                    $batch->syncLifecycleStatus();
                }

                $grossDue = $this->getSupplierOutstandingAmount((int) $supplier->id, true);
                $payNowAmount = (float) $this->payNowAmount;

                if ($payNowAmount > $grossDue + 0.0001) {
                    throw ValidationException::withMessages([
                        'payNowAmount' => 'Nominal bayar melebihi total hutang supplier saat ini.',
                    ]);
                }

                if ($payNowAmount > 0) {
                    $this->processPayout(
                        supplier: $supplier,
                        grossDueAmount: $grossDue,
                        paidAmount: $payNowAmount,
                        payoutDate: Carbon::parse($this->recapDate),
                        note: $this->payoutNote
                    );
                }

                $this->syncSupplierBatchStatuses((int) $supplier->id);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal proses rekap/payout supplier manual', [
                'supplierId' => $this->recapSupplierId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Proses gagal. Periksa data lalu coba lagi.',
            ]);

            return;
        }

        $this->loadCountItems();
        $this->countNote = '';
        $this->payoutNote = '';
        $this->refreshPayNowDefault();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Rekap dan pembayaran berhasil diproses.',
        ]);
    }

    public function refreshPayNowDefault(): void
    {
        if (! $this->recapSupplierId) {
            $this->payNowAmount = '';
            return;
        }

        $outstanding = $this->getSupplierOutstandingAmount((int) $this->recapSupplierId);
        $this->payNowAmount = $outstanding > 0 ? $outstanding : '';
    }

    public function loadCountItems(): void
    {
        if (! $this->recapSupplierId) {
            $this->countItems = [];
            return;
        }

        $items = ConsignmentItem::with(['product', 'batch'])
            ->whereHas('batch', function ($query) {
                $query->where('supplierId', $this->recapSupplierId)
                    ->where('status', 'ACTIVE');
            })
            ->where('remainingQty', '>', 0)
            ->orderBy('batchId')
            ->orderBy('id')
            ->get();

        $this->countItems = $items->map(function (ConsignmentItem $item) {
            return [
                'itemId' => $item->id,
                'batchCode' => $item->batch?->batchCode ?? '-',
                'productName' => $item->product?->name ?? '-',
                'beforeQty' => (int) $item->remainingQty,
                'physicalQty' => (int) $item->remainingQty,
                'sellPrice' => (float) $item->sellPrice,
                'supplierPrice' => (float) $item->supplierPrice,
            ];
        })->toArray();
    }

    public function getCountPreviewProperty(): array
    {
        $soldQty = 0;
        $omzet = 0.0;
        $payable = 0.0;

        foreach ($this->countItems as $row) {
            $beforeQty = (int) ($row['beforeQty'] ?? 0);
            $physicalQty = (int) ($row['physicalQty'] ?? 0);
            $delta = max(0, $beforeQty - $physicalQty);
            $soldQty += $delta;
            $omzet += $delta * (float) ($row['sellPrice'] ?? 0);
            $payable += $delta * (float) ($row['supplierPrice'] ?? 0);
        }

        return [
            'soldQty' => $soldQty,
            'omzet' => $omzet,
            'payable' => $payable,
            'margin' => $omzet - $payable,
        ];
    }

    public function getCurrentStepProperty(): int
    {
        return $this->tab === 'recap' ? 2 : 1;
    }

    public function getStepProgressTextProperty(): string
    {
        return "Langkah {$this->currentStep} dari 2";
    }

    public function getStep2SoftLockedProperty(): bool
    {
        return ! (bool) $this->recapSupplierId;
    }

    public function getStepperStepsProperty(): array
    {
        $step1Status = $this->currentStep === 1 ? 'active' : 'completed';
        $step2Status = $this->currentStep === 2
            ? ($this->step2SoftLocked ? 'locked' : 'active')
            : 'inactive';

        return [
            [
                'number' => 1,
                'tab' => 'stock-in',
                'title' => 'Stok Masuk',
                'instruction' => 'Pilih supplier, input item, lalu simpan batch masuk.',
                'status' => $step1Status,
                'isClickable' => true,
            ],
            [
                'number' => 2,
                'tab' => 'recap',
                'title' => 'Rekap & Bayar',
                'instruction' => 'Pilih supplier, hitung stok fisik, review angka, lalu bayar.',
                'status' => $step2Status,
                'isClickable' => true,
            ],
        ];
    }

    public function getCompactHeaderStatsProperty(): array
    {
        $preview = $this->countPreview;

        return [
            'omzet' => (float) ($preview['omzet'] ?? 0),
            'payable' => (float) ($preview['payable'] ?? 0),
            'outstanding' => $this->outstandingPayable,
        ];
    }

    public function getCanSubmitStockInProperty(): bool
    {
        if (! $this->stockSupplierId || blank($this->stockDate) || count($this->stockItems) < 1) {
            return false;
        }

        foreach ($this->stockItems as $row) {
            $hasProduct = ! blank($row['productId'] ?? null);
            $qty = (int) ($row['qty'] ?? 0);
            $price = $row['supplierPrice'] ?? null;

            if (! $hasProduct || $qty < 1 || ! is_numeric($price) || (float) $price < 0) {
                return false;
            }
        }

        return true;
    }

    public function getCanSubmitRecapProperty(): bool
    {
        if (! $this->recapSupplierId) {
            return false;
        }

        if (! blank($this->payNowAmount) && (! is_numeric($this->payNowAmount) || (float) $this->payNowAmount < 0)) {
            return false;
        }

        foreach ($this->countItems as $row) {
            if (! isset($row['physicalQty'], $row['beforeQty']) || ! is_numeric($row['physicalQty']) || ! is_numeric($row['beforeQty'])) {
                return false;
            }

            $physicalQty = (int) $row['physicalQty'];
            $beforeQty = (int) $row['beforeQty'];

            if ($physicalQty < 0 || $physicalQty > $beforeQty) {
                return false;
            }
        }

        return true;
    }

    public function getOutstandingPayableProperty(): float
    {
        if (! $this->recapSupplierId) {
            return 0;
        }

        return $this->getSupplierOutstandingAmount((int) $this->recapSupplierId);
    }

    public function getSuppliersProperty()
    {
        return Supplier::where('isActive', true)
            ->whereIn('status', ['APPROVED', 'ACTIVE'])
            ->orderBy('businessName')
            ->get();
    }

    public function getAvailableProductsProperty()
    {
        if (! $this->stockSupplierId) {
            return collect();
        }

        return Product::where('supplierId', $this->stockSupplierId)
            ->where('isActive', true)
            ->where('approvalStatus', 'APPROVED')
            ->orderBy('name')
            ->get();
    }

    public function getRecentCountLogsProperty()
    {
        return ConsignmentItemCount::with(['supplier', 'product', 'user'])
            ->when($this->recapSupplierId, fn ($query) => $query->where('supplierId', $this->recapSupplierId))
            ->latest('countedAt')
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public function getRecentPayoutsProperty()
    {
        return SupplierPayout::with(['supplier', 'user'])
            ->when($this->recapSupplierId, fn ($query) => $query->where('supplierId', $this->recapSupplierId))
            ->latest('payoutDate')
            ->latest('id')
            ->limit(8)
            ->get();
    }

    private function processPayout(Supplier $supplier, float $grossDueAmount, float $paidAmount, Carbon $payoutDate, ?string $note): void
    {
        $payout = $this->createPayoutWithRetry([
            'supplierId' => $supplier->id,
            'userId' => Auth::id(),
            'payoutDate' => $payoutDate->toDateString(),
            'grossDueAmount' => $grossDueAmount,
            'paidAmount' => $paidAmount,
            'outstandingAfter' => max(0, $grossDueAmount - $paidAmount),
            'note' => $note ?: null,
        ]);

        $remaining = $paidAmount;

        $dueItems = ConsignmentItem::with(['batch'])
            ->whereHas('batch', function ($query) use ($supplier) {
                $query->where('supplierId', $supplier->id)
                    ->whereIn('status', ['ACTIVE', 'PENDING_SETTLEMENT', 'SETTLED']);
            })
            ->lockForUpdate()
            ->orderBy('batchId')
            ->orderBy('id')
            ->get()
            ->sortBy(function (ConsignmentItem $item) {
                return optional($item->batch?->receivedAt)->timestamp ?? 0;
            });

        foreach ($dueItems as $item) {
            if ($remaining <= 0) {
                break;
            }

            $grossItemPayable = (float) $item->soldQty * (float) $item->supplierPrice;
            $paidItemAmount = (float) SupplierPayoutAllocation::where('consignmentItemId', $item->id)
                ->lockForUpdate()
                ->sum('allocatedAmount');
            $outstandingItemAmount = max(0, $grossItemPayable - $paidItemAmount);

            if ($outstandingItemAmount <= 0) {
                continue;
            }

            $allocatedAmount = min($remaining, $outstandingItemAmount);
            $allocatedQty = (float) $item->supplierPrice > 0
                ? round($allocatedAmount / (float) $item->supplierPrice, 4)
                : null;

            SupplierPayoutAllocation::create([
                'supplierPayoutId' => $payout->id,
                'batchId' => $item->batchId,
                'consignmentItemId' => $item->id,
                'allocatedAmount' => $allocatedAmount,
                'allocatedQtyEquivalent' => $allocatedQty,
            ]);

            $remaining -= $allocatedAmount;
        }

        if ($remaining > 0.0001) {
            throw ValidationException::withMessages([
                'payNowAmount' => 'Terjadi perubahan data saat proses payout. Silakan refresh lalu coba lagi.',
            ]);
        }

        FinancialTransaction::create([
            'type' => 'EXPENSE',
            'category' => self::EXPENSE_CATEGORY,
            'amount' => $paidAmount,
            'transactionDate' => $payoutDate->toDateString(),
            'description' => "Pembayaran supplier manual {$supplier->businessName} ({$payout->payoutCode})",
            'userId' => Auth::id(),
        ]);
    }

    private function getSupplierOutstandingAmount(int $supplierId, bool $lockForUpdate = false): float
    {
        $query = ConsignmentItem::query()
            ->whereHas('batch', function ($query) use ($supplierId) {
                $query->where('supplierId', $supplierId)
                    ->whereIn('status', ['ACTIVE', 'PENDING_SETTLEMENT', 'SETTLED']);
            });

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $items = $query->get();

        $outstanding = 0.0;
        foreach ($items as $item) {
            $grossPayable = (float) $item->soldQty * (float) $item->supplierPrice;
            $paidQuery = SupplierPayoutAllocation::where('consignmentItemId', $item->id);
            if ($lockForUpdate) {
                $paidQuery->lockForUpdate();
            }
            $paidAmount = (float) $paidQuery->sum('allocatedAmount');
            $outstanding += max(0, $grossPayable - $paidAmount);
        }

        return round($outstanding, 2);
    }

    private function createBatchWithRetry(array $attributes): ConsignmentBatch
    {
        $attempt = 0;

        while ($attempt < 3) {
            try {
                return ConsignmentBatch::create(array_merge($attributes, [
                    'batchCode' => ConsignmentBatch::generateBatchCode(),
                ]));
            } catch (QueryException $e) {
                if (! $this->isUniqueViolation($e)) {
                    throw $e;
                }
                $attempt++;
            }
        }

        throw ValidationException::withMessages([
            'stockSupplierId' => 'Gagal membuat kode batch unik. Silakan coba ulang.',
        ]);
    }

    private function createPayoutWithRetry(array $attributes): SupplierPayout
    {
        $attempt = 0;

        while ($attempt < 3) {
            try {
                return SupplierPayout::create(array_merge($attributes, [
                    'payoutCode' => SupplierPayout::generateCode(),
                ]));
            } catch (QueryException $e) {
                if (! $this->isUniqueViolation($e)) {
                    throw $e;
                }
                $attempt++;
            }
        }

        throw ValidationException::withMessages([
            'payNowAmount' => 'Gagal membuat kode payout unik. Silakan ulangi proses payout.',
        ]);
    }

    private function isUniqueViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        $driverCode = $e->errorInfo[1] ?? null;

        return $sqlState === '23000' || $driverCode === 1062;
    }

    private function syncSupplierBatchStatuses(int $supplierId): void
    {
        $batches = ConsignmentBatch::with('items')
            ->where('supplierId', $supplierId)
            ->whereIn('status', ['ACTIVE', 'PENDING_SETTLEMENT', 'SETTLED'])
            ->get();

        foreach ($batches as $batch) {
            $batch->syncLifecycleStatus();
        }
    }

    private function emptyStockItem(): array
    {
        return [
            'productId' => '',
            'qty' => 1,
            'supplierPrice' => '',
        ];
    }

    public function render()
    {
        return view('livewire.supplier-daily-ops')->layout('layouts.admin');
    }
}
