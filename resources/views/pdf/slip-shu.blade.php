<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SLIP PENCAIRAN SHU DAN SIMPANAN - {{ $member?->name ?? 'Anggota' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 20mm 20mm 18mm 20mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #09090b;
            line-height: 1.45;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Kop Surat & Identitas Penerbit Dokumen */
        .header-table td {
            vertical-align: middle;
        }
        .kop-img {
            height: 44px;
            max-width: 100%;
            display: block;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 11pt;
            color: #09090b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #52525b;
        }
        .header-divider {
            border-bottom: 1.5pt solid #155A6B;
            margin-top: 14px;
            margin-bottom: 22px;
        }

        /* Document Title & Reference */
        .title-table {
            margin-bottom: 24px;
        }
        .doc-title {
            font-size: 14.5pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 9pt;
            color: #52525b;
            margin: 3px 0 0 0;
        }
        .badge-monochrome {
            display: inline-block;
            padding: 3.5px 9px;
            border-radius: 2px;
            font-size: 7.5pt;
            font-weight: bold;
            background-color: #f4f4f5;
            color: #18181b;
            border: 0.5pt solid #d4d4d8;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .meta-text-muted {
            font-size: 7.5pt;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Member Dossier (Fokus Penerima Hak) */
        .member-dossier {
            border: 0.5pt solid #e4e4e7;
            border-radius: 3px;
            background-color: #fafafa;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .dossier-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #71717a;
            margin-bottom: 3px;
        }
        .member-name {
            font-size: 11pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.2px;
        }
        .member-no {
            font-size: 8.5pt;
            color: #52525b;
            margin-top: 1px;
        }

        /* Hero Total Statement Box (High-Contrast Apple HIG Anchor) */
        .hero-statement {
            border-top: 1.5pt solid #18181b;
            border-bottom: 1.5pt solid #18181b;
            background-color: #fafafa;
            padding: 16px 18px;
            margin-bottom: 26px;
        }
        .hero-title {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #18181b;
        }
        .hero-terbilang {
            font-size: 8.5pt;
            color: #52525b;
            font-style: italic;
            margin-top: 4px;
            line-height: 1.35;
        }
        .hero-amount {
            font-size: 19pt;
            font-weight: bold;
            color: #09090b;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: -0.5px;
            text-align: right;
        }
        .hero-sukarela-note {
            font-size: 7.5pt;
            color: #71717a;
            margin-top: 6px;
            border-top: 0.5pt dashed #d4d4d8;
            padding-top: 5px;
        }

        /* Financial Ledger Panels */
        .ledger-panel {
            border: 0.5pt solid #e4e4e7;
            border-radius: 3px;
            overflow: hidden;
        }
        .ledger-panel-header {
            background-color: #f4f4f5;
            color: #18181b;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 9px 14px;
            border-bottom: 0.5pt solid #e4e4e7;
        }
        .ledger-table {
            font-size: 8.5pt;
        }
        .ledger-table td {
            padding: 9px 14px;
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
            padding-top: 10px;
            padding-bottom: 10px;
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

        /* Quiet Authority & Execution Metadata (Apple HIG Minimalist) */
        .auth-table {
            width: 100%;
            margin-top: 44px;
            border-top: 0.5pt solid #e4e4e7;
            padding-top: 18px;
        }
        .auth-table td {
            vertical-align: top;
        }
        .auth-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #71717a;
            margin-bottom: 4px;
        }
        .auth-value {
            font-size: 9.5pt;
            font-weight: bold;
            color: #09090b;
        }
        .auth-sub {
            font-size: 7.5pt;
            color: #71717a;
            margin-top: 2px;
        }

        /* Clean 1-Line Footer */
        .audit-footer {
            margin-top: 60px;
            border-top: 0.5pt solid #e4e4e7;
            padding-top: 12px;
            font-size: 7pt;
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
                    No. Dokumen: <strong style="color: #09090b; font-family: 'Courier New', monospace;">{{ $refNumber ?? ('SLIP/' . ($session?->year ?? date('Y')) . '/' . ($member?->nomorAnggota ?? $distribution->id)) }}</strong>
                </div>
                <div class="meta-text-muted" style="margin-top: 3px;">
                    Tahun Buku RAT: <strong style="color: #09090b;">{{ $session?->year ?? date('Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>

    {{-- JUDUL DOKUMEN & BADGE RESMI --}}
    <table class="title-table">
        <tr>
            <td>
                <h1 class="doc-title">SLIP PENCAIRAN SHU DAN SIMPANAN</h1>
                <p class="doc-subtitle">Rapat Anggota Tahunan (RAT) Tahun Buku {{ $session?->year ?? date('Y') }}</p>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <span class="badge-monochrome">
                    BUKTI TRANSAKSI ELEKTRONIK
                </span>
            </td>
        </tr>
    </table>

    {{-- DOSSIER PENERIMA HAK (ANGGOTA) --}}
    <div class="member-dossier">
        <table>
            <tr>
                <td width="60%">
                    <div class="dossier-label">Penerima Hak (Anggota)</div>
                    <div class="member-name">{{ $member?->name ?? 'Anggota' }}</div>
                    <div class="member-no font-mono">Nomor Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: middle;">
                    <div class="dossier-label">Status Keanggotaan</div>
                    <div style="font-size: 8.5pt; font-weight: bold; color: #09090b;">Anggota Aktif Koperasi</div>
                    <div style="font-size: 7.5pt; color: #71717a; margin-top: 2px;">
                        Tanggal: {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- HERO TOTAL PENCAIRAN (APPLE HIG STATEMENT HIGHLIGHT) --}}
    <div class="hero-statement">
        <table>
            <tr>
                <td width="55%" style="vertical-align: middle;">
                    <div class="hero-title">Total Pencairan</div>
                    <div class="hero-terbilang">
                        Terbilang: &ldquo;{{ $terbilang ?? (terbilang_id($rcptTotalPencairan) . ' Rupiah') }}&rdquo;
                    </div>
                    @if($rcptSukarela > 0)
                        <div class="hero-sukarela-note">
                            * Saldo Simpanan Sukarela: <strong>Rp {{ number_format($rcptSukarela, 0, ',', '.') }}</strong> (tetap tersimpan aman di rekening Koperasi)
                        </div>
                    @endif
                </td>
                <td width="45%" class="text-right" style="vertical-align: middle;">
                    <div class="hero-amount">
                        Rp {{ number_format($rcptTotalPencairan, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2-COLUMN FINANCIAL LEDGER --}}
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
                                <div style="font-size: 6.5pt; font-weight: normal; color: #71717a;">Porsi Kontribusi: {{ number_format((float)$distribution->portion_percentage, 4, ',', '.') }}%</div>
                            </td>
                            <td class="text-right font-mono" style="font-size: 9pt;">
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
                            <td>
                                <div>Subtotal Simpanan</div>
                                <div style="font-size: 6.5pt; font-weight: normal; color: #71717a;">Pokok &amp; Wajib Diperhitungkan</div>
                            </td>
                            <td class="text-right font-mono" style="font-size: 9pt;">
                                Rp {{ number_format($rcptTotalSimpanan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- OTORISASI OPERASIONAL & EKSEKUSI (QUIET CONFIDENCE) --}}
    <table class="auth-table">
        <tr>
            <td width="55%">
                <div class="auth-label">Otorisasi Koperasi</div>
                <div class="auth-value">{{ coop_setting('bendahara_name', 'M. Reyvan Purnama') }}</div>
                <div class="auth-sub">{{ coop_setting('bendahara_title', 'Manajer Operasional') }} &bull; {{ coop_config('legal_name') }}</div>
            </td>
            <td width="45%" class="text-right">
                <div class="auth-label">Status &bull; Waktu Transaksi</div>
                <div class="auth-value" style="font-size: 8.5pt;">
                    @if($distribution->is_disbursed)
                        <span style="color: #155A6B;">&bull; Berhasil Dicairkan</span>
                    @else
                        <span style="color: #71717a;">&bull; Menunggu Pencairan</span>
                    @endif
                </div>
                <div class="auth-sub font-mono">
                    {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y, H:i') . ' WIB' : now()->translatedFormat('d F Y, H:i') . ' WIB' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- CLEAN 1-LINE MICROCOPY FOOTER --}}
    <div class="audit-footer">
        Dokumen resmi diterbitkan secara elektronik oleh Sistem Informasi {{ coop_config('legal_name') }}. Sah tanpa memerlukan tanda tangan basah.
    </div>

</body>
</html>
