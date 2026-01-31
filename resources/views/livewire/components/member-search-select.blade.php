<div class="relative w-full">
    @if($selectedName)
        <div class="flex items-center justify-between p-2 bg-emerald-50 border border-emerald-200 rounded-lg">
            <span class="text-xs font-bold text-emerald-700 truncate">{{ $selectedName }}</span>
            <button wire:click="$set('selectedName', '')" class="text-emerald-400 hover:text-emerald-600">
                <i class='bx bx-x'></i>
            </button>
        </div>
    @else
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="query" placeholder="Cari nama..."
                class="w-full px-3 py-2 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 outline-none">

            <div wire:loading wire:target="query" class="absolute right-3 top-2.5">
                <i class='bx bx-loader-alt animate-spin text-slate-400'></i>
            </div>
        </div>

        @if(!empty($results))
            <div
                class="absolute z-50 w-full mt-1 bg-white dark:bg-darkCard border border-slate-100 dark:border-slate-700 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                @foreach($results as $result)
                    <button wire:click="selectResult({{ $result->id }}, '{{ addslashes($result->name) }}')"
                        class="w-full text-left px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors border-b border-slate-50 dark:border-slate-800 last:border-none group">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-primary">{{ $result->name }}
                        </p>
                        <p class="text-[10px] text-slate-400">{{ $result->nomorAnggota }}</p>
                    </button>
                @endforeach
            </div>
        @elseif(strlen($query) >= 2)
            <div class="absolute z-50 w-full mt-1 bg-white p-3 text-center text-xs text-slate-400 border rounded-lg shadow-sm">
                Tidak ditemukan.
            </div>
        @endif
    @endif
</div>