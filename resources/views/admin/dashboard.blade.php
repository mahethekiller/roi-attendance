<x-admin-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">Admin Dashboard</h2>
            <p class="text-body-secondary mb-0">Welcome back, {{ Auth::user()->name }}! Here's what's happening today.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-1">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                <span>Export Report</span>
            </button>
            <button class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-1">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                <span>Add Employee</span>
            </button>
        </div>
    </div>

    <!-- Top KPI Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-body text-body stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-medium">Total Staff</span>
                        <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-body-emphasis">148</h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                            <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i> +4%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-body text-body stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-medium">Present Today</span>
                        <div class="rounded-circle p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-body-emphasis">132</h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">89.1%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-body text-body stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-medium">Late Arrival</span>
                        <div class="rounded-circle p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i data-lucide="clock" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-body-emphasis">9</h3>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">6.0%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-body text-body stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-medium">On Leave</span>
                        <div class="rounded-circle p-2 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i data-lucide="calendar" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-body-emphasis">7</h3>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">Approved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Overview Table -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">Today's Attendance Status</h6>
            <div class="badge bg-primary-subtle text-primary border border-primary-subtle">
                {{ date('M d, Y') }}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                    SC
                                </div>
                                <span class="fw-medium text-body-emphasis">Sarah Connor</span>
                            </div>
                        </td>
                        <td class="text-body-secondary">Engineering</td>
                        <td class="text-body-emphasis">08:58 AM</td>
                        <td class="text-body-secondary">-- : --</td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">On Time</span></td>
                        <td><button class="btn btn-sm btn-outline-secondary">View Log</button></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                    MT
                                </div>
                                <span class="fw-medium text-body-emphasis">Mark Taylor</span>
                            </div>
                        </td>
                        <td class="text-body-secondary">Operations</td>
                        <td class="text-body-emphasis">09:22 AM</td>
                        <td class="text-body-secondary">-- : --</td>
                        <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle">Late (+22m)</span></td>
                        <td><button class="btn btn-sm btn-outline-secondary">View Log</button></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                    JW
                                </div>
                                <span class="fw-medium text-body-emphasis">John Wick</span>
                            </div>
                        </td>
                        <td class="text-body-secondary">Security</td>
                        <td class="text-body-emphasis">08:45 AM</td>
                        <td class="text-body-emphasis">05:00 PM</td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">Completed</span></td>
                        <td><button class="btn btn-sm btn-outline-secondary">View Log</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
