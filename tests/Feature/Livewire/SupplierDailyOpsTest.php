<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SupplierDailyOps;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentItemCount;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierPayout;
use App\Models\SupplierPayoutAllocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class SupplierDailyOpsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_in_creates_batch_items_and_increases_stock(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 2, 'sellPrice' => 8000]);

        Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', now()->toDateString())
            ->set('stockSupplierId', $supplier->id)
            ->set('stockDate', now()->toDateString())
            ->set('stockItems', [[
                'productId' => $product->id,
                'qty' => 5,
                'supplierPrice' => 6000,
            ]])
            ->call('saveStockIn');

        $batch = ConsignmentBatch::query()->firstOrFail();
        $item = ConsignmentItem::query()->firstOrFail();

        $this->assertSame($supplier->id, $batch->supplierId);
        $this->assertSame('ACTIVE', $batch->status);
        $this->assertSame(5, $item->initialQty);
        $this->assertSame(5, $item->remainingQty);
        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_recap_with_partial_payout_updates_ledger_and_keeps_batch_active(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 10, 'sellPrice' => 10000]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-1000',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'totalValue' => 100000,
            'receivedAt' => now(),
        ]);

        $item = ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 10,
            'receivedQty' => 10,
            'soldQty' => 0,
            'remainingQty' => 10,
            'sellPrice' => 10000,
            'supplierPrice' => 7000,
        ]);

        Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', now()->toDateString())
            ->set('recapSupplierId', $supplier->id)
            ->set('countItems', [[
                'itemId' => $item->id,
                'batchCode' => $batch->batchCode,
                'productName' => $product->name,
                'beforeQty' => 10,
                'physicalQty' => 6,
                'sellPrice' => 10000,
                'supplierPrice' => 7000,
            ]])
            ->set('payNowAmount', 10000)
            ->call('saveRecapAndPayout');

        $this->assertSame(6, $item->fresh()->remainingQty);
        $this->assertSame(4, $item->fresh()->soldQty);
        $this->assertSame(6, $product->fresh()->stock);

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'INCOME',
            'category' => 'Omset Supplier Manual (Non-POS)',
            'amount' => 40000,
        ]);

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'EXPENSE',
            'category' => 'Pembayaran Supplier Manual (Non-POS)',
            'amount' => 10000,
        ]);

        $this->assertSame('ACTIVE', $batch->fresh()->status);
    }

    public function test_full_payout_marks_batch_settled_when_stock_exhausted(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 5, 'sellPrice' => 12000]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-1001',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'totalValue' => 60000,
            'receivedAt' => now(),
        ]);

        $item = ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 5,
            'receivedQty' => 5,
            'soldQty' => 0,
            'remainingQty' => 5,
            'sellPrice' => 12000,
            'supplierPrice' => 9000,
        ]);

        Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', now()->toDateString())
            ->set('recapSupplierId', $supplier->id)
            ->set('countItems', [[
                'itemId' => $item->id,
                'batchCode' => $batch->batchCode,
                'productName' => $product->name,
                'beforeQty' => 5,
                'physicalQty' => 0,
                'sellPrice' => 12000,
                'supplierPrice' => 9000,
            ]])
            ->set('payNowAmount', 45000)
            ->call('saveRecapAndPayout');

        $this->assertSame(0, $item->fresh()->remainingQty);
        $this->assertSame(5, $item->fresh()->soldQty);
        $this->assertSame('SETTLED', $batch->fresh()->status);
    }

    public function test_recap_validation_for_overpay_and_physical_qty(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 3, 'sellPrice' => 10000]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-1002',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'receivedAt' => now(),
        ]);

        $item = ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 3,
            'receivedQty' => 3,
            'soldQty' => 0,
            'remainingQty' => 3,
            'sellPrice' => 10000,
            'supplierPrice' => 7000,
        ]);

        Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('recapSupplierId', $supplier->id)
            ->set('countItems', [[
                'itemId' => $item->id,
                'batchCode' => $batch->batchCode,
                'productName' => $product->name,
                'beforeQty' => 3,
                'physicalQty' => 0,
                'sellPrice' => 10000,
                'supplierPrice' => 7000,
            ]])
            ->set('payNowAmount', 30000)
            ->call('saveRecapAndPayout')
            ->assertHasErrors(['payNowAmount']);

        Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('recapSupplierId', $supplier->id)
            ->set('countItems', [[
                'itemId' => $item->id,
                'batchCode' => $batch->batchCode,
                'productName' => $product->name,
                'beforeQty' => 3,
                'physicalQty' => 9,
                'sellPrice' => 10000,
                'supplierPrice' => 7000,
            ]])
            ->set('payNowAmount', 0)
            ->call('saveRecapAndPayout')
            ->assertHasErrors(['countItems.0.physicalQty']);
    }

    public function test_stock_item_supplier_price_autofills_from_selected_product_buy_price(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['buyPrice' => 8450, 'sellPrice' => 12000]);

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('stockSupplierId', $supplier->id);

        $this->assertSame('', $component->instance()->stockItems[0]['supplierPrice']);

        $component->set('stockItems.0.productId', $product->id);

        $this->assertSame(8450.0, (float) ($component->instance()->stockItems[0]['supplierPrice'] ?? 0));
    }

    public function test_supplier_roster_contains_all_active_suppliers_for_date(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplierA = $this->makeSupplier();
        $supplierB = $this->makeSupplier();

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', now()->toDateString());

        $roster = collect($component->instance()->supplierRoster);

        $this->assertTrue($roster->contains(fn ($row) => $row['supplierId'] === $supplierA->id));
        $this->assertTrue($roster->contains(fn ($row) => $row['supplierId'] === $supplierB->id));

        $rowA = $roster->firstWhere('supplierId', $supplierA->id);
        $this->assertSame('Belum Diproses', $rowA['statusLabel']);
    }

    public function test_finalize_and_reopen_date_changes_no_delivery_status(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $date = now()->toDateString();

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', $date)
            ->call('finalizeDate');

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'SupplierDailyOps',
            'action' => 'COMPLETE',
            'description' => 'DAILY_FINALIZE:' . $date,
        ]);

        $refreshed = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', $date);

        $row = collect($refreshed->instance()->supplierRoster)->firstWhere('supplierId', $supplier->id);
        $this->assertSame('Tidak Kirim', $row['statusLabel']);

        $component->call('reopenDate');

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'SupplierDailyOps',
            'action' => 'REOPEN',
            'description' => 'DAILY_REOPEN:' . $date,
        ]);

        $refreshedAfterReopen = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', $date);

        $rowAfterReopen = collect($refreshedAfterReopen->instance()->supplierRoster)->firstWhere('supplierId', $supplier->id);
        $this->assertSame('Belum Diproses', $rowAfterReopen['statusLabel']);
    }

    public function test_date_supplier_lock_is_true_when_allocations_cover_date_payable(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 3, 'sellPrice' => 10000]);
        $date = Carbon::parse('2026-04-10');

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-3001',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'receivedAt' => $date,
        ]);

        $item = ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 3,
            'receivedQty' => 3,
            'soldQty' => 2,
            'remainingQty' => 1,
            'sellPrice' => 10000,
            'supplierPrice' => 7000,
        ]);

        ConsignmentItemCount::create([
            'consignmentItemId' => $item->id,
            'batchId' => $batch->id,
            'supplierId' => $supplier->id,
            'productId' => $product->id,
            'userId' => $admin->id,
            'beforeQty' => 3,
            'physicalQty' => 1,
            'soldDeltaQty' => 2,
            'soldDeltaAmount' => 20000,
            'payableDeltaAmount' => 14000,
            'marginDeltaAmount' => 6000,
            'countedAt' => $date->copy()->setTime(10, 0),
        ]);

        $payout = SupplierPayout::create([
            'payoutCode' => 'PAY-000001',
            'supplierId' => $supplier->id,
            'userId' => $admin->id,
            'payoutDate' => $date->copy()->addDay()->toDateString(),
            'grossDueAmount' => 14000,
            'paidAmount' => 14000,
            'outstandingAfter' => 0,
        ]);

        SupplierPayoutAllocation::create([
            'supplierPayoutId' => $payout->id,
            'batchId' => $batch->id,
            'consignmentItemId' => $item->id,
            'allocatedAmount' => 14000,
            'allocatedQtyEquivalent' => 2,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', $date->toDateString());

        $row = collect($component->instance()->supplierRoster)->firstWhere('supplierId', $supplier->id);

        $this->assertTrue($row['locked']);
        $this->assertSame('Lunas (Locked)', $row['statusLabel']);
    }

    public function test_copy_previous_day_draft_only_when_target_date_empty(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['buyPrice' => 6000]);

        $today = Carbon::parse('2026-04-11');
        $yesterday = $today->copy()->subDay();

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-3002',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'receivedAt' => $yesterday,
        ]);

        ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 2,
            'receivedQty' => 2,
            'soldQty' => 0,
            'remainingQty' => 2,
            'sellPrice' => 9000,
            'supplierPrice' => 6000,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', $today->toDateString())
            ->set('stockSupplierId', $supplier->id)
            ->call('copyPreviousDayDraft');

        $this->assertSame(1, count($component->instance()->stockItems));
        $this->assertSame((string) $product->id, $component->instance()->stockItems[0]['productId']);
        $this->assertSame(2, $component->instance()->stockItems[0]['qty']);

        $component
            ->set('stockItems', [[
                'productId' => $product->id,
                'qty' => 1,
                'supplierPrice' => 6000,
            ]])
            ->call('saveStockIn')
            ->set('stockItems', [
                ['productId' => '', 'qty' => 1, 'supplierPrice' => ''],
            ])
            ->call('copyPreviousDayDraft');

        $this->assertSame('', $component->instance()->stockItems[0]['productId']);
    }

    public function test_selected_date_summary_counts_supplier_ops_categories_only(): void
    {
        $admin = $this->makeUser('ADMIN');
        $date = '2026-04-12';

        FinancialTransaction::create([
            'type' => 'INCOME',
            'category' => 'Omset Supplier Manual (Non-POS)',
            'amount' => 50000,
            'transactionDate' => $date,
            'description' => 'Test income supplier',
            'userId' => $admin->id,
        ]);

        FinancialTransaction::create([
            'type' => 'EXPENSE',
            'category' => 'Pembayaran Supplier Manual (Non-POS)',
            'amount' => 12000,
            'transactionDate' => $date,
            'description' => 'Test payout supplier',
            'userId' => $admin->id,
        ]);

        FinancialTransaction::create([
            'type' => 'INCOME',
            'category' => 'Kategori Lain',
            'amount' => 99999,
            'transactionDate' => $date,
            'description' => 'Should be ignored',
            'userId' => $admin->id,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('selectedDate', $date);

        $summary = $component->instance()->selectedDateSummary;

        $this->assertSame(50000.0, (float) $summary['incomeSupplierOps']);
        $this->assertSame(12000.0, (float) $summary['expenseSupplierOps']);
        $this->assertSame(38000.0, (float) $summary['netSupplierOps']);
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'isActive' => true,
            'mustChangePassword' => false,
        ]);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create([
            'code' => 'SUP-' . Str::upper(Str::random(6)),
            'ownerName' => 'Owner Supplier',
            'businessName' => 'Supplier ' . Str::upper(Str::random(4)),
            'phone' => '08123' . random_int(100000, 999999),
            'email' => Str::lower(Str::random(8)) . '@supplier.test',
            'address' => 'Alamat supplier test',
            'password' => 'password123',
            'status' => 'ACTIVE',
            'isActive' => true,
        ]);
    }

    private function makeCategory(): Category
    {
        return Category::create([
            'name' => 'Kategori ' . Str::random(6),
            'description' => 'Kategori test',
            'icon' => 'BOX',
            'order' => 1,
            'isActive' => true,
        ]);
    }

    private function makeProduct(Supplier $supplier, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Produk ' . Str::random(6),
            'description' => 'Produk test supplier',
            'categoryId' => $this->makeCategory()->id,
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
            'buyPrice' => 7000,
            'sellPrice' => 10000,
            'stock' => 0,
            'threshold' => 2,
            'unit' => 'pcs',
            'ownershipType' => 'SUPPLIER',
            'status' => 'ACTIVE',
            'approvalStatus' => 'APPROVED',
            'isConsignment' => true,
            'isActive' => true,
            'supplierId' => $supplier->id,
        ], $overrides));
    }
}
