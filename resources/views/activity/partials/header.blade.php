<!-- Activity Header Component -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    
    <!-- Left: Breadcrumbs, Title & Subtitle -->
    <div class="space-y-1">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-slate-700 transition">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Home</span>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Activity Monitoring Log</span>
        </nav>

        <!-- Main Page Title -->
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
            Activity Monitoring Log
        </h1>

        <!-- Subtitle Description -->
        <p class="text-xs md:text-sm text-slate-500 font-normal max-w-2xl leading-relaxed">
            Real-time tracking of Kaizen, Jishuken, and Genba problem-solving activities across manufacturing plants
        </p>
    </div>

    <!-- Right: Header Action Buttons -->
    <div class="flex items-center gap-3 shrink-0 self-start lg:self-center">
        @if (auth()->user()?->isAdmin() && ($pendingReviews ?? 0) > 0)
            <span class="inline-flex items-center gap-2 px-3 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 text-xs font-bold">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $pendingReviews }} menunggu ACC</span>
            </span>
        @endif
        @if (auth()->user()?->hasRole('admin', 'manager'))
            <!-- Export CSV / Report -->
            <a href="{{ route('reports.activity.export') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition active:scale-[0.98]">
                <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                <span>Export CSV / Report</span>
            </a>
        @endif

        <!-- Submit New Activity Update -->
        <a href="{{ route('activity.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-[0.98]">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>+ Tambah Kaizen Baru</span>
        </a>
    </div>

</div>

