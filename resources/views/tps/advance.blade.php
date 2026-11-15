<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPS Advance (KP 1 - KP 2) — TPS Portal</title>
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
            <main class="flex-1 p-4 md:p-8 space-y-6 max-w-7xl w-full mx-auto">

                <!-- 1. Header Banner -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-900 font-semibold">TPS Advance Tier</span>
                        </nav>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
                                TPS Advance (KP 1 - KP 2)
                            </h1>
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs">Foundation Tier</span>
                        </div>
                        <p class="text-xs text-slate-500 max-w-2xl">
                            Praktisi Lean manufaktur tingkat dasar dan menengah yang sedang mengasah kemampuan 5S, Standar Kerja, dan eliminasi pemborosan (Muda/Mura/Muri).
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('candidates.index') }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-500/20 transition">
                            Pipeline Evaluasi Jishuken &rarr;
                        </a>
                    </div>
                </div>

                <!-- 2. KPI Metrics Bar for TPS Advance -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Advance & KP 1-2</p>
                        <p class="text-2xl font-black text-amber-600 mt-1">{{ $totalAdvance }} <span class="text-xs font-normal text-slate-500">Personel</span></p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Readiness Index</p>
                        <p class="text-2xl font-black text-blue-600 mt-1">{{ $readinessScore }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Modul 5S Tuntas</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ $completed5SCourses }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Mentorship Aktif</p>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ $mentorshipPairs }} <span class="text-xs font-normal text-slate-500">Pasang</span></p>
                    </div>
                </div>

                <!-- 3. Search & Filter Bar -->
                <form method="GET" action="{{ route('tps.advance') }}" class="flex flex-col sm:flex-row items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs">
                    <div class="relative flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari praktisi TPS Advance..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>

                    <select name="plant_id" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs cursor-pointer font-medium">
                        <option value="">Semua Plant</option>
                        @foreach($plants as $plant)
                            <option value="{{ $plant->id }}" {{ request('plant_id') == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl hover:bg-slate-900 transition">
                        Filter
                    </button>
                </form>

                <!-- 4. TPS Advance Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @forelse($employees as $emp)
                        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold">
                                        {{ $emp->current_tps_level }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        Cadence Track
                                    </span>
                                </div>

                                <div class="flex items-center gap-3">
                                    @include('partials.avatar', [
                                        'photo' => $emp->photo,
                                        'name' => $emp->name,
                                        'size' => 'w-11 h-11 text-sm',
                                        'rounded' => 'rounded-xl',
                                        'bgClass' => 'bg-amber-500',
                                    ])
                                    <div class="truncate">
                                        <h3 class="text-xs font-bold text-slate-900 truncate">{{ $emp->name }}</h3>
                                        <p class="text-[10px] text-slate-400 font-mono">NIK {{ $emp->username }}</p>
                                    </div>
                                </div>

                                <div class="text-[11px] text-slate-500 space-y-1 pt-1 border-t border-slate-100">
                                    <p><strong>Plant:</strong> {{ $emp->plant->name ?? '-' }}</p>
                                    <p><strong>Dept:</strong> {{ $emp->department->name ?? '-' }}</p>
                                    <p><strong>Posisi:</strong> {{ $emp->position }}</p>
                                </div>
                            </div>

                            <a href="{{ route('employees.show', $emp->id) }}" class="w-full text-center py-2 rounded-xl bg-slate-50 hover:bg-amber-50 hover:text-amber-700 text-slate-700 text-xs font-bold transition">
                                Lihat Profil Lengkap
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-400 text-xs">
                            Belum ada praktisi TPS Advance pada kriteria ini.
                        </div>
                    @endforelse
                </div>

                <div>{{ $employees->links() }}</div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

</body>
</html>

