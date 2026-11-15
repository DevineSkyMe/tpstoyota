@props([
    'tag' => 'MODULE',
    'title' => 'Title',
    'subtitle' => 'Description',
    'gradient' => 'from-blue-500 via-blue-600 to-blue-700',
    'url' => '#',
])

<a href="{{ $url }}" {{ $attributes->merge(['class' => "relative block overflow-hidden rounded-3xl bg-gradient-to-br {$gradient} p-6 text-white shadow-xl hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group"]) }}>
    <!-- Top Bar -->
    <div class="flex items-center justify-between">
        <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-bold uppercase tracking-wider text-white">
            {{ $tag }}
        </span>
        <div class="w-7 h-7 rounded-full bg-white/20 group-hover:bg-white/30 backdrop-blur-sm flex items-center justify-center text-white transition">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="7" y1="17" x2="17" y2="7"/>
                <polyline points="7 7 17 7 17 17"/>
            </svg>
        </div>
    </div>

    <!-- Center Icon Circle -->
    <div class="my-6 flex justify-center">
        <div class="w-20 h-20 rounded-full bg-white shadow-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
            {{ $slot }}
        </div>
    </div>

    <!-- Title & Subtitle -->
    <div class="text-center space-y-1">
        <h3 class="text-xl font-bold tracking-tight text-white">{{ $title }}</h3>
        <p class="text-xs text-white/80 font-medium">{{ $subtitle }}</p>
    </div>
</a>

