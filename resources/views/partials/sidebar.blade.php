<!-- Sidebar Component (role-aware) -->
@php
    $user = $currentUser ?? auth()->user();
    $isDashboard = request()->routeIs('dashboard');
    $isActivity  = request()->routeIs('activity*');
    $isWorkshops = request()->routeIs('workshops.*');
    $isEmployees = request()->routeIs('employees.*');
    $isKp4       = request()->routeIs('tps.kp4');
    $isKp3       = request()->routeIs('tps.kp3');
    $isAdvance   = request()->routeIs('tps.advance');
    $isCandidates   = request()->routeIs('candidates.*');
    $isEvaluations  = request()->routeIs('evaluations.*');
    $isReports   = request()->routeIs('reports.*');
    $isAnalytics = request()->routeIs('analytics');
    $isMessages  = request()->routeIs('messages.*');
    $isProfile   = request()->routeIs('profile');

    $link       = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition group';
    $activeLink = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold bg-[#1d63ed] text-white shadow-md shadow-blue-500/20';
    $icon       = 'w-5 h-5 text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300';
    $activeIcon = 'w-5 h-5 text-white';
@endphp

<aside id="portal-sidebar"
       class="fixed inset-y-0 left-0 z-40 w-64 h-full bg-white dark:bg-[#0f172a] border-r border-slate-200 dark:border-slate-700/50 flex flex-col justify-between shrink-0 select-none transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:h-full">

    <!-- Top Branding & Navigation -->
    <div class="p-4 space-y-6 overflow-y-auto min-h-0 flex-1">

        <!-- Logo Header -->
        <div class="flex items-center justify-between px-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-[#1d63ed] flex items-center justify-center text-white shadow-sm group-hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4"/><path d="M6.5 8h11"/>
                        <rect width="8" height="6" x="8" y="8" rx="1"/>
                        <path d="m8 14-2 6"/><path d="m16 14 2 6"/>
                        <path d="M5 20h14"/>
                        <circle cx="12" cy="11" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-tight text-slate-900 dark:text-white leading-tight">TPS Portal</h1>
                    <p class="text-[10px] uppercase font-semibold tracking-wider text-slate-400 dark:text-slate-500">OMDD KEY PERSON</p>
                </div>
            </a>

            <button type="button" onclick="document.getElementById('portal-sidebar').classList.add('-translate-x-full')"
                    class="lg:hidden p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition" aria-label="Tutup menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Group: Menu Utama -->
        <div class="space-y-1">
            <p class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Menu Utama</p>

            <nav class="space-y-1 pt-1.5">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" class="{{ $isDashboard ? $activeLink : $link }}">
                    <svg class="{{ $isDashboard ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                @unless ($user?->isHrd())
                    {{-- Activity --}}
                    <a href="{{ route('activity') }}" class="{{ $isActivity ? $activeLink : $link }}">
                        <svg class="{{ $isActivity ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <path d="m9 14 2 2 4-4"/>
                        </svg>
                        <span>Activity</span>
                    </a>

                    {{-- Workshop --}}
                    <a href="{{ route('workshops.index') }}" class="{{ $isWorkshops ? $activeLink : $link }}">
                        <svg class="{{ $isWorkshops ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Workshop</span>
                    </a>
                @endunless

                {{-- Profile --}}
                <a href="{{ route('profile') }}" class="{{ $isProfile ? $activeLink : $link }}">
                    <svg class="{{ $isProfile ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M20 21a8 8 0 0 0-16 0"/>
                    </svg>
                    <span>Profil Saya</span>
                </a>

                {{-- Messages --}}
                <a href="{{ route('messages.index') }}" class="{{ $isMessages ? $activeLink : $link }}">
                    <svg class="{{ $isMessages ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>Pesan</span>
                    @if (($unreadMessagesCount ?? 0) > 0)
                        <span class="ml-auto inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-rose-500 text-white text-[10px] font-bold">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
            </nav>
        </div>

        {{-- Manajemen TPS (admin/hrd/omdd/manager) --}}
        @if ($user && $user->hasRole('admin', 'hrd', 'omdd', 'manager'))
            <div class="space-y-1">
                <p class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Manajemen TPS</p>

                <nav class="space-y-1 pt-1.5">
                    @if ($user->hasRole('admin', 'omdd', 'manager'))
                        {{-- Employees --}}
                        <a href="{{ route('employees.index') }}" class="{{ $isEmployees ? $activeLink : $link }}">
                            <svg class="{{ $isEmployees ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Employees</span>
                        </a>

                        {{-- TPS KP 4 --}}
                        <a href="{{ route('tps.kp4') }}" class="{{ $isKp4 ? $activeLink : $link }}">
                            <svg class="{{ $isKp4 ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15.4 15.63 1.94 4.37-3.34-1.9-3.34 1.9 1.94-4.37"/>
                                <circle cx="12" cy="9" r="6"/>
                                <path d="m9 9 2 2 4-4"/>
                            </svg>
                            <span>TPS KP 4</span>
                        </a>

                        {{-- TPS KP 3 --}}
                        <a href="{{ route('tps.kp3') }}" class="{{ $isKp3 ? $activeLink : $link }}">
                            <svg class="{{ $isKp3 ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                            </svg>
                            <span>TPS KP 3</span>
                        </a>

                        {{-- TPS Advance --}}
                        <a href="{{ route('tps.advance') }}" class="{{ $isAdvance ? $activeLink : $link }}">
                            <svg class="{{ $isAdvance ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                                <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                            </svg>
                            <span>TPS Advance</span>
                        </a>
                    @endif

                    {{-- Next Candidate --}}
                    <a href="{{ route('candidates.index') }}" class="{{ $isCandidates ? $activeLink : $link }}">
                        <svg class="{{ $isCandidates ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" x2="19" y1="8" y2="14"/>
                            <line x1="22" x2="16" y1="11" y2="11"/>
                        </svg>
                        <span>Next Candidate</span>
                    </a>

                    @if ($user->hasRole('omdd', 'admin'))
                        {{-- Penilaian OMDD --}}
                        <a href="{{ route('evaluations.index') }}" class="{{ $isEvaluations ? $activeLink : $link }}">
                            <svg class="{{ $isEvaluations ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                            </svg>
                            <span>Penilaian OMDD</span>
                        </a>
                    @endif

                    @if ($user->hasRole('admin', 'manager', 'hrd'))
                        {{-- Laporan --}}
                        <a href="{{ route('reports.index') }}" class="{{ $isReports ? $activeLink : $link }}">
                            <svg class="{{ $isReports ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                            <span>Laporan</span>
                        </a>
                    @endif

                    @if ($user->hasRole('admin', 'omdd', 'manager'))
                        {{-- Analytics --}}
                        <a href="{{ route('analytics') }}" class="{{ $isAnalytics ? $activeLink : $link }}">
                            <svg class="{{ $isAnalytics ? $activeIcon : $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" x2="18" y1="20" y2="10"/>
                                <line x1="12" x2="12" y1="20" y2="4"/>
                                <line x1="6" x2="6" y1="20" y2="14"/>
                            </svg>
                            <span>Analytics</span>
                        </a>
                    @endif
                </nav>
            </div>
        @endif
    </div>

    <!-- Bottom: Account card + logout -->
    <div class="p-4 border-t border-slate-100 dark:border-slate-700/50 space-y-2 shrink-0">
        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3 border border-slate-200/70 dark:border-slate-700/50 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-[#1d63ed] text-white flex items-center justify-center font-bold text-[11px] uppercase shrink-0">
                {{ substr($user->username ?? 'TPS', 0, 2) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ $user->display_name ?? $user->username ?? 'Guest' }}</p>
                <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 dark:text-slate-500">{{ $user->role_label ?? '' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:border-rose-200 dark:hover:border-rose-800 hover:text-rose-600 dark:hover:text-rose-400 text-slate-600 dark:text-slate-300 text-xs font-bold transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" x2="9" y1="12" y2="12"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>

</aside>
