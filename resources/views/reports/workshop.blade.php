@extends('layouts.app')

@section('title', 'Laporan Hasil ' . $workshop->title . ' — TPS Portal')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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

            <div class="flex items-center gap-2 self-start">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition">
                    Cetak / PDF
                </button>
            </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Peserta</p>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $summary['participants'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Dinilai</p>
                <p class="text-xl font-black text-blue-600 mt-1">{{ $summary['evaluated'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Lulus</p>
                <p class="text-xl font-black text-emerald-600 mt-1">{{ $summary['passed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tidak Lulus</p>
                <p class="text-xl font-black text-rose-600 mt-1">{{ $summary['failed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Rata-rata</p>
                <p class="text-xl font-black text-purple-600 mt-1">{{ $summary['average'] }}%</p>
            </div>
        </div>

        <!-- Distribution -->
        @if ($canSend)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3">
                <h2 class="text-sm font-bold text-slate-900">Distribusi Laporan</h2>
                <p class="text-[11px] text-slate-500">Kirim laporan hasil ini ke HRD dan/atau Manager (email + inbox aplikasi).</p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <form method="POST" action="{{ route('reports.workshop.generate', $workshop) }}">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                            {{ $report ? 'Refresh Laporan' : 'Generate Laporan' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('reports.workshop.send', $workshop) }}" class="flex items-center gap-2">
                        @csrf
                        <select name="target" class="px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium">
                            <option value="both">HRD &amp; Manager</option>
                            <option value="hrd">HRD saja</option>
                            <option value="manager">Manager saja</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                            Kirim Laporan
                        </button>
                    </form>
                </div>

                @if ($report)
                    <p class="text-[11px] text-slate-400">
                        Status: <strong class="text-slate-600">{{ ucfirst($report->status) }}</strong>
                        @if ($report->sent_to_hrd_at) &bull; HRD: {{ $report->sent_to_hrd_at->format('d M Y H:i') }} @endif
                        @if ($report->sent_to_director_at) &bull; Manager: {{ $report->sent_to_director_at->format('d M Y H:i') }} @endif
                    </p>
                @endif
            </div>
        @endif

        <!-- Participants table -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <th class="py-3 px-4">Peserta</th>
                            <th class="py-3 px-4">NIK</th>
                            <th class="py-3 px-4">Level</th>
                            <th class="py-3 px-4 text-right">Composite</th>
                            <th class="py-3 px-4 text-center">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($workshop->participants as $participant)
                            @php($evaluation = $participant->evaluation)
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="py-3 px-4">
                                    <p class="font-bold text-slate-800">{{ $participant->employee->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $participant->employee->department->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">{{ $participant->employee->username ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $participant->employee->current_tps_level ?? '-' }}</td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">{{ $evaluation ? $evaluation->composite_score . '%' : '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if ($evaluation)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $evaluation->isPassed() ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $evaluation->result_label }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">Belum dinilai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-slate-400">Belum ada peserta pada workshop ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
