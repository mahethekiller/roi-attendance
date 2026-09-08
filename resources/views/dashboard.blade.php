<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-base-content tracking-tight">User Workspace</h1>
        <p class="text-sm text-base-content/70 mt-0.5">Welcome to ROI Attendance, {{ Auth::user()->name }}.</p>
    </div>

    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-6 sm:p-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-success/10 text-success flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-base-content">Successfully Authenticated</h3>
                    <span class="text-xs text-base-content/60">Logged in as {{ Auth::user()->email }}</span>
                </div>
            </div>

            <p class="text-sm text-base-content/70 mb-6">
                You are currently signed in with role <span class="badge badge-primary badge-soft font-medium">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</span>.
            </p>

            <div class="flex flex-wrap items-center gap-3">
                @hasanyrole('super-admin|admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary gap-2 shadow-xs">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Go to Admin Dashboard</span>
                    </a>
                @endhasanyrole
                <a href="{{ route('profile.edit') }}" class="btn btn-outline gap-2 shadow-xs">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span>Manage Account Profile</span>
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
