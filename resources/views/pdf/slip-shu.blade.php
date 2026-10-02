<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BUKTI PENCAIRAN HAK ANGGOTA - {{ $member?->name ?? 'Anggota' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #09090b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Header & Brand Letterhead */
        .header-table td {
            vertical-align: middle;
        }
        .kop-img {
            height: 38px;
            max-width: 100%;
            display: block;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 10.5pt;
            color: #09090b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 7.5pt;
            color: #52525b;
        }
        .header-divider {
            border-bottom: 1.5pt solid #155A6B;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        /* Document Title & Status */
        .title-table {
            margin-bottom: 12px;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 8.5pt;
            color: #52525b;
            margin: 2px 0 0 0;
        }
        .badge-monochrome {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 2px;
            font-size: 7pt;
            font-weight: bold;
            background-color: #f4f4f5;
            color: #18181b;
            border: 0.5pt solid #d4d4d8;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .meta-text-muted {
            font-size: 7pt;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Two-Column Stakeholder Dossier */
        .dossier-container {
            border: 0.5pt solid #e4e4e7;
            border-radius: 3px;
            background-color: #fafafa;
            margin-bottom: 14px;
            overflow: hidden;
        }
        .dossier-table td {
            padding: 8px 12px;
            vertical-align: top;
        }
        .dossier-label {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #71717a;
            margin-bottom: 3px;
        }
        .dossier-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #09090b;
            margin-bottom: 2px;
        }
        .dossier-detail {
            font-size: 7.5pt;
            color: #52525b;
            line-height: 1.3;
        }

        /* Hero Total Statement Box */
        .hero-statement {
            border-top: 1.5pt solid #18181b;
            border-bottom: 1.5pt solid #18181b;
            background-color: #fafafa;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .hero-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #18181b;
        }
        .hero-terbilang {
            font-size: 8pt;
            color: #52525b;
            font-style: italic;
            margin-top: 3px;
            line-height: 1.3;
        }
        .hero-amount {
            font-size: 16pt;
            font-weight: bold;
            color: #09090b;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: -0.5px;
            text-align: right;
        }
        .hero-sukarela-note {
            font-size: 7pt;
            color: #71717a;
            margin-top: 4px;
            border-top: 0.5pt dashed #d4d4d8;
            padding-top: 3px;
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
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 10px;
            border-bottom: 0.5pt solid #e4e4e7;
        }
        .ledger-table {
            font-size: 8pt;
        }
        .ledger-table td {
            padding: 6px 10px;
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
            padding-top: 7px;
            padding-bottom: 7px;
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

        /* Legal & Institutional Governance Notes */
        .legal-box {
            border: 0.5pt solid #e4e4e7;
            border-radius: 3px;
            background-color: #fafafa;
            padding: 8px 12px;
            margin-top: 14px;
            margin-bottom: 14px;
        }
        .legal-header {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #52525b;
            margin-bottom: 4px;
        }
        .legal-list {
            margin: 0;
            padding-left: 14px;
            font-size: 6.8pt;
            color: #71717a;
            line-height: 1.4;
        }
        .legal-list li {
            margin-bottom: 2px;
        }

        /* Verification & Signatures Block */
        .signature-table {
            width: 100%;
            margin-top: 10px;
        }
        .verification-box {
            border: 0.5pt solid #d4d4d8;
            border-radius: 2px;
            padding: 8px 10px;
            background-color: #ffffff;
            display: inline-block;
            width: 95%;
            text-align: left;
        }
        .verification-title {
            font-size: 6.5pt;
            font-weight: bold;
            color: #18181b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .verification-hash {
            font-family: 'Courier New', Courier, monospace;
            font-size: 7.5pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .verification-meta {
            font-size: 6pt;
            color: #71717a;
            line-height: 1.3;
        }

        .signature-space {
            height: 48px;
        }
        .signature-line {
            font-size: 8.5pt;
            font-weight: bold;
            color: #09090b;
            border-top: 0.5pt solid #a1a1aa;
            display: inline-block;
            padding-top: 3px;
            min-width: 180px;
        }
        .signature-role {
            font-size: 7pt;
            color: #71717a;
            margin-top: 2px;
        }

        /* Microcopy Footer */
        .audit-footer {
            margin-top: 18px;
            border-top: 0.5pt solid #e4e4e7;
            padding-top: 6px;
            font-size: 6pt;
            color: #a1a1aa;
            text-align: center;
            letter-spacing: 0.2px;
            line-height: 1.4;
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
                    No. Referensi: <strong style="color: #09090b; font-family: 'Courier New', monospace;">{{ $refNumber ?? ('SLIP/' . ($session?->year ?? date('Y')) . '/' . ($member?->nomorAnggota ?? $distribution->id)) }}</strong>
                </div>
                <div class="meta-text-muted" style="margin-top: 2px;">
                    Tanggal Terbit: <strong style="color: #09090b;">{{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>
                </div>
                <div class="meta-text-muted" style="margin-top: 2px;">
                    Tahun Buku RAT: <strong style="color: #09090b;">{{ $session?->year ?? date('Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>

    {{-- JUDUL DOKUMEN & BADGE ARSIP --}}
    <table class="title-table">
        <tr>
            <td>
                <h1 class="doc-title">BUKTI PENCAIRAN HAK ANGGOTA</h1>
                <p class="doc-subtitle">Rapat Anggota Tahunan (RAT) Tahun Buku {{ $session?->year ?? date('Y') }}</p>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                @if(($slipType ?? 'member') === 'coop')
                    <span class="badge-monochrome">
                        LEMBAR 1 &bull; ARSIP KOPERASI
                    </span>
                @else
                    <span class="badge-monochrome">
                        LEMBAR 2 &bull; ARSIP ANGGOTA
                    </span>
                @endif
                <div class="meta-text-muted" style="margin-top: 3px;">
                    @if($distribution->is_disbursed)
                        <span style="color: #155A6B;">&bull;</span> <strong>Status: Terverifikasi & Dicairkan</strong>
                    @else
                        <span style="color: #a1a1aa;">&bull;</span> Status: Menunggu Pencairan
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- DOSSIER 2 PIHAK (KOPERASI & ANGGOTA) --}}
    <div class="dossier-container">
        <table class="dossier-table">
            <tr>
                {{-- PIHAK PENYERAH / KOPERASI --}}
                <td width="50%" style="border-right: 0.5pt solid #e4e4e7;">
                    <div class="dossier-label">Pihak Pemberi Dana (Koperasi)</div>
                    <div class="dossier-name">{{ coop_config('legal_name') }}</div>
                    <div class="dossier-detail">
                        {{ coop_config('address') }}<br>
                        Badan Hukum: No. AHU-0001234.AH.01.26 / Kemenkop UKM
                    </div>
                </td>
                {{-- PIHAK PENERIMA / ANGGOTA --}}
                <td width="50%">
                    <div class="dossier-label">Pihak Penerima Hak (Anggota)</div>
                    <div class="dossier-name">{{ $member?->name ?? 'Anggota' }}</div>
                    <div class="dossier-detail">
                        Nomor Anggota: <strong class="font-mono">#{{ $member?->nomorAnggota ?? '-' }}</strong><br>
                        Status Keanggotaan: <span style="color: #09090b; font-weight: bold;">Anggota Aktif Koperasi</span>
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
                    <div class="hero-title">Total Dana Pencairan Hak</div>
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

    {{-- KETENTUAN & DASAR HUKUM RAT (INSTITUTIONAL GOVERNANCE) --}}
    <div class="legal-box">
        <div class="legal-header">Dasar Ketentuan &amp; Pengesahan Dokumen</div>
        <ol class="legal-list">
            <li>Dokumen ini merupakan bukti sah penyerahan hak keuangan Anggota atas pelaksanaan Rapat Anggota Tahunan (RAT) {{ coop_config('legal_name') }} Tahun Buku {{ $session?->year ?? date('Y') }}.</li>
            <li>Alokasi pembagian Sisa Hasil Usaha (SHU) dan perhitungan saldo simpanan telah diverifikasi sesuai Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART) Koperasi yang berlaku.</li>
            <li>Tanda terima ini mengikat kedua belah pihak sebagai instrumen pertanggungjawaban yuridis dan pembukuan resmi Koperasi.</li>
        </ol>
    </div>

    {{-- VERIFIKASI DIGITAL & TANDA TANGAN RESMI --}}
    <table class="signature-table">
        <tr>
            {{-- OTENTIKASI SISTEM --}}
            <td width="50%" style="vertical-align: top; text-align: left;">
                <div class="verification-box">
                    <div class="verification-title">Otentikasi Digital Sistem</div>
                    <div class="verification-hash">{{ $verificationHash ?? strtoupper(substr(md5($distribution->id . ($distribution->created_at ?? now())), 0, 16)) }}</div>
                    <div class="verification-meta">
                        Dokumen tercatat resmi pada Buku Kas &amp; Ledger RAT.<br>
                        Waktu Terbit: {{ $generatedAt }}<br>
                        Sistem Informasi: {{ coop_config('short_name', 'Bermadani') }} Core v1.0
                    </div>
                </div>
            </td>

            {{-- TANDA TANGAN PENGESAHAN --}}
            <td width="50%" style="vertical-align: top; text-align: right;">
                @if(($slipType ?? 'member') === 'coop')
                    <div style="display: inline-block; text-align: center; min-width: 180px;">
                        <p style="margin: 0; font-size: 8pt; color: #3f3f46;">
                            {{ coop_config('city', 'Bandung') }}, {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                            Yang Menerima,
                        </p>
                        <div class="signature-space"></div>
                        <div class="signature-line">({{ $member?->name ?? 'Anggota' }})</div>
                        <div class="signature-role font-mono">No. Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
                    </div>
                @else
                    <div style="display: inline-block; text-align: center; min-width: 180px;">
                        <p style="margin: 0; font-size: 8pt; color: #3f3f46;">
                            {{ coop_config('city', 'Bandung') }}, {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                            Diserahkan oleh,
                        </p>
                        @if(!empty($sigBase64))
                            <div style="height: 44px; margin: 2px 0;">
                                <img src="{{ $sigBase64 }}" style="height: 44px; max-width: 150px; display: inline-block; vertical-align: middle;">
                            </div>
                        @else
                            <div class="signature-space"></div>
                        @endif
                        <div class="signature-line">({{ coop_setting('bendahara_name', 'M. Reyvan Purnama') }})</div>
                        <div class="signature-role">{{ coop_setting('bendahara_title', 'Manajer Operasional') }}</div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- MICROCOPY FOOTER --}}
    <div class="audit-footer">
        {{ coop_config('legal_name') }} &bull; {{ coop_config('address') }} &bull; {{ coop_config('website', 'koperasi.umbandung.ac.id') }}<br>
        Dokumen resmi diterbitkan secara elektronik dan sah tanpa memerlukan stempel fisik tambahan apabila telah tervalidasi oleh sistem. (Halaman 1 dari 1)
    </div>

</body>
</html>
