<!-- Role-aware Feature Cards Grid Component -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @php
        $icons = [
            'chart' => '<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>',
            'medal' => '<circle cx="12" cy="9" r="6"/><path d="m9 14-2 7 5-3 5 3-2-7"/><path d="m9 9 2 2 4-4"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
            'rocket' => '<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>',
            'clipboard' => '<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'profile' => '<circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/>',
            'userSearch' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><circle cx="17" cy="11" r="3"/><path d="m19 13 2 2"/>',
            'report' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        ];
    @endphp

    @foreach ($cards as $card)
        <a href="{{ $card['href'] }}"
           class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $card['gradient'] }} p-6 text-white shadow-xl {{ $card['shadow'] }} hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-bold uppercase tracking-wider text-white">
                    {{ $card['badge'] }}
                </span>
                <div class="w-7 h-7 rounded-full bg-white/20 group-hover:bg-white/30 backdrop-blur-sm flex items-center justify-center text-white transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"/>
                        <polyline points="7 7 17 7 17 17"/>
                    </svg>
                </div>
            </div>

            <div class="my-6 flex justify-center">
                <div class="w-20 h-20 rounded-full bg-white shadow-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-9 h-9 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        {!! $icons[$card['icon']] ?? $icons['chart'] !!}
                    </svg>
                </div>
            </div>

            <div class="text-center space-y-1">
                <h3 class="text-xl font-bold tracking-tight text-white">{{ $card['title'] }}</h3>
                <p class="text-xs text-white/80 font-medium">{{ $card['subtitle'] }}</p>
                <p class="pt-2 text-[11px] font-bold text-white/90 uppercase tracking-wider">{{ $card['stat'] }}</p>
            </div>
        </a>
    @endforeach
</section>
