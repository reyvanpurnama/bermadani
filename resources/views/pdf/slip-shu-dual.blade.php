<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SLIP PENCAIRAN 2 RANGKAP (A4) - {{ $member?->name ?? 'Anggota' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 6mm 10mm 6mm 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7.5pt;
            color: #09090b;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .slip-half {
            height: 136mm;
            max-height: 138mm;
            overflow: hidden;
            position: relative;
        }

        /* Kop & Brand */
        .kop-text h2 {
            margin: 0;
            font-size: 8.5pt;
            color: #09090b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .kop-text p {
            margin: 1px 0 0 0;
            font-size: 6.5pt;
            color: #71717a;
        }
        .header-divider {
            border-bottom: 1.2pt solid #155A6B;
            margin-top: 3px;
            margin-bottom: 5px;
        }

        /* Title */
        .doc-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #09090b;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 7pt;
            color: #71717a;
            margin: 1px 0 0 0;
        }
        .meta-text-muted {
            font-size: 6pt;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Member Strip */
        .member-strip {
            background-color: #fafafa;
            border-top: 0.5pt solid #e4e4e7;
            border-bottom: 0.5pt solid #e4e4e7;
            padding: 4px 6px;
            margin-bottom: 6px;
        }
        .member-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #09090b;
        }
        .member-no {
            font-size: 7pt;
            color: #52525b;
            margin-top: 1px;
        }
        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 9999px;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .badge-coop {
            background-color: #f1f5f9;
            color: #334155;
            border: 0.5pt solid #cbd5e1;
        }
        .badge-member {
            background-color: #ecfdf5;
            color: #047857;
            border: 0.5pt solid #a7f3d0;
        }

        /* Ledger Table */
        .ledger-col-table {
            margin-bottom: 5px;
        }
        .ledger-cell-left {
            padding-right: 5px;
            vertical-align: top;
        }
        .ledger-cell-right {
            padding-left: 5px;
            vertical-align: top;
        }
        .data-table {
            border: 0.5pt solid #e4e4e7;
        }
        .data-table th {
            background-color: #f8fafc;
            border-bottom: 0.5pt solid #e4e4e7;
            padding: 3px 6px;
            font-size: 6.5pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
        }
        .data-table td {
            padding: 3px 6px;
            font-size: 7pt;
            border-bottom: 0.5pt solid #f1f5f9;
        }
        .data-table tr.total-row td {
            border-top: 0.5pt solid #cbd5e1;
            border-bottom: none;
            background-color: #fafafa;
            font-weight: bold;
            color: #09090b;
        }

        /* Grand Total */
        .grand-total-box {
            border-top: 1.2pt solid #09090b;
            border-bottom: 0.5pt solid #e4e4e7;
            padding: 4px 6px;
            margin-bottom: 6px;
            background-color: #ffffff;
        }
        .grand-total-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #09090b;
        }
        .grand-total-amount {
            font-size: 11pt;
            font-weight: bold;
            color: #09090b;
        }

        /* Signatures */
        .signature-table td {
            vertical-align: top;
        }
        .signature-space {
            height: 24px;
        }
        .signature-line {
            font-weight: bold;
            color: #09090b;
            font-size: 7.5pt;
            border-bottom: 0.5pt solid #71717a;
            display: inline-block;
            min-width: 140px;
            padding-bottom: 1px;
        }
        .signature-role {
            font-size: 6.5pt;
            color: #52525b;
            margin-top: 2px;
        }

        /* Perforation / Cut Line */
        .cut-divider {
            height: 9mm;
            text-align: center;
            line-height: 9mm;
            font-size: 6.5pt;
            color: #94a3b8;
            letter-spacing: 1px;
            border-top: 1pt dashed #cbd5e1;
            margin-top: 1mm;
            margin-bottom: 1mm;
            position: relative;
        }
        .cut-badge {
            background-color: #ffffff;
            padding: 0 8px;
            position: relative;
            top: -1px;
        }

        .audit-footer {
            font-size: 5.5pt;
            color: #a1a1aa;
            text-align: center;
            border-top: 0.5pt solid #f4f4f5;
            padding-top: 2px;
            margin-top: 4px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier', monospace; }
    </style>
</head>
<body>

    {{-- ========================================================================= --}}
    {{-- BAGIAN 1: LEMBAR 1 • ARSIP KOPERASI (TTD ANGGOTA PENERIMA) --}}
    {{-- ========================================================================= --}}
    <div class="slip-half">
        {{-- Header & Kop --}}
        <table class="header-table">
            <tr>
                <td width="65%" class="kop-text">
                    <h2>{{ coop_config('legal_name') }}</h2>
                    <p>{{ coop_config('parent_org') }} &bull; {{ coop_config('address') }}</p>
                </td>
                <td width="35%" class="text-right meta-text-muted">
                    NO. REF: <span class="font-mono">SLIP/{{ $session?->year ?? date('Y') }}/{{ $member?->nomorAnggota ?? $distribution->id }}</span><br>
                    TANGGAL: <strong>{{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>
                </td>
            </tr>
        </table>
        <div class="header-divider"></div>

        {{-- Document Title --}}
        <table style="margin-bottom: 4px;">
            <tr>
                <td width="70%">
                    <h1 class="doc-title">SLIP PENCAIRAN SHU DAN SIMPANAN</h1>
                    <p class="doc-subtitle">Rapat Anggota Tahunan (RAT) Tahun Buku {{ $session?->year ?? date('Y') }}</p>
                </td>
                <td width="30%" class="text-right">
                    <span class="badge-pill badge-coop">LEMBAR 1 &bull; ARSIP KOPERASI</span>
                </td>
            </tr>
        </table>

        {{-- Member Strip --}}
        <div class="member-strip">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <span class="member-name">{{ $member?->name ?? 'Anggota Koperasi' }}</span>
                        <div class="member-no font-mono">No. Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
                    </td>
                    <td class="text-right" style="vertical-align: middle;">
                        <span style="font-size: 6.5pt; color: #155A6B; font-weight: bold;">
                            &bull; {{ $distribution->is_disbursed ? 'Sudah Dicairkan' : 'Menunggu Pencairan' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Financial Ledger 2-Column --}}
        <table class="ledger-col-table">
            <tr>
                {{-- Column Left: SHU --}}
                <td width="50%" class="ledger-cell-left">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>I. HAK SISA HASIL USAHA (SHU)</th>
                                <th class="text-right" width="35%">NOMINAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jasa Modal (Simpanan)</td>
                                <td class="text-right font-mono">Rp {{ number_format((float) $distribution->jasa_simpanan_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Jasa Usaha (Transaksi)</td>
                                <td class="text-right font-mono">Rp {{ number_format((float) $distribution->jasa_usaha_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td>Subtotal Hak SHU</td>
                                <td class="text-right font-mono">Rp {{ number_format((float) $distribution->shu_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                {{-- Column Right: Simpanan --}}
                <td width="50%" class="ledger-cell-right">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>II. SALDO SIMPANAN (TERKINI)</th>
                                <th class="text-right" width="35%">NOMINAL</th>
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
                            <tr class="total-row">
                                <td>Subtotal Simpanan</td>
                                <td class="text-right font-mono">Rp {{ number_format($rcptTotalSimpanan, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Grand Total --}}
        <div class="grand-total-box">
            <table style="width: 100%;">
                <tr>
                    <td width="55%">
                        <span class="grand-total-title">Total Pencairan</span>
                    </td>
                    <td width="45%" class="text-right">
                        <span class="grand-total-amount font-mono">Rp {{ number_format($rcptTotalPencairan, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Signatures: Single TTD (Anggota Penerima) --}}
        <table class="signature-table">
            <tr>
                <td width="55%">
                    <div style="font-size: 6.5pt; color: #71717a; padding-right: 15px; line-height: 1.3;">
                        <strong>Arsip Koperasi:</strong> Disimpan oleh Koperasi sebagai bukti sah penyerahan dana yang telah diterima oleh anggota.
                    </div>
                </td>
                <td width="45%" class="text-right">
                    <div style="display: inline-block; text-align: center; min-width: 150px;">
                        <p style="margin: 0; font-size: 7pt; color: #3f3f46;">
                            {{ coop_config('city', 'Bandung') }}, {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                            Yang Menerima (Anggota),
                        </p>
                        <div class="signature-space"></div>
                        <div class="signature-line">({{ $member?->name ?? 'Anggota' }})</div>
                        <div class="signature-role font-mono">No. Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footnote --}}
        <div class="audit-footer">
            Dokumen resmi diterbitkan oleh Sistem Informasi {{ coop_config('legal_name') }} &bull; Ref: SLIP-{{ $distribution->id }}-{{ substr(md5($distribution->id . ($distribution->created_at ?? now())), 0, 8) }}
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- GARIS POTONG / PERFORASI PEMISAH --}}
    {{-- ========================================================================= --}}
    <div class="cut-divider">
        <span class="cut-badge">&minus;&minus;&minus; POTONG DI SINI (Gunting / Cutter) &minus;&minus;&minus;</span>
    </div>

    {{-- ========================================================================= --}}
    {{-- BAGIAN 2: LEMBAR 2 • ARSIP ANGGOTA (TTD PETUGAS KOPERASI) --}}
    {{-- ========================================================================= --}}
    <div class="slip-half">
        {{-- Header & Kop --}}
        <table class="header-table">
            <tr>
                <td width="65%" class="kop-text">
                    <h2>{{ coop_config('legal_name') }}</h2>
                    <p>{{ coop_config('parent_org') }} &bull; {{ coop_config('address') }}</p>
                </td>
                <td width="35%" class="text-right meta-text-muted">
                    NO. REF: <span class="font-mono">SLIP/{{ $session?->year ?? date('Y') }}/{{ $member?->nomorAnggota ?? $distribution->id }}</span><br>
                    TANGGAL: <strong>{{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>
                </td>
            </tr>
        </table>
        <div class="header-divider"></div>

        {{-- Document Title --}}
        <table style="margin-bottom: 4px;">
            <tr>
                <td width="70%">
                    <h1 class="doc-title">SLIP PENCAIRAN SHU DAN SIMPANAN</h1>
                    <p class="doc-subtitle">Rapat Anggota Tahunan (RAT) Tahun Buku {{ $session?->year ?? date('Y') }}</p>
                </td>
                <td width="30%" class="text-right">
                    <span class="badge-pill badge-member">LEMBAR 2 &bull; ARSIP ANGGOTA</span>
                </td>
            </tr>
        </table>

        {{-- Member Strip --}}
        <div class="member-strip">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <span class="member-name">{{ $member?->name ?? 'Anggota Koperasi' }}</span>
                        <div class="member-no font-mono">No. Anggota: #{{ $member?->nomorAnggota ?? '-' }}</div>
                    </td>
                    <td class="text-right" style="vertical-align: middle;">
                        <span style="font-size: 6.5pt; color: #155A6B; font-weight: bold;">
                            &bull; {{ $distribution->is_disbursed ? 'Sudah Dicairkan' : 'Menunggu Pencairan' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Financial Ledger 2-Column --}}
        <table class="ledger-col-table">
            <tr>
                {{-- Column Left: SHU --}}
                <td width="50%" class="ledger-cell-left">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>I. HAK SISA HASIL USAHA (SHU)</th>
                                <th class="text-right" width="35%">NOMINAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jasa Modal (Simpanan)</td>
                                <td class="text-right font-mono">Rp {{ number_format((float) $distribution->jasa_simpanan_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Jasa Usaha (Transaksi)</td>
                                <td class="text-right font-mono">Rp {{ number_format((float) $distribution->jasa_usaha_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td>Subtotal Hak SHU</td>
                                <td class="text-right font-mono">Rp {{ number_format((float) $distribution->shu_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                {{-- Column Right: Simpanan --}}
                <td width="50%" class="ledger-cell-right">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>II. SALDO SIMPANAN (TERKINI)</th>
                                <th class="text-right" width="35%">NOMINAL</th>
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
                            <tr class="total-row">
                                <td>Subtotal Simpanan</td>
                                <td class="text-right font-mono">Rp {{ number_format($rcptTotalSimpanan, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Grand Total --}}
        <div class="grand-total-box">
            <table style="width: 100%;">
                <tr>
                    <td width="55%">
                        <span class="grand-total-title">Total Pencairan</span>
                    </td>
                    <td width="45%" class="text-right">
                        <span class="grand-total-amount font-mono">Rp {{ number_format($rcptTotalPencairan, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Signatures: Single TTD (Petugas Koperasi) --}}
        <table class="signature-table">
            <tr>
                <td width="55%">
                    <div style="font-size: 6.5pt; color: #71717a; padding-right: 15px; line-height: 1.3;">
                        <strong>Arsip Anggota:</strong> Diserahkan kepada Anggota sebagai tanda terima resmi pencairan hak oleh Koperasi.
                    </div>
                </td>
                <td width="45%" class="text-right">
                    <div style="display: inline-block; text-align: center; min-width: 150px;">
                        <p style="margin: 0; font-size: 7pt; color: #3f3f46;">
                            {{ coop_config('city', 'Bandung') }}, {{ $distribution->disbursed_at ? $distribution->disbursed_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                            Petugas Operasional,
                        </p>
                        <div class="signature-space"></div>
                        <div class="signature-line">({{ coop_setting('bendahara_name', 'M. Reyvan Purnama') }})</div>
                        <div class="signature-role">{{ coop_setting('bendahara_title', 'Manajer Operasional') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footnote --}}
        <div class="audit-footer">
            Dokumen resmi diterbitkan oleh Sistem Informasi {{ coop_config('legal_name') }} &bull; Ref: SLIP-{{ $distribution->id }}-{{ substr(md5($distribution->id . ($distribution->created_at ?? now())), 0, 8) }}
        </div>
    </div>

</body>
</html>
