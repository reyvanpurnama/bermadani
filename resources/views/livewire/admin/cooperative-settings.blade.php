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

            <button wire:click="setTab('backup')" 
                class="px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'backup' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class='bx bx-data text-lg'></i>
                <span>Backup Database</span>
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

            <!-- TAB 7: BACKUP DATABASE -->
            @if ($activeTab === 'backup')
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class='bx bx-data text-indigo-600 text-xl'></i>
                                Backup & Export Database MySQL
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Hasilkan file dump SQL murni yang kompatibel penuh dengan phpMyAdmin & MySQL Client.
                            </p>
                        </div>
                        <button wire:click="createBackup" wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-sm transition-all duration-200 flex items-center gap-2 shrink-0">
                            <i class='bx bx-plus-circle text-lg' wire:loading.remove wire:target="createBackup"></i>
                            <i class='bx bx-loader-alt animate-spin text-lg' wire:loading wire:target="createBackup"></i>
                            <span>Buat Backup SQL Baru</span>
                        </button>
                    </div>

                    <!-- Upload & Restore SQL Dump Form -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 space-y-3">
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white flex items-center gap-2">
                            <i class='bx bx-upload text-blue-500 text-lg'></i>
                            Import & Restore Database (.sql Dump)
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Upload file <code>.sql</code> dari hasil download server Production atau lokal untuk langsung di-restore ke database ini.
                        </p>

                        <form wire:submit.prevent="restoreUploadedBackup" class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                            <input type="file" wire:model="upload_sql_file" accept=".sql" required 
                                   class="w-full sm:flex-1 text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">

                            <button type="submit" wire:loading.attr="disabled" onclick="return confirm('PERHATIAN! Merestore database akan menimpa seluruh data yang ada saat ini. Lanjutkan?')"
                                    class="w-full sm:w-auto px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-sm transition-all flex items-center justify-center gap-2 shrink-0">
                                <i class='bx bx-import text-base' wire:loading.remove wire:target="restoreUploadedBackup"></i>
                                <i class='bx bx-loader-alt animate-spin text-base' wire:loading wire:target="restoreUploadedBackup"></i>
                                <span>Upload & Restore Database</span>
                            </button>
                        </form>
                        @error('upload_sql_file') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Backup Files Table -->
                    <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase font-semibold border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-3">Nama File Backup</th>
                                    <th class="px-4 py-3">Ukuran File</th>
                                    <th class="px-4 py-3">Waktu Dibuat</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($backups as $b)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-4 py-3 font-mono text-xs text-slate-800 dark:text-white font-medium flex items-center gap-2">
                                            <i class='bx bxs-file-doc text-indigo-500 text-lg'></i>
                                            <span>{{ $b['filename'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                            {{ $b['size_formatted'] }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                            {{ $b['created_at'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right space-x-1.5">
                                            <button wire:click="restoreBackup('{{ $b['filename'] }}')" onclick="confirm('PERHATIAN! Merestore {{ $b['filename'] }} akan menimpa data yang ada saat ini. Lanjutkan?') || event.stopImmediatePropagation()"
                                                class="px-2.5 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 font-medium text-xs transition-colors inline-flex items-center gap-1">
                                                <i class='bx bx-history text-sm'></i>
                                                <span>Restore</span>
                                            </button>

                                            <button wire:click="downloadBackup('{{ $b['filename'] }}')" 
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-medium text-xs transition-colors inline-flex items-center gap-1">
                                                <i class='bx bx-download text-sm'></i>
                                                <span>Download SQL</span>
                                            </button>

                                            <button wire:click="deleteBackup('{{ $b['filename'] }}')" onclick="confirm('Hapus file backup ini?') || event.stopImmediatePropagation()"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-medium text-xs transition-colors inline-flex items-center gap-1">
                                                <i class='bx bx-trash text-sm'></i>
                                                <span>Hapus</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs">
                                            Belum ada file backup database yang dibuat. Klik tombol di atas untuk membuat backup pertama.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
