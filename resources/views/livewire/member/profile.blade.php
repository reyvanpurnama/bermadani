<div x-data="{ 
    activeModal: null, 
    openModal(id) { this.activeModal = id; document.body.classList.add('overflow-hidden'); },
    closeModal() { this.activeModal = null; document.body.classList.remove('overflow-hidden'); }
}">
    @section('page-title', 'Profil Saya')

    <div class="max-w-xl mx-auto pb-24 lg:pb-0">
        {{-- Header Profile --}}
        <div
            class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 mb-4 relative overflow-hidden text-center group">
            <div
                class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-primary/10 to-blue-500/10 dark:from-primary/20 dark:to-blue-500/20">
            </div>

            <div class="relative z-10 pt-8">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-primary to-blue-600 rounded-full mx-auto p-1 shadow-lg mb-3 relative group-hover:scale-105 transition-transform duration-300">
                    <div
                        class="w-full h-full bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-4xl font-bold">
                        <span class="text-transparent bg-clip-text bg-gradient-to-br from-primary to-blue-600">
                            {{ substr($member->name ?? 'M', 0, 1) }}
                        </span>
                    </div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-amber-400 rounded-full border-2 border-white dark:border-slate-800 flex items-center justify-center shadow-sm"
                        title="Tier Member">
                        <i class='bx bxs-medal text-white text-sm'></i>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ $member->name }}</h2>
                <div class="flex items-center justify-center gap-2 mb-4">
                    <span
                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 tracking-wider uppercase">{{ $member->nomorAnggota }}</span>
                    <span class="text-slate-300 dark:text-slate-600">•</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $member->email }}</span>
                </div>

                <div class="flex justify-center gap-2">
                    <div
                        class="px-4 py-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Tier</div>
                        <div class="font-bold text-primary">{{ $member->tier ?? 'Bronze' }}</div>
                    </div>
                    <div
                        class="px-4 py-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">Poin</div>
                        <div class="font-bold text-amber-500">{{ number_format($member->points ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Menu List (Settings) --}}
        <div class="space-y-4">
            {{-- Account Group --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-4 py-3 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akun & Data Diri</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    <button @click="openModal('profile')"
                        class="w-full flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                <i class='bx bxs-user-detail'></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Informasi Personal</h4>
                                <p class="text-xs text-slate-400">Nama, Email, Telepon, Alamat</p>
                            </div>
                        </div>
                        <i class='bx bx-chevron-right text-slate-300 text-xl'></i>
                    </button>

                    <div class="w-full flex items-center justify-between p-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 flex items-center justify-center text-xl">
                                <i class='bx bxs-briefcase-alt-2'></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Unit Kerja</h4>
                                <p class="text-xs text-slate-400">{{ $member->unitKerja ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Finance Group --}}
            @if($member->isMemberKoperasi)
                <div
                    class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div
                        class="px-4 py-3 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengaturan Keuangan</h3>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        <button @click="openModal('simpanan')"
                            class="w-full flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                    <i class='bx bxs-wallet-alt'></i>
                                </div>
                                <div class="text-left">
                                    <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Konfigurasi Simpanan
                                    </h4>
                                    <p class="text-xs text-slate-400">Atur autodebet Wajib & Sukarela</p>
                                </div>
                            </div>
                            <i class='bx bx-chevron-right text-slate-300 text-xl'></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Security Group --}}
            <div
                class="bg-white dark:bg-darkCard rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div
                    class="px-4 py-3 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Keamanan</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    <button @click="openModal('password')"
                        class="w-full flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                <i class='bx bxs-lock-alt'></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Ubah Password</h4>
                                <p class="text-xs text-slate-400">Amankan akun anda secara berkala</p>
                            </div>
                        </div>
                        <i class='bx bx-chevron-right text-slate-300 text-xl'></i>
                    </button>
                </div>
            </div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 p-4 bg-white dark:bg-darkCard text-rose-600 dark:text-rose-400 font-bold text-sm rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-colors">
                    <i class='bx bx-log-out text-xl'></i> Keluar Aplikasi
                </button>
            </form>

            <div class="text-center py-6">
                <p class="text-[10px] text-slate-400">BERMADANI v1.0.0 • Koperasi UMB</p>
            </div>
        </div>
    </div>

    {{-- MODALS (Slide-over for Mobile, Modal for Desktop) --}}

    {{-- 1. Profile Edit Modal --}}
    <div x-show="activeModal === 'profile'" x-cloak
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
        <div
            class="bg-white dark:bg-slate-900 w-full max-w-lg sm:rounded-2xl rounded-t-3xl p-6 relative z-10 max-h-[90vh] overflow-y-auto animate-[slideUp_0.3s_ease-out]">
            <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-6 sm:hidden"></div>

            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Edit Profil</h3>

            <form wire:submit="updateProfile">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama
                            Lengkap</label>
                        <input type="text" value="{{ $member->name }}" readonly
                            class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl cursor-not-allowed border-none text-sm">
                        <p class="text-[10px] text-slate-400 mt-1">Hubungi admin untuk ubah nama</p>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                        <input type="text" value="{{ $member->email }}" readonly
                            class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl cursor-not-allowed border-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">No.
                            Telepon</label>
                        <input type="text" wire:model="phone"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary border-slate-200 dark:border-slate-700 text-sm">
                        @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat</label>
                        <textarea wire:model="address" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary border-slate-200 dark:border-slate-700 text-sm"></textarea>
                        @error('address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Unit
                            Kerja</label>
                        <input type="text" wire:model="unitKerja"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary border-slate-200 dark:border-slate-700 text-sm">
                        @error('unitKerja') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex gap-3">
                    <button type="button" @click="closeModal()"
                        class="flex-1 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-primary text-white font-bold rounded-xl text-sm shadow-lg shadow-blue-500/20">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Simpanan Config Modal --}}
    @if($member->isMemberKoperasi)
        <div x-show="activeModal === 'simpanan'" x-cloak
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
            <div
                class="bg-white dark:bg-slate-900 w-full max-w-lg sm:rounded-2xl rounded-t-3xl p-6 relative z-10 max-h-[90vh] overflow-y-auto animate-[slideUp_0.3s_ease-out]">
                <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-6 sm:hidden"></div>

                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Konfigurasi Simpanan</h3>

                <form wire:submit="updateSimpananSettings">
                    <div class="space-y-6">
                        {{-- Simpanan Wajib --}}
                        <div
                            class="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-800/20">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class='bx bxs-calendar'></i>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-blue-200 text-sm">Simpanan Wajib</h4>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nominal
                                        Bulanan</label>
                                    <div class="relative" x-data="{
                                            rawValue: {{ $monthly_simpanan_wajib ?? 0 }},
                                            formatted: '',
                                            init() {
                                                this.formatted = this.formatNumber(this.rawValue);
                                            },
                                            formatNumber(num) {
                                                return new Intl.NumberFormat('id-ID').format(num);
                                            },
                                            parseNumber(str) {
                                                return parseInt(str.replace(/\./g, '')) || 0;
                                            },
                                            updateValue(e) {
                                                this.rawValue = this.parseNumber(e.target.value);
                                                this.formatted = this.formatNumber(this.rawValue);
                                                $wire.set('monthly_simpanan_wajib', this.rawValue);
                                            }
                                        }">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">Rp</span>
                                        <input type="text" x-model="formatted" @input="updateValue($event)"
                                            @blur="updateValue($event)" @focus="$el.select()" placeholder="0"
                                            class="w-full pl-8 pr-3 py-2 bg-white dark:bg-slate-800 border-none rounded-lg text-sm font-bold text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-0">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Metode
                                        Bayar</label>
                                    <select wire:model="simwa_payment_method"
                                        class="w-full py-2 px-3 bg-white dark:bg-slate-800 border-none rounded-lg text-sm text-slate-700 dark:text-slate-300">
                                        <option value="SALARY_DEDUCTION">Potong Gaji</option>
                                        <option value="AUTO_DEBIT">Auto Debit (Saldo Sukarela)</option>
                                        <option value="MANUAL">Transfer Manual</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Simpanan Sukarela --}}
                        <div
                            class="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-800/20">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i class='bx bxs-bank'></i>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-emerald-200 text-sm">Simpanan Sukarela (Auto)
                                </h4>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nominal
                                        Auto-Debet Bulanan</label>
                                    <div class="relative" x-data="{
                                                rawValue: {{ $monthly_sukarela_amount ?? 0 }},
                                                formatted: '',
                                                init() {
                                                    this.formatted = this.formatNumber(this.rawValue);
                                                },
                                                formatNumber(num) {
                                                    return new Intl.NumberFormat('id-ID').format(num);
                                                },
                                                parseNumber(str) {
                                                    return parseInt(str.replace(/\./g, '')) || 0;
                                                },
                                                updateValue(e) {
                                                    this.rawValue = this.parseNumber(e.target.value);
                                                    this.formatted = this.formatNumber(this.rawValue);
                                                    $wire.set('monthly_sukarela_amount', this.rawValue);
                                                }
                                            }">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">Rp</span>
                                        <input type="text" x-model="formatted" @input="updateValue($event)"
                                            @blur="updateValue($event)" @focus="$el.select()" placeholder="0"
                                            class="w-full pl-8 pr-3 py-2 bg-white dark:bg-slate-800 border-none rounded-lg text-sm font-bold text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-0">
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan (0) jika tidak ingin menabung
                                        otomatis.</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sumber
                                        Dana</label>
                                    <select wire:model="sukarela_payment_method"
                                        class="w-full py-2 px-3 bg-white dark:bg-slate-800 border-none rounded-lg text-sm text-slate-700 dark:text-slate-300">
                                        <option value="SALARY_DEDUCTION">Potong Gaji</option>
                                        <option value="MANUAL">Transfer Manual</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex gap-3">
                        <button type="button" @click="closeModal()"
                            class="flex-1 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm">Batal</button>
                        <button type="submit"
                            class="flex-1 py-3 bg-primary text-white font-bold rounded-xl text-sm shadow-lg shadow-blue-500/20">Simpan
                            Konfigurasi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- 3. Password Modal --}}
    <div x-show="activeModal === 'password'" x-cloak
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
        <div
            class="bg-white dark:bg-slate-900 w-full max-w-lg sm:rounded-2xl rounded-t-3xl p-6 relative z-10 max-h-[90vh] overflow-y-auto animate-[slideUp_0.3s_ease-out]">
            <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-6 sm:hidden"></div>

            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Ubah Password</h3>

            <form wire:submit="updatePassword">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Password
                            Lama</label>
                        <input type="password" wire:model="currentPassword"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary border-slate-200 dark:border-slate-700 text-sm">
                        @error('currentPassword') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Password
                            Baru</label>
                        <input type="password" wire:model="newPassword"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary border-slate-200 dark:border-slate-700 text-sm">
                        @error('newPassword') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi
                            Password</label>
                        <input type="password" wire:model="newPassword_confirmation"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary border-slate-200 dark:border-slate-700 text-sm">
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex gap-3">
                    <button type="button" @click="closeModal()"
                        class="flex-1 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-amber-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-amber-500/20">Ubah
                        Password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast Notifications --}}
    @if(session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000); closeModal()"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur text-white px-6 py-3 rounded-full shadow-xl flex items-center gap-2 z-[60] min-w-[300px] justify-center">
            <i class='bx bx-check-circle text-emerald-400 text-xl'></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
</div>

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }
    </style>
@endpush