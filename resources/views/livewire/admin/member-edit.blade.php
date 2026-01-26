<div class="max-w-5xl mx-auto">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div
            class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg">
            <p class="text-sm text-rose-600 dark:text-rose-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Show ALL validation errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-lg">
            <p class="text-sm font-bold text-rose-600 dark:text-rose-400 mb-2">Validation Errors:</p>
            <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="update">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <div
                    class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-center relative">
                    <div class="w-24 h-24 mx-auto bg-slate-100 dark:bg-slate-700 rounded-full relative mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&background=0F52BA&color=fff&size=128"
                            class="w-full h-full rounded-full object-cover border-4 border-white dark:border-darkCard shadow-sm"
                            alt="{{ $member->user->name }}">
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $member->user->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Bergabung:
                        {{ $member->joinDate->format('d M Y') }}
                    </p>
                </div>

                <!-- Status Card -->
                <div
                    class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-white mb-4">Status Keanggotaan</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Status
                                Akun</label>
                            <select wire:model="status"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white cursor-pointer">
                                <option value="ACTIVE">Aktif</option>
                                <option value="INACTIVE">Non-Aktif (Cuti)</option>
                                <option value="SUSPENDED">Dibekukan (Banned)</option>
                            </select>
                            @error('status') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Info Saldo
                                (Read-Only)</p>
                            <div class="flex justify-between items-center text-[12px] mb-1">
                                <span class="text-slate-500">Total Simpanan</span>
                                <span class="font-bold text-slate-800 dark:text-white">Rp
                                    {{ number_format($member->totalSimpanan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-slate-500">Poin Loyalty</span>
                                <span
                                    class="font-bold text-amber-500">{{ number_format($member->points, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('admin.members.show', $member->id) }}"
                                class="block mt-3 text-center text-[11px] text-primary font-bold hover:underline">
                                Lihat Detail Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Data Pribadi</h3>
                        <span class="text-[10px] text-slate-400 italic">*Kolom abu-abu tidak dapat diedit</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Read-only fields -->
                        <div
                            class="md:col-span-2 p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nomor
                                    Anggota</label>
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-lock-alt text-slate-400'></i>
                                    <span
                                        class="text-[13px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $member->nomorAnggota }}</span>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal
                                    Bergabung</label>
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-lock-alt text-slate-400'></i>
                                    <span
                                        class="text-[13px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $member->joinDate->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Linked
                                    User Email</label>
                                <div class="flex items-center gap-2">
                                    <i class='bx bxs-lock-alt text-slate-400'></i>
                                    <span
                                        class="text-[13px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $member->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Editable fields -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nama
                                Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white">
                            @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nomor
                                Telepon</label>
                            <input type="text" wire:model="phone"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white">
                            @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Unit
                                Kerja / Prodi</label>
                            <input type="text" wire:model="unitKerja" list="unitKerjaList"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white"
                                placeholder="Contoh: Teknik Informatika">
                            <datalist id="unitKerjaList">
                                @foreach($unitKerjaList as $unit)
                                    <option value="{{ $unit }}">
                                @endforeach
                            </datalist>
                            @error('unitKerja') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Jenis
                                Kelamin</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="gender" value="MALE"
                                        class="text-primary focus:ring-primary">
                                    <span class="text-[13px] text-slate-700 dark:text-slate-300">Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="gender" value="FEMALE"
                                        class="text-primary focus:ring-primary">
                                    <span class="text-[13px] text-slate-700 dark:text-slate-300">Perempuan</span>
                                </label>
                            </div>
                            @error('gender') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Alamat
                                Domisili</label>
                            <textarea wire:model="address" rows="3"
                                class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white"></textarea>
                            @error('address') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Preferences Card -->
                @if($member->isMemberKoperasi)
                                <div
                                    class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                                    <div class="flex justify-between items-center mb-6">
                                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">
                                            <i class='bx bx-wallet mr-2'></i>Preferensi Pembayaran Simpanan
                                        </h3>
                                        <span
                                            class="text-[10px] bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2 py-1 rounded-full font-semibold">
                                            Untuk Potong Gaji
                                        </span>
                                    </div>

                                    <div class="space-y-6">
                                        <!-- SIMWA Payment Method -->
                                        <div
                                            class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-3">
                                                Simpanan Wajib (SIMWA)
                                            </label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Metode
                                                        Pembayaran</label>
                                                    <select wire:model.live="simwa_payment_method"
                                                        class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white cursor-pointer">
                                                        <option value="SALARY_DEDUCTION">Potong Gaji</option>
                                                        <option value="MANUAL">Bayar Manual</option>
                                                    </select>
                                                    @error('simwa_payment_method') <span
                                                    class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Jumlah
                                                        per Bulan (Tetap)</label>
                                                    <div class="relative">
                                                        <span
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[12px]">Rp</span>
                                                        <input type="text" value="50.000" disabled readonly
                                                            class="w-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg pl-10 pr-3 py-2.5 text-[13px] outline-none dark:text-white opacity-70 cursor-not-allowed">
                                                    </div>
                                                    <p class="mt-1 text-[10px] text-slate-400 flex items-center gap-1">
                                                        <i class='bx bxs-lock-alt'></i> Jumlah simpanan wajib sudah ditetapkan
                                                    </p>
                                                </div>
                                            </div>
                                            @if($simwa_payment_method === 'MANUAL')
                                                <p class="mt-2 text-[11px] text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                                    <i class='bx bx-info-circle'></i> Member akan bayar simpanan wajib secara manual
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Sukarela Payment Method -->
                                        <div
                                            class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-3">
                                                Simpanan Sukarela
                                            </label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Metode
                                                        Pembayaran</label>
                                                    <select wire:model.live="sukarela_payment_method"
                                                        class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white cursor-pointer">
                                                        <option value="MANUAL">Bayar Manual (Opsional)</option>
                                                        <option value="SALARY_DEDUCTION">Potong Gaji</option>
                                                    </select>
                                                    @error('sukarela_payment_method') <span
                                                    class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Jumlah
                                                        per Bulan</label>
                                                    <div class="relative" x-data="{ 
                                                                                        rawValue: {{ $monthly_sukarela_amount ?? 0 }},
                                                                                        formatted: '',
                                                                                        init() {
                                                                                            this.formatted = this.formatNumber(this.rawValue);
                                                                                        },
                                                                                        formatNumber(num) {
                                                                                            return new Intl.NumberFormat('id-ID').format(num);
                                                                                        },
                                                                                        parseNumber(str) {
                                                                                            return parseInt(str.replace(/\./g, '')) || 0;
                                                                                        },
                                                                                        updateValue(e) {
                                                                                            this.rawValue = this.parseNumber(e.target.value);
                                                                                            this.formatted = this.formatNumber(this.rawValue);
                                                                                            $wire.set('monthly_sukarela_amount', this.rawValue);
                                                                                        }
                                                                                    }">
                                                        <span
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[12px]">Rp</span>
                                                        <input type="text" x-model="formatted" @input="updateValue($event)"
                                                            @blur="updateValue($event)" @focus="$el.select()"
                                                            class="w-full bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg pl-10 pr-3 py-2.5 text-[13px] outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all dark:text-white {{ $sukarela_payment_method === 'MANUAL' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            {{ $sukarela_payment_method === 'MANUAL' ? 'disabled' : '' }} placeholder="0">
                                                    </div>
                                                    @error('monthly_sukarela_amount') <span
                                                    class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            @if($sukarela_payment_method === 'SALARY_DEDUCTION' && $monthly_sukarela_amount > 0)
                                                <p class="mt-2 text-[11px] text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                    <i class='bx bx-check-circle'></i> Simpanan sukarela Rp
                                                    {{ number_format($monthly_sukarela_amount, 0, ',', '.') }} akan dipotong dari gaji
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Summary -->
                                        <div
                                            class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
                                            <div class="flex justify-between items-center">
                                                <span class="text-[12px] font-semibold text-indigo-700 dark:text-indigo-300">Total
                                                    Potongan Gaji per Bulan</span>
                                                <span class="text-[15px] font-bold text-indigo-800 dark:text-indigo-200">
                                                    Rp {{ number_format(
                        ($simwa_payment_method === 'SALARY_DEDUCTION' ? 50000 : 0) +
                        ($sukarela_payment_method === 'SALARY_DEDUCTION' ? ($monthly_sukarela_amount ?? 0) : 0),
                        0,
                        ',',
                        '.'
                    ) }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-[10px] text-indigo-600 dark:text-indigo-400">
                                                Belum termasuk angsuran pinjaman (jika ada)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                @endif

                <!-- Loan Management Section -->
                <div
                    class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">
                            <i class='bx bx-money mr-2'></i>Data Pinjaman
                        </h3>
                        <button type="button" wire:click="openLoanModal"
                            class="text-[11px] bg-primary/10 text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all">
                            <i class='bx bx-plus'></i> Tambah Pinjaman
                        </button>
                    </div>

                    @if($member->loans->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[12px]">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase font-bold text-[10px]">
                                    <tr>
                                        <th class="px-4 py-2">Sumber</th>
                                        <th class="px-4 py-2 text-right">Plafond</th>
                                        <th class="px-4 py-2 text-right">Cicilan/Bulan</th>
                                        <th class="px-4 py-2 text-center">Tenor</th>
                                        <th class="px-4 py-2 text-center">Dibayar</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                        <th class="px-4 py-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($member->loans as $loan)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                            <td class="px-4 py-2 font-bold">{{ $loan->loanSource }}</td>
                                            <td class="px-4 py-2 text-right font-mono">
                                                {{ number_format($loan->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 text-right font-mono text-primary font-bold">
                                                {{ number_format($loan->monthlyPayment, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 text-center">{{ $loan->tenor }} Bln</td>
                                            <td class="px-4 py-2 text-center">{{ $loan->paid_installments }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <span
                                                    class="px-2 py-1 rounded-full text-[10px] font-bold {{ $loan->status == 'ACTIVE' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                    {{ $loan->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <button type="button" wire:click="openLoanModal({{ $loan->id }})"
                                                    class="text-blue-500 hover:text-blue-700 mr-2">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                                <button type="button" wire:click="deleteLoan({{ $loan->id }})"
                                                    wire:confirm="Yakin hapus data pinjaman ini?"
                                                    class="text-rose-500 hover:text-rose-700">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada data pinjaman.
                        </div>
                    @endif
                </div>

                <!-- Loan Modal -->
                @if($loanModalVisible)
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-xl shadow-lg p-6 m-4"
                            @click.away="$wire.set('loanModalVisible', false)">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-slate-800 dark:text-white">
                                    {{ $editingLoanId ? 'Edit Pinjaman' : 'Tambah Pinjaman Baru' }}
                                </h3>
                                <button type="button" wire:click="$set('loanModalVisible', false)"
                                    class="text-slate-400 hover:text-slate-600">
                                    <i class='bx bx-x text-2xl'></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Source -->
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Sumber
                                        Pinjaman</label>
                                    <select wire:model="loanForm.loanSource"
                                        class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                        <option value="BMT_ITQAN">BMT ITQAN (Eksternal)</option>
                                        <option value="BERMADANI">BERMADANI (Internal)</option>
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Plafond
                                        (Rp)</label>
                                    <input type="number" wire:model="loanForm.amount"
                                        class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                </div>

                                <!-- Installment -->
                                <div>
                                    <label
                                        class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Cicilan
                                        per Bulan (Total
                                        Tagihan)</label>
                                    <input type="number" wire:model="loanForm.monthlyPayment"
                                        class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                    <p class="text-[10px] text-slate-400 mt-1">*Termasuk Simwa BMT jika ada</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Tenor
                                            (Bulan)</label>
                                        <input type="number" wire:model="loanForm.tenor"
                                            class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Sudah
                                            Dibayar
                                            (x)</label>
                                        <input type="number" wire:model="loanForm.paid_installments"
                                            class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Tanggal
                                            Cair</label>
                                        <input type="date" wire:model="loanForm.startDate"
                                            class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Status</label>
                                        <select wire:model="loanForm.status"
                                            class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:text-white">
                                            <option value="ACTIVE">ACTIVE</option>
                                            <option value="COMPLETED">COMPLETED</option>
                                            <option value="PENDING">PENDING</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" wire:click="$set('loanModalVisible', false)"
                                    class="px-4 py-2 text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">Batal</button>
                                <button type="button" wire:click="saveLoan"
                                    class="px-4 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary/90">Simpan</button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                    <a href="{{ route('admin.members.show', $member->id) }}"
                        class="px-5 py-2.5 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 rounded-lg text-[13px] font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2.5 bg-primary hover:bg-indigo-700 text-white rounded-lg text-[13px] font-bold shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="update">
                            <i class='bx bx-save text-lg'></i> Simpan Perubahan
                        </span>
                        <span wire:loading wire:target="update">
                            <i class='bx bx-loader-alt animate-spin text-lg'></i> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>

</div>
</form>
</div>