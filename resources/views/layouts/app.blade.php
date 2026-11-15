<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TPS Portal — OMDD Key Person Development')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN for instant, zero-build rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        tps: {
                            blue: '#1d63ed',
                            dark: '#0f172a',
                            subtle: '#f1f4f9',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @include('partials.theme')
</head>
<body class="h-full bg-[#f4f7fc] text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="min-h-full flex flex-row">

        <!-- Sidebar Component -->
        @include('partials.sidebar')

        <!-- Main Content Area Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Navbar Component -->
            @include('partials.navbar')

            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6 lg:p-8 space-y-6">
                @include('partials.flash')

                @yield('content')
            </main>

            <!-- Optional Footer Component -->
            @include('partials.footer')

        </div>

    </div>

    @stack('scripts')
</body>
</html>

