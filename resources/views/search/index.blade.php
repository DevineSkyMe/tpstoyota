@extends('layouts.app')

@section('title', 'Hasil Pencarian — TPS Portal')

@section('content')
    <div class="space-y-5">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">Pencarian</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Hasil Pencarian</h1>
            <p class="text-xs text-slate-500">
                @if ($term !== '')
                    Menampilkan hasil untuk <strong class="text-slate-700">"{{ $term }}"</strong>.
                @else
                    Masukkan kata kunci pada kolom pencarian di atas.
                @endif
            </p>
        </div>

        <form method="GET" action="{{ route('search') }}" class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs">
            <input type="search" name="q" value="{{ $term }}" placeholder="Cari karyawan, NIK, activity, atau workshop..."
                   class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <button type="submit" class="px-4 py-2 bg-[#1d63ed] text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">Cari</button>
        </form>

        @if ($term !== '')
            @if (auth()->user()?->isHrd())
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm px-5 py-10 text-center">
                    <p class="text-xs text-slate-500">
                        Pencarian global tidak tersedia untuk role HRD. Gunakan menu <strong class="text-slate-700">Next Candidate</strong> untuk rekomendasi dan <strong class="text-slate-700">Laporan</strong> untuk melihat hasil dari Admin.
                    </p>
                </div>
            @else
                <!-- Employees -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Karyawan ({{ $employees->count() }})</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        <a href="{{ route('employees.show', $employee->id) }}" class="block px-5 py-3 hover:bg-blue-50/20 transition">
                            <p class="text-xs font-bold text-slate-900">{{ $employee->name }}</p>
                            <p class="text-[11px] text-slate-500 font-mono">
                                {{ $employee->username }} &bull; {{ $employee->position }}
                                &bull; {{ $employee->department->name ?? '-' }}
                            </p>
                        </a>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Tidak ada karyawan yang cocok.</p>
                    @endforelse
                </div>
            </div>

            <!-- Activities -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Activity ({{ $activities->count() }})</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($activities as $activity)
                        <a href="{{ route('activity.show', $activity->id) }}" class="block px-5 py-3 hover:bg-blue-50/20 transition">
                            <p class="text-xs font-bold text-slate-900">{{ $activity->title }}</p>
                            <p class="text-[11px] text-slate-500">
                                KZ-{{ sprintf('%03d', $activity->id) }} &bull; {{ $activity->category }} &bull; {{ $activity->employee->name ?? '-' }}
                            </p>
                        </a>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Tidak ada activity yang cocok.</p>
                    @endforelse
                </div>
            </div>

            <!-- Workshops -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Workshop ({{ $workshops->count() }})</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($workshops as $workshop)
                        <a href="{{ route('workshops.show', $workshop) }}" class="block px-5 py-3 hover:bg-blue-50/20 transition">
                            <p class="text-xs font-bold text-slate-900">{{ $workshop->title }}</p>
                            <p class="text-[11px] text-slate-500">Batch {{ $workshop->batch }} &bull; {{ $workshop->status }} &bull; {{ $workshop->location ?? '-' }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Tidak ada workshop yang cocok.</p>
                    @endforelse
                </div>
            </div>
            @endif
        @endif
    </div>
@endsection
