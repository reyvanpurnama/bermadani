<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Portal — {{ coop_config('short_name', 'Bermadani') }} UMBandung</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .text-apple-headline {
            letter-spacing: -0.035em;
            line-height: 1.08;
        }
    </style>
</head>
<body class="bg-[#f5f5f7] text-zinc-900 min-h-dvh flex flex-col justify-between overflow-x-hidden relative selection:bg-[#155A6B] selection:text-white">

    <!-- 1. Background Graphics (Preserved Hero Landscape & Portrait) -->
    <div class="fixed inset-0 bg-[url('/images/hero-portrait.jpeg')] bg-cover bg-center md:hidden opacity-30 pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-[url('/images/hero-landscape.jpeg')] bg-cover bg-right hidden md:block opacity-35 pointer-events-none z-0"></div>
    
    <!-- Ambient Overlay for Clean Readability -->
    <div class="fixed inset-0 bg-gradient-to-b from-[#f5f5f7]/95 via-[#f5f5f7]/85 to-[#f5f5f7]/95 backdrop-blur-sm pointer-events-none z-0"></div>

    <!-- 2. Header Navigation Bar -->
    <header class="relative z-20 w-full max-w-6xl mx-auto px-4 sm:px-6 pt-5 sm:pt-7 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-semibold text-xs text-zinc-800 hover:opacity-80 transition-opacity">
            <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi Bermadani" class="w-7 h-7 object-contain">
            <span class="font-bold tracking-tight text-xs text-zinc-900 truncate max-w-[200px] sm:max-w-none">{{ coop_config('short_name', 'Bermadani') }} — UMBandung</span>
        </a>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white/80 hover:bg-white text-zinc-800 text-xs font-bold shadow-sm transition-all border border-zinc-200/60">
            <i class='bx bx-left-arrow-alt text-base text-[#155A6B]'></i>
            <span>Kembali ke Beranda</span>
        </a>
    </header>

    <!-- 3. Main Login Card Container -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-10 sm:py-16">
        <div class="w-full max-w-md">
            
            <!-- Modern Borderless Depth Login Card -->
            <div class="bg-white/90 backdrop-blur-2xl rounded-3xl p-7 sm:p-10 text-left shadow-2xl shadow-zinc-900/10 border border-white/80 relative overflow-hidden">
                
                <!-- Security Top Badge -->
                <div class="text-center mb-6">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#155A6B] bg-teal-50 px-3 py-1 rounded-full mb-3 shadow-2xs">
                        <i class='bx bx-shield-quarter text-xs'></i> PORTAL AKSES SYARIAH
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 text-apple-headline tracking-tight">
                        Masuk Portal.
                    </h1>
                    <p class="text-xs text-zinc-500 mt-1 font-medium">
                        Akses layanan simpanan, poin belanja POS, & portal supplier.
                    </p>
                </div>

                {{-- Active Session Alert --}}
                @auth
                    <div class="bg-teal-50/90 border border-teal-200/80 rounded-2xl p-4 mb-6 text-xs text-zinc-800 backdrop-blur-sm">
                        <div class="flex items-center gap-2 font-bold text-[#155A6B] mb-1">
                            <i class='bx bx-user-check text-base'></i>
                            <span>Sesi Log Masuk Aktif</span>
                        </div>
                        <p class="text-zinc-600 text-[11px] mb-3">
                            Role terdeteksi: <span class="font-bold text-[#155A6B]">{{ auth()->user()->role }}</span>
                        </p>
                        <div class="flex items-center gap-2">
                            @php
                                $role = auth()->user()->role;
                                $dashboardUrl = match($role) {
                                    'SUPER_ADMIN', 'ADMIN', 'DEVELOPER' => route('admin.dashboard'),
                                    'KASIR' => route('admin.pos'),
                                    'SUPPLIER' => route('supplier.dashboard'),
                                    'MEMBER' => route('member.dashboard'),
                                    default => route('home'),
                                };
                            @endphp
                            <a href="{{ $dashboardUrl }}" class="px-3.5 py-1.5 bg-[#155A6B] hover:bg-[#1a6b80] text-white rounded-xl font-bold text-xs shadow-sm transition-all">
                                Ke Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-sm transition-all">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="bg-rose-50/90 border border-rose-200/80 rounded-2xl p-3.5 mb-6 flex items-center gap-2 text-rose-700 text-xs font-semibold">
                        <i class='bx bx-error-circle text-lg flex-shrink-0'></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Username / Email Input -->
                    <div>
                        <label for="email" class="block mb-1.5 text-xs font-bold text-zinc-800">
                            ID Pengguna / Email
                        </label>
                        <div class="relative flex items-center">
                            <i class='bx bxs-id-card text-lg text-zinc-400 absolute left-3.5 pointer-events-none'></i>
                            <input type="text" id="email" name="email" value="{{ old('email') }}"
                                class="w-full bg-zinc-50/80 focus:bg-white border border-zinc-200/80 focus:border-[#155A6B] focus:ring-2 focus:ring-[#155A6B]/15 rounded-2xl pl-10 pr-4 py-3 text-xs sm:text-sm text-zinc-900 outline-none font-medium placeholder:text-zinc-400 transition-all"
                                placeholder="NIM, NIDN, atau Email..." required autofocus>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-bold text-zinc-800">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="relative flex items-center">
                            <i class='bx bxs-lock-alt text-lg text-zinc-400 absolute left-3.5 pointer-events-none'></i>
                            <input type="password" id="password" name="password"
                                class="w-full bg-zinc-50/80 focus:bg-white border border-zinc-200/80 focus:border-[#155A6B] focus:ring-2 focus:ring-[#155A6B]/15 rounded-2xl pl-10 pr-10 py-3 text-xs sm:text-sm text-zinc-900 outline-none font-medium placeholder:text-zinc-400 transition-all"
                                placeholder="••••••••" required>
                            <button type="button" id="toggle-password" class="absolute right-3.5 text-zinc-400 hover:text-zinc-600 transition-colors">
                                <i id="password-icon" class='bx bx-hide text-lg'></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#155A6B] hover:bg-[#19697d] text-white py-3.5 px-6 rounded-full font-bold text-xs sm:text-sm shadow-md hover:shadow-lg hover:scale-[1.01] flex items-center justify-center gap-2 transition-all duration-300 cursor-pointer mt-3">
                        <span>Masuk Portal</span>
                        <i class='bx bx-right-arrow-alt text-lg'></i>
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-6 pt-5 border-t border-zinc-100 text-center space-y-2.5 text-xs text-zinc-500">
                    <p>
                        Belum terdaftar sebagai supplier? 
                        <a href="{{ route('supplier.register') }}" class="text-[#155A6B] font-bold hover:underline">Daftar Mitra UMKM</a>
                    </p>
                </div>

            </div>

        </div>
    </main>

    <!-- Page Footer -->
    <footer class="relative z-20 w-full py-6 text-center text-xs text-zinc-400 font-medium">
        <p>&copy; {{ date('Y') }} {{ coop_config('legal_name', 'Koperasi Konsumen Syariah Berkah Solusi Madani') }} — UMBandung</p>
    </footer>

    <script>
        // Password Visibility Toggle
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');

        if (togglePassword && passwordInput && passwordIcon) {
            togglePassword.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.replace('bx-hide', 'bx-show');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.replace('bx-show', 'bx-hide');
                }
            });
        }
    </script>
</body>
</html>