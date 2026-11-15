@extends('layouts.app')

@section('title', 'Rekomendasi Workshop — TPS Portal')

@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('candidates.index') }}" class="text-slate-500 hover:text-slate-700">Candidate Pipeline</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">Rekomendasi HRD</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Rekomendasi Peserta Workshop Berikutnya</h1>
            <p class="text-xs text-slate-500">Pilih karyawan yang layak mengikuti workshop tingkat selanjutnya. Rekomendasi akan dikirim ke Admin untuk disetujui.</p>
        </div>

        @if ($employees->isEmpty())
            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 text-xs text-amber-800">
                Tidak ada karyawan yang dapat direkomendasikan saat ini. Semua karyawan aktif sudah berada di pipeline atau sudah mencapai level tertinggi.
            </div>
        @else
            <form method="POST" action="{{ route('candidates.store') }}" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                @csrf

                <div class="space-y-1.5 text-xs">
                    <label class="font-bold text-slate-700">Karyawan <span class="text-rose-500">*</span></label>
                    <select name="employee_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs">
                        <option value="">Pilih karyawan</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->username }}) — {{ $employee->current_tps_level }} &rarr; {{ $employee->getNextTpsLevel() ?? 'Level tertinggi' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5 text-xs">
                    <label class="font-bold text-slate-700">Target Level Workshop</label>
                    <select name="target_tps_level" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs">
                        <option value="">Otomatis (level berikutnya)</option>
                        @foreach (['KP4', 'KP3', 'KP2', 'KP1', 'Advance'] as $level)
                            <option value="{{ $level }}" {{ old('target_tps_level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5 text-xs">
                    <label class="font-bold text-slate-700">Alasan / Saran HRD <span class="text-rose-500">*</span></label>
                    <textarea name="tlc_reason" rows="4" required placeholder="Contoh: Kandidat konsisten menyelesaikan Jishuken dengan hasil terukur dan siap mengikuti workshop KP4."
                              class="w-full p-3.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs leading-relaxed">{{ old('tlc_reason') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('candidates.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                        Kirim Rekomendasi ke Admin
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection
