@extends('layouts.admin')

@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.members.show', $member->id) }}"
        class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
        <i class='bx bx-arrow-back text-xl'></i>
    </a>
    <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>
    <div>
        <h1 class="text-[14px] font-bold text-slate-800 dark:text-white flex items-center gap-2">
            Kelola Simpanan
            <span class="text-slate-400 font-normal">|</span>
            {{ $member->user->name }}
        </h1>
    </div>
</div>

@livewire('admin.simpanan-management', ['id' => $member->id])
@endsection
