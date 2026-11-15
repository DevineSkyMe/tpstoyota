@extends('layouts.app')

@section('title', 'Laporan Activity — TPS Portal')

@section('content')
    @php($user = auth()->user())

    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="no-print flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200">Home</a>
                    <span class="text-slate-300 dark:text-slate-600">/</span>
                    <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Laporan</a>
                    <span class="text-slate-300 dark:text-slate-600">/</span>
                    <span class="text-slate-900 dark:text-white font-semibold">Activity</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Laporan Activity Otomatis</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Rekap seluruh proyek Kaizen berdasarkan periode, departemen, dan status.</p>
            </div>

            <div class="no-print flex items-center gap-2 self-start flex-wrap">
                <a href="{{ route('reports.activity.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                    </svg>
                    Export CSV
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
                    </svg>
                    Cetak / PDF
                </button>
                @if ($user->isAdmin())
                    <button type="button" onclick="document.getElementById('send-email-panel').classList.toggle('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        Kirim ke Email
                    </button>
                @endif
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-sm text-emerald-800 dark:text-emerald-200 no-print">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-start gap-3 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-sm text-rose-800 dark:text-rose-200 no-print">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.07 19h13.86a2 2 0 0 0 1.73-3L13.73 4a2 2 0 0 0-3.46 0L3.34 16a2 2 0 0 0 1.73 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Email send panel (hidden by default) --}}
        @if ($user->isAdmin())
            <div id="send-email-panel" class="hidden no-print">
                <form method="POST" action="{{ route('reports.activity.send') }}"
                      class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 space-y-4 shadow-sm">
                    @csrf

                    {{-- Pass current filters as hidden inputs --}}
                    <input type="hidden" name="start_date"    value="{{ $filters['start_date'] }}">
                    <input type="hidden" name="end_date"      value="{{ $filters['end_date'] }}">
                    <input type="hidden" name="department_id" value="{{ $filters['department_id'] ?? '' }}">
                    <input type="hidden" name="status"        value="{{ $filters['status'] ?? '' }}">

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Distribusi Laporan via Email</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">PDF laporan akan digenerate otomatis dan dikirim sebagai lampiran.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-600 dark:text-slate-300">Tujuan Pengiriman</label>
                            <select name="target" id="activity-target"
                                    onchange="document.getElementById('activity-custom-email-wrap').classList.toggle('hidden', this.value !== 'custom')"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="both">HRD + Manager</option>
                                <option value="hrd">HRD saja</option>
                                <option value="manager">Manager saja</option>
                                <option value="custom">Email Tertentu</option>
                            </select>
                        </div>
                        <div id="activity-custom-email-wrap" class="hidden space-y-1.5">
                            <label class="font-bold text-slate-600 dark:text-slate-300">Alamat Email Tujuan</label>
                            <input type="email" name="custom_email" placeholder="contoh@gmail.com"
                                   class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            Periode: <strong class="text-slate-600 dark:text-slate-300">{{ $filters['start_date'] }}</strong> s/d <strong class="text-slate-600 dark:text-slate-300">{{ $filters['end_date'] }}</strong>
                            &bull; <strong class="text-slate-600 dark:text-slate-300">{{ $summary['total'] }}</strong> activity
                        </p>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" x2="11" y1="2" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Kirim Laporan PDF
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">Total</p>
                <p class="text-lg font-black text-slate-900 dark:text-white mt-1">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">Completed</p>
                <p class="text-lg font-black text-emerald-600 mt-1">{{ $summary['completed'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">In Progress</p>
                <p class="text-lg font-black text-blue-600 mt-1">{{ $summary['in_progress'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">Delayed</p>
                <p class="text-lg font-black text-rose-600 mt-1">{{ $summary['delayed'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">Not Started</p>
                <p class="text-lg font-black text-slate-500 mt-1">{{ $summary['not_started'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">Avg Progress</p>
                <p class="text-lg font-black text-purple-600 mt-1">{{ $summary['avg_progress'] }}%</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('reports.activity') }}" class="no-print flex flex-col lg:flex-row items-stretch lg:items-end gap-3 bg-white dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 shadow-xs text-xs">
            <div class="space-y-1">
                <label class="font-bold text-slate-600 dark:text-slate-300">Mulai</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/90 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-1">
                <label class="font-bold text-slate-600 dark:text-slate-300">Selesai</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/90 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-1 flex-1">
                <label class="font-bold text-slate-600 dark:text-slate-300">Departemen</label>
                <select name="department_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/90 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ (string) $filters['department_id'] === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="font-bold text-slate-600 dark:text-slate-300">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/90 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200">
                    <option value="">Semua Status</option>
                    @foreach (['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'delay' => 'Delayed', 'completed' => 'Completed'] as $value => $label)
                        <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-[#1d63ed] text-white font-bold rounded-xl hover:bg-blue-700 transition">Terapkan</button>
        </form>

        <!-- Table -->
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200/90 dark:border-slate-700/50 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-700/50 bg-slate-50/70 dark:bg-slate-900/40 text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Judul</th>
                            <th class="py-3 px-4">Karyawan</th>
                            <th class="py-3 px-4">Departemen</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                        @forelse ($activities as $activity)
                            <tr class="hover:bg-blue-50/20 dark:hover:bg-blue-950/20 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-600">KZ-{{ sprintf('%03d', $activity->id) }}</td>
                                <td class="py-3 px-4">
                                    @if (auth()->user()?->isHrd())
                                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $activity->title }}</span>
                                    @else
                                        <a href="{{ route('activity.show', $activity->id) }}" class="font-bold text-slate-800 dark:text-slate-100 hover:text-blue-600">{{ $activity->title }}</a>
                                    @endif
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $activity->category }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $activity->employee->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $activity->employee->username ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                    {{ $activity->employee->department->name ?? '-' }}
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $activity->employee->plant->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ $activity->planned_start?->format('d M Y') }} – {{ $activity->planned_end?->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $activity->status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' :
                                           ($activity->status === 'delay'    ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' :
                                           ($activity->status === 'in_progress' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900 dark:text-white">{{ $activity->progress_pct }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-slate-400 dark:text-slate-500">Tidak ada activity pada periode / filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
