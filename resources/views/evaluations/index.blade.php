@extends('layouts.app')

@section('title', 'Penilaian OMDD — TPS Portal')

@section('content')
    @php($user = auth()->user())

    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Penilaian OMDD</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">OMDD Assessment Workspace</h1>
                <p class="text-xs text-slate-500">Beri nilai 1–5 pada tujuh kriteria, lalu tentukan lulus / tidak lulus setiap peserta workshop.</p>
            </div>

            <a href="{{ route('candidates.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition self-start">
                Candidate Pipeline
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Peserta</p>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Sudah Dinilai</p>
                <p class="text-xl font-black text-blue-600 mt-1">{{ $stats['evaluated'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Lulus</p>
                <p class="text-xl font-black text-emerald-600 mt-1">{{ $stats['passed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tidak Lulus</p>
                <p class="text-xl font-black text-rose-600 mt-1">{{ $stats['failed'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('evaluations.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs">
            <select name="workshop_id" onchange="this.form.submit()" class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                <option value="">Semua Workshop</option>
                @foreach ($workshops as $workshop)
                    <option value="{{ $workshop->id }}" {{ (string) request('workshop_id') === (string) $workshop->id ? 'selected' : '' }}>
                        {{ $workshop->title }} (Batch {{ $workshop->batch }})
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Belum Dinilai</option>
                <option value="pass" {{ request('status') === 'pass' ? 'selected' : '' }}>Lulus</option>
                <option value="fail" {{ request('status') === 'fail' ? 'selected' : '' }}>Tidak Lulus</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-[#1d63ed] text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">Filter</button>
        </form>

        <!-- Participant list -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="divide-y divide-slate-100">
                @forelse ($participants as $participant)
                    @php($evaluation = $participant->evaluation)
                    <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @include('partials.avatar', [
                                'photo' => $participant->employee?->photo,
                                'name' => $participant->employee->name ?? '?',
                                'size' => 'w-10 h-10 text-xs',
                                'rounded' => 'rounded-xl',
                                'bgClass' => 'bg-slate-900',
                            ])
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $participant->employee->name ?? 'Karyawan dihapus' }}</p>
                                <p class="text-[11px] text-slate-500 font-mono">
                                    {{ $participant->employee->username ?? '-' }} &bull; {{ $participant->employee->current_tps_level ?? '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400 truncate">{{ $participant->workshop->title ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($evaluation)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $evaluation->isPassed() ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $evaluation->composite_score }}% &bull; {{ $evaluation->result_label }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold">Belum Dinilai</span>
                            @endif

                            @if ($user->isOmdd())
                                <a href="{{ route('evaluations.show', $participant) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold text-xs transition">
                                    {{ $evaluation ? 'Ubah Nilai' : 'Beri Nilai' }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-12 text-center text-xs text-slate-400">Belum ada peserta workshop terkonfirmasi yang cocok dengan filter.</p>
                @endforelse
            </div>
        </div>

        <div>{{ $participants->links() }}</div>
    </div>
@endsection
