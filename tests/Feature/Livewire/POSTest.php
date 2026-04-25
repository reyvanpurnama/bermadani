<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PosCustom;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\POSCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class POSTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully_for_admin(): void
    {
        $admin = $this->makeUser('ADMIN');
        $this->makeProduct();

        Livewire::actingAs($admin)
            ->test(PosCustom::class)
            ->assertStatus(200);
    }

    public function test_barcode_enter_adds_exact_sku_to_cart(): void
    {
        $admin = $this->makeUser('ADMIN');
        $product = $this->makeProduct([
            'name' => 'Aqua Botol',
            'sku' => '899999900001',
            'sellPrice' => 4000,
        ]);

        Livewire::actingAs($admin)
            ->test(PosCustom::class)
            ->set('search', '899999900001')
            ->call('addSearchResultToCart')
            ->assertSet('search', '')
            ->assertSet('cart.0.productId', $product->id)
            ->assertSet('cart.0.quantity', 1);
    }

    public function test_cash_checkout_creates_transaction_and_updates_stock(): void
    {
        $admin = $this->makeUser('ADMIN');
        $product = $this->makeProduct([
            'buyPrice' => 2500,
            'sellPrice' => 5000,
            'stock' => 10,
        ]);

        Livewire::actingAs($admin)
            ->test(PosCustom::class)
            ->call('addToCart', $product->id)
            ->call('openPaymentModal')
            ->set('cashReceived', 10000)
            ->call('processPayment')
            ->assertDispatched('open-receipt')
            ->assertSet('cart', []);

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame('CASH', $transaction->paymentMethod);
        $this->assertSame('COMPLETED', $transaction->status);
        $this->assertNotNull($transaction->checkoutToken);
        $this->assertStringStartsWith('INV-', $transaction->invoiceNumber);
        $this->assertEquals(5000, (float) $transaction->totalAmount);

        $this->assertDatabaseHas('transaction_items', [
            'transactionId' => $transaction->id,
            'productId' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'productId' => $product->id,
            'movementType' => 'SALE_OUT',
            'quantity' => -1,
            'referenceType' => 'SALE',
            'referenceId' => (string) $transaction->id,
        ]);

        $this->assertSame(9, $product->fresh()->stock);

        $this->actingAs($admin)
            ->get(route('transaction.receipt', $transaction))
            ->assertOk()
            ->assertSee($transaction->invoiceNumber);
    }

    public function test_sukarela_checkout_deducts_balance_and_records_saving(): void
    {
        $admin = $this->makeUser('ADMIN');
        $member = $this->makeMember(['simpananSukarela' => 20000]);
        $product = $this->makeProduct([
            'sellPrice' => 7000,
            'stock' => 3,
        ]);

        Livewire::actingAs($admin)
            ->test(PosCustom::class)
            ->call('addToCart', $product->id)
            ->call('selectMember', $member->id)
            ->set('paymentMethod', 'SUKARELA')
            ->call('processPayment')
            ->assertDispatched('open-receipt');

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame('SUKARELA', $transaction->paymentMethod);
        $this->assertEquals(13000, (float) $member->fresh()->simpananSukarela);
        $this->assertDatabaseHas('savings', [
            'memberId' => $member->id,
            'type' => 'WITHDRAWAL',
            'amount' => 7000,
        ]);
        $this->assertDatabaseHas('member_points_histories', [
            'memberId' => $member->id,
            'transactionId' => $transaction->id,
            'type' => 'EARNED',
            'points' => 7,
        ]);
    }

    public function test_duplicate_checkout_token_is_idempotent(): void
    {
        $admin = $this->makeUser('ADMIN');
        $product = $this->makeProduct(['sellPrice' => 5000, 'stock' => 10]);
        $service = app(POSCheckoutService::class);

        $first = $service->checkout(
            cart: [['productId' => $product->id, 'quantity' => 2]],
            memberId: null,
            userId: $admin->id,
            paymentMethod: 'CASH',
            cashReceived: 10000,
            checkoutToken: 'checkout-token-1',
        );

        $second = $service->checkout(
            cart: [['productId' => $product->id, 'quantity' => 2]],
            memberId: null,
            userId: $admin->id,
            paymentMethod: 'CASH',
            cashReceived: 10000,
            checkoutToken: 'checkout-token-1',
        );

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Transaction::count());
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_cashier_without_active_shift_cannot_checkout(): void
    {
        $cashier = $this->makeUser('KASIR');
        $product = $this->makeProduct(['stock' => 2]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Kasir harus check-in');

        app(POSCheckoutService::class)->checkout(
            cart: [['productId' => $product->id, 'quantity' => 1]],
            memberId: null,
            userId: $cashier->id,
            paymentMethod: 'CASH',
            cashReceived: 5000,
            checkoutToken: 'no-shift-token',
        );
    }

    public function test_cashier_checkout_records_active_shift(): void
    {
        $cashier = $this->makeUser('KASIR');
        $shift = CashierShift::create([
            'user_id' => $cashier->id,
            'opening_cash' => 50000,
            'check_in_at' => now(),
            'status' => 'OPEN',
        ]);
        $product = $this->makeProduct(['sellPrice' => 3000, 'stock' => 5]);

        $transaction = app(POSCheckoutService::class)->checkout(
            cart: [['productId' => $product->id, 'quantity' => 1]],
            memberId: null,
            userId: $cashier->id,
            paymentMethod: 'CASH',
            cashReceived: 3000,
            checkoutToken: 'shift-token',
        );

        $this->assertSame($shift->id, $transaction->cashierShiftId);
    }

    public function test_out_of_stock_product_is_rejected(): void
    {
        $admin = $this->makeUser('ADMIN');
        $product = $this->makeProduct(['stock' => 0]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stok');

        app(POSCheckoutService::class)->checkout(
            cart: [['productId' => $product->id, 'quantity' => 1]],
            memberId: null,
            userId: $admin->id,
            paymentMethod: 'CASH',
            cashReceived: 5000,
            checkoutToken: 'out-of-stock-token',
        );
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'isActive' => true,
            'mustChangePassword' => false,
        ]);
    }

    private function makeCategory(): Category
    {
        return Category::create([
            'name' => 'Test Category '.Str::random(8),
            'description' => 'Produk test',
            'icon' => 'BOX',
            'order' => 1,
            'isActive' => true,
        ]);
    }

    private function makeProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Produk Test '.Str::random(8),
            'description' => 'Produk untuk test POS',
            'categoryId' => $this->makeCategory()->id,
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'buyPrice' => 2000,
            'sellPrice' => 5000,
            'stock' => 10,
            'threshold' => 2,
            'unit' => 'pcs',
            'ownershipType' => 'TOKO',
            'status' => 'ACTIVE',
            'approvalStatus' => 'APPROVED',
            'isConsignment' => false,
            'isActive' => true,
        ], $attributes));
    }

    private function makeMember(array $attributes = []): Member
    {
        $user = $this->makeUser('MEMBER');

        return Member::create(array_merge([
            'userId' => $user->id,
            'nomorAnggota' => '2600'.random_int(1000, 9999),
            'name' => 'Member Test',
            'email' => 'member-'.Str::random(8).'@example.test',
            'phone' => '081234567890',
            'gender' => 'MALE',
            'unitKerja' => 'UMUM',
            'joinDate' => now(),
            'status' => 'ACTIVE',
            'isMemberKoperasi' => true,
            'simpananSukarela' => 0,
        ], $attributes));
    }
}
