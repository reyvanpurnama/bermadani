{{-- Sidebar Overlay (Mobile) --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

{{-- Sidebar --}}
<aside id="member-sidebar"
    class="fixed inset-y-0 left-0 bg-white dark:bg-darkCard w-[240px] border-r border-slate-200 dark:border-slate-700 flex flex-col z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0">
    
    {{-- Logo --}}
    <div class="h-16 flex items-center px-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
                <i class='bx bxs-cube-alt text-xl'></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900 dark:text-white leading-none">Bermadani</h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold mt-0.5">Member Area</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>

        <a href="{{ route('member.dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('member.dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-primary dark:text-blue-300' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bxs-dashboard text-lg mr-3'></i> 
            <span class="text-sm {{ request()->routeIs('member.dashboard') ? 'font-bold' : 'font-medium' }}">Dashboard</span>
        </a>

        <a href="{{ route('member.transactions') }}" 
           class="flex items-center px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('member.transactions') ? 'bg-blue-50 dark:bg-blue-900/20 text-primary dark:text-blue-300' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-shopping-bag text-lg mr-3'></i> 
            <span class="text-sm {{ request()->routeIs('member.transactions') ? 'font-bold' : 'font-medium' }}">Riwayat Belanja</span>
        </a>

        <a href="{{ route('member.simpanan') }}" 
           class="flex items-center px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('member.simpanan') ? 'bg-blue-50 dark:bg-blue-900/20 text-primary dark:text-blue-300' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-wallet text-lg mr-3'></i> 
            <span class="text-sm {{ request()->routeIs('member.simpanan') ? 'font-bold' : 'font-medium' }}">Simpanan Saya</span>
            @php
                $unreadCount = App\Models\SimpananTransaction::where('memberId', auth()->user()->member?->id)
                    ->where('isRead', false)
                    ->where('transactionType', 'TRANSFER_IN')
                    ->count();
            @endphp
            @if($unreadCount > 0)
                <span class="ml-auto w-2 h-2 bg-rose-500 rounded-full animate-pulse" title="{{ $unreadCount }} transfer belum dibaca"></span>
            @endif
        </a>

        <a href="{{ route('member.transfer') }}" 
           class="flex items-center px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('member.transfer') ? 'bg-blue-50 dark:bg-blue-900/20 text-primary dark:text-blue-300' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-transfer text-lg mr-3'></i> 
            <span class="text-sm {{ request()->routeIs('member.transfer') ? 'font-bold' : 'font-medium' }}">Transfer</span>
            <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold bg-emerald-500 text-white rounded-full">NEW</span>
        </a>

        <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 mt-6">Akun</p>

        <a href="{{ route('member.profile') }}" 
           class="flex items-center px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('member.profile') ? 'bg-blue-50 dark:bg-blue-900/20 text-primary dark:text-blue-300' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <i class='bx bx-user-circle text-lg mr-3'></i> 
            <span class="text-sm {{ request()->routeIs('member.profile') ? 'font-bold' : 'font-medium' }}">Profil & Keamanan</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-3 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-all group">
                <i class='bx bx-log-out text-lg mr-3'></i> 
                <span class="text-sm font-medium">Keluar</span>
            </button>
        </form>
    </nav>

    {{-- User Info --}}
    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                {{ substr(auth()->user()->member->name ?? auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">
                    {{ auth()->user()->member->name ?? auth()->user()->name }}
                </p>
                <p class="text-[11px] text-slate-500 truncate">
                    {{ auth()->user()->member->nomorAnggota ?? 'Member' }}
                </p>
            </div>
        </div>
    </div>
</aside>
