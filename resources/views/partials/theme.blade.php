@php
    // Shared theme layer: visible (black) form borders + optional dark mode.
    // Injected into <head> of every layout so the theme applies before first paint.
@endphp

<script>
    (function () {
        try {
            var stored = localStorage.getItem('tps-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = stored || (prefersDark ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    })();

    window.tpsToggleTheme = function () {
        var root = document.documentElement;
        var isDark = root.classList.toggle('dark');
        try {
            localStorage.setItem('tps-theme', isDark ? 'dark' : 'light');
        } catch (e) {}
    };
</script>

<style>
    /* ------------------------------------------------------------------ */
    /* 1. Batas form jelas: garis hitam pada semua field                    */
    /* ------------------------------------------------------------------ */
    input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
    select,
    textarea {
        border: 2px solid #0f172a !important;
    }

    /* ------------------------------------------------------------------ */
    /* 1b. Latar terang yang lebih lembut (tidak putih menyilaukan)        */
    /* ------------------------------------------------------------------ */
    html:not(.dark) {
        background-color: #e4e8ef !important;
    }

    html:not(.dark) body {
        background-color: #e4e8ef !important;
    }

    /* Kanvas halaman (html & pembungkus konten standalone) */
    html:not(.dark) .bg-\[\#f8fafc\],
    html:not(.dark) .bg-\[\#f4f7fc\] {
        background-color: #e4e8ef !important;
    }

    /* Permukaan / kartu: sedikit diturunkan dari putih murni agar tidak kontras */
    html:not(.dark) .bg-white {
        background-color: #f6f8fb !important;
    }

    /* ------------------------------------------------------------------ */
    /* 2. Mode malam                                                       */
    /* ------------------------------------------------------------------ */
    html.dark {
        background-color: #0b1220 !important;
        color-scheme: dark;
    }

    html.dark body {
        background-color: #0b1220 !important;
        color: #e2e8f0 !important;
    }

    /* Permukaan / kartu */
    html.dark .bg-white { background-color: #111c2e !important; }
    html.dark .bg-slate-50 { background-color: #0f172a !important; }
    html.dark .bg-slate-50\/70 { background-color: #0f172a !important; }
    html.dark .bg-slate-50\/50 { background-color: #0f172a !important; }
    html.dark .bg-slate-50\/40 { background-color: #0f172a !important; }
    html.dark .bg-slate-100 { background-color: #1e293b !important; }
    html.dark .bg-slate-100\/70 { background-color: #1e293b !important; }
    html.dark .bg-slate-200 { background-color: #334155 !important; }
    html.dark .bg-slate-300 { background-color: #475569 !important; }
    html.dark .bg-\[\#f8fafc\] { background-color: #0b1220 !important; }
    html.dark .bg-\[\#f4f7fc\] { background-color: #0b1220 !important; }
    html.dark .bg-\[\#f1f4f9\] { background-color: #16233a !important; }
    html.dark .bg-\[\#f0f3fa\] { background-color: #16233a !important; }

    /* Warna aksen lembut (badge / kartu) */
    html.dark .bg-blue-50,
    html.dark .bg-blue-50\/70,
    html.dark .bg-blue-50\/50,
    html.dark .bg-blue-50\/20,
    html.dark .bg-blue-50\/10 { background-color: rgba(29, 99, 237, 0.16) !important; }
    html.dark .bg-blue-100,
    html.dark .bg-blue-100\/70 { background-color: rgba(29, 99, 237, 0.28) !important; }
    html.dark .bg-emerald-50,
    html.dark .bg-emerald-50\/70 { background-color: rgba(16, 185, 129, 0.16) !important; }
    html.dark .bg-emerald-100,
    html.dark .bg-emerald-100\/70 { background-color: rgba(16, 185, 129, 0.28) !important; }
    html.dark .bg-rose-50,
    html.dark .bg-rose-50\/70 { background-color: rgba(244, 63, 94, 0.16) !important; }
    html.dark .bg-rose-100,
    html.dark .bg-rose-100\/70 { background-color: rgba(244, 63, 94, 0.28) !important; }
    html.dark .bg-amber-50,
    html.dark .bg-amber-50\/70 { background-color: rgba(245, 158, 11, 0.16) !important; }
    html.dark .bg-amber-100,
    html.dark .bg-amber-100\/70 { background-color: rgba(245, 158, 11, 0.28) !important; }
    html.dark .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.16) !important; }
    html.dark .bg-indigo-100 { background-color: rgba(99, 102, 241, 0.28) !important; }
    html.dark .bg-purple-50 { background-color: rgba(139, 92, 246, 0.16) !important; }
    html.dark .bg-purple-100 { background-color: rgba(139, 92, 246, 0.28) !important; }
    html.dark .bg-teal-50 { background-color: rgba(20, 184, 166, 0.16) !important; }
    html.dark .bg-orange-50 { background-color: rgba(249, 115, 22, 0.16) !important; }

    /* Teks */
    html.dark .text-slate-900 { color: #f1f5f9 !important; }
    html.dark .text-slate-800 { color: #e2e8f0 !important; }
    html.dark .text-slate-700 { color: #cbd5e1 !important; }
    html.dark .text-slate-600 { color: #b6c2d4 !important; }
    html.dark .text-slate-500 { color: #94a3b8 !important; }
    html.dark .text-slate-400 { color: #7c8aa3 !important; }
    html.dark .text-slate-300 { color: #64748b !important; }
    html.dark .text-blue-500,
    html.dark .text-blue-600 { color: #60a5fa !important; }
    html.dark .text-blue-700,
    html.dark .text-blue-800 { color: #93c5fd !important; }
    html.dark .text-emerald-600 { color: #34d399 !important; }
    html.dark .text-emerald-700,
    html.dark .text-emerald-800 { color: #6ee7b7 !important; }
    html.dark .text-rose-500,
    html.dark .text-rose-600 { color: #fb7185 !important; }
    html.dark .text-rose-700,
    html.dark .text-rose-800 { color: #fda4af !important; }
    html.dark .text-amber-600 { color: #fbbf24 !important; }
    html.dark .text-amber-700,
    html.dark .text-amber-800 { color: #fcd34d !important; }
    html.dark .text-indigo-600,
    html.dark .text-indigo-700 { color: #a5b4fc !important; }
    html.dark .text-purple-600,
    html.dark .text-purple-700,
    html.dark .text-purple-800 { color: #c4b5fd !important; }
    html.dark .text-teal-700 { color: #5eead4 !important; }
    html.dark .text-orange-600 { color: #fdba74 !important; }

    /* Garis / border */
    html.dark .border-slate-50 { border-color: #16233a !important; }
    html.dark .border-slate-100 { border-color: #1e293b !important; }
    html.dark .border-slate-200,
    html.dark .border-slate-200\/90,
    html.dark .border-slate-200\/80,
    html.dark .border-slate-200\/70 { border-color: #2b3a52 !important; }
    html.dark .border-slate-300 { border-color: #3b4b66 !important; }
    html.dark .border-blue-100,
    html.dark .border-blue-100\/80,
    html.dark .border-blue-200,
    html.dark .border-blue-300 { border-color: rgba(96, 165, 250, 0.4) !important; }
    html.dark .border-emerald-100,
    html.dark .border-emerald-100\/80,
    html.dark .border-emerald-200 { border-color: rgba(52, 211, 153, 0.4) !important; }
    html.dark .border-rose-100\/80,
    html.dark .border-rose-200 { border-color: rgba(251, 113, 133, 0.4) !important; }
    html.dark .border-amber-100\/80,
    html.dark .border-amber-200 { border-color: rgba(251, 191, 36, 0.4) !important; }
    html.dark .border-indigo-200 { border-color: rgba(129, 140, 248, 0.4) !important; }
    html.dark .border-purple-200 { border-color: rgba(167, 139, 250, 0.4) !important; }
    html.dark .border-teal-200 { border-color: rgba(45, 212, 191, 0.4) !important; }

    /* Divider */
    html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]) { border-color: #1e293b !important; }

    /* Hover lembut */
    html.dark .hover\:bg-slate-50:hover,
    html.dark .hover\:bg-slate-100:hover { background-color: #1e293b !important; }
    html.dark .hover\:bg-blue-50:hover,
    html.dark .hover\:bg-blue-50\/50:hover,
    html.dark .hover\:bg-blue-100:hover { background-color: rgba(29, 99, 237, 0.28) !important; }
    html.dark .hover\:bg-rose-50:hover,
    html.dark .hover\:bg-rose-100:hover { background-color: rgba(244, 63, 94, 0.25) !important; }
    html.dark .hover\:bg-slate-900:hover { background-color: #0f172a !important; }

    /* Ring (outline) putih pada badge */
    html.dark .ring-white { --tw-ring-color: #111c2e !important; }
    html.dark .ring-blue-100 { --tw-ring-color: rgba(96, 165, 250, 0.35) !important; }

    /* Field form pada mode malam */
    html.dark input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]),
    html.dark select,
    html.dark textarea {
        border-color: #64748b !important;
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
    }
    html.dark input::placeholder,
    html.dark textarea::placeholder { color: #64748b !important; }
    html.dark input[type="file"]::file-selector-button {
        background-color: rgba(29, 99, 237, 0.25) !important;
        color: #93c5fd !important;
    }

    /* ------------------------------------------------------------------ */
    /* 2b. Pilihan aktif (radio/checkbox) selalu ter-highlight              */
    /*     Override mode malam di atas memakai !important sehingga menimpa  */
    /*     varian peer-checked Tailwind. Di sini kita paksa agar menang     */
    /*     supaya skor yang sudah dipilih tetap terlihat (mis. rubrik OMDD).*/
    /* ------------------------------------------------------------------ */
    .peer:checked ~ .peer-checked\:bg-blue-600,
    .peer:checked ~ .peer-checked\:text-white,
    .peer:checked ~ .peer-checked\:border-blue-600,
    html.dark .peer:checked ~ .peer-checked\:bg-blue-600,
    html.dark .peer:checked ~ .peer-checked\:text-white,
    html.dark .peer:checked ~ .peer-checked\:border-blue-600 {
        background-color: #1d63ed !important;
        border-color: #1d63ed !important;
        color: #ffffff !important;
    }

    .peer:checked ~ .peer-checked\:bg-emerald-50 {
        background-color: #ecfdf5 !important;
    }

    html.dark .peer:checked ~ .peer-checked\:bg-emerald-50 {
        background-color: rgba(16, 185, 129, 0.22) !important;
    }

    .peer:checked ~ .peer-checked\:border-emerald-500,
    html.dark .peer:checked ~ .peer-checked\:border-emerald-500 {
        border-color: #10b981 !important;
    }

    .peer:checked ~ .peer-checked\:bg-rose-50 {
        background-color: #fff1f2 !important;
    }

    html.dark .peer:checked ~ .peer-checked\:bg-rose-50 {
        background-color: rgba(244, 63, 94, 0.22) !important;
    }

    .peer:checked ~ .peer-checked\:border-rose-500,
    html.dark .peer:checked ~ .peer-checked\:border-rose-500 {
        border-color: #f43f5e !important;
    }

    /* ------------------------------------------------------------------ */
    /* 3. Ikon tombol mode terang/gelap                                    */
    /* ------------------------------------------------------------------ */
    [data-theme-icon="sun"] { display: none; }
    html.dark [data-theme-icon="sun"] { display: inline; }
    html.dark [data-theme-icon="moon"] { display: none; }
</style>
