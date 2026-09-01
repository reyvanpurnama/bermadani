<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Anggota Koperasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1e293b;
            padding: 10px 24px;
        }

        /* ============ HEADER ============ */
        .header {
            text-align: center;
            padding: 14px 0 12px;
            border-bottom: 3px solid {{ config('cooperative.theme.primary') }};
            margin-bottom: 14px;
        }

        .header .org {
            font-size: 13px;
            font-weight: bold;
            color: {{ config('cooperative.theme.primary') }};
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .header .sub-org {
            font-size: 11px;
            font-weight: normal;
            color: #475569;
        }

        .header .doc-title {
            font-size: 15px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .header .doc-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
        }

        /* ============ INFO BOX ============ */
        .info-box {
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 14px;
            border-left: 4px solid {{ config('cooperative.theme.primary') }};
        }

        .info-box table {
            border-collapse: collapse;
        }

        .info-box td {
            padding: 2px 6px 2px 0;
            font-size: 10px;
            color: #334155;
            vertical-align: top;
        }

        .info-box td:first-child {
            font-weight: bold;
            white-space: nowrap;
            min-width: 130px;
        }

        /* ============ TABLE ============ */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            table-layout: fixed;
        }

        table.report-table thead tr {
            background: {{ config('cooperative.theme.primary') }};
            color: white;
        }

        table.report-table th {
            padding: 9px 7px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #0d47a1;
        }

        table.report-table th.center { text-align: center; }

        table.report-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        table.report-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.report-table td {
            padding: 8px 7px;
            font-size: 10px;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
        }

        table.report-table td.center { text-align: center; }

        .name-cell {
            font-weight: bold;
            text-transform: uppercase;
        }

        .username-cell {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 0.5px;
        }

        .password-cell {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #dc2626;
            letter-spacing: 0.5px;
        }

        .nomor-cell {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #64748b;
        }

        .sign-cell {
            height: 36px;
        }

        /* ============ NOTE BOX ============ */
        .note-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 9px 12px;
            margin-top: 10px;
            margin-bottom: 16px;
            font-size: 9px;
            color: #78350f;
        }

        .note-box strong {
            font-size: 10px;
        }

        /* ============ SIGNATURE ============ */
        .sig-section {
            display: table;
            width: 100%;
            margin-top: 18px;
        }

        .sig-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 12px;
        }

        .sig-box .sig-city-date {
            font-size: 10px;
            margin-bottom: 65px;
        }

        .sig-box .sig-role {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .sig-box .sig-name {
            font-size: 10px;
            font-weight: bold;
        }

        /* ============ FOOTER ============ */
        .footer {
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        /* ============ PAGE BREAK ============ */
        .page-break { page-break-before: always; }

        /* ============ BADGE ============ */
        .badge-coop {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 3px;
            padding: 1px 4px;
            font-size: 8px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="org">{{ strtoupper(config('cooperative.legal_name')) }}</div>
        <div class="sub-org">{{ config('cooperative.parent_org') }}</div>
        <div class="doc-title">DAFTAR AKUN PORTAL ANGGOTA KOPERASI</div>
        <div class="doc-subtitle">Dokumen Sosialisasi Akses Portal Sistem {{ config('cooperative.short_name') }} &mdash; Rahasia &amp; Terbatas</div>
    </div>

    {{-- INFO BOX --}}
    <div class="info-box">
        <table>
            <tr>
                <td>Dicetak Pada</td>
                <td>: {{ $generatedAt }}</td>
            </tr>
            <tr>
                <td>Total Anggota</td>
                <td>: {{ count($members) }} Anggota Koperasi</td>
            </tr>
            @if(!empty($filters['filterJoinMonth']) || !empty($filters['filterJoinYear']))
            <tr>
                <td>Filter Bergabung</td>
                <td>: {{ !empty($filters['filterJoinMonth']) ? ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][(int)$filters['filterJoinMonth']] : 'Semua Bulan' }}
                    {{ !empty($filters['filterJoinYear']) ? $filters['filterJoinYear'] : '' }}</td>
            </tr>
            @endif
            @if(!empty($filters['filterUnitKerja']))
            <tr>
                <td>Unit Kerja</td>
                <td>: {{ $filters['filterUnitKerja'] }}</td>
            </tr>
            @endif
            @if(!empty($filters['search']))
            <tr>
                <td>Pencarian</td>
                <td>: &ldquo;{{ $filters['search'] }}&rdquo;</td>
            </tr>
            @endif
            <tr>
                <td>Login URL</td>
                <td>: <strong>{{ $loginUrl }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- NOTE --}}
    <div class="note-box">
        <strong>📋 PETUNJUK PENGGUNAAN AKUN:</strong><br><br>
        &bull; <strong>Username</strong> = Nomor Anggota Anda (contoh: <code>21000001</code>)<br>
        &bull; <strong>Password Awal</strong> = Nomor Anggota Anda (sama dengan username, harap segera diganti setelah login pertama)<br>
        &bull; Akses portal di: <strong>{{ $loginUrl }}</strong><br>
        &bull; Dokumen ini bersifat <strong>rahasia</strong>. Jangan bagikan akun kepada pihak lain.
    </div>

    {{-- TABLE --}}
    <table class="report-table">
        <thead>
            <tr>
                <th class="center" style="width: 5%;">No</th>
                <th style="width: 32%;">Nama Anggota</th>
                <th style="width: 16%;">Username</th>
                <th style="width: 16%;">Password Awal</th>
                <th style="width: 19%;">Unit Kerja</th>
                <th class="center" style="width: 12%;">Tanda Terima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $index => $member)
                @php
                    $username = $member->nomorAnggota ?? explode('@', $member->email ?? '')[0];
                    $passwordAwal = config('cooperative.default_password', 'password');
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="name-cell">{{ strtoupper($member->name) }}</td>
                    <td class="username-cell">{{ $username }}</td>
                    <td class="password-cell">{{ $passwordAwal }}</td>
                    <td style="font-size: 9px; color: #cbd5e1;">&nbsp;</td>
                    <td class="sign-cell center"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- SIGNATURE SECTION --}}
    <div class="sig-section">
        <div class="sig-box">&nbsp;</div>
        <div class="sig-box">
            <div class="sig-city-date">{{ config('cooperative.city') }}, {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="sig-role">Ketua Koperasi</div>
            <div style="margin-top: 55px;">
                <div class="sig-name">({{ coop_setting('ketua_name') }})</div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem {{ config('cooperative.short_name') }} &bull; Dicetak: {{ $generatedAt }}</p>
        <p>RAHASIA &mdash; Hanya untuk keperluan sosialisasi internal anggota koperasi</p>
    </div>

</body>
</html>
