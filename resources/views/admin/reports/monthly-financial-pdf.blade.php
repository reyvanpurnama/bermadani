<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Bulanan - {{ $monthName }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            /* Font lebih kecil biar muat */
            line-height: 1.3;
            color: #1e293b;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #0F52BA;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #0F52BA;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 12px;
            font-weight: normal;
            color: #475569;
            margin-bottom: 5px;
        }

        .header .period {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
        }

        .info-box {
            background: #f1f5f9;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 3px solid #0F52BA;
            font-size: 9px;
        }

        .info-box table {
            width: 100%;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.report-table thead th {
            background: #0F52BA;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
        }

        table.report-table thead th.sub {
            background: #f1f5f9;
            color: #1e293b;
            font-size: 7px;
        }

        table.report-table tbody td {
            padding: 5px 4px;
            font-size: 8px;
            border: 1px solid #e2e8f0;
        }

        table.report-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.report-table td.right {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        table.report-table td.center {
            text-align: center;
        }

        table.report-table tfoot td {
            background: #1e293b;
            color: white;
            font-weight: bold;
            padding: 8px 4px;
            font-size: 9px;
            border: 1px solid #1e293b;
        }

        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-box {
            display: table-cell;
            width: 16%;
            /* Bagi 6 */
            padding: 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .summary-box .label {
            font-size: 7px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }

        .summary-box .value {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        .summary-box.primary {
            background: #eff6ff;
            border-color: #3b82f6;
        }

        .summary-box.primary .value {
            color: #1d4ed8;
        }

        .note {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 8px;
            font-size: 8px;
            color: #92400e;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>KOPERASI KONSUMEN SYARIAH BERKAH SOLUSI MADANI</h1>
        <h2>KKSBSM UNIVERSITAS MUHAMMADIYAH BANDUNG</h2>
        <div class="period">LAPORAN POTONGAN GAJI & SIMPANAN WAJIB - PERIODE: {{ strtoupper($monthName) }} {{ $year }}
        </div>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total Member</div>
            <div class="value">{{ $data['summary']['total_members'] }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Simwa</div>
            <div class="value">{{ number_format($data['summary']['total_simwa'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Sukarela</div>
            <div class="value">{{ number_format($data['summary']['total_sukarela'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Angs. Bermadani</div>
            <div class="value">{{ number_format($data['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Angs. BMT Itqan</div>
            <div class="value">
                {{ number_format($data['summary']['total_angsuran_bmt_itqan_1'] + $data['summary']['total_angsuran_bmt_itqan_2'], 0, ',', '.') }}
            </div>
        </div>
        <div class="summary-box primary">
            <div class="label">GRAND TOTAL</div>
            <div class="value">{{ number_format($data['summary']['grand_total'], 0, ',', '.') }}</div>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2">Nama Anggota</th>

                {{-- Simpanan --}}
                <th colspan="2" style="background:#059669;">Simpanan</th>

                {{-- Bermadani --}}
                <th colspan="3" style="background:#2563eb;">Internal (Bermadani)</th>

                {{-- BMT 1 --}}
                <th colspan="4" style="background:#7c3aed;">BMT Itqan 1</th>

                {{-- BMT 2 --}}
                <th colspan="4" style="background:#db2777;">BMT Itqan 2</th>

                <th rowspan="2" style="width: 70px; background:#1e293b;">Total (Rp)</th>
            </tr>
            <tr>
                {{-- Sub Header Simpanan --}}
                <th class="sub">Wajib</th>
                <th class="sub">Sukarela</th>

                {{-- Sub Header Bermadani --}}
                <th class="sub">Angsuran</th>
                <th class="sub" style="width: 20px;">Ke</th>
                <th class="sub" style="width: 20px;">Tnr</th>

                {{-- Sub Header BMT 1 --}}
                <th class="sub">Angsuran</th>
                <th class="sub">Simwa</th> <!-- INI KOLOM 30K -->
                <th class="sub" style="width: 20px;">Ke</th>
                <th class="sub" style="width: 20px;">Tnr</th>

                {{-- Sub Header BMT 2 --}}
                <th class="sub">Angsuran</th>
                <th class="sub">Simwa</th>
                <th class="sub" style="width: 20px;">Ke</th>
                <th class="sub" style="width: 20px;">Tnr</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong style="font-size: 8px;">{{ strtoupper($item['nama']) }}</strong></td>

                    {{-- Simpanan --}}
                    <td class="right">{{ number_format($item['simwa'], 0, ',', '.') }}</td>
                    <td class="right">{{ $item['sukarela'] > 0 ? number_format($item['sukarela'], 0, ',', '.') : '-' }}</td>

                    {{-- Bermadani --}}
                    <td class="right">
                        {{ $item['angsuran_bermadani'] > 0 ? number_format($item['angsuran_bermadani'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="center">{{ $item['angsuran_bermadani'] > 0 ? $item['angsuran_ke_bermadani'] : '' }}</td>
                    <td class="center">{{ $item['angsuran_bermadani'] > 0 ? $item['tenor_bermadani'] : '' }}</td>

                    {{-- BMT 1 --}}
                    <td class="right">
                        {{ $item['angsuran_bmt_itqan_1'] > 0 ? number_format($item['angsuran_bmt_itqan_1'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="right text-purple-600">
                        {{ $item['simwa_bmt_itqan_1'] > 0 ? number_format($item['simwa_bmt_itqan_1'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="center">{{ $item['angsuran_bmt_itqan_1'] > 0 ? $item['angsuran_ke_bmt_itqan_1'] : '' }}</td>
                    <td class="center">{{ $item['angsuran_bmt_itqan_1'] > 0 ? $item['tenor_bmt_itqan_1'] : '' }}</td>

                    {{-- BMT 2 --}}
                    <td class="right">
                        {{ $item['angsuran_bmt_itqan_2'] > 0 ? number_format($item['angsuran_bmt_itqan_2'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="right">
                        {{ $item['simwa_bmt_itqan_2'] > 0 ? number_format($item['simwa_bmt_itqan_2'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="center">{{ $item['angsuran_bmt_itqan_2'] > 0 ? $item['angsuran_ke_bmt_itqan_2'] : '' }}</td>
                    <td class="center">{{ $item['angsuran_bmt_itqan_2'] > 0 ? $item['tenor_bmt_itqan_2'] : '' }}</td>

                    <td class="right" style="font-weight: bold; background: #f1f5f9;">
                        {{ number_format($item['total'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;">TOTAL</td>

                <td class="right">{{ number_format($data['summary']['total_simwa'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data['summary']['total_sukarela'], 0, ',', '.') }}</td>

                <td class="right">{{ number_format($data['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</td>
                <td colspan="2"></td>

                <td class="right">{{ number_format($data['summary']['total_angsuran_bmt_itqan_1'], 0, ',', '.') }}</td>
                <td class="right"></td>
                <!-- Total Simwa BMT 1 belum di summary, biarkan empty atau hitung manual jika perlu -->
                <td colspan="2"></td>

                <td class="right">{{ number_format($data['summary']['total_angsuran_bmt_itqan_2'], 0, ',', '.') }}</td>
                <td class="right"></td>
                <td colspan="2"></td>

                <td class="right" style="font-size: 10px;">
                    {{ number_format($data['summary']['grand_total'], 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="note">
        <strong>CATATAN PENTING:</strong><br>
        1. Laporan ini digenerate dalam format LANDSCAPE untuk menampilkan detail potongan secara lengkap.<br>
        2. Simwa BMT adalah Simpanan Wajib BMT Itqan (biasanya Rp 30.000) yang terpisah dari angsuran pokok.<br>
        3. Pastikan total transfer ke BMT Itqan = Total Angs. BMT 1 + Total Angs. BMT 2 + Total Simwa BMT.<br>
    </div>

    <div style="margin-top: 20px; text-align: right; font-size: 10px;">
        <p>Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p style="font-weight: bold; text-decoration: underline;">(Muhammad Alwi Almaliki)</p>
        <p>Manager Operasional</p>
    </div>
</body>

</html>