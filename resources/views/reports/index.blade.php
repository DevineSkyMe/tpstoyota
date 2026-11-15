@extends('layouts.app')

@section('title', 'Pusat Laporan — TPS Portal')

@section('content')
    @php($user = auth()->user())

    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Laporan</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Pusat Laporan</h1>
                <p class="text-xs text-slate-500">
                    @if ($user->isManager() || $user->isHrd())
                        Laporan activity dan hasil workshop yang dikirim Admin ke inbox Anda.
                    @else
                        Generate laporan activity otomatis dan laporan hasil workshop, lalu kirim ke HRD &amp; Manager.
                    @endif
                </p>
            </div>

            <a href="{{ route('reports.activity') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition self-start">
                Laporan Activity
            </a>
        </div>

        <!-- Activity summary -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Activity</p>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $activityStats['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">In Progress</p>
                <p class="text-xl font-black text-blue-600 mt-1">{{ $activityStats['in_progress'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Completed</p>
                <p class="text-xl font-black text-emerald-600 mt-1">{{ $activityStats['completed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Laporan Terkirim</p>
                <p class="text-xl font-black text-purple-600 mt-1">{{ $reportsSent }}</p>
            </div>
        </div>

        <!-- Workshop reports -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Laporan Hasil Workshop</h2>
                <p class="text-[11px] text-slate-500">Rekap nilai peserta, keputusan kelulusan, dan distribusi ke HRD / Manager.</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($workshops as $workshop)
                    @php($report = $workshop->scoreReports->sortByDesc('id')->first())
                    <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $workshop->title }}</p>
                            <p class="text-[11px] text-slate-500">
                                Batch {{ $workshop->batch }} &bull; {{ $workshop->year }} &bull;
                                {{ $workshop->participants_count }} peserta &bull; {{ $workshop->evaluations_count }} dinilai
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($report)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $report->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">Belum digenerate</span>
                            @endif

                            <a href="{{ route('reports.workshop', $workshop) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold text-xs transition">
                                Lihat Laporan
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-12 text-center text-xs text-slate-400">Belum ada workshop untuk dilaporkan.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
