<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tanda Tangan Anggota</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            padding: 18px 24px;
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 2px solid #0f52ba;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 4px;
            color: #0f52ba;
        }

        .header h2 {
            font-size: 13px;
            font-weight: normal;
            color: #334155;
        }

        .meta {
            margin: 10px 0 14px;
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }

        .filters {
            margin-top: 6px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            background: #0f52ba;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #dbe3ef;
            padding: 8px 6px;
        }

        tbody td {
            border: 1px solid #dbe3ef;
            padding: 10px 8px;
            vertical-align: middle;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .col-no {
            width: 7%;
            text-align: center;
        }

        .col-name {
            width: 53%;
        }

        .col-sign {
            width: 40%;
            height: 32px;
        }

        .footer {
            margin-top: 14px;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DAFTAR TANDA TANGAN PENERIMA PAKET LEBARAN</h1>
        <h2>Koperasi Konsumen Syariah Berkah Solusi Madani</h2>
    </div>

    <div class="meta">
        <div>Tanggal cetak: {{ $generatedAt }}</div>
        <div>Total anggota: {{ $members->count() }} orang</div>
        <div class="filters">
            Filter aktif:
            Status {{ $filters['status'] }},
            Tier {{ $filters['tier'] ?: 'Semua' }},
            Unit {{ $filters['unitKerja'] ?: 'Semua' }},
            Tanggal gabung {{ $filters['joinDate'] ?: 'Semua' }},
            Pencarian {{ $filters['search'] ?: '-' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-name">Nama Anggota</th>
                <th class="col-sign">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $index => $member)
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-name">{{ strtoupper($member->name) }}</td>
                    <td class="col-sign"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digunakan sebagai bukti serah terima paket lebaran anggota.
    </div>
</body>

</html>
