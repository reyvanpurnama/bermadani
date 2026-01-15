<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\ConsignmentBatch;
use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PosCustom extends Component
{
    public $search = '';
    public $categoryFilter = '';
    public $cart = [];
    public $selectedMember = null;
    public $members = []; // All active members for client-side search

    public $showPaymentModal = false;
    public $paymentMethod = 'CASH';
    public $cashReceived = 0;
    public $note = '';

    public $lastInvoice = null;

    public function mount()
    {
        // Block kasir yang belum check-in
        if (auth()->user()->isKasir()) {
            $hasActiveShift = CashierShift::getOpenShift(auth()->id());
            if (!$hasActiveShift) {
                session()->flash('error', 'Kamu harus check-in terlebih dahulu sebelum melakukan transaksi');
                return redirect()->route('kasir.dashboard');
            }
        }

        // Load all active members for client-side search
        $this->members = Member::select('id', 'name', 'nomorAnggota', 'unitKerja', 'tier', 'points')
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || !$product->isActive) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Produk tidak tersedia']);
            return;
        }

        if ($product->stock < 1) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Stok produk habis']);
            return;
        }

        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['productId'] == $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            if ($this->cart[$existingIndex]['quantity'] < $product->stock) {
                $this->cart[$existingIndex]['quantity']++;
                $this->cart[$existingIndex]['subtotal'] = $this->cart[$existingIndex]['quantity'] * $this->cart[$existingIndex]['price'];
            } else {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Stok tidak cukup']);
            }
        } else {
            $this->cart[] = [
                'productId' => $product->id,
                'name' => $product->name,
                'price' => $product->sellPrice,
                'quantity' => 1,
                'subtotal' => $product->sellPrice,
                'stock' => $product->stock,
            ];
        }
    }

    public function updateQuantity($index, $newQuantity)
    {
        if (!isset($this->cart[$index]))
            return;

        if ($newQuantity < 1) {
            $this->removeFromCart($index);
            return;
        }

        if ($newQuantity > $this->cart[$index]['stock']) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Stok tidak cukup']);
            return;
        }

        $this->cart[$index]['quantity'] = $newQuantity;
        $this->cart[$index]['subtotal'] = $newQuantity * $this->cart[$index]['price'];
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->selectedMember = null;
    }

    public function selectMember($memberId)
    {
        $member = Member::find($memberId);
        if ($member) {
            $this->selectedMember = [
                'id' => $member->id,
                'name' => $member->name,
                'nomorAnggota' => $member->nomorAnggota,
                'unitKerja' => $member->unitKerja ?? '',
                'tier' => $member->tier,
                'points' => $member->points,
            ];
        }
    }

    public function clearMember()
    {
        $this->selectedMember = null;
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Keranjang kosong']);
            return;
        }
        $this->showPaymentModal = true;
        $this->cashReceived = $this->getCartTotalProperty();
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->cashReceived = 0;
        $this->note = '';
    }

    public function processPayment()
    {
        // Validate cash payment
        if ($this->paymentMethod === 'CASH' && $this->cashReceived < $this->getCartTotalProperty()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Uang tidak cukup']);
            return;
        }

        // Validate SUKARELA payment - must have member and sufficient balance
        if ($this->paymentMethod === 'SUKARELA') {
            if (!$this->selectedMember) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih member terlebih dahulu']);
                return;
            }
            $member = Member::find($this->selectedMember['id']);
            if (!$member || $member->simpananSukarela < $this->getCartTotalProperty()) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Saldo Simpanan Sukarela tidak mencukupi']);
                return;
            }
        }

        DB::beginTransaction();
        try {
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'invoiceNumber' => $invoiceNumber,
                'memberId' => $this->selectedMember['id'] ?? null,
                'userId' => auth()->id(),
                'type' => 'SALE',
                'totalAmount' => $this->getCartTotalProperty(),
                'paymentMethod' => $this->paymentMethod,
                'status' => 'COMPLETED',
                'note' => $this->note,
                'date' => now(),
            ]);

            foreach ($this->cart as $item) {
                $product = Product::find($item['productId']);

                TransactionItem::create([
                    'transactionId' => $transaction->id,
                    'productId' => $product->id,
                    'quantity' => $item['quantity'],
                    'unitPrice' => $item['price'],
                    'totalPrice' => $item['subtotal'],
                    'cogsPerUnit' => $product->buyPrice ?? 0,
                    'totalCogs' => ($product->buyPrice ?? 0) * $item['quantity'],
                    'grossProfit' => $item['subtotal'] - (($product->buyPrice ?? 0) * $item['quantity']),
                ]);

                $product->reduceStock($item['quantity']);

                // Update consignment batch if this is a consignment product
                if ($product->isConsignment) {
                    $this->updateConsignmentSale($product->id, $item['quantity']);
                }
            }

            if ($this->selectedMember) {
                $member = Member::find($this->selectedMember['id']);

                // Deduct Simpanan Sukarela if paying with it
                if ($this->paymentMethod === 'SUKARELA') {
                    $member->payWithSukarela(
                        $this->getCartTotalProperty(),
                        'Belanja ' . $invoiceNumber
                    );
                }

                // Award points (1 point per Rp1.000)
                $pointsEarned = floor($this->getCartTotalProperty() / 1000);
                if ($pointsEarned > 0) {
                    $member->addPoints($pointsEarned, 'Belanja ' . $invoiceNumber, $transaction->id);
                }

                // Record purchase in member history
                $member->recordPurchase($this->getCartTotalProperty());
            }

            DB::commit();

            // Log activity for POS transaction
            ActivityLog::log(
                'CREATE',
                'Transaction',
                "Transaksi POS {$invoiceNumber} sebesar Rp " . number_format($transaction->totalAmount, 0, ',', '.'),
                $transaction,
                null,
                [
                    'invoiceNumber' => $invoiceNumber,
                    'totalAmount' => $transaction->totalAmount,
                    'paymentMethod' => $transaction->paymentMethod,
                    'itemCount' => count($this->cart),
                ]
            );

            $this->lastInvoice = $invoiceNumber;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Transaksi berhasil!']);

            $this->clearCart();
            $this->closePaymentModal();

            $this->dispatch('open-receipt', ['transactionId' => $transaction->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getCartItemCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getChangeProperty()
    {
        return max(0, $this->cashReceived - $this->getCartTotalProperty());
    }

    public function getProductsProperty()
    {
        return Product::with('category')
            ->where('isActive', true)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            }))
            ->when($this->categoryFilter, fn($q) => $q->where('categoryId', $this->categoryFilter))
            ->orderBy('name')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return Category::where('isActive', true)->orderBy('order')->get();
    }

    /**
     * Update consignment batch when a consignment product is sold
     * Uses FIFO to determine which batch to deduct from
     */
    private function updateConsignmentSale(int $productId, int $quantity): void
    {
        $remainingQty = $quantity;

        while ($remainingQty > 0) {
            $consignmentItem = ConsignmentBatch::findActiveItemForProduct($productId);

            if (!$consignmentItem) {
                // No active consignment batch found, skip
                break;
            }

            // Determine how much to deduct from this batch
            $deductQty = min($remainingQty, $consignmentItem->remainingQty);

            // Record the sale
            $consignmentItem->recordSale($deductQty);

            $remainingQty -= $deductQty;
        }
    }

    public function render()
    {
        return view('livewire.pos-custom');
    }
}
