<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — TPS-G Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'] },
                    colors: { tps: { blue: '#1d63ed', dark: '#0f172a' } }
                }
            }
        }
    </script>
    @include('partials.theme')
</head>
<body class="h-full font-sans text-slate-800 antialiased">
    {{-- Theme toggle (mode terang / gelap) --}}
    <div class="fixed top-4 right-4 z-50">
        @include('partials.theme-toggle')
    </div>

    <div class="min-h-full grid lg:grid-cols-2">

        {{-- Branding panel --}}
        <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-[#0b2a6f] via-[#1d63ed] to-[#0e7490] p-12 text-white">
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-cyan-300/20 blur-3xl"></div>

            <div class="relative flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center ring-1 ring-white/30">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4"/><path d="M6.5 8h11"/><rect width="8" height="6" x="8" y="8" rx="1"/>
                        <path d="m8 14-2 6"/><path d="m16 14 2 6"/><path d="M5 20h14"/><circle cx="12" cy="11" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <p class="font-extrabold text-lg leading-tight">TPS-G Portal</p>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-white/70 font-semibold">OMDD Key Person Development</p>
                </div>
            </div>

            <div class="relative space-y-6">
                <h1 class="text-4xl font-extrabold leading-tight tracking-tight">
                    Satu portal untuk<br>perjalanan TPS Key Person.
                </h1>
                <p class="text-sm text-white/80 leading-relaxed max-w-md">
                    Kelola aktivitas Kaizen, workshop, penilaian OMDD, dan laporan hasil workshop
                    dalam satu alur kerja yang transparan untuk Karyawan, Admin, HRD, OMDD, dan Manager.
                </p>
                <div class="grid grid-cols-2 gap-3 max-w-md text-xs">
                    <div class="rounded-2xl bg-white/10 backdrop-blur p-4 ring-1 ring-white/15">
                        <p class="text-2xl font-extrabold">5</p>
                        <p class="text-white/70">Role terintegrasi</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur p-4 ring-1 ring-white/15">
                        <p class="text-2xl font-extrabold">5</p>
                        <p class="text-white/70">Tahap workshop</p>
                    </div>
                </div>
            </div>

            <p class="relative text-[11px] text-white/60">© {{ date('Y') }} Toyota Motor Manufacturing Indonesia — OMDD</p>
        </aside>

        {{-- Login form --}}
        <main class="flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-[#1d63ed] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4"/><path d="M6.5 8h11"/><rect width="8" height="6" x="8" y="8" rx="1"/>
                            <path d="m8 14-2 6"/><path d="m16 14 2 6"/><path d="M5 20h14"/><circle cx="12" cy="11" r="1" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 leading-tight">TPS-G Portal</p>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">OMDD Key Person</p>
                    </div>
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke portal</h2>
                <p class="text-sm text-slate-500 mt-1">Gunakan username dan password yang diberikan administrator.</p>

                @if (session('success'))
                    <div class="mt-5 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 text-xs font-semibold text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                                   autocomplete="username" placeholder="contoh: admin"
                                   class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••"
                                   class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-xs text-slate-600 select-none">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#1d63ed] hover:bg-blue-700 text-white text-sm font-bold shadow-lg shadow-blue-500/25 transition active:scale-[0.99]">
                        Masuk
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-8">
                    <div class="flex items-center gap-3 text-[11px] uppercase tracking-wider font-bold text-slate-400">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        Akun demo
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        @foreach ([
                            ['admin', 'Admin'],
                            ['hrd', 'HRD'],
                            ['omdd', 'OMDD'],
                            ['manager', 'Manager'],
                            ['karyawan', 'Karyawan'],
                        ] as [$username, $label])
                            <button type="button"
                                    onclick="document.getElementById('username').value='{{ $username }}';document.getElementById('password').value='password';"
                                    class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-left hover:border-blue-300 hover:bg-blue-50/50 transition {{ $loop->last ? 'col-span-2' : '' }}">
                                <span class="text-xs font-bold text-slate-700">{{ $label }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $username }}</span>
                            </button>
                        @endforeach
                    </div>
                    <p class="mt-3 text-center text-[11px] text-slate-400">Semua akun demo memakai password <span class="font-mono font-bold text-slate-600">password</span></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
