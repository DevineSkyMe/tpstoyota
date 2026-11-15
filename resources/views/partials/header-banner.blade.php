<!-- Header Welcome Banner Component -->
<section class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    
    <!-- Left: Welcome Title & Operations Tag -->
    <div class="space-y-1.5">
        <!-- Status Pill -->
        <div class="inline-flex items-center gap-2 text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 text-blue-600 font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Lean Operations Live
            </span>
            <span class="text-slate-300">•</span>
            <span class="text-slate-500 font-medium">TPS Cadence FY25-Q1</span>
        </div>

        <!-- Main Title -->
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
            Welcome To Dashboard TPS Key Person Development Web
        </h1>

        <!-- Subtitle -->
        <p class="text-xs md:text-sm text-slate-500 font-normal">
            Digitalization and Agent Monitoring Portal &mdash; Operations Management Development Division
        </p>
    </div>

    <!-- Right: Evaluation Window Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-sm flex items-center gap-3 shrink-0 self-start lg:self-center">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="10" y1="10" y2="10"/>
                <path d="M8 14h.01"/>
                <path d="M12 14h.01"/>
                <path d="M16 14h.01"/>
                <path d="M8 18h.01"/>
                <path d="M12 18h.01"/>
            </svg>
        </div>
        <div>
            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 leading-none">Evaluation Window</p>
            <p class="text-xs font-bold text-slate-900 mt-1">{{ $evaluationWindow['formatted'] ?? 'Oct 24 – Nov 12, 2025' }}</p>
        </div>
    </div>

</section>

