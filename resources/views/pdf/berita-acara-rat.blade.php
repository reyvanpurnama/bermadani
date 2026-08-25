<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara RAT {{ $session->year }} - Koperasi Bermadani</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2cm 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.5;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .kop-subtitle {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .kop-address {
            font-size: 9pt;
            text-align: center;
            font-style: italic;
        }

        /* Judul Dokumen */
        .doc-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-header h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }
        .doc-header p {
            font-size: 10.5pt;
            margin: 4px 0 0 0;
        }

        .paragraph {
            text-align: justify;
            text-indent: 35px;
            margin-bottom: 12px;
        }

        .list-info {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .list-info td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 11pt;
        }

        ol.numbered-list, ul.bullet-list {
            margin: 0 0 15px 25px;
            padding: 0;
            text-align: justify;
        }
        ol.numbered-list li, ul.bullet-list li {
            margin-bottom: 4px;
        }

        /* Tabel Keputusan SHU */
        .shu-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 18px 0;
            font-size: 10.5pt;
        }
        .shu-table th, .shu-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
        }
        .shu-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        /* Tanda Tangan */
        .signature-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-table td {
            vertical-align: top;
            text-align: center;
            padding: 10px;
        }
        .sig-title {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 60px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10.5pt;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <table class="kop-table">
        <tr>
            <td>
                <div class="kop-title">KOPERASI KONSUMEN SYARIAH BERMADANI</div>
                <div class="kop-subtitle">UNIVERSITAS MUHAMMADIYAH BANDUNG</div>
                <div class="kop-address">Jl. Soekarno-Hatta No.752, Cipadung Kidul, Panyileukan, Kota Bandung, Jawa Barat 40614</div>
            </td>
        </tr>
    </table>

    <!-- Judul Berita Acara -->
    <div class="doc-header">
        <h2>BERITA ACARA RAPAT ANGGOTA TAHUNAN (RAT)</h2>
        <p><strong>TAHUN BUKU {{ $session->year }}</strong></p>
        <p>Nomor: {{ $nomorSurat ?? ('.../BA-RAT/BERMADANI/' . date('Y')) }}</p>
    </div>

    <!-- Paragraf Pembuka -->
    <p class="paragraph">
        Pada hari ini, <strong>{{ $hariTanggal ?? ($session->event_date ? $session->event_date->translatedFormat('l, d F Y') : date('d F Y')) }}</strong>,
        bertempat di <strong>{{ $tempat ?? 'Ruang Rapat Utama Koperasi Bermadani UMB' }}</strong>,
        telah diselenggarakan Rapat Anggota Tahunan (RAT) <strong>{{ $session->title ?? ('Koperasi Bermadani Tahun Buku ' . $session->year) }}</strong>
        yang dimulai pada pukul <strong>{{ $jam ?? '09:00 WIB' }}</strong>.
    </p>

    <p style="margin-bottom: 5px;">Rapat dihadiri oleh:</p>
    <table class="list-info">
        <tr>
            <td style="width: 250px;">1. Jumlah Total Anggota Koperasi</td>
            <td style="width: 15px;">:</td>
            <td><strong>{{ number_format($totalAnggota ?? 0, 0, ',', '.') }}</strong> orang</td>
        </tr>
        <tr>
            <td>2. Anggota yang Hadir (Memenuhi Kuorum)</td>
            <td>:</td>
            <td><strong>{{ number_format($anggotaHadir ?? 0, 0, ',', '.') }}</strong> orang</td>
        </tr>
        <tr>
            <td>3. Pengurus Koperasi</td>
            <td>:</td>
            <td><strong>{{ $pengurusHadir ?? 3 }}</strong> orang</td>
        </tr>
        <tr>
            <td>4. Pengawas Koperasi</td>
            <td>:</td>
            <td><strong>{{ $pengawasHadir ?? 1 }}</strong> orang</td>
        </tr>
        <tr>
            <td>5. Undangan / Tamu</td>
            <td>:</td>
            <td><strong>{{ $tamuHadir ?? 0 }}</strong> orang</td>
        </tr>
    </table>

    <p class="paragraph">
        Karena jumlah anggota yang hadir telah memenuhi ketentuan Anggaran Dasar / Anggaran Rumah Tangga (AD/ART) Koperasi Konsumen Syariah Bermadani UMB, maka Rapat Anggota Tahunan ini dinyatakan <strong>SAH</strong> dan berhak mengambil keputusan hukum yang mengikat.
    </p>

    <!-- Susunan Acara -->
    <div class="section-title">SUSUNAN RANGKAIAN ACARA RAT:</div>
    <ol class="numbered-list">
        <li>Pembukaan oleh Pembawa Acara.</li>
        <li>Menyanyikan Lagu Indonesia Raya dan Mars Koperasi / Universitas Muhammadiyah Bandung.</li>
        <li>Sambutan Ketua Koperasi Bermadani UMB.</li>
        <li>Pemilihan dan Penetapan Pimpinan Sidang RAT.</li>
        <li>Pembacaan dan Pengesahan Peraturan Tata Tertib RAT.</li>
        <li>Penyampaian Laporan Pertanggungjawaban (LPJ) Pengurus Tahun Buku {{ $session->year }}.</li>
        <li>Penyampaian Laporan Hasil Pengawasan oleh Pengawas.</li>
        <li>Penyampaian dan Pengesahan Laporan Keuangan Tahun Buku {{ $session->year }}.</li>
        <li>Pembahasan dan Pengesahan Rencana Kerja (RK) dan Rencana Anggaran Pendapatan dan Belanja (RAPB) Tahun Buku berikutnya.</li>
        <li>Penetapan Pembagian Sisa Hasil Usaha (SHU) Tahun Buku {{ $session->year }}.</li>
        <li>Pemilihan / Penetapan Pengurus dan Pengawas (apabila terdapat pergantian masa jabatan).</li>
        <li>Sesi Pandangan Umum, Tanya Jawab, dan Pengambilan Keputusan Rapat.</li>
        <li>Penutup dan Doa.</li>
    </ol>

    <!-- Keputusan Rapat -->
    <div class="section-title">KEPUTUSAN RAPAT ANGGOTA TAHUNAN:</div>
    <p style="margin-bottom: 5px;">Setelah dilakukan pembahasan dan tanggapan oleh seluruh anggota, Rapat Anggota Tahunan menyepakati dan memutuskan:</p>

    <ol class="numbered-list">
        <li>Menerima dan mengesahkan Laporan Pertanggungjawaban (LPJ) Pengurus Koperasi Bermadani Tahun Buku {{ $session->year }}.</li>
        <li>Menerima dan mengesahkan Laporan Hasil Pengawasan Pengawas Koperasi Tahun Buku {{ $session->year }}.</li>
        <li>
            Mengesahkan Laporan Keuangan Tahun Buku {{ $session->year }} dengan rincian Perhitungan Hasil Usaha:
            <ul class="bullet-list" style="margin-top: 4px;">
                <li>Total Laba Bersih (Net Profit): <strong>Rp {{ number_format($session->total_net_profit, 0, ',', '.') }}</strong></li>
                <li>Total SHU Dialokasikan untuk Anggota: <strong>Rp {{ number_format($session->total_member_shu, 0, ',', '.') }}</strong></li>
            </ul>
        </li>
        <li>
            Menetapkan pembagian dan alokasi Sisa Hasil Usaha (SHU) Tahun Buku {{ $session->year }} berdasarkan 5 Pos Koperasi:
            <table class="shu-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">No</th>
                        <th style="width: 55%;">Pos Alokasi SHU</th>
                        <th style="width: 15%;">Persentase</th>
                        <th style="width: 20%;">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $netProfit = (float) $session->total_net_profit;
                        $memberShu = (float) $session->total_member_shu;
                        $cadanganPct = (float) ($session->cadangan_percentage ?? 25);
                        $jasaSimpananPct = (float) ($session->jasa_simpanan_percentage ?? 30);
                        $jasaUsahaPct = (float) ($session->jasa_usaha_percentage ?? 25);
                        $pengurusPct = (float) ($session->pengurus_percentage ?? 10);
                        $danaSosialPct = (float) ($session->dana_sosial_percentage ?? 10);

                        $cadanganAmt = round($memberShu * ($cadanganPct / 100), 2);
                        $jasaSimpananAmt = round($memberShu * ($jasaSimpananPct / 100), 2);
                        $jasaUsahaAmt = round($memberShu * ($jasaUsahaPct / 100), 2);
                        $pengurusAmt = round($memberShu * ($pengurusPct / 100), 2);
                        $danaSosialAmt = round($memberShu * ($danaSosialPct / 100), 2);
                    @endphp
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td>Cadangan Koperasi</td>
                        <td style="text-align: center;">{{ number_format($cadanganPct, 2, ',', '.') }}%</td>
                        <td style="text-align: right;">Rp {{ number_format($cadanganAmt, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">2</td>
                        <td>Jasa Simpanan Anggota</td>
                        <td style="text-align: center;">{{ number_format($jasaSimpananPct, 2, ',', '.') }}%</td>
                        <td style="text-align: right;">Rp {{ number_format($jasaSimpananAmt, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">3</td>
                        <td>Jasa Usaha / Transaksi Belanja Anggota</td>
                        <td style="text-align: center;">{{ number_format($jasaUsahaPct, 2, ',', '.') }}%</td>
                        <td style="text-align: right;">Rp {{ number_format($jasaUsahaAmt, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">4</td>
                        <td>Dana Pengurus & Karyawan</td>
                        <td style="text-align: center;">{{ number_format($pengurusPct, 2, ',', '.') }}%</td>
                        <td style="text-align: right;">Rp {{ number_format($pengurusAmt, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">5</td>
                        <td>Dana Sosial & Pendidikan</td>
                        <td style="text-align: center;">{{ number_format($danaSosialPct, 2, ',', '.') }}%</td>
                        <td style="text-align: right;">Rp {{ number_format($danaSosialAmt, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold; background-color: #f9f9f9;">
                        <td colspan="2" style="text-align: center;">TOTAL ALOKASI SHU ANGGOTA</td>
                        <td style="text-align: center;">100,00%</td>
                        <td style="text-align: right;">Rp {{ number_format($memberShu, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </li>
        <li>Menyetujui dan mengesahkan Rencana Kerja (RK) serta Rencana Anggaran Pendapatan dan Belanja (RAPB) Tahun Buku berikutnya.</li>
        <li>Memberikan kuasa dan mandat penuh kepada Pengurus Koperasi untuk melaksanakan seluruh keputusan RAT ini sesuai Anggaran Dasar Koperasi dan peraturan perundang-undangan yang berlaku.</li>
    </ol>

    <!-- Paragraf Penutup -->
    <p class="paragraph">
        Demikian Berita Acara Rapat Anggota Tahunan (RAT) ini dibuat dengan sebenarnya dan penuh rasa tanggung jawab untuk dipergunakan sebagaimana mestinya.
    </p>

    <div style="margin-top: 20px; text-align: right;">
        Bandung, {{ $hariTanggal ?? ($session->event_date ? $session->event_date->translatedFormat('d F Y') : date('d F Y')) }}
    </div>

    <!-- Lembar Tanda Tangan Pimpinan Sidang -->
    <div class="signature-section">
        <table class="sig-table">
            <tr>
                <td colspan="2" style="padding-bottom: 15px;">
                    <strong>PIMPINAN SIDANG RAT {{ $session->year }}</strong>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <div class="sig-title">Ketua Sidang</div>
                    <div class="sig-name">( {{ $ketuaSidang ?? '................................................' }} )</div>
                </td>
                <td style="width: 50%;">
                    <div class="sig-title">Sekretaris Sidang</div>
                    <div class="sig-name">( {{ $sekretarisSidang ?? '................................................' }} )</div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 30px; text-align: center; font-weight: bold;">MENGETAHUI & MENGESAHKAN:</div>

        <!-- Tanda Tangan Pengurus & Pengawas -->
        <table class="sig-table" style="margin-top: 15px;">
            <tr>
                <td style="width: 33%;">
                    <div class="sig-title">Ketua Koperasi</div>
                    <div class="sig-name">( {{ $ketuaKoperasi ?? '................................................' }} )</div>
                </td>
                <td style="width: 33%;">
                    <div class="sig-title">Sekretaris Koperasi</div>
                    <div class="sig-name">( {{ $sekretarisKoperasi ?? '................................................' }} )</div>
                </td>
                <td style="width: 33%;">
                    <div class="sig-title">Bendahara Koperasi</div>
                    <div class="sig-name">( {{ $bendaharaKoperasi ?? '................................................' }} )</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 25px;">
                    <div class="sig-title">Ketua Pengawas Koperasi</div>
                    <div class="sig-name">( {{ $ketuaPengawas ?? '................................................' }} )</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
