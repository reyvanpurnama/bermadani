{{-- RAT Wizard Step Indicator --}}
@props(['currentStep' => 1, 'sessionId' => null, 'sessionStatus' => 'DRAFT'])

@php
    $steps = [
        ['num' => 1, 'label' => 'Konfigurasi', 'icon' => 'bx-cog', 'route' => 'admin.rat.setup', 'params' => []],
        ['num' => 2, 'label' => 'Anggota', 'icon' => 'bx-group', 'route' => 'admin.rat.eligibility', 'params' => ['session' => $sessionId]],
        ['num' => 3, 'label' => 'Alokasi SHU', 'icon' => 'bx-pie-chart-alt-2', 'route' => 'admin.rat.allocation', 'params' => ['session' => $sessionId]],
        ['num' => 4, 'label' => 'Pencairan', 'icon' => 'bx-wallet', 'route' => 'admin.rat.disbursement', 'params' => ['session' => $sessionId]],
    ];
@endphp

<div class="bg-white dark:bg-darkCard p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
    <div class="flex items-center justify-between gap-2">
        @foreach($steps as $index => $step)
            @php
                $isActive = $step['num'] === $currentStep;
                $isCompleted = $step['num'] < $currentStep;
                $isClickable = $sessionId && $step['num'] <= $currentStep;
            @endphp

            {{-- Step --}}
            <div class="flex items-center gap-2 {{ $isActive ? 'flex-1' : '' }}">
                @if($isClickable && !$isActive)
                    <a href="{{ route($step['route'], $step['params']) }}"
                       class="flex items-center gap-2 group cursor-pointer">
                @else
                    <div class="flex items-center gap-2">
                @endif

                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0 transition-all
                        {{ $isActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : '' }}
                        {{ $isCompleted ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 group-hover:bg-emerald-200' : '' }}
                        {{ !$isActive && !$isCompleted ? 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500' : '' }}">
                        @if($isCompleted)
                            <i class='bx bx-check text-lg'></i>
                        @else
                            <i class='bx {{ $step["icon"] }} text-base'></i>
                        @endif
                    </div>
                    <div class="{{ $isActive ? '' : 'hidden sm:block' }}">
                        <p class="text-[9px] font-bold uppercase tracking-wider {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                            Step {{ $step['num'] }}
                        </p>
                        <p class="text-xs font-bold {{ $isActive ? 'text-slate-800 dark:text-white' : 'text-slate-500 dark:text-slate-400' }}">
                            {{ $step['label'] }}
                        </p>
                    </div>

                @if($isClickable && !$isActive)
                    </a>
                @else
                    </div>
                @endif
            </div>

            {{-- Connector Line --}}
            @if(!$loop->last)
                <div class="flex-1 h-0.5 rounded-full max-w-[60px] {{ $isCompleted ? 'bg-emerald-300 dark:bg-emerald-600' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
            @endif
        @endforeach
    </div>
</div>
