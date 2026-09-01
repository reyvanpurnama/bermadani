<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | {{ coop_config('short_name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { primary: '{{ coop_config('theme.primary') }}' }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-6 font-sans">
    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur rounded-2xl border border-slate-700 p-8 shadow-2xl text-center">
        <!-- 403 Icon Header -->
        <div class="w-20 h-20 bg-rose-500/20 text-rose-400 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-rose-500/10">
            <i class='bx bx-lock-alt text-4xl'></i>
        </div>

        <h1 class="text-3xl font-extrabold text-white mb-2">403 - Akses Ditolak</h1>
        
        <p class="text-sm text-slate-300 mb-6">
            {{ $exception->getMessage() ?: 'Kamu tidak memiliki hak akses untuk membuka halaman ini.' }}
        </p>

        @auth
            <!-- Current Logged in User Card -->
            <div class="mb-6 p-4 rounded-xl bg-slate-900/80 border border-slate-700 text-left flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <h4 class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</h4>
                    <p class="text-[11px] text-slate-400">Role Terdeteksi: <span class="text-indigo-400 font-semibold">{{ auth()->user()->role }}</span></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <!-- 1-Click Logout & Switch Account -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm transition-all shadow-lg shadow-rose-600/30 flex items-center justify-center gap-2">
                        <i class='bx bx-log-out text-lg'></i>
                        <span>Logout & Ganti Akun</span>
                    </button>
                </form>

                <!-- Back to Own Dashboard -->
                @php
                    $role = auth()->user()->role;
                    $targetRoute = match($role) {
                        'SUPER_ADMIN', 'ADMIN', 'DEVELOPER' => route('admin.dashboard'),
                        'KASIR' => route('admin.pos'),
                        'SUPPLIER' => route('supplier.dashboard'),
                        'MEMBER' => route('member.dashboard'),
                        default => route('home'),
                    };
                @endphp
                <a href="{{ $targetRoute }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-sm transition-all">
                    <i class='bx bx-left-arrow-alt text-lg'></i>
                    <span>Kembali ke Dashboard Saya</span>
                </a>
            </div>
        @else
            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition-all">
                <i class='bx bx-log-in text-lg'></i>
                <span>Ke Halaman Login</span>
            </a>
        @endauth
    </div>
</body>
</html>
