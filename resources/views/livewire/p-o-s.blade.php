<div class="space-y-6" x-data="{ showPayment: @entangle('showPaymentModal') }">
    {{-- Notification listener --}}
    <div x-on:notify.window="
        $notification = $event.detail;
        if ($notification.type === 'success') {
            new FilamentNotification().title($notification.message).success().send();
        } else if ($notification.type === 'error') {
            new FilamentNotification().title($notification.message).danger().send();
        } else {
            new FilamentNotification().title($notification.message).info().send();
        }
    "></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Products --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Search & Filter --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="🔍 Cari produk atau scan barcode..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500"
                        >
                    </div>
                    <select 
                        wire:model.live="categoryFilter"
                        class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($this->products as $product)
                    <button 
                        wire:click="addToCart({{ $product->id }})"
                        wire:loading.attr="disabled"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-all p-4 text-left group hover:ring-2 hover:ring-primary-500"
                    >
                        <div class="mb-2">
                            <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ $product->category?->name ?? '-' }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate group-hover:text-primary-600">
                            {{ $product->name }}
                        </h3>
                        @if($product->sku)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</p>
                        @endif
                        <div class="mt-2 flex justify-between items-end">
                            <span class="text-lg font-bold text-primary-600">
                                Rp {{ number_format($product->sellPrice, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Stok: {{ $product->stock }}
                            </span>
                        </div>
                        @if($product->isConsignment)
                            <span class="mt-1 inline-block text-xs px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">
                                📦 Titipan
                            </span>
                        @endif
                    </button>
                @empty
                    <div class="col-span-full text-center py-12 bg-white dark:bg-gray-800 rounded-xl">
                        <div class="text-gray-400 text-6xl mb-4">📦</div>
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada produk ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Cart --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow sticky top-4">
                {{-- Cart Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        🛒 Keranjang 
                        @if($this->cartItemCount > 0)
                            <span class="bg-primary-500 text-white text-sm px-2 py-0.5 rounded-full">{{ $this->cartItemCount }}</span>
                        @endif
                    </h2>
                    @if(count($cart) > 0)
                        <button wire:click="clearCart" class="text-red-500 hover:text-red-700 text-sm">
                            🗑️ Hapus Semua
                        </button>
                    @endif
                </div>

                {{-- Member Selection --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    @if($selectedMember)
                        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                            <div>
                                <p class="font-semibold text-green-800 dark:text-green-300">{{ $selectedMember['name'] }}</p>
                                <p class="text-xs text-green-600 dark:text-green-400">
                                    {{ $selectedMember['nomorAnggota'] }} • {{ $selectedMember['tier'] }} • {{ $selectedMember['points'] }} pts
                                </p>
                            </div>
                            <button wire:click="clearMember" class="text-red-500 hover:text-red-700">✕</button>
                        </div>
                    @else
                        <input 
                            type="text"
                            wire:model.live.debounce.300ms="memberSearch"
                            placeholder="🔍 Cari member (opsional)..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                        @if($this->members->count() > 0)
                            <div class="mt-2 max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                                @foreach($this->members as $member)
                                    <button 
                                        wire:click="selectMember({{ $member->id }})"
                                        class="w-full px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-sm"
                                    >
                                        <span class="font-medium">{{ $member->name }}</span>
                                        <span class="text-gray-500 text-xs">• {{ $member->nomorAnggota }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Cart Items --}}
                <div class="max-h-80 overflow-y-auto">
                    @forelse($cart as $index => $item)
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-gray-500">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <button 
                                    wire:click="removeFromCart({{ $index }})"
                                    class="text-red-400 hover:text-red-600 ml-2"
                                >
                                    ✕
                                </button>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <button 
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                        class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center"
                                    >-</button>
                                    <span class="w-8 text-center font-bold">{{ $item['quantity'] }}</span>
                                    <button 
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                        class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center"
                                    >+</button>
                                </div>
                                <span class="font-bold text-primary-600">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400">
                            <div class="text-4xl mb-2">🛒</div>
                            <p>Keranjang kosong</p>
                            <p class="text-xs">Klik produk untuk menambahkan</p>
                        </div>
                    @endforelse
                </div>

                {{-- Cart Total & Pay --}}
                @if(count($cart) > 0)
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total</span>
                            <span class="text-2xl font-bold text-primary-600">
                                Rp {{ number_format($this->cartTotal, 0, ',', '.') }}
                            </span>
                        </div>
                        <button 
                            wire:click="openPaymentModal"
                            class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2"
                        >
                            💳 Bayar Sekarang
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    <div 
        x-show="showPayment"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition
    >
        <div class="flex min-h-screen items-center justify-center p-4">
            <div 
                x-show="showPayment"
                class="fixed inset-0 bg-black/50"
                x-on:click="showPayment = false"
            ></div>
            
            <div 
                x-show="showPayment"
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6"
                x-transition
            >
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">💳 Pembayaran</h3>
                
                <div class="space-y-4">
                    {{-- Total --}}
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Bayar</p>
                        <p class="text-3xl font-bold text-primary-600">
                            Rp {{ number_format($this->cartTotal, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button 
                                wire:click="$set('paymentMethod', 'CASH')"
                                class="py-3 rounded-lg border-2 transition-colors {{ $paymentMethod === 'CASH' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700' : 'border-gray-300 dark:border-gray-600' }}"
                            >
                                💵 Cash
                            </button>
                            <button 
                                wire:click="$set('paymentMethod', 'TRANSFER')"
                                class="py-3 rounded-lg border-2 transition-colors {{ $paymentMethod === 'TRANSFER' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700' : 'border-gray-300 dark:border-gray-600' }}"
                            >
                                🏦 Transfer
                            </button>
                            <button 
                                wire:click="$set('paymentMethod', 'CREDIT')"
                                class="py-3 rounded-lg border-2 transition-colors {{ $paymentMethod === 'CREDIT' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700' : 'border-gray-300 dark:border-gray-600' }}"
                            >
                                💳 Kredit
                            </button>
                        </div>
                    </div>

                    {{-- Cash Input --}}
                    @if($paymentMethod === 'CASH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Uang Diterima</label>
                            <input 
                                type="number"
                                wire:model.live="cashReceived"
                                class="w-full px-4 py-3 text-xl font-bold border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center"
                            >
                            <div class="mt-2 grid grid-cols-4 gap-2">
                                @foreach([10000, 20000, 50000, 100000] as $amount)
                                    <button 
                                        wire:click="$set('cashReceived', {{ $amount }})"
                                        class="py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg"
                                    >
                                        {{ number_format($amount/1000) }}rb
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Change --}}
                        @if($cashReceived >= $this->cartTotal)
                            <div class="bg-green-100 dark:bg-green-900/20 rounded-xl p-4 text-center">
                                <p class="text-sm text-green-600 dark:text-green-400">Kembalian</p>
                                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                    Rp {{ number_format($this->change, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    @endif

                    {{-- Note --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (opsional)</label>
                        <input 
                            type="text"
                            wire:model="note"
                            placeholder="Catatan transaksi..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-4">
                        <button 
                            wire:click="closePaymentModal"
                            class="flex-1 py-3 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            Batal
                        </button>
                        <button 
                            wire:click="processPayment"
                            wire:loading.attr="disabled"
                            @if($paymentMethod === 'CASH' && $cashReceived < $this->cartTotal) disabled @endif
                            class="flex-1 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold rounded-xl transition-colors"
                        >
                            <span wire:loading.remove wire:target="processPayment">✅ Proses</span>
                            <span wire:loading wire:target="processPayment">⏳ Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Last Invoice Display --}}
    @if($lastInvoice)
        <div class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg animate-pulse">
            ✅ Transaksi Berhasil: {{ $lastInvoice }}
        </div>
    @endif
</div>
