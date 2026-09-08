<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'ROI Attendance') }} - Enterprise Biometric Platform</title>

    <!-- Pre-hydration theme script to prevent FOUC -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('roi_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Distinct Typography: Outfit (Sans) + JetBrains Mono (Code/Numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-100 text-base-content min-h-screen flex flex-col antialiased selection:bg-primary selection:text-white">

    <!-- Navigation Header -->
    <header class="navbar bg-base-100/90 backdrop-blur-md border-b border-base-200/60 sticky top-0 z-50 px-4 sm:px-8">
        <div class="navbar-start">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-base-content font-bold text-lg">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <span class="tracking-tight">ROI Attendance</span>
            </a>
        </div>

        <div class="navbar-end flex items-center gap-3">
            <!-- Theme Toggle Button -->
            <button class="btn btn-circle btn-ghost btn-sm sm:btn-md btn-outline border-base-content/20 hover:border-primary shadow-xs" id="themeToggleBtn" type="button" aria-label="Toggle Theme Mode" title="Toggle Theme">
                <i data-lucide="sun" id="themeIcon" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Admin Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Admin Login</span>
                    </a>
                @endauth
            @endif
        </div>
    </header>

    <!-- Main Landing Surface -->
    <main class="grow">
        <!-- Hero Section -->
        <section class="relative py-16 sm:py-24 px-4 sm:px-8 overflow-hidden bg-radial from-primary/10 via-base-100 to-base-100">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/15 border border-primary/30 text-primary text-xs font-semibold uppercase tracking-wider mb-6">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Enterprise Attendance & Biometric Infrastructure</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-base-content leading-tight mb-6">
                    Modern Biometric Tracking & Developer-First HR Platform
                </h1>

                <p class="text-base sm:text-lg text-base-content/70 max-w-2xl mx-auto leading-relaxed mb-8">
                    Effortlessly synchronize RFID cards, monitor biometric punch entries, manage Spatie RBAC permissions, and automate HR payroll feeds with secure REST APIs.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-md sm:btn-lg gap-2 shadow-xs">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            <span>Open Admin Dashboard</span>
                        </a>
                        <a href="{{ route('admin.api-docs.index') }}" class="btn btn-outline btn-md sm:btn-lg gap-2 shadow-xs">
                            <i data-lucide="book-open" class="w-5 h-5"></i>
                            <span>API Documentation</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-md sm:btn-lg gap-2 shadow-xs">
                            <i data-lucide="log-in" class="w-5 h-5"></i>
                            <span>Log In to Dashboard</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline btn-md sm:btn-lg gap-2 shadow-xs">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                            <span>Explore Features</span>
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="max-w-6xl mx-auto px-4 sm:px-8 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div class="card bg-base-100 border border-base-200/60 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                            <i data-lucide="credit-card" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-base-content text-base mb-1">RFID Card Mapping</h3>
                        <p class="text-xs text-base-content/70 leading-relaxed">
                            Link employee records to unique biometric badge and smart card IDs with instant validation.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="card bg-base-100 border border-base-200/60 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-success/10 text-success flex items-center justify-center mb-4">
                            <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-base-content text-base mb-1">Live Biometric Sync</h3>
                        <p class="text-xs text-base-content/70 leading-relaxed">
                            Automated cron schedules, manual dashboard triggers, and secure webhook synchronization.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="card bg-base-100 border border-base-200/60 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-info/10 text-info flex items-center justify-center mb-4">
                            <i data-lucide="code-2" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-base-content text-base mb-1">REST API & Tokens</h3>
                        <p class="text-xs text-base-content/70 leading-relaxed">
                            Token-based REST API with rate-limiting, live traffic audit logs, and downloadable specs.
                        </p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="card bg-base-100 border border-base-200/60 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-warning/10 text-warning flex items-center justify-center mb-4">
                            <i data-lucide="lock" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-base-content text-base mb-1">Spatie RBAC</h3>
                        <p class="text-xs text-base-content/70 leading-relaxed">
                            Enterprise role-based access control safeguarding administrative operations and audit trails.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-base-200/60 py-6 bg-base-100 text-xs text-base-content/60">
        <div class="max-w-6xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-center gap-3">
            <span>&copy; {{ date('Y') }} ROI Attendance. All rights reserved.</span>
            <div class="flex items-center gap-3">
                <span class="badge badge-neutral badge-soft font-mono">v2.0 daisyUI</span>
                <span>PHP 8.2 & Laravel 12</span>
            </div>
        </div>
    </footer>
</body>
</html>
