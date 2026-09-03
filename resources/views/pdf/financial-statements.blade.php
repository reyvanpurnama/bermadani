<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan {{ $year }} - KSPPS Berkah Madani</title>
    <style>
        @page {
            margin: 20px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
        }
        .kop-img {
            max-height: 70px;
            margin-bottom: 5px;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .doc-subtitle {
            font-size: 11px;
            color: #64748b;
        }
        .page-break {
            page-break-after: always;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f766e;
            margin-top: 15px;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 5px 8px;
            font-size: 10px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        .table-striped tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        
        .subtotal-row td {
            border-top: 1px solid #94a3b8;
            font-weight: bold;
            background-color: #f8fafc;
        }
        .grandtotal-row td {
            border-top: 2px solid #0f766e;
            border-bottom: 2px double #0f766e;
            font-weight: bold;
            font-size: 11px;
            background-color: #ccfbf1;
            color: #0f766e;
        }
        
        .two-col {
            width: 100%;
        }
        .two-col td {
            vertical-align: top;
            width: 50%;
            padding: 0 5px;
        }

        .footer-sign {
            margin-top: 30px;
            width: 100%;
        }
        .footer-sign td {
            text-align: center;
            width: 33.3%;
            vertical-align: top;
        }
        .sign-space {
            height: 50px;
        }
    </style>
</head>
<body>

@php
    $formatRp = function($val) {
        if ($val < 0) {
            return '(Rp ' . number_format(abs($val), 0, ',', '.') . ')';
        }
        return 'Rp ' . number_format($val, 0, ',', '.');
    };
@endphp

<!-- HEADER -->
<div class="header">
    @if(!empty($kopBase64))
        <img src="{{ $kopBase64 }}" class="kop-img" alt="Kop Surat">
    @else
        <div style="font-size: 18px; font-weight: bold; color: #0f766e;">KSPPS BERKAH MADANI</div>
        <div style="font-size: 10px; color: #64748b;">Koperasi Simpan Pinjam dan Pembiayaan Syariah</div>
    @endif
    <div class="doc-title">LAPORAN KEUANGAN TAHUNAN</div>
    <div class="doc-subtitle">Untuk Periode yang Berakhir pada 31 Desember {{ $year }}</div>
</div>

<!-- 1. NERACA -->
<div class="section-title">1. NERACA (LAPORAN POSISI KEUANGAN)</div>
<table class="two-col">
    <tr>
        <!-- ASET -->
        <td>
            <table>
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: left;">ASET</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="2" class="font-bold" style="background:#f8fafc;">Aset Lancar</td></tr>
                    <tr>
                        <td>Kas & Setara Kas</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['kas'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Piutang Pembiayaan</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['piutang_pembiayaan'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 15px; color: #64748b;">Cadangan Kerugian Piutang</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ $formatRp(-abs($neraca['cadangan_kerugian'] ?? 0)) }}</td>
                    </tr>
                    <tr>
                        <td>Piutang Lain-lain</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['piutang_lain'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Persediaan Minimarket</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['persediaan'] ?? 0) }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td>Total Aset Lancar</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['total_aset_lancar'] ?? 0) }}</td>
                    </tr>

                    <tr><td colspan="2" class="font-bold" style="background:#f8fafc; padding-top: 8px;">Aset Tidak Lancar</td></tr>
                    <tr>
                        <td>Aset Tetap (Neto)</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['aset_tetap'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Aset Lainnya</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['aset_lainnya'] ?? 0) }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td>Total Aset Tidak Lancar</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['total_aset_tidak_lancar'] ?? 0) }}</td>
                    </tr>
                    <tr class="grandtotal-row">
                        <td>TOTAL ASET</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['total_aset'] ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </td>

        <!-- LIABILITAS & EKUITAS -->
        <td>
            <table>
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: left;">LIABILITAS & EKUITAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="2" class="font-bold" style="background:#f8fafc;">Liabilitas</td></tr>
                    <tr>
                        <td>Simpanan Anggota (Wadiah)</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['simpanan_anggota'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Utang Lain-lain</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['utang_lain'] ?? 0) }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td>Total Liabilitas</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['total_liabilitas'] ?? 0) }}</td>
                    </tr>

                    <tr><td colspan="2" class="font-bold" style="background:#f8fafc; padding-top: 8px;">Ekuitas</td></tr>
                    <tr>
                        <td>Simpanan Pokok</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['simpanan_pokok'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Simpanan Wajib</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['simpanan_wajib'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Cadangan Koperasi</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['cadangan'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>SHU Tahun Berjalan</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['shu_berjalan'] ?? 0) }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td>Total Ekuitas</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['total_ekuitas'] ?? 0) }}</td>
                    </tr>
                    <tr class="grandtotal-row">
                        <td>TOTAL LIABILITAS & EKUITAS</td>
                        <td class="text-right font-mono">{{ $formatRp($neraca['total_liabilitas_ekuitas'] ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<div class="page-break"></div>

<!-- 2. LAPORAN SHU -->
<div class="section-title">2. LAPORAN SISA HASIL USAHA (SHU)</div>
<table class="table-striped">
    <thead>
        <tr>
            <th>URAIAN</th>
            <th class="text-right">NOMINAL (RP)</th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="2" class="font-bold" style="background:#f1f5f9;">PENDAPATAN OPERASIONAL</td></tr>
        <tr>
            <td>Margin Pembiayaan (Murabahah/Mudharabah)</td>
            <td class="text-right font-mono">{{ $formatRp($shu['margin_pembiayaan'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Pendapatan Administrasi & Layanan</td>
            <td class="text-right font-mono">{{ $formatRp($shu['pendapatan_administrasi'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Pendapatan Lain-lain</td>
            <td class="text-right font-mono">{{ $formatRp($shu['pendapatan_lain'] ?? 0) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>TOTAL PENDAPATAN OPERASIONAL</td>
            <td class="text-right font-mono">{{ $formatRp($shu['total_pendapatan'] ?? 0) }}</td>
        </tr>

        <tr><td colspan="2" class="font-bold" style="background:#f1f5f9;">BEBAN OPERASIONAL</td></tr>
        <tr>
            <td>Beban Gaji & Tunjangan Karyawan</td>
            <td class="text-right font-mono">{{ $formatRp($shu['beban_gaji'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Beban Perlengkapan & ATK</td>
            <td class="text-right font-mono">{{ $formatRp($shu['beban_atk'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Beban Listrik, Air, & Operasional Kantor</td>
            <td class="text-right font-mono">{{ $formatRp($shu['beban_listrik'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Beban Penyusutan Aset Tetap</td>
            <td class="text-right font-mono">{{ $formatRp($shu['beban_penyusutan'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Beban Operasional Lainnya</td>
            <td class="text-right font-mono">{{ $formatRp($shu['beban_lain'] ?? 0) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>TOTAL BEBAN OPERASIONAL</td>
            <td class="text-right font-mono">{{ $formatRp($shu['total_beban'] ?? 0) }}</td>
        </tr>

        <tr class="grandtotal-row">
            <td>SISA HASIL USAHA (SHU) BERSIH TAHUN BERJALAN</td>
            <td class="text-right font-mono">{{ $formatRp($shu['shu_bersih'] ?? 0) }}</td>
        </tr>
    </tbody>
</table>

<!-- 3. LAPORAN PERUBAHAN EKUITAS -->
<div class="section-title" style="margin-top: 25px;">3. LAPORAN PERUBAHAN EKUITAS</div>
<table>
    <thead>
        <tr>
            <th class="text-left">URAIAN</th>
            <th class="text-right">SIMPANAN POKOK</th>
            <th class="text-right">SIMPANAN WAJIB</th>
            <th class="text-right">CADANGAN</th>
            <th class="text-right">SHU BERJALAN</th>
            <th class="text-right">TOTAL EKUITAS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $rows = $equity['rows'] ?? [
                ['uraian' => 'Saldo Awal', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                ['uraian' => 'Penambahan', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                ['uraian' => 'Pengurangan', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
                ['uraian' => 'Saldo Akhir', 'pokok' => 0, 'wajib' => 0, 'cadangan' => 0, 'shu' => 0, 'total' => 0],
            ];
        @endphp
        @foreach($rows as $r)
            <tr class="{{ $loop->last ? 'grandtotal-row' : '' }}">
                <td class="font-bold">{{ $r['uraian'] }}</td>
                <td class="text-right font-mono">{{ $formatRp($r['pokok'] ?? 0) }}</td>
                <td class="text-right font-mono">{{ $formatRp($r['wajib'] ?? 0) }}</td>
                <td class="text-right font-mono">{{ $formatRp($r['cadangan'] ?? 0) }}</td>
                <td class="text-right font-mono">{{ $formatRp($r['shu'] ?? 0) }}</td>
                <td class="text-right font-mono">{{ $formatRp($r['total'] ?? 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- 4. LAPORAN ARUS KAS -->
<div class="section-title" style="margin-top: 25px;">4. LAPORAN ARUS KAS (DIRECT METHOD)</div>
<table>
    <thead>
        <tr>
            <th>URAIAN ARUS KAS</th>
            <th class="text-right">NOMINAL (RP)</th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="2" class="font-bold" style="background:#f1f5f9;">ARUS KAS DARI AKTIVITAS OPERASI</td></tr>
        <tr>
            <td>Penerimaan Margin & Bagi Hasil Pembiayaan</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['penerimaan_margin'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Pembayaran Beban Operasional Kantor</td>
            <td class="text-right font-mono">{{ $formatRp(-abs($cashflow['pembayaran_beban'] ?? 0)) }}</td>
        </tr>
        <tr>
            <td>Kenaikan / Penurunan Pembiayaan Disalurkan</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['perubahan_piutang'] ?? 0) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>Kas Bersih dari Aktivitas Operasi</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['total_operasi'] ?? 0) }}</td>
        </tr>

        <tr><td colspan="2" class="font-bold" style="background:#f1f5f9;">ARUS KAS DARI AKTIVITAS INVESTASI</td></tr>
        <tr>
            <td>Pembelian Aset Tetap Kantor</td>
            <td class="text-right font-mono">{{ $formatRp(-abs($cashflow['perolehan_aset'] ?? 0)) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>Kas Bersih dari Aktivitas Investasi</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['total_investasi'] ?? 0) }}</td>
        </tr>

        <tr><td colspan="2" class="font-bold" style="background:#f1f5f9;">ARUS KAS DARI AKTIVITAS PENDANAAN</td></tr>
        <tr>
            <td>Penerimaan Simpanan Pokok & Wajib Anggota</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['penerimaan_modal'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Pembagian Sisa Hasil Usaha (SHU) ke Anggota</td>
            <td class="text-right font-mono">{{ $formatRp(-abs($cashflow['pembagian_shu'] ?? 0)) }}</td>
        </tr>
        <tr class="subtotal-row">
            <td>Kas Bersih dari Aktivitas Pendanaan</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['total_pendanaan'] ?? 0) }}</td>
        </tr>

        <tr class="subtotal-row">
            <td>KENAIKAN / (PENURUNAN) BERSIH KAS & SETARA KAS</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['perubahan_kas'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Saldo Kas & Setara Kas Awal Tahun</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['kas_awal'] ?? 0) }}</td>
        </tr>
        <tr class="grandtotal-row">
            <td>SALDO KAS & SETARA KAS AKHIR TAHUN</td>
            <td class="text-right font-mono">{{ $formatRp($cashflow['kas_akhir'] ?? 0) }}</td>
        </tr>
    </tbody>
</table>

<!-- SIGNATURE BLOCK -->
<table class="footer-sign">
    <tr>
        <td>
            <div>Bandung, 31 Desember {{ $year }}</div>
            <div class="font-bold">Ketua KSPPS Berkah Madani</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">Ridlo Abdillah, M.Kom</div>
        </td>
        <td>
            <div style="visibility: hidden;">Spacer</div>
            <div class="font-bold">Bendahara / Manager</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">Muhammad Alwi Almaliki</div>
        </td>
        <td>
            <div style="visibility: hidden;">Spacer</div>
            <div class="font-bold">Pengawas Syariah</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">Dr. H. Ahmad Sobari, M.Ag</div>
        </td>
    </tr>
</table>

</body>
</html>
