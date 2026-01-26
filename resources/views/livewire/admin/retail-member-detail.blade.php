<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.retail-members.index') }}"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-primary hover:border-primary transition-all shadow-sm">
                <i class='bx bx-arrow-back text-xl'></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Detail Member</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola profil dan preferensi saldo.</p>
            </div>
        </div>

        <div class="flex gap-2">
            @if (session()->has('message'))
                <div
                    class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-lg flex items-center gap-2 animate-in fade-in slide-in-from-top-2">
                    <i class='bx bx-check-circle'></i> {{ session('message') }}
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column: Profile (4 cols) --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Profile Card --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden relative group">
                {{-- Cover Pattern --}}
                <div class="h-32 bg-gradient-to-r from-slate-900 to-slate-800 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]">
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emerald-500/30 rounded-full blur-3xl"></div>
                    <div class="absolute top-10 -left-10 w-40 h-40 bg-indigo-500/30 rounded-full blur-3xl"></div>
                </div>

                <div class="px-6 pb-8 text-center relative">
                    <div
                        class="w-28 h-28 mx-auto -mt-14 mb-4 relative z-10 transition-transform duration-300 group-hover:scale-105">
                        <div class="w-full h-full rounded-full p-1.5 bg-white dark:bg-darkCard shadow-lg">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=0F52BA&color=fff&size=128&bold=true"
                                class="w-full h-full rounded-full object-cover bg-slate-100 dark:bg-slate-700"
                                alt="{{ $name }}">
                        </div>
                        <div class="absolute bottom-1 right-2 w-8 h-8 bg-emerald-500 border-4 border-white dark:border-darkCard rounded-full flex items-center justify-center text-white text-sm shadow-md"
                            title="Retail Member">
                            <i class='bx bx-shopping-bag'></i>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight mb-1">{{ $name }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-mono mb-4">{{ $member->nomorAnggota }}</p>

                    <div class="flex justify-center flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border transition-colors
                            @if($status === 'ACTIVE') bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800
                            @else bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-800
                            @endif">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $status === 'ACTIVE' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $status }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-800">
                            <i class='bx bxs-medal'></i> {{ $member->tier }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Form Edit --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm">Informasi Personal</h3>
                    <button wire:click="updateProfile"
                        class="text-xs font-bold text-primary hover:underline">Simpan</button>
                </div>
                <div class="p-6">
                    <form wire:submit="updateProfile" class="space-y-5">
                        <div class="group">
                            <label
                                class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-2 group-focus-within:text-primary transition-colors">Nama
                                Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white placeholder-slate-300">
                            @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="group">
                            <label
                                class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-2 group-focus-within:text-primary transition-colors">No.
                                WhatsApp</label>
                            <input type="text" wire:model="phone"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white placeholder-slate-300">
                            @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="group">
                            <label
                                class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-2 group-focus-within:text-primary transition-colors">Unit
                                Kerja</label>
                            <input type="text" wire:model="unitKerja" list="unit-list"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white placeholder-slate-300">
                            @error('unitKerja') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="group">
                            <label
                                class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-2 group-focus-within:text-primary transition-colors">Status
                                Akun</label>
                            <div class="relative">
                                <select wire:model="status"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white appearance-none cursor-pointer">
                                    <option value="ACTIVE">Aktif</option>
                                    <option value="INACTIVE">Non-Aktif</option>
                                    <option value="SUSPENDED">Suspended</option>
                                </select>
                                <i
                                    class='bx bx-chevron-down absolute right-4 top-3 text-slate-400 pointer-events-none'></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Financials (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Big Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Saldo Bermadani --}}
                <div
                    class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-xl shadow-slate-300/50 dark:shadow-none relative overflow-hidden group">
                    <div
                        class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 group-hover:opacity-30 transition-opacity">
                    </div>
                    <div
                        class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px] group-hover:bg-emerald-500/30 transition-all duration-700">
                    </div>

                    <div class="relative z-10 flex flex-col h-full justify-between min-h-[140px]">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Saldo
                                    Bermadani</p>
                                <h2 class="text-4xl font-bold tracking-tight text-white drop-shadow-sm">
                                    Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}
                                </h2>
                            </div>
                            <div
                                class="w-10 h-10 rounded-full bg-white/10 backdrop-blur flex items-center justify-center border border-white/10 group-hover:bg-white/20 transition-all">
                                <i class='bx bx-wallet text-xl text-emerald-400'></i>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-[11px] text-slate-400">Saldo aktif belanja & tabungan</p>
                            @if($sukarela_payment_method === 'SALARY_DEDUCTION')
                                <div
                                    class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 backdrop-blur-sm text-[10px] font-bold text-emerald-300 flex items-center gap-1.5">
                                    <i class='bx bx-check-double'></i> Autodebet Aktif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Loyalty Points --}}
                <div
                    class="bg-white dark:bg-darkCard rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all duration-700">
                    </div>

                    <div class="relative z-10 flex flex-col h-full justify-between min-h-[140px]">
                        <div class="flex justify-between items-start">
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Loyalty Points</p>
                                <div class="flex items-baseline gap-2">
                                    <h2 class="text-4xl font-bold text-amber-500 tracking-tight">
                                        {{ number_format($member->points) }}</h2>
                                    <span class="text-sm font-bold text-slate-400">pts</span>
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center border border-amber-100 dark:border-amber-500/20 text-amber-500 group-hover:scale-110 transition-transform">
                                <i class='bx bxs-star text-xl'></i>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1.5">
                                <span>Current: {{ $member->tier }}</span>
                                <span>Next: Gold</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-full rounded-full transition-all duration-1000"
                                    style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Configuration Section --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i class='bx bx-cog text-xl'></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Konfigurasi Top-up Otomatis</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur metode pembayaran sukarela potong
                            gaji.</p>
                    </div>
                </div>

                <div class="p-6">
                    <form wire:submit="updateProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Method Selection --}}
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Pilih
                                    Metode</label>
                                <div class="space-y-3">
                                    <label
                                        class="group relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200
                                        {{ $sukarela_payment_method === 'SALARY_DEDUCTION'
    ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/10 shadow-sm'
    : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-800' }}">

                                        <div class="mt-0.5">
                                            <input type="radio" wire:model.live="sukarela_payment_method"
                                                value="SALARY_DEDUCTION" class="sr-only">
                                            <div
                                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors
                                                {{ $sukarela_payment_method === 'SALARY_DEDUCTION' ? 'border-emerald-500' : 'border-slate-300' }}">
                                                @if($sukarela_payment_method === 'SALARY_DEDUCTION')
                                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-in zoom-in">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <span
                                                class="block text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                                                Potong Gaji Otomatis
                                            </span>
                                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                                Saldo Bermadani akan terisi otomatis setiap tanggal gajian.
                                            </p>
                                        </div>
                                    </label>

                                    <label
                                        class="group relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200
                                        {{ $sukarela_payment_method !== 'SALARY_DEDUCTION'
    ? 'border-slate-300 bg-slate-50/50 dark:bg-slate-700/50 shadow-sm'
    : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 bg-white dark:bg-slate-800' }}">

                                        <div class="mt-0.5">
                                            <input type="radio" wire:model.live="sukarela_payment_method" value="MANUAL"
                                                class="sr-only">
                                            <div
                                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors
                                                {{ $sukarela_payment_method !== 'SALARY_DEDUCTION' ? 'border-slate-400' : 'border-slate-300' }}">
                                                @if($sukarela_payment_method !== 'SALARY_DEDUCTION')
                                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-400 animate-in zoom-in">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <span class="block text-sm font-bold text-slate-900 dark:text-white">
                                                Top-up Manual
                                            </span>
                                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                                Member melakukan isi ulang saldo melalui kasir atau transfer.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Amount Input (Conditional) --}}
                            <div class="flex flex-col justify-center">
                                @if($sukarela_payment_method === 'SALARY_DEDUCTION')
                                    <div
                                        class="bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl p-6 border border-emerald-100 dark:border-emerald-500/20 text-center animate-in fade-in zoom-in duration-300">
                                        <label
                                            class="block text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-widest mb-3">
                                            Nominal Potongan
                                        </label>
                                        <div class="relative inline-block w-full max-w-[200px]" x-data="{
                                                     original: @entangle('monthly_sukarela_amount'),
                                                     display: '',
                                                     init() {
                                                         this.display = (this.original && this.original != 0) ? new Intl.NumberFormat('id-ID').format(this.original) : '';
                                                         $watch('original', value => {
                                                             this.display = (value && value != 0) ? new Intl.NumberFormat('id-ID').format(value) : '';
                                                         });
                                                     },
                                                     update(e) {
                                                         let raw = e.target.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                                                         this.original = raw ? parseInt(raw) : null;
                                                         this.display = (raw && raw != 0) ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                     }
                                                 }">
                                            <span
                                                class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 font-bold">Rp</span>
                                            <input type="text" x-model="display" @input="update" placeholder="0"
                                                class="w-full pl-10 pr-4 py-3 bg-white dark:bg-darkCard border-2 border-emerald-200 dark:border-emerald-500/30 rounded-xl text-center font-bold text-lg text-emerald-700 dark:text-white shadow-sm focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all placeholder-emerald-300/50">
                                        </div>
                                        <p
                                            class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 mt-3 font-medium">
                                            <i class='bx bx-check-circle'></i> Disetujui pada {{ now()->format('d M Y') }}
                                        </p>
                                    </div>
                                @else
                                    <div
                                        class="text-center text-slate-400 p-6 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl animate-in fade-in zoom-in">
                                        <i class='bx bx-shopping-bag text-3xl mb-2 opacity-50'></i>
                                        <p class="text-xs font-medium">Tidak ada potongan gaji.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                            <button type="submit"
                                class="px-6 py-2.5 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold shadow-lg shadow-slate-300/50 dark:shadow-none transition-all transform active:scale-95 flex items-center gap-2">
                                <i class='bx bx-save text-base'></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>