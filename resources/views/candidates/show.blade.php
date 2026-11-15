@extends('layouts.app')

@section('title', $employee->name . ' — OMDD Assessment')

@section('content')
    @php
        $user = auth()->user();
        $canAssess = $canAssess ?? $user->isOmdd();
        $criteria = [
            'ws_1' => ['Problem Identification & Grasp of Situation', 'Kemampuan mengamati Genba dan mendefinisikan deviasi standar kerja.'],
            'ws_2' => ['Root Cause Analysis (5-Why & Fishbone)', 'Kedalaman analisis sampai akar masalah teknis dan manajerial.'],
            'ws_3' => ['Countermeasure Execution & Yokoten', 'Kecepatan implementasi Poka-Yoke dan potensi replikasi ke lini lain.'],
            'ws_4' => ['Presentation & TPS Coaching Skill', 'Kejelasan A3 report dan kemampuan transfer mindset lean.'],
            'ws_5' => ['Genba Observation & Safety Mindset', 'Kepatuhan standar keselamatan dan ketajaman observasi lapangan.'],
            'ws_6' => ['Standardization & Sustainment', 'Konsistensi standard work dan sustainment hasil kaizen.'],
            'ws_7' => ['Team Leadership & Coaching Impact', 'Dampak kepemimpinan dan pembinaan anggota tim di area kerja.'],
        ];
        $existing = $evaluation ?? null;
        $currentScores = [];
        foreach ($criteria as $key => $_) {
            $currentScores[$key] = old($key, $existing?->{$key} ?? 3);
        }
    @endphp

    <div class="max-w-5xl mx-auto space-y-5 pb-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('candidates.index') }}" class="text-slate-500 hover:text-slate-700">Candidate Pipeline</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">{{ $employee->name }}</span>
                </nav>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $employee->name }}</h1>
                <p class="text-xs text-slate-500">NIK {{ $employee->username }} &bull; {{ $employee->position ?? '-' }}</p>
            </div>

            <a href="{{ route('candidates.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition self-start">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali
            </a>
        </div>

        <!-- Profile summary -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <p class="text-slate-400">Level Saat Ini</p>
                <p class="font-extrabold text-blue-600 text-sm mt-0.5">{{ $employee->current_tps_level ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400">Target Level</p>
                <p class="font-extrabold text-emerald-600 text-sm mt-0.5">{{ $candidate->target_tps_level ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400">Plant / Departemen</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->plant->name ?? '-' }} &bull; {{ $employee->department->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400">Status Rekomendasi</p>
                <p class="font-bold text-slate-800 mt-0.5 capitalize">{{ $candidate->tlc_status }}</p>
            </div>
        </div>

        @if ($candidate->tlc_reason)
            <div class="rounded-2xl bg-blue-50/70 border border-blue-100 p-4 text-xs text-slate-700">
                <p class="font-bold text-blue-700 mb-1">Catatan Rekomendasi</p>
                {{ $candidate->tlc_reason }}
            </div>
        @endif

        <!-- Existing evaluation summary -->
        @if ($existing)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Evaluasi Terakhir</p>
                        <p class="text-sm font-bold text-slate-900 mt-0.5">{{ $existing->workshop?->title ?? 'Workshop' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black {{ $existing->isPassed() ? 'text-emerald-600' : 'text-rose-600' }}">{{ $existing->composite_score }}%</p>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $existing->isPassed() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $existing->result_label }}
                        </span>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    @foreach ($criteria as $key => $item)
                        <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2">
                            <p class="text-[10px] text-slate-400 truncate">{{ $item[0] }}</p>
                            <p class="font-bold text-slate-800">{{ $existing->{$key} }}/5</p>
                        </div>
                    @endforeach
                </div>
                @if ($existing->notes)
                    <p class="mt-3 text-xs text-slate-600 leading-relaxed">{{ $existing->notes }}</p>
                @endif
                <p class="mt-2 text-[11px] text-slate-400">Dinilai oleh {{ $existing->evaluated_by ?? '-' }} &bull; {{ $existing->evaluated_at?->format('d M Y H:i') }}</p>
            </div>
        @endif

        <!-- Assessment form (OMDD only) -->
        @if ($canAssess)
            <form method="POST" action="{{ route('candidates.assessment', $candidate->id) }}" class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-5">
                @csrf

                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <h2 class="text-sm font-bold text-slate-900">Rubrik Penilaian OMDD (skala 1–5)</h2>
                </div>

                <div class="space-y-4">
                    @foreach ($criteria as $key => $item)
                        <div class="rounded-xl border border-slate-200/90 p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $loop->iteration }}. {{ $item[0] }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $item[1] }}</p>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">1 = kurang &bull; 5 = unggul</span>
                            </div>

                            <div class="mt-3 grid grid-cols-5 gap-2">
                                @for ($score = 1; $score <= 5; $score++)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="{{ $key }}" value="{{ $score }}" class="peer sr-only"
                                               {{ (int) $currentScores[$key] === $score ? 'checked' : '' }} required>
                                        <span class="flex items-center justify-center py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-600
                                                     peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition">
                                            {{ $score }}
                                        </span>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-1.5 text-xs">
                    <label class="font-bold text-slate-700">Catatan Observasi Genba</label>
                    <textarea name="notes" rows="3" class="w-full p-3.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs leading-relaxed"
                              placeholder="Tuliskan kekuatan kandidat, area perbaikan, dan rekomendasi tindak lanjut...">{{ old('notes', $existing->notes ?? '') }}</textarea>
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-700">Keputusan Kelulusan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="final_decision" value="pass" class="peer sr-only" {{ old('final_decision', $existing?->final_decision ?? 'pass') === 'pass' ? 'checked' : '' }}>
                            <span class="flex flex-col rounded-xl border border-slate-200 bg-white p-4 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition">
                                <span class="text-xs font-bold text-emerald-700">LULUS (PASS)</span>
                                <span class="text-[11px] text-slate-500 mt-1">Kandidat dipromosikan ke level berikutnya.</span>
                            </span>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="final_decision" value="fail" class="peer sr-only" {{ old('final_decision', $existing?->final_decision) === 'fail' ? 'checked' : '' }}>
                            <span class="flex flex-col rounded-xl border border-slate-200 bg-white p-4 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                <span class="text-xs font-bold text-rose-700">TIDAK LULUS (FAIL)</span>
                                <span class="text-[11px] text-slate-500 mt-1">Kandidat perlu mengulang workshop.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                        Simpan Penilaian OMDD
                    </button>
                </div>
            </form>
        @else
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-500">
                Hanya akun OMDD yang dapat mengisi atau mengubah penilaian peserta.
            </div>
        @endif
    </div>
@endsection
