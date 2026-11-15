{{-- Tombol ubah mode terang / gelap. Ikon dikontrol oleh CSS di partials/theme.blade.php --}}
<button type="button" onclick="window.tpsToggleTheme()"
        class="relative p-2 rounded-xl border border-slate-200/90 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition shadow-sm"
        aria-label="Ubah mode terang atau gelap" title="Mode terang / gelap">
    <svg data-theme-icon="moon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
    </svg>
    <svg data-theme-icon="sun" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
    </svg>
</button>
