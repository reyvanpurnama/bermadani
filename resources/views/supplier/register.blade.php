<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra Supplier - Bermadani</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: '#0F52BA', 
                        secondary: '#F8FAFC', 
                        darkBg: '#0f172a',
                        darkCard: '#1e293b'
                    }
                }
            }
        }
    </script>
    <style>
        /* Animasi Slide buat Step */
        .step-content { display: none; animation: fadeIn 0.4s ease-out; }
        .step-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        /* HILANGKAN SPINNER INPUT NUMBER */
        .no-spinner::-webkit-inner-spin-button, 
        .no-spinner::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        .no-spinner { 
            -moz-appearance: textfield; 
        }
    </style>
</head>
<body class="bg-white dark:bg-darkBg font-sans">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <div class="lg:w-5/12 bg-primary relative p-8 lg:p-12 text-white flex flex-col justify-between min-h-[300px] lg:min-h-screen lg:fixed lg:left-0 lg:top-0 lg:h-full z-10">
            
            <div class="absolute top-0 left-0 w-full h-full opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-6 lg:mb-12">
                    <a href="{{ route('home') }}" class="w-8 h-8 bg-white/20 backdrop-blur-md rounded-lg flex items-center justify-center hover:bg-white/30 transition-colors">
                        <i class='bx bxs-cube-alt text-xl'></i>
                    </a>
                    <span class="text-xl font-bold tracking-tight">Bermadani<span class="font-light">Partner</span></span>
                </div>
                
                <h1 class="text-3xl lg:text-4xl font-extrabold leading-tight mb-4">
                    Jadi Mitra <br> <span class="text-yellow-400">Supplier Kami</span>.
                </h1>
                <p class="text-blue-100 text-base lg:text-lg">Titip produk Anda di minimarket kami. Pantau penjualan secara real-time.</p>
            </div>

            <div class="hidden lg:block relative z-10 my-8">
                <div class="space-y-6">
                    <div class="flex items-center gap-4 opacity-100 transition-opacity" id="ind-step-1">
                        <div class="w-8 h-8 rounded-full bg-white text-primary flex items-center justify-center font-bold ring-4 ring-blue-400/30">1</div>
                        <div><h6 class="font-bold">Data Pemilik</h6><p class="text-xs text-blue-200">Identitas diri</p></div>
                    </div>
                    <div class="w-0.5 h-6 bg-white/20 ml-4"></div>
                    
                    <div class="flex items-center gap-4 opacity-50 transition-opacity" id="ind-step-2">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold">2</div>
                        <div><h6 class="font-bold">Info Produk</h6><p class="text-xs text-blue-200">Detail jualan</p></div>
                    </div>
                    <div class="w-0.5 h-6 bg-white/20 ml-4"></div>

                    <div class="flex items-center gap-4 opacity-50 transition-opacity" id="ind-step-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold">3</div>
                        <div><h6 class="font-bold">Akun & Bank</h6><p class="text-xs text-blue-200">Pencairan dana</p></div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-xs text-blue-200 hidden lg:block">
                © 2025 Bermadani - Koperasi & Retail Management.
            </div>
        </div>

        <div class="w-full lg:w-7/12 lg:ml-[41.666667%] bg-secondary dark:bg-darkBg min-h-screen flex flex-col justify-center p-6 lg:p-12">
            
            <div class="max-w-xl mx-auto w-full">
                
                <div class="lg:hidden mb-8">
                    <div class="flex justify-between text-xs font-bold text-slate-500 mb-2">
                        <span id="mob-text-step">Langkah 1 dari 3</span>
                        <span id="mob-text-name">Data Pemilik</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                        <div id="mob-progress" class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
                    </div>
                </div>

                <form id="wizardForm" action="supplier-dashboard.html">
                    
                    <div class="step-content active" id="step1">
                        <div class="bg-white dark:bg-darkCard p-6 lg:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Informasi Pemilik</h2>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Status Kemitraan</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" class="peer sr-only" checked onchange="toggleIdentity('student')">
                                            <div class="p-3 border border-slate-200 dark:border-slate-600 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center transition-all">
                                                <i class='bx bxs-graduation text-2xl text-slate-400 peer-checked:text-primary mb-1'></i>
                                                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 peer-checked:text-primary">Mahasiswa / Civitas</p>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" class="peer sr-only" onchange="toggleIdentity('public')">
                                            <div class="p-3 border border-slate-200 dark:border-slate-600 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center transition-all">
                                                <i class='bx bxs-store text-2xl text-slate-400 peer-checked:text-primary mb-1'></i>
                                                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 peer-checked:text-primary">Umum</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nama Lengkap</label>
                                    <input type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Nama sesuai KTP">
                                </div>

                                <div>
                                    <label id="id-label" class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">NIM / NIP</label>
                                    <input type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Nomor Identitas">
                                </div>

                                <div class="md:col-span-2">
    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nomor WhatsApp (Aktif)</label>
    <div class="relative">
        <span class="absolute inset-y-0 left-4 flex items-center text-slate-500 text-sm font-bold">+62</span>
        
        <input type="number" 
               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none no-spinner" 
               placeholder="812 3456 7890"
               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </div>
