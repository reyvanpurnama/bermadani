<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Member;
use App\Models\Product;
use App\Services\POSCheckoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Throwable;

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

    public $checkoutToken = null;

    public $lastInvoice = null;

    public $showNewMemberModal = false;

    public $newMemberName = '';

    public $newMemberPhone = '';

    public $newMemberGender = 'MALE'; // Default

    public $newMemberUnit = '';

    public function mount()
    {
        // Block kasir yang belum check-in
        if (auth()->user()->isKasir()) {
            $hasActiveShift = CashierShift::getOpenShift(auth()->id());
            if (! $hasActiveShift) {
                session()->flash('error', 'Kamu harus check-in terlebih dahulu sebelum melakukan transaksi');

                return redirect()->route('kasir.dashboard');
            }
        }

        // Load all active members for client-side search
        $this->loadMembers();
    }

    public function loadMembers()
    {
        $this->members = Member::select('id', 'name', 'nomorAnggota', 'phone', 'unitKerja', 'tier', 'points')
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->limit(300)
            ->get()
            ->toArray();
    }

    public function createNewMember()
    {
        $this->reset(['newMemberName', 'newMemberPhone', 'newMemberGender', 'newMemberUnit']);
        $this->showNewMemberModal = true;
    }

    public function storeNewMember()
    {
        $this->validate([
            'newMemberName' => 'required|string|min:3',
            'newMemberPhone' => 'required|numeric|digits_between:10,13',
            'newMemberGender' => 'required|in:MALE,FEMALE',
        ]);

        DB::beginTransaction();
        try {
            // Normalize phone number to 08... format
            $phone = $this->newMemberPhone;
            // Remove any non-digit characters just in case
            $phone = preg_replace('/\D/', '', $phone);
            // If starts with 62 (country code), replace with 0
            if (str_starts_with($phone, '62')) {
                $phone = '0'.substr($phone, 2);
            }
            // If starts with 8 (without leading 0), prepend 0
            if (str_starts_with($phone, '8')) {
                $phone = '0'.$phone;
            }

            // 1. Create User Account
            $dummyEmail = $phone.'@bermadani.id';

            // Check if user exists (by email/phone logic - simplified)
            if (\App\Models\User::where('email', $dummyEmail)->exists()) {
                throw new \Exception('Nomor HP sudah terdaftar di sistem.');
            }

            $user = \App\Models\User::create([
                'name' => $this->newMemberName,
                'email' => $dummyEmail,
                'password' => bcrypt('password'), // Consolidated default password
                'role' => 'MEMBER',
            ]);

            // 2. Create Member Record (Member Koperasi - for transaction history)
            $member = Member::create([
                'userId' => $user->id,
                'nomorAnggota' => Member::generateNomorAnggota(),
                'name' => $this->newMemberName,
                'email' => $dummyEmail,
                'phone' => $phone, // Use normalized phone
                'gender' => $this->newMemberGender,
                'unitKerja' => $this->newMemberUnit ?: 'UMUM', // Default to UMUM if empty
                'status' => 'ACTIVE',
                'joinDate' => now(),
                'isMemberKoperasi' => false, // Toko retail member usually not full koperasi member initially
            ]);

            // 3. Create Member Minimarket (for loyalty program)
            \App\Models\MemberMinimarket::create([
                'userId' => $user->id,
                'memberNumber' => 'MM-'.date('y').'-'.str_pad(\App\Models\MemberMinimarket::whereYear('created_at', date('Y'))->count() + 1, 5, '0', STR_PAD_LEFT),
                'cardNumber' => 'BC'.date('y').str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT),
                'points' => 0,
                'totalSpent' => 0,
                'status' => 'ACTIVE',
                'registeredBy' => auth()->id(),
            ]);

            DB::commit();

            // 4. Auto Select Member
            $this->loadMembers(); // Reload list
            $this->selectMember($member->id);
            $this->showNewMemberModal = false;

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Member berhasil dibuat & dipilih!']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal membuat member: '.$e->getMessage()]);
        }
    }

    public function addToCart($productId)
    {
        $product = Product::query()
            ->select('id', 'name', 'sku', 'sellPrice', 'stock', 'isActive', 'status', 'approvalStatus')
            ->find($productId);

        if (! $product || ! $product->isActive || $product->status !== 'ACTIVE') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Produk tidak tersedia']);

            return;
        }

        if ($product->approvalStatus && $product->approvalStatus !== 'APPROVED') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Produk belum disetujui']);

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

    public function addSearchResultToCart()
    {
        $term = trim($this->search);

        if ($term === '') {
            return;
        }

        $baseQuery = Product::query()
            ->where('isActive', true)
            ->where('status', 'ACTIVE')
            ->where('stock', '>', 0)
            ->where(function ($query) {
                $query->whereNull('approvalStatus')
                    ->orWhere('approvalStatus', 'APPROVED');
            });

        $product = (clone $baseQuery)
            ->where('sku', $term)
            ->first();

        if (! $product) {
            $matches = (clone $baseQuery)
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('sku', 'like', '%'.$term.'%');
                })
                ->limit(2)
                ->get();

            if ($matches->count() === 1) {
                $product = $matches->first();
            }
        }

        if (! $product) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Produk belum spesifik. Pilih dari daftar.']);

            return;
        }

        $this->addToCart($product->id);
        $this->search = '';
    }

    public function updateQuantity($index, $newQuantity)
    {
        if (! isset($this->cart[$index])) {
            return;
        }

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
        $this->checkoutToken = null;
        $this->paymentMethod = 'CASH';
        $this->cashReceived = 0;
        $this->note = '';
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
        $this->checkoutToken ??= (string) Str::uuid();
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
        if (empty($this->cart)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Keranjang kosong']);

            return;
        }

        // Validate cash payment
        if ($this->paymentMethod === 'CASH' && $this->cashReceived < $this->getCartTotalProperty()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Uang tidak cukup']);

            return;
        }

        // Validate SUKARELA payment - must have member and sufficient balance
        if ($this->paymentMethod === 'SUKARELA') {
            if (! $this->selectedMember) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih member terlebih dahulu']);

                return;
            }
            $member = Member::find($this->selectedMember['id']);
            if (! $member || $member->simpananSukarela < $this->getCartTotalProperty()) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Saldo Simpanan Sukarela tidak mencukupi']);

                return;
            }
        }

        $this->checkoutToken ??= (string) Str::uuid();

        try {
            $transaction = app(POSCheckoutService::class)->checkout(
                cart: $this->cart,
                memberId: $this->selectedMember['id'] ?? null,
                userId: auth()->id(),
                paymentMethod: $this->paymentMethod,
                cashReceived: $this->cashReceived,
                note: $this->note,
                checkoutToken: $this->checkoutToken,
            );

            // Log activity for POS transaction
            if ($transaction->wasRecentlyCreated) {
                ActivityLog::log(
                    'CREATE',
                    'Transaction',
                    "Transaksi POS {$transaction->invoiceNumber} sebesar Rp ".number_format($transaction->totalAmount, 0, ',', '.'),
                    $transaction,
                    null,
                    [
                        'invoiceNumber' => $transaction->invoiceNumber,
                        'totalAmount' => $transaction->totalAmount,
                        'paymentMethod' => $transaction->paymentMethod,
                        'itemCount' => count($this->cart),
                    ]
                );
            }

            $this->lastInvoice = $transaction->invoiceNumber;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Transaksi berhasil!']);

            $this->clearCart();
            $this->closePaymentModal();

            $this->dispatch('open-receipt', ['transactionId' => $transaction->id]);

        } catch (Throwable $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal memproses transaksi: '.$e->getMessage()]);
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
        return Product::query()
            ->select('id', 'name', 'sku', 'categoryId', 'sellPrice', 'stock', 'image', 'isConsignment', 'isActive', 'status', 'approvalStatus')
            ->with('category:id,name,icon')
            ->where('isActive', true)
            ->where('status', 'ACTIVE')
            ->where('stock', '>', 0)
            ->where(function ($query) {
                $query->whereNull('approvalStatus')
                    ->orWhere('approvalStatus', 'APPROVED');
            })
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            }))
            ->when($this->categoryFilter, fn ($q) => $q->where('categoryId', $this->categoryFilter))
            ->orderBy('name')
            ->limit(120)
            ->get();
    }

    public function getCategoriesProperty()
    {
        return Category::where('isActive', true)->orderBy('order')->get();
    }

    public function render()
    {
        return view('livewire.pos-custom');
    }
}
