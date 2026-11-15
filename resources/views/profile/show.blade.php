@extends('layouts.app')

@section('title', 'Profil Saya — TPS Portal')

@section('content')
    <div class="space-y-5">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">Profil Saya</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Profil Saya</h1>
            <p class="text-xs text-slate-500">Akun login, data karyawan, riwayat activity, dan hasil workshop.</p>
        </div>

        <!-- Account + stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    @include('partials.avatar', [
                        'photo' => $employee?->photo,
                        'name' => $user->display_name,
                        'size' => 'w-14 h-14 text-lg',
                        'rounded' => 'rounded-2xl',
                    ])
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $user->display_name }}</p>
                        <p class="text-[11px] text-slate-400">{{ $user->role_label }}</p>
                    </div>
                </div>

                <div class="text-xs space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex justify-between"><span class="text-slate-400">Username</span><strong class="text-slate-700">{{ $user->username }}</strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Email</span><strong class="text-slate-700">{{ $user->email ?? '-' }}</strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Pesan belum dibaca</span><strong class="text-slate-700">{{ $unreadMessages }}</strong></div>
                </div>

                @if ($employee)
                    <div class="text-xs space-y-2 pt-2 border-t border-slate-100">
                        <div class="flex justify-between"><span class="text-slate-400">NIK</span><strong class="text-slate-700 font-mono">{{ $employee->username }}</strong></div>
                        <div class="flex justify-between"><span class="text-slate-400">Level TPS</span><strong class="text-blue-600">{{ $employee->current_tps_level ?? '-' }}</strong></div>
                        <div class="flex justify-between"><span class="text-slate-400">Posisi</span><strong class="text-slate-700">{{ $employee->position ?? '-' }}</strong></div>
                        <div class="flex justify-between"><span class="text-slate-400">Departemen</span><strong class="text-slate-700">{{ $employee->department->name ?? '-' }}</strong></div>
                        <div class="flex justify-between"><span class="text-slate-400">Plant</span><strong class="text-slate-700">{{ $employee->plant->name ?? '-' }}</strong></div>
                    </div>
                @else
                    <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100">Akun ini belum terhubung ke data karyawan.</p>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Activity</p>
                        <p class="text-xl font-black text-slate-900 mt-1">{{ $stats['activities'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Completed</p>
                        <p class="text-xl font-black text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Workshop</p>
                        <p class="text-xl font-black text-blue-600 mt-1">{{ $stats['workshops'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Lulus</p>
                        <p class="text-xl font-black text-purple-600 mt-1">{{ $stats['passed'] }}</p>
                    </div>
                </div>

                <!-- Latest evaluation -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-slate-900">Hasil Workshop Terakhir</h2>
                    @if ($latestEvaluation)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $latestEvaluation->workshop->title ?? 'Workshop TPS-G' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $latestEvaluation->evaluated_at?->format('d M Y') }} &bull; {{ $latestEvaluation->evaluated_by }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black {{ $latestEvaluation->isPassed() ? 'text-emerald-600' : 'text-rose-600' }}">{{ $latestEvaluation->composite_score }}%</p>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $latestEvaluation->isPassed() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $latestEvaluation->result_label }}
                                </span>
                            </div>
                        </div>
                        @if ($latestEvaluation->notes)
                            <p class="text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">{{ $latestEvaluation->notes }}</p>
                        @endif
                    @else
                        <p class="text-xs text-slate-400">Belum ada riwayat penilaian workshop.</p>
                    @endif
                </div>

                <!-- Activity history -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Activity</h2>
                    <div class="divide-y divide-slate-100">
                        @forelse ($activities as $activity)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    @if (auth()->user()?->isHrd())
                                        <span class="text-xs font-bold text-slate-800 truncate block">{{ $activity->title }}</span>
                                    @else
                                        <a href="{{ route('activity.show', $activity->id) }}" class="text-xs font-bold text-slate-800 hover:text-blue-600 truncate block">{{ $activity->title }}</a>
                                    @endif
                                    <p class="text-[10px] text-slate-400">{{ ucfirst(str_replace('_', ' ', $activity->status)) }} &bull; {{ $activity->category }}</p>
                                </div>
                                <span class="text-xs font-black text-slate-900 shrink-0">{{ $activity->progress_pct }}%</span>
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-slate-400">Belum ada activity terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Spider / Radar Chart: strength per workshop batch -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Peta Kekuatan per Batch Workshop</h2>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Spider chart 7 kriteria OMDD untuk melihat ke arah mana Anda lebih unggul di setiap batch.
                    </p>
                </div>
                <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">Skala 1–5</span>
            </div>

            @include('partials.radar-chart', ['radar' => $radarChart, 'chartId' => 'profileRadarChart'])
        </div>

        <!-- Change password -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm max-w-xl space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Ubah Password</h2>
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700">Password Saat Ini</label>
                    <input type="password" name="current_password" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">Password Baru</label>
                        <input type="password" name="password" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
                    </div>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white font-bold shadow-md shadow-blue-500/20 transition">Perbarui Password</button>
            </form>
        </div>
    </div>
@endsection
