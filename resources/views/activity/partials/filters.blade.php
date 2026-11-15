<!-- Activity Filters & Search Toolbar Component (Dynamic & Interactive) -->
<form method="GET" action="{{ url('/activity') }}" id="filterForm" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-sm">

    <!-- Search Input & Dropdowns Container -->
    <div class="flex flex-wrap items-center gap-2.5 flex-1">
        
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[280px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input type="text" 
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by Project ID, Agent Name, or Kaizen Topic..." 
                   class="w-full pl-9 pr-3 py-2 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
        </div>

        <!-- Filter 1: Departments from Database -->
        <div class="relative">
            <select name="department_id" onchange="document.getElementById('filterForm').submit()" class="appearance-none pl-3 pr-8 py-2 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100/70 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition">
                <option value="">All Departments</option>
                @if(isset($departments) && $departments->isNotEmpty())
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                @else
                    <option value="assembly">Assembly Plant</option>
                    <option value="press">Press Shop</option>
                    <option value="body">Body Shop</option>
                    <option value="powertrain">Powertrain</option>
                    <option value="qa">Quality Assurance</option>
                @endif
            </select>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
        </div>

        <!-- Filter 2: Levels -->
        <div class="relative">
            <select name="level" onchange="document.getElementById('filterForm').submit()" class="appearance-none pl-3 pr-8 py-2 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100/70 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition">
                <option value="">All Levels</option>
                @foreach(['KP4', 'KP3', 'Advance', 'KP2', 'KP1'] as $lvl)
                    <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>
                        {{ $lvl === 'Advance' ? 'TPS Advance' : $lvl }}
                    </option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
        </div>

        <!-- Filter 3: Statuses -->
        <div class="relative">
            <select name="status" onchange="document.getElementById('filterForm').submit()" class="appearance-none pl-3 pr-8 py-2 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-100/70 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition">
                <option value="">All Statuses</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="delay" {{ request('status') === 'delay' ? 'selected' : '' }}>Delayed / Review Due</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- Date Range, Reset & View Toggle Container -->
    <div class="flex items-center gap-2 shrink-0">
        
        <!-- Date Range Filter -->
        <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-700">
            <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Oct 2025 - Nov 2025</span>
        </div>

        <!-- Reset Filter Link -->
        <a href="{{ url('/activity') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50/70 hover:bg-slate-100 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-600 transition" title="Reset Filters">
            <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                <path d="M3 3v5h5"/>
            </svg>
            <span>Reset</span>
        </a>

        <!-- View Switcher -->
        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200/70">
            <!-- List View (Active) -->
            <button type="button" class="p-1.5 rounded-lg bg-white shadow-sm text-blue-600 border border-slate-200/80 transition" aria-label="List View">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
            </button>
            <!-- Grid View -->
            <button type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 transition" aria-label="Grid View">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
            </button>
        </div>

    </div>

</form>
