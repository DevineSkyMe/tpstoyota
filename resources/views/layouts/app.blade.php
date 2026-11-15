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
        html {
            height: 100%;
        }
        body {
            min-height: 100%;
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

        /* ------------------------------------------------------------- */
        /* Print Stylesheet: Bersih, Tanpa Navbar, Elegan, Nyaman Dibaca */
        /* ------------------------------------------------------------- */
        @media print {
            html, body {
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Sembunyikan semua elemen navigasi dan tombol saat cetak */
            #portal-sidebar,
            header,
            footer,
            nav,
            .no-print {
                display: none !important;
            }

            .min-h-full,
            .flex-row,
            .flex-col,
            .flex-1 {
                display: block !important;
                width: 100% !important;
                min-height: 0 !important;
                height: auto !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
                height: auto !important;
                width: 100% !important;
            }
        }
    </style>
    @include('partials.theme')
</head>
<body class="h-full bg-[#f4f7fc] text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
<body class="h-full overflow-hidden bg-[#f4f7fc] text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="flex h-screen overflow-hidden">
    <div class="flex h-full w-full overflow-hidden">

        <!-- Sidebar Component -->
        @include('partials.sidebar')

        <!-- Main Content Area Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <div class="flex-1 flex flex-col h-full min-w-0 min-h-0 overflow-hidden">

            <!-- Navbar Component -->
            @include('partials.navbar')

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
            <main class="flex-1 overflow-y-auto min-h-0 p-4 md:p-6 lg:p-8 space-y-6">
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

