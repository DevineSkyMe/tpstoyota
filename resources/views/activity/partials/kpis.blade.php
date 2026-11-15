<!-- Activity 3 KPI Cards Component -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-5">

    <!-- KPI Card 1: Total Activities -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
        <!-- Top Row -->
        <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Activities</span>
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                    <path d="m3 9 18 0"/>
                    <path d="M9 21V9"/>
                </svg>
            </div>
        </div>

        <!-- Metric Value -->
        <div class="my-3 flex items-baseline gap-1.5">
            <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalActivities ?? 32 }}</span>
            <span class="text-sm font-semibold text-slate-500">Projects</span>
        </div>

        <!-- Bottom Stat Indicator -->
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[11px]">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                    <polyline points="17 6 23 6 23 12"/>
                </svg>
                +{{ $thisWeekNew ?? 4 }} this week
            </span>
            <span class="text-slate-400">vs. last cycle</span>
        </div>
    </div>

    <!-- KPI Card 2: In Progress -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
        <!-- Top Row -->
        <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">In Progress</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
        </div>

        <!-- Metric Value -->
        <div class="my-3 flex items-baseline gap-1.5">
            <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $inProgressActivities ?? 14 }}</span>
            <span class="text-sm font-semibold text-slate-500">Projects</span>
        </div>

        <!-- Bottom Stat Indicator -->
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px]">
                <svg class="w-3 h-3 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ $reviewsDue ?? 6 }} reviews due
            </span>
            <span class="text-slate-400">pending OMDD review</span>
        </div>
    </div>

    <!-- KPI Card 3: Completed This Month -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
        <!-- Top Row -->
        <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Completed This Month</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
        </div>

        <!-- Metric Value -->
        <div class="my-3 flex items-baseline gap-1.5">
            <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $completedActivities ?? 18 }}</span>
            <span class="text-sm font-semibold text-slate-500">Projects</span>
        </div>

        <!-- Bottom Stat Indicator -->
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[11px]">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                {{ $completionRate ?? '84.2' }}% rate
            </span>
            <span class="text-slate-400">on-target completion</span>
        </div>
    </div>

</section>

