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
            Edit Data: {{ $member->user->name }}
            @if($member->tier === 'PLATINUM')
                <span class="bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wide">Platinum</span>
            @elseif($member->tier === 'GOLD')
                <span class="bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wide">Gold</span>
            @elseif($member->tier === 'SILVER')
                <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wide">Silver</span>
            @else
                <span class="bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wide">Bronze</span>
            @endif
        </h1>
    </div>
</div>

@livewire('admin.member-edit', ['id' => $member->id])
@endsection
