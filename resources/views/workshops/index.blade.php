@extends('layouts.app')

@section('title', 'Workshop — TPS Portal')

@section('content')
    @php
        $user = auth()->user();
        $statusStyles = [
            'draft' => 'bg-slate-100 text-slate-600 border-slate-200',
            'scheduled' => 'bg-blue-50 text-blue-700 border-blue-200',
            'ongoing' => 'bg-amber-50 text-amber-700 border-amber-200',
            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ];
    @endphp

    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Workshop</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Workshop TPS-G</h1>
                <p class="text-xs text-slate-500">
                    @if ($user->isKaryawan())
                        Daftar workshop yang Anda ikuti beserta status kelulusan dan bukti kehadiran mingguan.
                    @else
                        Kelola batch workshop, peserta, dan hasil penilaian OMDD.
                    @endif
                </p>
            </div>

            @if ($canManage)
                <a href="{{ route('workshops.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition self-start">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/>
                    </svg>
                    Buat Workshop
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('workshops.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau lokasi workshop..."
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                <option value="">Semua Status</option>
                @foreach (['draft', 'scheduled', 'ongoing', 'completed'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-[#1d63ed] text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">Cari</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($workshops as $workshop)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between gap-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $statusStyles[$workshop->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ ucfirst($workshop->status) }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Batch {{ $workshop->batch }} &bull; {{ $workshop->year }}</span>
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $workshop->title }}</h3>

                        <div class="space-y-1.5 text-[11px] text-slate-500">
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $workshop->start_date?->format('d M Y') }} – {{ $workshop->end_date?->format('d M Y') }}
                            </p>
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $workshop->location ?? 'Lokasi belum ditentukan' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center text-[10px]">
                            <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                                <p class="font-black text-slate-900 text-sm">{{ $workshop->participants_count }}</p>
                                <p class="text-slate-400">Peserta</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                                <p class="font-black text-emerald-600 text-sm">{{ $workshop->evaluations_count }}</p>
                                <p class="text-slate-400">Dinilai</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                                <p class="font-black text-blue-600 text-sm">{{ $workshop->quota ?? '∞' }}</p>
                                <p class="text-slate-400">Kuota</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        @if ($user->isKaryawan())
                            <span class="text-[11px] font-bold text-emerald-600">Terdaftar</span>
                        @else
                            <span class="text-[11px] text-slate-400">{{ $workshop->confirmed_participants_count }} confirmed</span>
                        @endif

                        <a href="{{ route('workshops.show', $workshop) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold text-xs transition">
                            Detail
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 text-xs">Belum ada workshop yang tersedia.</div>
            @endforelse
        </div>

        <div>{{ $workshops->links() }}</div>
    </div>
@endsection
