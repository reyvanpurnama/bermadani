<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Clearing & Matching Produk Retail</h1>
            <p class="text-xs text-slate-500">Petakan nama barang dari CSV ke data Produk & Supplier di database untuk laporan RAT yang akurat.</p>
        </div>
        @if($csvExists && $unmappedCount > 0)
            <button wire:click="autoMap" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg transition-all flex items-center gap-2">
                <i class='bx bx-wand text-sm'></i>
                Match Otomatis Nama Sama
            </button>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Produk CSV</h3>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($totalCsvProducts) }} <span class="text-xs font-normal text-slate-400">Barang</span></p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2">Terpetakan (Mapped)</h3>
            <p class="text-3xl font-bold text-emerald-600">{{ number_format($mappedCount) }} <span class="text-xs font-normal text-emerald-400">Barang</span></p>
        </div>
        <div class="bg-white dark:bg-darkCard p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="text-xs font-bold text-rose-500 uppercase tracking-widest mb-2">Belum Terpetakan</h3>
            <p class="text-3xl font-bold text-rose-600">{{ number_format($unmappedCount) }} <span class="text-xs font-normal text-rose-400">Barang</span></p>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        {{-- Tabs --}}
        <div class="flex border-b border-slate-100 dark:border-slate-700">
            <button wire:click="$set('activeTab', 'upload')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'upload' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                1. Upload CSV
            </button>
            <button wire:click="$set('activeTab', 'mapping')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'mapping' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                2. Pemetaan Produk ({{ $unmappedCount }} Unmapped)
            </button>
            <button wire:click="$set('activeTab', 'preview')"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'preview' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                3. Preview Clearing Laporan
            </button>
        </div>

        <div class="p-6">
            {{-- Flash messages --}}
            @if(session()->has('message'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold rounded-xl flex items-center gap-2 border border-emerald-100 dark:border-emerald-900/30">
                    <i class='bx bx-check-circle text-lg'></i>
                    {{ session('message') }}
                </div>
            @endif

            {{-- TAB 1: UPLOAD CSV --}}
            @if($activeTab === 'upload')
                <div class="space-y-6">
                    <div class="text-center py-12 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800/30"
                        x-data="{ isDropping: false }" @dragover.prevent="isDropping = true"
                        @dragleave.prevent="isDropping = false" @drop.prevent="isDropping = false"
                        :class="{ 'border-primary bg-primary/5': isDropping }">

                        <input type="file" wire:model="csvFile" class="hidden" id="csvFile">

                        <label for="csvFile" class="cursor-pointer block space-y-4">
                            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 mx-auto flex items-center justify-center">
                                <i class='bx bx-cloud-upload text-3xl'></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700 dark:text-slate-200">Klik untuk upload Laporan Retail CSV</p>
                                <p class="text-xs text-slate-400 mt-1">Format file .csv dengan header kolom Tanggal, Nama Barang, Qty Terjual, dll.</p>
                            </div>
                        </label>

                        @if($csvFile)
                            <div class="mt-6 space-y-2 max-w-xs mx-auto">
                                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $csvFile->getClientOriginalName() }}</p>
                                <button wire:click="importCsv" class="w-full py-2.5 bg-primary text-white text-xs font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition-colors">
                                    <span wire:loading.remove wire:target="importCsv">Proses Upload & Clearing</span>
                                    <span wire:loading wire:target="importCsv">Memproses...</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($csvExists)
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 text-primary flex items-center justify-center">
                                    <i class='bx bxs-file-csv text-xl'></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">File Ter-import Saat Ini</p>
                                    <p class="text-[10px] text-slate-400">Ukuran: {{ number_format($fileStats['size'] / 1024, 2) }} KB | Diperbarui: {{ date('d M Y H:i:s', $fileStats['updated_at']) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="$set('activeTab', 'mapping')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                                    Mulai Pemetaan
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB 2: PEMETAAN PRODUK --}}
            @if($activeTab === 'mapping')
                <div class="space-y-4">
                    {{-- Search Mapping & Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/80">
                        {{-- Search --}}
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-search text-base'></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchMapping" placeholder="Cari nama CSV, produk, supplier..."
                                class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20">
                        </div>

                        {{-- Filter Status --}}
                        <div>
                            <select wire:model.live="filterStatus" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="all">Semua Status Hubungan</option>
                                <option value="mapped">Terpetakan (Mapped)</option>
                                <option value="unmapped">Belum Terpetakan (Unmapped)</option>
                            </select>
                        </div>

                        {{-- Filter Category --}}
                        <div>
                            <select wire:model.live="filterCategory" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="all">Semua Kategori Produk</option>
                                <option value="unmapped">Tanpa Kategori (Unmapped)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort By --}}
                        <div>
                            <select wire:model.live="sortBy" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="name_asc">Urutkan: Nama A - Z</option>
                                <option value="name_desc">Urutkan: Nama Z - A</option>
                                <option value="category_asc">Urutkan: Kategori</option>
                                <option value="supplier_asc">Urutkan: Supplier</option>
                            </select>
                        </div>
                    </div>

                    @if(count($mappingList) > 0)
                        <div class="overflow-hidden border border-slate-100 dark:border-slate-700 rounded-2xl">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4">Nama Barang di CSV (Raw)</th>
                                        <th class="px-6 py-4">Produk Database (Real)</th>
                                        <th class="px-6 py-4">Supplier & SKU</th>
                                        <th class="px-6 py-4 text-center w-36">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-darkCard">
                                    @foreach($mappingList as $item)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                            <td class="px-6 py-4">
                                                <span class="font-mono font-bold text-slate-700 dark:text-slate-200 block">{{ $item['raw_name'] }}</span>
                                                 <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-[10px] text-slate-450 font-medium">
                                                     @if(!empty($item['prices']['beli']))
                                                         <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700">
                                                             Beli: Rp {{ implode(', Rp ', array_map(fn($p) => number_format($p, 0, ',', '.'), $item['prices']['beli'])) }}
                                                         </span>
                                                     @endif
                                                     @if(!empty($item['prices']['jual']))
                                                         <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30">
                                                             Jual: Rp {{ implode(', Rp ', array_map(fn($p) => number_format($p, 0, ',', '.'), $item['prices']['jual'])) }}
                                                         </span>
                                                     @endif
                                                 </div>
                                            </td>
                                            <td class="px-6 py-4 min-w-[250px]">
                                                @if($item['mapping'] && $item['mapping']->product)
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                        <span class="font-bold text-slate-800 dark:text-white">{{ $item['mapping']->product->name }}</span>
                                                    </div>
                                                @else
                                                    {{-- Product Search Autocomplete selector --}}
                                                    <livewire:components.product-search-select
                                                        :key="'select-'.$item['raw_name']"
                                                        :extra-data="$item['raw_name']"
                                                    />
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($item['mapping'] && $item['mapping']->product)
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-350 rounded text-[9px] font-bold">
                                                            {{ $item['category_name'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-slate-600 dark:text-slate-305 mt-1 font-semibold">
                                                        {{ $item['supplier_name'] }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                        SKU: {{ $item['mapping']->product->sku ?? '-' }}
                                                    </p>
                                                @else
                                                    <span class="text-xs text-slate-400 italic block">Belum terhubung</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($item['mapping'] && $item['mapping']->product)
                                                    <button wire:click="unmapProduct('{{ addslashes($item['raw_name']) }}')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-lg transition-colors">
                                                        Batal Petakan
                                                    </button>
                                                @else
                                                    <span class="text-xs text-slate-300 italic">Menunggu input</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class='bx bx-info-circle text-4xl text-slate-300 mb-2'></i>
                            <h3 class="font-bold text-slate-700 dark:text-slate-300">Tidak ada produk matching</h3>
                            <p class="text-slate-400 text-xs mt-1">Pastikan CSV sudah di-upload dan filter pencarian tidak salah.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB 3: PREVIEW DATA CLEARING --}}
            @if($activeTab === 'preview')
                <div class="space-y-4">
                    @if(count($previewRows) > 0)
                        <div class="overflow-x-auto border border-slate-100 dark:border-slate-700 rounded-2xl">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Nama CSV</th>
                                        <th class="px-4 py-3">Produk Database</th>
                                        <th class="px-4 py-3">Supplier</th>
                                        <th class="px-4 py-3 text-right">Qty</th>
                                        <th class="px-4 py-3 text-right">Harga Beli</th>
                                        <th class="px-4 py-3 text-right">Total Beli</th>
                                        <th class="px-4 py-3 text-right">Harga Jual</th>
                                        <th class="px-4 py-3 text-right">Total Jual</th>
                                        <th class="px-4 py-3 text-right text-emerald-600">Keuntungan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-darkCard text-xs">
                                    @foreach($previewRows as $row)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                            <td class="px-4 py-3 font-mono text-slate-500">{{ $row['tanggal'] }}</td>
                                            <td class="px-4 py-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                                {{ $row['raw_name'] }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($row['product'])
                                                    <span class="font-bold text-slate-900 dark:text-white">{{ $row['product']->name }}</span>
                                                @else
                                                    <span class="px-2 py-1 bg-rose-50 text-rose-600 font-bold rounded text-[10px] border border-rose-100">
                                                        ⚠️ Belum Dipetakan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($row['product'])
                                                    <span class="text-slate-600 dark:text-slate-400">
                                                        {{ $row['product']->supplier?->businessName ?? 'TOKO (Koperasi)' }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-300 italic">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono">{{ number_format($row['quantity']) }} {{ $row['satuan'] }}</td>
                                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($row['harga_beli_satuan']) }}</td>
                                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($row['total_harga_beli']) }}</td>
                                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($row['harga_jual_satuan']) }}</td>
                                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($row['total_harga_jual']) }}</td>
                                            <td class="px-4 py-3 text-right font-mono text-emerald-600 font-bold">
                                                Rp {{ number_format($row['total_keuntungan']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class='bx bx-spreadsheet text-4xl text-slate-300 mb-2'></i>
                            <h3 class="font-bold text-slate-700 dark:text-slate-300">Belum ada preview data</h3>
                            <p class="text-slate-400 text-xs mt-1">Pastikan CSV sudah di-upload dengan benar.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
