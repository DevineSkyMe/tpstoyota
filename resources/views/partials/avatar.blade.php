@php
    // Reusable employee/account avatar.
    //
    // Shows the real photo when one is available (an absolute URL, or a path
    // relative to /public such as "uploads/photos/x.jpg"), and quietly falls
    // back to the person's initials when the photo is missing or fails to load.
    //
    // Expected variables:
    //   $photo      (string|null) stored photo URL/path
    //   $name       (string)      full name used for the initials fallback
    //   $size       (string)      Tailwind sizing classes, e.g. "w-9 h-9 text-xs"
    //   $rounded    (string)      Tailwind radius classes, default "rounded-full"
    //   $bgClass    (string)      Tailwind background classes, default blue
    //   $textClass  (string)      Tailwind text colour classes, default white
    //   $extraClass (string)      any extra classes (ring, shadow, ...)
    $avatarPhoto = isset($photo) && is_string($photo) ? trim($photo) : '';
    $avatarName = trim((string) ($name ?? ''));

    $avatarUrl = null;

    if ($avatarPhoto !== '') {
        $avatarUrl = \Illuminate\Support\Str::startsWith($avatarPhoto, ['http://', 'https://', '//'])
            ? $avatarPhoto
            : asset(ltrim($avatarPhoto, '/'));
    }

    $parts = array_values(array_filter(
        preg_split('/\s+/', $avatarName) ?: [],
        fn ($part) => $part !== '' && preg_match('/^(ii|iii|iv|jr|sr)\.?$/i', $part) !== 1
    ));

    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1))
        : strtoupper(substr($parts[0] ?? '?', 0, 2));
@endphp

<span class="relative inline-flex items-center justify-center overflow-hidden font-black uppercase shrink-0 {{ $size ?? 'w-9 h-9 text-xs' }} {{ $rounded ?? 'rounded-full' }} {{ $bgClass ?? 'bg-[#1d63ed]' }} {{ $textClass ?? 'text-white' }} {{ $extraClass ?? '' }}">
    <span aria-hidden="true">{{ $initials }}</span>
    @if ($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="{{ $avatarName }}" loading="lazy"
             class="absolute inset-0 h-full w-full object-cover"
             onerror="this.style.display='none'">
    @endif
</span>
