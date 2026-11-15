<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Aktivitas: {{ $activity->title }} — TPS Portal</title>
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

                <!-- Breadcrumb & Title -->
                <div class="space-y-1">
                    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                        <span class="text-slate-300">/</span>
                        <a href="{{ route('activity') }}" class="text-slate-500 hover:text-slate-700">Activity Log</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900 font-semibold">Edit Aktivitas</span>
                    </nav>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                        Edit Rencana Proyek Kaizen
                    </h1>
                    <p class="text-xs text-slate-500">
                        Perbarui rincian target, PIC pelaksana, dan status progres milestonenya.
                    </p>
                </div>

                <!-- Validation Errors Alert -->
                @if($errors->any())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Periksa kembali formulir:
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
                    <form action="{{ route('activity.update', $activity->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Field 1: Title -->
                        <div class="space-y-1 text-xs">
                            <label class="font-bold text-slate-700">Judul Inisiatif / Tema Kaizen <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $activity->title) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-xs font-medium">
                        </div>

                        <!-- Field 2: Description -->
                        <div class="space-y-1 text-xs">
                            <label class="font-bold text-slate-700">Deskripsi Masalah & Target Indikator</label>
                            <textarea name="description" rows="3" class="w-full p-3.5 bg-slate-50 border border-slate-200/90 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-xs leading-relaxed">{{ old('description', $activity->description) }}</textarea>
                        </div>

                        <!-- Field 3: Employee (PIC) & Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Penanggung Jawab / Key Person (PIC) <span class="text-rose-500">*</span></label>
                                <select name="employee_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id', $activity->employee_id) == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }} ({{ $emp->username }} &bull; {{ $emp->current_tps_level }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Kategori Kaizen <span class="text-rose-500">*</span></label>
                                <select name="category" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                    <option value="Kaizen Jishuken" {{ old('category', $activity->category) == 'Kaizen Jishuken' ? 'selected' : '' }}>Kaizen Jishuken</option>
                                    <option value="Poka-Yoke Quality" {{ old('category', $activity->category) == 'Poka-Yoke Quality' ? 'selected' : '' }}>Poka-Yoke Quality</option>
                                    <option value="Productivity & Cycle Time" {{ old('category', $activity->category) == 'Productivity & Cycle Time' ? 'selected' : '' }}>Productivity & Cycle Time</option>
                                    <option value="Safety & Ergonomics" {{ old('category', $activity->category) == 'Safety & Ergonomics' ? 'selected' : '' }}>Safety & Ergonomics</option>
                                    <option value="Standardized Work" {{ old('category', $activity->category) == 'Standardized Work' ? 'selected' : '' }}>Standardized Work</option>
                                </select>
                            </div>
                        </div>

                        <!-- Field 4: Timeline Planned Start & End -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Target Tanggal Mulai <span class="text-rose-500">*</span></label>
                                <input type="date" name="planned_start" value="{{ old('planned_start', $activity->planned_start ? $activity->planned_start->format('Y-m-d') : '') }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Target Tanggal Selesai <span class="text-rose-500">*</span></label>
                                <input type="date" name="planned_end" value="{{ old('planned_end', $activity->planned_end ? $activity->planned_end->format('Y-m-d') : '') }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
                            </div>
                        </div>

                        <!-- Field 5: Progress Pct & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Status Aktivitas <span class="text-rose-500">*</span></label>
                                <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl cursor-pointer">
                                    <option value="not_started" {{ old('status', $activity->status) == 'not_started' ? 'selected' : '' }}>Not Started</option>
                                    <option value="in_progress" {{ old('status', $activity->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="delay" {{ old('status', $activity->status) == 'delay' ? 'selected' : '' }}>Delayed</option>
                                    <option value="completed" {{ old('status', $activity->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Progress Kemajuan (%) <span class="text-rose-500">*</span></label>
                                <input type="number" min="0" max="100" name="progress_pct" value="{{ old('progress_pct', $activity->progress_pct) }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Batch Cadence</label>
                                <input type="text" name="batch" value="{{ old('batch', $activity->batch) }}" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('activity.show', $activity->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-[0.98]">
                                Simpan Perubahan
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

