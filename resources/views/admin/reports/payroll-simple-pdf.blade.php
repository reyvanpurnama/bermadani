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
            color: #333;
        }

        .container {
            padding: 20px 30px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 13px;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .header .period {
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }

        table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
        }

        table td {
            font-size: 10px;
        }

        table td.number {
            text-align: center;
            width: 35px;
        }

        table td.name {
            text-align: left;
        }

        table td.unit {
            text-align: left;
            width: 120px;
        }

        table td.amount {
            text-align: right;
            width: 100px;
            font-family: 'Courier New', monospace;
        }

        table tfoot td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .footer-note {
            font-size: 9px;
            color: #666;
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
        }

        .signature-box .location-date {
            font-size: 10px;
            margin-bottom: 60px;
        }

        .signature-box .title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-size: 10px;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-box .position {
            font-size: 9px;
        }

        /* Bank Info */
        .bank-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 9px;
        }

        .bank-info strong {
            display: block;
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Koperasi Konsumen Syariah Berkah Solusi Madani</h1>
            <h2>KKSBSM Universitas Muhammadiyah Bandung</h2>
            <div class="period">
                DAFTAR POTONGAN GAJI ANGGOTA KOPERASI<br>
                Periode: {{ $monthName }} {{ $year }}
            </div>
        </div>

        <!-- Main Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Nama Anggota</th>
                    <th style="width: 130px;">Unit Kerja</th>
                    <th style="width: 110px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['items'] as $index => $item)
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td class="name">{{ strtoupper($item['nama']) }}</td>
                        <td class="unit">{{ $item['unit_kerja'] }}</td>
                        <td class="amount">{{ number_format($item['total'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">TOTAL POTONGAN</td>
                    <td class="amount">{{ number_format($data['summary']['grand_total'], 0, ',', '.') }}</td>
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
    </div>
</body>

</html>