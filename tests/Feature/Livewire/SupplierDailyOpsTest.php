<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SupplierDailyOps;
use App\Models\Category;
use App\Models\ConsignmentBatch;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('stockSupplierId', $supplier->id)
            ->set('stockDate', now()->toDateString())
            ->set('stockItems', [[
                'productId' => $product->id,
                'qty' => 5,
                'supplierPrice' => 6000,
            ]])
            ->call('saveStockIn');

        $component->assertSet('tab', 'stock-in');

        $batch = ConsignmentBatch::query()->firstOrFail();
        $item = ConsignmentItem::query()->firstOrFail();

        $this->assertSame($supplier->id, $batch->supplierId);
        $this->assertSame('ACTIVE', $batch->status);
        $this->assertSame(5, $item->initialQty);
        $this->assertSame(5, $item->remainingQty);
        $this->assertSame(7, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'productId' => $product->id,
            'movementType' => 'CONSIGNMENT_IN',
            'quantity' => 5,
            'referenceType' => 'CONSIGNMENT_BATCH',
            'referenceId' => $batch->id,
        ]);
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

        $this->assertDatabaseHas('supplier_payouts', [
            'supplierId' => $supplier->id,
            'grossDueAmount' => 28000,
            'paidAmount' => 10000,
            'outstandingAfter' => 18000,
        ]);

        $this->assertDatabaseHas('consignment_item_counts', [
            'consignmentItemId' => $item->id,
            'soldDeltaQty' => 4,
            'soldDeltaAmount' => 40000,
            'payableDeltaAmount' => 28000,
        ]);

        $this->assertSame('ACTIVE', $batch->fresh()->status);
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

        $this->assertDatabaseHas('supplier_payouts', [
            'supplierId' => $supplier->id,
            'grossDueAmount' => 45000,
            'paidAmount' => 45000,
            'outstandingAfter' => 0,
        ]);
    }

    public function test_recap_rejects_overpayment_amount(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 2, 'sellPrice' => 10000]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-1002',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'receivedAt' => now(),
        ]);

        $item = ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 2,
            'receivedQty' => 2,
            'soldQty' => 0,
            'remainingQty' => 2,
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
                'beforeQty' => 2,
                'physicalQty' => 0,
                'sellPrice' => 10000,
                'supplierPrice' => 7000,
            ]])
            ->set('payNowAmount', 20000)
            ->call('saveRecapAndPayout')
            ->assertHasErrors(['payNowAmount']);
    }

    public function test_recap_rejects_physical_qty_more_than_recorded(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 3]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-1003',
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
                'physicalQty' => 5,
                'sellPrice' => 10000,
                'supplierPrice' => 7000,
            ]])
            ->set('payNowAmount', 0)
            ->call('saveRecapAndPayout')
            ->assertHasErrors(['countItems.0.physicalQty']);
    }

    public function test_stepper_metadata_reflects_active_completed_and_locked_states(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();

        $component = Livewire::actingAs($admin)->test(SupplierDailyOps::class);

        $this->assertSame(1, $component->instance()->currentStep);
        $this->assertSame('Langkah 1 dari 2', $component->instance()->stepProgressText);
        $this->assertTrue($component->instance()->step2SoftLocked);
        $this->assertSame('', $component->instance()->payNowAmount);
        $this->assertSame('active', $component->instance()->stepperSteps[0]['status']);
        $this->assertSame('inactive', $component->instance()->stepperSteps[1]['status']);

        $component->call('setTab', 'recap');

        $this->assertSame(2, $component->instance()->currentStep);
        $this->assertSame('locked', $component->instance()->stepperSteps[1]['status']);
        $this->assertTrue($component->instance()->step2SoftLocked);

        $component->set('recapSupplierId', $supplier->id);

        $this->assertFalse($component->instance()->step2SoftLocked);
        $this->assertSame('', $component->instance()->payNowAmount);
        $this->assertSame('active', $component->instance()->stepperSteps[1]['status']);
    }

    public function test_cta_flags_follow_soft_gate_and_input_validity(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 3]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-2001',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'receivedAt' => now(),
        ]);

        ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 3,
            'receivedQty' => 3,
            'soldQty' => 0,
            'remainingQty' => 3,
            'sellPrice' => 10000,
            'supplierPrice' => 7000,
        ]);

        $component = Livewire::actingAs($admin)->test(SupplierDailyOps::class);

        $this->assertFalse($component->instance()->canSubmitStockIn);
        $this->assertFalse($component->instance()->canSubmitRecap);

        $component
            ->set('stockSupplierId', $supplier->id)
            ->set('stockDate', now()->toDateString())
            ->set('stockItems', [[
                'productId' => $product->id,
                'qty' => 2,
                'supplierPrice' => 6000,
            ]]);

        $this->assertTrue($component->instance()->canSubmitStockIn);

        $component->set('stockItems.0.qty', 0);
        $this->assertFalse($component->instance()->canSubmitStockIn);

        $component
            ->set('recapSupplierId', $supplier->id)
            ->set('payNowAmount', 0);

        $this->assertFalse($component->instance()->step2SoftLocked);
        $this->assertTrue($component->instance()->canSubmitRecap);

        $component->set('payNowAmount', -1);
        $this->assertFalse($component->instance()->canSubmitRecap);
    }

    public function test_fill_pay_now_from_supplier_rights_uses_preview_payable(): void
    {
        $admin = $this->makeUser('ADMIN');
        $supplier = $this->makeSupplier();
        $product = $this->makeProduct($supplier, ['stock' => 4, 'sellPrice' => 10000]);

        $batch = ConsignmentBatch::create([
            'batchCode' => 'BCH-2002',
            'supplierId' => $supplier->id,
            'status' => 'ACTIVE',
            'receivedAt' => now(),
        ]);

        $item = ConsignmentItem::create([
            'batchId' => $batch->id,
            'productId' => $product->id,
            'initialQty' => 4,
            'receivedQty' => 4,
            'soldQty' => 0,
            'remainingQty' => 4,
            'sellPrice' => 10000,
            'supplierPrice' => 7000,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(SupplierDailyOps::class)
            ->set('recapSupplierId', $supplier->id)
            ->set('countItems', [[
                'itemId' => $item->id,
                'batchCode' => $batch->batchCode,
                'productName' => $product->name,
                'beforeQty' => 4,
                'physicalQty' => 1,
                'sellPrice' => 10000,
                'supplierPrice' => 7000,
            ]])
            ->call('fillPayNowFromSupplierRights');

        $this->assertSame(21000.0, (float) $component->instance()->payNowAmount);
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
