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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=SF+Pro+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .text-apple-headline {
            letter-spacing: -0.035em;
            line-height: 1.05;
        }

        /* Glassmorphism Card Overlay (0% Opacity) */
        .glass-card-login {
            background: rgba(255, 255, 255, 0);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1.5px solid rgba(255, 255, 255, 0.85);
            box-shadow: 
                inset 0 1.5px 2px 0 rgba(255, 255, 255, 0.8),
                0 20px 40px -15px rgba(0, 0, 0, 0.12);
            border-radius: 32px;
            transition: all 0.4s ease;
        }

        .glass-card-login:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Glass Input Fields */
        .glass-input {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 16px;
            transition: all 0.25s ease;
        }

        .glass-input:focus-within {
            background: rgba(255, 255, 255, 0.85);
            border-color: #155A6B;
            box-shadow: 0 0 0 4px rgba(21, 90, 107, 0.15);
        }

        /* Primary Glass CTA Button */
        .glass-login-btn {
            background: #155A6B;
            color: #ffffff;
            border-radius: 980px;
            box-shadow: 0 8px 24px -4px rgba(21, 90, 107, 0.35);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-login-btn:hover {
            background: #1a6b80;
            transform: translateY(-2px) scale(1.015);
            box-shadow: 0 14px 32px -4px rgba(21, 90, 107, 0.5);
            color: #ffffff;
        }
    </style>
</head>
<body class="bg-[#f5f5f7] text-zinc-900 min-h-dvh flex flex-col justify-between overflow-x-hidden relative selection:bg-[#155A6B] selection:text-white">

    <!-- 3D Ambient Background Graphic (Matches Landing Hero) -->
    <div class="fixed inset-0 bg-[url('/images/hero-bg.jpg')] bg-cover bg-center opacity-80 pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-gradient-to-b from-white/40 via-transparent to-[#f5f5f7]/90 pointer-events-none z-0"></div>

    <!-- Header Navigation Bar -->
    <header class="relative z-20 w-full max-w-6xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-xs text-zinc-800 hover:text-[#155A6B] transition-colors">
            <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi Bermadani" class="w-6 h-6 sm:w-7 sm:h-7 object-contain">
            <span class="font-bold tracking-tight text-[11px] sm:text-xs truncate max-w-[170px] sm:max-w-none">{{ coop_config('short_name', 'Bermadani') }} — UMBandung</span>
        </a>

        <a href="{{ route('home') }}" class="glass-cta-primary px-3.5 sm:px-4 py-1.5 sm:py-2 text-[11px] sm:text-xs font-bold gap-1 sm:gap-1.5">
            <i class='bx bx-left-arrow-alt text-base'></i>
            <span><span class="hidden sm:inline">Kembali ke </span>Beranda</span>
        </a>
    </header>

    <!-- Main Content Grid -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-3 sm:px-4 py-8 sm:py-16">
        <div class="w-full max-w-md">
            
            <!-- Glassmorphism Login Card -->
            <div class="glass-card-login p-6 sm:p-10 text-left">
                
                <!-- Brand Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#155A6B]/10 text-[#155A6B] mb-3 shadow-inner">
                        <i class='bx bxs-user-circle text-2xl sm:text-3xl'></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 text-apple-headline tracking-tight">
                        Portal Anggota
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-1 font-medium">
                        Masuk untuk mengakses simpanan & transaksi.
                    </p>
                </div>

                {{-- Active Session Alert --}}
                @auth
                    <div class="bg-blue-50/80 border border-blue-200/80 rounded-2xl p-4 mb-6 text-xs text-zinc-800 backdrop-blur-sm">
                        <div class="flex items-center gap-2 font-bold text-[#155A6B] mb-1">
                            <i class='bx bx-user-check text-base'></i>
                            <span>Sesi Aktif Terdeteksi</span>
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
                            <a href="{{ $dashboardUrl }}" class="px-3 py-1.5 bg-[#155A6B] hover:bg-[#1a6b80] text-white rounded-lg font-bold text-xs transition-colors">
                                Ke Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-xs transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="bg-rose-50/90 border border-rose-200 rounded-2xl p-3.5 mb-6 flex items-center gap-2 text-rose-700 text-xs font-semibold">
                        <i class='bx bx-error-circle text-lg flex-shrink-0'></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
                    @csrf

                    <!-- ID / Email Input -->
                    <div>
                        <label for="email" class="block mb-1.5 text-xs font-bold text-zinc-700">
                            ID Pengguna / Email
                        </label>
                        <div class="glass-input relative flex items-center">
                            <i class='bx bxs-id-card text-lg text-zinc-400 absolute left-4 pointer-events-none'></i>
                            <input type="text" id="email" name="email" value="{{ old('email') }}"
                                class="w-full bg-transparent pl-11 pr-4 py-3 text-base sm:text-sm text-zinc-900 outline-none font-medium placeholder:text-zinc-400"
                                placeholder="Nomor anggota atau email..." required autofocus>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-bold text-zinc-700">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="glass-input relative flex items-center">
                            <i class='bx bxs-lock-alt text-lg text-zinc-400 absolute left-4 pointer-events-none'></i>
                            <input type="password" id="password" name="password"
                                class="w-full bg-transparent pl-11 pr-11 py-3 text-base sm:text-sm text-zinc-900 outline-none font-medium placeholder:text-zinc-400"
                                placeholder="••••••••" required>
                            <button type="button" id="toggle-password" class="absolute right-4 text-zinc-400 hover:text-zinc-600 transition-colors">
                                <i id="password-icon" class='bx bx-hide text-lg'></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit CTA Button -->
                    <button type="submit" class="w-full glass-login-btn py-3.5 px-6 font-bold text-sm flex items-center justify-center gap-2 group mt-2">
                        <span>Masuk Sistem</span>
                        <i class='bx bx-right-arrow-alt text-xl group-hover:translate-x-1 transition-transform duration-300'></i>
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t border-zinc-200/80 text-center space-y-3 text-xs text-zinc-500">
                    <p>
                        Belum punya akun? 
                        <a href="{{ route('supplier.register') }}" class="text-[#155A6B] font-bold hover:underline">Daftar Supplier</a>
                    </p>
                </div>

            </div>

        </div>
    </main>

    <!-- Page Footer -->
    <footer class="relative z-20 w-full py-6 text-center text-xs text-zinc-500">
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