<x-admin-layout>
    <div class="mb-4">
        <h2 class="fw-bold text-body-emphasis mb-1">User Workspace</h2>
        <p class="text-body-secondary mb-0">Welcome to ROI Attendance, {{ Auth::user()->name }}.</p>
    </div>

    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-body-emphasis">Successfully Authenticated</h4>
                    <span class="text-body-secondary small">Logged in as {{ Auth::user()->email }}</span>
                </div>
            </div>

            <p class="text-body-secondary mb-4">
                You are currently signed in with role <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</span>.
            </p>

            <div class="d-flex flex-wrap gap-2">
                @hasanyrole('super-admin|admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary d-flex align-items-center gap-2">
                        <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                        <span>Go to Admin Dashboard</span>
                    </a>
                @endhasanyrole
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                    <span>Manage Account Profile</span>
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
