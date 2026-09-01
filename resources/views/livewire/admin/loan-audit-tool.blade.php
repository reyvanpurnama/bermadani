<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold backdrop-blur-md mb-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Direct Database Audit & Cash Flow Analytics
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Audit Keuangan & Aliran Kas Bulanan</h1>
                <p class="mt-1 text-sm text-indigo-200 max-w-2xl">
                    Analisis real-time data pinjaman, angsuran terbayar, mutasi simpanan, serta visualisasi tren aliran kas bulanan {{ config('cooperative.name') }}.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.rat-detail') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition backdrop-blur-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan RAT
                </a>
                <button wire:click="syncToRatReport" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Sync Real Data ke RAT
                </button>
            </div>
        </div>
    </div>

    {{-- Real-Time KPI Cards Header --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Plafon --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Plafon Disalurkan</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                    <i class='bx bx-credit-card text-xl'></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">
                Rp {{ number_format($totalPlafonDisalurkan, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $totalLoanCount }} Kontrak</span> Pinjaman terdaftar
            </div>
        </div>

        {{-- Sisa Piutang Berjalan --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-500">Piutang Berjalan (Outstanding)</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                    <i class='bx bx-time text-xl'></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">
                Rp {{ number_format($totalSisaPiutang, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 font-semibold text-[10px]">{{ $activeLoanCount }} Lancar</span>
                @if($overdueLoanCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-600 font-semibold text-[10px]">{{ $overdueLoanCount }} Menunggak</span>
                @endif
            </div>
        </div>

        {{-- Realisasi Angsuran --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-500">Realisasi Angsuran Terbayar</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i class='bx bx-check-double text-xl'></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                Rp {{ number_format($totalAngsuranTerbayar, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                <span class="font-semibold text-emerald-600">{{ $completedLoanCount }} Pinjaman</span> telah LUNAS
            </div>
        </div>

        {{-- Total Simpanan --}}
        <div class="bg-white dark:bg-darkCard p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-500">Total Akumulasi Simpanan</span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                    <i class='bx bx-wallet text-xl'></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-purple-600 dark:text-purple-400">
                Rp {{ number_format($totalSemuaSimpanan, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500 truncate">
                Pokok: {{ number_format($totalSimpananPokok/1000, 0) }}rb | Wajib: {{ number_format($totalSimpananWajib/1000, 0) }}rb
            </div>
        </div>
    </div>

    {{-- Source Share & Revenue Distribution Card (BMT ITQAN vs BERMADANI) --}}
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-5 border border-indigo-500/20 text-white shadow-lg space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-sm">
                    %
                </div>
                <div>
                    <h3 class="text-sm font-bold">Analisis Pembagian Pendapatan & Sumber Dana</h3>
                    <p class="text-[11px] text-slate-400">Perbandingan kontribusi realisasi pendapatan & penyaluran plafon antara <strong>BMT ITQAN</strong> dan <strong>Internal BERMADANI</strong>.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-extrabold border border-indigo-500/30">
                Direct DB Calculation
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 1. Realisasi Pendapatan Angsuran --}}
            <div class="space-y-2 bg-white/5 p-4 rounded-xl border border-white/5">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-indigo-300 uppercase tracking-wider text-[10px]">Realisasi Pendapatan Angsuran</span>
                    <span class="font-black text-emerald-400">Rp {{ number_format($totalAngsuranTerbayar, 0, ',', '.') }}</span>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-slate-800 rounded-full h-3 flex overflow-hidden border border-white/10">
                    <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $sourceShareStats['payments']['percent_bmt'] }}%" title="BMT ITQAN: {{ $sourceShareStats['payments']['percent_bmt'] }}%"></div>
                    <div class="bg-indigo-500 h-full transition-all duration-500" style="width: {{ $sourceShareStats['payments']['percent_bermadani'] }}%" title="Internal BERMADANI: {{ $sourceShareStats['payments']['percent_bermadani'] }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold">BMT ITQAN</div>
                            <div class="font-bold text-sky-400">
                                Rp {{ number_format($sourceShareStats['payments']['bmt'], 0, ',', '.') }}
                                <span class="text-[10px] text-sky-300 font-extrabold">({{ $sourceShareStats['payments']['percent_bmt'] }}%)</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold">Internal BERMADANI</div>
                            <div class="font-bold text-indigo-300">
                                Rp {{ number_format($sourceShareStats['payments']['bermadani'], 0, ',', '.') }}
                                <span class="text-[10px] text-indigo-200 font-extrabold">({{ $sourceShareStats['payments']['percent_bermadani'] }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Plafon Disalurkan --}}
            <div class="space-y-2 bg-white/5 p-4 rounded-xl border border-white/5">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-purple-300 uppercase tracking-wider text-[10px]">Total Plafon Pinjaman Disalurkan</span>
                    <span class="font-black text-purple-300">Rp {{ number_format($totalPlafonDisalurkan, 0, ',', '.') }}</span>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-slate-800 rounded-full h-3 flex overflow-hidden border border-white/10">
                    <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $sourceShareStats['plafon']['percent_bmt'] }}%" title="BMT ITQAN: {{ $sourceShareStats['plafon']['percent_bmt'] }}%"></div>
                    <div class="bg-purple-500 h-full transition-all duration-500" style="width: {{ $sourceShareStats['plafon']['percent_bermadani'] }}%" title="Internal BERMADANI: {{ $sourceShareStats['plafon']['percent_bermadani'] }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold">BMT ITQAN</div>
                            <div class="font-bold text-sky-400">
                                Rp {{ number_format($sourceShareStats['plafon']['bmt'], 0, ',', '.') }}
                                <span class="text-[10px] text-sky-300 font-extrabold">({{ $sourceShareStats['plafon']['percent_bmt'] }}%)</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                        <div>
                            <div class="text-[10px] text-slate-400 font-semibold">Internal BERMADANI</div>
                            <div class="font-bold text-purple-300">
                                Rp {{ number_format($sourceShareStats['plafon']['bermadani'], 0, ',', '.') }}
                                <span class="text-[10px] text-purple-200 font-extrabold">({{ $sourceShareStats['plafon']['percent_bermadani'] }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Navigation Tabs --}}
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pt-2">
        <div class="flex items-center gap-2 overflow-x-auto">
            <button wire:click="$set('activeTab', 'loans')" class="px-5 py-3 text-xs font-bold transition-all relative border-b-2 whitespace-nowrap {{ $activeTab === 'loans' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300' }}">
                <div class="flex items-center gap-2">
                    <i class='bx bx-money-withdraw text-base'></i>
                    <span>Portofolio Pinjaman</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'loans' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                        {{ $totalLoanCount }}
                    </span>
                </div>
            </button>

            <button wire:click="$set('activeTab', 'savings')" class="px-5 py-3 text-xs font-bold transition-all relative border-b-2 whitespace-nowrap {{ $activeTab === 'savings' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300' }}">
                <div class="flex items-center gap-2">
                    <i class='bx bx-vault text-base'></i>
                    <span>Simpanan Anggota</span>
                </div>
            </button>

            <button wire:click="$set('activeTab', 'cash_flow')" class="px-5 py-3 text-xs font-bold transition-all relative border-b-2 whitespace-nowrap {{ $activeTab === 'cash_flow' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300' }}">
                <div class="flex items-center gap-2">
                    <i class='bx bx-line-chart text-base'></i>
                    <span>Aliran Kas Bulanan</span>
                </div>
            </button>

            <button wire:click="$set('activeTab', 'rat_sync')" class="px-5 py-3 text-xs font-bold transition-all relative border-b-2 whitespace-nowrap {{ $activeTab === 'rat_sync' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300' }}">
                <div class="flex items-center gap-2">
                    <i class='bx bx-sync text-base'></i>
                    <span>Sinkronisasi RAT</span>
                </div>
            </button>
        </div>

        {{-- Search & Filter Tools --}}
        @if(in_array($activeTab, ['loans', 'savings']))
            <div class="flex items-center gap-2 pb-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-2.5 text-slate-400 text-sm'></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / nomor anggota..." class="pl-9 pr-4 py-1.5 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 dark:text-white w-48 sm:w-64 shadow-sm">
                </div>

                @if($activeTab === 'loans')
                    <select wire:model.live="statusFilter" class="py-1.5 px-3 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 dark:text-white shadow-sm">
                        <option value="ALL">Semua Status</option>
                        <option value="ACTIVE">ACTIVE (Lancar)</option>
                        <option value="OVERDUE">OVERDUE (Menunggak)</option>
                        <option value="COMPLETED">COMPLETED (Lunas)</option>
                    </select>
                @endif
            </div>
        @endif
    </div>

    {{-- TAB 1: PORTOFOLIO PINJAMAN ANGGOTA --}}
    @if($activeTab === 'loans')
        <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4">Anggota / Kontrak</th>
                            <th class="py-3.5 px-4 text-right">Plafon Pinjaman</th>
                            <th class="py-3.5 px-4 text-center">Tenor & Angsuran</th>
                            <th class="py-3.5 px-4 text-right">Angsuran / Bln</th>
                            <th class="py-3.5 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3.5 px-4 text-center">Progress Pelunasan</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($loans as $loan)
                            @php
                                $percent = $loan->tenor > 0 ? min(100, round(($loan->paid_installments / $loan->tenor) * 100)) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $loan->member->name ?? 'Anggota #'.$loan->member_id }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                        <span>No: {{ $loan->member->nomorAnggota ?? '-' }}</span>
                                        @if($loan->account_number)
                                            <span class="text-indigo-500">({{ $loan->account_number }})</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format($loan->amount, 0, ',', '.') }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $loan->paid_installments }}</span>
                                    <span class="text-slate-400">/ {{ $loan->tenor }} Bln</span>
                                </td>

                                <td class="py-3.5 px-4 text-right font-medium">
                                    Rp {{ number_format($loan->monthlyPayment, 0, ',', '.') }}
                                </td>

                                <td class="py-3.5 px-4 text-right font-bold {{ $loan->remainingAmount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600' }}">
                                    Rp {{ number_format(max(0, $loan->remainingAmount), 0, ',', '.') }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="w-32 mx-auto">
                                        <div class="flex justify-between text-[10px] font-bold mb-1">
                                            <span class="text-slate-500">{{ $percent }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $percent >= 100 ? 'bg-emerald-500' : ($percent >= 50 ? 'bg-indigo-500' : 'bg-amber-500') }}" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if($loan->status === 'COMPLETED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            LUNAS
                                        </span>
                                    @elseif($loan->status === 'ACTIVE')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                            ACTIVE
                                        </span>
                                    @elseif($loan->status === 'OVERDUE')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            MENUNGGAK
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-500/10 text-slate-600 dark:text-slate-400">
                                            {{ $loan->status }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <button wire:click="viewLoanDetails({{ $loan->id }})" class="p-2 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold transition flex items-center justify-center mx-auto" title="Detail Pembayaran">
                                        <i class='bx bx-show text-base'></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <i class='bx bx-search-alt text-4xl mb-2 text-slate-300'></i>
                                    <p class="font-medium">Tidak ada data pinjaman yang cocok dengan pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $loans->links() }}
            </div>
        </div>
    @endif

    {{-- TAB 2: REKAPITULASI SIMPANAN ANGGOTA --}}
    @if($activeTab === 'savings')
        <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4">Nama Anggota</th>
                            <th class="py-3.5 px-4">No. Anggota / NIP</th>
                            <th class="py-3.5 px-4 text-right">Simpanan Pokok</th>
                            <th class="py-3.5 px-4 text-right">Simpanan Wajib</th>
                            <th class="py-3.5 px-4 text-right">Simpanan Sukarela</th>
                            <th class="py-3.5 px-4 text-right">Total Simpanan</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($membersSavings as $member)
                            @php
                                $totalSimpananMember = $member->simpananPokok + $member->simpananWajib + $member->simpananSukarela;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                    {{ $member->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">
                                    {{ $member->nomorAnggota ?? $member->nip ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium">
                                    Rp {{ number_format($member->simpananPokok, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium">
                                    Rp {{ number_format($member->simpananWajib, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium">
                                    Rp {{ number_format($member->simpananSukarela, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-purple-600 dark:text-purple-400">
                                    Rp {{ number_format($totalSimpananMember, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button wire:click="viewMemberSavingsDetails({{ $member->id }})" class="p-2 rounded-xl bg-slate-100 hover:bg-purple-50 hover:text-purple-600 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold transition flex items-center justify-center mx-auto" title="Histori Mutasi">
                                        <i class='bx bx-history text-base'></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class='bx bx-search-alt text-4xl mb-2 text-slate-300'></i>
                                    <p class="font-medium">Tidak ada data anggota yang ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $membersSavings->links() }}
            </div>
        </div>
    @endif

    {{-- TAB 3: VISUALISASI ALIRAN KAS BULANAN (APEXCHARTS & MONTHLY CARDS) --}}
    @if($activeTab === 'cash_flow')
        <div class="space-y-6">
            {{-- Year Selector Header Card --}}
            <div class="bg-white dark:bg-darkCard p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Analisis Tren Aliran Kas (Monthly Cash Flow)</h3>
                    <p class="text-xs text-slate-500 mt-1">Perbandingan real-time Angsuran Pinjaman Masuk, Setoran Simpanan, dan Penarikan Simpanan per bulan.</p>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500">Tahun:</label>
                    <select wire:model.live="selectedCashFlowYear" class="py-2 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                </div>
            </div>

            {{-- Financial ApexChart Visualisation --}}
            <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6"
                 x-data="{
                     chart: null,
                     chartData: @js($cashFlowChartData),
                     getColors() {
                         const isDark = document.documentElement.classList.contains('dark');
                         return {
                             text: isDark ? '#94a3b8' : '#64748b',
                             grid: isDark ? '#1e293b' : '#e2e8f0',
                             tooltipTheme: isDark ? 'dark' : 'light'
                         };
                     },
                     init() {
                         const c = this.getColors();
                         const options = {
                             series: [
                                 { name: 'Angsuran BMT ITQAN', type: 'column', data: this.chartData.angsuran_bmt.map(Number) },
                                 { name: 'Angsuran BERMADANI (Internal)', type: 'column', data: this.chartData.angsuran_bermadani.map(Number) },
                                 { name: 'Simpanan Masuk (Setor)', type: 'column', data: this.chartData.simpanan_masuk.map(Number) },
                                 { name: 'Simpanan Keluar (Tarik)', type: 'column', data: this.chartData.simpanan_keluar.map(Number) },
                                 { name: 'Net Arus Kas (Surplus/Defisit)', type: 'line', data: this.chartData.net_cashflow.map(Number) }
                             ],
                             chart: {
                                 height: 350,
                                 type: 'line',
                                 toolbar: { show: false },
                                 fontFamily: 'Inter',
                                 foreColor: c.text
                             },
                             stroke: {
                                 width: [0, 0, 0, 0, 3],
                                 curve: 'smooth'
                             },
                             plotOptions: {
                                 bar: { columnWidth: '60%', borderRadius: 4 }
                             },
                             colors: ['#0284c7', '#6366f1', '#10b981', '#f43f5e', '#f59e0b'],
                             fill: { opacity: [0.9, 0.9, 0.9, 0.9, 1] },
                             xaxis: { categories: this.chartData.categories },
                             grid: { borderColor: c.grid, strokeDashArray: 4 },
                             legend: { position: 'top', horizontalAlign: 'right' },
                             yaxis: {
                                 labels: {
                                     formatter: function (val) {
                                         return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
                                     }
                                 }
                             },
                             tooltip: {
                                 theme: c.tooltipTheme,
                                 y: {
                                     formatter: function (val) {
                                         return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
                                     }
                                 }
                             }
                         };

                         this.chart = new ApexCharts(this.$refs.cashFlowChart, options);
                         this.chart.render();

                         this.$watch('chartData', (val) => {
                             if (this.chart) {
                                 this.chart.updateOptions({ xaxis: { categories: val.categories } });
                                 this.chart.updateSeries([
                                     { name: 'Angsuran BMT ITQAN', type: 'column', data: val.angsuran_bmt.map(Number) },
                                     { name: 'Angsuran BERMADANI (Internal)', type: 'column', data: val.angsuran_bermadani.map(Number) },
                                     { name: 'Simpanan Masuk (Setor)', type: 'column', data: val.simpanan_masuk.map(Number) },
                                     { name: 'Simpanan Keluar (Tarik)', type: 'column', data: val.simpanan_keluar.map(Number) },
                                     { name: 'Net Arus Kas (Surplus/Defisit)', type: 'line', data: val.net_cashflow.map(Number) }
                                 ]);
                             }
                         });
                     }
                 }"
                 x-effect="chartData = @js($cashFlowChartData)">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">Grafik Trend Arus Kas Bulanan Tahun {{ $selectedCashFlowYear }}</h4>
                </div>
                <div x-ref="cashFlowChart" wire:ignore class="w-full"></div>
            </div>

            {{-- Horizontal Scrollable Month Selector Cards --}}
            <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Rincian Arus Kas Per Bulan</h4>
                        <p class="text-xs text-slate-400">Klik salah satu kartu bulan di bawah untuk membuka rincian log transaksi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                    @foreach($cashFlowSummaries as $item)
                        <button wire:click="selectCashFlowMonth({{ $item['month_num'] }})" class="p-3.5 rounded-xl border text-left transition-all duration-200 focus:outline-none {{ $selectedCashFlowMonth === $item['month_num'] ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700 hover:border-indigo-400 text-slate-800 dark:text-slate-200' }}">
                            <div class="font-bold text-xs flex items-center justify-between">
                                <span>{{ $item['month_name'] }}</span>
                                @if($item['net_cashflow'] > 0)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $selectedCashFlowMonth === $item['month_num'] ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-600' }}">Surplus</span>
                                @elseif($item['net_cashflow'] < 0)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $selectedCashFlowMonth === $item['month_num'] ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-600' }}">Defisit</span>
                                @endif
                            </div>

                            <div class="mt-3 space-y-1 text-[11px]">
                                <div class="flex justify-between">
                                    <span class="{{ $selectedCashFlowMonth === $item['month_num'] ? 'text-indigo-200' : 'text-slate-400' }}">Angsuran:</span>
                                    <span class="font-semibold">Rp {{ number_format($item['angsuran']/1000, 0) }}k</span>
                                </div>
                                @if($item['angsuran'] > 0)
                                    <div class="text-[9px] flex justify-between {{ $selectedCashFlowMonth === $item['month_num'] ? 'text-indigo-200/80' : 'text-slate-400' }}">
                                        <span>• BMT: {{ $item['percent_bmt'] }}%</span>
                                        <span>• Internal: {{ $item['percent_bermadani'] }}%</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="{{ $selectedCashFlowMonth === $item['month_num'] ? 'text-indigo-200' : 'text-slate-400' }}">Simp. Masuk:</span>
                                    <span class="font-semibold">Rp {{ number_format($item['simpanan_masuk']/1000, 0) }}k</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="{{ $selectedCashFlowMonth === $item['month_num'] ? 'text-indigo-200' : 'text-slate-400' }}">Simp. Keluar:</span>
                                    <span class="font-semibold text-rose-400">Rp {{ number_format($item['simpanan_keluar']/1000, 0) }}k</span>
                                </div>
                                <div class="flex justify-between pt-1.5 border-t {{ $selectedCashFlowMonth === $item['month_num'] ? 'border-white/20' : 'border-slate-200 dark:border-slate-700' }}">
                                    <span class="{{ $selectedCashFlowMonth === $item['month_num'] ? 'text-indigo-200' : 'text-slate-400' }}">Net Kas:</span>
                                    <span class="font-extrabold {{ $selectedCashFlowMonth === $item['month_num'] ? 'text-white' : ($item['net_cashflow'] >= 0 ? 'text-emerald-600' : 'text-rose-600') }}">
                                        Rp {{ number_format($item['net_cashflow']/1000, 0) }}k
                                    </span>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Monthly Revenue Share & Cash Flow Table Breakdown --}}
            <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Tabel Breakdown Pendapatan & Arus Kas Per Bulan (Tahun {{ $selectedCashFlowYear }})</h4>
                        <p class="text-xs text-slate-400">Rincian bulanan nominal Rupiah dan persentase pembagian pendapatan antara BMT ITQAN dan Internal BERMADANI.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="py-3 px-4">Bulan</th>
                                <th class="py-3 px-4 text-right">Angsuran BMT ITQAN</th>
                                <th class="py-3 px-4 text-right">Angsuran BERMADANI</th>
                                <th class="py-3 px-4 text-right font-extrabold">Total Angsuran</th>
                                <th class="py-3 px-4 text-right">Simpanan Masuk</th>
                                <th class="py-3 px-4 text-right">Simpanan Keluar</th>
                                <th class="py-3 px-4 text-right font-extrabold">Net Kas (Surplus/Defisit)</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            @php
                                $sumBmt = 0;
                                $sumBermadani = 0;
                                $sumTotalAngsuran = 0;
                                $sumSimMasuk = 0;
                                $sumSimKeluar = 0;
                                $sumNetKas = 0;
                            @endphp
                            @foreach($cashFlowSummaries as $row)
                                @php
                                    $sumBmt += $row['angsuran_bmt'];
                                    $sumBermadani += $row['angsuran_bermadani'];
                                    $sumTotalAngsuran += $row['angsuran'];
                                    $sumSimMasuk += $row['simpanan_masuk'];
                                    $sumSimKeluar += $row['simpanan_keluar'];
                                    $sumNetKas += $row['net_cashflow'];
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <span>{{ $row['month_name'] }}</span>
                                        @if($selectedCashFlowMonth === $row['month_num'])
                                            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-ping"></span>
                                        @endif
                                    </td>

                                    {{-- BMT ITQAN --}}
                                    <td class="py-3 px-4 text-right">
                                        <div class="font-semibold text-sky-600 dark:text-sky-400">
                                            Rp {{ number_format($row['angsuran_bmt'], 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold">
                                            ({{ $row['percent_bmt'] }}%)
                                        </div>
                                    </td>

                                    {{-- BERMADANI --}}
                                    <td class="py-3 px-4 text-right">
                                        <div class="font-semibold text-indigo-600 dark:text-indigo-400">
                                            Rp {{ number_format($row['angsuran_bermadani'], 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold">
                                            ({{ $row['percent_bermadani'] }}%)
                                        </div>
                                    </td>

                                    {{-- Total Angsuran --}}
                                    <td class="py-3 px-4 text-right font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-950/10">
                                        Rp {{ number_format($row['angsuran'], 0, ',', '.') }}
                                    </td>

                                    {{-- Simpanan Masuk --}}
                                    <td class="py-3 px-4 text-right font-medium text-slate-700 dark:text-slate-300">
                                        Rp {{ number_format($row['simpanan_masuk'], 0, ',', '.') }}
                                    </td>

                                    {{-- Simpanan Keluar --}}
                                    <td class="py-3 px-4 text-right font-medium text-rose-500">
                                        Rp {{ number_format($row['simpanan_keluar'], 0, ',', '.') }}
                                    </td>

                                    {{-- Net Kas --}}
                                    <td class="py-3 px-4 text-right font-extrabold {{ $row['net_cashflow'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        Rp {{ number_format($row['net_cashflow'], 0, ',', '.') }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3 px-4 text-center">
                                        @if($row['net_cashflow'] > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600">Surplus</span>
                                        @elseif($row['net_cashflow'] < 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600">Defisit</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-500/10 text-slate-500">Nihil</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 dark:bg-slate-800 font-extrabold text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700">
                            <tr>
                                <td class="py-3.5 px-4">TOTAL TAHUN {{ $selectedCashFlowYear }}</td>
                                <td class="py-3.5 px-4 text-right text-sky-600 dark:text-sky-400">
                                    Rp {{ number_format($sumBmt, 0, ',', '.') }}
                                    <div class="text-[10px] text-slate-400">({{ $sumTotalAngsuran > 0 ? round(($sumBmt / $sumTotalAngsuran) * 100, 1) : 0 }}%)</div>
                                </td>
                                <td class="py-3.5 px-4 text-right text-indigo-600 dark:text-indigo-400">
                                    Rp {{ number_format($sumBermadani, 0, ',', '.') }}
                                    <div class="text-[10px] text-slate-400">({{ $sumTotalAngsuran > 0 ? round(($sumBermadani / $sumTotalAngsuran) * 100, 1) : 0 }}%)</div>
                                </td>
                                <td class="py-3.5 px-4 text-right text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($sumTotalAngsuran, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    Rp {{ number_format($sumSimMasuk, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-rose-500">
                                    Rp {{ number_format($sumSimKeluar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right {{ $sumNetKas >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($sumNetKas, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Selected Month Transactions Detail Panel --}}
            @if($selectedCashFlowMonth)
                <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                            Detail Transaksi Bulan {{ DateTime::createFromFormat('!m', $selectedCashFlowMonth)->format('F') }} {{ $selectedCashFlowYear }}
                        </h4>
                        <button wire:click="$set('selectedCashFlowMonth', null)" class="text-xs text-slate-400 hover:text-slate-600 font-semibold flex items-center gap-1">
                            <i class='bx bx-x text-lg'></i> Tutup Detail
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Left: Month Loan Payments --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <i class='bx bx-check-shield text-emerald-500 text-lg'></i>
                                <span class="font-bold text-xs text-slate-800 dark:text-slate-200">Realisasi Angsuran Pinjaman ({{ $monthLoanPayments->count() }} Transaksi)</span>
                            </div>
                            <div class="max-h-72 overflow-y-auto custom-scroll border border-slate-200 dark:border-slate-800 rounded-xl">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold">
                                        <tr>
                                            <th class="py-2.5 px-3">Tanggal</th>
                                            <th class="py-2.5 px-3">Anggota</th>
                                            <th class="py-2.5 px-3 text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @forelse($monthLoanPayments as $lp)
                                            <tr>
                                                <td class="py-2 px-3 text-slate-500">{{ $lp->paymentDate ? $lp->paymentDate->format('d M Y') : '-' }}</td>
                                                <td class="py-2 px-3 font-semibold text-slate-800 dark:text-slate-200">{{ $lp->loan->member->name ?? 'Anggota #'.$lp->loan->member_id }}</td>
                                                <td class="py-2 px-3 text-right font-bold text-emerald-600">Rp {{ number_format($lp->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="py-6 text-center text-slate-400">Tidak ada angsuran di bulan ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Right: Month Simpanan Transactions --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <i class='bx bx-vault text-indigo-500 text-lg'></i>
                                <span class="font-bold text-xs text-slate-800 dark:text-slate-200">Mutasi Simpanan Anggota ({{ $monthSimpananTransactions->count() }} Transaksi)</span>
                            </div>
                            <div class="max-h-72 overflow-y-auto custom-scroll border border-slate-200 dark:border-slate-800 rounded-xl">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 font-bold">
                                        <tr>
                                            <th class="py-2.5 px-3">Waktu</th>
                                            <th class="py-2.5 px-3">Anggota</th>
                                            <th class="py-2.5 px-3">Tipe</th>
                                            <th class="py-2.5 px-3 text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @forelse($monthSimpananTransactions as $st)
                                            <tr>
                                                <td class="py-2 px-3 text-slate-500">{{ $st->created_at ? $st->created_at->format('d M H:i') : '-' }}</td>
                                                <td class="py-2 px-3 font-semibold text-slate-800 dark:text-slate-200">{{ $st->member->name ?? 'Anggota' }}</td>
                                                <td class="py-2 px-3"><span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800">{{ $st->type }}</span></td>
                                                <td class="py-2 px-3 text-right font-bold {{ in_array($st->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'text-indigo-600' : 'text-rose-600' }}">
                                                    {{ in_array($st->transactionType, ['SETOR', 'TRANSFER_IN']) ? '+' : '-' }} Rp {{ number_format($st->amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="py-6 text-center text-slate-400">Tidak ada mutasi simpanan di bulan ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 4: SINKRONISASI RAT --}}
    @if($activeTab === 'rat_sync')
        <div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Sinkronisasi Ke Laporan Finansial RAT</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Sinkronkan secara langsung akumulasi Angsuran Pinjaman Terbayar & Total Simpanan Anggota ke Laporan RAT (Entri Manual Pembukuan).
                </p>
            </div>

            <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Tahun Buku RAT:</label>
                <select wire:model.live="selectedYear" class="py-1.5 px-3 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500 dark:text-white">
                    <option value="2024">Tahun Buku 2024</option>
                    <option value="2025">Tahun Buku 2025</option>
                    <option value="2026">Tahun Buku 2026</option>
                </select>
            </div>

            {{-- Comparison Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50">
                    <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">1. Pendapatan Jasa Pinjaman / Margin (Laba Rugi)</div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">
                        Rp {{ number_format($totalAngsuranTerbayar, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500">
                        Diperoleh dari total angsuran terbayar di tabel <code class="text-indigo-600">loan_payments</code>.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/50">
                    <div class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider mb-2">2. Akumulasi Simpanan Anggota (Neraca)</div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">
                        Rp {{ number_format($totalSemuaSimpanan, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500">
                        Simpanan Pokok (Rp {{ number_format($totalSimpananPokok, 0, ',', '.') }}), Wajib (Rp {{ number_format($totalSimpananWajib, 0, ',', '.') }}), Sukarela (Rp {{ number_format($totalSimpananSukarela, 0, ',', '.') }}).
                    </p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button wire:click="syncToRatReport" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 transition flex items-center gap-2">
                    <i class='bx bx-refresh text-lg'></i>
                    Eksekusi Sync Ke RAT Tahun {{ $selectedYear }}
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL 1: DETAIL PEMBAYARAN ANGSURAN PINJAMAN --}}
    @if($showDetailModal && $selectedLoan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-darkCard rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Detail Riwayat Angsuran Pinjaman</h3>
                        <p class="text-xs text-slate-500">
                            {{ $selectedLoan->member->name ?? 'Anggota #'.$selectedLoan->member_id }} - Account: {{ $selectedLoan->account_number ?? '-' }}
                        </p>
                    </div>
                    <button wire:click="closeDetailModal" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Loan Overview --}}
                <div class="grid grid-cols-3 gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Plafon Pinjaman</span>
                        <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($selectedLoan->amount, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Monthly Payment</span>
                        <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($selectedLoan->monthlyPayment, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Sisa Piutang</span>
                        <span class="font-bold text-amber-600">Rp {{ number_format(max(0, $selectedLoan->remainingAmount), 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Payments Table --}}
                <div class="max-h-60 overflow-y-auto custom-scroll border border-slate-200 dark:border-slate-800 rounded-xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal Pembayaran</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 px-3 text-right">Jumlah Terbayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            @forelse($selectedLoanPayments as $payment)
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold">
                                        {{ $payment->paymentDate ? $payment->paymentDate->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-500">
                                        {{ $payment->description ?? 'Pembayaran Angsuran' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-slate-400">
                                        Belum ada riwayat angsuran pembayaran terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <button wire:click="closeDetailModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 2: DETAIL MUTASI SIMPANAN ANGGOTA --}}
    @if($showMemberSavingsModal && $selectedMember)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-darkCard rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Detail Mutasi Simpanan Anggota</h3>
                        <p class="text-xs text-slate-500">
                            {{ $selectedMember->name }} - No. Anggota: {{ $selectedMember->nomorAnggota ?? '-' }}
                        </p>
                    </div>
                    <button wire:click="closeMemberSavingsModal" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Member Savings Breakdown --}}
                <div class="grid grid-cols-3 gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Simpanan Pokok</span>
                        <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($selectedMember->simpananPokok, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Simpanan Wajib</span>
                        <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($selectedMember->simpananWajib, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Simpanan Sukarela</span>
                        <span class="font-bold text-purple-600">Rp {{ number_format($selectedMember->simpananSukarela, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Transactions Table --}}
                <div class="max-h-60 overflow-y-auto custom-scroll border border-slate-200 dark:border-slate-800 rounded-xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold">
                            <tr>
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3">Jenis Simpanan</th>
                                <th class="py-2.5 px-3">Tipe Mutasi</th>
                                <th class="py-2.5 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            @forelse($selectedMemberTransactions as $tx)
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-500">
                                        {{ $tx->created_at ? $tx->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 font-semibold">
                                        {{ $tx->transactionType }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold {{ in_array($tx->transactionType, ['SETOR', 'TRANSFER_IN']) ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ in_array($tx->transactionType, ['SETOR', 'TRANSFER_IN']) ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">
                                        Belum ada data mutasi simpanan terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <button wire:click="closeMemberSavingsModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
