<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }} — Activity Log Detail</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @include('partials.theme')
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <div class="min-h-full flex flex-row">
        
        <!-- Left Sidebar Component -->
        @include('partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
            
            <!-- Top Navbar Component -->
            @include('partials.navbar')

            @include('partials.flash')

            <!-- Main Container -->
            <main class="flex-1 p-4 md:p-8 space-y-6 max-w-5xl w-full mx-auto">

                <!-- Breadcrumbs & Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                        <span class="text-slate-300">/</span>
                        <a href="{{ route('activity') }}" class="text-slate-500 hover:text-slate-700">Activity Log</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900 font-semibold">KZ-{{ sprintf('%03d', $activity->id) }}</span>
                    </nav>

                    @if (($currentUser ?? auth()->user())?->isAdmin())
                        <div class="flex items-center gap-2.5">
                            <a href="{{ route('activity.edit', $activity->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs transition">
                                <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                <span>Edit Rencana</span>
                            </a>

                            <form action="{{ route('activity.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Hapus aktivitas Kaizen ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold shadow-xs transition">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Flash Message -->
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Hero Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-mono font-bold text-xs">
                                KZ-{{ sprintf('%03d', $activity->id) }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-bold text-xs">
                                {{ $activity->category }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full {{ $activity->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }} font-bold text-xs">
                                {{ strtoupper($activity->status) }}
                            </span>
                        </div>
                    </div>

                    <h1 class="text-xl md:text-2xl font-black text-slate-900 leading-snug">
                        {{ $activity->title }}
                    </h1>

                    <p class="text-xs md:text-sm text-slate-600 leading-relaxed">
                        {{ $activity->description ?? 'Tidak ada deskripsi rinci untuk aktivitas ini.' }}
                    </p>

                    <!-- Progress Bar -->
                    <div class="pt-2 space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span>Target vs Realisasi Progres</span>
                            <span class="text-blue-600 text-sm font-black">{{ $activity->progress_pct }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $activity->progress_pct }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                    <!-- Key Person PIC -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-2">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Key Person / PIC</p>
                        @if($activity->employee)
                            <div class="flex items-center gap-3 pt-1">
                                @include('partials.avatar', [
                                    'photo' => $activity->employee->photo,
                                    'name' => $activity->employee->name,
                                    'size' => 'w-10 h-10',
                                    'rounded' => 'rounded-xl',
                                    'bgClass' => 'bg-slate-900',
                                ])
                                <div>
                                    @if (($currentUser ?? auth()->user())?->isKaryawan())
                                        <span class="font-bold text-slate-900">{{ $activity->employee->name }}</span>
                                    @else
                                        <a href="{{ route('employees.show', $activity->employee->id) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                            {{ $activity->employee->name }}
                                        </a>
                                    @endif
                                    <p class="text-[10px] text-slate-400 font-mono">NIK: {{ $activity->employee->username }}</p>
                                    <span class="text-[10px] text-blue-600 font-bold">{{ $activity->employee->current_tps_level }}</span>
                                </div>
                            </div>
                        @else
                            <p class="font-bold text-slate-800">{{ $activity->pic ?? 'General Kaizen Team' }}</p>
                        @endif
                    </div>

                    <!-- Manufacturing Location -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-2">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Area Manufaktur</p>
                        <div class="space-y-1 pt-1">
                            <p class="font-bold text-slate-800">{{ $activity->employee->department->name ?? 'Production' }}</p>
                            <p class="text-slate-500">{{ $activity->employee->plant->name ?? 'Karawang Plant 1' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $activity->batch ?? 'Cadence Q1' }}</p>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-2">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Jadwal Pelaksanaan</p>
                        <div class="space-y-1 pt-1">
                            <p class="text-slate-600">Mulai: <strong class="text-slate-800">{{ $activity->planned_start ? $activity->planned_start->format('d M Y') : '-' }}</strong></p>
                            <p class="text-slate-600">Target Selesai: <strong class="text-slate-800">{{ $activity->planned_end ? $activity->planned_end->format('d M Y') : '-' }}</strong></p>
                            @if($activity->actual_end)
                                <p class="text-emerald-600 font-bold">Aktual: {{ $activity->actual_end->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $user = auth()->user();
                    $isAdmin = (bool) $user?->isAdmin();
                    $isOwner = $user?->isKaryawan() && (int) $activity->employee_id === (int) $user->employee_id;
                    $canSubmit = $isAdmin || $isOwner;
                    $updates = $activity->progressUpdates;
                    $pendingCount = $updates->where('approval_status', \App\Models\ActivityProgress::STATUS_PENDING)->count();
                    $approvalBadge = [
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $followupBadge = [
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'in_review' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'escalated' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                @endphp

                {{-- Owner (or Admin): submit a progress update --}}
                @if ($canSubmit)
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-bold text-slate-900">Kirim Perkembangan Activity</h3>
                            <p class="text-[11px] text-slate-500">
                                Laporkan progres yang sedang dikerjakan. Setiap laporan akan ditinjau (ACC) oleh Admin beserta feedback.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('activity.progress.store', $activity->id) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Status / Tahap Perkembangan <span class="text-rose-500">*</span></label>
                                    <input type="text" name="status_update" required maxlength="100"
                                           placeholder="mis. Trial layout baru & perbaikan jalur feeding"
                                           class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-xs">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Progres Saat Ini (%)</label>
                                    <input type="number" name="progress_pct" min="0" max="100"
                                           value="{{ $activity->progress_pct }}"
                                           class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-xs">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Kegiatan</label>
                                <textarea name="description" rows="3" maxlength="2000"
                                          placeholder="Jelaskan apa yang sudah/akan dikerjakan, kendala, dan rencana berikutnya."
                                          class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-xs"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Foto Bukti (opsional)</label>
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                                       class="w-full px-2 py-1.5 bg-white border border-slate-200/90 rounded-lg text-[11px] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 file:text-[10px] file:font-bold">
                                <p class="text-[10px] text-slate-400 mt-1">Format JPG/PNG/WEBP, maksimal 5 MB.</p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    <span>Kirim Perkembangan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Progress timeline with ACC status + admin feedback --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Riwayat Perkembangan & Persetujuan
                        </h3>
                        @if ($pendingCount > 0)
                            <span class="px-2.5 py-0.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200 text-[10px] font-bold">
                                {{ $pendingCount }} menunggu ACC
                            </span>
                        @endif
                    </div>

                    @forelse ($updates as $update)
                        <div class="py-4 border-b border-slate-50 last:border-0 space-y-3">
                            <div class="flex items-start justify-between gap-4 text-xs">
                                <div class="space-y-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-bold text-slate-800">{{ $update->status_update }}</p>
                                        <span class="px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $approvalBadge[$update->approval_status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                            {{ $update->approval_label }}
                                        </span>
                                    </div>
                                    @if ($update->description)
                                        <p class="text-slate-600 leading-relaxed">{{ $update->description }}</p>
                                    @endif
                                    <p class="text-[10px] text-slate-400">
                                        {{ $update->submittedBy?->display_name ?? 'Sistem' }}
                                        &bull; {{ $update->created_at ? $update->created_at->format('d M Y H:i') : '-' }}
                                    </p>
                                    @if ($update->image_url)
                                        <a href="{{ $update->image_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:underline">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                            Lihat foto bukti
                                        </a>
                                    @endif
                                </div>
                                @if ($update->progress_pct !== null)
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-black text-xs shrink-0">
                                        {{ $update->progress_pct }}%
                                    </span>
                                @endif
                            </div>

                            {{-- Admin feedback --}}
                            @if ($update->admin_note)
                                <div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2 text-[11px] text-slate-600">
                                    <span class="font-bold text-slate-700">Feedback Admin:</span> {{ $update->admin_note }}
                                    @if ($update->reviewer)
                                        <span class="text-slate-400">— {{ $update->reviewer->display_name }}, {{ $update->reviewed_at?->format('d M Y H:i') }}</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Admin review controls --}}
                            @if ($isAdmin)
                                @if ($update->isPending())
                                    <form method="POST" action="{{ route('activity.progress.review', [$activity->id, $update->id]) }}" class="space-y-2">
                                        @csrf @method('PATCH')
                                        <textarea name="admin_note" rows="2" maxlength="1000" placeholder="Feedback / catatan untuk karyawan (opsional)"
                                                  class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-[11px]"></textarea>
                                        <div class="flex items-center gap-2">
                                            <button type="submit" name="approval_status" value="approved" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-[11px] font-bold transition">ACC / Setujui</button>
                                            <button type="submit" name="approval_status" value="rejected" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 text-[11px] font-bold transition">Tolak</button>
                                        </div>
                                    </form>
                                @else
                                    <details class="text-[11px]">
                                        <summary class="cursor-pointer font-bold text-slate-400 hover:text-slate-600">Ubah keputusan / feedback</summary>
                                        <form method="POST" action="{{ route('activity.progress.review', [$activity->id, $update->id]) }}" class="space-y-2 pt-2">
                                            @csrf @method('PATCH')
                                            <textarea name="admin_note" rows="2" maxlength="1000" placeholder="Feedback / catatan"
                                                      class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-[11px]">{{ $update->admin_note }}</textarea>
                                            <div class="flex items-center gap-2">
                                                <button type="submit" name="approval_status" value="approved" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-[11px] font-bold transition">Setujui</button>
                                                <button type="submit" name="approval_status" value="rejected" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 text-[11px] font-bold transition">Tolak</button>
                                            </div>
                                        </form>
                                    </details>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">
                            Belum ada perkembangan yang dilaporkan untuk activity ini.
                        </p>
                    @endforelse
                </div>

                {{-- Admin monitoring notes (follow-up) --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Catatan Monitoring Admin</h3>
                        <p class="text-[11px] text-slate-500">Catatan pemantauan & tindak lanjut untuk activity ini.</p>
                    </div>

                    @if ($isAdmin)
                        <form method="POST" action="{{ route('activity.followup.store', $activity->id) }}" class="space-y-2">
                            @csrf
                            <textarea name="followup_note" rows="2" maxlength="1000" required placeholder="Tulis catatan pemantauan / arahan tindak lanjut"
                                      class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-xs"></textarea>
                            <div class="flex items-center gap-2">
                                <select name="status" class="px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-[11px] font-semibold">
                                    <option value="pending">Pending</option>
                                    <option value="in_review">In Review</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="escalated">Escalated</option>
                                </select>
                                <button type="submit" class="px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-[11px] font-bold transition">Tambah Catatan</button>
                            </div>
                        </form>
                    @endif

                    <div class="divide-y divide-slate-100">
                        @forelse ($activity->followups->sortByDesc('created_at') as $followup)
                            <div class="py-3 text-xs space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-slate-800">{{ $followup->admin?->display_name ?? 'Admin' }}</span>
                                    <span class="px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $followupBadge[$followup->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ strtoupper(str_replace('_', ' ', $followup->status)) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $followup->created_at?->format('d M Y H:i') }}</span>
                                </div>
                                <p class="text-slate-600 leading-relaxed">{{ $followup->followup_note }}</p>
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-slate-400">Belum ada catatan monitoring.</p>
                        @endforelse
                    </div>
                </div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

</body>
</html>

