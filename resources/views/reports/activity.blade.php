@extends('layouts.app')

@section('title', 'Laporan Activity — TPS Portal')

@section('content')
    @php($user = auth()->user())

    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700">Laporan</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Activity</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Laporan Activity Otomatis</h1>
                <p class="text-xs text-slate-500">Rekap seluruh proyek Kaizen berdasarkan periode, departemen, dan status.</p>
            </div>

            <div class="flex items-center gap-2 self-start">
                <a href="{{ route('reports.activity.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition">
                    Export CSV
                </a>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                    Cetak / PDF
                </button>
            </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total</p>
                <p class="text-lg font-black text-slate-900 mt-1">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Completed</p>
                <p class="text-lg font-black text-emerald-600 mt-1">{{ $summary['completed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">In Progress</p>
                <p class="text-lg font-black text-blue-600 mt-1">{{ $summary['in_progress'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Delayed</p>
                <p class="text-lg font-black text-rose-600 mt-1">{{ $summary['delayed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Not Started</p>
                <p class="text-lg font-black text-slate-500 mt-1">{{ $summary['not_started'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Avg Progress</p>
                <p class="text-lg font-black text-purple-600 mt-1">{{ $summary['avg_progress'] }}%</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('reports.activity') }}" class="flex flex-col lg:flex-row items-stretch lg:items-end gap-3 bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs text-xs">
            <div class="space-y-1">
                <label class="font-bold text-slate-600">Mulai</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
            </div>
            <div class="space-y-1">
                <label class="font-bold text-slate-600">Selesai</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
            </div>
            <div class="space-y-1 flex-1">
                <label class="font-bold text-slate-600">Departemen</label>
                <select name="department_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ (string) $filters['department_id'] === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="font-bold text-slate-600">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
                    <option value="">Semua Status</option>
                    @foreach (['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'delay' => 'Delayed', 'completed' => 'Completed'] as $value => $label)
                        <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-[#1d63ed] text-white font-bold rounded-xl hover:bg-blue-700 transition">Terapkan</button>
        </form>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Judul</th>
                            <th class="py-3 px-4">Karyawan</th>
                            <th class="py-3 px-4">Departemen</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($activities as $activity)
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-600">KZ-{{ sprintf('%03d', $activity->id) }}</td>
                                <td class="py-3 px-4">
                                    @if (auth()->user()?->isHrd())
                                        <span class="font-bold text-slate-800">{{ $activity->title }}</span>
                                    @else
                                        <a href="{{ route('activity.show', $activity->id) }}" class="font-bold text-slate-800 hover:text-blue-600">{{ $activity->title }}</a>
                                    @endif
                                    <p class="text-[10px] text-slate-400">{{ $activity->category }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-slate-700">{{ $activity->employee->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">{{ $activity->employee->username ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $activity->employee->department->name ?? '-' }}
                                    <p class="text-[10px] text-slate-400">{{ $activity->employee->plant->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                    {{ $activity->planned_start?->format('d M Y') }} – {{ $activity->planned_end?->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $activity->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($activity->status === 'delay' ? 'bg-rose-50 text-rose-700' : 'bg-blue-50 text-blue-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">{{ $activity->progress_pct }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-slate-400">Tidak ada activity pada periode / filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
