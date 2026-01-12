<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jam Kerja Developer - {{ $monthName }} {{ $year }}</title>
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

        .info-box {
            background: #f1f5f9;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #0F52BA;
        }

        .info-box table {
            width: 100%;
        }

        .info-box td {
            padding: 3px 0;
            font-size: 10px;
        }

        .info-box td:first-child {
            width: 150px;
            font-weight: bold;
            color: #475569;
        }

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

        table.report-table th.center {
            text-align: center;
        }

        table.report-table th.right {
            text-align: right;
        }

        table.report-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        table.report-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.report-table td {
            padding: 8px;
            font-size: 10px;
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

        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-box {
            display: table-cell;
            width: 20%;
            padding: 10px;
            text-align: center;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .summary-box.primary {
            border-color: #0F52BA;
            background: #dbeafe;
        }

        .summary-box .label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            margin-bottom: 5px;
        }

        .summary-box .value {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
        }

        .summary-box.primary .value {
            color: #0F52BA;
        }

        .developer-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .developer-header {
            background: #1e293b;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 0;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-paid {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-box .title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-box .name {
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #1e293b;
            display: inline-block;
            min-width: 200px;
        }

        .signature-box .position {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }

        .note {
            background: #dbeafe;
            border-left: 4px solid #0F52BA;
            padding: 10px;
            margin-top: 20px;
            font-size: 9px;
            color: #1e40af;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>KOPERASI KARYAWAN UNIVERSITAS MUHAMMADIYAH BANDUNG</h1>
        <h2>LAPORAN JAM KERJA DEVELOPER IT</h2>
        <div class="period">Periode: {{ $monthName }} {{ $year }}</div>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Tanggal Generate</td>
                <td>: {{ $generatedAt }}</td>
            </tr>
            <tr>
                <td>Rate Per Jam</td>
                <td>: Rp 6.000</td>
            </tr>
            @if($filterDeveloper)
                <tr>
                    <td>Developer</td>
                    <td>: {{ $filterDeveloper }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total Jam</div>
            <div class="value">{{ number_format($stats['totalHours'], 1) }} Jam</div>
        </div>
        <div class="summary-box">
            <div class="label">Pending</div>
            <div class="value">Rp {{ number_format($stats['pending'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Approved</div>
            <div class="value">Rp {{ number_format($stats['approved'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Paid</div>
            <div class="value">Rp {{ number_format($stats['paid'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box primary">
            <div class="label">Grand Total</div>
            <div class="value">Rp {{ number_format($stats['totalAmount'], 0, ',', '.') }}</div>
        </div>
    </div>

    @php
        $groupedLogs = $logs->groupBy('developerName');
    @endphp

    @foreach($groupedLogs as $devName => $devLogs)
        @php
            $devTotalHours = $devLogs->sum('hoursWorked');
            $devTotalAmount = $devLogs->sum('totalAmount');
        @endphp
        <div class="developer-section">
            <div class="developer-header">
                {{ strtoupper($devName) }} — {{ number_format($devTotalHours, 1) }} Jam — Rp
                {{ number_format($devTotalAmount, 0, ',', '.') }}
            </div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 60px;" class="center">Jam Mulai</th>
                        <th style="width: 60px;" class="center">Jam Selesai</th>
                        <th style="width: 50px;" class="center">Durasi</th>
                        <th>Uraian Kegiatan</th>
                        <th style="width: 80px;" class="right">Bayaran</th>
                        <th style="width: 70px;" class="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devLogs as $index => $log)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $log->date->translatedFormat('l, d M Y') }}</td>
                            <td class="center">{{ $log->startTime ?? '-' }}</td>
                            <td class="center">{{ $log->endTime ?? '-' }}</td>
                            <td class="center"><strong>{{ number_format($log->hoursWorked, 1) }}</strong></td>
                            <td>{{ $log->description }}</td>
                            <td class="right">{{ number_format($log->totalAmount, 0, ',', '.') }}</td>
                            <td class="center">
                                <span class="badge badge-{{ strtolower($log->status) }}">{{ $log->status }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">SUBTOTAL {{ strtoupper($devName) }}</td>
                        <td class="center">{{ number_format($devTotalHours, 1) }}</td>
                        <td></td>
                        <td class="right">{{ number_format($devTotalAmount, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    <div class="note">
        <strong>KETERANGAN:</strong><br>
        1. Rate: Rp 6.000 per jam kerja<br>
        2. Status PENDING: Menunggu approval dari Admin<br>
        3. Status APPROVED: Sudah disetujui, siap dibayar<br>
        4. Status PAID: Sudah dibayarkan ke developer
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Mengetahui,<br>Ketua Koperasi</div>
            <div class="name">
                (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            </div>
            <div class="position">NIP: _______________</div>
        </div>
        <div class="signature-box">
            <div class="title">Menyetujui,<br>Koordinator IT</div>
            <div class="name">
                (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            </div>
            <div class="position">NIP: _______________</div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem Informasi Koperasi UMB</p>
        <p>Dicetak pada: {{ $generatedAt }}</p>
    </div>
</body>

</html>