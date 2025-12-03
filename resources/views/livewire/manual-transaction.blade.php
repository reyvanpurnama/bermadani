<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Catat Keuangan</h1>
            <p class="text-sm text-slate-500">Input pemasukan & pengeluaran operasional</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class='bx bx-check-circle text-xl'></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Form Section --}}
        <div class="lg:col-span-2">
            <form wire:submit.prevent="save" class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6">
                
                {{-- Type Switcher --}}
                <div class="grid grid-cols-2 gap-4 mb-8 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                    
                    <label class="cursor-pointer group">
                        <input type="radio" wire:model.live="type" value="EXPENSE" class="peer sr-only">
                        <div class="flex items-center justify-center gap-2 py-3 rounded-lg border border-transparent peer-checked:bg-white dark:peer-checked:bg-darkCard peer-checked:border-rose-200 dark:peer-checked:border-rose-900/50 peer-checked:shadow-sm transition-all">
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 peer-checked:bg-rose-100 peer-checked:text-rose-600 flex items-center justify-center transition-colors">
                                <i class='bx bx-trending-down text-lg'></i>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest peer-checked:text-rose-600 transition-colors">Pengeluaran</p>
                                <p class="text-[12px] font-medium text-slate-600 dark:text-slate-300 peer-checked:font-bold">Operasional</p>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer group">
                        <input type="radio" wire:model.live="type" value="INCOME" class="peer sr-only">
                        <div class="flex items-center justify-center gap-2 py-3 rounded-lg border border-transparent peer-checked:bg-white dark:peer-checked:bg-darkCard peer-checked:border-emerald-200 dark:peer-checked:border-emerald-900/50 peer-checked:shadow-sm transition-all">
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 peer-checked:bg-emerald-100 peer-checked:text-emerald-600 flex items-center justify-center transition-colors">
                                <i class='bx bx-trending-up text-lg'></i>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest peer-checked:text-emerald-600 transition-colors">Pemasukan</p>
                                <p class="text-[12px] font-medium text-slate-600 dark:text-slate-300 peer-checked:font-bold">Lain-lain</p>
                            </div>
                        </div>
                    </label>

                </div>

                <div class="space-y-5">
                    
                    {{-- Amount --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">
                            Nominal (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">Rp</span>
                            <input type="number" wire:model="amount" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl pl-10 pr-4 py-3 text-lg font-bold text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-{{ $type === 'INCOME' ? 'emerald' : 'rose' }}-500/20 focus:border-{{ $type === 'INCOME' ? 'emerald' : 'rose' }}-500 transition-all placeholder-slate-300" placeholder="0">
                        </div>
                        @error('amount') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Category & Date --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Kategori</label>
                            <select wire:model="category" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white cursor-pointer">
                                @if($type === 'EXPENSE')
                                    @foreach($expenseCategories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                @else
                                    @foreach($incomeCategories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Tanggal Transaksi</label>
                            <input type="date" wire:model="transactionDate" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white">
                            @error('transactionDate') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Keterangan / Catatan</label>
                        <textarea wire:model="description" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2.5 text-[13px] outline-none focus:border-primary dark:text-white placeholder-slate-400" placeholder="Contoh: Beli lampu baru untuk gudang..."></textarea>
                        @error('description') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Proof File --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1.5">Bukti Struk / Foto (Opsional)</label>
                        
                        @if($proofFile)
                            {{-- Preview --}}
                            <div class="mb-3 relative">
                                <img src="{{ $proofFile->temporaryUrl() }}" class="w-full h-40 object-cover rounded-lg border border-slate-200 dark:border-slate-600">
                                <button type="button" wire:click="$set('proofFile', null)" class="absolute top-2 right-2 bg-rose-500 hover:bg-rose-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg">
                                    <i class='bx bx-x text-lg'></i>
                                </button>
                            </div>
                        @endif

                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 dark:hover:bg-slate-800 dark:bg-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:hover:border-slate-500 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class='bx bx-cloud-upload text-2xl text-slate-400 mb-2'></i>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        @if($proofFile)
                                            <span class="font-semibold text-emerald-600">{{ $proofFile->getClientOriginalName() }}</span>
                                        @else
                                            <span class="font-semibold">Klik upload</span> atau drag file
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-1">PNG, JPG (Max 2MB)</p>
                                </div>
                                <input id="dropzone-file" type="file" wire:model="proofFile" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        @error('proofFile') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="proofFile" class="text-xs text-indigo-500 mt-1 flex items-center gap-1">
                            <i class='bx bx-loader-alt bx-spin'></i> Uploading...
                        </div>
                    </div>

                </div>

                {{-- Submit Button --}}
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="w-full bg-{{ $type === 'INCOME' ? 'emerald' : 'rose' }}-600 hover:bg-{{ $type === 'INCOME' ? 'emerald' : 'rose' }}-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-{{ $type === 'INCOME' ? 'emerald' : 'rose' }}-500/20 transition-all text-sm flex items-center justify-center gap-2">
                        <i class='bx bx-save text-lg'></i> Simpan {{ $type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran' }}
                    </button>
                </div>

            </form>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Petty Cash --}}
            <div class="bg-indigo-600 p-5 rounded-xl shadow-lg shadow-indigo-500/20 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-1">Saldo Kas Kecil (Petty Cash)</p>
                    <h3 class="text-2xl font-bold">Rp {{ number_format($this->pettyCash, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-indigo-100 mt-1">Untuk operasional harian</p>
                </div>
                <i class='bx bx-wallet-alt absolute -bottom-4 -right-4 text-6xl text-white opacity-10'></i>
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden flex-1">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-[13px] font-bold text-slate-800 dark:text-white">Riwayat Input</h3>
                    <a href="{{ route('admin.manual-transaction.history') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                        Lihat Semua
                    </a>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[400px] overflow-y-auto">
                    @forelse($this->recentTransactions as $trx)
                        <a href="{{ route('admin.manual-transaction.detail', $trx->id) }}" class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer">
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[12px] font-bold text-slate-800 dark:text-white">{{ $trx->category }}</span>
                                    @if($trx->proofFile)
                                        <button 
                                            type="button" 
                                            onclick="showProof('{{ asset('storage/' . $trx->proofFile) }}')"
                                            class="text-[10px] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors flex items-center gap-1"
                                            title="Lihat bukti"
                                        >
                                            <i class='bx bx-image-alt'></i>
                                        </button>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400">{{ $trx->transactionDate->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-[10px] bg-slate-100 dark:bg-slate-700 text-slate-500 px-1.5 py-0.5 rounded truncate max-w-[150px]">{{ Str::limit($trx->description ?? '-', 20) }}</span>
                                <span class="text-[12px] font-bold {{ $trx->type === 'INCOME' ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $trx->type === 'INCOME' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 text-[9px] text-slate-400">
                                <i class='bx bx-user'></i>
                                <span>oleh {{ $trx->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <i class='bx bx-receipt text-4xl'></i>
                            <p class="text-sm mt-2">Belum ada riwayat</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    {{-- Modal Preview Bukti --}}
    <div id="proofModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4" onclick="closeProof()">
        <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
            <button onclick="closeProof()" class="absolute -top-10 right-0 text-white hover:text-rose-400 transition-colors">
                <i class='bx bx-x text-4xl'></i>
            </button>
            <img id="proofImage" src="" class="w-full h-auto max-h-[90vh] object-contain rounded-xl shadow-2xl">
        </div>
    </div>

    @push('scripts')
    <script>
        function showProof(url) {
            document.getElementById('proofImage').src = url;
            document.getElementById('proofModal').classList.remove('hidden');
            document.getElementById('proofModal').classList.add('flex');
        }

        function closeProof() {
            document.getElementById('proofModal').classList.add('hidden');
            document.getElementById('proofModal').classList.remove('flex');
        }

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeProof();
        });
    </script>
    @endpush
</div>
