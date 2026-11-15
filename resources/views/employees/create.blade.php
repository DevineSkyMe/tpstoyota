<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai Baru — TPS Portal</title>
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
            <main class="flex-1 p-4 md:p-8 space-y-6 max-w-4xl w-full mx-auto">

                <!-- 1. Breadcrumb & Title -->
                <div class="space-y-1">
                    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                        <span class="text-slate-300">/</span>
                        <a href="{{ route('employees.index') }}" class="text-slate-500 hover:text-slate-700">Employees</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900 font-semibold">Tambah Pegawai</span>
                    </nav>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                        Pendaftaran Pegawai & TPS Key Person
                    </h1>
                    <p class="text-xs text-slate-500">
                        Tambahkan profil praktisi baru untuk pelacakan kompetensi Kaizen dan sertifikasi TPS.
                    </p>
                </div>

                <!-- Validation Errors Alert -->
                @if($errors->any())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Terdapat kesalahan pengisian formulir:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm">
                    <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Section 1: Data Identitas Pegawai -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2">
                                1. Identitas Dasar Pegawai
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <!-- Name -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Kenji Takahashi" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                </div>

                                <!-- Username / NIK -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Nomor Induk Karyawan (NIK) <span class="text-rose-500">*</span></label>
                                    <input type="text" name="username" value="{{ old('username') }}" required placeholder="Contoh: 51204" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                </div>

                                <!-- Birth Date -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                </div>

                                <!-- Position -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Posisi / Jabatan <span class="text-rose-500">*</span></label>
                                    <input type="text" name="position" value="{{ old('position') }}" required placeholder="Contoh: Welding Automation Specialist" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Penempatan & Departemen -->
                        <div class="space-y-4 pt-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2">
                                2. Penempatan Pabrik & Organisasi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <!-- Plant -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Plant <span class="text-rose-500">*</span></label>
                                    <select name="plant_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                        <option value="">Pilih Plant</option>
                                        @foreach($plants as $plant)
                                            <option value="{{ $plant->id }}" {{ old('plant_id') == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Division -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Divisi <span class="text-rose-500">*</span></label>
                                    <select name="division_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                        <option value="">Pilih Divisi</option>
                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Department -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Departemen <span class="text-rose-500">*</span></label>
                                    <select name="department_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                        <option value="">Pilih Departemen</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Level TPS & Sertifikasi -->
                        <div class="space-y-4 pt-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2">
                                3. Kompetensi & Kualifikasi TPS Key Person
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <!-- Current Level -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Level TPS Saat Ini <span class="text-rose-500">*</span></label>
                                    <select name="current_tps_level" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer font-bold text-blue-700">
                                        @foreach (array_reverse(\App\Models\Employee::TPS_LEVELS) as $level)
                                            <option value="{{ $level }}" {{ old('current_tps_level', 'Advance') === $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Previous Level -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Level TPS Sebelumnya</label>
                                    <select name="previous_tps_level" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                        <option value="">Tidak Ada / Baru</option>
                                        @foreach (array_reverse(\App\Models\Employee::TPS_LEVELS) as $level)
                                            <option value="{{ $level }}" {{ old('previous_tps_level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Status Keaktifan <span class="text-rose-500">*</span></label>
                                    <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('employees.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-[0.98]">
                                Simpan Data Pegawai
                            </button>
                        </div>
                    </form>
                </div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

</body>
</html>

