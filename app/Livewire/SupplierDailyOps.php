<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentItemCount;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierPayout;
use App\Models\SupplierPayoutAllocation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

class SupplierDailyOps extends Component
{
    private const INCOME_CATEGORY = 'Omset Supplier Manual (Non-POS)';
    private const EXPENSE_CATEGORY = 'Pembayaran Supplier Manual (Non-POS)';
    private const FINALIZE_PREFIX = 'DAILY_FINALIZE:';
    private const REOPEN_PREFIX = 'DAILY_REOPEN:';

    public string $selectedDate = '';
    public $selectedSupplierId = '';
    public string $tab = 'stock-in';
    public string $mobileView = 'roster';
    public string $supplierSearch = '';
    public string $rosterStatusFilter = 'all';

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
        $this->selectedDate = today()->toDateString();
        $this->syncDateContext();
        $this->stockItems = [$this->emptyStockItem()];
    }

    public function updatedSelectedDate(): void
    {
        $this->syncDateContext();

        if ($this->selectedSupplierId) {
            $this->selectSupplier((int) $this->selectedSupplierId, $this->tab);
            return;
        }

        $this->stockItems = [$this->emptyStockItem()];
        $this->countItems = [];
        $this->payNowAmount = '';
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['stock-in', 'recap'], true) ? $tab : 'stock-in';
    }

    public function setMobileView(string $view): void
    {
        $this->mobileView = in_array($view, ['roster', 'detail'], true) ? $view : 'roster';
    }

    public function navigateDate(int $days): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDays($days)->toDateString();
        $this->updatedSelectedDate();
    }

    public function selectSupplier(int $supplierId, ?string $tab = null): void
    {
        if (! $this->isSupplierSelectable($supplierId)) {
            return;
        }

        $this->selectedSupplierId = $supplierId;
        $this->stockSupplierId = $supplierId;
        $this->recapSupplierId = $supplierId;
        $this->syncDateContext();
        $this->stockItems = [$this->emptyStockItem()];
        $this->countNote = '';
        $this->payoutNote = '';

        $this->loadCountItems();
        $this->refreshPayNowDefault();

        if ($tab !== null) {
            $this->setTab($tab);
        }

        $this->mobileView = 'detail';
    }

    public function clearSelectedSupplier(): void
    {
        $this->selectedSupplierId = '';
        $this->stockSupplierId = '';
        $this->recapSupplierId = '';
        $this->stockItems = [$this->emptyStockItem()];
        $this->countItems = [];
        $this->countNote = '';
        $this->payoutNote = '';
        $this->payNowAmount = '';
    }

    public function updatedStockSupplierId(): void
    {
        $this->stockItems = [$this->emptyStockItem()];

        if (! $this->stockSupplierId) {
            return;
        }

        $this->selectedSupplierId = $this->stockSupplierId;

        if ((int) $this->recapSupplierId !== (int) $this->stockSupplierId) {
            $this->recapSupplierId = $this->stockSupplierId;
        }
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
        if (! $this->recapSupplierId) {
            $this->countItems = [];
            $this->payNowAmount = '';
            return;
        }

        $this->selectedSupplierId = $this->recapSupplierId;

        if ((int) $this->stockSupplierId !== (int) $this->recapSupplierId) {
            $this->stockSupplierId = $this->recapSupplierId;
            $this->stockItems = [$this->emptyStockItem()];
        }

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

    public function decrementPhysicalQty(int $index): void
    {
        if (! isset($this->countItems[$index])) {
            return;
        }

        $current = (int) ($this->countItems[$index]['physicalQty'] ?? 0);
        $this->countItems[$index]['physicalQty'] = max(0, $current - 1);
    }

    public function incrementPhysicalQty(int $index): void
    {
        if (! isset($this->countItems[$index])) {
            return;
        }

        $current = (int) ($this->countItems[$index]['physicalQty'] ?? 0);
        $max = (int) ($this->countItems[$index]['beforeQty'] ?? 0);
        $this->countItems[$index]['physicalQty'] = min($max, $current + 1);
    }

    public function saveStockIn(): void
    {
        $this->assertDateSupplierNotLocked((int) $this->stockSupplierId, $this->stockDate);

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
        $this->assertDateSupplierNotLocked((int) $this->recapSupplierId, $this->recapDate);

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

    public function fillPayNowFromSupplierRights(): void
    {
        if (! $this->recapSupplierId) {
            $this->payNowAmount = '';
            return;
        }

        $payable = (float) ($this->countPreview['payable'] ?? 0);
        $this->payNowAmount = $payable > 0 ? round($payable, 2) : '';
    }

    public function copyPreviousDayDraft(): void
    {
        if (! $this->stockSupplierId) {
            return;
        }

        $this->assertDateSupplierNotLocked((int) $this->stockSupplierId, $this->stockDate);

        $date = Carbon::parse($this->stockDate);
        $prevDate = $date->copy()->subDay()->toDateString();

        if ($this->hasAnyDateActivity((int) $this->stockSupplierId, $date->toDateString())) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Copy draft hanya bisa dilakukan jika tanggal target masih kosong.',
            ]);

            return;
        }

        $sourceBatches = ConsignmentBatch::with('items')
            ->where('supplierId', $this->stockSupplierId)
            ->whereDate('receivedAt', $prevDate)
            ->orderBy('id')
            ->get();

        if ($sourceBatches->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Tidak ada stok masuk H-1 untuk disalin.',
            ]);

            return;
        }

        $draft = [];
        foreach ($sourceBatches as $batch) {
            foreach ($batch->items as $item) {
                $qty = (int) ($item->receivedQty ?: $item->initialQty);
                if ($qty <= 0) {
                    continue;
                }

                $draft[] = [
                    'productId' => (string) $item->productId,
                    'qty' => $qty,
                    'supplierPrice' => (float) $item->supplierPrice,
                ];
            }
        }

        if (empty($draft)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Data H-1 tidak memiliki item valid untuk draft.',
            ]);

            return;
        }

        $this->stockItems = $draft;
        $this->stockNote = "Copy draft dari {$prevDate}";

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Draft stok masuk berhasil disalin dari H-1.',
        ]);
    }

    public function finalizeDate(): void
    {
        $date = Carbon::parse($this->selectedDate)->toDateString();

        if ($this->isDateFinalized($date)) {
            return;
        }

        $summary = $this->selectedDateSummary;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'COMPLETE',
            'module' => 'SupplierDailyOps',
            'description' => self::FINALIZE_PREFIX . $date,
            'loggable_type' => \App\Models\User::class,
            'loggable_id' => (int) Auth::id(),
            'old_values' => null,
            'new_values' => [
                'date' => $date,
                'summary' => $summary,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Tanggal operasional berhasil difinalisasi.',
        ]);
    }

    public function reopenDate(): void
    {
        $user = Auth::user();
        if (! $user || ! ($user->isAdmin() || $user->isSuperAdmin())) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Hanya admin/superadmin yang dapat membuka finalisasi tanggal.',
            ]);

            return;
        }

        $date = Carbon::parse($this->selectedDate)->toDateString();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'REOPEN',
            'module' => 'SupplierDailyOps',
            'description' => self::REOPEN_PREFIX . $date,
            'loggable_type' => \App\Models\User::class,
            'loggable_id' => (int) Auth::id(),
            'old_values' => null,
            'new_values' => [
                'date' => $date,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Finalisasi tanggal dibuka kembali.',
        ]);
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

    public function getCanSubmitStockInProperty(): bool
    {
        if (! $this->stockSupplierId || blank($this->stockDate) || count($this->stockItems) < 1) {
            return false;
        }

        if ($this->isSupplierDateLocked((int) $this->stockSupplierId, $this->stockDate)) {
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

        if ($this->isSupplierDateLocked((int) $this->recapSupplierId, $this->recapDate)) {
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

    public function getSelectedSupplierProperty(): ?Supplier
    {
        if (! $this->selectedSupplierId) {
            return null;
        }

        return $this->suppliers->firstWhere('id', (int) $this->selectedSupplierId);
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

    public function getSupplierRosterProperty(): array
    {
        $suppliers = $this->suppliers;
        $date = Carbon::parse($this->selectedDate)->toDateString();
        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = Carbon::parse($date)->endOfDay();
        $isFinalized = $this->isDateFinalized($date);

        $supplierIds = $suppliers->pluck('id')->all();

        $batchesBySupplier = ConsignmentBatch::with('items')
            ->whereIn('supplierId', $supplierIds)
            ->whereBetween('receivedAt', [$dayStart, $dayEnd])
            ->get()
            ->groupBy('supplierId');

        $countsBySupplier = ConsignmentItemCount::whereIn('supplierId', $supplierIds)
            ->whereBetween('countedAt', [$dayStart, $dayEnd])
            ->get()
            ->groupBy('supplierId');

        $payoutBySupplier = SupplierPayout::whereIn('supplierId', $supplierIds)
            ->whereDate('payoutDate', $date)
            ->get()
            ->groupBy('supplierId');

        $rows = [];

        foreach ($suppliers as $supplier) {
            $dailyBatches = $batchesBySupplier->get($supplier->id, collect());
            $dailyCounts = $countsBySupplier->get($supplier->id, collect());
            $dailyPayouts = $payoutBySupplier->get($supplier->id, collect());

            $stockInQty = 0;
            $stockInItems = 0;
            foreach ($dailyBatches as $batch) {
                $stockInItems += $batch->items->count();
                $stockInQty += (int) $batch->items->sum(fn ($item) => (int) ($item->receivedQty ?: $item->initialQty));
            }

            $soldQty = (int) $dailyCounts->sum('soldDeltaQty');
            $payableToday = (float) $dailyCounts->sum('payableDeltaAmount');
            $payoutToday = (float) $dailyPayouts->sum('paidAmount');
            $outstandingCurrent = $this->getSupplierOutstandingAmount((int) $supplier->id);

            $locked = $this->isSupplierDateLocked((int) $supplier->id, $date);
            $statusKey = $this->resolveRosterStatus(
                hasStockIn: $stockInItems > 0,
                hasRecap: $soldQty > 0 || $payableToday > 0,
                hasPayoutToday: $payoutToday > 0,
                locked: $locked,
                dateFinalized: $isFinalized
            );

            $rows[] = [
                'supplierId' => (int) $supplier->id,
                'supplierName' => $supplier->businessName,
                'statusKey' => $statusKey,
                'statusLabel' => $this->statusLabel($statusKey),
                'statusClass' => $this->statusClass($statusKey),
                'stockInItems' => $stockInItems,
                'stockInQty' => $stockInQty,
                'soldQty' => $soldQty,
                'payableToday' => $payableToday,
                'payoutToday' => $payoutToday,
                'outstandingCurrent' => $outstandingCurrent,
                'locked' => $locked,
                'isSelected' => (int) $this->selectedSupplierId === (int) $supplier->id,
            ];
        }

        return $rows;
    }

    public function getSelectedDateSummaryProperty(): array
    {
        $date = Carbon::parse($this->selectedDate)->toDateString();
        $rows = collect($this->supplierRoster);

        $income = (float) FinancialTransaction::query()
            ->whereDate('transactionDate', $date)
            ->where('category', self::INCOME_CATEGORY)
            ->sum('amount');

        $expense = (float) FinancialTransaction::query()
            ->whereDate('transactionDate', $date)
            ->where('category', self::EXPENSE_CATEGORY)
            ->sum('amount');

        return [
            'date' => $date,
            'supplierTotal' => $rows->count(),
            'processedSuppliers' => $rows->whereNotIn('statusKey', ['PENDING', 'NO_DELIVERY'])->count(),
            'noDeliverySuppliers' => $rows->where('statusKey', 'NO_DELIVERY')->count(),
            'stockInQty' => (int) $rows->sum('stockInQty'),
            'soldQty' => (int) $rows->sum('soldQty'),
            'payableToday' => (float) $rows->sum('payableToday'),
            'payoutToday' => (float) $rows->sum('payoutToday'),
            'incomeSupplierOps' => $income,
            'expenseSupplierOps' => $expense,
            'netSupplierOps' => $income - $expense,
            'isFinalized' => $this->isDateFinalized($date),
        ];
    }

    public function getVisibleSupplierRosterProperty(): array
    {
        $rows = collect($this->supplierRoster);

        if ($this->supplierSearch !== '') {
            $keyword = mb_strtolower(trim($this->supplierSearch));
            $rows = $rows->filter(function (array $row) use ($keyword) {
                return str_contains(mb_strtolower((string) $row['supplierName']), $keyword);
            });
        }

        if ($this->rosterStatusFilter !== 'all') {
            $statusMap = [
                'pending' => 'PENDING',
                'no_delivery' => 'NO_DELIVERY',
                'stock_in' => 'STOCK_IN',
                'recap' => 'RECAP',
                'payout_partial' => 'PAYOUT_PARTIAL',
                'locked' => 'LOCKED',
            ];

            $target = $statusMap[$this->rosterStatusFilter] ?? null;
            if ($target) {
                $rows = $rows->where('statusKey', $target);
            }
        }

        return $rows->values()->all();
    }

    public function getSelectedSupplierDailyDetailProperty(): array
    {
        if (! $this->selectedSupplierId) {
            return [
                'hasSelection' => false,
                'supplierName' => '-',
                'lockStatus' => false,
                'isFinalized' => $this->isDateFinalized($this->selectedDate),
                'todayBatches' => collect(),
                'todayPayouts' => collect(),
                'todayCounts' => collect(),
            ];
        }

        $date = Carbon::parse($this->selectedDate)->toDateString();
        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = Carbon::parse($date)->endOfDay();

        $todayBatches = ConsignmentBatch::with('items.product')
            ->where('supplierId', $this->selectedSupplierId)
            ->whereBetween('receivedAt', [$dayStart, $dayEnd])
            ->latest('receivedAt')
            ->get();

        $todayPayouts = SupplierPayout::query()
            ->where('supplierId', $this->selectedSupplierId)
            ->whereDate('payoutDate', $date)
            ->latest('id')
            ->get();

        $todayCounts = ConsignmentItemCount::with('product')
            ->where('supplierId', $this->selectedSupplierId)
            ->whereBetween('countedAt', [$dayStart, $dayEnd])
            ->latest('id')
            ->get();

        return [
            'hasSelection' => true,
            'supplierName' => $this->selectedSupplier?->businessName ?? '-',
            'lockStatus' => $this->isSupplierDateLocked((int) $this->selectedSupplierId, $date),
            'isFinalized' => $this->isDateFinalized($date),
            'todayBatches' => $todayBatches,
            'todayPayouts' => $todayPayouts,
            'todayCounts' => $todayCounts,
        ];
    }

    public function getRecentCountLogsProperty()
    {
        return ConsignmentItemCount::with(['supplier', 'product', 'user'])
            ->when($this->selectedSupplierId, fn ($query) => $query->where('supplierId', $this->selectedSupplierId))
            ->when($this->selectedDate, fn ($query) => $query->whereDate('countedAt', $this->selectedDate))
            ->latest('countedAt')
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public function getRecentPayoutsProperty()
    {
        return SupplierPayout::with(['supplier', 'user'])
            ->when($this->selectedSupplierId, fn ($query) => $query->where('supplierId', $this->selectedSupplierId))
            ->when($this->selectedDate, fn ($query) => $query->whereDate('payoutDate', $this->selectedDate))
            ->latest('payoutDate')
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public function getCanReopenFinalizationProperty(): bool
    {
        $user = Auth::user();

        return (bool) $user && ($user->isAdmin() || $user->isSuperAdmin());
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
        if ($supplierId <= 0) {
            return 0;
        }

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

    private function syncDateContext(): void
    {
        $date = Carbon::parse($this->selectedDate)->toDateString();
        $this->stockDate = $date;
        $this->recapDate = $date;
    }

    private function isSupplierSelectable(int $supplierId): bool
    {
        return $this->suppliers->contains(fn ($supplier) => (int) $supplier->id === $supplierId);
    }

    private function hasAnyDateActivity(int $supplierId, string $date): bool
    {
        if ($supplierId <= 0) {
            return false;
        }

        $hasStockIn = ConsignmentBatch::query()
            ->where('supplierId', $supplierId)
            ->whereDate('receivedAt', $date)
            ->exists();

        if ($hasStockIn) {
            return true;
        }

        $hasRecap = ConsignmentItemCount::query()
            ->where('supplierId', $supplierId)
            ->whereDate('countedAt', $date)
            ->exists();

        return $hasRecap;
    }

    private function isDateFinalized(string $date): bool
    {
        $lastFinalizeId = ActivityLog::query()
            ->where('module', 'SupplierDailyOps')
            ->where('description', self::FINALIZE_PREFIX . $date)
            ->max('id');

        if (! $lastFinalizeId) {
            return false;
        }

        $lastReopenId = ActivityLog::query()
            ->where('module', 'SupplierDailyOps')
            ->where('description', self::REOPEN_PREFIX . $date)
            ->max('id');

        return ! $lastReopenId || $lastFinalizeId > $lastReopenId;
    }

    private function isSupplierDateLocked(int $supplierId, string $date): bool
    {
        if ($supplierId <= 0) {
            return false;
        }

        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = Carbon::parse($date)->endOfDay();

        $counts = ConsignmentItemCount::query()
            ->where('supplierId', $supplierId)
            ->whereBetween('countedAt', [$dayStart, $dayEnd])
            ->get(['consignmentItemId', 'payableDeltaAmount']);

        if ($counts->isEmpty()) {
            return false;
        }

        $payableTotal = (float) $counts->sum('payableDeltaAmount');
        if ($payableTotal <= 0) {
            return false;
        }

        $itemIds = $counts->pluck('consignmentItemId')->unique()->values();
        if ($itemIds->isEmpty()) {
            return false;
        }

        $allocatedTotal = (float) SupplierPayoutAllocation::query()
            ->whereIn('consignmentItemId', $itemIds)
            ->sum('allocatedAmount');

        return $allocatedTotal + 0.0001 >= $payableTotal;
    }

    private function assertDateSupplierNotLocked(int $supplierId, string $date): void
    {
        if ($supplierId <= 0) {
            return;
        }

        if (! $this->isSupplierDateLocked($supplierId, $date)) {
            return;
        }

        throw ValidationException::withMessages([
            'recapSupplierId' => 'Data tanggal ini sudah lunas/locked dan tidak dapat diubah.',
        ]);
    }

    private function resolveRosterStatus(bool $hasStockIn, bool $hasRecap, bool $hasPayoutToday, bool $locked, bool $dateFinalized): string
    {
        if ($locked) {
            return 'LOCKED';
        }

        if ($hasPayoutToday) {
            return 'PAYOUT_PARTIAL';
        }

        if ($hasRecap) {
            return 'RECAP';
        }

        if ($hasStockIn) {
            return 'STOCK_IN';
        }

        return $dateFinalized ? 'NO_DELIVERY' : 'PENDING';
    }

    private function statusLabel(string $statusKey): string
    {
        return match ($statusKey) {
            'LOCKED' => 'Lunas (Locked)',
            'PAYOUT_PARTIAL' => 'Payout Parsial',
            'RECAP' => 'Rekap',
            'STOCK_IN' => 'Stok Masuk',
            'NO_DELIVERY' => 'Tidak Kirim',
            default => 'Belum Diproses',
        };
    }

    private function statusClass(string $statusKey): string
    {
        return match ($statusKey) {
            'LOCKED' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
            'PAYOUT_PARTIAL' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
            'RECAP' => 'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-300',
            'STOCK_IN' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
            'NO_DELIVERY' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-300',
            default => 'border-slate-200 bg-slate-100 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    public function render()
    {
        return view('livewire.supplier-daily-ops')->layout('layouts.admin');
    }
}
