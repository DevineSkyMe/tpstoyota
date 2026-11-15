@extends('layouts.app')

@section('title', 'Candidate Pipeline — TPS Portal')

@section('content')
    @php
        $user = auth()->user();
        $canRecommend = $canRecommend ?? $user->hasRole('hrd', 'admin');
        $canApprove = $user->isAdmin();
        $canAssess = $user->isOmdd();
    @endphp

    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Next Candidate Pipeline</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">Candidate Promotion Pipeline</h1>
                <p class="text-xs text-slate-500">
                    @if ($user->isHrd())
                        Rekomendasikan karyawan untuk mengikuti workshop tingkat selanjutnya, lalu kirim ke Admin.
                    @elseif ($user->isOmdd())
                        Beri nilai peserta workshop dan tentukan lulus / tidak lulus.
                    @elseif ($user->isAdmin())
                        Tinjau rekomendasi HRD dan tambahkan kandidat sebagai peserta workshop.
                    @else
                        Pantau pipeline kandidat dan hasil workshop.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-white border border-slate-200/90 text-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="font-bold text-slate-800">{{ $underEvaluation }} menunggu evaluasi</span>
                </div>

                @if ($canRecommend)
                    <a href="{{ route('candidates.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/>
                        </svg>
                        Buat Rekomendasi
                    </a>
                @endif
            </div>
        </div>

        <!-- KPI cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Kandidat</p>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $totalCandidates }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Pipeline ke KP4</p>
                <p class="text-xl font-black text-blue-600 mt-1">{{ $kp4Candidates }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Pipeline ke KP3</p>
                <p class="text-xl font-black text-emerald-600 mt-1">{{ $kp3Candidates }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Menunggu Evaluasi</p>
                <p class="text-xl font-black text-amber-600 mt-1">{{ $underEvaluation }}</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('candidates.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kandidat atau NIK..."
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <select name="target_level" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                <option value="">Semua Target Level</option>
                @foreach (['KP4', 'KP3', 'KP2', 'KP1', 'Advance'] as $level)
                    <option value="{{ $level }}" {{ request('target_level') === $level ? 'selected' : '' }}>Target: {{ $level }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                <option value="">Semua Status</option>
                <option value="recommended" {{ request('status') === 'recommended' ? 'selected' : '' }}>Recommended</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <select name="plant_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                <option value="">Semua Plant</option>
                @foreach ($plants as $plant)
                    <option value="{{ $plant->id }}" {{ (string) request('plant_id') === (string) $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-[#1d63ed] text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">Filter</button>
        </form>

        <!-- Candidate cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($candidates as $cand)
                @php($emp = $cand->employee)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between gap-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border
                                {{ $cand->tlc_status === 'approved' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($cand->tlc_status === 'rejected' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-amber-50 border-amber-200 text-amber-700') }}">
                                {{ ucfirst($cand->tlc_status) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-bold">
                                {{ $emp->current_tps_level }} &rarr; {{ $cand->target_tps_level }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            @include('partials.avatar', [
                                'photo' => $emp->photo,
                                'name' => $emp->name,
                                'size' => 'w-12 h-12 text-sm',
                                'rounded' => 'rounded-xl',
                                'bgClass' => 'bg-slate-900',
                            ])
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $emp->name }}</h3>
                                <p class="text-[11px] text-slate-500 font-mono">NIK: {{ $emp->username }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ $emp->department->name ?? '-' }} &bull; {{ $emp->plant->name ?? '-' }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 line-clamp-2">{{ $cand->tlc_reason ?? 'Belum ada catatan rekomendasi.' }}</p>

                        @if ($cand->hrdReview)
                            <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Review HRD</p>
                                <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2">{{ $cand->hrdReview->hrd_note ?? '-' }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-slate-400">{{ $cand->recommendedBy->display_name ?? 'HRD' }}</span>

                        <div class="flex items-center gap-2">
                            @if ($canApprove && $cand->tlc_status === 'recommended')
                                <form method="POST" action="{{ route('candidates.approve', $cand->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold text-xs transition">
                                        Setujui
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('candidates.show', $cand->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold text-xs transition">
                                <span>{{ $canAssess ? 'Beri Nilai' : 'Detail' }}</span>
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 text-xs">
                    Belum ada kandidat yang cocok dengan filter. @if ($canRecommend) Klik "Buat Rekomendasi" untuk menambahkan. @endif
                </div>
            @endforelse
        </div>

        <div>{{ $candidates->links() }}</div>
    </div>
@endsection
