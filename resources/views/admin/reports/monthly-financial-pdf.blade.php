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
        
        table.report-table tbody tr:hover {
            background: #f1f5f9;
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
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
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
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .badge-gray {
            background: #f1f5f9;
            color: #64748b;
        }
        
        .note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin-top: 20px;
            font-size: 9px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI KARYAWAN UNIVERSITAS MUHAMMADIYAH BANDUNG</h1>
        <h2>LAPORAN POTONGAN GAJI & SIMPANAN WAJIB</h2>
        <div class="period">Periode: {{ $monthName }} {{ $year }}</div>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Tanggal Generate</td>
                <td>: {{ $generatedAt }}</td>
            </tr>
            <tr>
                <td>Total Anggota Tercatat</td>
                <td>: {{ $data['summary']['total_members'] }} Orang</td>
            </tr>
            <tr>
                <td>Unit Tujuan</td>
                <td>: Unit Keuangan UMB</td>
            </tr>
        </table>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total SIMWA</div>
            <div class="value">Rp {{ number_format($data['summary']['total_simwa'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Sukarela</div>
            <div class="value">Rp {{ number_format($data['summary']['total_sukarela'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Angsuran Bermadani</div>
            <div class="value">Rp {{ number_format($data['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Angsuran BMT ITQAN</div>
            <div class="value">Rp {{ number_format($data['summary']['total_angsuran_bmt_itqan'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box primary">
            <div class="label">GRAND TOTAL</div>
            <div class="value">Rp {{ number_format($data['summary']['grand_total'], 0, ',', '.') }}</div>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Anggota</th>
                <th class="right" style="width: 85px;">SIMWA (Rp)</th>
                <th class="right" style="width: 85px;">Sukarela (Rp)</th>
                <th class="right" style="width: 85px;">Angs. Bermadani (Rp)</th>
                <th class="right" style="width: 85px;">Angs. BMT ITQAN (Rp)</th>
                <th class="right" style="width: 90px;">Total (Rp)</th>
                <th class="center" style="width: 80px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ strtoupper($item['nama']) }}</strong></td>
                    <td class="right">{{ number_format($item['simwa'], 0, ',', '.') }}</td>
                    <td class="right">
                        @if($item['sukarela'] > 0)
                            {{ number_format($item['sukarela'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="right">
                        @if($item['angsuran_bermadani'] > 0)
                            {{ number_format($item['angsuran_bermadani'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="right">
                        @if($item['angsuran_bmt_itqan'] > 0)
                            {{ number_format($item['angsuran_bmt_itqan'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="right"><strong>{{ number_format($item['total'], 0, ',', '.') }}</strong></td>
                    <td class="center">
                        @if($item['has_loan'])
                            @if($item['angsuran_bmt_itqan'] > 0 && $item['angsuran_bermadani'] > 0)
                                <span class="badge badge-blue">BM+BMT</span>
                            @elseif($item['angsuran_bmt_itqan'] > 0)
                                <span class="badge badge-blue">BMT ITQAN</span>
                            @else
                                <span class="badge badge-blue">Bermadani</span>
                            @endif
                        @else
                            <span class="badge badge-gray">SIMWA</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: left;">TOTAL KESELURUHAN</td>
                <td class="right">{{ number_format($data['summary']['total_simwa'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data['summary']['total_sukarela'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data['summary']['total_angsuran_bermadani'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($data['summary']['total_angsuran_bmt_itqan'], 0, ',', '.') }}</td>
                <td class="right" style="font-size: 13px;">{{ number_format($data['summary']['grand_total'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="note">
        <strong>CATATAN:</strong><br>
        1. Potongan gaji dilakukan otomatis melalui sistem payroll UMB<br>
        2. SIMWA: Simpanan Wajib bulanan Rp 50.000/bulan<br>
        3. Sukarela: Tambahan simpanan sukarela yang disetujui anggota<br>
        4. Angsuran Bermadani: Cicilan pinjaman dari Koperasi Bermadani UMB<br>
        5. Angsuran BMT ITQAN: Cicilan pinjaman channeling dari BMT ITQAN<br>
        6. Dana mohon ditransfer ke Rekening Koperasi: BCA 1234567890 a.n. Koperasi Karyawan UMB
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Mengetahui,<br>Ketua Koperasi</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="position">NIP: _______________</div>
        </div>
        <div class="signature-box">
            <div class="title">Menyetujui,<br>Bendahara Koperasi</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="position">NIP: _______________</div>
        </div>
    </div>

    <div class="footer" style="text-align: center; font-size: 9px; color: #94a3b8; margin-top: 30px;">
        <p>Dokumen ini digenerate otomatis oleh Sistem Informasi Koperasi UMB</p>
        <p>Dicetak pada: {{ $generatedAt }}</p>
    </div>
</body>
</html>
