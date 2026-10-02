<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SLIP PENCAIRAN SHU DAN SIMPANAN - {{ $member?->name ?? 'Anggota' }}</title>
    <style>
        @page {
            size: a5 landscape;
            margin: 8mm 12mm 6mm 12mm;
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
            height: 32px;
            max-width: 100%;
            display: block;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 9pt;
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
            margin-bottom: 7px;
        }

        /* Document Title */
        .title-table {
            margin-bottom: 7px;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 7.5pt;
            color: #71717a;
            margin: 1px 0 0 0;
        }
        .meta-text-muted {
            font-size: 6.5pt;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Member Info Strip (Clean 2-Column Focus, No Unknown placeholders) */
        .member-strip {
            background-color: #fafafa;
            border-top: 0.5pt solid #e4e4e7;
            border-bottom: 0.5pt solid #e4e4e7;
            padding: 5px 8px;
            margin-bottom: 8px;
        }
        .member-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.2px;
        }
        .member-no {
            font-size: 7.5pt;
            color: #52525b;
            margin-top: 1px;
        }
        .status-dot-active {
            color: #155A6B;
            font-size: 7.5pt;
            margin-right: 2px;
        }
        .status-dot-pending {
            color: #a1a1aa;
            font-size: 7.5pt;
            margin-right: 2px;
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
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 7px;
            border-bottom: 0.5pt solid #e4e4e7;
        }
        .ledger-table {
            font-size: 7.5pt;
        }
        .ledger-table td {
            padding: 4px 7px;
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
            padding-top: 4.5px;
            padding-bottom: 4.5px;
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

        /* Grand Total Section (Apple HIG: Bold Hero Contrast, Zero Fluff) */
        .grand-total-container {
            border-top: 1pt solid #18181b;
            border-bottom: 1pt solid #18181b;
            padding: 6px 8px;
            margin-top: 8px;
            margin-bottom: 8px;
            background-color: #fafafa;
        }
        .grand-total-title {
            font-size: 9pt;
            font-weight: bold;
            color: #09090b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grand-total-amount {
            font-size: 13.5pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: -0.3px;
        }
        .sukarela-note {
            font-size: 6.5pt;
            color: #71717a;
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
            height: 35px;
        }
        .signature-line {
            font-size: 7.5pt;
            font-weight: bold;
            color: #09090b;
            border-top: 0.5pt solid #a1a1aa;
            display: inline-block;
            padding-top: 2px;
            min-width: 170px;
        }
        .signature-role {
            font-size: 6.5pt;
            color: #71717a;
            margin-top: 1px;
        }

        /* Microcopy Footer */
        .audit-footer {
            margin-top: 6px;
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
            <td width="60%">
                @if(!empty($kopBase64))
                    <img src="{{ $kopBase64 }}" class="kop-img" alt="{{ coop_config('name') }}">
                @else
                    <div class="kop-text">
                        <h2>{{ coop_config('legal_name') }}</h2>
                        <p>{{ coop_config('parent_org') }} &bull; {{ coop_config('address') }}</p>
                    </div>
                @endif
            </td>
            <td width="40%" class="text-right">
                <div class="meta-text-muted">
                    No. Ref: <strong style="color: #09090b; font-family: 'Courier New', monospace;">SLIP/{{ $session?->year ?? date('Y') }}/{{ $member?->nomorAnggota ?? $distribution->id }}</strong>
                </div>
                <div class="meta-text-muted" style="margin-top: 2px;">
                    Tanggal: <strong style="color: #09090b;">{{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>

    {{-- JUDUL DOKUMEN --}}
    <table class="title-table">
        <tr>
            <td>
                <h1 class="doc-title">SLIP PENCAIRAN SHU DAN SIMPANAN</h1>
                <p class="doc-subtitle">Rapat Anggota Tahunan (RAT) Tahun Buku {{ $session?->year ?? date('Y') }}</p>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <span class="meta-text-muted">Waktu Cetak: {{ $generatedAt }}</span>
            </td>
        </tr>
    </table>

    {{-- DATA PENERIMA (Zero Fluff, Tanpa Unknown) --}}
    <div class="member-strip">
        <table>
            <tr>
                <td width="60%">
                    <div class="member-name">{{ $member?->name ?? 'Anggota' }}</div>
                    <div class="member-no font-mono">No. Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: middle;">
                    <div style="font-size: 7pt; color: #09090b;">
                        @if($distribution->is_disbursed)
                            <span class="status-dot-active">&bull;</span> <strong>Sudah Dicairkan</strong>
                        @else
                            <span class="status-dot-pending">&bull;</span> <span style="color: #71717a;">Menunggu Pencairan</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2-COLUMN UNIFIED FINANCIAL LEDGER --}}
    <table>
        <tr>
            {{-- KOLOM KIRI: I. HAK SHU --}}
            <td width="49%" style="vertical-align: top;">
                <div class="ledger-panel">
                    <div class="ledger-panel-header">
                        I. Hak Sisa Hasil Usaha (SHU)
                    </div>
                    <table class="ledger-table">
                        <tr>
                            <td>Jasa Modal (Simpanan)</td>
                            <td class="text-right font-mono">Rp {{ number_format((float)$distribution->jasa_simpanan_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Jasa Usaha (Transaksi)</td>
                            <td class="text-right font-mono">Rp {{ number_format((float)$distribution->jasa_usaha_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="ledger-subtotal-row">
                            <td>
                                <div>Subtotal Hak SHU</div>
                                <div style="font-size: 5.5pt; font-weight: normal; color: #71717a;">Porsi: {{ number_format((float)$distribution->portion_percentage, 4, ',', '.') }}%</div>
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

            {{-- KOLOM KANAN: II. SALDO SIMPANAN --}}
            <td width="49%" style="vertical-align: top;">
                <div class="ledger-panel">
                    <div class="ledger-panel-header">
                        II. Saldo Simpanan (Terkini)
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
                            <td>Subtotal Simpanan</td>
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
                <td width="55%" style="vertical-align: middle;">
                    <div class="grand-total-title">Total Pencairan</div>
                    @if($rcptSukarela > 0)
                        <div class="sukarela-note">
                            * Saldo Simpanan Sukarela: <strong>Rp {{ number_format($rcptSukarela, 0, ',', '.') }}</strong> (tersimpan terpisah)
                        </div>
                    @endif
                </td>
                <td width="45%" class="text-right" style="vertical-align: middle;">
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
                <div class="signature-role font-mono">No. Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
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
        Dokumen digital resmi diterbitkan oleh Sistem Informasi {{ coop_config('legal_name') }} &bull; Ref ID: SLIP-{{ $distribution->id }}-{{ substr(md5($distribution->id . ($distribution->created_at ?? now())), 0, 8) }}
    </div>

</body>
</html>
