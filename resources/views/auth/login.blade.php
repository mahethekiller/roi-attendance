<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | ROI Mantra - Attendance & Workforce OS</title>
    <meta name="description" content="Secure administrative login portal for ROI Mantra workforce management, real-time biometric telemetry, and attendance logs.">
    <meta name="author" content="ROI Mantra">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:title" content="Login | ROI Mantra Attendance Portal">
    <meta property="og:description" content="Secure administrative login portal for ROI Mantra workforce management and attendance logs.">
    <meta property="og:image" content="https://www.roimantra.com/wp-content/uploads/2024/09/roi-logo.jpg">

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
<body class="bg-base-200/50 text-base-content min-h-screen flex flex-col justify-between antialiased selection:bg-primary selection:text-white relative overflow-x-hidden">

    <!-- Ambient Background Lighting & Grid Effects -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <!-- Top Center Glow -->
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-primary/15 rounded-full blur-3xl opacity-70"></div>
        <!-- Subtle Bottom Indigo Glow -->
        <div class="absolute -bottom-32 right-1/4 w-[450px] h-[300px] bg-indigo-500/10 rounded-full blur-3xl opacity-60"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="relative z-20 w-full px-6 py-4 flex items-center justify-between max-w-7xl mx-auto">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-base-content/70 hover:text-primary transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Portal</span>
        </a>

        <div class="flex items-center gap-3">
            <!-- Telemetry Indicator -->
            <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-base-100 border border-base-300 text-xs font-mono text-base-content/70 shadow-xs">
                <span class="telemetry-beacon">
                    <span class="telemetry-pulse"></span>
                    <span class="telemetry-dot"></span>
                </span>
                <span>System Operational</span>
            </div>

            <!-- Dark/Light Theme Toggle -->
            <button class="btn btn-circle btn-ghost btn-sm sm:btn-md btn-outline border-base-content/20 hover:border-primary shadow-xs" id="themeToggleBtn" type="button" aria-label="Toggle Theme Mode" title="Toggle Theme">
                <i data-lucide="sun" id="themeIcon" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>
        </div>
    </header>

    <!-- Main Authentication Content -->
    <main class="relative z-10 flex-grow flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-md">
            <!-- Login Card -->
            <div class="card bg-base-100/90 backdrop-blur-xl border border-base-300/80 dark:border-white/10 shadow-2xl rounded-3xl overflow-hidden">
                <div class="card-body p-6 sm:p-10">

                    <!-- Brand Logo -->
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="p-3.5 bg-white rounded-2xl shadow-md border border-slate-200/80 inline-flex items-center justify-center transition-all duration-300 hover:shadow-lg hover:scale-105 mb-4">
                            <img src="https://www.roimantra.com/wp-content/uploads/2024/09/roi-logo.jpg"
                                 alt="ROI Mantra Logo"
                                 class="h-10 sm:h-12 w-auto object-contain block"
                                 loading="eager">
                        </div>

                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-semibold uppercase tracking-wider mb-2">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Attendance & HR Portal</span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-base-content">
                            Welcome Back
                        </h1>
                        <p class="text-xs sm:text-sm text-base-content/70 mt-1 max-w-xs">
                            Sign in with your authorized agency credentials to access workforce telemetry.
                        </p>
                    </div>

                    <!-- Session Status Alerts -->
                    @if (session('status'))
                        <div class="alert alert-success shadow-xs mb-5 rounded-2xl" role="alert">
                            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                            <span class="text-sm font-medium">{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error shadow-xs mb-5 rounded-2xl" role="alert">
                            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                            <div class="text-sm">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                        @csrf

                        <!-- Email Input -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">
                                Email Address <span class="text-error">*</span>
                            </legend>
                            <label class="input input-bordered flex items-center gap-3 w-full rounded-xl @error('email') input-error @enderror focus-within:border-primary">
                                <i data-lucide="mail" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                <input type="email"
                                       name="email"
                                       id="emailInput"
                                       class="grow bg-transparent text-sm placeholder:text-base-content/40"
                                       placeholder="name@roimantra.com"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="username">
                            </label>
                        </fieldset>

                        <!-- Password Input -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">
                                Password <span class="text-error">*</span>
                            </legend>
                            <label class="input input-bordered flex items-center gap-3 w-full rounded-xl @error('password') input-error @enderror focus-within:border-primary relative">
                                <i data-lucide="lock" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                <input type="password"
                                       name="password"
                                       id="passwordInput"
                                       class="grow bg-transparent text-sm placeholder:text-base-content/40 pr-8"
                                       placeholder="••••••••"
                                       required
                                       autocomplete="current-password">
                                <button type="button"
                                        id="togglePasswordBtn"
                                        class="absolute right-3 text-base-content/50 hover:text-base-content focus:outline-none"
                                        aria-label="Toggle password visibility"
                                        title="Show/Hide password">
                                    <i data-lucide="eye" id="passwordEyeIcon" class="w-4 h-4"></i>
                                </button>
                            </label>
                        </fieldset>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember" id="rememberMe" class="checkbox checkbox-primary checkbox-sm rounded-md">
                                <span class="text-xs text-base-content/80 font-medium">Remember me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-primary font-semibold hover:underline">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-block rounded-xl gap-2 shadow-md hover:shadow-lg transition-all">
                                <span id="submitBtnSpinner" class="hidden">
                                    <svg class="animate-spin-smooth w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                    </svg>
                                </span>
                                <span id="submitBtnText">Sign In</span>
                                <i data-lucide="arrow-right" id="submitBtnIcon" class="w-4 h-4 shrink-0"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Trust / Security Callout -->
            <div class="flex items-center justify-center gap-4 text-xs text-base-content/50 mt-6">
                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-success"></i>
                    <span>Encrypted Session</span>
                </span>
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="fingerprint" class="w-3.5 h-3.5 text-primary"></i>
                    <span>Biometric Sync Ready</span>
                </span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full py-4 px-6 text-center text-xs text-base-content/60 border-t border-base-200 dark:border-base-300">
        <p>&copy; {{ date('Y') }} ROI Mantra. All rights reserved. &bull; Enterprise Workforce System</p>
    </footer>

    <!-- Interactive Helper Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Re-initialize lucide icons if needed
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Password visibility toggle
            const passwordInput = document.getElementById('passwordInput');
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordEyeIcon = document.getElementById('passwordEyeIcon');

            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';

                    if (window.lucide) {
                        passwordEyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
                        window.lucide.createIcons();
                    }
                });
            }

            // Button loading state on submit
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnSpinner = document.getElementById('submitBtnSpinner');
            const submitBtnIcon = document.getElementById('submitBtnIcon');
            const submitBtnText = document.getElementById('submitBtnText');

            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    submitBtn.classList.add('opacity-80', 'pointer-events-none');
                    if (submitBtnSpinner) submitBtnSpinner.classList.remove('hidden');
                    if (submitBtnIcon) submitBtnIcon.classList.add('hidden');
                    if (submitBtnText) submitBtnText.textContent = 'Signing In...';
                });
            }
        });
    </script>
</body>
</html>

