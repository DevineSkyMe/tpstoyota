<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Kaizen Intelligence — TPS Portal</title>
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
                            <span class="text-slate-900 font-semibold">Analytics & Intelligence</span>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            TPS Intelligence & Cadence Analytics
                        </h1>
                        <p class="text-xs text-slate-500">
                            Matriks persebaran Key Person antar pabrik, rasio kelulusan workshop OMDD, efisiensi Kaizen, dan indeks kepatuhan standar Toyota.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200/90 shadow-xs text-xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-slate-800">OMDD Sync Active &bull; Q1 Live</span>
                    </div>
                </div>

                <!-- 2. System High-Level KPIs -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Key Persons</p>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalEmployees }}</p>
                        <p class="text-[10px] text-emerald-600 font-bold mt-1">&uarr; +14% YoY</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">KP 4 Ratio</p>
                        <p class="text-2xl font-black text-blue-600 mt-1">{{ $totalKp4 }} <span class="text-xs text-slate-400 font-bold">({{ round(($totalKp4 / max($totalEmployees, 1)) * 100) }}%)</span></p>
                        <p class="text-[10px] text-slate-400 mt-1">Master tier</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">KP 3 Implementers</p>
                        <p class="text-2xl font-black text-indigo-600 mt-1">{{ $totalKp3 }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">Standard tier</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Kaizen Completion</p>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ $completionRate }}%</p>
                        <p class="text-[10px] text-emerald-600 font-bold mt-1">{{ $completedKaizen }} of {{ $totalKaizen }} Selesai</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs col-span-2 sm:col-span-1">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Workshop Pass Rate</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ $passRate }}%</p>
                        <p class="text-[10px] text-slate-400 mt-1">OMDD Verified</p>
                    </div>
                </div>

                <!-- 3. Cross-Plant Level Distribution Analysis -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Persebaran Level TPS Antar Pabrik (Cross-Plant Deployment)</h3>
                            <p class="text-xs text-slate-500">Keseimbangan rasio Master (KP4), Implementer (KP3), dan Foundation (Advance) di tiap fasilitas</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-bold">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> KP 4</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> KP 3</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Advance</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($plants as $p)
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                    <button type="button"
                                            class="text-left rounded transition hover:text-blue-600 hover:underline underline-offset-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                            title="Klik untuk melihat seluruh Key Person di {{ $p['name'] }}"
                                            data-tps-open
                                            data-plant-id="{{ $p['id'] }}"
                                            data-plant-name="{{ $p['name'] }}"
                                            data-tier="all"
                                            data-tier-label="Semua Level TPS">
                                        {{ $p['name'] }}
                                    </button>
                                    <span class="text-slate-500">{{ $p['total'] }} Personel Total</span>
                                </div>
                                <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden flex">
                                    @php
                                        $t = max($p['total'], 1);
                                        $w4 = round(($p['kp4'] / $t) * 100);
                                        $w3 = round(($p['kp3'] / $t) * 100);
                                        $wa = round(($p['advance'] / $t) * 100);
                                    @endphp
                                    <button type="button"
                                            class="bg-blue-600 h-full transition hover:brightness-125 focus:outline-none"
                                            style="width: {{ $w4 }}%;"
                                            title="KP 4: {{ $p['kp4'] }} — klik untuk melihat daftar"
                                            data-tps-open
                                            data-plant-id="{{ $p['id'] }}"
                                            data-plant-name="{{ $p['name'] }}"
                                            data-tier="kp4"
                                            data-tier-label="KP 4"></button>
                                    <button type="button"
                                            class="bg-indigo-500 h-full transition hover:brightness-125 focus:outline-none"
                                            style="width: {{ $w3 }}%;"
                                            title="KP 3: {{ $p['kp3'] }} — klik untuk melihat daftar"
                                            data-tps-open
                                            data-plant-id="{{ $p['id'] }}"
                                            data-plant-name="{{ $p['name'] }}"
                                            data-tier="kp3"
                                            data-tier-label="KP 3"></button>
                                    <button type="button"
                                            class="bg-amber-500 h-full transition hover:brightness-125 focus:outline-none"
                                            style="width: {{ $wa }}%;"
                                            title="Advance: {{ $p['advance'] }} — klik untuk melihat daftar"
                                            data-tps-open
                                            data-plant-id="{{ $p['id'] }}"
                                            data-plant-name="{{ $p['name'] }}"
                                            data-tier="advance"
                                            data-tier-label="Advance"></button>
                                </div>
                                <div class="flex items-center justify-end gap-3 text-[10px] text-slate-400 font-mono">
                                    <button type="button" class="rounded transition hover:text-blue-600 hover:underline underline-offset-2 focus:outline-none"
                                            data-tps-open data-plant-id="{{ $p['id'] }}" data-plant-name="{{ $p['name'] }}"
                                            data-tier="kp4" data-tier-label="KP 4">KP4: {{ $p['kp4'] }}</button>
                                    <button type="button" class="rounded transition hover:text-indigo-600 hover:underline underline-offset-2 focus:outline-none"
                                            data-tps-open data-plant-id="{{ $p['id'] }}" data-plant-name="{{ $p['name'] }}"
                                            data-tier="kp3" data-tier-label="KP 3">KP3: {{ $p['kp3'] }}</button>
                                    <button type="button" class="rounded transition hover:text-amber-600 hover:underline underline-offset-2 focus:outline-none"
                                            data-tps-open data-plant-id="{{ $p['id'] }}" data-plant-name="{{ $p['name'] }}"
                                            data-tier="advance" data-tier-label="Advance">Advance: {{ $p['advance'] }}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Two Column Analytics: Cost Savings & OMDD Radar -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Kaizen Yield by Department -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-bold text-slate-900">Performa Kaizen & Kepadatan Personel per Departemen</h3>
                            <p class="text-xs text-slate-500">Estimasi kontribusi efisiensi lini dan standardisasi kerja</p>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @foreach($departments as $dept)
                                <div class="py-3 flex items-center justify-between gap-4 text-xs">
                                    <div class="space-y-0.5">
                                        <p class="font-bold text-slate-800">{{ $dept->name }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $dept->employees_count }} Key Persons Terdaftar</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-emerald-600 text-xs">+${{ rand(45, 160) }}K</span>
                                        <p class="text-[10px] text-slate-400">Annualized Yield</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quality & Safety Governance -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-bold text-slate-900">Indeks Kepatuhan Genba & Tata Kelola Standar</h3>
                            <p class="text-xs text-slate-500">Audit berkala kepatuhan instruksi kerja OMDD dan nihil kecelakaan (Zero Incident)</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-1">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Safety & Q6 Score</p>
                                <p class="text-2xl font-black text-slate-900">98.4<span class="text-xs text-slate-400">/100</span></p>
                                <p class="text-[10px] text-emerald-600 font-bold">Zero Safety Incident</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-1">
                                <p class="text-[10px] uppercase font-bold text-slate-400">5-Why Rigor Index</p>
                                <p class="text-2xl font-black text-blue-600">91.8%</p>
                                <p class="text-[10px] text-slate-500">Root Cause Solutif</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-1">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Yokoten Ratio</p>
                                <p class="text-2xl font-black text-indigo-600">84.2%</p>
                                <p class="text-[10px] text-slate-500">Replikasi Antar Lini</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-1">
                                <p class="text-[10px] uppercase font-bold text-slate-400">A3 Standard Audit</p>
                                <p class="text-2xl font-black text-emerald-600">96.0%</p>
                                <p class="text-[10px] text-slate-500">Tersertifikasi OMDD</p>
                            </div>
                        </div>
                    </div>

                </div>

            </main>

            <!-- Footer Component -->
            @include('partials.footer')

        </div>

    </div>

    <!-- TPS Tier Members Modal -->
    <div id="tps-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="tps-modal-title">
        <div id="tps-modal-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative z-10 flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-2xl bg-white rounded-2xl border border-slate-200/90 shadow-2xl overflow-hidden">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
                    <div class="min-w-0">
                        <p id="tps-modal-tier" class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Level TPS</p>
                        <h3 id="tps-modal-title" class="text-lg font-extrabold text-slate-900 truncate">Daftar Key Person</h3>
                        <p id="tps-modal-count" class="text-xs text-slate-500"></p>
                    </div>
                    <button type="button" id="tps-modal-close"
                            class="shrink-0 w-9 h-9 inline-flex items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition text-xl leading-none focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            aria-label="Tutup">&times;</button>
                </div>
                <div id="tps-modal-body" class="max-h-[60vh] overflow-y-auto divide-y divide-slate-100">
                    <div class="p-6 text-center text-sm text-slate-500">Memuat data&hellip;</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var endpoint = @json(route('analytics.members'));
            var modal = document.getElementById('tps-modal');
            var backdrop = document.getElementById('tps-modal-backdrop');
            var closeBtn = document.getElementById('tps-modal-close');
            var tierLabel = document.getElementById('tps-modal-tier');
            var title = document.getElementById('tps-modal-title');
            var count = document.getElementById('tps-modal-count');
            var body = document.getElementById('tps-modal-body');
            var lastFocus = null;

            var levelClasses = {
                'KP4': 'bg-blue-50 text-blue-700 border-blue-200',
                'KP3': 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'Advance': 'bg-amber-50 text-amber-700 border-amber-200',
                'KP2': 'bg-amber-50 text-amber-700 border-amber-200',
                'KP1': 'bg-amber-50 text-amber-700 border-amber-200'
            };

            function escapeHtml(value) {
                return String(value === null || value === undefined ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function openModal() {
                lastFocus = document.activeElement;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                closeBtn.focus();
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                if (lastFocus && typeof lastFocus.focus === 'function') {
                    lastFocus.focus();
                }
            }

            function rowHtml(member) {
                var badge = levelClasses[member.level] || 'bg-slate-50 text-slate-700 border-slate-200';
                var meta = [member.position, member.department].filter(Boolean).join(' &bull; ');
                return '<a href="' + escapeHtml(member.url) + '" class="flex items-center justify-between gap-3 px-5 py-3 transition hover:bg-slate-50">' +
                    '<div class="min-w-0">' +
                        '<p class="text-sm font-bold text-slate-800 truncate">' + escapeHtml(member.name) + '</p>' +
                        '<p class="text-[11px] text-slate-500 truncate">' + (meta || '&mdash;') + '</p>' +
                    '</div>' +
                    '<div class="text-right shrink-0 space-y-1">' +
                        '<span class="block text-[11px] font-mono text-slate-400">' + escapeHtml(member.nik) + '</span>' +
                        '<span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[10px] font-bold ' + badge + '">' + escapeHtml(member.level) + '</span>' +
                    '</div>' +
                '</a>';
            }

            function render(data) {
                tierLabel.textContent = 'Level ' + (data.tier === 'all' ? 'Semua' : data.tier.toUpperCase());
                title.textContent = data.plant;
                count.textContent = data.total + ' Key Person' + (data.total === 1 ? '' : 's');

                if (!data.members || data.members.length === 0) {
                    body.innerHTML = '<div class="p-8 text-center space-y-1">' +
                        '<p class="text-sm font-bold text-slate-700">Belum ada Key Person</p>' +
                        '<p class="text-xs text-slate-500">Tidak ada personel pada level ini di ' + escapeHtml(data.plant) + '.</p>' +
                    '</div>';
                    return;
                }

                body.innerHTML = data.members.map(rowHtml).join('');
            }

            function load(trigger) {
                var plantId = trigger.getAttribute('data-plant-id');
                var tier = trigger.getAttribute('data-tier');
                var plantName = trigger.getAttribute('data-plant-name');
                var label = trigger.getAttribute('data-tier-label');

                tierLabel.textContent = 'Level ' + label;
                title.textContent = plantName;
                count.textContent = '';
                body.innerHTML = '<div class="p-6 text-center text-sm text-slate-500">Memuat data&hellip;</div>';
                openModal();

                fetch(endpoint + '?plant_id=' + encodeURIComponent(plantId) + '&tier=' + encodeURIComponent(tier), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }
                        return response.json();
                    })
                    .then(render)
                    .catch(function () {
                        body.innerHTML = '<div class="p-8 text-center space-y-1">' +
                            '<p class="text-sm font-bold text-rose-600">Gagal memuat data</p>' +
                            '<p class="text-xs text-slate-500">Silakan coba lagi beberapa saat lagi.</p>' +
                        '</div>';
                    });
            }

            document.querySelectorAll('[data-tps-open]').forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    load(trigger);
                });
            });

            closeBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', closeModal);
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>

</body>
</html>

