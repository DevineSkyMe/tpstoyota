<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Activity Kaizen TPS-G</title>
</head>
<body style="margin: 0; padding: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; line-height: 1.5;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 680px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(15,23,42,0.05);">

        <!-- Header -->
        <tr>
            <td style="padding: 24px 28px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
                <div style="font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8;">
                    PT TOYOTA MOTOR MANUFACTURING INDONESIA
                </div>
                <div style="font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #38bdf8; margin-top: 2px;">
                    OPERATIONS MANAGEMENT DEVELOPMENT DIVISION (OMDD)
                </div>
                <h1 style="margin: 12px 0 4px 0; font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                    Laporan Activity Kaizen TPS-G
                </h1>
                <p style="margin: 0; font-size: 13px; color: #cbd5e1;">
                    Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }}
                </p>
            </td>
        </tr>

        <!-- Meta bar -->
        <tr>
            <td style="padding: 12px 28px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; color: #64748b;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><strong style="color:#334155;">Total Activity:</strong> {{ $summary['total'] }} item</td>
                        <td align="right"><strong style="color:#334155;">Dikirim:</strong> {{ now()->format('d M Y, H:i') }} WIB</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 24px 28px;">

                <!-- Summary Cards -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 8px;">
                        Ringkasan Status Activity
                    </div>
                    <table width="100%" cellpadding="0" cellspacing="4">
                        <tr>
                            <td width="16%" style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 6px; text-align:center;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#64748b;">Total</div>
                                <div style="font-size:20px; font-weight:900; color:#0f172a; margin-top:2px;">{{ $summary['total'] }}</div>
                            </td>
                            <td width="17%" style="background-color:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:10px 6px; text-align:center;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#047857;">Completed</div>
                                <div style="font-size:20px; font-weight:900; color:#059669; margin-top:2px;">{{ $summary['completed'] }}</div>
                            </td>
                            <td width="17%" style="background-color:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:10px 6px; text-align:center;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#1d4ed8;">In Progress</div>
                                <div style="font-size:20px; font-weight:900; color:#1d63ed; margin-top:2px;">{{ $summary['in_progress'] }}</div>
                            </td>
                            <td width="17%" style="background-color:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:10px 6px; text-align:center;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#b91c1c;">Delayed</div>
                                <div style="font-size:20px; font-weight:900; color:#e11d48; margin-top:2px;">{{ $summary['delayed'] }}</div>
                            </td>
                            <td width="16%" style="background-color:#f1f5f9; border:1px solid #e2e8f0; border-radius:10px; padding:10px 6px; text-align:center;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#64748b;">Not Started</div>
                                <div style="font-size:20px; font-weight:900; color:#475569; margin-top:2px;">{{ $summary['not_started'] }}</div>
                            </td>
                            <td width="17%" style="background-color:#faf5ff; border:1px solid #e9d5ff; border-radius:10px; padding:10px 6px; text-align:center;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#6b21a8;">Avg Progress</div>
                                <div style="font-size:20px; font-weight:900; color:#7e22ce; margin-top:2px;">{{ $summary['avg_progress'] }}%</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Activity Table -->
                <div style="margin-top: 24px;">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 8px;">
                        Daftar Activity &amp; Progress
                    </div>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; font-size: 11px;">
                        <thead>
                            <tr style="background-color: #f1f5f9; border-bottom: 1px solid #cbd5e1; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px;">
                                <th style="padding: 8px 10px;">ID</th>
                                <th style="padding: 8px 10px;">Judul</th>
                                <th style="padding: 8px 10px;">Karyawan</th>
                                <th style="padding: 8px 10px;">Departemen</th>
                                <th style="padding: 8px 10px; text-align:center;">Status</th>
                                <th style="padding: 8px 10px; text-align:right;">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $idx => $activity)
                                @php
                                    $rowBg = $idx % 2 === 0 ? '#ffffff' : '#f8fafc';
                                    [$badgeBg, $badgeBorder, $badgeColor] = match($activity->status) {
                                        'completed'   => ['#ecfdf5', '#a7f3d0', '#047857'],
                                        'in_progress' => ['#eff6ff', '#bfdbfe', '#1d4ed8'],
                                        'delay'       => ['#fff1f2', '#fecdd3', '#b91c1c'],
                                        default       => ['#f1f5f9', '#e2e8f0', '#64748b'],
                                    };
                                    $statusLabel = match($activity->status) {
                                        'completed'   => 'Completed',
                                        'in_progress' => 'In Progress',
                                        'delay'       => 'Delayed',
                                        default       => 'Not Started',
                                    };
                                @endphp
                                <tr style="background-color: {{ $rowBg }}; border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 8px 10px; font-family: monospace; font-weight: bold; color: #1d63ed; font-size: 10px;">KZ-{{ sprintf('%03d', $activity->id) }}</td>
                                    <td style="padding: 8px 10px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 11px;">{{ $activity->title }}</div>
                                        <div style="font-size: 9px; color: #64748b;">{{ $activity->category }}</div>
                                    </td>
                                    <td style="padding: 8px 10px;">
                                        <div style="font-weight: 600; color: #334155;">{{ $activity->employee->name ?? '-' }}</div>
                                        <div style="font-size: 9px; color: #64748b; font-family: monospace;">{{ $activity->employee->username ?? '-' }}</div>
                                    </td>
                                    <td style="padding: 8px 10px; color: #475569; font-size: 10px;">
                                        {{ $activity->employee->department->name ?? '-' }}
                                    </td>
                                    <td style="padding: 8px 10px; text-align: center;">
                                        <span style="display:inline-block; padding:2px 8px; border-radius:9999px; background-color:{{ $badgeBg }}; border:1px solid {{ $badgeBorder }}; color:{{ $badgeColor }}; font-size:9px; font-weight:700; text-transform:uppercase;">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td style="padding: 8px 10px; text-align: right; font-weight: 700; color: #0f172a; font-size: 12px;">{{ $activity->progress_pct }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 24px; text-align: center; color: #94a3b8; font-style: italic;">
                                        Tidak ada activity pada periode / filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- CTA Button -->
                <div style="margin-top: 28px; text-align: center;">
                    <a href="{{ $portalUrl }}" style="display: inline-block; background-color: #1d63ed; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 12px; box-shadow: 0 2px 6px rgba(29,99,237,0.25);">
                        Buka Laporan Activity di TPS Portal &rarr;
                    </a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 16px 28px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; text-align: center;">
                TPS Portal &bull; Operations Management Development Division &bull; &copy; {{ date('Y') }} PT Toyota Motor Manufacturing Indonesia.<br>
                Email otomatis ini dikirim sebagai laporan resmi rekap Activity Kaizen TPS-G.
            </td>
        </tr>

    </table>

</body>
</html>
