<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CATATAN ATAS LAPORAN KEUANGAN (CALK) {{ $year }} - KSPPS Berkah Madani</title>
    <style>
        @page {
            margin: 25px 35px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
        .chapter-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f766e;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1.5px solid #0f766e;
            text-transform: uppercase;
        }
        .sub-chapter {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        p {
            margin-top: 0;
            margin-bottom: 8px;
            text-align: justify;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 12px;
        }
        th, td {
            padding: 5px 8px;
            font-size: 10px;
            border: 1px solid #cbd5e1;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        .footer-sign {
            margin-top: 35px;
            width: 100%;
            border: none;
        }
        .footer-sign td {
            border: none;
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
    <div class="doc-title">CATATAN ATAS LAPORAN KEUANGAN (CALK)</div>
    <div class="doc-subtitle">Untuk Periode yang Berakhir pada 31 Desember {{ $year }}</div>
</div>

<!-- BAB 1: GAMBARAN UMUM KOPERASI -->
<div class="chapter-title">BAB I. GAMBARAN UMUM KOPERASI</div>
<div class="sub-chapter">1.1 Pendirian dan Legalitas</div>
<p>{{ $calkData['bab1']['profil'] ?? 'KSPPS Berkah Madani didirikan sebagai Koperasi Simpan Pinjam dan Pembiayaan Syariah yang berkedudukan di Bandung. Koperasi ini beroperasi berdasar pada prinsip-prinsip syariah Islam dan peraturan perundang-undangan perkoperasian yang berlaku di Republik Indonesia.' }}</p>

<div class="sub-chapter">1.2 Kegiatan Utama Usaha</div>
<p>{{ $calkData['bab1']['kegiatan'] ?? 'Kegiatan utama KSPPS Berkah Madani meliputi penghimpunan dana simpanan anggota (Wadiah dan Mudharabah), penyaluran pembiayaan syariah (Murabahah, Mudharabah, Musyarakah, dan Qardh), serta pengelolaan unit toko minimarket retail untuk kesejahteraan anggota.' }}</p>

<!-- BAB 2: KEBIJAKAN AKUNTANSI UTAMA -->
<div class="chapter-title">BAB II. KEBIJAKAN AKUNTANSI SIGNIFICANT</div>
<div class="sub-chapter">2.1 Dasar Penyusunan Laporan Keuangan</div>
<p>Laporan keuangan disusun berdasarkan Standar Akuntansi Keuangan Entitas Privat (SAK EP) dan PSAK Syariah (PSAK 101 tentang Penyajian Laporan Keuangan Syariah, PSAK 102 Akuntansi Murabahah, PSAK 105 Akuntansi Mudharabah).</p>

<div class="sub-chapter">2.2 Pengakuan Pendapatan Margin Pembiayaan</div>
<p>Pendapatan margin pembiayaan diakui secara proporsional sesuai dengan jadwal angsuran yang disepakati (metode anuitas/proporsional syariah).</p>

<div class="sub-chapter">2.3 Aset Tetap dan Penyusutan</div>
<p>Aset tetap dicatat berdasarkan harga perolehan dikurangi akumulasi penyusutan. Penyusutan dihitung menggunakan metode garis lurus (straight-line method) berdasarkan estimasi masa manfaat aset.</p>

<!-- BAB 3: RINCIAN KAS DAN SETARA KAS -->
<div class="chapter-title">BAB III. KAS DAN SETARA KAS</div>
<p>Rincian kas dan setara kas per 31 Desember {{ $year }} adalah sebagai berikut:</p>
<table>
    <thead>
        <tr>
            <th>URAIAN</th>
            <th class="text-right">SALDO (RP)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Kas Tunai Brankas Office</td>
            <td class="text-right font-mono">{{ $formatRp($calkData['bab3']['kas_tunai'] ?? ($neraca['kas'] * 0.15 ?? 0)) }}</td>
        </tr>
        <tr>
            <td>Bank Syariah / Bank Operasional</td>
            <td class="text-right font-mono">{{ $formatRp($calkData['bab3']['bank'] ?? ($neraca['kas'] * 0.85 ?? 0)) }}</td>
        </tr>
        <tr class="font-bold" style="background:#f1f5f9;">
            <td>TOTAL KAS DAN SETARA KAS</td>
            <td class="text-right font-mono">{{ $formatRp($neraca['kas'] ?? 0) }}</td>
        </tr>
    </tbody>
</table>

<!-- BAB 4: PIUTANG PEMBIAYAAAN -->
<div class="chapter-title">BAB IV. PIUTANG PEMBIAYAAN SYARIAH</div>
<p>Rincian portofolio pembiayaan anggota per 31 Desember {{ $year }}:</p>
<table>
    <thead>
        <tr>
            <th>JENIS PEMBIAYAAN</th>
            <th class="text-right">SALDO PIUTANG (RP)</th>
            <th class="text-center">PERSENTASE (%)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Pembiayaan Bermadani (Internal)</td>
            <td class="text-right font-mono">{{ $formatRp($calkData['bab4']['bermadani'] ?? ($neraca['piutang_pembiayaan'] * 0.7 ?? 0)) }}</td>
            <td class="text-center font-mono">70%</td>
        </tr>
        <tr>
            <td>Pembiayaan BMT Itqan (Channeling)</td>
            <td class="text-right font-mono">{{ $formatRp($calkData['bab4']['bmt'] ?? ($neraca['piutang_pembiayaan'] * 0.3 ?? 0)) }}</td>
            <td class="text-center font-mono">30%</td>
        </tr>
        <tr class="font-bold" style="background:#f1f5f9;">
            <td>TOTAL PIUTANG PEMBIAYAAN</td>
            <td class="text-right font-mono">{{ $formatRp($neraca['piutang_pembiayaan'] ?? 0) }}</td>
            <td class="text-center font-mono">100%</td>
        </tr>
    </tbody>
</table>

<!-- BAB 5: SIMPANAN ANGGOTA -->
<div class="chapter-title">BAB V. SIMPANAN ANGGOTA</div>
<p>Rincian saldo simpanan anggota per 31 Desember {{ $year }}:</p>
<table>
    <thead>
        <tr>
            <th>JENIS SIMPANAN</th>
            <th class="text-right">SALDO (RP)</th>
            <th>AKAD SYARIAH</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Simpanan Pokok</td>
            <td class="text-right font-mono">{{ $formatRp($neraca['simpanan_pokok'] ?? 0) }}</td>
            <td>Syirkah / Ekuitas</td>
        </tr>
        <tr>
            <td>Simpanan Wajib</td>
            <td class="text-right font-mono">{{ $formatRp($neraca['simpanan_wajib'] ?? 0) }}</td>
            <td>Syirkah / Ekuitas</td>
        </tr>
        <tr>
            <td>Simpanan Sukarela</td>
            <td class="text-right font-mono">{{ $formatRp($neraca['simpanan_anggota'] ?? 0) }}</td>
            <td>Wadiah Yad Dhomanah (Titipan)</td>
        </tr>
    </tbody>
</table>

<!-- BAB 6: KEPATUHAN SYARIAH -->
<div class="chapter-title">BAB VI. KEPATUHAN SYARIAH & OPINI DPS</div>
<p>{{ $calkData['bab6']['opini_dps'] ?? 'Berdasarkan pengawasan Dewan Pengawas Syariah (DPS) KSPPS Berkah Madani, seluruh kegiatan operasional pembiayaan, penghimpunan dana, dan investasi yang dilakukan selama tahun buku ' . $year . ' telah sesuai dengan prinsip-prinsip Syariah Islam dan Fatwa Dewan Syariah Nasional (DSN-MUI).' }}</p>

<!-- SIGNATURE BLOCK -->
<table class="footer-sign">
    <tr>
        <td>
            <div>Bandung, 31 Desember {{ $year }}</div>
            <div class="font-bold">Ketua Pengurus</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">Ridlo Abdillah, M.Kom</div>
        </td>
        <td>
            <div style="visibility: hidden;">Spacer</div>
            <div class="font-bold">Manager / Bendahara</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">Muhammad Alwi Almaliki</div>
        </td>
        <td>
            <div style="visibility: hidden;">Spacer</div>
            <div class="font-bold">Dewan Pengawas Syariah</div>
            <div class="sign-space"></div>
            <div class="font-bold" style="text-decoration: underline;">Dr. H. Ahmad Sobari, M.Ag</div>
        </td>
    </tr>
</table>

</body>
</html>
