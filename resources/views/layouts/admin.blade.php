<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
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
    </style>
</head>
<body class="bg-body-tertiary text-body">
    <!-- Top Header Navigation Bar -->
    <header class="navbar navbar-expand-lg border-bottom sticky-top bg-body shadow-sm">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-body-emphasis me-4" href="{{ route('admin.dashboard') }}">
                <div class="rounded-3 bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center">
                    <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
                </div>
                <span>ROI Attendance</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavbar">
                <form class="me-auto my-2 my-lg-0 col-12 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="search" class="form-control bg-body-tertiary border-start-0 text-body" placeholder="Search employees, reports, logs...">
                    </div>
                </form>

                <div class="d-flex align-items-center gap-3 ms-auto mt-2 mt-lg-0">
                    <!-- Dark/Light Theme Toggle -->
                    <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" id="themeToggleBtn" type="button" title="Toggle Dark/Light Mode" style="width: 36px; height: 36px;">
                        <i data-lucide="sun" id="themeIcon" style="width: 18px; height: 18px;"></i>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 position-relative d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" style="width: 36px; height: 36px;">
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
                        <button class="btn btn-body d-flex align-items-center gap-2 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown">
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

    <div class="container-fluid px-0">
        <div class="d-flex">
            <!-- Left Main Navigation Sidebar -->
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
            <main class="flex-grow-1 p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('roi_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();

        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const htmlElement = document.documentElement;
            const currentTheme = htmlElement.getAttribute('data-bs-theme') || 'dark';

            // Set initial icon
            const iconName = currentTheme === 'dark' ? 'sun' : 'moon';
            if (themeToggleBtn) {
                themeToggleBtn.innerHTML = `<i data-lucide="${iconName}" style="width: 18px; height: 18px;"></i>`;
            }

            if (window.lucide) {
                lucide.createIcons();
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const theme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                    htmlElement.setAttribute('data-bs-theme', theme);
                    localStorage.setItem('roi_theme', theme);
                    
                    const newIcon = theme === 'dark' ? 'sun' : 'moon';
                    themeToggleBtn.innerHTML = `<i data-lucide="${newIcon}" style="width: 18px; height: 18px;"></i>`;
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                });
            }
        });
    </script>
</body>
</html>
