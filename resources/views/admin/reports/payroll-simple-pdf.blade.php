<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Potongan Gaji Koperasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1e293b;
            padding: 10px 30px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #0F52BA;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #0F52BA;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #475569;
            margin-bottom: 10px;
        }

        .header .period {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 10px;
        }

        /* Table Style */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.report-table thead {
            background: #0F52BA;
            color: white;
        }

        table.report-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.report-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        table.report-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.report-table tbody tr:hover {
            background: #f1f5f9;
        }

        table.report-table td {
            padding: 8px;
            font-size: 10px;
            vertical-align: middle;
        }

        table.report-table td.center {
            text-align: center;
        }

        table.report-table td.right {
            text-align: right;
        }

        table.report-table tfoot {
            background: #1e293b;
            color: white;
            font-weight: bold;
        }

        table.report-table tfoot td {
            padding: 12px 8px;
            font-size: 11px;
        }

        /* Footer & Signature */
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .footer-note {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 20px;
            font-style: italic;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-box .location-date {
            font-size: 10px;
            margin-bottom: 60px;
        }

        .signature-box .title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-box .name {
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        /* Bank Info Box */
        .bank-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f1f5f9;
            border-left: 4px solid #0F52BA;
            border-radius: 6px;
            font-size: 10px;
            color: #1e293b;
        }

        .bank-info strong {
            display: block;
            margin-bottom: 5px;
            color: #0F52BA;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>KOPERASI KONSUMEN SYARIAH BERKAH SOLUSI MADANI</h1>
        <h2>KKSBSM UNIVERSITAS MUHAMMADIYAH BANDUNG</h2>
        <div class="period">
            DAFTAR POTONGAN GAJI ANGGOTA KOPERASI<br>
            Periode: {{ $monthName }} {{ $year }}
        </div>
    </div>

    <!-- Main Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th class="center" style="width: 40px;">No</th>
                <th>Nama Anggota</th>
                <th style="width: 200px;">Unit Kerja</th>
                <th class="right" style="width: 150px;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ strtoupper($item['nama']) }}</strong></td>
                    <td>{{ $item['unit_kerja'] }}</td>
                    <td class="right" style="font-family: 'Courier New', monospace; font-weight: bold;">
                        {{ number_format($item['total'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="right">TOTAL POTONGAN</td>
                <td class="right">{{ number_format($data['summary']['grand_total'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p class="footer-note">
            *Potongan gaji dilakukan sesuai persetujuan anggota koperasi yang bersangkutan.
        </p>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                &nbsp;
            </div>
            <div class="signature-box">
                <div class="location-date">Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="title" style="margin-bottom: 50px;">ttd</div>
                <div class="name">(Muhammad Alwi Almaliki)</div>
            </div>
        </div>

        <!-- Bank Info -->
        <div class="bank-info">
            <strong>Informasi Transfer:</strong>
            Bank KB Bukopin Syariah<br>
            a.n. Koperasi Konsumen Syariah Berkah Solusi Madani<br>
            No. Rekening: 7704020507
        </div>
    </div>
</body>

</html>