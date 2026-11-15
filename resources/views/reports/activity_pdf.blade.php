<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Activity Kaizen TPS-G</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
        }

        /* HEADER */
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 20px 28px 16px 28px;
        }
        .header-org {
            font-size: 8px; font-weight: bold;
            letter-spacing: 1.2px; text-transform: uppercase;
            color: #94a3b8; margin-bottom: 2px;
        }
        .header-div {
            font-size: 7.5px; font-weight: bold;
            letter-spacing: 0.9px; text-transform: uppercase;
            color: #38bdf8; margin-bottom: 10px;
        }
        .header-title {
            font-size: 18px; font-weight: bold;
            color: #ffffff; margin-bottom: 3px; letter-spacing: -0.3px;
        }
        .header-subtitle { font-size: 10px; color: #cbd5e1; }

        /* META BAR */
        .meta-bar {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 28px;
        }
        .meta-bar table { width: 100%; }
        .meta-bar td { font-size: 9px; color: #64748b; }
        .meta-bar strong { color: #334155; }

        /* SUMMARY */
        .summary-wrap { padding: 16px 28px 0 28px; }
        .summary-label {
            font-size: 8px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: #64748b; margin-bottom: 8px;
        }
        .summary-table { width: 100%; border-collapse: separate; border-spacing: 4px; }
        .summary-table td {
            text-align: center; padding: 10px 6px;
            border-radius: 6px; vertical-align: middle;
        }
        .card-label { font-size: 8px; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 4px; }
        .card-value { font-size: 20px; font-weight: bold; display: block; }

        .card-default { background-color: #f8fafc; border: 1px solid #e2e8f0; }
        .card-default .card-label { color: #64748b; }
        .card-default .card-value { color: #0f172a; }

        .card-green { background-color: #f0fdf4; border: 1px solid #bbf7d0; }
        .card-green .card-label { color: #047857; }
        .card-green .card-value { color: #059669; }

        .card-blue { background-color: #eff6ff; border: 1px solid #bfdbfe; }
        .card-blue .card-label { color: #1d4ed8; }
        .card-blue .card-value { color: #1d63ed; }

        .card-red { background-color: #fff1f2; border: 1px solid #fecdd3; }
        .card-red .card-label { color: #b91c1c; }
        .card-red .card-value { color: #e11d48; }

        .card-slate { background-color: #f1f5f9; border: 1px solid #e2e8f0; }
        .card-slate .card-label { color: #64748b; }
        .card-slate .card-value { color: #475569; }

        .card-purple { background-color: #faf5ff; border: 1px solid #e9d5ff; }
        .card-purple .card-label { color: #6b21a8; }
        .card-purple .card-value { color: #7e22ce; }

        /* RESULTS TABLE */
        .results-wrap { padding: 20px 28px 0 28px; }
        .results-label {
            font-size: 8px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: #64748b; margin-bottom: 8px;
        }
        .results-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        .results-table thead tr { background-color: #1e293b; color: #e2e8f0; }
        .results-table thead th {
            padding: 7px 8px; text-align: left;
            font-size: 7.5px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.5px; border: none;
        }
        .results-table thead th.right { text-align: right; }
        .results-table thead th.center { text-align: center; }
        .results-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        .results-table tbody tr.odd  { background-color: #ffffff; }
        .results-table tbody tr.even { background-color: #f8fafc; }
        .results-table tbody td { padding: 7px 8px; vertical-align: middle; color: #334155; }
        .results-table tbody td.right  { text-align: right; font-weight: bold; color: #0f172a; }
        .results-table tbody td.center { text-align: center; }

        .name-cell  { font-weight: bold; color: #0f172a; font-size: 9px; }
        .sub-cell   { font-size: 7.5px; color: #64748b; }
        .mono-cell  { font-size: 8.5px; color: #475569; }

        /* Status badges */
        .badge { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 7.5px; font-weight: bold; text-transform: uppercase; }
        .badge-completed { background-color: #dcfce7; border: 1px solid #86efac; color: #15803d; }
        .badge-inprogress { background-color: #dbeafe; border: 1px solid #93c5fd; color: #1d4ed8; }
        .badge-delay { background-color: #ffe4e6; border: 1px solid #fca5a5; color: #b91c1c; }
        .badge-notstarted { background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #64748b; }

        /* Progress bar */
        .progress-bar-bg { background-color: #e2e8f0; border-radius: 999px; height: 5px; width: 60px; }
        .progress-bar-fill { background-color: #1d63ed; border-radius: 999px; height: 5px; }

        /* FOOTER */
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 28px;
            margin-top: 24px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
        .footer strong { color: #64748b; }
        .generated-at { font-size: 7.5px; color: #94a3b8; margin-top: 3px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-org">PT Toyota Motor Manufacturing Indonesia</div>
        <div class="header-div">Operations Management Development Division (OMDD)</div>
        <div class="header-title">Laporan Activity Kaizen TPS-G</div>
        <div class="header-subtitle">
            Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }}
            @if ($filters['department_id'])
                &bull; Departemen Tertentu
            @else
                &bull; Semua Departemen
            @endif
            @if ($filters['status'])
                &bull; Status: {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}
            @endif
        </div>
    </div>

    {{-- META BAR --}}
    <div class="meta-bar">
        <table>
            <tr>
                <td><strong>Total Activity:</strong> {{ $summary['total'] }} item</td>
                <td style="text-align:center;"><strong>Periode:</strong> {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }}</td>
                <td style="text-align:right;"><strong>Dicetak:</strong> {{ now()->format('d M Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-wrap">
        <div class="summary-label">Ringkasan Status Activity</div>
        <table class="summary-table">
            <tr>
                <td class="card-default" width="16.6%">
                    <span class="card-label">Total</span>
                    <span class="card-value">{{ $summary['total'] }}</span>
                </td>
                <td class="card-green" width="16.6%">
                    <span class="card-label">Completed</span>
                    <span class="card-value">{{ $summary['completed'] }}</span>
                </td>
                <td class="card-blue" width="16.6%">
                    <span class="card-label">In Progress</span>
                    <span class="card-value">{{ $summary['in_progress'] }}</span>
                </td>
                <td class="card-red" width="16.6%">
                    <span class="card-label">Delayed</span>
                    <span class="card-value">{{ $summary['delayed'] }}</span>
                </td>
                <td class="card-slate" width="16.6%">
                    <span class="card-label">Not Started</span>
                    <span class="card-value">{{ $summary['not_started'] }}</span>
                </td>
                <td class="card-purple" width="16.6%">
                    <span class="card-label">Avg Progress</span>
                    <span class="card-value">{{ $summary['avg_progress'] }}%</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- RESULTS TABLE --}}
    <div class="results-wrap">
        <div class="results-label">Daftar Activity Kaizen &amp; Progress</div>
        <table class="results-table">
            <thead>
                <tr>
                    <th width="6%">ID</th>
                    <th width="26%">Judul Activity</th>
                    <th width="18%">Karyawan</th>
                    <th width="16%">Departemen</th>
                    <th width="16%">Periode</th>
                    <th width="10%" class="center">Status</th>
                    <th width="8%" class="right">Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activities as $idx => $activity)
                    @php
                        $rowClass = $idx % 2 === 0 ? 'odd' : 'even';
                        $badgeClass = match($activity->status) {
                            'completed'   => 'badge-completed',
                            'in_progress' => 'badge-inprogress',
                            'delay'       => 'badge-delay',
                            default       => 'badge-notstarted',
                        };
                        $statusLabel = match($activity->status) {
                            'completed'   => 'Completed',
                            'in_progress' => 'In Progress',
                            'delay'       => 'Delayed',
                            default       => 'Not Started',
                        };
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="mono-cell" style="color:#1d63ed; font-weight:bold;">KZ-{{ sprintf('%03d', $activity->id) }}</td>
                        <td>
                            <div class="name-cell">{{ $activity->title }}</div>
                            <div class="sub-cell">{{ $activity->category }}</div>
                        </td>
                        <td>
                            <div class="name-cell">{{ $activity->employee->name ?? '-' }}</div>
                            <div class="sub-cell mono-cell">{{ $activity->employee->username ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-size:9px; color:#334155;">{{ $activity->employee->department->name ?? '-' }}</div>
                            <div class="sub-cell">{{ $activity->employee->plant->name ?? '-' }}</div>
                        </td>
                        <td style="font-size:8.5px; color:#475569; white-space:nowrap;">
                            {{ $activity->planned_start?->format('d M Y') }}<br>
                            <span style="color:#94a3b8;">s/d</span> {{ $activity->planned_end?->format('d M Y') }}
                        </td>
                        <td class="center">
                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="right">{{ $activity->progress_pct }}%</td>
                    </tr>
                @empty
                    <tr class="odd">
                        <td colspan="7" style="text-align:center; color:#94a3b8; font-style:italic; padding:20px;">
                            Tidak ada activity pada periode / filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
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
