<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.retail-members.index') }}"
            class="flex items-center text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors text-sm font-bold">
            <i class='bx bx-arrow-back mr-2 text-lg'></i> Kembali
        </a>
        <h1 class="text-xl font-bold text-slate-800 dark:text-white">Detail Member Retail</h1>
    </div>

    @if (session()->has('message'))
        <div
            class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center gap-2">
            <i class='bx bx-check-circle text-emerald-600 text-xl'></i>
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold">{{ session('message') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Profile & Edit Form --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Profile Card --}}
            <div
                class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-emerald-500 to-teal-600 z-0"></div>
                <div class="relative z-10 mt-8">
                    <div class="w-24 h-24 mx-auto bg-white dark:bg-slate-700 p-1 rounded-full shadow-md mb-3 relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=0F52BA&color=fff&size=128"
                            class="w-full h-full rounded-full object-cover bg-slate-100" alt="{{ $name }}">
                        <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 border-2 border-white rounded-full flex items-center justify-center text-white text-xs"
                            title="Retail Member">
                            <i class='bx bx-shopping-bag'></i>
                        </div>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">{{ $name }}</h2>
                    <p class="text-xs text-slate-500 font-mono mt-1">{{ $member->nomorAnggota }}</p>

                    <div class="mt-4 flex justify-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border
                            @if($status === 'ACTIVE') bg-emerald-50 text-emerald-600 border-emerald-100
                            @else bg-rose-50 text-rose-600 border-rose-100
                            @endif">
                            {{ $status }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-amber-50 text-amber-600 border-amber-100">
                            {{ $member->tier }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Edit Profile Form --}}
            <div
                class="bg-white dark:bg-darkCard p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class='bx bx-edit'></i> Edit Data Diri
                </h3>
                <form wire:submit="updateProfile">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nama
                                Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full text-sm border-slate-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                            @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">No. HP / WA</label>
                            <input type="text" wire:model="phone"
                                class="w-full text-sm border-slate-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                            @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Unit Kerja /
                                Prodi</label>
                            <input type="text" wire:model="unitKerja" list="unit-list"
                                class="w-full text-sm border-slate-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                            <datalist id="unit-list">
                                @foreach($unitKerjaList as $unit) <option value="{{ $unit }}"> @endforeach
                            </datalist>
                            @error('unitKerja') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Status</label>
                            <select wire:model="status"
                                class="w-full text-sm border-slate-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                                <option value="ACTIVE">Aktif</option>
                                <option value="INACTIVE">Non-Aktif</option>
                                <option value="SUSPENDED">Suspended</option>
                            </select>
                        </div>
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 rounded-lg text-xs transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Financials --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Big Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Saldo Bermadani --}}
                <div
                    class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-5 text-white shadow-lg relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-white opacity-10 rounded-full blur-2xl">
                    </div>
                    <p class="text-xs font-bold text-emerald-100 uppercase tracking-widest mb-1">Saldo Bermadani</p>
                    <h2 class="text-3xl font-bold">Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}</h2>
                    <p class="text-[10px] mt-2 text-emerald-100 flex items-center gap-1">
                        <i class='bx bx-info-circle'></i> Saldo belanja & tabungan sukarela
                    </p>
                </div>

                {{-- Loyalty Points --}}
                <div
                    class="bg-white dark:bg-darkCard rounded-xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Poin Loyalty</p>
                    <div class="flex items-end gap-2">
                        <h2 class="text-3xl font-bold text-amber-500">{{ number_format($member->points) }}</h2>
                        <span class="text-xs font-bold text-slate-400 mb-1.5">pts</span>
                    </div>
                    <div class="mt-2 w-full bg-slate-100 dark:bg-slate-600 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-amber-400 h-full rounded-full" style="width: 45%"></div>
                    </div>
                    <p class="text-[10px] mt-1 text-slate-400 text-right">Menuju Silver Tier</p>
                </div>
            </div>

            {{-- Configuration: Auto Topup / Salary Deduction --}}
            <div
                class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class='bx bx-wallet-alt text-xl'></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white">Konfigurasi Top-up Rutin</h3>
                        <p class="text-xs text-slate-500">Atur pemotongan gaji untuk mengisi Saldo Bermadani secara
                            otomatis.</p>
                    </div>
                </div>

                <div
                    class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                    <form wire:submit="updateProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Metode
                                    Top-up</label>
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center gap-3 p-3 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:border-emerald-500 transition-colors {{ $sukarela_payment_method === 'SALARY_DEDUCTION' ? 'ring-2 ring-emerald-500 border-transparent' : '' }}">
                                        <input type="radio" wire:model.live="sukarela_payment_method"
                                            value="SALARY_DEDUCTION" class="text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block text-xs font-bold text-slate-700 dark:text-white">Potong
                                                Gaji Otomatis</span>
                                            <span class="block text-[10px] text-slate-400">Saldo terisi tiap
                                                gajian</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-3 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:border-slate-300 transition-colors {{ $sukarela_payment_method !== 'SALARY_DEDUCTION' ? 'ring-2 ring-slate-300 border-transparent' : '' }}">
                                        <input type="radio" wire:model.live="sukarela_payment_method" value="MANUAL"
                                            class="text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block text-xs font-bold text-slate-700 dark:text-white">Top-up
                                                Manual</span>
                                            <span class="block text-[10px] text-slate-400">Melalui Kasir /
                                                Transfer</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @if($sukarela_payment_method === 'SALARY_DEDUCTION')
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Nominal
                                        per Bulan</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">Rp</span>
                                        <input type="number" wire:model="monthly_sukarela_amount" placeholder="0"
                                            class="w-full pl-9 py-2.5 text-sm font-bold border-slate-200 dark:border-slate-600 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 dark:bg-darkCard dark:text-white">
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-2">
                                        *Nominal ini akan dipotong dari slip gaji bulanan dan masuk ke Saldo Bermadani.
                                    </p>
                                </div>
                            @else
                                <div
                                    class="flex items-center justify-center p-4 border-2 border-dashed border-slate-200 dark:border-slate-600 rounded-lg text-slate-400 text-center">
                                    <div>
                                        <i class='bx bx-store-alt text-2xl mb-1'></i>
                                        <p class="text-xs">Member melakukan top-up langsung di kasir.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md shadow-emerald-500/20 transition-colors flex items-center gap-2">
                                <i class='bx bx-save text-base'></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Recent Transactions (Placeholder) --}}
            <div
                class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Riwayat Transaksi Terakhir</h3>
                <div class="text-center py-8 text-slate-400">
                    <i class='bx bx-receipt text-4xl mb-2 opacity-50'></i>
                    <p class="text-xs">Belum ada transaksi.</p>
                </div>
            </div>

        </div>
    </div>
</div>