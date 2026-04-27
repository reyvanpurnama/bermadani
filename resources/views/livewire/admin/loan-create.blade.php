<div>
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Input Pinjaman Baru</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Formulir untuk memasukkan pinjaman internal/eksternal anggota secara manual.</p>
        </div>
        <a href="/admin/loans" class="bg-gray-500 hover:bg-gray-600 justify-center text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
            <i class='bx bx-arrow-back'></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
        <form wire:submit.prevent="createLoan">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Member Search -->
                <div class="col-span-1 md:col-span-2 relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Anggota (Nama/No.Anggota) *</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Ketik nama anggota..." {{ \ ? 'disabled' : '' }}>
                    @error('member_id') <span class="text-red-500 text-xs mt-1">{{ \ }}</span> @enderror
                    
                    @if(count(\) > 0)
                        <div class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg">
                            <ul class="max-h-60 overflow-y-auto">
                                @foreach(\ as \)
                                    <li wire:click="selectMember({{ \->id }})" class="px-4 py-3 hover:bg-green-50 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-0">
                                        <div class="font-medium text-gray-800 dark:text-gray-200">{{ \->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ \->nomorAnggota }} - {{ \->position ?? 'Jabatan -' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(\)
                        <div class="mt-2 text-sm text-green-600 font-medium flex items-center space-x-2">
                            <i class='bx bx-check-circle text-lg'></i>
                            <span>Anggota terpilih.</span>
                            <button type="button" wire:click="\('member_id', null); \('search', '')" class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 ml-2">Ganti Anggota</button>
                        </div>
                    @endif
                </div>

                <!-- Loan Source -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sumber Pinjaman *</label>
                    <select wire:model.live="loanSource" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="BERMADANI">Koperasi Bermadani (Internal)</option>
                        <option value="BMT_ITQAN">BMT ITQAN (Eksternal)</option>
                    </select>
                    @error('loanSource') <span class="text-red-500 text-xs mt-1">{{ \ }}</span> @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plafon / Jumlah Pinjaman (Rp) *</label>
                    <input type="number" step="1000" wire:model.live.debounce.500ms="amount" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="1000000">
                    @error('amount') <span class="text-red-500 text-xs mt-1">{{ \ }}</span> @enderror
                </div>

                <!-- Tenor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tenor (Bulan) *</label>
                    <input type="number" wire:model.live.debounce.500ms="tenor" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="10">
                    @error('tenor') <span class="text-red-500 text-xs mt-1">{{ \ }}</span> @enderror
                </div>

                <!-- Interest Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Margin Bunga/Admin (%)</label>
                    <input type="number" step="0.1" wire:model.live.debounce.500ms="interestRate" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Isi 0 jika tidak ada margin.</p>
                </div>

                @if(\ === 'BMT_ITQAN')
                <!-- Simwa (BMT Only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titipan Simpanan Wajib Tipe BMT (Rp)</label>
                    <input type="number" step="1000" wire:model.live.debounce.500ms="simwa_amount" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="15000">
                </div>
                @endif

                <!-- Monthly Payment -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Angsuran per Bulan (Rp) *</label>
                    <input type="number" wire:model="monthlyPayment" class="w-full rounded-lg border-gray-300 dark:border-indigo-600 dark:bg-indigo-900/30 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-indigo-50 font-bold text-indigo-700">
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">Dihitung otomatis, tapi Anda bisa mengubahnya manual jika ada penyesuaian khusus.</p>
                    @error('monthlyPayment') <span class="text-red-500 text-xs mt-1">{{ \ }}</span> @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai Potong Gaji *</label>
                    <input type="date" wire:model="startDate" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('startDate') <span class="text-red-500 text-xs mt-1">{{ \ }}</span> @enderror
                </div>

                <!-- Purpose -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tujuan / Keperluan Pinjaman</label>
                    <input type="text" wire:model="purpose" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Misal: Biaya pendidikan anak">
                </div>

                <!-- Description -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea wire:model="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Catatan..."></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg flex items-center space-x-2 shadow-md hover:shadow-lg transition-all" wire:loading.attr="disabled">
                    <i class='bx bx-save text-xl'></i>
                    <span wire:loading.remove>Simpan & Aktifkan Pinjaman</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
