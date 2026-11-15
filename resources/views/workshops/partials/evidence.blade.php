@php
    $user = auth()->user();
    $isParticipant = (bool) $myParticipant;
    $myEvidences = $myParticipant ? $myParticipant->evidences->keyBy('week_no') : collect();
    $approvedCount = $myParticipant ? $myParticipant->evidences->where('status', 'approved')->count() : 0;

    $statusBadge = [
        'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];
@endphp

{{-- Participant: weekly evidence submission --}}
@if ($isParticipant)
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Bukti Kehadiran Mingguan</h2>
                <p class="text-[11px] text-slate-500">
                    Kirim bukti mengikuti kegiatan setiap minggu. Workshop ini berlangsung {{ $totalWeeks }} minggu.
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-[11px] font-bold {{ $approvedCount >= $totalWeeks ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                {{ $approvedCount }}/{{ $totalWeeks }} minggu disetujui
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @for ($week = 1; $week <= $totalWeeks; $week++)
                @php $evidence = $myEvidences->get($week); @endphp
                <div class="rounded-xl border border-slate-200/90 p-4 space-y-3 bg-slate-50/40">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-900">Minggu ke-{{ $week }}</p>
                        @if ($evidence)
                            <span class="px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $statusBadge[$evidence->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ $evidence->status_label }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full border border-slate-200 bg-slate-100 text-slate-500 text-[10px] font-bold">Belum dikirim</span>
                        @endif
                    </div>

                    @if ($evidence)
                        <div class="text-[11px] text-slate-600 space-y-1">
                            @if ($evidence->title)
                                <p class="font-bold text-slate-800">{{ $evidence->title }}</p>
                            @endif
                            @if ($evidence->note)
                                <p class="leading-relaxed">{{ $evidence->note }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                @if ($evidence->file_url)
                                    <a href="{{ $evidence->file_url }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-blue-600 hover:underline">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                        Lihat file bukti
                                    </a>
                                @endif
                                @if ($evidence->link_url)
                                    <a href="{{ $evidence->link_url }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-blue-600 hover:underline">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                        Lihat tautan
                                    </a>
                                @endif
                            </div>
                            @if ($evidence->review_note)
                                <p class="mt-1 rounded-lg bg-white border border-slate-200 px-2.5 py-1.5">
                                    <span class="font-bold text-slate-700">Catatan reviewer:</span> {{ $evidence->review_note }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if (! $evidence || $evidence->isRejected())
                        <form method="POST" action="{{ route('workshops.evidence.store', $workshop) }}" enctype="multipart/form-data" class="space-y-2">
                            @csrf
                            <input type="hidden" name="week_no" value="{{ $week }}">

                            <input type="text" name="title" maxlength="150" placeholder="Judul bukti (opsional)"
                                   class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-[11px]">

                            <textarea name="note" rows="2" maxlength="1000" placeholder="Catatan kegiatan minggu ini (opsional)"
                                      class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-[11px]"></textarea>

                            <input type="url" name="link_url" maxlength="255" placeholder="Tautan bukti (opsional, mis. Google Drive)"
                                   class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-[11px]">

                            <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                   class="w-full px-2 py-1.5 bg-white border border-slate-200/90 rounded-lg text-[11px] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 file:text-[10px] file:font-bold">

                            <button type="submit" class="w-full px-3 py-2 rounded-lg bg-[#1d63ed] hover:bg-blue-700 text-white text-[11px] font-bold transition">
                                {{ $evidence ? 'Kirim Ulang Bukti' : 'Kirim Bukti' }}
                            </button>
                            <p class="text-[10px] text-slate-400">Minimal isi salah satu: file, tautan, atau catatan.</p>
                        </form>
                    @endif
                </div>
            @endfor
        </div>
    </div>
@endif

{{-- Admin / OMDD: review submitted evidence --}}
@if ($canManage || $canEvaluate)
    @php
        $reviewable = $workshop->participants
            ->flatMap(fn ($participant) => $participant->evidences->map(fn ($evidence) => ['participant' => $participant, 'evidence' => $evidence]))
            ->sortByDesc(fn ($row) => $row['evidence']->submitted_at);
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Review Bukti Kehadiran Peserta</h2>
                <p class="text-[11px] text-slate-500">Verifikasi bukti mingguan yang dikirim peserta workshop ini.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                {{ $reviewable->count() }} bukti terkirim
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($reviewable as $row)
                @php $evidence = $row['evidence']; @endphp
                <div class="py-3 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <div class="min-w-0 space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-xs font-bold text-slate-900">{{ $row['participant']->employee->name ?? 'Karyawan dihapus' }}</p>
                            <span class="px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $statusBadge[$evidence->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ $evidence->status_label }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Minggu {{ $evidence->week_no }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            {{ $evidence->title ?: 'Bukti kehadiran' }}
                            @if ($evidence->submitted_at) &bull; {{ $evidence->submitted_at->format('d M Y H:i') }} @endif
                        </p>
                        <div class="flex flex-wrap items-center gap-3 text-[11px]">
                            @if ($evidence->file_url)
                                <a href="{{ $evidence->file_url }}" target="_blank" class="font-bold text-blue-600 hover:underline">File bukti</a>
                            @endif
                            @if ($evidence->link_url)
                                <a href="{{ $evidence->link_url }}" target="_blank" class="font-bold text-blue-600 hover:underline">Tautan</a>
                            @endif
                            @if ($evidence->note)
                                <span class="text-slate-500">{{ \Illuminate\Support\Str::limit($evidence->note, 90) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if ($evidence->status !== 'approved')
                            <form method="POST" action="{{ route('workshops.evidence.review', [$workshop, $evidence]) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-[11px] font-bold transition">Setujui</button>
                            </form>
                        @endif
                        @if ($evidence->status !== 'rejected')
                            <form method="POST" action="{{ route('workshops.evidence.review', [$workshop, $evidence]) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 text-[11px] font-bold transition">Tolak</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-xs text-slate-400">Belum ada bukti kehadiran yang dikirim peserta.</p>
            @endforelse
        </div>
    </div>
@endif
