<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ROI Attendance') }}</title>
        <meta name="description" content="ROI Mantra enterprise biometric attendance management system and workforce platform.">
        <meta name="author" content="ROI Mantra">
        <meta name="robots" content="noindex, nofollow">

        <!-- Pre-hydration theme script -->
        <script>
            (function() {
                const savedTheme = localStorage.getItem('roi_theme') || 'dark';
                document.documentElement.setAttribute('data-theme', savedTheme);
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-base-content antialiased bg-base-200/50 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div>
                <a href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 p-6 sm:p-8 bg-base-100 border border-base-200/60 shadow-xs rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
