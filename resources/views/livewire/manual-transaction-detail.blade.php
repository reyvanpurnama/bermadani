<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.manual-transaction') }}" class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                <i class='bx bx-arrow-back text-xl'></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Detail Transaksi #{{ $transaction->id }}</h1>
                <p class="text-sm text-slate-500">{{ $transaction->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran' }} Manual</p>
            </div>
        </div>
        
        {{-- Actions --}}
        <div class="flex items-center gap-2">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isDeveloper())
                <button 
                    onclick="confirmDelete()"
                    class="px-4 py-2 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-lg text-sm font-medium hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors flex items-center gap-2"
                >
                    <i class='bx bx-trash'></i> Hapus
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class='bx bx-error-circle text-xl'></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Transaction Card --}}
            <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                {{-- Type Banner --}}
                <div class="px-6 py-4 {{ $transaction->type === 'INCOME' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-900/30' : 'bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-900/30' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl {{ $transaction->type === 'INCOME' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-600' }} flex items-center justify-center">
                                <i class='bx {{ $transaction->type === 'INCOME' ? 'bx-trending-up' : 'bx-trending-down' }} text-2xl'></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold {{ $transaction->type === 'INCOME' ? 'text-emerald-600' : 'text-rose-600' }} uppercase tracking-widest">{{ $transaction->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran' }}</p>
                                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">
                                    {{ $transaction->type === 'INCOME' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </h2>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $transaction->type === 'INCOME' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400' }}">
                            {{ $transaction->category }}
                        </span>
                    </div>
                </div>

                {{-- Details --}}
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal Transaksi</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white flex items-center gap-2">
                                <i class='bx bx-calendar text-slate-400'></i>
                                {{ $transaction->transactionDate->format('d F Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dicatat Pada</p>
                            <p class="text-sm font-medium text-slate-800 dark:text-white flex items-center gap-2">
                                <i class='bx bx-time text-slate-400'></i>
                                {{ $transaction->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dicatat Oleh</p>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                {{ strtoupper(substr($transaction->user?->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $transaction->user?->name ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $transaction->user?->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($transaction->description)
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Keterangan</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 rounded-lg p-3">
                                {{ $transaction->description }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Proof Image --}}
            @if($transaction->proofFile)
                <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class='bx bx-image text-indigo-500'></i> Bukti Transaksi
                        </h3>
                    </div>
                    <div class="p-4">
                        <div 
                            onclick="showProof('{{ asset('storage/' . $transaction->proofFile) }}')" 
                            class="block group cursor-pointer"
                        >
                            <img 
                                src="{{ asset('storage/' . $transaction->proofFile) }}" 
                                alt="Bukti Transaksi" 
                                class="w-full h-auto rounded-lg border border-slate-200 dark:border-slate-600 group-hover:opacity-90 transition-opacity"
                            >
                            <p class="text-[10px] text-center text-slate-400 mt-2 group-hover:text-indigo-500 transition-colors">
                                <i class='bx bx-zoom-in'></i> Klik untuk memperbesar
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-[13px] font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class='bx bx-image text-slate-400'></i> Bukti Transaksi
                        </h3>
                    </div>
                    <div class="p-8 text-center text-slate-400">
                        <i class='bx bx-image-alt text-4xl opacity-50'></i>
                        <p class="text-xs mt-2">Tidak ada bukti</p>
                    </div>
                </div>
            @endif

            {{-- Quick Info --}}
            <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">ID Transaksi</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-white">#{{ $transaction->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tipe</span>
                        <span class="font-bold {{ $transaction->type === 'INCOME' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $transaction->type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kategori</span>
                        <span class="font-medium text-slate-800 dark:text-white">{{ $transaction->category }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">User ID</span>
                        <span class="font-mono text-slate-800 dark:text-white">{{ $transaction->userId }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-darkCard rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-trash text-3xl text-rose-600'></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Hapus Transaksi?</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Transaksi <strong>{{ $transaction->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran' }}</strong> sebesar 
                    <strong>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong> akan dihapus permanen.
                </p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Batal
                    </button>
                    <button wire:click="delete" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-rose-600 text-white rounded-lg font-medium hover:bg-rose-700 transition-colors">
                        Ya, Hapus
                    </button>
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

        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeProof();
            }
        });
    </script>
    @endpush
</div>
