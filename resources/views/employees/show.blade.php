<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pegawai: {{ $employee->name }} — TPS Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @include('partials.theme')
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <div class="min-h-full flex flex-row">
        
        <!-- Left Sidebar Component -->
        @include('partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
            
            <!-- Top Navbar Component -->
            @include('partials.navbar')

            @include('partials.flash')

            <!-- Main Container -->
            <main class="flex-1 p-4 md:p-8 space-y-6 max-w-7xl w-full mx-auto">

                <!-- 1. Breadcrumbs & Header Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                        <span class="text-slate-300">/</span>
                        <a href="{{ route('employees.index') }}" class="text-slate-500 hover:text-slate-700">Employees</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900 font-semibold">{{ $employee->name }}</span>
                    </nav>

                    <div class="flex items-center gap-2.5">
                        @if (($currentUser ?? auth()->user())?->isAdmin())
                            <a href="{{ route('employees.edit', $employee->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs transition">
                                <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                <span>Edit Profil</span>
                            </a>
                        @endif

                        @if($employee->workshopCandidates->isNotEmpty())
                            <a href="{{ route('candidates.show', $employee->workshopCandidates->first()->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold shadow-xs transition">
                                <span>Lihat Penilaian OMDD</span>
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Flash Message -->
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- 2. Hero Profile Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        @include('partials.avatar', [
                            'photo' => $employee->photo,
                            'name' => $employee->name,
                            'size' => 'w-20 h-20 text-2xl',
                            'rounded' => 'rounded-2xl',
                            'bgClass' => 'bg-slate-900',
                            'extraClass' => 'shadow-md',
                        ])
                        <div class="space-y-1">
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-xl md:text-2xl font-black text-slate-900">{{ $employee->name }}</h1>
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-xs font-bold">NIK {{ $employee->username }}</span>
                            </div>
                            <p class="text-xs font-medium text-slate-600">{{ $employee->position }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $employee->department->name ?? '-' }} &bull; {{ $employee->plant->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Level Badge & Status -->
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="px-4 py-2 rounded-2xl bg-blue-50 border border-blue-200 text-center">
                            <p class="text-[9px] uppercase font-bold text-slate-400">Level TPS</p>
                            <p class="text-sm font-extrabold text-blue-700 mt-0.5">{{ $employee->current_tps_level }}</p>
                        </div>
                        <div class="px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                            <p class="text-[9px] uppercase font-bold text-slate-400">Status</p>
                            <p class="text-sm font-extrabold {{ $employee->status === 'active' ? 'text-emerald-600' : 'text-slate-400' }} mt-0.5">
                                {{ ucfirst($employee->status) }}
                            </p>
                        </div>
                    </div>
                </div>

                @php
                    $levels = \App\Models\Employee::TPS_LEVELS;
                    $currentLevel = $employee->normalizedTpsLevel();
                    $currentIndex = $currentLevel ? array_search($currentLevel, $levels, true) : -1;
                    $levelMeta = [
                        'Advance' => ['FOUNDATION', 'Fondasi Kaizen, Standard Work & 5S'],
                        'KP1' => ['PRACTITIONER', 'Implementasi Kaizen & A3 Report'],
                        'KP2' => ['ADVANCED', 'Problem Solving & Jishuken'],
                        'KP3' => ['IMPLEMENTER', 'Line Leader, 5-Why Problem Solving & Jishuken'],
                        'KP4' => ['MASTER (TERTINGGI)', 'Master Coach OMDD & Pemimpin Yokoten Global'],
                    ];
                @endphp

                <!-- TPS Career Progression Pathway (Advance -> KP1 -> KP2 -> KP3 -> KP4) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Jenjang Karir Praktisi TPS: Advance &rarr; KP1 &rarr; KP2 &rarr; KP3 &rarr; KP4
                            </h3>
                        </div>
                        @if($employee->canPromote())
                            <span class="text-[11px] font-bold text-blue-600">
                                Target Kenaikan Berikutnya: {{ $employee->getNextTpsLevel() }}
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-emerald-600">
                                &check; Telah Mencapai Level Tertinggi (KP4 Master)
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        @foreach ($levels as $i => $level)
                            @php
                                $isPassed = $currentIndex > $i;
                                $isCurrent = $currentIndex === $i;
                                $border = $isCurrent
                                    ? 'bg-blue-50 border-blue-500 shadow-xs'
                                    : ($isPassed ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200 opacity-60');
                                $labelColor = $isCurrent ? 'text-blue-700' : ($isPassed ? 'text-emerald-700' : 'text-slate-400');
                            @endphp
                            <div class="p-3 rounded-xl border {{ $border }}">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[10px] font-bold {{ $labelColor }}">{{ $levelMeta[$level][0] }}</span>
                                    @if($isCurrent)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-blue-600 text-white">Posisi Saat Ini</span>
                                    @elseif($isPassed)
                                        <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">&check; Lulus</span>
                                    @else
                                        <span class="text-[9px] text-slate-400 font-medium">Target</span>
                                    @endif
                                </div>
                                <h4 class="text-xs font-extrabold text-slate-900 mt-1">{{ $level }}</h4>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $levelMeta[$level][1] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Two Column Info Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left: Personal & Corporate Info (1 Col) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                            Detail Karyawan & Penempatan
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div>
                                <p class="text-slate-400">Plant / Lokasi Fabrikasi</p>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->plant->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Divisi</p>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->division->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Departemen</p>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->department->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Tanggal Lahir</p>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Level Sebelumnya</p>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->previous_tps_level ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Tahun Kelulusan TPS</p>
                                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->tahun_lulus_terakhir ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Kaizen Activities & Workshop History (2 Cols) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Kaizen Projects List -->
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Riwayat Kaizen & Proyek TPS ({{ $totalActivities }})
                                    </h3>
                                </div>
                                <a href="{{ route('activity.create') }}?employee_id={{ $employee->id }}" class="text-xs font-bold text-blue-600 hover:underline">
                                    + Tambah Aktivitas
                                </a>
                            </div>

                            <div class="divide-y divide-slate-100">
                                @forelse($employee->activityPlans as $act)
                                    <div class="py-3 flex items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">KZ-{{ sprintf('%03d', $act->id) }}</span>
                                                <a href="{{ route('activity.show', $act->id) }}" class="text-xs font-bold text-slate-900 hover:text-blue-600">
                                                    {{ $act->title }}
                                                </a>
                                            </div>
                                            <p class="text-[11px] text-slate-500 line-clamp-1">{{ $act->description ?? 'Implementasi Poka-Yoke dan standardisasi kerja.' }}</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-black text-slate-900">{{ $act->progress_pct }}%</span>
                                            <p class="text-[10px] text-slate-400">{{ ucfirst($act->status) }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="py-4 text-center text-xs text-slate-400">Belum ada proyek Kaizen yang terdaftar.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Workshop Evaluation History -->
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">
                                Evaluasi Workshop OMDD
                            </h3>

                            @if($latestEvaluation)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800">Evaluasi Terakhir: {{ $latestEvaluation->workshop->title ?? 'Workshop TPS-G' }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full {{ $latestEvaluation->final_decision === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} font-bold text-[10px]">
                                            {{ strtoupper($latestEvaluation->final_decision) }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 leading-relaxed">{{ $latestEvaluation->notes }}</p>
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                                        <span>Penilai: <strong>{{ $latestEvaluation->evaluated_by }}</strong></span>
                                        <span>{{ $latestEvaluation->evaluated_at ? $latestEvaluation->evaluated_at->diffForHumans() : '-' }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="py-4 text-center text-xs text-slate-400">Belum ada riwayat penilaian workshop.</p>
                            @endif
                        </div>

                    </div>

                </div>

                <!-- Spider / Radar Chart: strength per workshop batch -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Peta Kekuatan per Batch Workshop (Spider Chart)
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Perbandingan skor 7 kriteria OMDD pada setiap batch yang pernah diikuti.
                            </p>
                        </div>
                        <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">
                            Skala 1–5
                        </span>
                    </div>

                    @include('partials.radar-chart', ['radar' => $radarChart, 'chartId' => 'employeeRadarChart'])
                </div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

</body>
</html>

