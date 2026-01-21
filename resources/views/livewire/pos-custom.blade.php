<div class="flex-1 flex h-full min-w-0 relative" x-data="{ 
    showPayment: @entangle('showPaymentModal')
}">

    <main class="flex-1 flex flex-col h-full min-w-0 relative pb-20 lg:pb-0">

        {{-- Header --}}
        <header
            class="h-16 bg-card dark:bg-darkCard border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 shrink-0">
            <div class="flex items-center gap-3 w-full max-w-md">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class='bx bx-search text-lg'></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="w-full bg-slate-100 dark:bg-slate-700/50 border-none text-[11px] font-semibold rounded-lg pl-9 py-2 focus:ring-1 focus:ring-primary text-slate-800 dark:text-white placeholder-slate-400"
                        placeholder="Scan barcode atau cari produk...">
                    <span
                        class="absolute inset-y-0 right-0 pr-2 flex items-center cursor-pointer text-slate-400 hover:text-primary">
                        <i class='bx bx-barcode-reader text-xl'></i>
                    </span>
                </div>
            </div>

            <button id="theme-toggle"
                class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors">
                <i id="theme-icon" class='bx bx-moon text-lg'></i>
            </button>
        </header>

        {{-- Category Filter --}}
        <div
            class="h-[68px] px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 shrink-0 overflow-x-auto no-scrollbar flex items-center gap-2">
            <button wire:click="$set('categoryFilter', '')"
                class="px-4 py-1.5 rounded-full text-[11px] font-semibold whitespace-nowrap transition-colors {{ $categoryFilter === '' ? 'bg-primary text-white shadow-sm shadow-indigo-200 dark:shadow-none' : 'bg-card dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary' }}">
                Semua Item
            </button>
            @foreach($this->categories as $category)
                <button wire:click="$set('categoryFilter', '{{ $category->id }}')"
                    class="px-4 py-1.5 rounded-full text-[11px] font-medium whitespace-nowrap transition-colors {{ $categoryFilter === $category->id ? 'bg-primary text-white shadow-sm shadow-indigo-200 dark:shadow-none' : 'bg-card dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary' }}">
                    {{ $category->icon }} {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 overflow-y-auto p-4 custom-scroll bg-page dark:bg-darkPage">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($this->products as $product)
                    <div wire:click="addToCart({{ $product->id }})"
                        class="bg-card dark:bg-darkCard rounded-xl p-3 shadow-sm border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500 transition-all group relative">
                        <div
                            class="h-28 w-full bg-slate-50 dark:bg-slate-700 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl">{{ $product->category?->icon ?? '📦' }}</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-start mb-1">
                            <h5 class="text-[13px] font-bold text-slate-800 dark:text-white line-clamp-2 leading-tight">
                                {{ $product->name }}
                            </h5>
                        </div>
                        <p class="text-[10px] text-slate-400 mb-2">Stok: {{ $product->stock }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-[14px] font-bold text-primary">Rp
                                {{ number_format($product->sellPrice, 0, ',', '.') }}</span>
                            <button
                                class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                <i class='bx bx-plus'></i>
                            </button>
                        </div>
                        @if($product->isConsignment)
                            <span
                                class="absolute top-2 right-2 text-[10px] px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                📦 Titipan
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-20 text-slate-400">
                        <div class="text-6xl mb-4">📦</div>
                        <p class="text-sm">Tidak ada produk ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    {{-- Cart Sidebar --}}
    <aside id="pos-cart"
        class="fixed inset-0 z-50 bg-card dark:bg-darkCard flex flex-col transition-transform duration-300 translate-y-full lg:translate-y-0 lg:static lg:w-[340px] lg:border-l lg:border-slate-200 lg:dark:border-slate-700 lg:shadow-xl lg:flex-shrink-0">

        {{-- Cart Header --}}
        <div
            class="h-16 px-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between shrink-0">
            <div>
                <h4 class="text-[14px] font-bold text-slate-800 dark:text-white">Pesanan Saat Ini</h4>
                <p class="text-[10px] text-slate-400">#{{ date('Ymd') }}</p>
            </div>
            <button id="close-cart-btn"
                class="lg:hidden text-slate-500 hover:text-rose-500 p-1.5 rounded-md transition-colors">
                <i class='bx bx-chevron-down text-2xl'></i>
            </button>
        </div>

        {{-- Member Selection (Alpine.js Searchable Dropdown) --}}
        <div class="min-h-[68px] px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 shrink-0 flex items-center"
            x-data="{
                open: false,
                search: '',
                members: @js($members),
                get filtered() {
                    if (!this.search) return this.members.slice(0, 5);
                    const s = this.search.toLowerCase();
                    return this.members.filter(m => 
                        m.name.toLowerCase().includes(s) || 
                        m.nomorAnggota.toLowerCase().includes(s) ||
                        (m.unitKerja && m.unitKerja.toLowerCase().includes(s))
                    ).slice(0, 8);
                },
                selectMember(member) {
                    this.search = '';
                    this.open = false;
                    $wire.selectMember(member.id);
                },
                clear() {
                    $wire.clearMember();
                }
            }">
            {{-- Selected Member Display --}}
            @if($selectedMember)
                <div wire:key="selected-member-display"
                    class="w-full flex items-center gap-3 bg-white dark:bg-slate-700 border border-emerald-200 dark:border-emerald-600 rounded-lg px-3 py-2">
                    <div
                        class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-xs font-bold shrink-0">
                        {{ strtoupper(substr($selectedMember['name'], 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold text-slate-800 dark:text-white truncate">
                            {{ $selectedMember['name'] }}
                        </p>
                        <p class="text-[10px] text-slate-500 truncate">{{ $selectedMember['nomorAnggota'] }} •
                            {{ $selectedMember['unitKerja'] ?: '-' }}
                        </p>
                    </div>
                    <button wire:click="clearMember" class="text-rose-500 hover:text-rose-700 shrink-0">
                        <i class='bx bx-x text-lg'></i>
                    </button>
                </div>
            @else
                <div wire:key="member-search-input" class="w-full relative">
                    {{-- Search Input --}}
                    <div class="relative">
                        <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                            placeholder="Cari member (nama / no. anggota)..."
                            class="w-full text-[11px] px-3 py-2 pl-8 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary text-slate-800 dark:text-white">
                        <div class="absolute inset-y-0 left-2.5 flex items-center pointer-events-none text-slate-400">
                            <i class='bx bx-search text-sm'></i>
                        </div>

                        {{-- Quick Add Button (Always visible inside input for easy access) --}}
                        <button wire:click="createNewMember"
                            class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-primary transition-colors"
                            title="Buat Member Baru">
                            <i class='bx bx-user-plus text-lg'></i>
                        </button>
                    </div>

                    {{-- Dropdown Results --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl max-h-48 overflow-y-auto custom-scroll">
                        <div
                            class="p-2 border-b border-slate-100 dark:border-slate-600 sticky top-0 bg-white dark:bg-slate-700 z-10">
                            <button wire:click="createNewMember"
                                class="w-full py-2 bg-indigo-50 dark:bg-indigo-900/40 border border-transparent dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-300 rounded-lg text-xs font-bold hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors flex items-center justify-center gap-2">
                                <i class='bx bx-plus-circle text-lg'></i> Buat Member Baru
                            </button>
                        </div>
                        <template x-if="filtered.length === 0">
                            <div class="px-4 py-4 text-center text-slate-400 text-[11px]">
                                <i class='bx bx-user-x text-2xl mb-1 block opacity-50'></i>
                                <p class="mb-2">Member tidak ditemukan</p>
                            </div>
                        </template>
                        <template x-for="member in filtered" :key="member.id">
                            <button @click="selectMember(member)" type="button"
                                class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors text-left border-b border-slate-100 dark:border-slate-600 last:border-0">
                                <div
                                    class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-500 text-slate-600 dark:text-slate-200 flex items-center justify-center text-[10px] font-bold shrink-0">
                                    <span x-text="member.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-medium text-slate-800 dark:text-white truncate"
                                        x-text="member.name"></p>
                                    <p class="text-[10px] text-slate-500 truncate"
                                        x-text="member.nomorAnggota + (member.unitKerja ? ' • ' + member.unitKerja : '')">
                                    </p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            @endif
        </div>

        {{-- Quick Member Registration Modal --}}
        <div x-show="$wire.showNewMemberModal" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
            <div class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl max-w-sm w-full p-6 relative">
                <button wire:click="$set('showNewMemberModal', false)"
                    class="absolute top-4 right-4 text-slate-400 hover:text-rose-500">
                    <i class='bx bx-x text-2xl'></i>
                </button>

                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1 flex items-center gap-2">
                    <i class='bx bx-user-plus text-primary'></i> Member Baru
                </h3>
                <p class="text-[11px] text-slate-500 mb-4">Registrasi cepat untuk pelanggan toko.</p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap
                            <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="newMemberName" placeholder="Nama pelanggan"
                            class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none">
                        @error('newMemberName') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA
                            <span class="text-rose-500">*</span></label>
                        <div class="relative flex">
                            <span
                                class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-sm font-bold font-mono">
                                +62
                            </span>
                            <input type="tel" wire:model="newMemberPhone" placeholder="812..." inputmode="numeric"
                                pattern="[0-9]*"
                                class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-r-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none font-mono">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 ml-1">Masukkan tanpa angka 0 di depan.</p>
                        @error('newMemberPhone') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Gender
                                <span class="text-rose-500">*</span></label>
                            <select wire:model="newMemberGender"
                                class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none">
                                <option value="MALE">Laki-laki</option>
                                <option value="FEMALE">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Unit</label>
                            <select wire:model="newMemberUnit"
                                class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none">
                                <option value="">UMUM</option>
                                <option value="DOSEN">Dosen</option>
                                <option value="KARYAWAN">Karyawan</option>
                                <option value="MAHASISWA">Mahasiswa</option>
                            </select>
                        </div>
                    </div>

                    <button wire:click="storeNewMember" wire:loading.attr="disabled"
                        class="w-full mt-2 bg-primary hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="storeNewMember">Simpan & Pilih</span>
                        <span wire:loading wire:target="storeNewMember"><i class='bx bx-loader-alt bx-spin'></i>
                            Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scroll">
            @forelse($cart as $index => $item)
                <div class="flex gap-3 group">
                    <div
                        class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center text-xl shrink-0">
                        📦
                    </div>
                    <div class="flex-1 min-w-0">
                        <h6 class="text-[13px] font-semibold text-slate-800 dark:text-white leading-tight mb-1 truncate">
                            {{ $item['name'] }}
                        </h6>
                        <p class="text-[11px] text-slate-400">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-[13px] font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded px-1">
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                class="text-slate-400 hover:text-rose-500 text-xs px-1">
                                <i class='bx bx-minus'></i>
                            </button>
                            <span
                                class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                class="text-slate-400 hover:text-emerald-500 text-xs px-1">
                                <i class='bx bx-plus'></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-400">
                    <div class="text-4xl mb-2">🛒</div>
                    <p class="text-sm">Keranjang kosong</p>
                    <p class="text-xs mt-1">Pilih produk untuk memulai</p>
                </div>
            @endforelse
        </div>

        {{-- Cart Footer --}}
        <div class="p-5 bg-card dark:bg-darkCard border-t border-slate-200 dark:border-slate-700 shrink-0">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-[14px] font-bold text-slate-800 dark:text-white">Total</span>
                    <span class="text-[18px] font-bold text-primary">Rp
                        {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-2">
                <button wire:click="clearCart"
                    class="col-span-1 py-3 rounded-xl border border-rose-200 text-rose-500 hover:bg-rose-50 dark:border-rose-900/50 dark:hover:bg-rose-900/20 transition-colors flex items-center justify-center"
                    @if(empty($cart)) disabled @endif>
                    <i class='bx bx-trash text-xl'></i>
                </button>
                <button wire:click="openPaymentModal"
                    class="col-span-3 py-3 rounded-xl bg-primary hover:bg-indigo-700 disabled:bg-slate-200 disabled:dark:bg-slate-700 disabled:text-slate-400 disabled:dark:text-slate-500 disabled:cursor-not-allowed disabled:shadow-none text-white font-bold text-[14px] shadow-lg shadow-indigo-500/30 transition-all flex items-center justify-center gap-2"
                    @if(empty($cart)) disabled @endif>
                    Bayar <i class='bx bx-right-arrow-alt'></i>
                </button>
            </div>
        </div>

    </aside>

    {{-- Mobile Bottom Bar --}}
    <div id="pos-bottom-bar"
        class="lg:hidden fixed bottom-0 left-0 right-0 bg-card dark:bg-darkCard border-t border-slate-200 dark:border-slate-700 p-4 flex items-center justify-between z-40 transition-all duration-300">
        <div>
            <p class="text-[10px] text-slate-400">Total Item: {{ $this->cartItemCount }}</p>
            <h4 class="text-lg font-bold text-primary">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</h4>
        </div>
        <button id="toggle-cart-btn"
            class="bg-primary text-white px-6 py-2 rounded-lg font-bold text-sm shadow-lg shadow-indigo-500/30 flex items-center gap-2">
            Lihat Pesanan <i class='bx bx-chevron-up'></i>
        </button>
    </div>

    {{-- Payment Modal --}}
    <div x-show="showPayment" x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
        <div @click.away="showPayment = false"
            class="bg-white dark:bg-darkCard rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <i class='bx bx-credit-card text-primary'></i> Pembayaran
            </h3>

            <div class="space-y-4">
                {{-- Total --}}
                <div class="bg-slate-100 dark:bg-slate-800 rounded-xl p-4 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Bayar</p>
                    <p class="text-3xl font-bold text-primary">
                        Rp {{ number_format($this->cartTotal, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Metode
                        Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="$set('paymentMethod', 'CASH')"
                            class="py-3 rounded-lg border-2 transition-colors text-sm flex items-center justify-center gap-2 {{ $paymentMethod === 'CASH' ? 'border-primary bg-indigo-50 dark:bg-indigo-900/20 text-primary' : 'border-slate-300 dark:border-slate-600' }}">
                            <i class='bx bx-money text-lg'></i> Cash
                        </button>
                        <button wire:click="$set('paymentMethod', 'TRANSFER')"
                            class="py-3 rounded-lg border-2 transition-colors text-sm flex items-center justify-center gap-2 {{ $paymentMethod === 'TRANSFER' ? 'border-primary bg-indigo-50 dark:bg-indigo-900/20 text-primary' : 'border-slate-300 dark:border-slate-600' }}">
                            <i class='bx bxs-bank text-lg'></i> Transfer
                        </button>
                        <button wire:click="$set('paymentMethod', 'CREDIT')"
                            class="py-3 rounded-lg border-2 transition-colors text-sm flex items-center justify-center gap-2 {{ $paymentMethod === 'CREDIT' ? 'border-primary bg-indigo-50 dark:bg-indigo-900/20 text-primary' : 'border-slate-300 dark:border-slate-600' }}">
                            <i class='bx bx-credit-card-alt text-lg'></i> Kredit
                        </button>
                        @if($selectedMember)
                            @php
                                $memberBalance = \App\Models\Member::find($selectedMember['id'])?->simpananSukarela ?? 0;
                                $hasEnoughBalance = $memberBalance >= $this->cartTotal;
                            @endphp
                            <button wire:click="$set('paymentMethod', 'SUKARELA')" @if(!$hasEnoughBalance) disabled @endif
                                class="py-3 rounded-lg border-2 transition-colors text-sm relative flex flex-col items-center justify-center {{ $paymentMethod === 'SUKARELA' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : ($hasEnoughBalance ? 'border-slate-300 dark:border-slate-600' : 'border-slate-200 dark:border-slate-700 opacity-50 cursor-not-allowed') }}">
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-wallet text-lg'></i> Simpanan
                                </div>
                                <span
                                    class="block text-[10px] {{ $hasEnoughBalance ? 'text-emerald-600' : 'text-rose-500' }}">
                                    Rp {{ number_format($memberBalance, 0, ',', '.') }}
                                </span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Cash Input --}}
                @if($paymentMethod === 'CASH')
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Uang
                            Diterima</label>
                        <input type="number" wire:model.live="cashReceived"
                            class="w-full px-4 py-3 text-xl font-bold border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-center">
                        <div class="mt-2 grid grid-cols-4 gap-2">
                            @foreach([10000, 20000, 50000, 100000] as $amount)
                                <button wire:click="$set('cashReceived', {{ $amount }})"
                                    class="py-2 text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg">
                                    {{ number_format($amount / 1000) }}rb
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Change --}}
                    @if($cashReceived >= $this->cartTotal)
                        <div class="bg-emerald-100 dark:bg-emerald-900/20 rounded-xl p-4 text-center">
                            <p class="text-sm text-emerald-600 dark:text-emerald-400">Kembalian</p>
                            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">
                                Rp {{ number_format($this->change, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif
                @endif

                {{-- Actions --}}
                <div class="flex gap-3 pt-4">
                    <button wire:click="closePaymentModal"
                        class="flex-1 py-3 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-slate-600 dark:text-slate-300">
                        Batal
                    </button>
                    <button wire:click="processPayment" wire:loading.attr="disabled" @if($paymentMethod === 'CASH' && $cashReceived < $this->cartTotal) disabled @endif
                        class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-400 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="processPayment"><i
                                class='bx bx-check-circle text-lg'></i>
                            Proses</span>
                        <span wire:loading wire:target="processPayment"><i class='bx bx-loader-alt bx-spin text-lg'></i>
                            Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Toast --}}
    @if($lastInvoice)
        <div class="fixed top-4 right-4 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg z-[70] animate-pulse">
            ✅ Transaksi Berhasil: {{ $lastInvoice }}
        </div>
    @endif
</div>