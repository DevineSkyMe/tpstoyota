<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Workshop {{ $workshop->title }}</title>
</head>
<body style="margin: 0; padding: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; line-height: 1.5;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 680px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);">
        
        <!-- Header Banner: Toyota OMDD -->
        <tr>
            <td style="padding: 24px 28px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <div style="font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8;">
                                PT TOYOTA MOTOR MANUFACTURING INDONESIA
                            </div>
                            <div style="font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #38bdf8; margin-top: 2px;">
                                OPERATIONS MANAGEMENT DEVELOPMENT DIVISION (OMDD)
                            </div>
                            <h1 style="margin: 12px 0 4px 0; font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Laporan Hasil Workshop
                            </h1>
                            <p style="margin: 0; font-size: 13px; color: #cbd5e1;">
                                {{ $workshop->title }} &bull; Batch {{ $workshop->batch }} (Tahun {{ $workshop->year }})
                            </p>
                        </td>
                        <td align="right" valign="top" style="width: 60px;">
                            <div style="width: 44px; height: 44px; border-radius: 10px; background-color: #1d63ed; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; text-align: center; line-height: 44px;">
                                TPS
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Workshop Metadata Info -->
        <tr>
            <td style="padding: 16px 28px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; color: #64748b;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <strong style="color: #334155;">Lokasi:</strong> {{ $workshop->location ?? 'Sunter Training Center Room TPS-1' }}
                        </td>
                        <td align="right">
                            <strong style="color: #334155;">Periode:</strong> 
                            {{ $workshop->start_date ? $workshop->start_date->format('d M Y') : '-' }} s/d 
                            {{ $workshop->end_date ? $workshop->end_date->format('d M Y') : '-' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Content Body -->
        <tr>
            <td style="padding: 24px 28px;">

                <!-- Executive Summary Cards -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 8px;">
                        Ringkasan Hasil Evaluasi
                    </div>

                    <table width="100%" cellpadding="0" cellspacing="6" style="margin: 0 -6px;">
                        <tr>
                            <td width="20%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 10px; text-align: center;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b;">Peserta</div>
                                <div style="font-size: 20px; font-weight: 900; color: #0f172a; margin-top: 2px;">{{ $summary['participants'] }}</div>
                            </td>
                            <td width="20%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 10px; text-align: center;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b;">Dinilai</div>
                                <div style="font-size: 20px; font-weight: 900; color: #1d63ed; margin-top: 2px;">{{ $summary['evaluated'] }}</div>
                            </td>
                            <td width="20%" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px 10px; text-align: center;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #047857;">Lulus</div>
                                <div style="font-size: 20px; font-weight: 900; color: #059669; margin-top: 2px;">{{ $summary['passed'] }}</div>
                            </td>
                            <td width="20%" style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 12px 10px; text-align: center;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #b91c1c;">Tidak Lulus</div>
                                <div style="font-size: 20px; font-weight: 900; color: #e11d48; margin-top: 2px;">{{ $summary['failed'] }}</div>
                            </td>
                            <td width="20%" style="background-color: #faf5ff; border: 1px solid #e9d5ff; border-radius: 10px; padding: 12px 10px; text-align: center;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #6b21a8;">Rata-rata</div>
                                <div style="font-size: 20px; font-weight: 900; color: #7e22ce; margin-top: 2px;">{{ $summary['average'] }}%</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Participants Results Table -->
                <div style="margin-top: 24px;">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 8px;">
                        Daftar Nilai &amp; Keputusan Peserta
                    </div>

                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #f1f5f9; border-bottom: 1px solid #cbd5e1; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.5px;">
                                <th style="padding: 10px 12px;">No</th>
                                <th style="padding: 10px 12px;">Nama Peserta</th>
                                <th style="padding: 10px 12px;">NIK</th>
                                <th style="padding: 10px 12px;">Level</th>
                                <th style="padding: 10px 12px; text-align: right;">Composite</th>
                                <th style="padding: 10px 12px; text-align: center;">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workshop->participants as $idx => $participant)
                                @php
                                    $evaluation = $participant->evaluation;
                                    $rowBg = $idx % 2 === 0 ? '#ffffff' : '#f8fafc';
                                    $isPass = $evaluation && $evaluation->isPassed();
                                @endphp
                                <tr style="background-color: {{ $rowBg }}; border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 10px 12px; color: #64748b; font-size: 11px;">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td style="padding: 10px 12px;">
                                        <div style="font-weight: 700; color: #0f172a;">{{ $participant->employee->name ?? '-' }}</div>
                                        <div style="font-size: 10px; color: #64748b;">{{ $participant->employee->department->name ?? '-' }}</div>
                                    </td>
                                    <td style="padding: 10px 12px; font-family: monospace; color: #475569; font-size: 11px;">
                                        {{ $participant->employee->username ?? '-' }}
                                    </td>
                                    <td style="padding: 10px 12px; font-weight: 600; color: #334155;">
                                        {{ $participant->employee->current_tps_level ?? '-' }}
                                    </td>
                                    <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #0f172a; font-size: 13px;">
                                        {{ $evaluation ? $evaluation->composite_score . '%' : '-' }}
                                    </td>
                                    <td style="padding: 10px 12px; text-align: center;">
                                        @if ($evaluation)
                                            @if ($isPass)
                                                <span style="display: inline-block; padding: 3px 10px; border-radius: 9999px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                                    LULUS (PASS)
                                                </span>
                                            @else
                                                <span style="display: inline-block; padding: 3px 10px; border-radius: 9999px; background-color: #fff1f2; border: 1px solid #fecdd3; color: #b91c1c; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                                    TIDAK LULUS (FAIL)
                                                </span>
                                            @endif
                                        @else
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 9999px; background-color: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 600;">
                                                Belum dinilai
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 24px; text-align: center; color: #94a3b8; font-style: italic;">
                                        Belum ada peserta pada workshop ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Call to action button -->
                <div style="margin-top: 28px; text-align: center;">
                    <a href="{{ $portalUrl }}" style="display: inline-block; background-color: #1d63ed; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 12px; box-shadow: 0 2px 6px rgba(29, 99, 237, 0.25);">
                        Buka Laporan Lengkap di TPS Portal &rarr;
                    </a>
                </div>

            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 16px 28px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; text-align: center;">
                TPS Portal &bull; Operations Management Development Division &bull; &copy; {{ date('Y') }} PT Toyota Motor Manufacturing Indonesia.<br>
                Email otomatis ini dikirim sebagai laporan resmi hasil workshop &amp; evaluasi TPS Key Person.
            </td>
        </tr>

    </table>

</body>
</html>
