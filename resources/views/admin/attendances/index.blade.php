<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">Attendance Logs</h2>
            <p class="text-body-secondary mb-0">Biometric punch entries, check-in/out timestamps, and employee status tracking.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.sync-logs.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="history" style="width: 16px; height: 16px;"></i>
                <span>View Sync History</span>
            </a>
            <form method="POST" action="{{ route('admin.attendances.sync') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                    <span>Sync Biometric Data</span>
                </button>
            </form>
            <button class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-2" onclick="window.print()">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                <span>Print Daily Sheet</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Present on {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalPresentToday }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Late Arrivals</span>
                    <h3 class="mb-0 fw-bold text-warning mt-1">{{ $totalLateToday }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Total Registered Employees</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalEmployees }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.attendances.index') }}" class="row g-2 align-items-center">
                <!-- Date Picker -->
                <div class="col-12 col-md-3">
                    <input type="date" name="date" class="form-control bg-body-tertiary text-body" value="{{ $date }}">
                </div>

                <!-- Search Input -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="search" name="search" class="form-control bg-body-tertiary border-start-0 text-body" placeholder="Search Card, Badge No, or Name..." value="{{ $search }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select bg-body-tertiary text-body">
                        <option value="">All Statuses</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                        <option value="early_exit" {{ $status === 'early_exit' ? 'selected' : '' }}>Early Exit</option>
                        <option value="half_day" {{ $status === 'half_day' ? 'selected' : '' }}>Half Day</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
                    </button>
                    @if($search || $status || $date !== date('Y-m-d'))
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">
                Daily Records for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }} ({{ $attendances->total() }})
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>Employee</th>
                        <th>Card No</th>
                        <th>Badge No</th>
                        <th>Punch Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.8rem;">
                                        {{ $attendance->employee->initials ?? 'NA' }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-body-emphasis">
                                            {{ $attendance->employee->full_name ?? 'Unregistered Card' }}
                                        </div>
                                        <div class="text-body-secondary small">
                                            {{ $attendance->employee->employee_id ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-body-tertiary text-body border fw-mono">
                                    {{ $attendance->card_no ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-body-secondary">{{ $attendance->badgenumber ?? '-' }}</td>
                            <td class="text-body-secondary">{{ $attendance->punch_date ? $attendance->punch_date->format('M d, Y') : '-' }}</td>
                            <td>
                                @if($attendance->check_in_time)
                                    <span class="text-body-emphasis fw-medium">{{ $attendance->check_in_time }}</span>
                                @elseif($attendance->check_in_datetime)
                                    <span class="text-body-emphasis fw-medium">{{ $attendance->check_in_datetime->format('h:i A') }}</span>
                                @else
                                    <span class="text-body-secondary">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_out_time)
                                    <span class="text-body-emphasis fw-medium">{{ $attendance->check_out_time }}</span>
                                @elseif($attendance->check_out_datetime)
                                    <span class="text-body-emphasis fw-medium">{{ $attendance->check_out_datetime->format('h:i A') }}</span>
                                @else
                                    <span class="text-body-secondary">--:--</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($attendance->show_status) {
                                        'present' => 'success',
                                        'late' => 'warning',
                                        'early_exit' => 'info',
                                        'half_day' => 'secondary',
                                        'absent' => 'danger',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }} border border-{{ $statusBadge }}-subtle">
                                    {{ ucfirst(str_replace('_', ' ', $attendance->show_status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-body-secondary">
                                <i data-lucide="calendar-x" class="mb-2 d-block mx-auto text-body-tertiary" style="width: 36px; height: 36px;"></i>
                                <span>No attendance records logged for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="card-footer bg-body border-top p-3">
                {{ $attendances->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-admin-layout>
