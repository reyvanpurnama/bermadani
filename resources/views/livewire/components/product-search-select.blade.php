<div class="relative w-full {{ (count($results) > 0 || strlen(trim($query)) >= 2) ? 'z-50' : 'z-10' }}">
    @if($selectedName)
        <div class="flex items-center justify-between p-2 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-lg">
            <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400 truncate">{{ $selectedName }}</span>
            <button type="button" wire:click="$set('selectedName', '')" class="text-indigo-400 hover:text-indigo-600">
                <i class='bx bx-x'></i>
            </button>
        </div>
    @else
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="query" placeholder="Cari produk / supplier..."
                class="w-full px-3 py-2 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-600 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 outline-none">

            <div wire:loading wire:target="query" class="absolute right-3 top-2.5">
                <i class='bx bx-loader-alt animate-spin text-slate-400'></i>
            </div>
        </div>

        @if(count($results) > 0)
            <div
                class="absolute z-[99] w-full mt-1 bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl max-h-56 overflow-y-auto left-0">
                @foreach($results as $result)
                    @php
                        $supplierInfo = $result->supplier?->businessName ?? 'Toko';
                        if ($result->supplier?->ownerName && $result->supplier->ownerName !== $result->supplier->businessName) {
                            $supplierInfo .= ' (' . $result->supplier->ownerName . ')';
                        }
                    @endphp
                    <button type="button" wire:click="selectResult({{ $result->id }}, '{{ addslashes($result->name) }}')"
                        class="w-full text-left px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-none group">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-primary">{{ $result->name }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            SKU: {{ $result->sku ?? '-' }} | Supplier: {{ $supplierInfo }}
                        </p>
                    </button>
                @endforeach
            </div>
        @elseif(strlen(trim($query)) >= 2)
            <div class="absolute z-[99] w-full mt-1 bg-white dark:bg-darkCard p-3 text-center text-xs text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl">
                Tidak ditemukan.
            </div>
        @endif
    @endif
</div>
