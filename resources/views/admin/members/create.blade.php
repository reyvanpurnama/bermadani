@extends('layouts.admin')

@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.members.index') }}"
        class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
        <i class='bx bx-arrow-back text-xl'></i>
    </a>
    <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>
    <div>
        <h1 class="text-[14px] font-bold text-slate-800 dark:text-white">Registrasi Anggota Baru</h1>
    </div>
</div>

@livewire('admin.member-create')
@endsection
