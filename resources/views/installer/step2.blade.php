@extends('installer.layout')

@section('title', 'Database Configuration')

@section('content')
<div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-8 shadow-xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <i class='bx bx-data text-blue-400 text-2xl'></i>
            2. Konfigurasi Database MySQL
        </h2>
        <p class="text-slate-400 text-sm mt-1">
            Masukkan kredensial database MySQL server kamu. (Sudah dibuat di cPanel / phpMyAdmin).
        </p>
    </div>

    <!-- Alert Messages -->
    <div id="connectionAlert" class="hidden mb-6 p-4 rounded-xl text-sm flex items-center gap-3"></div>

    <form action="{{ route('installer.step3') }}" method="POST" id="dbForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Host Database</label>
                <input type="text" name="db_host" id="db_host" value="{{ old('db_host', '127.0.0.1') }}" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Port Database</label>
                <input type="number" name="db_port" id="db_port" value="{{ old('db_port', '3306') }}" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Database (Database Name)</label>
                <input type="text" name="db_database" id="db_database" placeholder="contoh: db_koperasi" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Username Database</label>
                <input type="text" name="db_username" id="db_username" placeholder="contoh: root / user_koperasi" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password Database</label>
                <input type="password" name="db_password" id="db_password" placeholder="Kosongkan jika tanpa password" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-900 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-700">
            <button type="button" id="btnTestConn" class="px-5 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-semibold transition-all flex items-center gap-2">
                <i class='bx bx-refresh text-lg'></i>
                <span>Test Koneksi Database</span>
            </button>

            <button type="submit" id="btnNext" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition-all shadow-lg shadow-blue-600/30 flex items-center gap-2">
                <span>Lanjut ke Identitas</span>
                <i class='bx bx-right-arrow-alt text-xl'></i>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('btnTestConn').addEventListener('click', function() {
        const btn = this;
        const alert = document.getElementById('connectionAlert');
        btn.disabled = true;
        btn.innerHTML = "<i class='bx bx-loader-alt animate-spin text-lg'></i> Testing...";

        fetch("{{ route('installer.test-db') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                db_host: document.getElementById('db_host').value,
                db_port: document.getElementById('db_port').value,
                db_database: document.getElementById('db_database').value,
                db_username: document.getElementById('db_username').value,
                db_password: document.getElementById('db_password').value,
            })
        })
        .then(res => res.json())
        .then(data => {
            alert.classList.remove('hidden', 'bg-rose-950/60', 'border-rose-700', 'text-rose-300', 'bg-emerald-950/60', 'border-emerald-700', 'text-emerald-300');
            if (data.success) {
                alert.classList.add('bg-emerald-950/60', 'border', 'border-emerald-700', 'text-emerald-300');
                alert.innerHTML = "<i class='bx bx-check-circle text-xl'></i> " + data.message;
            } else {
                alert.classList.add('bg-rose-950/60', 'border', 'border-rose-700', 'text-rose-300');
                alert.innerHTML = "<i class='bx bx-error-circle text-xl'></i> " + data.message;
            }
        })
        .catch(err => {
            alert.classList.remove('hidden');
            alert.classList.add('bg-rose-950/60', 'border', 'border-rose-700', 'text-rose-300');
            alert.innerHTML = "<i class='bx bx-error-circle text-xl'></i> Gagal menguji koneksi.";
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = "<i class='bx bx-refresh text-lg'></i> Test Koneksi Database";
        });
    });
</script>
@endpush
@endsection
