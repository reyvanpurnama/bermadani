@extends('layouts.supplier')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Notifikasi</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Pemberitahuan dari Koperasi UMB</p>
        </div>
        @if($notifications->where('isRead', false)->count() > 0)
        <form method="POST" action="{{ route('supplier.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="text-primary text-[13px] font-medium hover:underline flex items-center gap-1">
                <i class='bx bx-check-double'></i> Tandai Semua Sudah Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Notification List --}}
    <div class="space-y-3">
        @forelse($notifications as $notif)
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border {{ $notif->isRead ? 'border-slate-100 dark:border-slate-700' : 'border-primary/30 bg-indigo-50/30 dark:bg-indigo-500/5' }} p-4 transition-all hover:shadow-md">
                <div class="flex gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-xl {{ $notif->isRead ? 'bg-slate-100 dark:bg-slate-700 text-slate-500' : 'bg-primary/10 text-primary' }} flex items-center justify-center text-xl">
                            <i class='bx {{ $notif->icon ?? 'bx-bell' }}'></i>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-grow min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="font-semibold text-slate-800 dark:text-white text-[14px] {{ !$notif->isRead ? 'text-primary' : '' }}">
                                {{ $notif->title }}
                            </h4>
                            @if(!$notif->isRead)
                                <span class="flex-shrink-0 w-2 h-2 rounded-full bg-primary mt-1.5"></span>
                            @endif
                        </div>
                        <p class="text-[13px] text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                            {{ $notif->message }}
                        </p>
                        <div class="flex items-center gap-4 mt-3">
                            <span class="text-[11px] text-slate-400">
                                <i class='bx bx-time-five mr-0.5'></i> {{ $notif->created_at->diffForHumans() }}
                            </span>
                            @if(!$notif->isRead)
                                <form method="POST" action="{{ route('supplier.notifications.mark-read', $notif->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-primary hover:underline">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-4xl text-slate-300 dark:text-slate-600 mb-4">
                        <i class='bx bx-bell-off'></i>
                    </div>
                    <h4 class="font-semibold text-slate-700 dark:text-slate-300">Belum Ada Notifikasi</h4>
                    <p class="text-[13px] text-slate-500 mt-1">Notifikasi dari Koperasi akan muncul di sini</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
