<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip SHU RAT {{ $session?->year ?? date('Y') }} - {{ $member?->name ?? 'Anggota' }}</title>
    <style>
        @page {
            size: a5 landscape;
            margin: 7mm 10mm 6mm 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .kop-img {
            height: 36px;
            max-width: 100%;
            display: block;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 10pt;
            color: #155A6B;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .kop-text p {
            margin: 1px 0 0 0;
            font-size: 7pt;
            color: #64748b;
        }
        .divider {
            border-bottom: 2px solid #155A6B;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        .title-bar {
            text-align: center;
            margin-bottom: 6px;
        }
        .doc-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            font-size: 7.5pt;
            color: #155A6B;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 8px;
            margin-bottom: 7px;
        }
        .meta-box td {
            font-size: 7.5pt;
            padding: 1px 4px;
        }
        .meta-label {
            color: #64748b;
            font-size: 6.5pt;
            text-transform: uppercase;
        }
        .meta-val {
            font-weight: bold;
            color: #0f172a;
        }
        .card-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
        }
        .card-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 6px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .card-table td {
            padding: 3.5px 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .card-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .row-highlight-shu {
            background-color: #ecfdf5;
            font-weight: bold;
            color: #065f46;
        }
        .row-highlight-simpanan {
            background-color: #eef2ff;
            font-weight: bold;
            color: #312e81;
        }
        .grand-total-box {
            background-color: #fffbeb;
            border: 1.5px solid #f59e0b;
            border-radius: 4px;
            padding: 5px 8px;
            margin-top: 6px;
            margin-bottom: 7px;
        }
        .grand-total-box td {
            vertical-align: middle;
        }
        .sig-table {
            width: 100%;
            margin-top: 5px;
        }
        .sig-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 7pt;
            color: #475569;
        }
        .sig-space {
            height: 36px;
        }
        .sig-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 7.5pt;
            border-top: 1px solid #cbd5e1;
            display: inline-block;
            padding-top: 2px;
            min-width: 150px;
        }
        .footer-note {
            margin-top: 5px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 3px;
            font-size: 6pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td width="65%">
                @if(!empty($kopBase64))
                    <img src="{{ $kopBase64 }}" class="kop-img" alt="Kop Surat {{ coop_config('name') }}">
                @else
                    <div class="kop-text">
                        <h2>{{ coop_config('legal_name') }}</h2>
                        <p>{{ coop_config('parent_org') }} &bull; {{ coop_config('address') }}</p>
                    </div>
                @endif
            </td>
            <td width="35%" class="text-right">
                <div style="font-size: 6.5pt; color: #64748b; font-family: monospace;">
                    No. Ref: <strong>SLIP-SHU/{{ $session?->year ?? date('Y') }}/{{ $member?->nomorAnggota ?? $distribution->id }}</strong>
                </div>
                <div style="margin-top: 3px;">
                    @if($distribution->is_disbursed)
                        <span class="badge badge-success">&#10003; SUDAH DICAIRKAN</span>
                    @else
                        <span class="badge badge-pending">&#9203; BELUM DICAIRKAN</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    {{-- DOKUMEN TITLE --}}
    <div class="title-bar">
        <span class="doc-title">SLIP PEMBERITAHUAN &amp; KWITANSI PENCAIRAN SHU</span>
        <span class="doc-subtitle">(RAT TAHUN BUKU {{ $session?->year ?? date('Y') }})</span>
    </div>

    {{-- DATA ANGGOTA --}}
    <div class="meta-box">
        <table>
            <tr>
                <td width="30%">
                    <div class="meta-label">Nomor Anggota (NIK)</div>
                    <div class="meta-val font-mono" style="color: #155A6B;">#{{ $member?->nomorAnggota ?? '-' }}</div>
                </td>
                <td width="42%">
                    <div class="meta-label">Nama Anggota</div>
                    <div class="meta-val">{{ $member?->name ?? '-' }}</div>
                </td>
                <td width="28%">
                    <div class="meta-label">Unit Kerja / Institusi</div>
                    <div class="meta-val">{{ $member?->unitKerja ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2-COLUMN SIDE-BY-SIDE BREAKDOWN --}}
    <table>
        <tr>
            {{-- KOLOM KIRI: A. RINCIAN HAK SHU --}}
            <td width="49%" style="vertical-align: top;">
                <table class="card-table">
                    <thead>
                        <tr>
                            <th colspan="2" style="background-color: #0f172a; color: #ffffff;">A. RINCIAN HAK SHU RAT {{ $session?->year ?? date('Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Jasa Simpanan (Alokasi Modal)</td>
                            <td class="text-right font-mono">Rp {{ number_format((float)$distribution->jasa_simpanan_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Jasa Usaha / Transaksi (Partisipasi)</td>
                            <td class="text-right font-mono">Rp {{ number_format((float)$distribution->jasa_usaha_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="row-highlight-shu">
                            <td>
                                <strong>TOTAL HAK SHU</strong>
                                <div style="font-size: 6pt; color: #047857; font-weight: normal;">Porsi: {{ number_format((float)$distribution->portion_percentage, 4, ',', '.') }}%</div>
                            </td>
                            <td class="text-right font-mono" style="font-size: 9pt;">
                                Rp {{ number_format($rcptShu, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>

            {{-- SPACER --}}
            <td width="2%"></td>

            {{-- KOLOM KANAN: B. POSISI SIMPANAN ANGGOTA --}}
            <td width="49%" style="vertical-align: top;">
                <table class="card-table">
                    <thead>
                        <tr>
                            <th colspan="2" style="background-color: #1e1b4b; color: #ffffff;">B. SALDO SIMPANAN (TERKINI)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Simpanan Pokok</td>
                            <td class="text-right font-mono">Rp {{ number_format($rcptPokok, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Simpanan Wajib</td>
                            <td class="text-right font-mono">Rp {{ number_format($rcptWajib, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="row-highlight-simpanan">
                            <td>
                                <strong>TOTAL SIMPANAN</strong>
                                <div style="font-size: 6pt; color: #4338ca; font-weight: normal;">Pokok + Wajib</div>
                            </td>
                            <td class="text-right font-mono" style="font-size: 9pt;">
                                Rp {{ number_format($rcptTotalSimpanan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- GRAND TOTAL BANNER --}}
    <div class="grand-total-box">
        <table>
            <tr>
                <td width="60%">
                    <div style="font-size: 7.5pt; font-weight: bold; color: #92400e; text-transform: uppercase;">
                        TOTAL PENCAIRAN (HAK SHU + SIMPANAN)
                    </div>
                    <div style="font-size: 6.5pt; color: #78350f;">
                        Kewajiban pembayaran hak anggota dari Koperasi Bermadani
                    </div>
                    @if($rcptSukarela > 0)
                        <div style="font-size: 6.5pt; color: #b45309; font-style: italic; margin-top: 1px;">
                            * Anggota juga memiliki saldo Simpanan Sukarela sebesar <strong>Rp {{ number_format($rcptSukarela, 0, ',', '.') }}</strong> (dapat dicairkan terpisah).
                        </div>
                    @endif
                </td>
                <td width="40%" class="text-right">
                    <span style="font-size: 6.5pt; color: #92400e; display: block;">GRAND TOTAL</span>
                    <span class="font-mono" style="font-size: 13pt; color: #92400e;">
                        Rp {{ number_format($rcptTotalPencairan, 0, ',', '.') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- SIGNATURE SECTION --}}
    <table class="sig-table">
        <tr>
            <td>
                <p style="margin: 0;">Penerima (Anggota),</p>
                <div class="sig-space"></div>
                <div class="sig-name">({{ $member?->name ?? 'Anggota' }})</div>
                <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">NIK: {{ $member?->nomorAnggota ?? '-' }}</div>
            </td>
            <td>
                <p style="margin: 0;">
                    {{ coop_config('city', 'Bandung') }}, {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                    Pengurus / Kasir Koperasi,
                </p>
                <div class="sig-space"></div>
                <div class="sig-name">({{ coop_setting('bendahara_name', 'Muhammad Alwi Almaliki') }})</div>
                <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">{{ coop_setting('bendahara_title', 'Manager Operasional / Bendahara') }}</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER / AUDIT NOTE --}}
    <div class="footer-note">
        Dokumen ini sah dan diterbitkan secara resmi oleh Sistem Informasi {{ coop_config('legal_name') }} &bull; Dicetak pada: {{ $generatedAt }}
    </div>

</body>
</html>
