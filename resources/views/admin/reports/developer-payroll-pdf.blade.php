<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Absensi Kerja Lembur - {{ $monthName }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        
        .header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta-info {
            width: 100%;
            margin-bottom: 15px;
        }

        .meta-info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta-info td:first-child {
            width: 100px;
            font-weight: bold;
        }

        .meta-info td:nth-child(2) {
            width: 10px;
        }
        
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        
        table.report-table th, 
        table.report-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 10px;
        }
        
        table.report-table th {
            text-align: center;
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        table.report-table td.center {
            text-align: center;
        }
        
        table.report-table td.right {
            text-align: right;
        }

        .footer-summary {
            width: 50%;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }
        
        .signature-box .title {
            margin-bottom: 60px;
            font-weight: bold;
        }
        
        .signature-box .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $groupedLogs = $logs->groupBy('developerName');
    @endphp

    @foreach($groupedLogs as $devName => $devLogs)
        <div class="{{ !$loop->last ? 'page-break' : '' }}">
            <div class="header">
                <h1>REKAPITULASI ABSENSI KERJA LEMBUR</h1>
                <h2>KOPERASI BERMADANI</h2>
            </div>

            <table class="meta-info">
                <tr>
                    <td>NAMA</td>
                    <td>:</td>
                    <td>{{ strtoupper($devName) }}</td>
                </tr>
                <tr>
                    <td>BULAN</td>
                    <td>:</td>
                    <td>{{ strtoupper($monthName) }} {{ $year }}</td>
                </tr>
                <tr>
                    <td>JAM KERJA</td>
                    <td>:</td>
                    <td>6 Jam/ Hari</td>
                </tr>
            </table>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">NO</th>
                        <th style="width: 120px;">TANGGAL</th>
                        <th style="width: 60px;">JAM MULAI</th>
                        <th style="width: 60px;">JAM SELESAI</th>
                        <th style="width: 50px;">DURASI (JAM)</th>
                        <th>URAIAN KEGIATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devLogs as $index => $log)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $log->date->translatedFormat('l, d F Y') }}</td>
                            <td class="center">{{ $log->startTime ? \Carbon\Carbon::parse($log->startTime)->format('H:i') : '-' }}</td>
                            <td class="center">{{ $log->endTime ? \Carbon\Carbon::parse($log->endTime)->format('H:i') : '-' }}</td>
                            <td class="center">{{ number_format($log->hoursWorked, 0) }}</td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="meta-info footer-summary">
                <tr>
                    <td style="width: 150px;">JUMLAH LEMBUR</td>
                    <td>:</td>
                    <td>{{ $devLogs->count() }} HARI</td>
                </tr>
                <tr>
                    <td>TOTAL JAM</td>
                    <td>:</td>
                    <td>{{ number_format($devLogs->sum('hoursWorked'), 0) }} JAM</td>
                </tr>
            </table>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="title">Yang Mengajukan,</div>
                    <div class="name">{{ strtoupper($devName) }}</div>
                </div>
                <div class="signature-box">
                    <div class="title">Mengetahui,<br>Ketua Koperasi Bermadani</div>
                    <div class="name">RIDLO ABDILLAH, S.Pd., M.Si.</div>
                    <div>NIP: ...........................</div>
                </div>
            </div>
            
            <div style="font-size: 9px; margin-top: 30px; color: #888;">
                Generated by System: {{ $generatedAt }}
            </div>
        </div>
    @endforeach
</body>
</html>