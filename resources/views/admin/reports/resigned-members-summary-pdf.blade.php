<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Pengembalian Simpanan Anggota Keluar - {{ coop_config('short_name') }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #1e293b;
        }
        .kop-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .kop-image {
            width: 100%;
            max-height: 100px;
            object-fit: contain;
        }
        .kop-divider {
            border-bottom: 2px solid {{ coop_config('theme.primary') }};
            margin-top: 5px;
            margin-bottom: 12px;
        }
        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: {{ coop_config('theme.primary') }};
        }
        .subtitle {
            text-align: center;
            font-size: 9pt;
            color: #64748b;
            margin-bottom: 15px;
        }
        .table-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-summary th, .table-summary td {
            border: 1px solid #cbd5e1;
            padding: 6px 7px;
            font-size: 8.5pt;
        }
        .table-summary th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; }

        .badge-settled {
            color: #166534;
            font-weight: bold;
        }
        .badge-pending {
            color: #b45309;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Gambar Resmi -->
    <div class="kop-container">
        @if(!empty($kopBase64))
            <img src="{{ $kopBase64 }}" class="kop-image" alt="Kop Surat {{ coop_config('name') }}">
            <div class="kop-divider"></div>
        @else
            <h2 style="margin:0; color: {{ coop_config('theme.primary') }};">{{ strtoupper(coop_config('legal_name')) }}</h2>
            <p style="margin:0; font-size:9pt; color:#64748b;">{{ coop_config('address') }} | Telp: {{ coop_config('phone') }}</p>
            <div class="kop-divider"></div>
        @endif
    </div>

    <div class="title">LAPORAN REKAPITULASI PENGEMBALIAN SIMPANAN ANGGOTA KELUAR</div>
    <div class="subtitle">
        Status Filter: <strong>{{ $statusFilter === 'ALL' ? 'Semua Status' : ($statusFilter === 'SETTLED' ? 'Sudah Lunas' : 'Belum Lunas') }}</strong> • 
        Tanggal Cetak: <strong>{{ date('d F Y H:i') }}</strong>
    </div>

    <!-- Data Table -->
    <table class="table-summary">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">No. Anggota</th>
                <th width="18%">Nama Member</th>
                <th width="13%">Unit Kerja</th>
                <th width="10%">Simpok (Rp)</th>
                <th width="10%">Simwa (Rp)</th>
                <th width="10%">Simsuka (Rp)</th>
                <th width="11%">Total Gross (Rp)</th>
                <th width="10%">Pot. Loan (Rp)</th>
                <th width="11%">Net Refund (Rp)</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSimpok = 0;
                $grandSimwa = 0;
                $grandSimsuka = 0;
                $grandGross = 0;
                $grandLoan = 0;
                $grandNet = 0;
            @endphp
            @forelse($members as $index => $m)
                @php
                    $simpok = (float) $m->simpananPokok;
                    $simwa = (float) $m->simpananWajib;
                    $simsuka = (float) $m->simpananSukarela;
                    $gross = $simpok + $simwa + $simsuka;

                    $settlement = $m->settlement;
                    $isSettled = $settlement && $settlement->status === 'SETTLED';

                    $loanDeduction = $settlement ? (float)$settlement->loan_deduction : 0;
                    $netRefund = $settlement ? (float)$settlement->net_refund_amount : $gross;

                    $grandSimpok += $simpok;
                    $grandSimwa += $simwa;
                    $grandSimsuka += $simsuka;
                    $grandGross += $gross;
                    $grandLoan += $loanDeduction;
                    $grandNet += $netRefund;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-mono">#{{ $m->nomorAnggota }}</td>
                    <td class="font-bold">{{ $m->name }}</td>
                    <td>{{ $m->unitKerja ?? '-' }}</td>
                    <td class="text-right">{{ number_format($simpok, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($simwa, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($simsuka, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: {{ coop_config('theme.primary') }};">{{ number_format($gross, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #e11d48;">{{ $loanDeduction > 0 ? '-' . number_format($loanDeduction, 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-bold">{{ number_format($netRefund, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($isSettled)
                            <span class="badge-settled">LUNAS</span>
                        @else
                            <span class="badge-pending">PENDING</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data anggota keluar yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4" class="text-right uppercase">TOTAL KESELURUHAN:</td>
                <td class="text-right">{{ number_format($grandSimpok, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandSimwa, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandSimsuka, 0, ',', '.') }}</td>
                <td class="text-right" style="color: {{ coop_config('theme.primary') }};">Rp {{ number_format($grandGross, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #e11d48;">- Rp {{ number_format($grandLoan, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($grandNet, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Disetujui oleh,<br>
                Ketua Koperasi {{ coop_config('short_name') }}<br>
                <div class="signature-space"></div>
                <strong>({{ coop_setting('ketua_name', 'Ketua Koperasi') }})</strong>
            </td>
            <td>
                {{ coop_config('city') }}, {{ date('d F Y') }}<br>
                Manager Operasional / Bendahara,<br>
                <div class="signature-space"></div>
                <strong>({{ coop_setting('bendahara_name', 'Manager Operasional') }})</strong>
            </td>
        </tr>
    </table>

</body>
</html>
