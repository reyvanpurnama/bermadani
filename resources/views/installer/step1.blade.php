@extends('installer.layout')

@section('title', 'System Check')

@section('content')
<div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-8 shadow-xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <i class='bx bx-chip text-blue-400 text-2xl'></i>
            1. Pengecekan Persyaratan Server
        </h2>
        <p class="text-slate-400 text-sm mt-1">
            Memastikan lingkungan server memenuhi persyaratan minimum untuk menjalankan aplikasi Koperasi.
        </p>
    </div>

    <!-- Requirements List -->
    <div class="space-y-3 mb-8">
        @foreach($requirements as $key => $item)
            <div class="flex items-center justify-between p-4 rounded-xl border {{ $item['pass'] ? 'bg-slate-900/50 border-slate-700 text-slate-200' : 'bg-rose-950/30 border-rose-800 text-rose-300' }}">
                <div class="flex items-center gap-3">
                    @if($item['pass'])
                        <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                            <i class='bx bx-check text-lg'></i>
                        </div>
                    @else
                        <div class="w-7 h-7 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold">
                            <i class='bx bx-x text-lg'></i>
                        </div>
                    @endif
                    <span class="font-medium text-sm">{{ $item['name'] }}</span>
                </div>
                <div>
                    @if(isset($item['current']))
                        <span class="text-xs font-mono px-3 py-1 rounded-full {{ $item['pass'] ? 'bg-slate-800 text-slate-300' : 'bg-rose-900 text-rose-200' }}">
                            {{ $item['current'] }}
                        </span>
                    @else
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $item['pass'] ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-rose-950 text-rose-400 border border-rose-800' }}">
                            {{ $item['pass'] ? 'Memenuhi' : 'Belum Memenuhi' }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-700">
        <div>
            @if(!$allPassed)
                <p class="text-xs text-rose-400 flex items-center gap-1">
                    <i class='bx bx-error-circle'></i> Mohon perbaiki komponen server yang belum memenuhi sebelum melanjutkan.
                </p>
            @endif
        </div>
        <a href="{{ $allPassed ? route('installer.step2') : '#' }}" 
           class="px-6 py-3 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $allPassed ? 'bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-600/30' : 'bg-slate-700 text-slate-500 cursor-not-allowed pointer-events-none' }}">
            <span>Lanjut ke Database</span>
            <i class='bx bx-right-arrow-alt text-xl'></i>
        </a>
    </div>
</div>
@endsection
