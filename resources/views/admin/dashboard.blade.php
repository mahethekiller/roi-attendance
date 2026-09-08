<x-admin-layout>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="fw-bold text-body-emphasis mb-0">Dashboard Overview</h2>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-sm-inline-flex align-items-center gap-1 font-monospace" style="font-size: 0.75rem;">
                    <i data-lucide="calendar" style="width: 13px; height: 13px;"></i>
                    {{ now()->format('D, M d, Y') }}
                </span>
            </div>
            <p class="text-body-secondary mb-0">
                Welcome back, <strong class="text-body-emphasis">{{ Auth::user()->name }}</strong>. Here is your corporate workforce and biometric attendance intelligence.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <x-authorized permission="attendances.sync">
                <form action="{{ route('admin.attendances.sync') }}" method="POST" class="d-inline" id="quickSyncForm">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-1" id="quickSyncBtn">
                        <i data-lucide="refresh-cw" style="width: 15px; height: 15px;" id="quickSyncIcon"></i>
                        <span>Sync Biometrics</span>
                    </button>
                </form>
            </x-authorized>
            <x-authorized permission="attendances.view">
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-1">
                    <i data-lucide="calendar-check" style="width: 15px; height: 15px;"></i>
                    <span>View Attendance Logs</span>
                </a>
            </x-authorized>
            <x-authorized permission="employees.create">
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-1">
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Add Employee</span>
                </a>
            </x-authorized>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i data-lucide="check-circle-2" class="text-success" style="width: 18px; height: 18px;"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i data-lucide="alert-triangle" class="text-danger" style="width: 18px; height: 18px;"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Employee Self-Service Banner if linked to employee record --}}
    @if($personalStats && $employeeRecord)
        <div class="card border shadow-sm bg-body text-body mb-4">
            <div class="card-body p-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center fw-bold font-monospace" style="width: 44px; height: 44px; font-size: 1rem;">
                        {{ $employeeRecord->initials }}
                    </div>
                    <div>
                        <div class="fw-semibold text-body-emphasis">My Attendance Card ({{ $employeeRecord->full_name }})</div>
                        <div class="small text-body-secondary font-monospace">Card ID: {{ $employeeRecord->card_no }} &bull; {{ $employeeRecord->company ?? 'General' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <span class="small text-body-secondary d-block">This Month</span>
                        <strong class="text-success">{{ $personalStats['daysPresent'] }} Days Present</strong>
                    </div>
                    <div class="border-start ps-3">
                        <span class="small text-body-secondary d-block">Today's Punch</span>
                        @if($personalStats['todayRecord'])
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                                In: {{ $personalStats['todayRecord']->check_in_time ?? $personalStats['todayRecord']->check_in_datetime?->format('h:i A') }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-body-secondary border border-secondary-subtle">
                                No punch yet
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Top KPI Metric Cards Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-kpi-metric-card
                title="Total Staff"
                :value="number_format($totalEmployees)"
                :subtitle="$totalCardsAssigned . ' active RFID cards assigned'"
                icon="users"
                color="primary"
                trend="Workforce"
                trendType="primary"
                trendIcon="user-check"
            />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-kpi-metric-card
                title="Present Today"
                :value="number_format($todayPresent)"
                :subtitle="$attendanceRate . '% attendance rate today'"
                icon="check-circle-2"
                color="success"
                :trend="$attendanceRate . '%'"
                trendType="success"
                trendIcon="trending-up"
            />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-kpi-metric-card
                title="Absent Today"
                :value="number_format($todayAbsent)"
                subtitle="Staff without registered punches today"
                icon="user-x"
                color="danger"
                :trend="($totalEmployees > 0 ? round(($todayAbsent / $totalEmployees) * 100, 1) : 0) . '%'"
                :trendType="$todayAbsent > 0 ? 'danger' : 'secondary'"
                trendIcon="alert-circle"
            />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-kpi-metric-card
                title="Attendance Rate"
                :value="$attendanceRate . '%'"
                :subtitle="$todayPresent . ' of ' . $totalEmployees . ' present today'"
                icon="percent"
                color="info"
                :trend="$todayPresent . ' Present'"
                trendType="info"
                trendIcon="activity"
            />
        </div>
    </div>

    {{-- Main Analytics & Monitoring Grid --}}
    <div class="row g-4 mb-4">
        {{-- Left Column: Trend & Flow Charts --}}
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">
            {{-- 7-Day Attendance Trend Analysis --}}
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-body-emphasis">7-Day Attendance Trends</h6>
                        <span class="text-body-secondary small">Daily comparison of Present and Absent personnel</span>
                    </div>
                    <span class="badge bg-body-tertiary text-body-secondary border font-monospace">
                        Last 7 Days
                    </span>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Hourly Punch Distribution Flow --}}
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-body-emphasis">Peak Check-in Distribution</h6>
                        <span class="text-body-secondary small">Punch volume across hourly shifts today (06:00 AM – 06:00 PM)</span>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                        Today's Flow
                    </span>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 170px; width: 100%;">
                        <canvas id="hourlyPunchChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Hardware Health & Company Breakdown --}}
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">
            {{-- Biometric Sync Engine Card --}}
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="radio" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <h6 class="fw-bold mb-0 text-body-emphasis">Biometric Hardware Sync</h6>
                    </div>
                    @if($latestSync && $latestSync->status === 'success')
                        <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-2">
                            <span class="telemetry-beacon">
                                <span class="telemetry-pulse"></span>
                                <span class="telemetry-dot"></span>
                            </span>
                            <span>Online</span>
                        </span>
                    @elseif($latestSync && $latestSync->status === 'failed')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                            <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i>
                            Sync Error
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                            Standby
                        </span>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-2 rounded bg-body-tertiary border d-flex align-items-center justify-content-between">
                            <span class="small text-body-secondary">Last Sync Run</span>
                            <span class="small fw-semibold text-body-emphasis font-monospace">
                                {{ $latestSync ? $latestSync->created_at->diffForHumans() : 'No sync recorded' }}
                            </span>
                        </div>

                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div class="p-2 rounded bg-body-tertiary border">
                                    <span class="d-block small text-body-secondary mb-1">Today's Runs</span>
                                    <span class="h5 fw-bold mb-0 text-body-emphasis font-monospace">{{ $todaySyncCount }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-body-tertiary border">
                                    <span class="d-block small text-body-secondary mb-1">Punches Pulled</span>
                                    <span class="h5 fw-bold mb-0 text-success font-monospace">{{ number_format($todayImportedCount) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- API Engine Quick Status --}}
                        <div class="p-2 rounded bg-body-tertiary border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="activity" class="text-primary" style="width: 16px; height: 16px;"></i>
                                <span class="small text-body-secondary">API Traffic & Latency</span>
                            </div>
                            <span class="small fw-semibold text-body-emphasis font-monospace">
                                {{ $todayApiRequests }} reqs &bull; {{ $avgApiLatency }}ms
                            </span>
                        </div>

                        <x-authorized permission="attendances.sync">
                            <form action="{{ route('admin.attendances.sync') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                    <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                                    <span>Sync Attendances Now</span>
                                </button>
                            </form>
                        </x-authorized>

                        <x-authorized permission="sync-logs.view">
                            <div class="text-center mt-1">
                                <a href="{{ route('admin.sync-logs.index') }}" class="small text-body-secondary text-decoration-none d-inline-flex align-items-center gap-1">
                                    <span>View complete sync history logs</span>
                                    <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
                                </a>
                            </div>
                        </x-authorized>
                    </div>
                </div>
            </div>

            {{-- Company / Department Attendance Rate Card --}}
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0 text-body-emphasis">Company Divisions</h6>
                        <span class="text-body-secondary small">Workforce attendance rate</span>
                    </div>
                    <i data-lucide="building-2" class="text-body-secondary" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="card-body p-3">
                    @forelse($companyBreakdown as $comp)
                        <div class="mb-3 {{ $loop->last ? 'mb-0' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium text-body-emphasis small">{{ $comp['name'] }}</span>
                                <span class="small text-body-secondary font-monospace">
                                    {{ $comp['present'] }}/{{ $comp['total'] }} ({{ $comp['rate'] }}%)
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;" role="progressbar" aria-valuenow="{{ $comp['rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar {{ $comp['rate'] >= 80 ? 'bg-success' : ($comp['rate'] >= 50 ? 'bg-primary' : 'bg-warning') }}" style="width: {{ $comp['rate'] }}%; transition: width 0.8s var(--ease-out-expo);"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-body-secondary small">
                            <i data-lucide="layers" style="width: 24px; height: 24px;" class="mb-1 opacity-50"></i>
                            <div>No company divisions registered yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Live Attendance Feed Table --}}
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-body-emphasis">Today's Live Punch Feed</h6>
                <span class="text-body-secondary small">Stream of biometric punches synchronized from devices</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                    {{ $recentPunches->count() }} Recent Records
                </span>
                <x-authorized permission="attendances.view">
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                        <span>All Logs</span>
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </x-authorized>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary small text-uppercase tracking-wider">
                    <tr>
                        <th class="ps-3">Employee</th>
                        <th>Card / Badge</th>
                        <th>Company</th>
                        <th>Punch Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPunches as $punch)
                        @php
                            $empName = $punch->employee ? $punch->employee->full_name : 'Card #' . $punch->card_no;
                            $empInitials = $punch->employee ? $punch->employee->initials : 'ID';
                            $company = $punch->employee?->company ?? '—';
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center font-monospace" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ $empInitials }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-body-emphasis">{{ $empName }}</div>
                                        @if($punch->employee?->employee_id)
                                            <div class="small text-body-secondary font-monospace" style="font-size: 0.75rem;">
                                                ID: {{ $punch->employee->employee_id }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-body-tertiary text-body-secondary border font-monospace">
                                    {{ $punch->card_no }}
                                </span>
                            </td>
                            <td class="text-body-secondary small">
                                {{ $company }}
                            </td>
                            <td class="font-monospace small text-body-emphasis">
                                {{ $punch->punch_date ? $punch->punch_date->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                <span class="font-monospace small fw-medium text-body-emphasis">
                                    {{ $punch->check_in_time ?? ($punch->check_in_datetime ? $punch->check_in_datetime->format('h:i A') : '—') }}
                                </span>
                            </td>
                            <td>
                                @if($punch->check_out_time || $punch->check_out_datetime)
                                    <span class="font-monospace small text-body-emphasis">
                                        {{ $punch->check_out_time ?? $punch->check_out_datetime->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-body-secondary font-monospace small">-- : --</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                                    <i data-lucide="check" style="width: 12px; height: 12px;"></i>
                                    Present
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <x-authorized permission="attendances.view">
                                    <a href="{{ route('admin.attendances.index', ['card_no' => $punch->card_no]) }}" class="btn btn-sm btn-outline-secondary" title="View historical records for this card">
                                        Logs
                                    </a>
                                </x-authorized>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-body-secondary">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="rounded-circle bg-body-tertiary p-3 mb-3 border">
                                        <i data-lucide="calendar-x" class="text-body-secondary" style="width: 32px; height: 32px;"></i>
                                    </div>
                                    <h6 class="fw-semibold text-body-emphasis mb-1">No attendance punches recorded today</h6>
                                    <p class="small text-body-secondary mb-3">Punches will automatically stream here upon biometric device synchronization.</p>
                                    <x-authorized permission="attendances.sync">
                                        <form action="{{ route('admin.attendances.sync') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2">
                                                <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                                                <span>Synchronize Devices Now</span>
                                            </button>
                                        </form>
                                    </x-authorized>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return;
            }

            // Quick sync button loading state
            const quickSyncForm = document.getElementById('quickSyncForm');
            const quickSyncBtn = document.getElementById('quickSyncBtn');
            const quickSyncIcon = document.getElementById('quickSyncIcon');
            if (quickSyncForm && quickSyncBtn) {
                quickSyncForm.addEventListener('submit', function () {
                    quickSyncBtn.disabled = true;
                    if (quickSyncIcon) {
                        quickSyncIcon.classList.add('animate-spin-smooth');
                    }
                    quickSyncBtn.querySelector('span').innerText = 'Syncing...';
                });
            }

            // Helper to get theme color tokens
            function getThemeConfig() {
                const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                return {
                    textColor: isDark ? '#94A3B8' : '#64748B',
                    gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
                    tooltipBg: isDark ? '#1E293B' : '#FFFFFF',
                    tooltipText: isDark ? '#F8FAFC' : '#0F172A',
                    tooltipBorder: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                };
            }

            let themeColors = getThemeConfig();

            // 1. Initialize 7-Day Trend Chart with smooth easing
            const trendCtx = document.getElementById('attendanceTrendChart');
            let trendChart = null;
            if (trendCtx) {
                trendChart = new Chart(trendCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($trendLabels),
                        datasets: [
                            {
                                label: 'Present',
                                data: @json($trendPresent),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10B981',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Absent',
                                data: @json($trendAbsent),
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointBackgroundColor: '#EF4444',
                                pointBorderColor: '#fff',
                                pointHoverRadius: 5,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 650,
                            easing: 'easeOutQuart',
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: themeColors.textColor,
                                    font: { family: "'Outfit', sans-serif", size: 12 },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                }
                            },
                            tooltip: {
                                backgroundColor: themeColors.tooltipBg,
                                titleColor: themeColors.tooltipText,
                                bodyColor: themeColors.tooltipText,
                                borderColor: themeColors.tooltipBorder,
                                borderWidth: 1,
                                padding: 10,
                                boxPadding: 6,
                                usePointStyle: true,
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: themeColors.gridColor },
                                ticks: { color: themeColors.textColor, font: { family: "'Outfit', sans-serif" } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: themeColors.gridColor },
                                ticks: { color: themeColors.textColor, font: { family: "'JetBrains Mono', monospace" } }
                            }
                        }
                    }
                });
            }

            // 2. Initialize Hourly Check-in Distribution Chart
            const hourlyCtx = document.getElementById('hourlyPunchChart');
            let hourlyChart = null;
            if (hourlyCtx) {
                hourlyChart = new Chart(hourlyCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($hourlyLabels),
                        datasets: [
                            {
                                label: 'Punches',
                                data: @json($hourlyPunches),
                                backgroundColor: '#6366F1',
                                borderRadius: 4,
                                hoverBackgroundColor: '#4F46E5',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 650,
                            easing: 'easeOutQuart',
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: themeColors.tooltipBg,
                                titleColor: themeColors.tooltipText,
                                bodyColor: themeColors.tooltipText,
                                borderColor: themeColors.tooltipBorder,
                                borderWidth: 1,
                                padding: 10,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: themeColors.textColor, font: { family: "'Outfit', sans-serif", size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: themeColors.gridColor },
                                ticks: {
                                    color: themeColors.textColor,
                                    precision: 0,
                                    font: { family: "'JetBrains Mono', monospace", size: 11 }
                                }
                            }
                        }
                    }
                });
            }

            // 3. Smooth Counter Interpolation for KPI Cards (Respects prefers-reduced-motion)
            function animateCounters() {
                const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (prefersReduced) return;

                const counters = document.querySelectorAll('.counter-value');
                counters.forEach(counter => {
                    const target = parseFloat(counter.getAttribute('data-counter-target'));
                    const suffix = counter.getAttribute('data-counter-suffix') || '';
                    if (isNaN(target)) return;

                    const duration = 650;
                    const startTime = performance.now();
                    const isDecimal = target % 1 !== 0;

                    function updateCount(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        // Quartic ease out: 1 - pow(1 - progress, 4)
                        const easeOut = 1 - Math.pow(1 - progress, 4);
                        const currentVal = isDecimal 
                            ? (target * easeOut).toFixed(1)
                            : Math.round(target * easeOut).toLocaleString();

                        counter.innerText = currentVal + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(updateCount);
                        } else {
                            counter.innerText = (isDecimal ? target.toFixed(1) : Math.round(target).toLocaleString()) + suffix;
                        }
                    }

                    requestAnimationFrame(updateCount);
                });
            }
            animateCounters();

            // 4. Dynamic Theme Switch Listener for Chart Updates
            window.addEventListener('roi-theme-changed', function (e) {
                const updatedTheme = getThemeConfig();

                if (trendChart) {
                    trendChart.options.plugins.legend.labels.color = updatedTheme.textColor;
                    trendChart.options.plugins.tooltip.backgroundColor = updatedTheme.tooltipBg;
                    trendChart.options.plugins.tooltip.titleColor = updatedTheme.tooltipText;
                    trendChart.options.plugins.tooltip.bodyColor = updatedTheme.tooltipText;
                    trendChart.options.plugins.tooltip.borderColor = updatedTheme.tooltipBorder;
                    trendChart.options.scales.x.grid.color = updatedTheme.gridColor;
                    trendChart.options.scales.x.ticks.color = updatedTheme.textColor;
                    trendChart.options.scales.y.grid.color = updatedTheme.gridColor;
                    trendChart.options.scales.y.ticks.color = updatedTheme.textColor;
                    trendChart.update();
                }

                if (hourlyChart) {
                    hourlyChart.options.plugins.tooltip.backgroundColor = updatedTheme.tooltipBg;
                    hourlyChart.options.plugins.tooltip.titleColor = updatedTheme.tooltipText;
                    hourlyChart.options.plugins.tooltip.bodyColor = updatedTheme.tooltipText;
                    hourlyChart.options.plugins.tooltip.borderColor = updatedTheme.tooltipBorder;
                    hourlyChart.options.scales.y.grid.color = updatedTheme.gridColor;
                    hourlyChart.options.scales.x.ticks.color = updatedTheme.textColor;
                    hourlyChart.options.scales.y.ticks.color = updatedTheme.textColor;
                    hourlyChart.update();
                }

                if (typeof renderLucideIcons === 'function') {
                    renderLucideIcons();
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
