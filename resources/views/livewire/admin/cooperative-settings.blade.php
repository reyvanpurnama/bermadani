<div>
    @section('title', 'Pengaturan Koperasi (White-Label)')

    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class='bx bx-slider-alt text-indigo-600 dark:text-indigo-400'></i>
                Pengaturan Koperasi & White-Label
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola identitas, branding, pejabat, rekening bank, warna tema, dan parameter keuangan koperasi.
            </p>
        </div>
    </div>

    <!-- Alert Success Flash -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <i class='bx bx-check-circle text-2xl'></i>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class='bx bx-x text-xl'></i>
            </button>
        </div>
    @endif

    <!-- Main Settings Card with Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 p-2 gap-1">
            <button wire:click="setTab('general')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'general' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-building-house text-lg'></i>
                <span>Identitas & Kontak</span>
            </button>

            <button wire:click="setTab('branding')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'branding' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-image text-lg'></i>
                <span>Branding & Logo</span>
            </button>

            <button wire:click="setTab('officers')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'officers' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-user-voice text-lg'></i>
                <span>Pejabat & RAT</span>
            </button>

            <button wire:click="setTab('bank')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'bank' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-credit-card text-lg'></i>
                <span>Rekening Bank</span>
            </button>

            <button wire:click="setTab('theme')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'theme' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-palette text-lg'></i>
                <span>Tema & Warna</span>
            </button>

            <button wire:click="setTab('finance')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'finance' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-coin-stack text-lg'></i>
                <span>Keuangan & Struk</span>
            </button>
        </div>

        <!-- Tab Content -->
        <div class="p-6 md:p-8">
            <!-- TAB 1: GENERAL / IDENTITAS -->
            @if ($activeTab === 'general')
                <form wire:submit.prevent="saveGeneral" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Koperasi (Umum)</label>
                            <input type="text" wire:model.defer="coop_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            @error('coop_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Singkat / Brand</label>
                            <input type="text" wire:model.defer="coop_short_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            @error('coop_short_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Legal Koperasi (Formal)</label>
                            <input type="text" wire:model.defer="coop_legal_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            @error('coop_legal_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Organisasi Induk / Naungan</label>
                            <input type="text" wire:model.defer="coop_parent_org" placeholder="Contoh: Universitas Muhammadiyah Bandung" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email Domain Koperasi</label>
                            <div class="flex items-center">
                                <span class="px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-300 dark:border-slate-700 text-slate-500 rounded-l-xl text-sm">@</span>
                                <input type="text" wire:model.defer="coop_email_domain" placeholder="koperasi.id" class="w-full px-4 py-2.5 rounded-r-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tagline Utama (Aplikasi)</label>
                            <input type="text" wire:model.defer="coop_tagline" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tagline Landing Page</label>
                            <input type="text" wire:model.defer="coop_landing_tagline" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Website Resmi</label>
                            <input type="text" wire:model.defer="coop_website" placeholder="www.koperasi.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">No. Telepon / WhatsApp</label>
                            <input type="text" wire:model.defer="coop_phone" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Kota Kedudukan</label>
                            <input type="text" wire:model.defer="coop_city" placeholder="Bandung" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alamat Lengkap Koperasi</label>
                            <textarea wire:model.defer="coop_address" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2">
                            <i class='bx bx-save text-lg'></i>
                            Simpan Identitas
                        </button>
                    </div>
                </form>
            @endif

            <!-- TAB 2: BRANDING & LOGO -->
            @if ($activeTab === 'branding')
                <form wire:submit.prevent="saveBranding" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Logo Utama -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Logo Utama</label>
                            <div class="h-32 rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center p-2 mb-3 bg-white dark:bg-slate-800">
                                @if ($logo_file)
                                    <img src="{{ $logo_file->temporaryUrl() }}" class="max-h-full object-contain">
                                @elseif($current_logo)
                                    <img src="{{ asset($current_logo) }}" class="max-h-full object-contain">
                                @else
                                    <span class="text-xs text-slate-400">Belum ada logo</span>
                                @endif
                            </div>
                            <input type="file" wire:model="logo_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <!-- Kop Surat -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Gambar Kop Surat (PDF)</label>
                            <div class="h-32 rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center p-2 mb-3 bg-white dark:bg-slate-800">
                                @if ($kop_surat_file)
                                    <img src="{{ $kop_surat_file->temporaryUrl() }}" class="max-h-full object-contain">
                                @elseif($current_kop)
                                    <img src="{{ asset($current_kop) }}" class="max-h-full object-contain">
                                @else
                                    <span class="text-xs text-slate-400">Belum ada Kop Surat</span>
                                @endif
                            </div>
                            <input type="file" wire:model="kop_surat_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <!-- Favicon -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Favicon Icon</label>
                            <div class="h-32 rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center p-2 mb-3 bg-white dark:bg-slate-800">
                                @if ($favicon_file)
                                    <img src="{{ $favicon_file->temporaryUrl() }}" class="max-h-full object-contain">
                                @elseif($current_favicon)
                                    <img src="{{ asset($current_favicon) }}" class="max-h-full object-contain">
                                @else
                                    <span class="text-xs text-slate-400">Belum ada favicon</span>
                                @endif
                            </div>
                            <input type="file" wire:model="favicon_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2">
                            <i class='bx bx-upload text-lg'></i>
                            Upload & Simpan Branding
                        </button>
                    </div>
                </form>
            @endif

            <!-- TAB 3: OFFICERS & RAT -->
            @if ($activeTab === 'officers')
                <form wire:submit.prevent="saveOfficers" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2 border-b pb-2 border-slate-100 dark:border-slate-800">
                                <i class='bx bx-user text-indigo-500'></i> Ketua Koperasi
                            </h3>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap & Gelar</label>
                                <input type="text" wire:model.defer="ketua_name" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Jabatan Resmi</label>
                                <input type="text" wire:model.defer="ketua_title" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2 border-b pb-2 border-slate-100 dark:border-slate-800">
                                <i class='bx bx-user text-emerald-500'></i> Bendahara / Manager Operasional
                            </h3>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap & Gelar</label>
                                <input type="text" wire:model.defer="bendahara_name" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Jabatan Resmi</label>
                                <input type="text" wire:model.defer="bendahara_title" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2 border-b pb-2 border-slate-100 dark:border-slate-800">
                                <i class='bx bx-user text-amber-500'></i> Pengawas
                            </h3>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap & Gelar</label>
                                <input type="text" wire:model.defer="pengawas_name" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Jabatan Resmi</label>
                                <input type="text" wire:model.defer="pengawas_title" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2 border-b pb-2 border-slate-100 dark:border-slate-800">
                                <i class='bx bx-file text-indigo-500'></i> Pengaturan Dokumen RAT
                            </h3>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tempat RAT Default</label>
                                <input type="text" wire:model.defer="rat_default_venue" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Prefix Format Surat RAT</label>
                                <input type="text" wire:model.defer="rat_letter_prefix" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Slogan / Tema RAT</label>
                            <input type="text" wire:model.defer="rat_slogan" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2">
                            <i class='bx bx-save text-lg'></i>
                            Simpan Pejabat & RAT
                        </button>
                    </div>
                </form>
            @endif

            <!-- TAB 4: BANK ACCOUNTS -->
            @if ($activeTab === 'bank')
                <form wire:submit.prevent="saveBank" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rekening Utama -->
                        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 bg-slate-50/30 dark:bg-slate-900/30">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2 border-b pb-2 border-slate-200 dark:border-slate-800">
                                <i class='bx bxs-bank text-indigo-600'></i> Rekening Bank Utama (Penggajian & Slip)
                            </h3>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Bank</label>
                                <input type="text" wire:model.defer="bank_primary_name" placeholder="Contoh: KB Bukopin Syariah" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nomor Rekening</label>
                                <input type="text" wire:model.defer="bank_primary_number" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Atas Nama Rekening</label>
                                <input type="text" wire:model.defer="bank_primary_holder" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                        </div>

                        <!-- Rekening Transfer Simpanan -->
                        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 bg-slate-50/30 dark:bg-slate-900/30">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2 border-b pb-2 border-slate-200 dark:border-slate-800">
                                <i class='bx bx-transfer-alt text-emerald-600'></i> Rekening Transfer Simpanan (Portal Anggota)
                            </h3>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Bank</label>
                                <input type="text" wire:model.defer="bank_transfer_name" placeholder="Contoh: Bank Mandiri" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nomor Rekening</label>
                                <input type="text" wire:model.defer="bank_transfer_number" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Atas Nama Rekening</label>
                                <input type="text" wire:model.defer="bank_transfer_holder" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2">
                            <i class='bx bx-save text-lg'></i>
                            Simpan Rekening Bank
                        </button>
                    </div>
                </form>
            @endif

            <!-- TAB 5: THEME COLORS -->
            @if ($activeTab === 'theme')
                <form wire:submit.prevent="saveTheme" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Primary Color -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 dark:text-white">Warna Primary (Brand)</label>
                                <span class="text-xs text-slate-500">Landing, login, tombol utama</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.defer="theme_primary" class="h-10 w-10 rounded-lg cursor-pointer border-0 p-0">
                                <input type="text" wire:model.defer="theme_primary" class="w-20 px-2 py-1 border text-xs rounded font-mono">
                            </div>
                        </div>

                        <!-- Admin Color -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 dark:text-white">Warna Layout Admin</label>
                                <span class="text-xs text-slate-500">Dashboard & Panel Admin</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.defer="theme_admin" class="h-10 w-10 rounded-lg cursor-pointer border-0 p-0">
                                <input type="text" wire:model.defer="theme_admin" class="w-20 px-2 py-1 border text-xs rounded font-mono">
                            </div>
                        </div>

                        <!-- Member Color -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 dark:text-white">Warna Portal Anggota</label>
                                <span class="text-xs text-slate-500">Layout Member Dashboard</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.defer="theme_member" class="h-10 w-10 rounded-lg cursor-pointer border-0 p-0">
                                <input type="text" wire:model.defer="theme_member" class="w-20 px-2 py-1 border text-xs rounded font-mono">
                            </div>
                        </div>

                        <!-- Membership Color -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 dark:text-white">Warna Simpanan</label>
                                <span class="text-xs text-slate-500">Menu Simpanan & Transfer</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.defer="theme_membership" class="h-10 w-10 rounded-lg cursor-pointer border-0 p-0">
                                <input type="text" wire:model.defer="theme_membership" class="w-20 px-2 py-1 border text-xs rounded font-mono">
                            </div>
                        </div>

                        <!-- Supplier Color -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 dark:text-white">Warna Portal Supplier</label>
                                <span class="text-xs text-slate-500">Dashboard Mitra Supplier</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.defer="theme_supplier" class="h-10 w-10 rounded-lg cursor-pointer border-0 p-0">
                                <input type="text" wire:model.defer="theme_supplier" class="w-20 px-2 py-1 border text-xs rounded font-mono">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2">
                            <i class='bx bx-palette text-lg'></i>
                            Simpan Tema & Warna
                        </button>
                    </div>
                </form>
            @endif

            <!-- TAB 6: FINANCE & RECEIPT -->
            @if ($activeTab === 'finance')
                <form wire:submit.prevent="saveFinance" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Simpanan Wajib Default (Rp)</label>
                            <input type="number" wire:model.defer="fin_simwa_default" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Biaya Admin Pinjaman (Rp)</label>
                            <input type="number" wire:model.defer="fin_loan_admin_fee" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Potongan Simwa BMT (Rp)</label>
                            <input type="number" wire:model.defer="fin_bmt_simwa_deduction" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Biaya Pendaftaran Supplier (Rp)</label>
                            <input type="number" wire:model.defer="fin_supplier_reg_fee" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Biaya Bulanan Supplier (Rp)</label>
                            <input type="number" wire:model.defer="fin_supplier_monthly_fee" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Bagi Hasil Supplier (%)</label>
                            <input type="number" step="0.01" wire:model.defer="fin_consignment_share" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Teks Ucapan Footer Struk Kasir</label>
                            <input type="text" wire:model.defer="receipt_footer_text" placeholder="Terima kasih atas kunjungan Anda!" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Teks Kebijakan Pengembalian Struk</label>
                            <input type="text" wire:model.defer="receipt_policy_text" placeholder="Barang yang sudah dibeli tidak dapat dikembalikan" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2">
                            <i class='bx bx-save text-lg'></i>
                            Simpan Parameter Keuangan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
