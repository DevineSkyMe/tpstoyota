<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Hasil Workshop {{ $workshop->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            padding: 0;
            margin: 0;
        }

        /* ---- HEADER ---- */
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 20px 28px 16px 28px;
        }

        .header-org {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .header-div {
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 0.9px;
            text-transform: uppercase;
            color: #38bdf8;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 3px;
            letter-spacing: -0.3px;
        }

        .header-subtitle {
            font-size: 10px;
            color: #cbd5e1;
        }

        /* ---- METADATA BAR ---- */
        .meta-bar {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 28px;
        }

        .meta-bar table {
            width: 100%;
        }

        .meta-bar td {
            font-size: 9px;
            color: #64748b;
        }

        .meta-bar strong {
            color: #334155;
        }

        /* ---- SECTION LABEL ---- */
        .section-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 8px;
            padding-top: 20px;
            padding-left: 28px;
        }

        /* ---- SUMMARY CARDS ---- */
        .summary-wrap {
            padding: 16px 28px 0 28px;
        }

        .summary-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }

        .summary-table td {
            text-align: center;
            padding: 10px 6px;
            border-radius: 6px;
            vertical-align: middle;
        }

        .card-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }

        .card-value {
            font-size: 22px;
            font-weight: bold;
            display: block;
        }

        .card-default {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .card-default .card-label { color: #64748b; }
        .card-default .card-value { color: #0f172a; }

        .card-blue {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .card-blue .card-label { color: #1d4ed8; }
        .card-blue .card-value { color: #1d63ed; }

        .card-green {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .card-green .card-label { color: #047857; }
        .card-green .card-value { color: #059669; }

        .card-red {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
        }

        .card-red .card-label { color: #b91c1c; }
        .card-red .card-value { color: #e11d48; }

        .card-purple {
            background-color: #faf5ff;
            border: 1px solid #e9d5ff;
        }

        .card-purple .card-label { color: #6b21a8; }
        .card-purple .card-value { color: #7e22ce; }

        /* ---- RESULTS TABLE ---- */
        .results-wrap {
            padding: 16px 28px 0 28px;
        }

        .results-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        .results-table thead tr {
            background-color: #1e293b;
            color: #e2e8f0;
        }

        .results-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .results-table thead th.right {
            text-align: right;
        }

        .results-table thead th.center {
            text-align: center;
        }

        .results-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .results-table tbody tr.odd {
            background-color: #ffffff;
        }

        .results-table tbody tr.even {
            background-color: #f8fafc;
        }

        .results-table tbody td {
            padding: 9px 10px;
            vertical-align: middle;
            color: #334155;
        }

        .results-table tbody td.right {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        .results-table tbody td.center {
            text-align: center;
        }

        .name-cell {
            font-weight: bold;
            color: #0f172a;
        }

        .dept-cell {
            font-size: 8px;
            color: #64748b;
        }

        .nik-cell {
            font-size: 9px;
            color: #475569;
        }

        .badge-pass {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background-color: #dcfce7;
            border: 1px solid #86efac;
            color: #15803d;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-fail {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background-color: #ffe4e6;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background-color: #f1f5f9;
            color: #64748b;
            font-size: 8px;
        }

        /* ---- SIGNATURE BLOCK ---- */
        .signature-wrap {
            padding: 24px 28px 0 28px;
        }

        .signature-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .signature-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .signature-table td {
            text-align: center;
            border-top: 1.5px solid #334155;
            padding-top: 6px;
            padding-bottom: 20px;
        }

        .sig-name {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
        }

        .sig-title {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }

        .sig-space {
            height: 44px;
        }

        /* ---- FOOTER ---- */
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 28px;
            margin-top: 24px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }

        .footer strong {
            color: #64748b;
        }

        .generated-at {
            font-size: 7.5px;
            color: #94a3b8;
            margin-top: 3px;
        }

        /* ---- DIVIDER ---- */
        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 0 28px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-org">PT Toyota Motor Manufacturing Indonesia</div>
        <div class="header-div">Operations Management Development Division (OMDD)</div>
        <div class="header-title">Laporan Hasil Workshop TPS-G</div>
        <div class="header-subtitle">
            {{ $workshop->title }} &bull; Batch {{ $workshop->batch }} (Tahun {{ $workshop->year }})
        </div>
    </div>

    {{-- METADATA BAR --}}
    <div class="meta-bar">
        <table>
            <tr>
                <td>
                    <strong>Lokasi:</strong> {{ $workshop->location ?? 'Sunter Training Center Room TPS-1' }}
                </td>
                <td style="text-align:right;">
                    <strong>Periode:</strong>
                    {{ $workshop->start_date ? $workshop->start_date->format('d M Y') : '-' }}
                    s/d
                    {{ $workshop->end_date ? $workshop->end_date->format('d M Y') : '-' }}
                </td>
                <td style="text-align:right; padding-left:16px;">
                    <strong>Dicetak:</strong> {{ now()->format('d M Y, H:i') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-wrap">
        <div class="summary-label">Ringkasan Hasil Evaluasi</div>
        <table class="summary-table">
            <tr>
                <td class="card-default" width="20%">
                    <span class="card-label">Peserta</span>
                    <span class="card-value">{{ $summary['participants'] }}</span>
                </td>
                <td class="card-blue" width="20%">
                    <span class="card-label">Dinilai</span>
                    <span class="card-value">{{ $summary['evaluated'] }}</span>
                </td>
                <td class="card-green" width="20%">
                    <span class="card-label">Lulus</span>
                    <span class="card-value">{{ $summary['passed'] }}</span>
                </td>
                <td class="card-red" width="20%">
                    <span class="card-label">Tidak Lulus</span>
                    <span class="card-value">{{ $summary['failed'] }}</span>
                </td>
                <td class="card-purple" width="20%">
                    <span class="card-label">Rata-rata</span>
                    <span class="card-value">{{ $summary['average'] }}%</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- RESULTS TABLE --}}
    <div class="results-wrap" style="margin-top: 20px;">
        <div class="results-label">Daftar Nilai &amp; Keputusan Peserta</div>
        <table class="results-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="32%">Nama Peserta</th>
                    <th width="14%">NIK</th>
                    <th width="12%">Level</th>
                    <th width="14%" class="right">Composite</th>
                    <th width="24%" class="center">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workshop->participants as $idx => $participant)
                    @php
                        $evaluation = $participant->evaluation;
                        $rowClass   = $idx % 2 === 0 ? 'odd' : 'even';
                        $isPass     = $evaluation && $evaluation->isPassed();
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td style="color:#94a3b8; font-size:9px;">{{ $idx + 1 }}</td>
                        <td>
                            <div class="name-cell">{{ $participant->employee->name ?? '-' }}</div>
                            <div class="dept-cell">{{ $participant->employee->department->name ?? '-' }}</div>
                        </td>
                        <td class="nik-cell">{{ $participant->employee->username ?? '-' }}</td>
                        <td style="font-weight:600; color:#334155;">{{ $participant->employee->current_tps_level ?? '-' }}</td>
                        <td class="right">
                            {{ $evaluation ? $evaluation->composite_score . '%' : '-' }}
                        </td>
                        <td class="center">
                            @if ($evaluation)
                                @if ($isPass)
                                    <span class="badge-pass">LULUS (PASS)</span>
                                @else
                                    <span class="badge-fail">TIDAK LULUS (FAIL)</span>
                                @endif
                            @else
                                <span class="badge-pending">Belum dinilai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="odd">
                        <td colspan="6" style="text-align:center; color:#94a3b8; font-style:italic; padding:20px;">
                            Belum ada peserta pada workshop ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- SIGNATURE BLOCK --}}
    <div class="signature-wrap">
        <div class="signature-label">Pengesahan Laporan</div>
        <table class="signature-table">
            <tr>
                <td width="33%">
                    <div class="sig-space"></div>
                    <div class="sig-name">__________________________</div>
                    <div class="sig-title">OMDD Assessor</div>
                </td>
                <td width="33%">
                    <div class="sig-space"></div>
                    <div class="sig-name">__________________________</div>
                    <div class="sig-title">HRD & Training Center</div>
                </td>
                <td width="33%">
                    <div class="sig-space"></div>
                    <div class="sig-name">__________________________</div>
                    <div class="sig-title">Manager / Direktur</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <strong>TPS-G Portal &bull; Operations Management Development Division</strong><br>
        Version 1.0 (FY25) &bull; &copy; {{ date('Y') }} PT Toyota Motor Manufacturing Indonesia
        <div class="generated-at">Dokumen ini digenerate secara otomatis oleh sistem TPS Portal pada {{ now()->format('d M Y \p\u\k\u\l H:i') }} WIB</div>
    </div>

</body>
</html>
