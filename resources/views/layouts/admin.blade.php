<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

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
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
        .font-monospace, code, pre, .font-mono, .badge.fw-mono {
            font-family: var(--font-mono) !important;
            letter-spacing: -0.01em;
        }
        .table td, .table th, .stat-card h3, .badge {
            font-variant-numeric: tabular-nums;
        }
        .admin-sidebar {
            width: 260px;
            min-height: calc(100vh - 65px);
            transition: all 0.3s ease;
        }
        .nav-link.active {
            font-weight: 600;
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .lucide {
            vertical-align: middle;
        }
        @media (prefers-reduced-motion: reduce) {
            .stat-card,
            .admin-sidebar,
            .btn,
            .nav-link {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="bg-body-tertiary text-body">
    <!-- Top Header Navigation Bar -->
    <header class="navbar navbar-expand-lg border-bottom sticky-top bg-body shadow-sm">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex align-items-center gap-2">
                <!-- Mobile Navigation Drawer Toggle -->
                <button class="btn btn-outline-secondary btn-sm d-md-none p-2 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileDrawer" aria-controls="adminMobileDrawer" aria-label="Open Navigation Menu" style="width: 36px; height: 36px;">
                    <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
                </button>

                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-body-emphasis me-2 me-lg-4" href="{{ route('admin.dashboard') }}">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center">
                        <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
                    </div>
                    <span class="d-none d-sm-inline">ROI Attendance</span>
                </a>
            </div>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-label="Toggle navigation search">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavbar">
                <form class="me-auto my-2 my-lg-0 col-12 col-lg-5" role="search">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="search" id="globalSearchInput" class="form-control bg-body-tertiary border-start-0 border-end-0 text-body" placeholder="Search employees, reports, logs..." aria-label="Search employees, reports, and logs">
                        <span class="input-group-text bg-body-tertiary border-start-0 text-body-secondary d-none d-sm-flex">
                            <kbd class="bg-body text-body-secondary px-1.5 py-0.5 rounded border small" style="font-size: 0.7rem;">Ctrl K</kbd>
                        </span>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-3 ms-auto mt-2 mt-lg-0">
                    <!-- Dark/Light Theme Toggle -->
                    <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" id="themeToggleBtn" type="button" aria-label="Toggle Theme Mode" title="Toggle Dark/Light Mode" style="width: 36px; height: 36px;">
                        <i data-lucide="sun" id="themeIcon" style="width: 18px; height: 18px;"></i>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 position-relative d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-label="View notifications" aria-expanded="false" style="width: 36px; height: 36px;">
                            <i data-lucide="bell" style="width: 18px; height: 18px;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="width: 280px;">
                            <li><h6 class="dropdown-header text-uppercase fw-bold">Notifications</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item rounded-2 py-2 small" href="#">John Doe logged clock-in at 09:00 AM</a></li>
                            <li><a class="dropdown-item rounded-2 py-2 small" href="#">New attendance exception flag</a></li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-body d-flex align-items-center gap-2 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                            </div>
                            <div class="text-start d-none d-sm-block">
                                <div class="fw-semibold small text-body-emphasis mb-0">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="text-body-secondary" style="font-size: 0.75rem;">
                                    {{ Auth::user()->getRoleNames()->first() ?? 'Super Admin' }}
                                </div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Offcanvas Sidebar Drawer -->
    <div class="offcanvas offcanvas-start bg-body text-body" tabindex="-1" id="adminMobileDrawer" aria-labelledby="adminMobileDrawerLabel">
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2" id="adminMobileDrawerLabel">
                <div class="rounded-3 bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center">
                    <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="fw-bold text-body-emphasis">ROI Attendance</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            <div class="text-uppercase small fw-bold text-body-secondary mb-3 px-2">Main Menu</div>
            <ul class="nav nav-pills flex-column gap-1 mb-4">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="contact-2" style="width: 18px; height: 18px;"></i>
                        <span>Employee Directory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                        <span>User Accounts</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.attendances.index') }}" class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="calendar-check" style="width: 18px; height: 18px;"></i>
                        <span>Attendance Logs</span>
                    </a>
                </li>
            </ul>

            <div class="text-uppercase small fw-bold text-body-secondary mb-3 px-2">Developer & API</div>
            <ul class="nav nav-pills flex-column gap-1 mb-4">
                <li class="nav-item">
                    <a href="{{ route('admin.api-docs.index') }}" class="nav-link {{ request()->routeIs('admin.api-docs.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="book-open" style="width: 18px; height: 18px;"></i>
                        <span>API Documentation</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.api-tokens.index') }}" class="nav-link {{ request()->routeIs('admin.api-tokens.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="key" style="width: 18px; height: 18px;"></i>
                        <span>API Access Tokens</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.api-logs.index') }}" class="nav-link {{ request()->routeIs('admin.api-logs.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
                        <span>API Traffic Logs</span>
                    </a>
                </li>
            </ul>

            <div class="text-uppercase small fw-bold text-body-secondary mb-3 px-2">Administration</div>
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a href="{{ route('admin.sync-logs.index') }}" class="nav-link {{ request()->routeIs('admin.sync-logs.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i data-lucide="history" style="width: 18px; height: 18px;"></i>
                        <span>Sync History Logs</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="d-flex">
            <!-- Left Main Navigation Sidebar (Desktop) -->
            <aside class="admin-sidebar bg-body border-end d-none d-md-block p-3">
                <div class="text-uppercase small fw-bold text-body-secondary mb-3 px-3">Main Menu</div>
                <ul class="nav nav-pills flex-column gap-1">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="contact-2" style="width: 18px; height: 18px;"></i>
                            <span>Employee Directory</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                            <span>User Accounts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.attendances.index') }}" class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="calendar-check" style="width: 18px; height: 18px;"></i>
                            <span>Attendance Logs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-body-secondary rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="bar-chart-3" style="width: 18px; height: 18px;"></i>
                            <span>Reports & Analytics</span>
                        </a>
                    </li>
                </ul>

                <div class="text-uppercase small fw-bold text-body-secondary mt-4 mb-3 px-3">Developer & API</div>
                <ul class="nav nav-pills flex-column gap-1">
                    <li class="nav-item">
                        <a href="{{ route('admin.api-docs.index') }}" class="nav-link {{ request()->routeIs('admin.api-docs.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="book-open" style="width: 18px; height: 18px;"></i>
                            <span>API Documentation</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.api-tokens.index') }}" class="nav-link {{ request()->routeIs('admin.api-tokens.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="key" style="width: 18px; height: 18px;"></i>
                            <span>API Access Tokens</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.api-logs.index') }}" class="nav-link {{ request()->routeIs('admin.api-logs.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
                            <span>API Traffic Logs</span>
                        </a>
                    </li>
                </ul>

                <div class="text-uppercase small fw-bold text-body-secondary mt-4 mb-3 px-3">Administration</div>
                <ul class="nav nav-pills flex-column gap-1">
                    <li class="nav-item">
                        <a href="{{ route('admin.sync-logs.index') }}" class="nav-link {{ request()->routeIs('admin.sync-logs.*') ? 'active bg-primary text-white' : 'text-body-secondary' }} rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="history" style="width: 18px; height: 18px;"></i>
                            <span>Sync History Logs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-body-secondary rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="shield-alert" style="width: 18px; height: 18px;"></i>
                            <span>Roles & Spatie RBAC</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-body-secondary rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                            <i data-lucide="settings" style="width: 18px; height: 18px;"></i>
                            <span>System Settings</span>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Middle Main Content Area -->
            <main class="flex-grow-1 p-3 p-md-4">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
