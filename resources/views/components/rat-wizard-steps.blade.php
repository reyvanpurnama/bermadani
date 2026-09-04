{{-- RAT Wizard Step Indicator --}}
@props(['currentStep' => 1, 'sessionId' => null, 'sessionStatus' => 'DRAFT'])

@php
    $steps = [
        ['num' => 1, 'label' => 'Konfigurasi', 'icon' => 'bx-cog', 'route' => 'admin.rat.setup', 'params' => []],
        ['num' => 2, 'label' => 'Anggota', 'icon' => 'bx-group', 'route' => 'admin.rat.eligibility', 'params' => ['session' => $sessionId]],
        ['num' => 3, 'label' => 'Alokasi SHU', 'icon' => 'bx-pie-chart-alt-2', 'route' => 'admin.rat.allocation', 'params' => ['session' => $sessionId]],
        ['num' => 4, 'label' => 'Pencairan', 'icon' => 'bx-wallet', 'route' => 'admin.rat.disbursement', 'params' => ['session' => $sessionId]],
    ];
    $activeStepItem = $steps[$currentStep - 1] ?? $steps[0];
@endphp

<div class="bg-white dark:bg-darkCard p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
    {{-- Mobile Compact Step View (< sm) --}}
    <div class="block sm:hidden space-y-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                    {{ $currentStep }}
                </span>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Langkah {{ $currentStep }} dari 4</span>
                    <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1">
                        <i class='bx {{ $activeStepItem["icon"] }} text-indigo-500'></i>
                        {{ $activeStepItem['label'] }}
                    </h4>
                </div>
            </div>
            
            {{-- Quick Nav Pills for Completed Steps --}}
            <div class="flex items-center gap-1">
                @foreach($steps as $s)
                    @if($sessionId && $s['num'] <= $currentStep && $s['num'] !== $currentStep)
                        <a href="{{ route($s['route'], $s['params']) }}"
                           class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-[10px] font-bold">
                            {{ $s['num'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Progress Bar Mobile --}}
        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
            <div class="bg-indigo-600 h-full transition-all duration-300" style="width: {{ ($currentStep / 4) * 100 }}%"></div>
        </div>
    </div>

    {{-- Desktop Step Indicator (≥ sm) --}}
    <div class="hidden sm:flex items-center justify-between gap-2">
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
                    <div>
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
