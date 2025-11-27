<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Member;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;

class POS extends Component
{
    // Search & Filter
    public string $search = '';
    public ?int $categoryFilter = null;

    // Cart
    public array $cart = [];

    // Payment
    public bool $showPaymentModal = false;
    public string $paymentMethod = 'CASH';
    public float $cashReceived = 0;
    public ?string $memberId = null;
    public string $memberSearch = '';
    public ?array $selectedMember = null;
    public string $note = '';

    // Last transaction
    public ?string $lastInvoice = null;

    public function mount()
    {
        $this->cashReceived = 0;
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('isActive', true)
            ->where('stock', '>', 0)
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->when($this->categoryFilter, fn($q) => $q->where('categoryId', $this->categoryFilter))
            ->with('category')
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::where('isActive', true)->orderBy('order')->get();
    }

    #[Computed]
    public function members()
    {
        if (strlen($this->memberSearch) < 2) return collect();
        
        return Member::where('status', 'ACTIVE')
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->memberSearch}%")
                  ->orWhere('nomorAnggota', 'like', "%{$this->memberSearch}%")
                  ->orWhere('phone', 'like', "%{$this->memberSearch}%");
            })
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function cartTotal(): float
    {
        return collect($this->cart)->sum(fn($item) => $item['subtotal']);
    }

    #[Computed]
    public function cartItemCount(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    #[Computed]
    public function change(): float
    {
        return max(0, $this->cashReceived - $this->cartTotal);
    }

    public function addToCart(int $productId)
    {
        $product = Product::find($productId);
        if (!$product || !$product->isActive || $product->stock <= 0) {
            $this->dispatch('notify', type: 'error', message: 'Produk tidak tersedia');
            return;
        }

        $existingIndex = collect($this->cart)->search(fn($item) => $item['id'] === $productId);

        if ($existingIndex !== false) {
            $currentQty = $this->cart[$existingIndex]['quantity'];
            if ($currentQty >= $product->stock) {
                $this->dispatch('notify', type: 'error', message: 'Stok tidak cukup');
                return;
            }
            $this->cart[$existingIndex]['quantity']++;
            $this->cart[$existingIndex]['subtotal'] = $this->cart[$existingIndex]['quantity'] * $this->cart[$existingIndex]['price'];
        } else {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->sellPrice,
                'quantity' => 1,
                'subtotal' => (float) $product->sellPrice,
                'stock' => $product->stock,
                'unit' => $product->unit,
                'isConsignment' => $product->isConsignment,
                'supplierId' => $product->supplierId,
            ];
        }

        $this->dispatch('notify', type: 'success', message: "{$product->name} ditambahkan");
    }

    public function updateQuantity(int $index, int $quantity)
    {
        if (!isset($this->cart[$index])) return;

        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }

        if ($quantity > $this->cart[$index]['stock']) {
            $this->dispatch('notify', type: 'error', message: 'Stok tidak cukup');
            return;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['subtotal'] = $quantity * $this->cart[$index]['price'];
    }

    public function removeFromCart(int $index)
    {
        if (isset($this->cart[$index])) {
            $name = $this->cart[$index]['name'];
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
            $this->dispatch('notify', type: 'info', message: "{$name} dihapus");
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->selectedMember = null;
        $this->memberId = null;
        $this->note = '';
    }

    public function selectMember(int $id)
    {
        $member = Member::find($id);
        if ($member) {
            $this->selectedMember = [
                'id' => $member->id,
                'name' => $member->name,
                'nomorAnggota' => $member->nomorAnggota,
                'tier' => $member->tier,
                'points' => $member->points,
            ];
            $this->memberId = $member->id;
            $this->memberSearch = '';
        }
    }

    public function clearMember()
    {
        $this->selectedMember = null;
        $this->memberId = null;
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', type: 'error', message: 'Keranjang kosong');
            return;
        }
        $this->cashReceived = $this->cartTotal;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', type: 'error', message: 'Keranjang kosong');
            return;
        }

        if ($this->paymentMethod === 'CASH' && $this->cashReceived < $this->cartTotal) {
            $this->dispatch('notify', type: 'error', message: 'Uang tidak cukup');
            return;
        }

        try {
            \DB::beginTransaction();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Create transaction
            $transaction = Transaction::create([
                'invoiceNumber' => $invoiceNumber,
                'memberId' => $this->memberId,
                'type' => 'SALE',
                'totalAmount' => $this->cartTotal,
                'paymentMethod' => $this->paymentMethod,
                'status' => 'COMPLETED',
                'note' => $this->note ?: null,
                'date' => now(),
            ]);

            // Create transaction items & update stock
            foreach ($this->cart as $item) {
                $product = Product::find($item['id']);
                
                TransactionItem::create([
                    'transactionId' => $transaction->id,
                    'productId' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unitPrice' => $item['price'],
                    'totalPrice' => $item['subtotal'],
                    'cogsPerUnit' => $product->avgCost ?? $product->buyPrice ?? 0,
                    'totalCogs' => ($product->avgCost ?? $product->buyPrice ?? 0) * $item['quantity'],
                    'grossProfit' => $item['subtotal'] - (($product->avgCost ?? $product->buyPrice ?? 0) * $item['quantity']),
                ]);

                // Reduce stock
                $product->reduceStock($item['quantity'], 'SALE_OUT', "Invoice: {$invoiceNumber}");
            }

            // Update member points if applicable
            if ($this->memberId) {
                $member = Member::find($this->memberId);
                if ($member) {
                    $pointsEarned = floor($this->cartTotal / 10000); // 1 poin per 10rb
                    $member->addPoints($pointsEarned, "Pembelian {$invoiceNumber}");
                    $member->recordPurchase($this->cartTotal);
                }
            }

            \DB::commit();

            $this->lastInvoice = $invoiceNumber;
            $this->clearCart();
            $this->showPaymentModal = false;

            $this->dispatch('notify', type: 'success', message: "Transaksi berhasil! Invoice: {$invoiceNumber}");
            $this->dispatch('transaction-completed', invoiceNumber: $invoiceNumber);

        } catch (\Exception $e) {
            \DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.p-o-s');
    }
}
