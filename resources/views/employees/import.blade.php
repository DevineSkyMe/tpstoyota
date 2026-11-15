<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Pegawai (Excel) — TPS Portal</title>
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
                        <span class="text-slate-900 font-semibold">Import Excel</span>
                    </nav>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                        Import Pegawai dari Excel
                    </h1>
                    <p class="text-xs text-slate-500">
                        Unggah file <span class="font-semibold">.xlsx</span> (contoh: TPS Dashboard Web) untuk menambahkan pegawai sekaligus
                        membuat akun login karyawan secara otomatis.
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

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Import Result -->
                @php($result = session('import_result'))
                @if($result)
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <h2 class="text-sm font-bold text-slate-900">Ringkasan Hasil Import</h2>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Pegawai Baru</p>
                                <p class="text-xl font-black text-emerald-600 mt-1">{{ $result['created'] }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Diperbarui</p>
                                <p class="text-xl font-black text-blue-600 mt-1">{{ $result['updated'] }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Akun Dibuat</p>
                                <p class="text-xl font-black text-indigo-600 mt-1">{{ $result['accounts'] }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Dilewati</p>
                                <p class="text-xl font-black text-amber-600 mt-1">{{ $result['skipped'] }}</p>
                            </div>
                        </div>

                        @if(! empty($result['sheets']))
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="text-left text-slate-400 uppercase text-[10px] tracking-wider">
                                            <th class="py-2 pr-3 font-bold">Sheet</th>
                                            <th class="py-2 px-3 font-bold text-right">Baris</th>
                                            <th class="py-2 px-3 font-bold text-right">Baru</th>
                                            <th class="py-2 px-3 font-bold text-right">Update</th>
                                            <th class="py-2 pl-3 font-bold text-right">Dilewati</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($result['sheets'] as $sheet => $stat)
                                            <tr>
                                                <td class="py-2 pr-3 font-semibold text-slate-700">{{ $sheet }}</td>
                                                <td class="py-2 px-3 text-right text-slate-500">{{ $stat['rows'] }}</td>
                                                <td class="py-2 px-3 text-right text-emerald-600 font-bold">{{ $stat['created'] }}</td>
                                                <td class="py-2 px-3 text-right text-blue-600 font-bold">{{ $stat['updated'] }}</td>
                                                <td class="py-2 pl-3 text-right text-amber-600 font-bold">{{ $stat['skipped'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if(! empty($result['errors']))
                            <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-700 space-y-1">
                                <p class="font-bold">{{ count($result['errors']) }} baris gagal diimport:</p>
                                <ul class="list-disc list-inside space-y-0.5 max-h-56 overflow-y-auto">
                                    @foreach(array_slice($result['errors'], 0, 50) as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Upload Form Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm">
                    <form action="{{ route('employees.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2">
                                1. Berkas Excel
                            </h3>

                            <div class="space-y-1 text-xs">
                                <label class="font-bold text-slate-700">File .xlsx <span class="text-rose-500">*</span></label>
                                <input type="file" name="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[#1d63ed] file:text-white file:text-xs file:font-bold cursor-pointer">
                                @error('file')
                                    <p class="text-rose-600 text-[11px] font-semibold">{{ $message }}</p>
                                @enderror
                                <p class="text-[11px] text-slate-400">Maksimal 10 MB. Format mengikuti file HRD (sheet <em>TPS KP</em>, <em>TPS ADVANCE</em>, <em>MEMBER OFFICE JISHUKEN</em>).</p>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2">
                                2. Pengaturan Akun &amp; Level
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Password Default Akun <span class="text-rose-500">*</span></label>
                                    <input type="text" name="default_password" value="{{ old('default_password', $defaultPassword) }}" required
                                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                    @error('default_password')
                                        <p class="text-rose-600 text-[11px] font-semibold">{{ $message }}</p>
                                    @enderror
                                    <p class="text-[11px] text-slate-400">Setiap pegawai baru otomatis mendapat akun <span class="font-mono">tps_&lt;NIK&gt;</span> dengan password ini.</p>
                                </div>

                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Level untuk Sheet Tanpa Kolom KP</label>
                                    <select name="fallback_level" class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer font-bold text-blue-700">
                                        @foreach($levels as $level)
                                            <option value="{{ $level }}" {{ old('fallback_level', 'Advance') === $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-slate-400">Dipakai untuk sheet seperti <em>MEMBER OFFICE JISHUKEN</em> yang tidak mencantumkan level.</p>
                                </div>
                            </div>

                            <label class="flex items-start gap-2.5 text-xs cursor-pointer">
                                <input type="checkbox" name="update_existing" value="1" {{ old('update_existing', '1') ? 'checked' : '' }}
                                       class="mt-0.5 w-4 h-4 rounded border-slate-300 text-[#1d63ed] focus:ring-blue-500/30">
                                <span>
                                    <span class="font-bold text-slate-700">Perbarui data pegawai yang sudah ada</span>
                                    <span class="block text-[11px] text-slate-400">Jika dimatikan, NIK yang sudah terdaftar akan dilewati (tidak diubah).</span>
                                </span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('employees.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                                Kembali
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-[0.98]">
                                Mulai Import
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Column reference -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Kolom yang Dikenali</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1.5 text-[11px] text-slate-600">
                        <p><span class="font-mono font-bold">NOREG</span> &rarr; NIK / username pegawai</p>
                        <p><span class="font-mono font-bold">NAMA</span> &rarr; nama lengkap</p>
                        <p><span class="font-mono font-bold">FOTO</span> &rarr; URL foto</p>
                        <p><span class="font-mono font-bold">JABATAN</span> &rarr; posisi / position</p>
                        <p><span class="font-mono font-bold">BATCH</span> &rarr; angkatan</p>
                        <p><span class="font-mono font-bold">DIVISI</span> &rarr; divisi (dibuat otomatis bila baru)</p>
                        <p><span class="font-mono font-bold">DEPARTEMEN</span> &rarr; departemen (dibuat otomatis bila baru)</p>
                        <p><span class="font-mono font-bold">PLANT_ID</span> &rarr; plant (dibuat otomatis bila baru)</p>
                        <p><span class="font-mono font-bold">KP_ID</span> &rarr; level TPS (mis. TPS KP 4 &rarr; KP4)</p>
                        <p><span class="font-mono font-bold">TAHUN LAHIR</span> &rarr; tahun lahir</p>
                        <p><span class="font-mono font-bold">TAHUN PENSIUN</span> &rarr; tahun pensiun</p>
                        <p><span class="font-mono font-bold">PROSES_ID</span> &rarr; tahap proses (sheet Advance)</p>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Sheet yang tidak memiliki kolom <span class="font-mono">NOREG</span> dan <span class="font-mono">NAMA</span> (mis. rekap persentase) akan diabaikan otomatis.
                    </p>
                </div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

</body>
</html>
