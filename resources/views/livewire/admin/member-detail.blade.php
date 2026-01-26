<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div
            class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ session('message') }}</p>
        </div>
    @endif

    <!-- Member Profile Card -->
    <div
        class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 flex flex-col md:flex-row items-center md:items-start gap-6 relative overflow-hidden mb-6">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full pointer-events-none"></div>

        <div class="relative shrink-0">
            <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-700 p-1">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&background=0F52BA&color=fff&size=128"
                    class="w-full h-full rounded-full object-cover" alt="{{ $member->user->name }}">
            </div>
            <div class="absolute bottom-0 right-0 bg-white dark:bg-darkCard p-1 rounded-full">
                @if($member->status === 'ACTIVE')
                    <span class="w-4 h-4 bg-emerald-500 border-2 border-white dark:border-darkCard rounded-full block"
                        title="Aktif"></span>
                @elseif($member->status === 'SUSPENDED')
                    <span class="w-4 h-4 bg-rose-500 border-2 border-white dark:border-darkCard rounded-full block"
                        title="Diblokir"></span>
                @else
                    <span class="w-4 h-4 bg-slate-400 border-2 border-white dark:border-darkCard rounded-full block"
                        title="Nonaktif"></span>
                @endif
            </div>
        </div>

        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-1">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $member->user->name }}</h2>
                <div class="flex items-center justify-center gap-2">
                    <!-- Tier Badge -->
                    @if($member->tier === 'PLATINUM')
                        <span
                            class="bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-300 text-[10px] font-bold px-2 py-0.5 rounded border border-purple-100 dark:border-purple-800">Platinum
                            Member</span>
                    @elseif($member->tier === 'GOLD')
                        <span
                            class="bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded border border-amber-100 dark:border-amber-800">Gold
                            Member</span>
                    @elseif($member->tier === 'SILVER')
                        <span
                            class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded border border-blue-100 dark:border-blue-800">Silver
                            Member</span>
                    @else
                        <span
                            class="bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-300 text-[10px] font-bold px-2 py-0.5 rounded border border-orange-100 dark:border-orange-800">Bronze
                            Member</span>
                    @endif

                    <!-- Status Badge -->
                    @if($member->status === 'ACTIVE')
                        <span
                            class="bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded border border-emerald-100 dark:border-emerald-800">Aktif</span>
                    @elseif($member->status === 'SUSPENDED')
                        <span
                            class="bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 text-[10px] font-bold px-2 py-0.5 rounded border border-rose-100 dark:border-rose-800">Diblokir</span>
                    @else
                        <span
                            class="bg-slate-50 text-slate-600 dark:bg-slate-900/20 dark:text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded border border-slate-100 dark:border-slate-800">Nonaktif</span>
                    @endif
                </div>
            </div>
            <p class="text-sm text-slate-500 font-mono mb-4">{{ $member->nomorAnggota }}</p>

            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                <a href="{{ route('admin.members.edit', $member->id) }}"
                    class="px-4 py-2 bg-primary hover:bg-blue-700 text-white text-[12px] font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class='bx bx-edit'></i> Edit Profil
                </a>
                <button
                    class="px-4 py-2 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-[12px] font-bold rounded-lg transition-colors flex items-center gap-2">
                    <i class='bx bx-lock-alt'></i> Reset Password
                </button>
                @if($member->status === 'ACTIVE')
                    <button wire:click="suspendMember" wire:confirm="Yakin ingin memblokir member ini?"
                        class="px-4 py-2 bg-white dark:bg-darkCard border border-rose-200 dark:border-rose-900/50 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 text-[12px] font-bold rounded-lg transition-colors flex items-center gap-2">
                        <i class='bx bx-block'></i> Blokir
                    </button>
                @else
                    <button wire:click="activateMember"
                        class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-[12px] font-bold rounded-lg transition-colors flex items-center gap-2">
                        <i class='bx bx-check-circle'></i> Aktifkan
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Info and Portfolio Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Personal Information -->
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <h3
                class="text-[13px] font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-700 pb-2">
                Informasi Pribadi
            </h3>
            <div class="space-y-3 text-[13px]">
                <div class="flex justify-between">
                    <span class="text-slate-500">Unit Kerja/Prodi</span>
                    <span
                        class="font-medium text-slate-800 dark:text-white">{{ $member->unitKerja === 'unknown' ? 'Belum Diisi' : ($member->unitKerja ?? '-') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Email</span>
                    <span
                        class="font-medium text-slate-800 dark:text-white truncate max-w-[150px]">{{ $member->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">No. Telepon</span>
                    <span class="font-medium text-slate-800 dark:text-white">{{ $member->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jenis Kelamin</span>
                    <span class="font-medium text-slate-800 dark:text-white">
                        {{ $member->gender === 'M' ? 'Laki-laki' : ($member->gender === 'F' ? 'Perempuan' : '-') }}
                    </span>
                </div>
                <div class="flex flex-col gap-1 mt-2">
                    <span class="text-slate-500">Alamat Domisili</span>
                    <span
                        class="font-medium text-slate-800 dark:text-white leading-snug">{{ $member->address ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Portfolio Simpanan -->
        <div
            class="lg:col-span-2 bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Portfolio Simpanan</h3>
                <div class="text-[11px] text-slate-500 bg-slate-50 dark:bg-slate-800 px-2 py-1 rounded">
                    Total Aset: <span class="font-bold text-primary text-[13px]">Rp
                        {{ number_format($member->totalSimpanan, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Simpanan Pokok -->
                <div
                    class="p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Pokok</span>
                            <i class='bx bxs-lock-alt text-slate-400'></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($member->simpananPokok, 0, ',', '.') }}</h4>
                        <p class="text-[10px] text-slate-400 mt-1">Non-withdrawable</p>
                    </div>
                    @if($member->simpananPokok > 0)
                        <button
                            class="w-full mt-3 py-1.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-400 text-[11px] font-bold rounded cursor-not-allowed">
                            Lunas
                        </button>
                    @else
                        <a href="{{ route('admin.members.simpanan', $member->id) }}"
                            class="w-full mt-3 py-1.5 bg-primary hover:bg-blue-700 text-white text-[11px] font-bold rounded transition-colors flex items-center justify-center gap-1">
                            <i class='bx bx-plus'></i> Setor Pokok
                        </a>
                    @endif
                </div>

                <!-- Simpanan Wajib -->
                <div
                    class="p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Wajib</span>
                            <i class='bx bxs-calendar text-slate-400'></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($member->simpananWajib, 0, ',', '.') }}</h4>
                        <p class="text-[10px] text-slate-400 mt-1">Bulanan (Mandatory)</p>
                    </div>
                    <a href="{{ route('admin.members.simpanan', $member->id) }}"
                        class="w-full mt-3 py-1.5 bg-primary hover:bg-blue-700 text-white text-[11px] font-bold rounded transition-colors flex items-center justify-center gap-1">
                        <i class='bx bx-plus'></i> Setor Wajib
                    </a>
                </div>

                <!-- Simpanan Sukarela -->
                <div
                    class="p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-lg border border-emerald-200 dark:border-emerald-800/50 flex flex-col justify-between relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 w-12 h-12 bg-emerald-100 dark:bg-emerald-800/30 rounded-bl-full -mr-2 -mt-2">
                    </div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-1">
                            <span
                                class="text-[10px] text-emerald-700 dark:text-emerald-400 uppercase font-bold tracking-wider">Sukarela</span>
                            <i class='bx bxs-wallet text-emerald-500'></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 dark:text-white">Rp
                            {{ number_format($member->simpananSukarela, 0, ',', '.') }}</h4>
                        <p class="text-[10px] text-emerald-600/70 dark:text-emerald-400/70 mt-1">Liquid / Bisa Ditarik
                        </p>
                    </div>
                    <div class="flex gap-2 mt-3 relative z-10">
                        <a href="{{ route('admin.members.simpanan', $member->id) }}"
                            class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded transition-colors text-center">
                            Setor
                        </a>
                        <a href="{{ route('admin.members.simpanan', $member->id) }}"
                            class="flex-1 py-1.5 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-[11px] font-bold rounded hover:bg-emerald-50 dark:hover:bg-slate-700 transition-colors text-center">
                            Tarik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loyalty and Tabs Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Loyalty Tier Card -->
        <div
            class="bg-white dark:bg-darkCard p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">Loyalty Tier</p>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                        {{ ucfirst(strtolower($member->tier)) }}</h3>
                </div>
                <div
                    class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center text-2xl text-slate-500">
                    <i class='bx bxs-medal'></i>
                </div>
            </div>

            <div class="mb-2">
                <div class="flex justify-between text-[11px] font-medium mb-1">
                    <span class="text-slate-600 dark:text-slate-300">{{ number_format($member->points, 0, ',', '.') }}
                        Pts</span>
                    <span class="text-slate-400">Target:
                        {{ number_format($member->pointsToNextTier + $member->points, 0, ',', '.') }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    <div class="
                        @if($member->tier === 'PLATINUM') bg-purple-400
                        @elseif($member->tier === 'GOLD') bg-amber-400
                        @elseif($member->tier === 'SILVER') bg-blue-400
                        @else bg-orange-400
                        @endif
                        h-2 rounded-full transition-all duration-300
                    " style="width: {{ $member->nextTierProgress }}%"></div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                <div>
                    <p class="text-[10px] text-slate-400">Total Belanja (YTD)</p>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">Rp
                        {{ number_format($member->totalSpent, 0, ',', '.') }}</p>
                </div>
                <button class="text-primary text-[11px] font-bold hover:underline">Lihat Reward</button>
            </div>
        </div>

        <!-- Tabs Section -->
        <div
            class="lg:col-span-2 bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden min-h-[300px] flex flex-col">
            <!-- Tab Buttons -->
            <div class="flex border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20">
                <button wire:click="switchTab('trx')"
                    class="flex-1 py-3 text-[12px] {{ $activeTab === 'trx' ? 'font-bold text-primary border-b-2 border-primary bg-white dark:bg-darkCard' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent' }} transition-colors">
                    Riwayat Belanja
                </button>
                <button wire:click="switchTab('simpanan')"
                    class="flex-1 py-3 text-[12px] {{ $activeTab === 'simpanan' ? 'font-bold text-primary border-b-2 border-primary bg-white dark:bg-darkCard' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent' }} transition-colors">
                    Mutasi Simpanan
                </button>
                <button wire:click="switchTab('log')"
                    class="flex-1 py-3 text-[12px] {{ $activeTab === 'log' ? 'font-bold text-primary border-b-2 border-primary bg-white dark:bg-darkCard' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent' }} transition-colors">
                    Activity Log
                </button>
            </div>

            <!-- Tab Content -->
            <div class="flex-1 overflow-y-auto max-h-[300px]">
                <!-- Riwayat Belanja Tab -->
                @if($activeTab === 'trx')
                    <table class="w-full text-left text-[12px] text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0">
                            <tr>
                                <th class="px-5 py-2 font-bold text-slate-500 uppercase tracking-wide">ID & Waktu</th>
                                <th class="px-5 py-2 font-bold text-slate-500 uppercase tracking-wide">Kasir</th>
                                <th class="px-5 py-2 font-bold text-slate-500 uppercase tracking-wide text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-5 py-3">
                                        #{{ $transaction->invoiceNumber }}<br>
                                        <span
                                            class="text-[10px] text-slate-400">{{ $transaction->created_at->format('d M, H:i') }}</span>
                                    </td>
                                    <td class="px-5 py-3">{{ $transaction->user->name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-slate-800 dark:text-white">
                                        Rp {{ number_format($transaction->totalAmount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-slate-400">
                                        <i class='bx bx-receipt text-3xl mb-2'></i>
                                        <p>Belum ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($transactions->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @endif

                <!-- Mutasi Simpanan Tab -->
                @if($activeTab === 'simpanan')
                    <table class="w-full text-left text-[12px] text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0">
                            <tr>
                                <th class="px-5 py-2 font-bold text-slate-500 uppercase tracking-wide">Tanggal</th>
                                <th class="px-5 py-2 font-bold text-slate-500 uppercase tracking-wide">Tipe</th>
                                <th class="px-5 py-2 font-bold text-slate-500 uppercase tracking-wide text-right">Nominal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($simpananTransactions as $simpanan)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-5 py-3">{{ $simpanan->created_at->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="
                                                    @if($simpanan->type === 'POKOK') bg-slate-50 text-slate-600 dark:bg-slate-800 dark:text-slate-300
                                                    @elseif($simpanan->type === 'WAJIB') bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-300
                                                    @else bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300
                                                    @endif
                                                    px-2 py-0.5 rounded font-bold text-[10px]
                                                ">{{ $simpanan->typeLabel }}</span>
                                        {{ $simpanan->transactionTypeLabel }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-right font-bold {{ $simpanan->transactionType === 'SETOR' ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $simpanan->transactionType === 'SETOR' ? '+' : '-' }} Rp
                                        {{ number_format($simpanan->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-slate-400">
                                        <i class='bx bx-wallet text-3xl mb-2'></i>
                                        <p>Belum ada mutasi simpanan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($simpananTransactions->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                            {{ $simpananTransactions->links() }}
                        </div>
                    @endif
                @endif

                <!-- Activity Log Tab -->
                @if($activeTab === 'log')
                    <div class="p-5 space-y-4">
                        <div class="flex gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 text-xs">
                                <i class='bx bx-user-check'></i>
                            </div>
                            <div>
                                <p class="text-[12px] font-bold text-slate-800 dark:text-white">Member Terdaftar</p>
                                <p class="text-[10px] text-slate-400">{{ $member->joinDate->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($member->lastPurchase)
                            <div class="flex gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 text-xs">
                                    <i class='bx bx-shopping-bag'></i>
                                </div>
                                <div>
                                    <p class="text-[12px] font-bold text-slate-800 dark:text-white">Transaksi Terakhir</p>
                                    <p class="text-[10px] text-slate-400">{{ $member->lastPurchase->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="text-center text-slate-400 text-[11px] mt-6">
                            <p>Detail activity log akan ditambahkan</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>