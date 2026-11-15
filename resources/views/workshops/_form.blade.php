@php
    $model = $workshop ?? null;
    $statuses = ['draft' => 'Draft', 'scheduled' => 'Scheduled', 'ongoing' => 'Ongoing', 'completed' => 'Completed'];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
    <div class="space-y-1.5">
        <label class="font-bold text-slate-700">Tahun <span class="text-rose-500">*</span></label>
        <input type="number" name="year" min="2000" max="2100" required
               value="{{ old('year', $model->year ?? date('Y')) }}"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
    </div>

    <div class="space-y-1.5">
        <label class="font-bold text-slate-700">Batch <span class="text-rose-500">*</span></label>
        <input type="number" name="batch" min="1" required
               value="{{ old('batch', $model->batch ?? 1) }}"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
    </div>
</div>

<div class="space-y-1.5 text-xs">
    <label class="font-bold text-slate-700">Judul Workshop <span class="text-rose-500">*</span></label>
    <input type="text" name="title" maxlength="150" required
           value="{{ old('title', $model->title ?? '') }}"
           placeholder="Contoh: TPS-G Batch 1 — OMDD Key Person Development"
           class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
    <div class="space-y-1.5">
        <label class="font-bold text-slate-700">Lokasi</label>
        <input type="text" name="location" maxlength="150"
               value="{{ old('location', $model->location ?? '') }}"
               placeholder="Karawang Plant 1"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
    </div>

    <div class="space-y-1.5">
        <label class="font-bold text-slate-700">Kuota Peserta</label>
        <input type="number" name="quota" min="1" max="500"
               value="{{ old('quota', $model->quota ?? '') }}"
               placeholder="Kosongkan bila tidak dibatasi"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
    </div>
</div>

<div class="space-y-1.5 text-xs">
    <label class="font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
    <select name="status" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
        @foreach ($statuses as $value => $label)
            <option value="{{ $value }}" {{ old('status', $model->status ?? 'scheduled') === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
    <div class="space-y-1.5">
        <label class="font-bold text-slate-700">Tanggal Mulai <span class="text-rose-500">*</span></label>
        <input type="date" name="start_date" required
               value="{{ old('start_date', $model?->start_date?->format('Y-m-d') ?? now()->addWeek()->toDateString()) }}"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
    </div>

    <div class="space-y-1.5">
        <label class="font-bold text-slate-700">Tanggal Selesai <span class="text-rose-500">*</span></label>
        <input type="date" name="end_date" required
               value="{{ old('end_date', $model?->end_date?->format('Y-m-d') ?? now()->addWeek()->addDays(5)->toDateString()) }}"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl">
    </div>
</div>

<div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
    <a href="{{ route('workshops.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">Batal</a>
    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
        Simpan Workshop
    </button>
</div>
