<div x-data="{ showBalance: {{ $showBalance ? 'true' : 'false' }} }" class="mx-auto max-w-5xl space-y-6 pb-4">
    @section('title', 'Beranda')
    @php
        $totalSimpanan = ($member->simpananPokok ?? 0) + ($member->simpananWajib ?? 0) + ($member->simpananSukarela ?? 0);
        $firstName = \Illuminate\Support\Str::before(trim($member->name ?? 'Anggota'), ' ') ?: 'Anggota';
    @endphp

    @if($unreadCount > 0)
        <div x-data="{ open: true }" x-show="open" x-cloak class="flex items-start gap-3 rounded-2xl bg-emerald-500/10 p-4 text-sm text-emerald-900 dark:bg-emerald-400/15 dark:text-emerald-100">
            <i class='bx bx-down-arrow-alt mt-0.5 text-xl text-emerald-600 dark:text-emerald-400'></i>
            <p class="min-w-0 flex-1 leading-relaxed">
                @if($unreadCount === 1)
                    Transfer masuk sebesar <strong>Rp {{ number_format($unreadTransfers->first()->amount, 0, ',', '.') }}</strong> dari {{ $unreadTransfers->first()->relatedMember->name ?? 'anggota' }}.
                @else
                    Anda menerima {{ $unreadCount }} transfer hari ini.
                @endif
                <a href="{{ route('member.transfer.history') }}" class="ml-1 font-bold underline">Lihat aktivitas</a>
            </p>
            <button @click="open = false" class="p-1 text-emerald-700 dark:text-emerald-300" aria-label="Tutup pemberitahuan"><i class='bx bx-x text-xl'></i></button>
        </div>
    @endif

    <header class="flex items-center justify-between gap-4">
        <div class="min-w-0"><p class="text-sm text-zinc-500 dark:text-zinc-400">Selamat datang kembali,</p><h2 class="truncate font-heading text-2xl font-extrabold tracking-tight text-zinc-900 dark:text-white">{{ $firstName }}</h2></div>
        <a href="{{ route('member.profile') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-[#155A6B] transition-colors hover:bg-zinc-200 dark:bg-zinc-800 dark:text-emerald-400" aria-label="Buka akun"><i class='bx bx-user text-xl'></i></a>
    </header>

    <section class="relative overflow-hidden rounded-3xl bg-[#155A6B] p-5 text-white shadow-xl shadow-[#155A6B]/20 sm:p-7">
        <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-emerald-400/20 blur-3xl"></div>
        <div class="relative"><div class="flex items-center justify-between gap-3"><p class="text-sm font-semibold text-white/75">Total simpanan</p><button @click="showBalance = !showBalance; $wire.toggleBalance()" class="rounded-full p-2 text-white/80 transition-colors hover:bg-white/10 hover:text-white" :aria-label="showBalance ? 'Sembunyikan saldo' : 'Tampilkan saldo'"><i class='bx text-xl' :class="showBalance ? 'bx-hide' : 'bx-show'"></i></button></div>
            <p class="mt-3 min-h-10 font-heading text-3xl font-extrabold tracking-tight sm:text-4xl"><span x-show="showBalance">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span><span x-show="!showBalance" x-cloak>Rp ••••••••</span></p>
            <div class="mt-5 flex items-center justify-between gap-3 border-t border-white/15 pt-4 text-xs text-white/75"><span>Simpanan sukarela</span><span class="font-bold text-white"><span x-show="showBalance">Rp {{ number_format($member->simpananSukarela ?? 0, 0, ',', '.') }}</span><span x-show="!showBalance" x-cloak>Rp •••</span></span></div>
        </div>
    </section>

    <section aria-label="Aksi cepat"><div class="grid grid-cols-3 gap-3">
        @unless($member?->isReadOnly())
            <a href="{{ route('member.transfer') }}" class="flex min-h-24 flex-col items-center justify-center gap-2 rounded-2xl bg-white p-3 text-center shadow-sm transition-transform active:scale-[0.98] dark:bg-zinc-900"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#155A6B]/10 text-xl text-[#155A6B] dark:bg-emerald-400/15 dark:text-emerald-400"><i class='bx bx-transfer'></i></span><span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Transfer</span></a>
        @else
            <a href="{{ route('member.transfer.history') }}" class="flex min-h-24 flex-col items-center justify-center gap-2 rounded-2xl bg-white p-3 text-center shadow-sm transition-transform active:scale-[0.98] dark:bg-zinc-900"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#155A6B]/10 text-xl text-[#155A6B] dark:bg-emerald-400/15 dark:text-emerald-400"><i class='bx bx-time-five'></i></span><span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Aktivitas</span></a>
        @endunless
        <a href="{{ route('member.simpanan') }}" class="flex min-h-24 flex-col items-center justify-center gap-2 rounded-2xl bg-white p-3 text-center shadow-sm transition-transform active:scale-[0.98] dark:bg-zinc-900"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-xl text-emerald-600 dark:bg-emerald-400/15 dark:text-emerald-400"><i class='bx bx-wallet'></i></span><span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Simpanan</span></a>
        <a href="{{ route('member.loans') }}" class="flex min-h-24 flex-col items-center justify-center gap-2 rounded-2xl bg-white p-3 text-center shadow-sm transition-transform active:scale-[0.98] dark:bg-zinc-900"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-xl text-amber-600 dark:bg-amber-400/15 dark:text-amber-400"><i class='bx bx-bank'></i></span><span class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Pembiayaan</span></a>
    </div></section>

    @if($shuInfo && ($shuInfo['shuAmount'] > 0 || $shuInfo['isDisbursed']))
        <section class="rounded-2xl bg-white p-5 shadow-sm dark:bg-zinc-900 sm:flex sm:items-center sm:justify-between"><div><p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">SHU {{ $shuInfo['year'] }}</p><p class="mt-1 font-heading text-2xl font-extrabold text-zinc-900 dark:text-white">Rp {{ number_format($shuInfo['shuAmount'], 0, ',', '.') }}</p></div><span class="mt-3 inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-400 sm:mt-0">{{ $shuInfo['isDisbursed'] ? 'Sudah dicairkan' : 'Menunggu pencairan' }}</span></section>
    @endif

    @if(count($activeLoans) > 0)
        <section class="rounded-2xl bg-white p-5 shadow-sm dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-bold text-zinc-900 dark:text-white">Pembiayaan aktif</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ count($activeLoans) }} akad berjalan</p></div><a href="{{ route('member.loans') }}" class="text-xs font-bold text-[#155A6B] dark:text-emerald-400">Lihat</a></div>
            @foreach($activeLoans->take(1) as $loan)<div class="mt-4 flex items-end justify-between gap-3 rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800/70"><div><p class="text-xs text-zinc-500 dark:text-zinc-400">Sisa kewajiban</p><p class="mt-1 font-heading text-xl font-extrabold text-zinc-900 dark:text-white">Rp {{ number_format($loan->remainingAmount, 0, ',', '.') }}</p></div><p class="text-right text-xs text-zinc-500 dark:text-zinc-400">Angsuran {{ $loan->paid_installments + 1 }}/{{ $loan->tenor }}</p></div>@endforeach
        </section>
    @endif

    <section class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-sm dark:bg-zinc-900"><div class="mb-4 flex items-center justify-between"><h3 class="text-sm font-bold text-zinc-900 dark:text-white">Aktivitas simpanan</h3><a href="{{ route('member.simpanan') }}" class="text-xs font-bold text-[#155A6B] dark:text-emerald-400">Lihat semua</a></div><div class="space-y-3">
            @forelse($recentSimpanan->take(3) as $simp) @php($incoming = in_array($simp->transactionType, ['SETOR', 'TRANSFER_IN']))
                <div class="flex items-center justify-between gap-3"><div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $incoming ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600' }}"><i class='bx {{ $incoming ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' }}'></i></span><div class="min-w-0"><p class="truncate text-xs font-bold text-zinc-800 dark:text-zinc-100">{{ $simp->transactionType === 'TRANSFER_IN' ? 'Transfer masuk' : ($simp->transactionType === 'TRANSFER_OUT' ? 'Transfer keluar' : 'Simpanan ' . strtolower($simp->type)) }}</p><p class="mt-0.5 text-[11px] text-zinc-500">{{ $simp->created_at->format('d M Y') }}</p></div></div><span class="shrink-0 text-xs font-bold {{ $incoming ? 'text-emerald-600' : 'text-rose-600' }}">{{ $incoming ? '+' : '-' }}Rp {{ number_format($simp->amount, 0, ',', '.') }}</span></div>
            @empty <p class="py-5 text-center text-xs text-zinc-500">Belum ada aktivitas simpanan.</p> @endforelse
        </div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm dark:bg-zinc-900"><div class="mb-4 flex items-center justify-between"><h3 class="text-sm font-bold text-zinc-900 dark:text-white">Belanja terbaru</h3><a href="{{ route('member.transactions') }}" class="text-xs font-bold text-[#155A6B] dark:text-emerald-400">Lihat semua</a></div><div class="space-y-3">
            @forelse($recentTransactions->take(3) as $trx)
                <div class="flex items-center justify-between gap-3"><div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#155A6B]/10 text-[#155A6B] dark:bg-emerald-400/15 dark:text-emerald-400"><i class='bx bx-shopping-bag'></i></span><div><p class="text-xs font-bold text-zinc-800 dark:text-zinc-100">Belanja toko</p><p class="mt-0.5 text-[11px] text-zinc-500">{{ $trx->created_at->format('d M Y') }}</p></div></div><span class="shrink-0 text-xs font-bold text-zinc-800 dark:text-zinc-100">Rp {{ number_format($trx->totalAmount, 0, ',', '.') }}</span></div>
            @empty <p class="py-5 text-center text-xs text-zinc-500">Belum ada belanja tercatat.</p> @endforelse
        </div></div>
    </section>
</div>
