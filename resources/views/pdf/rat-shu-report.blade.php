<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SHU RAT {{ $session->year }} - {{ config('cooperative.name') }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.3;
        }

        /* Kop Surat */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .org-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .org-subtitle {
            font-size: 9pt;
            color: #475569;
            margin-top: 2px;
        }
        .doc-badge {
            background-color: #312e81;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            text-align: right;
            display: inline-block;
        }

        /* Title */
        .title-box {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-box h1 {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .title-box p {
            font-size: 8.5pt;
            color: #64748b;
            margin: 3px 0 0 0;
        }

        /* Executive Summary Grid */
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 15px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .label-sm {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .value-lg {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
        }
        .value-highlight {
            color: #15803d;
        }

        /* Formula Card */
        .formula-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        .formula-title {
            font-weight: bold;
            color: #166534;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .formula-list {
            margin: 0;
            padding-left: 15px;
            color: #14532d;
        }
        .formula-list li {
            margin-bottom: 2px;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #0f172a;
            text-align: center;
        }
        .data-table td {
            padding: 5px 5px;
            border: 1px solid #cbd5e1;
            font-size: 8pt;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier', monospace; }
        .font-bold { font-weight: bold; }
        .text-emerald { color: #15803d; }
        .text-muted { color: #64748b; }

        .total-row td {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            font-size: 8.5pt;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
        }

        /* Signature Section */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }
        .sig-space {
            height: 55px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9pt;
        }
        .sig-title {
            font-size: 8pt;
            color: #64748b;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat -->
    <table class="header-table">
        <tr>
            <td>
                <div class="org-title">{{ strtoupper(config('cooperative.legal_name')) }} {{ strtoupper(config('cooperative.parent_org')) }}</div>
                <div class="org-subtitle">Laporan Eksekutif Perhitungan dan Alokasi SHU Anggota</div>
            </td>
            <td style="text-align: right;">
                <div class="doc-badge">DOKUMEN RESMI RAT {{ $session->year }}</div>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="title-box">
        <h1>LAPORAN HASIL PERHITUNGAN SHU RAT TAHUN {{ $session->year }}</h1>
        <p>Sesi RAT: {{ $session->title }} • Tanggal Pelaksanaan: {{ $session->event_date ? $session->event_date->translatedFormat('d F Y') : '-' }} • Cutoff Snapshot: 31 Desember {{ $session->year }}</p>
    </div>

    <!-- Ringkasan Keuangan RAT -->
    <div class="summary-card">
        <table class="summary-table">
            <tr>
                <td style="width: 33.33%;">
                    <div class="label-sm">Total Laba Bersih RAT</div>
                    <div class="value-lg">Rp {{ number_format($session->total_net_profit, 0, ',', '.') }}</div>
                </td>
                <td style="width: 33.33%;">
                    <div class="label-sm">Alokasi SHU Anggota</div>
                    <div class="value-lg value-highlight">Rp {{ number_format($session->total_member_shu, 0, ',', '.') }}</div>
                    <div style="font-size: 7.5pt; color: #475569;">({{ $session->total_net_profit > 0 ? round(($session->total_member_shu / $session->total_net_profit) * 100, 1) : 0 }}% dari Laba Bersih)</div>
                </td>
                <td style="width: 33.33%;">
                    <div class="label-sm">Pool Jasa Simpanan ({{ $session->jasa_simpanan_portion }}%)</div>
                    <div class="value-lg">Rp {{ number_format($totalJasaSimpananPool, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Penjelasan Rumus Validasi -->
    <div class="formula-box">
        <div class="formula-title">Panduan Rumus Perhitungan SHU (Kaji Validasi)</div>
        <ul class="formula-list">
            <li><strong>Jasa Simpanan (Rp)</strong> = (Total Simpanan Anggota ÷ Total Simpanan Seluruh Anggota Eligible) × Pool Jasa Simpanan.</li>
            <li><strong>Porsi (%)</strong> = Persentase kontribusi simpanan individu anggota terhadap total akumulasi simpanan koperasi per 31 Desember {{ $session->year }}.</li>
            <li><strong>Total SHU Diterima</strong> = Jasa Simpanan.</li>
        </ul>
    </div>

    <!-- Tabel Rincian SHU Per Anggota -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">No. Anggota</th>
                <th style="width: 17%;">Nama Anggota</th>
                <th style="width: 12%;">Unit / Prodi</th>
                <th style="width: 10%;">Simp. Pokok (Rp)</th>
                <th style="width: 10%;">Simp. Wajib (Rp)</th>
                <th style="width: 10%;">Total Simpanan</th>
                <th style="width: 6%;">Porsi (%)</th>
                <th style="width: 8%;">Jasa Simpanan</th>
                <th style="width: 8%;">TOTAL SHU (Rp)</th>
                <th style="width: 8%;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distributions as $index => $dist)
                @php
                    $member = $dist->member;
                    $simPokok = (float) (($dist->simpanan_pokok_snapshot > 0) ? $dist->simpanan_pokok_snapshot : ($member?->simpananPokok ?? 0));
                    $simWajib = (float) (($dist->simpanan_wajib_snapshot > 0) ? $dist->simpanan_wajib_snapshot : ($member?->simpananWajib ?? 0));
                    $totalSimp = $simPokok + $simWajib;
                @endphp
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="text-center font-mono font-bold">{{ $member?->nomorAnggota ?? '-' }}</td>
                    <td class="text-left font-bold">{{ $member?->name ?? 'Anggota Tidak Ditemukan' }}</td>
                    <td class="text-left text-muted">{{ $member?->unitKerja ?? '-' }}</td>
                    <td class="text-right font-mono">{{ number_format($simPokok, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($simWajib, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($totalSimp, 0, ',', '.') }}</td>
                    <td class="text-center font-mono">{{ number_format((float) $dist->portion_percentage, 2, ',', '.') }}%</td>
                    <td class="text-right font-mono">{{ number_format((float) $dist->jasa_simpanan_amount, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold text-emerald">Rp {{ number_format((float) $dist->shu_amount, 0, ',', '.') }}</td>
                    <td class="font-mono text-muted" style="font-size: 7.5pt; {{ ($index + 1) % 2 === 0 ? 'text-align: right; padding-right: 8px;' : 'text-align: left; padding-left: 8px;' }}">
                        {{ $index + 1 }}. .........
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted" style="padding: 15px;">Tidak ada data alokasi SHU anggota.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-center">TOTAL KESELURUHAN ({{ count($distributions) }} ANGGOTA)</td>
                <td class="text-right font-mono">Rp {{ number_format($distributions->sum('simpanan_pokok_snapshot'), 0, ',', '.') }}</td>
                <td class="text-right font-mono">Rp {{ number_format($distributions->sum('simpanan_wajib_snapshot'), 0, ',', '.') }}</td>
                <td class="text-right font-mono">Rp {{ number_format($totalSimpananPool, 0, ',', '.') }}</td>
                <td class="text-center font-mono">100.00%</td>
                <td class="text-right font-mono">Rp {{ number_format($totalJasaSimpananPool, 0, ',', '.') }}</td>
                <td class="text-right font-mono text-emerald">Rp {{ number_format($totalShuPool, 0, ',', '.') }}</td>
                <td class="text-center font-mono"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Halaman Tanda Tangan Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-title">Mengetahui,</div>
                <div class="sig-title"><strong>Ketua Koperasi</strong></div>
                <div class="sig-space"></div>
                <div class="sig-name">( __________________________ )</div>
                <div class="sig-title">NPA. ........................................</div>
            </td>
            <td>
                <div class="sig-title">Diverifikasi oleh,</div>
                <div class="sig-title"><strong>Bendahara Koperasi</strong></div>
                <div class="sig-space"></div>
                <div class="sig-name">( __________________________ )</div>
                <div class="sig-title">NPA. ........................................</div>
            </td>
            <td>
                <div class="sig-title">Disusun oleh,</div>
                <div class="sig-title"><strong>Ketua Tim RAT {{ $session->year }}</strong></div>
                <div class="sig-space"></div>
                <div class="sig-name">( __________________________ )</div>
                <div class="sig-title">NPA. ........................................</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi {{ config('cooperative.legal_name') }} {{ config('cooperative.parent_org') }} pada {{ $generatedAt }} WIB
    </div>

</body>
</html>
