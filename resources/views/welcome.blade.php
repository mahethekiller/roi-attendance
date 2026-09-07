<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'ROI Attendance') }} - Enterprise Biometric Platform</title>

    <!-- Pre-hydration theme script to prevent FOUC -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('roi_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Distinct Typography: Outfit (Sans) + JetBrains Mono (Code/Numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])

    <style>
        :root {
            --font-sans: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        body {
            font-family: var(--font-sans);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
        }
        h1, h2, h3, h4, h5, h6 {
            letter-spacing: -0.025em;
        }
        .hero-gradient {
            background: radial-gradient(circle at 50% 10%, rgba(99, 102, 241, 0.15), transparent 70%);
        }
        .feature-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
        }
        @media (prefers-reduced-motion: reduce) {
            .feature-card, .btn {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="bg-body text-body d-flex flex-column min-h-screen">
    <!-- Navigation Header -->
    <header class="border-bottom bg-body sticky-top shadow-sm py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none text-body-emphasis fw-bold fs-5">
                <div class="rounded-3 bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center">
                    <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                </div>
                <span>ROI Attendance</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <!-- Theme Toggle Button -->
                <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" id="themeToggleBtn" type="button" aria-label="Toggle Theme Mode" title="Toggle Theme" style="width: 38px; height: 38px;">
                    <i data-lucide="sun" id="themeIcon" style="width: 18px; height: 18px;"></i>
                </button>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                            <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                            <span>Admin Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4 shadow-sm d-flex align-items-center gap-2">
                            <i data-lucide="log-in" style="width: 16px; height: 16px;"></i>
                            <span>Admin Login</span>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Main Landing Surface -->
    <main class="flex-grow-1 hero-gradient py-5">
        <div class="container py-lg-5">
            <!-- Hero Title & Lead -->
            <div class="text-center max-w-3xl mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3 d-inline-flex align-items-center gap-2">
                    <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i>
                    <span>Enterprise Attendance & Biometric Infrastructure</span>
                </span>
                <h1 class="display-4 fw-bold text-body-emphasis mb-3">
                    Modern Biometric Tracking & Developer-First HR Platform
                </h1>
                <p class="lead text-body-secondary mb-4 mx-auto" style="max-width: 700px;">
                    Effortlessly synchronize RFID cards, monitor biometric punch entries, manage Spatie RBAC permissions, and automate HR payroll feeds with secure REST APIs.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                            <i data-lucide="layout-dashboard" style="width: 20px; height: 20px;"></i>
                            <span>Open Admin Dashboard</span>
                        </a>
                        <a href="{{ route('admin.api-docs.index') }}" class="btn btn-outline-secondary btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                            <i data-lucide="book-open" style="width: 20px; height: 20px;"></i>
                            <span>API Documentation</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                            <i data-lucide="log-in" style="width: 20px; height: 20px;"></i>
                            <span>Log In to Dashboard</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                            <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                            <span>Explore Features</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Features Grid -->
            <div class="row g-4 mt-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-body text-body h-100 p-3 feature-card">
                        <div class="card-body">
                            <div class="rounded-circle bg-primary-subtle text-primary p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i data-lucide="credit-card" style="width: 24px; height: 24px;"></i>
                            </div>
                            <h5 class="fw-bold text-body-emphasis mb-2">RFID Card Mapping</h5>
                            <p class="text-body-secondary small mb-0">
                                Link employee records to unique biometric badge and smart card IDs with instant validation.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-body text-body h-100 p-3 feature-card">
                        <div class="card-body">
                            <div class="rounded-circle bg-success-subtle text-success p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i data-lucide="refresh-cw" style="width: 24px; height: 24px;"></i>
                            </div>
                            <h5 class="fw-bold text-body-emphasis mb-2">Live Biometric Sync</h5>
                            <p class="text-body-secondary small mb-0">
                                Automated cron schedules, manual dashboard triggers, and secure webhook synchronization.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-body text-body h-100 p-3 feature-card">
                        <div class="card-body">
                            <div class="rounded-circle bg-info-subtle text-info p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i data-lucide="code-2" style="width: 24px; height: 24px;"></i>
                            </div>
                            <h5 class="fw-bold text-body-emphasis mb-2">REST API & Tokens</h5>
                            <p class="text-body-secondary small mb-0">
                                Token-based REST API with rate-limiting, live traffic audit logs, and downloadable specs.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-body text-body h-100 p-3 feature-card">
                        <div class="card-body">
                            <div class="rounded-circle bg-warning-subtle text-warning p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i data-lucide="lock" style="width: 24px; height: 24px;"></i>
                            </div>
                            <h5 class="fw-bold text-body-emphasis mb-2">Spatie RBAC</h5>
                            <p class="text-body-secondary small mb-0">
                                Enterprise role-based access control safeguarding administrative operations and audit trails.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-top py-4 bg-body text-body-secondary small mt-auto">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} ROI Attendance. All rights reserved.</span>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-body-tertiary text-body border">Version 1.0.0</span>
                <span>PHP 8.2 & Laravel 12</span>
            </div>
        </div>
    </footer>
</body>
</html>
