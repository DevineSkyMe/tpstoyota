@extends('layouts.app')

@section('title', $workshop->title . ' — TPS Portal')

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

    <div class="space-y-5" x-data>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('workshops.index') }}" class="text-slate-500 hover:text-slate-700">Workshop</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Batch {{ $workshop->batch }}</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $workshop->title }}</h1>
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $statusStyles[$workshop->status] ?? '' }}">{{ ucfirst($workshop->status) }}</span>
                    <span class="text-[11px] text-slate-500">{{ $workshop->start_date?->format('d M Y') }} – {{ $workshop->end_date?->format('d M Y') }}</span>
                    <span class="text-[11px] text-slate-400">&bull; {{ $workshop->location ?? 'Lokasi belum ditentukan' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start">
                @if ($canManage)
                    <a href="{{ route('workshops.edit', $workshop) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition">Edit</a>
                    <form method="POST" action="{{ route('workshops.destroy', $workshop) }}" onsubmit="return confirm('Hapus workshop ini beserta data pesertanya?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold shadow-sm transition">Hapus</button>
                    </form>
                @endif

                @if ($user->isKaryawan() && $myParticipant)
                    <span class="px-3.5 py-2 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-bold">
                        Terdaftar sebagai peserta
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Participant list -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Peserta Workshop</h2>
                        <p class="text-[11px] text-slate-500">{{ $workshop->participants->count() }} peserta terdaftar</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($workshop->participants as $participant)
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                @include('partials.avatar', [
                                    'photo' => $participant->employee?->photo,
                                    'name' => $participant->employee->name ?? '?',
                                    'size' => 'w-9 h-9 text-[11px]',
                                    'rounded' => 'rounded-xl',
                                    'bgClass' => 'bg-slate-900',
                                ])
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $participant->employee->name ?? 'Karyawan dihapus' }}</p>
                                    <p class="text-[11px] text-slate-500 font-mono">{{ $participant->employee->username ?? '-' }} &bull; {{ $participant->employee->current_tps_level ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $participant->confirmation_status === 'confirmed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($participant->confirmation_status) }}
                                </span>

                                @if ($participant->evaluation)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $participant->evaluation->isPassed() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $participant->evaluation->composite_score }}% {{ $participant->evaluation->result_label }}
                                    </span>
                                @endif

                                @if ($canEvaluate && $participant->confirmation_status === 'confirmed')
                                    <a href="{{ route('evaluations.show', $participant) }}" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-[11px] font-bold transition">Nilai</a>
                                @endif

                                @if ($canManage)
                                    @if ($participant->confirmation_status !== 'confirmed')
                                        <form method="POST" action="{{ route('workshops.participants.confirm', [$workshop, $participant]) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-[11px] font-bold transition">Konfirmasi</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('workshops.participants.destroy', [$workshop, $participant]) }}" onsubmit="return confirm('Keluarkan peserta ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 text-[11px] font-bold transition">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-xs text-slate-400">Belum ada peserta terdaftar.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar actions -->
            <div class="space-y-5">
                @if ($canManage)
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3">
                        <h2 class="text-sm font-bold text-slate-900">Tambah Peserta</h2>
                        <p class="text-[11px] text-slate-500">Tambahkan karyawan langsung ke workshop ini.</p>

                        @if ($eligibleEmployees->isEmpty())
                            <p class="text-xs text-slate-400">Semua karyawan aktif sudah terdaftar.</p>
                        @else
                            <form method="POST" action="{{ route('workshops.participants.store', $workshop) }}" class="space-y-2">
                                @csrf
                                <select name="employee_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs">
                                    <option value="">Pilih karyawan</option>
                                    @foreach ($eligibleEmployees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->username }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold transition">Tambahkan</button>
                            </form>
                        @endif
                    </div>
                @endif

                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3 text-xs">
                    <h2 class="text-sm font-bold text-slate-900">Ringkasan</h2>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Total peserta</span><strong class="text-slate-900">{{ $workshop->participants->count() }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Sudah dinilai</span><strong class="text-slate-900">{{ $workshop->evaluations->count() }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Lulus</span><strong class="text-emerald-600">{{ $workshop->evaluations->filter->isPassed()->count() }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Kuota</span><strong class="text-slate-900">{{ $workshop->quota ?? 'Tidak dibatasi' }}</strong>
                    </div>

                    @if ($canManage)
                        <a href="{{ route('reports.workshop', $workshop) }}" class="mt-2 w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold transition">
                            Lihat Laporan Hasil
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @include('workshops.partials.evidence', ['workshop' => $workshop, 'myParticipant' => $myParticipant, 'totalWeeks' => $totalWeeks, 'canManage' => $canManage, 'canEvaluate' => $canEvaluate])
    </div>
@endsection
