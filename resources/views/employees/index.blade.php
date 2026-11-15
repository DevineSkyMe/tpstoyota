<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Directory & TPS Agents — TPS Portal</title>
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

                <!-- 1. Header & Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-slate-700 transition">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                </svg>
                                <span>Home</span>
                            </a>
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-900 font-semibold">Employees Directory</span>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            TPS Key Person & Employees
                        </h1>
                        <p class="text-xs text-slate-500">
                            Database seluruh praktisi Kaizen, Key Person manufaktur Toyota, dan status sertifikasi.
                        </p>
                    </div>

                    <!-- Add Employee Button -->
                    @if (($currentUser ?? auth()->user())?->isAdmin())
                        <div class="flex flex-wrap items-center gap-2.5 self-start">
                            <a href="{{ route('employees.import') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition active:scale-[0.98]">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>Import Excel</span>
                            </a>
                            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-[0.98]">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>Tambah Pegawai Baru</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Flash Message -->
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- 2. Summary KPI Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Pegawai</p>
                        <p class="text-xl font-black text-slate-900 mt-1">{{ $totalEmployees }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Aktif TPS</p>
                        <p class="text-xl font-black text-emerald-600 mt-1">{{ $activeEmployees }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">TPS KP 4</p>
                        <p class="text-xl font-black text-blue-600 mt-1">{{ $kp4Count }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">TPS KP 3</p>
                        <p class="text-xl font-black text-indigo-600 mt-1">{{ $kp3Count }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs col-span-2 sm:col-span-1">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">TPS Advance</p>
                        <p class="text-xl font-black text-amber-600 mt-1">{{ $advanceCount }}</p>
                    </div>
                </div>

                <!-- 3. Search & Interactive Multi-Filter Bar -->
                <form method="GET" action="{{ route('employees.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        
                        <!-- Search Keyword -->
                        <div class="relative lg:col-span-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau posisi..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>

                        <!-- Plant Filter -->
                        <select name="plant_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                            <option value="">Semua Plant</option>
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" {{ request('plant_id') == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                            @endforeach
                        </select>

                        <!-- Department Filter -->
                        <select name="department_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>

                        <!-- TPS Level Filter -->
                        <select name="level" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium cursor-pointer">
                            <option value="">Semua Level TPS</option>
                            @foreach (array_reverse(\App\Models\Employee::TPS_LEVELS) as $level)
                                <option value="{{ $level }}" {{ request('level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400">Status:</span>
                            <a href="{{ route('employees.index') }}" class="px-2.5 py-1 rounded-lg {{ !request('status') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">Semua</a>
                            <a href="{{ route('employees.index', array_merge(request()->query(), ['status' => 'active'])) }}" class="px-2.5 py-1 rounded-lg {{ request('status') === 'active' ? 'bg-emerald-50 text-emerald-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">Aktif</a>
                            <a href="{{ route('employees.index', array_merge(request()->query(), ['status' => 'inactive'])) }}" class="px-2.5 py-1 rounded-lg {{ request('status') === 'inactive' ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">Tidak Aktif</a>
                        </div>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold transition">Terapkan Filter</button>
                    </div>
                </form>

                <!-- 4. Employees Data Table -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                    <th class="py-3 px-4">Pegawai / NIK</th>
                                    <th class="py-3 px-4">Posisi & Departemen</th>
                                    <th class="py-3 px-4">Plant</th>
                                    <th class="py-3 px-4">Level TPS</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($employees as $emp)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <!-- Name & NIK -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                @include('partials.avatar', [
                                                    'photo' => $emp->photo,
                                                    'name' => $emp->name,
                                                    'size' => 'w-9 h-9 text-xs',
                                                    'rounded' => 'rounded-xl',
                                                    'bgClass' => 'bg-slate-900',
                                                ])
                                                <div>
                                                    <a href="{{ route('employees.show', $emp->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition">
                                                        {{ $emp->name }}
                                                    </a>
                                                    <p class="text-[10px] text-slate-400 font-mono">NIK: {{ $emp->username }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Position & Dept -->
                                        <td class="py-3.5 px-4">
                                            <p class="font-medium text-slate-800">{{ $emp->position }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $emp->department->name ?? '-' }}</p>
                                        </td>

                                        <!-- Plant -->
                                        <td class="py-3.5 px-4 text-slate-600">
                                            {{ $emp->plant->name ?? '-' }}
                                        </td>

                                        <!-- Level Badge -->
                                        <td class="py-3.5 px-4">
                                            @php
                                                $lvl = $emp->normalizedTpsLevel() ?? $emp->current_tps_level;
                                                $bgClass = match($lvl) {
                                                    'KP4' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'KP3' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'KP2' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'KP1' => 'bg-teal-50 text-teal-700 border-teal-200',
                                                    'Advance' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-md border {{ $bgClass }} font-bold text-[10px]">
                                                {{ $lvl }}
                                            </span>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-3.5 px-4">
                                            @if($emp->status === 'active')
                                                <span class="inline-flex items-center gap-1 text-emerald-600 font-bold text-[11px]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-slate-400 font-medium text-[11px]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('employees.show', $emp->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Lihat Profil">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </a>
                                                @if (($currentUser ?? auth()->user())?->isAdmin())
                                                    <a href="{{ route('employees.edit', $emp->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Pegawai">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                                    </a>
                                                    <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                            Tidak ada data pegawai yang sesuai dengan filter pencarian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer & Pagination -->
                    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                        <span>Menampilkan <strong>{{ $employees->firstItem() ?? 0 }}</strong> - <strong>{{ $employees->lastItem() ?? 0 }}</strong> dari <strong>{{ $employees->total() }}</strong> pegawai</span>
                        <div>
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

</body>
</html>

