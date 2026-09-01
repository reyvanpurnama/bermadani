<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Setup Wizard') - Koperasi Installer</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { primary: '#0F52BA' }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between font-sans antialiased">

    <!-- Navbar Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-500/20">
                    <i class='bx bx-cube-alt text-2xl'></i>
                </div>
                <div>
                    <h1 class="font-bold text-base text-white tracking-wide">Koperasi White-Label</h1>
                    <p class="text-xs text-slate-400">Automated Installation Wizard</p>
                </div>
            </div>
            <span class="text-xs font-mono bg-blue-950 text-blue-400 px-3 py-1 rounded-full border border-blue-800">
                v1.0.0 Setup
            </span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-6 py-10 flex-1 w-full">
        <!-- Progress Steps -->
        <div class="mb-10">
            <div class="flex items-center justify-between relative max-w-2xl mx-auto">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-800 -z-0"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ request()->routeIs('installer.step1') ? 'bg-blue-600 text-white ring-4 ring-blue-500/20' : (request()->routeIs('installer.step2', 'installer.step3', 'installer.success') ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400') }}">
                        @if(request()->routeIs('installer.step2', 'installer.step3', 'installer.success'))
                            <i class='bx bx-check text-xl'></i>
                        @else
                            1
                        @endif
                    </div>
                    <span class="text-xs font-medium text-slate-400">System</span>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ request()->routeIs('installer.step2') ? 'bg-blue-600 text-white ring-4 ring-blue-500/20' : (request()->routeIs('installer.step3', 'installer.success') ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400') }}">
                        @if(request()->routeIs('installer.step3', 'installer.success'))
                            <i class='bx bx-check text-xl'></i>
                        @else
                            2
                        @endif
                    </div>
                    <span class="text-xs font-medium text-slate-400">Database</span>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ request()->routeIs('installer.step3') ? 'bg-blue-600 text-white ring-4 ring-blue-500/20' : (request()->routeIs('installer.success') ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400') }}">
                        @if(request()->routeIs('installer.success'))
                            <i class='bx bx-check text-xl'></i>
                        @else
                            3
                        @endif
                    </div>
                    <span class="text-xs font-medium text-slate-400">Identitas & Admin</span>
                </div>

                <!-- Step 4 -->
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ request()->routeIs('installer.success') ? 'bg-emerald-500 text-white ring-4 ring-emerald-500/20' : 'bg-slate-800 text-slate-400' }}">
                        <i class='bx bx-rocket text-xl'></i>
                    </div>
                    <span class="text-xs font-medium text-slate-400">Selesai</span>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Koperasi White-Label Suite. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
