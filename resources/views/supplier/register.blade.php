<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra Supplier - {{ config('cooperative.short_name') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"SF Pro Display"', '"Plus Jakarta Sans"', 'Inter', 'sans-serif'] },
                    colors: {
                        primary: '#155A6B', 
                        secondary: '#f5f5f7', 
                        darkBg: '#09090b',
                        darkCard: '#18181b'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", "Inter", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .step-content { display: none; animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .step-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        .no-spinner::-webkit-inner-spin-button, 
        .no-spinner::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        .no-spinner { 
            -moz-appearance: textfield; 
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(228, 228, 231, 0.8);
        }

        .dark .glass-card {
            background: rgba(24, 24, 27, 0.85);
            border-color: rgba(39, 39, 42, 0.8);
        }
    </style>
</head>
<body class="bg-[#f5f5f7] dark:bg-[#09090b] text-zinc-900 dark:text-zinc-100 font-sans selection:bg-[#155A6B] selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- Left Banner Sidebar -->
        <div class="lg:w-5/12 bg-gradient-to-br from-[#155A6B] via-[#166072] to-[#1a6b80] relative p-8 lg:p-14 text-white flex flex-col justify-between min-h-[320px] lg:min-h-screen lg:fixed lg:left-0 lg:top-0 lg:h-full z-10 shadow-2xl">
            
            <div class="absolute inset-0 bg-[url('/images/hero-landscape.jpeg')] bg-cover bg-center opacity-15 pointer-events-none mix-blend-overlay"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-teal-400 opacity-20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8 lg:mb-14">
                    <a href="{{ route('home') }}" class="w-10 h-10 bg-white/15 backdrop-blur-xl rounded-2xl border border-white/20 flex items-center justify-center hover:bg-white/25 transition-all shadow-lg">
                        <img src="{{ asset('images/logo-koperasi.png') }}" class="w-6 h-6 object-contain" alt="Logo">
                    </a>
                    <div>
                        <span class="text-lg font-extrabold tracking-tight block leading-tight">{{ config('cooperative.short_name', 'Bermadani') }}</span>
                        <span class="text-xs text-teal-200 font-medium">Mitra Supplier UMKM</span>
                    </div>
                </div>
                
                <h1 class="text-3xl lg:text-5xl font-black leading-[1.1] mb-4 tracking-tight">
                    Dapatkan Ribuan Pembeli di <span class="text-amber-300">UMBandung</span>.
                </h1>
                <p class="text-teal-100 text-sm lg:text-base leading-relaxed max-w-md font-medium">Titip jual produk usaha kamu di Bermadani Mart. Pantau omzet harian & stok barang secara transparan langsung dari dashboard.</p>
            </div>

            <!-- Desktop Step Indicator -->
            <div class="hidden lg:block relative z-10 my-8">
                <div class="space-y-6">
                    <div class="flex items-center gap-4 transition-all duration-300" id="ind-step-1">
                        <div class="w-9 h-9 rounded-full bg-white text-[#155A6B] flex items-center justify-center font-extrabold shadow-lg ring-4 ring-white/30 text-sm">1</div>
                        <div><h6 class="font-bold text-sm">Data Pemilik</h6><p class="text-xs text-teal-200">Identitas diri & kontak</p></div>
                    </div>
                    <div class="w-0.5 h-6 bg-white/20 ml-4"></div>
                    
                    <div class="flex items-center gap-4 opacity-50 transition-all duration-300" id="ind-step-2">
                        <div class="w-9 h-9 rounded-full bg-white/20 text-white flex items-center justify-center font-extrabold text-sm">2</div>
                        <div><h6 class="font-bold text-sm">Informasi Usaha</h6><p class="text-xs text-teal-200">Detail & kategori jualan</p></div>
                    </div>
                    <div class="w-0.5 h-6 bg-white/20 ml-4"></div>

                    <div class="flex items-center gap-4 opacity-50 transition-all duration-300" id="ind-step-3">
                        <div class="w-9 h-9 rounded-full bg-white/20 text-white flex items-center justify-center font-extrabold text-sm">3</div>
                        <div><h6 class="font-bold text-sm">Akun & Pembayaran</h6><p class="text-xs text-teal-200">Alamat, login & verifikasi</p></div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-xs text-teal-200/80 hidden lg:block font-medium">
                © {{ date('Y') }} {{ config('cooperative.legal_name', 'Koperasi Bermadani UMBandung') }}.
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="w-full lg:w-7/12 lg:ml-[41.666667%] min-h-screen flex flex-col justify-center p-4 sm:p-8 lg:p-14">
            
            <div class="max-w-xl mx-auto w-full">
                
                <!-- Mobile Progress Bar -->
                <div class="lg:hidden mb-8">
                    <div class="flex justify-between text-xs font-bold text-zinc-500 mb-2">
                        <span id="mob-text-step">Langkah 1 dari 3</span>
                        <span id="mob-text-name">Data Pemilik</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-2 rounded-full overflow-hidden">
                        <div id="mob-progress" class="bg-[#155A6B] h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-5 py-4 rounded-2xl text-sm font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 px-5 py-4 rounded-2xl text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="wizardForm" method="POST" action="{{ route('supplier.register.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- STEP 1: INFORMASI PEMILIK -->
                    <div class="step-content active" id="step1">
                        <div class="glass-card p-6 sm:p-10 rounded-3xl shadow-2xl space-y-6">
                            <div>
                                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Informasi Pemilik Usaha</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Lengkapi data diri pemilik usaha untuk verifikasi akun mitra.</p>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2 uppercase tracking-wider">Status Kemitraan</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="student" class="peer sr-only" checked onchange="toggleIdentity('student')">
                                            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-2xl peer-checked:border-[#155A6B] peer-checked:bg-[#155A6B]/5 dark:peer-checked:bg-[#155A6B]/20 text-center transition-all">
                                                <i class='bx bxs-graduation text-2xl text-zinc-400 peer-checked:text-[#155A6B] dark:peer-checked:text-teal-400 mb-1'></i>
                                                <p class="text-xs font-extrabold text-zinc-700 dark:text-zinc-200 peer-checked:text-[#155A6B] dark:peer-checked:text-teal-400">Mahasiswa / Dosen</p>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="public" class="peer sr-only" onchange="toggleIdentity('public')">
                                            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-2xl peer-checked:border-[#155A6B] peer-checked:bg-[#155A6B]/5 dark:peer-checked:bg-[#155A6B]/20 text-center transition-all">
                                                <i class='bx bxs-store text-2xl text-zinc-400 peer-checked:text-[#155A6B] dark:peer-checked:text-teal-400 mb-1'></i>
                                                <p class="text-xs font-extrabold text-zinc-700 dark:text-zinc-200 peer-checked:text-[#155A6B] dark:peer-checked:text-teal-400">Umum / External UMKM</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Nama Lengkap</label>
                                    <input type="text" name="ownerName" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="Nama sesuai KTP">
                                </div>

                                <div>
                                    <label id="id-label" class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">NIM / NIP</label>
                                    <input type="text" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="Nomor Identitas">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Nomor WhatsApp (Aktif)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-4 flex items-center text-zinc-500 text-sm font-bold">+62</span>
                                        <input type="text" 
                                               name="phone"
                                               required
                                               class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl pl-14 pr-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all no-spinner" 
                                               placeholder="812 3456 7890"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="button" onclick="nextStep(2)" class="bg-[#155A6B] hover:bg-[#1a6b80] text-white font-extrabold py-3.5 px-8 rounded-full shadow-lg shadow-[#155A6B]/25 transition-all flex items-center gap-2 text-sm">
                                    <span>Lanjut ke Informasi Usaha</span> <i class='bx bx-right-arrow-alt text-xl'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: INFORMASI USAHA -->
                    <div class="step-content" id="step2">
                        <div class="glass-card p-6 sm:p-10 rounded-3xl shadow-2xl space-y-6">
                            <div>
                                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Informasi Usaha & Produk</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Jelaskan brand dan kategori produk yang akan dijual.</p>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Nama Brand / Toko</label>
                                    <input type="text" name="businessName" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="Contoh: Keripik Mas Dani">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Kategori Utama Produk</label>
                                    <select name="productCategory" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none cursor-pointer transition-all">
                                        <option>Makanan Ringan (Snack)</option>
                                        <option>Minuman & Kopi</option>
                                        <option>Fashion & Aksesoris</option>
                                        <option>ATK & Perlengkapan Kuliah</option>
                                        <option>Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Deskripsi Singkat Produk</label>
                                    <textarea name="description" rows="4" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="Jelaskan produk apa yang ingin Anda titip jual..."></textarea>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between items-center">
                                <button type="button" onclick="nextStep(1)" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white font-bold py-3 px-5 transition-colors text-sm">
                                    Kembali
                                </button>
                                <button type="button" onclick="nextStep(3)" class="bg-[#155A6B] hover:bg-[#1a6b80] text-white font-extrabold py-3.5 px-8 rounded-full shadow-lg shadow-[#155A6B]/25 transition-all flex items-center gap-2 text-sm">
                                    <span>Lanjut ke Akun & Pembayaran</span> <i class='bx bx-right-arrow-alt text-xl'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: AKUN & PEMBAYARAN -->
                    <div class="step-content" id="step3">
                        <div class="glass-card p-6 sm:p-10 rounded-3xl shadow-2xl space-y-6">
                            <div>
                                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Akun & Biaya Pendaftaran</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-medium">Buat password login dan unggah bukti pembayaran pendaftaran.</p>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Alamat Lengkap Usaha</label>
                                    <textarea name="address" rows="2" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="Alamat lengkap lokasi usaha/rumah"></textarea>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Alamat Email</label>
                                        <input type="email" name="email" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="email@anda.com">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Password Login (min 8 karakter)</label>
                                        <input type="password" name="password" required minlength="8" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none transition-all" placeholder="••••••••">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Konfirmasi Password</label>
                                    <div class="relative">
                                        <input type="password" id="passwordConfirmation" name="password_confirmation" required minlength="8" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#155A6B] outline-none pr-10 transition-all" placeholder="Ketik ulang password" oninput="validatePasswordMatch()">
                                        <div id="passwordMatchIcon" class="absolute inset-y-0 right-3 flex items-center pointer-events-none"></div>
                                    </div>
                                    <div id="passwordMatchMessage" class="text-xs mt-1 hidden"></div>
                                </div>

                                <!-- Payment Box -->
                                <div class="p-5 bg-teal-50/50 dark:bg-teal-950/20 rounded-2xl border border-teal-200/80 dark:border-teal-800/40">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-[#155A6B] text-white flex items-center justify-center shrink-0">
                                            <i class='bx bx-credit-card text-xl'></i>
                                        </div>
                                        <div class="w-full">
                                            <h4 class="text-xs font-extrabold text-[#155A6B] dark:text-teal-300">Biaya Administrasi Pendaftaran Mitra</h4>
                                            <p class="text-lg font-black text-zinc-900 dark:text-white mt-0.5 mb-2">Rp 25.000</p>
                                            
                                            <div class="mb-3">
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-2 font-medium">Scan QR Code QRIS di bawah ini:</p>
                                                @php
                                                    $qrPath = public_path('assets/payment-qr/registration-fee.png');
                                                    $qrExists = file_exists($qrPath);
                                                @endphp
                                                
                                                @if($qrExists)
                                                    <div class="bg-white dark:bg-zinc-800 p-3 rounded-2xl inline-block shadow-md">
                                                        <img src="{{ asset('assets/payment-qr/registration-fee.png') }}" alt="QR Code Pembayaran" class="w-36 h-36 object-contain">
                                                    </div>
                                                @else
                                                    <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl text-center border border-zinc-200 dark:border-zinc-700">
                                                        <i class='bx bx-qr text-4xl text-zinc-400 mb-1'></i>
                                                        <p class="text-xs font-medium text-zinc-500">QR Code Pembayaran QRIS Koperasi</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Payment Proof -->
                                <div>
                                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Unggah Bukti Transfer / QRIS <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <div id="paymentProofPreview" class="hidden mb-3">
                                            <img id="paymentProofImg" src="" alt="Bukti Pembayaran" class="w-full h-44 object-cover rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-md">
                                            <button type="button" onclick="document.getElementById('paymentProof').value = ''; document.getElementById('paymentProofPreview').classList.add('hidden');" class="mt-2 text-xs text-rose-600 hover:text-rose-700 dark:text-rose-400 font-bold flex items-center gap-1">
                                                <i class='bx bx-trash'></i> Hapus Gambar
                                            </button>
                                        </div>
                                        <label for="paymentProof" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl cursor-pointer bg-white/50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <i class='bx bx-cloud-upload text-3xl text-[#155A6B] dark:text-teal-400 mb-1'></i>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                                    <span class="font-bold text-[#155A6B] dark:text-teal-400">Klik untuk upload</span> atau drag file
                                                </p>
                                                <p class="text-[10px] text-zinc-400 mt-1">Format PNG, JPG (Maks. 2MB)</p>
                                            </div>
                                            <input id="paymentProof" type="file" name="registrationPaymentProof" accept="image/*" class="hidden" onchange="previewPaymentProof(this)">
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2 pt-2">
                                    <input type="checkbox" id="terms" required class="mt-1 w-4 h-4 text-[#155A6B] border-zinc-300 rounded focus:ring-[#155A6B]">
                                    <label for="terms" class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">
                                        Saya setuju dengan <a href="#" class="text-[#155A6B] dark:text-teal-400 font-bold hover:underline">Syarat & Ketentuan Kemitraan</a>. Fee konsinyasi/bagi hasil berlaku sesuai kesepakatan produk.
                                    </label>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between items-center">
                                <button type="button" onclick="nextStep(2)" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white font-bold py-3 px-5 transition-colors text-sm">
                                    Kembali
                                </button>
                                <button type="submit" class="bg-[#155A6B] hover:bg-[#1a6b80] text-white font-extrabold py-3.5 px-9 rounded-full shadow-xl shadow-[#155A6B]/30 transition-all hover:scale-105 flex items-center gap-2 text-sm">
                                    <span>Kirim Pendaftaran</span> <i class='bx bx-check-circle text-xl'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-xs text-zinc-500 font-semibold">Sudah mendaftar sebagai mitra? <a href="{{ route('login') }}" class="text-[#155A6B] dark:text-teal-400 font-extrabold hover:underline">Masuk ke Portal di sini</a></p>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleIdentity(type) {
            const label = document.getElementById('id-label');
            label.innerText = type === 'student' ? 'NIM / NIP' : 'Nomor KTP (NIK)';
        }

        function previewPaymentProof(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('paymentProofImg').src = e.target.result;
                    document.getElementById('paymentProofPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function validatePasswordMatch() {
            const password = document.querySelector('input[name="password"]').value;
            const passwordConfirmation = document.getElementById('passwordConfirmation').value;
            const icon = document.getElementById('passwordMatchIcon');
            const message = document.getElementById('passwordMatchMessage');
            
            if (!passwordConfirmation) {
                icon.innerHTML = '';
                message.classList.add('hidden');
                return;
            }
            
            if (password === passwordConfirmation && password.length >= 8) {
                icon.innerHTML = '<i class="bx bx-check-circle text-lg text-emerald-500"></i>';
                message.textContent = 'Password cocok ✓';
                message.className = 'text-xs mt-1 text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-semibold';
                message.classList.remove('hidden');
            } else if (password !== passwordConfirmation && passwordConfirmation) {
                icon.innerHTML = '<i class="bx bx-x-circle text-lg text-rose-500"></i>';
                message.textContent = 'Password tidak cocok';
                message.className = 'text-xs mt-1 text-rose-600 dark:text-rose-400 flex items-center gap-1 font-semibold';
                message.classList.remove('hidden');
            } else {
                icon.innerHTML = '';
                message.classList.add('hidden');
            }
        }

        function nextStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');

            for(let i=1; i<=3; i++) {
                const el = document.getElementById('ind-step-'+i);
                const circle = el.querySelector('div');
                
                if(i === step) {
                    el.classList.remove('opacity-50');
                    circle.classList.remove('bg-white/20', 'text-white');
                    circle.classList.add('bg-white', 'text-[#155A6B]', 'ring-4', 'ring-white/30');
                } else if(i < step) {
                    el.classList.remove('opacity-50');
                    circle.classList.remove('bg-white', 'text-[#155A6B]', 'ring-4', 'ring-white/30');
                    circle.classList.add('bg-teal-400', 'text-[#155A6B]');
                    circle.innerHTML = "<i class='bx bx-check text-lg'></i>";
                } else {
                    el.classList.add('opacity-50');
                    circle.classList.remove('bg-white', 'text-[#155A6B]', 'ring-4', 'ring-white/30', 'bg-teal-400');
                    circle.classList.add('bg-white/20', 'text-white');
                    circle.innerText = i;
                }
            }

            const mobTextStep = document.getElementById('mob-text-step');
            const mobTextName = document.getElementById('mob-text-name');
            const mobProgress = document.getElementById('mob-progress');
            
            mobTextStep.innerText = `Langkah ${step} dari 3`;
            if(step === 1) { mobTextName.innerText = 'Data Pemilik'; mobProgress.style.width = '33%'; }
            if(step === 2) { mobTextName.innerText = 'Info Produk'; mobProgress.style.width = '66%'; }
            if(step === 3) { mobTextName.innerText = 'Akun & Pembayaran'; mobProgress.style.width = '100%'; }
        }
    </script>

</body>
</html>