</div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="button" onclick="nextStep(2)" class="bg-primary hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center gap-2">
                                    Lanjut <i class='bx bx-right-arrow-alt text-xl'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="step-content" id="step2">
                        <div class="bg-white dark:bg-darkCard p-6 lg:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Informasi Usaha</h2>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nama Brand / Toko</label>
                                    <input type="text" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Contoh: Keripik Mas Dani">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Kategori Utama</label>
                                    <select class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none cursor-pointer">
                                        <option>Makanan Ringan (Snack)</option>
                                        <option>Minuman</option>
                                        <option>Fashion / Aksesoris</option>
                                        <option>ATK</option>
                                        <option>Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Deskripsi Singkat</label>
                                    <textarea rows="4" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Jelaskan produk apa yang ingin Anda titip jual..."></textarea>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between">
                                <button type="button" onclick="nextStep(1)" class="text-slate-500 font-bold py-3 px-6 hover:text-primary transition-colors">
                                    Kembali
                                </button>
                                <button type="button" onclick="nextStep(3)" class="bg-primary hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center gap-2">
                                    Lanjut <i class='bx bx-right-arrow-alt text-xl'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="step-content" id="step3">
                        <div class="bg-white dark:bg-darkCard p-6 lg:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Akun & Pencairan</h2>
                            
                            <div class="space-y-5">
                                <div class="grid md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Email</label>
                                        <input type="email" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none" placeholder="email@anda.com">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Password</label>
                                        <input type="password" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none" placeholder="••••••••">
                                    </div>
                                </div>

                                <div class="p-5 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-800/30">
                                    <h4 class="text-xs font-bold text-blue-700 dark:text-blue-300 mb-4 flex items-center gap-2">
                                        <i class='bx bxs-bank'></i> Info Rekening (Penting)
                                    </h4>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="col-span-1">
                                            <select class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-xs outline-none cursor-pointer h-10">
                                                <option>BCA</option>
                                                <option>BRI</option>
                                                <option>Mandiri</option>
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <input type="text" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-xs outline-none h-10" placeholder="Nomor Rekening">
                                        </div>
                                        <div class="col-span-3">
                                            <input type="text" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-xs outline-none h-10" placeholder="Atas Nama Pemilik">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2">
                                    <input type="checkbox" id="terms" class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                    <label for="terms" class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Saya setuju dengan <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a>. Fee bagi hasil 10-15% berlaku untuk setiap penjualan.
                                    </label>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between">
                                <button type="button" onclick="nextStep(2)" class="text-slate-500 font-bold py-3 px-6 hover:text-primary transition-colors">
                                    Kembali
                                </button>
                                <button type="submit" class="bg-primary hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-1 flex items-center gap-2">
                                    Daftar Sekarang <i class='bx bx-check-circle text-xl'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-xs text-slate-500">Sudah punya akun? <a href="login-campus.html" class="text-primary font-bold hover:underline">Masuk disini</a></p>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleIdentity(type) {
            const label = document.getElementById('id-label');
            label.innerText = type === 'student' ? 'NIM / NIP' : 'Nomor KTP (NIK)';
        }

        function nextStep(step) {
            // 1. Sembunyikan semua step
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            
            // 2. Tampilkan step tujuan
            document.getElementById('step' + step).classList.add('active');

            // 3. Update Progress Indicator (Desktop Side)
            // Reset opacity semua
            for(let i=1; i<=3; i++) {
                const el = document.getElementById('ind-step-'+i);
                const circle = el.querySelector('div');
                
                if(i === step) {
                    // Active State
                    el.classList.remove('opacity-50');
                    circle.classList.remove('bg-white/20', 'text-white');
                    circle.classList.add('bg-white', 'text-primary', 'ring-4', 'ring-blue-400/30');
                } else if(i < step) {
                    // Done State
                    el.classList.remove('opacity-50');
                    circle.classList.remove('bg-white', 'text-primary', 'ring-4', 'ring-blue-400/30');
                    circle.classList.add('bg-green-400', 'text-white'); // Hijau tanda selesai
                    circle.innerHTML = "<i class='bx bx-check'></i>";
                } else {
                    // Inactive State
                    el.classList.add('opacity-50');
                    circle.classList.remove('bg-white', 'text-primary', 'ring-4', 'ring-blue-400/30', 'bg-green-400');
                    circle.classList.add('bg-white/20', 'text-white');
                    circle.innerText = i;
                }
            }

            // 4. Update Mobile Progress Bar
            const mobTextStep = document.getElementById('mob-text-step');
            const mobTextName = document.getElementById('mob-text-name');
            const mobProgress = document.getElementById('mob-progress');
            
            mobTextStep.innerText = `Langkah ${step} dari 3`;
            if(step === 1) { mobTextName.innerText = 'Data Pemilik'; mobProgress.style.width = '33%'; }
            if(step === 2) { mobTextName.innerText = 'Info Produk'; mobProgress.style.width = '66%'; }
            if(step === 3) { mobTextName.innerText = 'Akun & Bank'; mobProgress.style.width = '100%'; }
        }
    </script>

</body>
</html>