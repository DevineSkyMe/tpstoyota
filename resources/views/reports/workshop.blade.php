@extends('layouts.app')

@section('title', 'Laporan Hasil ' . $workshop->title . ' — TPS Portal')

@section('content')
    <style>
        /* Standalone Print Styling: Elegan, Bersih, Format Standar Toyota OMDD */
        .print-only {
            display: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 14mm;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 11pt !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Menghaluskan kontras agar tidak terlalu terang/menyilaukan */
            .print-card {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 8px !important;
                box-shadow: none !important;
            }
            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 1px solid #cbd5e1 !important;
            }
            .print-table th {
                background-color: #f1f5f9 !important;
                color: #334155 !important;
                border-bottom: 1px solid #94a3b8 !important;
                font-size: 9pt !important;
                text-transform: uppercase !important;
                padding: 7px 9px !important;
            }
            .print-table td {
                border-bottom: 1px solid #e2e8f0 !important;
                padding: 7px 9px !important;
                font-size: 9.5pt !important;
            }
            .print-table tr:nth-child(even) {
                background-color: #fcfcfd !important;
            }
            .print-badge-pass {
                background-color: #ecfdf5 !important;
                border: 1px solid #a7f3d0 !important;
                color: #065f46 !important;
                padding: 2px 7px !important;
                border-radius: 9999px !important;
                font-weight: 700 !important;
                font-size: 8.5pt !important;
                display: inline-block !important;
            }
            .print-badge-fail {
                background-color: #fff1f2 !important;
                border: 1px solid #fecdd3 !important;
                color: #9f1239 !important;
                padding: 2px 7px !important;
                border-radius: 9999px !important;
                font-weight: 700 !important;
                font-size: 8.5pt !important;
                display: inline-block !important;
            }
            tr {
                page-break-inside: avoid !important;
            }
        }
    </style>

    <div class="space-y-6">

        <!-- ======================================================== -->
        <!-- 1. KOP DOKUMEN RESMI (HANYA MUNCUL DI CETAKAN / PDF)     -->
        <!-- ======================================================== -->
        <div class="print-only">
            <!-- Kop Perusahaan & Divisi -->
            <div style="border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; width: 70%;">
                            <div style="font-size: 11pt; font-weight: 800; color: #0f172a; letter-spacing: 0.5px;">
                                PT TOYOTA MOTOR MANUFACTURING INDONESIA
                            </div>
                            <div style="font-size: 9.5pt; font-weight: 700; color: #1d63ed; margin-top: 1px;">
                                OPERATIONS MANAGEMENT DEVELOPMENT DIVISION (OMDD)
                            </div>
                            <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                                Toyota Production System (TPS) &bull; Key Person Development Program &bull; Plant 1 &amp; Plant 2
                            </div>
                        </td>
                        <td style="vertical-align: middle; text-align: right; width: 30%;">
                            <div style="font-size: 8pt; color: #64748b; font-family: monospace;">
                                NO DOKUMEN:<br>
                                <strong style="font-size: 9pt; color: #0f172a;">DOC-OMDD-WS-{{ $workshop->batch }}/{{ $workshop->year }}</strong>
                            </div>
                            <div style="font-size: 7.5pt; color: #94a3b8; margin-top: 3px;">
                                Rahasia / Internal Distribution
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Judul Laporan Cetakan -->
            <div style="text-align: center; margin-bottom: 18px;">
                <h1 style="font-size: 15pt; font-weight: 900; color: #0f172a; margin: 0 0 3px 0; letter-spacing: -0.3px; text-transform: uppercase;">
                    Laporan Hasil Evaluasi &amp; Kelulusan Workshop
                </h1>
                <p style="font-size: 9pt; color: #475569; margin: 0;">
                    Hasil Penilaian Kompetensi Peserta Workshop Tingkat TPS
                </p>
            </div>

            <!-- Detail Metadata Laporan -->
            <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; margin-bottom: 18px; font-size: 8.5pt;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 18%; color: #64748b; font-weight: 600; padding: 3px 0;">Nama Workshop</td>
                        <td style="width: 32%; color: #0f172a; font-weight: 700; padding: 3px 0;">: {{ $workshop->title }}</td>
                        <td style="width: 18%; color: #64748b; font-weight: 600; padding: 3px 0;">Lokasi Training</td>
                        <td style="width: 32%; color: #0f172a; font-weight: 700; padding: 3px 0;">: {{ $workshop->location ?? 'Sunter Training Center Room TPS-1' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-weight: 600; padding: 3px 0;">Batch / Tahun</td>
                        <td style="color: #0f172a; font-weight: 700; padding: 3px 0;">: Batch {{ $workshop->batch }} / Tahun {{ $workshop->year }}</td>
                        <td style="color: #64748b; font-weight: 600; padding: 3px 0;">Tanggal Cetak</td>
                        <td style="color: #0f172a; font-weight: 700; padding: 3px 0;">: {{ now()->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-weight: 600; padding: 3px 0;">Periode Pelaksanaan</td>
                        <td style="color: #0f172a; font-weight: 700; padding: 3px 0;">: 
                            {{ $workshop->start_date ? $workshop->start_date->format('d M Y') : '-' }} s/d 
                            {{ $workshop->end_date ? $workshop->end_date->format('d M Y') : '-' }}
                        </td>
                        <td style="color: #64748b; font-weight: 600; padding: 3px 0;">Passing Score</td>
                        <td style="color: #059669; font-weight: 700; padding: 3px 0;">: Minimal 80.0% (LULUS)</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 2. HEADER TAMPILAN SCREEN / WEB (DISEMBUNYIKAN SAAT PRINT)-->
        <!-- ======================================================== -->
        <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700">Laporan</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Hasil Workshop</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Laporan Hasil {{ $workshop->title }}</h1>
                <p class="text-xs text-slate-500">Batch {{ $workshop->batch }} &bull; {{ $workshop->year }} &bull; {{ $workshop->location ?? 'Lokasi belum ditentukan' }}</p>
            </div>

            <div class="flex items-center gap-2.5 self-start">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect width="12" height="8" x="6" y="14"/>
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 3. RINGKASAN HASIL EVALUASI (KOMPATIBEL WEB & CETAK)      -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="print-card bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Total Peserta</p>
                <p class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $summary['participants'] }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Orang terdaftar</p>
            </div>
            <div class="print-card bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Sudah Dinilai</p>
                <p class="text-xl sm:text-2xl font-black text-blue-600 mt-0.5">{{ $summary['evaluated'] }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Oleh Assessor OMDD</p>
            </div>
            <div class="print-card bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-emerald-700">Lulus</p>
                <p class="text-xl sm:text-2xl font-black text-emerald-600 mt-0.5">{{ $summary['passed'] }}</p>
                <p class="text-[10px] text-emerald-600/80 mt-0.5">&ge; 80.0% benchmark</p>
            </div>
            <div class="print-card bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-rose-700">Tidak Lulus</p>
                <p class="text-xl sm:text-2xl font-black text-rose-600 mt-0.5">{{ $summary['failed'] }}</p>
                <p class="text-[10px] text-rose-600/80 mt-0.5">&lt; 80.0% benchmark</p>
            </div>
            <div class="print-card bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-4 shadow-xs col-span-2 sm:col-span-1">
                <p class="text-[10px] uppercase font-bold tracking-wider text-purple-700">Rata-rata Skor</p>
                <p class="text-xl sm:text-2xl font-black text-purple-600 mt-0.5">{{ $summary['average'] }}%</p>
                <p class="text-[10px] text-purple-600/80 mt-0.5">Composite score</p>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 4. DISTRIBUSI KE EMAIL (HANYA WEB, DISEMBUNYIKAN SAAT PRINT)-->
        <!-- ======================================================== -->
        @if ($canSend)
            <div class="no-print bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            <span>Kirim Laporan Lengkap ke Email</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Laporan resmi beserta tabel nilai &amp; ringkasan hasil akan dikirim langsung dalam format email bersih (tanpa navbar/menu yang mengganggu).
                        </p>
                    </div>

                    <form method="POST" action="{{ route('reports.workshop.generate', $workshop) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition">
                            {{ $report ? 'Refresh Data' : 'Generate Dokumen' }}
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('reports.workshop.send', $workshop) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <div class="sm:col-span-4">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Target Pengiriman</label>
                            <select name="target" id="email-target-select" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="custom" selected>Kirim ke Email Tertentu</option>
                                <option value="both">HRD &amp; Manager (Inbox Portal)</option>
                                <option value="hrd">HRD saja</option>
                                <option value="manager">Manager saja</option>
                            </select>
                        </div>

                        <div class="sm:col-span-5">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Alamat Email Penerima</label>
                            <input type="email" name="custom_email" id="custom-email-input" value="{{ old('custom_email', 'suryadi19091@gmail.com') }}" placeholder="contoh: suryadi19091@gmail.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div class="sm:col-span-3 sm:self-end">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                <span>Kirim ke Email</span>
                            </button>
                        </div>
                    </div>
                </form>

                @if ($report)
                    <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-3 text-[11px] text-slate-400">
                        <span>Status: <strong class="text-slate-700 capitalize">{{ $report->status }}</strong></span>
                        @if ($report->sent_to_hrd_at)
                            <span>&bull; Terkirim ke HRD: <strong class="text-slate-700">{{ $report->sent_to_hrd_at->format('d M Y H:i') }}</strong></span>
                        @endif
                        @if ($report->sent_to_director_at)
                            <span>&bull; Terkirim ke Manager: <strong class="text-slate-700">{{ $report->sent_to_director_at->format('d M Y H:i') }}</strong></span>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- ======================================================== -->
        <!-- 5. TABEL HASIL EVALUASI PESERTA                           -->
        <!-- ======================================================== -->
        <div class="print-card bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="print-table w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                            <th class="py-3 px-3 text-center" style="width: 40px;">No</th>
                            <th class="py-3 px-4">Nama Peserta &amp; Departemen</th>
                            <th class="py-3 px-4">NIK</th>
                            <th class="py-3 px-4">Level</th>
                            <th class="py-3 px-4 text-right">Composite</th>
                            <th class="py-3 px-4 text-center">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($workshop->participants as $index => $participant)
                            @php
                                $evaluation = $participant->evaluation;
                                $isPass = $evaluation && $evaluation->isPassed();
                            @endphp
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="py-3 px-3 text-center font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-bold text-slate-900">{{ $participant->employee->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $participant->employee->department->name ?? '-' }} &bull; {{ $participant->employee->plant->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 font-mono font-semibold text-slate-600">{{ $participant->employee->username ?? '-' }}</td>
                                <td class="py-3 px-4 font-medium text-slate-700">{{ $participant->employee->current_tps_level ?? '-' }}</td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900 text-sm">
                                    {{ $evaluation ? $evaluation->composite_score . '%' : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($evaluation)
                                        @if ($isPass)
                                            <span class="print-badge-pass inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                LULUS (PASS)
                                            </span>
                                        @else
                                            <span class="print-badge-fail inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                TIDAK LULUS (FAIL)
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">
                                            Belum dinilai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400 font-medium">
                                    Belum ada peserta pada workshop ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 6. LEMBAR PENGESAHAN DOKUMEN (HANYA MUNCUL DI CETAKAN)    -->
        <!-- ======================================================== -->
        <div class="print-only" style="margin-top: 36px; page-break-inside: avoid;">
            <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 8.5pt;">
                <tr>
                    <td style="width: 33.3%; padding: 0 16px; vertical-align: top;">
                        <p style="margin: 0 0 2px 0; color: #64748b; font-weight: 600;">Disiapkan Oleh:</p>
                        <p style="margin: 0; font-weight: 700; color: #0f172a;">Assessor / Evaluator OMDD</p>
                        <div style="height: 52px;"></div>
                        <div style="border-top: 1px solid #475569; width: 85%; margin: 0 auto; padding-top: 4px;">
                            <strong style="color: #0f172a;">OMDD Specialist</strong>
                            <div style="color: #64748b; font-size: 7.5pt; margin-top: 1px;">Tanggal: .................................</div>
                        </div>
                    </td>
                    <td style="width: 33.3%; padding: 0 16px; vertical-align: top;">
                        <p style="margin: 0 0 2px 0; color: #64748b; font-weight: 600;">Diverifikasi Oleh:</p>
                        <p style="margin: 0; font-weight: 700; color: #0f172a;">HRD &amp; Training Development</p>
                        <div style="height: 52px;"></div>
                        <div style="border-top: 1px solid #475569; width: 85%; margin: 0 auto; padding-top: 4px;">
                            <strong style="color: #0f172a;">HRD Section Head</strong>
                            <div style="color: #64748b; font-size: 7.5pt; margin-top: 1px;">Tanggal: .................................</div>
                        </div>
                    </td>
                    <td style="width: 33.3%; padding: 0 16px; vertical-align: top;">
                        <p style="margin: 0 0 2px 0; color: #64748b; font-weight: 600;">Disetujui Oleh:</p>
                        <p style="margin: 0; font-weight: 700; color: #0f172a;">Division Head / Manager OMDD</p>
                        <div style="height: 52px;"></div>
                        <div style="border-top: 1px solid #475569; width: 85%; margin: 0 auto; padding-top: 4px;">
                            <strong style="color: #0f172a;">OMDD Department Head</strong>
                            <div style="color: #64748b; font-size: 7.5pt; margin-top: 1px;">Tanggal: .................................</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 24px; padding-top: 8px; border-top: 1px dashed #cbd5e1; text-align: center; font-size: 7.5pt; color: #94a3b8;">
                Dokumen ini merupakan cetakan resmi TPS-G Portal &bull; Operations Management Development Division &bull; &copy; {{ date('Y') }} PT Toyota Motor Manufacturing Indonesia.
            </div>
        </div>

    </div>
@endsection
