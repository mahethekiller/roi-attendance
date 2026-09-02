<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Login</title>

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
        .login-wrapper {
            min-height: 100vh;
        }
        .brand-panel {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            position: relative;
            overflow: hidden;
        }
        .brand-panel::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(0,0,0,0) 70%);
            top: -100px;
            left: -100px;
            border-radius: 50%;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
        }
        .theme-toggle-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10;
        }
        .lucide {
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-body">
    <!-- Dark/Light Theme Toggle -->
    <button class="btn btn-outline-secondary rounded-circle theme-toggle-btn p-2 d-flex align-items-center justify-content-center" id="themeToggleBtn" type="button" title="Toggle Theme" style="width: 40px; height: 40px;">
        <i data-lucide="sun" id="themeIcon" style="width: 20px; height: 20px;"></i>
    </button>

    <div class="container-fluid p-0">
        <div class="row g-0 login-wrapper">
            <!-- Left Branding Panel -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5 brand-panel text-white">
                <div class="d-flex align-items-center gap-3 z-1">
                    <div class="rounded-3 bg-primary bg-opacity-25 p-2 border border-primary border-opacity-50 d-flex align-items-center justify-content-center">
                        <i data-lucide="shield-check" class="text-primary" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-white">ROI Attendance</h4>
                        <small class="text-white-50">Enterprise Admin Portal</small>
                    </div>
                </div>

                <div class="z-1 my-auto py-5">
                    <span class="badge bg-indigo bg-opacity-25 text-indigo border border-indigo border-opacity-25 mb-3 px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1">
                        <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i> Admin Management System
                    </span>
                    <h1 class="display-5 fw-bold mb-4">Streamline Attendance & HR Operations Effortlessly</h1>
                    <p class="lead text-white-50">Secure role-based access, real-time analytics, automated tracking, and intuitive management tools built for high performance.</p>

                    <div class="row g-3 mt-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 backdrop-blur">
                                <i data-lucide="lock" class="text-info mb-2 d-block" style="width: 24px; height: 24px;"></i>
                                <h6 class="fw-semibold text-white mb-1">Spatie RBAC</h6>
                                <small class="text-white-50">Strict permissions & roles</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 backdrop-blur">
                                <i data-lucide="gauge" class="text-warning mb-2 d-block" style="width: 24px; height: 24px;"></i>
                                <h6 class="fw-semibold text-white mb-1">Live Analytics</h6>
                                <small class="text-white-50">Real-time stats dashboard</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="z-1 d-flex justify-content-between align-items-center text-white-50 small">
                    <span>&copy; {{ date('Y') }} ROI Attendance. All rights reserved.</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">v1.0.0</span>
                </div>
            </div>

            <!-- Right Login Form Panel -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-sm-5 bg-body-tertiary">
                <div class="login-card">
                    <div class="mb-4 text-center text-lg-start">
                        <div class="d-lg-none mb-3 d-inline-flex align-items-center gap-2">
                            <i data-lucide="shield-check" class="text-primary" style="width: 32px; height: 32px;"></i>
                            <span class="fs-4 fw-bold text-body-emphasis">ROI Attendance</span>
                        </div>
                        <h2 class="fw-bold text-body-emphasis mb-1">Welcome Back</h2>
                        <p class="text-body-secondary">Enter your credentials to access the admin portal.</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
                            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="needs-validation">
                        @csrf

                        <!-- Email Input -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="emailInput" placeholder="name@example.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                            <label for="emailInput">Email Address</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="passwordInput" placeholder="Password" required autocomplete="current-password">
                            <label for="passwordInput">Password</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                <label class="form-check-label text-body-secondary small" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small fw-medium">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="log-in" style="width: 20px; height: 20px;"></i>
                            <span>Log In to Dashboard</span>
                        </button>

                        <div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-body-secondary small">
                            Default Admin Login: <strong class="text-body-emphasis">admin@example.com</strong> / <strong class="text-body-emphasis">password</strong>
                        </div>
                    </form>
                </div>
            </div>
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
                themeToggleBtn.innerHTML = `<i data-lucide="${iconName}" style="width: 20px; height: 20px;"></i>`;
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
                    themeToggleBtn.innerHTML = `<i data-lucide="${newIcon}" style="width: 20px; height: 20px;"></i>`;
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                });
            }
        });
    </script>
</body>
</html>
