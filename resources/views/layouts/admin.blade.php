<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

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
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 text-base-content min-h-screen">
    <div class="drawer lg:drawer-open min-h-screen">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle" />
        
        <!-- Drawer Content (Main Area + Navbar) -->
        <div class="drawer-content flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="navbar bg-base-100 border-b border-base-200 sticky top-0 z-30 px-4 gap-2 shadow-xs">
                <!-- Mobile Drawer Toggle -->
                <div class="flex-none lg:hidden">
                    <label for="admin-drawer" class="btn btn-ghost btn-square btn-sm" aria-label="Open Navigation Menu">
                        <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
                    </label>
                </div>

                <!-- Brand Logo (Mobile visible) -->
                <div class="flex-1 flex items-center gap-2">
                    <a class="flex items-center gap-2 font-bold text-base-content lg:hidden" href="{{ route('admin.dashboard') }}">
                        <div class="rounded-lg bg-primary/10 p-1.5 text-primary">
                            <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                        </div>
                        <span class="text-sm font-semibold">ROI Attendance</span>
                    </a>

                    <!-- Global Search Bar -->
                    <div class="hidden sm:flex items-center w-full max-w-md ml-2">
                        <label class="input input-sm input-bordered flex items-center gap-2 w-full bg-base-200/50">
                            <i data-lucide="search" style="width: 16px; height: 16px;" class="text-base-content/50"></i>
                            <input type="search" id="globalSearchInput" class="grow" placeholder="Search employees, logs, cards... (Ctrl+K)" aria-label="Search employees, reports, and logs" />
                            <kbd class="kbd kbd-xs font-mono opacity-60">Ctrl K</kbd>
                        </label>
                    </div>
                </div>

                <!-- Right Action Icons -->
                <div class="flex-none flex items-center gap-2">
                    <!-- Theme Mode Controller Button -->
                    <button class="btn btn-ghost btn-circle btn-sm" id="themeToggleBtn" type="button" aria-label="Toggle Theme Mode" title="Toggle Dark/Light Mode">
                        <i data-lucide="sun" style="width: 18px; height: 18px;"></i>
                    </button>


                    <!-- User Profile Dropdown -->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm gap-2 pl-2 pr-3">
                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-7 h-7 text-xs font-bold flex items-center justify-center">
                                    {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                                </div>
                            </div>
                            <div class="text-left hidden md:block leading-tight">
                                <div class="font-semibold text-xs text-base-content">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="text-[10px] text-base-content/60">{{ Auth::user()->getRoleNames()->first() ?? 'Super Admin' }}</div>
                            </div>
                            <i data-lucide="chevron-down" style="width: 14px; height: 14px;" class="opacity-50"></i>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 w-52 p-2 shadow-lg border border-base-200 mt-2">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2">
                                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li class="border-t border-base-200 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="text-error flex items-center gap-2 w-full text-left">
                                        <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Dynamic Page Content Area -->
            <main class="p-4 md:p-6 flex-1 min-w-0">
                {{ $slot }}
            </main>
        </div>

        <!-- Drawer Side (Left Sidebar Navigation) -->
        <div class="drawer-side z-40">
            <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="w-64 min-h-full bg-base-100 border-r border-base-200 p-4 flex flex-col justify-between">
                <div>
                    <!-- Brand Banner -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 mb-4 border-b border-base-200 pb-4">
                        <div class="rounded-xl bg-primary/10 text-primary p-2 flex items-center justify-center">
                            <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <div class="font-bold text-base text-base-content leading-tight">ROI Attendance</div>
                            <div class="text-[11px] text-base-content/60 font-mono">Biometric Intelligence</div>
                        </div>
                    </a>

                    <!-- Sidebar Navigation Menus -->
                    <ul class="menu menu-sm gap-1 w-full p-0">
                        <li class="menu-title text-[11px] font-bold uppercase tracking-wider text-base-content/50 px-3">Main Menu</li>
                        <x-admin-nav-item route="admin.dashboard" icon="layout-dashboard" label="Dashboard" permission="dashboard.view" />
                        <x-admin-nav-item route="admin.employees.index" activePattern="admin.employees.*" icon="contact-2" label="Employee Directory" permission="employees.view" />
                        <x-admin-nav-item route="admin.users.index" activePattern="admin.users.*" icon="users" label="User Accounts" permission="users.view" />
                        <x-admin-nav-item route="admin.attendances.index" activePattern="admin.attendances.*" icon="calendar-check" label="Attendance Logs" permission="attendances.view" />
                        <x-admin-nav-item url="#" icon="bar-chart-3" label="Reports & Analytics" permission="reports.view" />

                        <x-authorized :permission="['api.docs.view', 'api.tokens.manage', 'api.logs.view']">
                            <li class="menu-title text-[11px] font-bold uppercase tracking-wider text-base-content/50 mt-4 px-3">Developer & API</li>
                            <x-admin-nav-item route="admin.api-docs.index" activePattern="admin.api-docs.*" icon="book-open" label="API Documentation" permission="api.docs.view" />
                            <x-admin-nav-item route="admin.api-tokens.index" activePattern="admin.api-tokens.*" icon="key" label="API Access Tokens" permission="api.tokens.manage" />
                            <x-admin-nav-item route="admin.api-logs.index" activePattern="admin.api-logs.*" icon="activity" label="API Traffic Logs" permission="api.logs.view" />
                        </x-authorized>

                        <x-authorized :permission="['sync-logs.view', 'attendance.overrides.manage', 'roles.manage', 'settings.manage']">
                            <li class="menu-title text-[11px] font-bold uppercase tracking-wider text-base-content/50 mt-4 px-3">Administration</li>
                            <x-admin-nav-item route="admin.sync-logs.index" activePattern="admin.sync-logs.*" icon="history" label="Sync History Logs" permission="sync-logs.view" />
                            <x-admin-nav-item route="admin.attendance-overrides.index" activePattern="admin.attendance-overrides.*" icon="sliders" label="Attendance Overrides" permission="attendance.overrides.manage" />
                            <x-admin-nav-item url="#" icon="shield-alert" label="Roles & Spatie RBAC" permission="roles.manage" />
                            <x-admin-nav-item url="#" icon="settings" label="System Settings" permission="settings.manage" />
                        </x-authorized>
                    </ul>
                </div>

                <!-- Bottom Version Tag -->
                <div class="px-3 py-2 border-t border-base-200 text-xs text-base-content/50 flex items-center justify-between font-mono">
                    <span>v2.1.0</span>
                    <span class="flex items-center gap-1.5 text-success">
                        <span class="telemetry-beacon" style="width:6px;height:6px;">
                            <span class="telemetry-pulse" style="background-color:#10B981;"></span>
                            <span class="telemetry-dot" style="width:6px;height:6px;background-color:#10B981;"></span>
                        </span>
                        Sync Ready
                    </span>
                </div>
            </aside>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
