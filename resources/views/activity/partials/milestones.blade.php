<!-- Bottom Milestones & Reference Cards Component -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-5">

    <!-- Milestone Card 1: Current Genba Milestone -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-sm flex items-center justify-between gap-3 hover:shadow-md transition">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                </svg>
            </div>
            <div class="truncate">
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Current Genba Milestone</p>
                <h4 class="text-xs font-bold text-slate-900 mt-1 truncate">
                    {{ $activeMilestone->title ?? 'Q1 OMDD Cross-Plant Review' }}
                </h4>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                    {{ $activeMilestone->location ?? 'Karawang Plant 1' }} &bull; Target: {{ $activeMilestone && $activeMilestone->end_date ? $activeMilestone->end_date->format('d M Y') : '18 Nov 2025' }}
                </p>
            </div>
        </div>
        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-bold text-[10px] shrink-0 border border-blue-100">
            {{ ucfirst($activeMilestone->status ?? 'Active') }}
        </span>
    </div>

    <!-- Milestone Card 2: Assessor Panel -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-sm flex items-center justify-between gap-3 hover:shadow-md transition">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <path d="m9 14 2 2 4-4"/>
                </svg>
            </div>
            <div class="truncate">
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Assessor Panel</p>
                <h4 class="text-xs font-bold text-slate-900 mt-1 truncate">OMDD Certification Council</h4>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">Lead: {{ $assessorUser->username ?? 'Hiroshi Sato' }} (OMDD HQ)</p>
            </div>
        </div>
        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[10px] shrink-0 border border-emerald-100">
            Verified
        </span>
    </div>

    <!-- Milestone Card 3: Reference Materials -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-sm flex items-center justify-between gap-3 hover:shadow-md transition">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                    <path d="M6 6h10"/>
                    <path d="M6 10h10"/>
                </svg>
            </div>
            <div class="truncate">
                <p class="text-[9px] uppercase font-bold tracking-wider text-slate-400 leading-none">Reference Materials</p>
                <h4 class="text-xs font-bold text-slate-900 mt-1 truncate">TPS Standard Kaizen</h4>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">A3 Problem Solving Guidelines</p>
            </div>
        </div>
        <a href="#reference" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 text-blue-600 hover:bg-blue-50 font-bold text-xs shrink-0 shadow-sm transition">
            <span>View</span>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
        </a>
    </div>

</section>

