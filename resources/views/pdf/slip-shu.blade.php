<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SLIP PENCAIRAN SHU DAN SIMPANAN - {{ $member?->name ?? 'Anggota' }}</title>
    <style>
        @page {
            size: a5 landscape;
            margin: 7mm 11mm 6mm 11mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #09090b;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Header & Brand */
        .header-table td {
            vertical-align: middle;
        }
        .kop-img {
            height: 34px;
            max-width: 100%;
            display: block;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 9.5pt;
            color: #09090b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .kop-text p {
            margin: 1px 0 0 0;
            font-size: 7pt;
            color: #71717a;
        }
        .header-divider {
            border-bottom: 1.5px solid #155A6B;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        /* Title Area */
        .title-table {
            margin-bottom: 6px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 7.5pt;
            color: #71717a;
            margin: 1px 0 0 0;
        }

        /* Status & Metadata Tags (Apple HIG: Quiet & Deferent) */
        .meta-text-muted {
            font-size: 6.5pt;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .status-dot-active {
            color: #155A6B;
            font-size: 8pt;
            margin-right: 2px;
        }
        .status-dot-pending {
            color: #a1a1aa;
            font-size: 8pt;
            margin-right: 2px;
        }

        /* Member Info Strip */
        .member-strip {
            background-color: #fafafa;
            border-top: 0.5pt solid #e4e4e7;
            border-bottom: 0.5pt solid #e4e4e7;
            padding: 4px 6px;
            margin-bottom: 8px;
        }
        .member-strip td {
            font-size: 7.5pt;
            padding: 1px 4px;
            vertical-align: top;
        }
        .field-label {
            font-size: 6pt;
            font-weight: 600;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }
        .field-value {
            font-size: 7.5pt;
            font-weight: bold;
            color: #09090b;
        }

        /* Ledger Tables */
        .ledger-panel {
            border: 0.5pt solid #e4e4e7;
            border-radius: 2px;
            overflow: hidden;
        }
        .ledger-panel-header {
            background-color: #f4f4f5;
            color: #18181b;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3.5px 6px;
            border-bottom: 0.5pt solid #e4e4e7;
        }
        .ledger-table {
            font-size: 7.5pt;
        }
        .ledger-table td {
            padding: 3.5px 6px;
            border-bottom: 0.5pt solid #f4f4f5;
            color: #27272a;
        }
        .ledger-table tr:last-child td {
            border-bottom: none;
        }
        .ledger-subtotal-row {
            background-color: #fafafa;
            border-top: 0.5pt solid #d4d4d8 !important;
        }
        .ledger-subtotal-row td {
            font-weight: bold;
            color: #09090b;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        /* Tabular Numbers */
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            font-variant-numeric: tabular-nums;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* Grand Total Section (Apple HIG: High Contrast, No Neon Clutter) */
        .grand-total-container {
            border-top: 1pt solid #18181b;
            border-bottom: 1pt solid #18181b;
            padding: 5px 6px;
            margin-top: 7px;
            margin-bottom: 7px;
            background-color: #fafafa;
        }
        .grand-total-title {
            font-size: 8pt;
            font-weight: bold;
            color: #09090b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grand-total-desc {
            font-size: 6.5pt;
            color: #71717a;
            margin-top: 1px;
        }
        .grand-total-amount {
            font-size: 13pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: -0.3px;
        }
        .sukarela-note {
            font-size: 6.5pt;
            color: #52525b;
            font-style: italic;
            margin-top: 2px;
        }

        /* Signatures Block */
        .signature-table {
            width: 100%;
            margin-top: 6px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 7pt;
            color: #52525b;
        }
        .signature-space {
            height: 34px;
        }
        .signature-line {
            font-size: 7.5pt;
            font-weight: bold;
            color: #09090b;
            border-top: 0.5pt solid #a1a1aa;
            display: inline-block;
            padding-top: 2px;
            min-width: 160px;
        }
        .signature-role {
            font-size: 6pt;
            color: #71717a;
            margin-top: 1px;
        }

        /* Microcopy Footer */
        .audit-footer {
            margin-top: 5px;
            border-top: 0.5pt solid #e4e4e7;
            padding-top: 2px;
            font-size: 5.5pt;
            color: #a1a1aa;
            text-align: center;
            letter-spacing: 0.2px;
        }
    </style>
</head>
<body>

    {{-- KOP IDENTITAS KOPERASI --}}
    <table class="header-table">
        <tr>
            <td width="65%">
                @if(!empty($kopBase64))
                    <img src="{{ $kopBase64 }}" class="kop-img" alt="{{ coop_config('name') }}">
                @else
                    <div class="kop-text">
                        <h2>{{ coop_config('legal_name') }}</h2>
                        <p>{{ coop_config('parent_org') }} &bull; {{ coop_config('address') }}</p>
                    </div>
                @endif
            </td>
            <td width="35%" class="text-right">
                <div class="meta-text-muted">
                    No. Dokumen: <strong style="color: #09090b; font-family: 'Courier New', monospace;">SLIP/SHU/{{ $session?->year ?? date('Y') }}/{{ $member?->nomorAnggota ?? $distribution->id }}</strong>
                </div>
                <div style="font-size: 6.5pt; color: #52525b; margin-top: 2px;">
                    @if($distribution->is_disbursed)
                        <span class="status-dot-active">&bull;</span> <strong>Sudah Dicairkan</strong>
                        @if($distribution->disbursed_at)
                            <span style="color: #71717a;">({{ $distribution->disbursed_at->format('d/m/Y') }})</span>
                        @endif
                    @else
                        <span class="status-dot-pending">&bull;</span> <span style="color: #71717a;">Menunggu Pencairan</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>

    {{-- JUDUL RESMI DOKUMEN --}}
    <table class="title-table">
        <tr>
            <td>
                <h1 class="doc-title">SLIP PENCAIRAN SHU DAN SIMPANAN</h1>
                <p class="doc-subtitle">Rapat Anggota Tahunan (RAT) Tahun Buku {{ $session?->year ?? date('Y') }}</p>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <span class="meta-text-muted">Tanggal Cetak: {{ $generatedAt }}</span>
            </td>
        </tr>
    </table>

    {{-- DATA ANGGOTA --}}
    <div class="member-strip">
        <table>
            <tr>
                <td width="28%">
                    <div class="field-label">Nomor Anggota (NIK)</div>
                    <div class="field-value font-mono">#{{ $member?->nomorAnggota ?? '-' }}</div>
                </td>
                <td width="42%">
                    <div class="field-label">Nama Lengkap Anggota</div>
                    <div class="field-value">{{ $member?->name ?? '-' }}</div>
                </td>
                <td width="30%">
                    <div class="field-label">Unit Kerja / Institusi</div>
                    <div class="field-value">{{ $member?->unitKerja ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2-COLUMN UNIFIED FINANCIAL LEDGER --}}
    <table>
        <tr>
            {{-- KOLOM KIRI: I. HAK SISA HASIL USAHA (SHU) --}}
            <td width="49%" style="vertical-align: top;">
                <div class="ledger-panel">
                    <div class="ledger-panel-header">
                        I. Hak Sisa Hasil Usaha (SHU) RAT {{ $session?->year ?? date('Y') }}
                    </div>
                    <table class="ledger-table">
                        <tr>
                            <td>Jasa Simpanan (Alokasi Modal)</td>
                            <td class="text-right font-mono">Rp {{ number_format((float)$distribution->jasa_simpanan_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Jasa Usaha / Transaksi (Partisipasi)</td>
                            <td class="text-right font-mono">Rp {{ number_format((float)$distribution->jasa_usaha_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="ledger-subtotal-row">
                            <td>
                                <div>Subtotal Hak SHU</div>
                                <div style="font-size: 5.5pt; font-weight: normal; color: #71717a;">Porsi Alokasi: {{ number_format((float)$distribution->portion_percentage, 4, ',', '.') }}%</div>
                            </td>
                            <td class="text-right font-mono" style="font-size: 8.5pt;">
                                Rp {{ number_format($rcptShu, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            {{-- SPACER --}}
            <td width="2%"></td>

            {{-- KOLOM KANAN: II. SALDO SIMPANAN ANGGOTA --}}
            <td width="49%" style="vertical-align: top;">
                <div class="ledger-panel">
                    <div class="ledger-panel-header">
                        II. Saldo Simpanan Anggota (Terkini)
                    </div>
                    <table class="ledger-table">
                        <tr>
                            <td>Simpanan Pokok</td>
                            <td class="text-right font-mono">Rp {{ number_format($rcptPokok, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Simpanan Wajib</td>
                            <td class="text-right font-mono">Rp {{ number_format($rcptWajib, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="ledger-subtotal-row">
                            <td>
                                <div>Subtotal Simpanan</div>
                                <div style="font-size: 5.5pt; font-weight: normal; color: #71717a;">Akumulasi Pokok &amp; Wajib</div>
                            </td>
                            <td class="text-right font-mono" style="font-size: 8.5pt;">
                                Rp {{ number_format($rcptTotalSimpanan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- GRAND TOTAL PENCAIRAN --}}
    <div class="grand-total-container">
        <table>
            <tr>
                <td width="58%" style="vertical-align: middle;">
                    <div class="grand-total-title">Total Pencairan (Hak SHU + Simpanan)</div>
                    <div class="grand-total-desc">Jumlah hak bersih yang dibayarkan kepada anggota bersangkutan.</div>
                    @if($rcptSukarela > 0)
                        <div class="sukarela-note">
                            * Saldo Simpanan Sukarela: <strong>Rp {{ number_format($rcptSukarela, 0, ',', '.') }}</strong> (dapat dicairkan terpisah).
                        </div>
                    @endif
                </td>
                <td width="42%" class="text-right" style="vertical-align: middle;">
                    <span class="meta-text-muted" style="display: block; margin-bottom: 2px;">JUMLAH TOTAL BERSIH</span>
                    <span class="grand-total-amount font-mono">
                        Rp {{ number_format($rcptTotalPencairan, 0, ',', '.') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- AREA TANDA TANGAN RESMI --}}
    <table class="signature-table">
        <tr>
            <td>
                <p style="margin: 0;">Penerima (Anggota),</p>
                <div class="signature-space"></div>
                <div class="signature-line">({{ $member?->name ?? 'Anggota' }})</div>
                <div class="signature-role font-mono">NIK: #{{ $member?->nomorAnggota ?? '-' }}</div>
            </td>
            <td>
                <p style="margin: 0;">
                    {{ coop_config('city', 'Bandung') }}, {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                    Pengurus / Bendahara Koperasi,
                </p>
                <div class="signature-space"></div>
                <div class="signature-line">({{ coop_setting('bendahara_name', 'Muhammad Alwi Almaliki') }})</div>
                <div class="signature-role">{{ coop_setting('bendahara_title', 'Bendahara Koperasi') }}</div>
            </td>
        </tr>
    </table>

    {{-- AUDIT FOOTNOTE --}}
    <div class="audit-footer">
        Dokumen ini sah dan diterbitkan secara digital oleh Sistem Informasi {{ coop_config('legal_name') }} &bull; Ref ID: SLIP-{{ $distribution->id }}-{{ substr(md5($distribution->id . ($distribution->created_at ?? now())), 0, 8) }}
    </div>

</body>
</html>
