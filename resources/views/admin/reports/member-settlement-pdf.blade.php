<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pengembalian Simpanan - {{ $member->name }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2cm 2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1e293b;
        }
        .kop-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .kop-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .kop-divider {
            border-bottom: 2px solid #0F52BA;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: {{ coop_config('theme.primary') }};
        }
        .subtitle {
            text-align: center;
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 20px;
        }
        .table-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .table-info td {
            padding: 4px 8px;
            font-size: 10pt;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-data th, .table-data td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 10pt;
        }
        .table-data th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        .total-title {
            font-size: 10pt;
            color: #1e40af;
            font-weight: bold;
        }
        .total-amount {
            font-size: 16pt;
            font-weight: font-extrabold;
            color: {{ coop_config('theme.primary') }};
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }
        .signature-space {
            height: 70px;
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

    <div class="title">BERITA ACARA & KWITANSI PENGEMBALIAN SIMPANAN</div>
    <div class="subtitle">Nomor Ref: BK-OUT/{{ $member->nomorAnggota }}/{{ date('Y') }}</div>

    <p style="font-size:10pt;">Pada hari ini, tanggal <strong>{{ date('d F Y', strtotime($settlement->settled_at ?? now())) }}</strong>, telah dilakukan serah terima penyelesaian hak & kewajiban simpanan anggota keluar atas nama:</p>

    <!-- Informasi Anggota -->
    <table class="table-info">
        <tr>
            <td width="30%"><strong>Nama Anggota</strong></td>
            <td width="3%">:</td>
            <td>{{ $member->name }}</td>
        </tr>
        <tr>
            <td><strong>Nomor Anggota</strong></td>
            <td>:</td>
            <td>#{{ $member->nomorAnggota }}</td>
        </tr>
        <tr>
            <td><strong>Unit Kerja / Instansi</strong></td>
            <td>:</td>
            <td>{{ $member->unitKerja ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Metode Pembayaran</strong></td>
            <td>:</td>
            <td>
                {{ $settlement->payment_method === 'CASH' ? 'Tunai / Cash' : 'Transfer Bank' }}
                @if($settlement->payment_method === 'BANK_TRANSFER' && $settlement->bank_name)
                    ({{ $settlement->bank_name }} - {{ $settlement->bank_account_number }} a.n {{ $settlement->bank_account_holder }})
                @endif
            </td>
        </tr>
    </table>

    <!-- Rincian Hak Simpanan -->
    <h4 style="margin-bottom:8px; font-size:11pt; color:{{ coop_config('theme.primary') }};">A. RINCIAN HAK SIMPANAN ANGGOTA</h4>
    <table class="table-data">
        <thead>
            <tr>
                <th>Jenis Simpanan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1. Simpanan Pokok</td>
                <td class="text-right">Rp {{ number_format($settlement->simpanan_pokok ?? $member->simpananPokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>2. Simpanan Wajib</td>
                <td class="text-right">Rp {{ number_format($settlement->simpanan_wajib ?? $member->simpananWajib, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>3. Simpanan Sukarela</td>
                <td class="text-right">Rp {{ number_format($settlement->simpanan_sukarela ?? $member->simpananSukarela, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color:#f8fafc; font-weight:bold;">
                <td>TOTAL HAK SIMPANAN GROSS</td>
                <td class="text-right">Rp {{ number_format($settlement->total_gross_simpanan ?? ($member->simpananPokok + $member->simpananWajib + $member->simpananSukarela), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Rincian Potongan Pinjaman -->
    <h4 style="margin-bottom:8px; font-size:11pt; color:{{ coop_config('theme.primary') }};">B. POTONGAN SISA KEWAJIBAN / PINJAMAN</h4>
    <table class="table-data">
        <thead>
            <tr>
                <th>Keterangan Potongan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Potongan Sisa Pinjaman Aktif</td>
                <td class="text-right text-rose-600">
                    @if(($settlement->loan_deduction ?? 0) > 0)
                        - Rp {{ number_format($settlement->loan_deduction, 0, ',', '.') }}
                    @else
                        Rp 0 (Tidak Ada Pinjaman)
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Total Bersih Diterima -->
    <div class="total-box">
        <table width="100%">
            <tr>
                <td>
                    <div class="total-title">TOTAL SISA BERSIH PENGEMBALIAN DITERIMA:</div>
                </td>
                <td class="text-right">
                    <div class="total-amount">Rp {{ number_format($settlement->net_refund_amount ?? ($member->simpananPokok + $member->simpananWajib + $member->simpananSukarela), 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Anggota Berhenti,<br><br>
                <div class="signature-space"></div>
                <strong>({{ $member->name }})</strong>
            </td>
            <td>
                {{ coop_config('city') }}, {{ date('d F Y', strtotime($settlement->settled_at ?? now())) }}<br>
                Pengurus {{ coop_config('short_name') }},<br><br>
                <div class="signature-space"></div>
                <strong>({{ coop_setting('bendahara_name', 'Manager Operasional') }})</strong><br>
                <span style="font-size:8pt; color:#64748b;">{{ coop_setting('bendahara_title', 'Bendahara Koperasi') }}</span>
            </td>
        </tr>
    </table>

</body>
</html>
