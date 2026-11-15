<!-- Bottom Metrics Bar & Action Controls Component (role-aware) -->
@php
    $user = $currentUser ?? auth()->user();
    $canCreateActivity = $user !== null && ! $user->isHrd();
    $canExport = $user?->hasRole('admin', 'manager') ?? false;
@endphp

<section class="bg-white rounded-2xl border border-slate-200/90 p-3 shadow-sm flex flex-col xl:flex-row items-center justify-between gap-4">

    <!-- Left: Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 w-full xl:w-auto">

        <div class="bg-blue-50/70 border border-blue-100/80 rounded-xl px-3.5 py-2 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100/70 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Total Agents</p>
                <p class="text-xs font-extrabold text-slate-900 mt-1">{{ $totalAgents ?? 0 }} <span class="font-semibold text-slate-600">Active</span></p>
            </div>
        </div>

        <div class="bg-rose-50/70 border border-rose-100/80 rounded-xl px-3.5 py-2 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-rose-100/70 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                    <polyline points="16 7 22 7 22 13"/>
                </svg>
            </div>
            <div>
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Kaizen Projects</p>
                <p class="text-xs font-extrabold text-slate-900 mt-1">{{ $kaizenProjects ?? 0 }} <span class="font-semibold text-slate-600">Monitored</span></p>
            </div>
        </div>

        <div class="bg-amber-50/70 border border-amber-100/80 rounded-xl px-3.5 py-2 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>
                </svg>
            </div>
            <div>
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Under Review</p>
                <p class="text-xs font-extrabold text-slate-900 mt-1">{{ $underReview ?? 0 }} <span class="font-semibold text-slate-600">Projects</span></p>
            </div>
        </div>

        <div class="bg-emerald-50/70 border border-emerald-100/80 rounded-xl px-3.5 py-2 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100/70 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Success Rate</p>
                <p class="text-xs font-extrabold text-slate-900 mt-1">{{ $successRate ?? 0 }}% <span class="font-semibold text-slate-600">Pass</span></p>
            </div>
        </div>

    </div>

    <div class="text-center xl:text-left px-2">
        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 leading-none">Assessor System Status</p>
        <p class="text-xs font-bold text-emerald-600 flex items-center justify-center xl:justify-start gap-1.5 mt-1">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            OMDD Engine Synced
        </p>
    </div>

    <!-- Right: Action Buttons -->
    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
        @if ($canCreateActivity)
            <a href="{{ route('activity.create') }}"
               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-[0.98]">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" x2="12" y1="8" y2="16"/>
                    <line x1="8" x2="16" y1="12" y2="12"/>
                </svg>
                <span>Submit Activity</span>
            </a>
        @endif

        @if ($canExport)
            <a href="{{ route('reports.activity.export') }}"
               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition active:scale-[0.98]">
                <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                <span>Export Log</span>
            </a>
        @endif
    </div>

</section>
