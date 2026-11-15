<!-- Navbar Component -->
@php
    $user = $currentUser ?? auth()->user();
    $unread = $unreadMessagesCount ?? 0;
@endphp

<header class="sticky top-0 h-16 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between gap-4 shrink-0 z-20">

    <!-- Left: mobile menu toggle + global search -->
    <div class="flex items-center gap-2 flex-1 max-w-md">
        <button type="button" onclick="document.getElementById('portal-sidebar').classList.toggle('-translate-x-full')"
                class="lg:hidden p-2 rounded-xl border border-slate-200/90 bg-white text-slate-600 hover:bg-slate-50 transition" aria-label="Buka menu">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <form method="GET" action="{{ route('search') }}" class="relative flex items-center w-full">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input type="search"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Cari karyawan, NIK, activity, atau workshop..."
                   class="w-full pl-9 pr-4 py-2 bg-[#f1f4f9] border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-inner">
        </form>
    </div>

    <!-- Right: Utility Actions & User Profile -->
    <div class="flex items-center gap-3">

        <!-- Theme toggle (mode terang / gelap) -->
        @include('partials.theme-toggle')

        <!-- Notifications -->
        <a href="{{ route('messages.index') }}" class="relative p-2 rounded-xl border border-slate-200/90 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition shadow-sm" aria-label="Notifikasi">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
            </svg>
            @if ($unread > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-bold ring-2 ring-white">{{ $unread }}</span>
            @endif
        </a>

        <!-- User Profile -->
        <div class="h-6 w-px bg-slate-200 mx-1"></div>

        <a href="{{ route('profile') }}" class="flex items-center gap-3 pl-1 group">
            <div class="text-right hidden sm:block">
                <h2 class="text-xs font-bold text-slate-900 leading-tight group-hover:text-blue-600 transition">
                    {{ $user->display_name ?? ($user->username ?? 'Guest') }}
                </h2>
                <p class="text-[10px] uppercase font-semibold tracking-wider text-slate-400 leading-tight mt-0.5">
                    {{ $user->role_label ?? '' }}
                </p>
            </div>

            @include('partials.avatar', [
                'photo' => $user->employee?->photo,
                'name' => $user->display_name ?? $user->username ?? 'Guest',
                'size' => 'w-9 h-9 text-xs',
                'rounded' => 'rounded-full',
                'extraClass' => 'shadow-sm ring-2 ring-blue-100',
            ])
        </a>

    </div>

</header>
