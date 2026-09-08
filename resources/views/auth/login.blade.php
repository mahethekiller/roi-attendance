<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Login</title>

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
<body class="bg-base-100 text-base-content min-h-screen antialiased selection:bg-primary selection:text-white">

    <!-- Dark/Light Theme Toggle -->
    <div class="fixed top-5 right-5 z-50">
        <button class="btn btn-circle btn-ghost btn-outline border-base-content/20 hover:border-primary shadow-xs" id="themeToggleBtn" type="button" aria-label="Toggle Theme Mode" title="Toggle Theme">
            <i data-lucide="sun" id="themeIcon" class="w-5 h-5"></i>
        </button>
    </div>

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
        <!-- Left Branding Hero Panel (Desktop) -->
        <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 flex-col justify-between p-12 bg-linear-to-br from-slate-950 via-indigo-950 to-blue-950 text-white relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-primary/20 border border-primary/40 flex items-center justify-center text-primary shadow-inner">
                    <i data-lucide="shield-check" class="w-7 h-7 text-primary"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-white">ROI Attendance</h2>
                    <p class="text-xs text-indigo-200/70 font-mono">Enterprise Workforce OS</p>
                </div>
            </div>

            <!-- Hero Body Content -->
            <div class="my-auto py-12 relative z-10 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/20 border border-primary/40 text-primary-content text-xs font-semibold uppercase tracking-wider mb-6">
                    <i data-lucide="sparkles" class="w-4 h-4 text-primary"></i>
                    <span>Next-Gen Attendance Infrastructure</span>
                </div>
                <h1 class="text-4xl xl:text-5xl font-extrabold tracking-tight text-white leading-tight mb-4">
                    Streamline Attendance & HR Operations Effortlessly
                </h1>
                <p class="text-base text-slate-300/80 leading-relaxed mb-8">
                    Secure role-based access, real-time biometric telemetry, automated sync pipelines, and high-frequency analytical dashboards.
                </p>

                <!-- Value Props Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
                        <i data-lucide="lock" class="w-6 h-6 text-sky-400 mb-2"></i>
                        <h4 class="font-bold text-white text-sm">Spatie RBAC</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Role-guarded endpoint security</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
                        <i data-lucide="gauge" class="w-6 h-6 text-amber-400 mb-2"></i>
                        <h4 class="font-bold text-white text-sm">Live Telemetry</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Sub-second punch monitoring</p>
                    </div>
                </div>
            </div>

            <!-- Brand Footer -->
            <div class="flex items-center justify-between text-xs text-slate-400 relative z-10 pt-6 border-t border-white/10">
                <span>&copy; {{ date('Y') }} ROI Attendance. All rights reserved.</span>
                <span class="badge badge-success badge-soft font-mono">v2.0 daisyUI</span>
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div class="col-span-1 lg:col-span-6 xl:col-span-5 flex items-center justify-center p-6 sm:p-12 bg-base-100">
            <div class="w-full max-w-md">
                <!-- Mobile Brand Header -->
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-base-content">ROI Attendance</h2>
                        <p class="text-xs text-base-content/60">Enterprise Admin Portal</p>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-base-content tracking-tight">Welcome Back</h2>
                    <p class="text-sm text-base-content/70 mt-1">Enter your credentials to access the admin portal.</p>
                </div>

                <!-- Session Status Alerts -->
                @if (session('status'))
                    <div class="alert alert-success shadow-xs mb-6" role="alert">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                        <span class="text-sm">{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error shadow-xs mb-6" role="alert">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                        <div class="text-sm">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Email Address <span class="text-error">*</span></legend>
                        <label class="input input-bordered flex items-center gap-2 w-full @error('email') input-error @enderror">
                            <i data-lucide="mail" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                            <input type="email" name="email" id="emailInput" class="grow bg-transparent" placeholder="name@example.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                        </label>
                    </fieldset>

                    <!-- Password Input -->
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Password <span class="text-error">*</span></legend>
                        <label class="input input-bordered flex items-center gap-2 w-full @error('password') input-error @enderror">
                            <i data-lucide="lock" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                            <input type="password" name="password" id="passwordInput" class="grow bg-transparent" placeholder="••••••••" required autocomplete="current-password">
                        </label>
                    </fieldset>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" id="rememberMe" class="checkbox checkbox-primary checkbox-sm">
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
                        <button type="submit" class="btn btn-primary btn-block gap-2 shadow-xs">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span>Log In to Dashboard</span>
                        </button>
                    </div>

                    <div class="pt-4 mt-6 border-t border-base-200 text-center text-xs text-base-content/60">
                        Default Admin Login: <strong class="text-base-content">admin@example.com</strong> / <strong class="text-base-content">password</strong>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
