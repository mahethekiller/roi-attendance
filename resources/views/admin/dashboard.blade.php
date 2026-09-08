<x-admin-layout>
    <!-- Header Title & Quick Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                <h2 class="text-2xl sm:text-3xl font-bold text-base-content tracking-tight">Dashboard Overview</h2>
                <span class="badge badge-primary badge-soft font-mono text-xs inline-flex items-center gap-1.5 px-2.5 py-1">
                    <i data-lucide="calendar" style="width: 13px; height: 13px;"></i>
                    <span>{{ now()->format('D, M d, Y') }}</span>
                </span>
                <span class="badge badge-success badge-soft font-mono text-xs hidden sm:inline-flex items-center gap-1 px-2.5 py-1">
                    <span class="telemetry-beacon">
                        <span class="telemetry-pulse"></span>
                        <span class="telemetry-dot"></span>
                    </span>
                    <span>Live Biometric Feed</span>
                </span>
            </div>
            <p class="text-sm text-base-content/70 max-w-2xl leading-relaxed">
                Welcome back, <strong class="text-base-content font-semibold">{{ Auth::user()->name }}</strong>. Here is your corporate workforce and biometric attendance intelligence.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <x-authorized permission="attendances.sync">
                <form action="{{ route('admin.attendances.sync') }}" method="POST" class="inline" id="quickSyncForm">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-outline btn-sm shadow-xs flex items-center gap-2 transition-all duration-200 hover:shadow-sm" id="quickSyncBtn">
                        <i data-lucide="refresh-cw" style="width: 14px; height: 14px;" id="quickSyncIcon"></i>
                        <span>Sync Biometrics</span>
                    </button>
                </form>
            </x-authorized>
            <x-authorized permission="attendances.view">
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline btn-sm shadow-xs flex items-center gap-2 transition-all duration-200 hover:shadow-sm">
                    <i data-lucide="calendar-check" style="width: 14px; height: 14px;"></i>
                    <span>View Attendance Logs</span>
                </a>
            </x-authorized>
            <x-authorized permission="employees.create">
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm shadow-xs flex items-center gap-2 transition-all duration-200 hover:shadow-sm">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                    <span>Add Employee</span>
                </a>
            </x-authorized>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-xs mb-6 flex items-center gap-3 border border-success/20" role="alert">
            <i data-lucide="check-circle-2" class="text-success shrink-0" style="width: 18px; height: 18px;"></i>
            <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error shadow-xs mb-6 flex items-center gap-3 border border-error/20" role="alert">
            <i data-lucide="alert-triangle" class="text-error shrink-0" style="width: 18px; height: 18px;"></i>
            <span class="flex-1 text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Employee Self-Service Banner if linked to employee record --}}
    @if($personalStats && $employeeRecord)
        <div class="card bg-base-100 border border-base-200 shadow-xs mb-6 transition-all duration-200 hover:border-base-content/20 hover:shadow-sm">
            <div class="card-body p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="avatar placeholder shrink-0">
                        <div class="bg-primary/10 text-primary border border-primary/20 rounded-2xl w-12 h-12 font-bold font-mono text-sm flex items-center justify-center shadow-xs">
                            {{ $employeeRecord->initials }}
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-base-content text-sm sm:text-base flex items-center gap-2">
                            <span>My Attendance Card ({{ $employeeRecord->full_name }})</span>
                            <span class="badge badge-primary badge-soft badge-xs font-mono">Self Service</span>
                        </div>
                        <div class="text-xs text-base-content/60 font-mono mt-0.5 flex items-center gap-2">
                            <span>Card ID: {{ $employeeRecord->card_no }}</span>
                            <span>&bull;</span>
                            <span>{{ $employeeRecord->company ?? 'General' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-5 shrink-0">
                    <div class="text-left md:text-right">
                        <span class="text-xs text-base-content/60 block font-medium">This Month</span>
                        <strong class="text-success text-sm font-mono tabular-nums">{{ $personalStats['daysPresent'] }} Days Present</strong>
                    </div>
                    <div class="border-l border-base-200 pl-5">
                        <span class="text-xs text-base-content/60 block font-medium">Today's Punch</span>
                        @if($personalStats['todayRecord'])
                            <span class="badge badge-success badge-soft font-mono text-xs px-2.5 py-1">
                                In: {{ $personalStats['todayRecord']->check_in_time ?? $personalStats['todayRecord']->check_in_datetime?->format('h:i A') }}
                            </span>
                        @else
                            <span class="badge badge-neutral badge-soft text-xs px-2.5 py-1">
                                No punch yet
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Top KPI Metric Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
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

    {{-- Main Analytics & Monitoring Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Left Column: Trend & Flow Charts (2 cols on lg) --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            {{-- 7-Day Attendance Trend Analysis --}}
            <div class="card bg-base-100 border border-base-200 shadow-xs transition-all duration-200 hover:border-base-content/20 hover:shadow-sm">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4 pb-3 border-b border-base-200">
                        <div>
                            <h3 class="font-bold text-base text-base-content tracking-tight">7-Day Attendance Trends</h3>
                            <p class="text-xs text-base-content/60">Daily comparison of Present and Absent personnel</p>
                        </div>
                        <span class="badge badge-neutral badge-soft font-mono text-xs px-2.5 py-1">
                            Last 7 Days
                        </span>
                    </div>
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Hourly Punch Distribution Flow --}}
            <div class="card bg-base-100 border border-base-200 shadow-xs transition-all duration-200 hover:border-base-content/20 hover:shadow-sm">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4 pb-3 border-b border-base-200">
                        <div>
                            <h3 class="font-bold text-base text-base-content tracking-tight">Peak Check-in Distribution</h3>
                            <p class="text-xs text-base-content/60">Punch volume across hourly shifts today (06:00 AM – 06:00 PM)</p>
                        </div>
                        <span class="badge badge-primary badge-soft font-mono text-xs px-2.5 py-1">
                            Today's Flow
                        </span>
                    </div>
                    <div style="position: relative; height: 180px; width: 100%;">
                        <canvas id="hourlyPunchChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Hardware Health & Company Breakdown (1 col on lg) --}}
        <div class="lg:col-span-1 flex flex-col gap-6">
            {{-- Biometric Sync Engine Card --}}
            <div class="card bg-base-100 border border-base-200 shadow-xs transition-all duration-200 hover:border-base-content/20 hover:shadow-sm">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-base-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <i data-lucide="radio" style="width: 16px; height: 16px;"></i>
                            </div>
                            <h3 class="font-bold text-base text-base-content tracking-tight">Biometric Hardware Sync</h3>
                        </div>
                        @if($latestSync && $latestSync->status === 'success')
                            <span class="badge badge-success badge-soft font-medium flex items-center gap-2 px-2.5 py-1">
                                <span class="telemetry-beacon">
                                    <span class="telemetry-pulse"></span>
                                    <span class="telemetry-dot"></span>
                                </span>
                                <span>Online</span>
                            </span>
                        @elseif($latestSync && $latestSync->status === 'failed')
                            <span class="badge badge-error badge-soft font-medium flex items-center gap-1.5 px-2.5 py-1">
                                <i data-lucide="alert-circle" style="width: 13px; height: 13px;"></i>
                                <span>Sync Error</span>
                            </span>
                        @else
                            <span class="badge badge-neutral badge-soft px-2.5 py-1">
                                Standby
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="p-3 rounded-xl bg-base-200/50 border border-base-200 flex items-center justify-between text-xs">
                            <span class="text-base-content/70 font-medium">Last Sync Run</span>
                            <span class="font-semibold text-base-content font-mono tabular-nums">
                                {{ $latestSync ? $latestSync->created_at->diffForHumans() : 'No sync recorded' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 text-center">
                            <div class="p-3 rounded-xl bg-base-200/50 border border-base-200">
                                <span class="block text-xs text-base-content/60 mb-1 font-medium">Today's Runs</span>
                                <span class="text-xl font-bold text-base-content font-mono tabular-nums">{{ $todaySyncCount }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-base-200/50 border border-base-200">
                                <span class="block text-xs text-base-content/60 mb-1 font-medium">Punches Pulled</span>
                                <span class="text-xl font-bold text-success font-mono tabular-nums">{{ number_format($todayImportedCount) }}</span>
                            </div>
                        </div>

                        {{-- API Engine Quick Status --}}
                        <div class="p-3 rounded-xl bg-base-200/50 border border-base-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <i data-lucide="activity" class="text-primary shrink-0" style="width: 14px; height: 14px;"></i>
                                <span class="text-base-content/70 font-medium">API Traffic & Latency</span>
                            </div>
                            <span class="font-semibold text-base-content font-mono tabular-nums">
                                {{ $todayApiRequests }} reqs &bull; {{ $avgApiLatency }}ms
                            </span>
                        </div>

                        <x-authorized permission="attendances.sync">
                            <form action="{{ route('admin.attendances.sync') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-outline btn-sm w-full flex items-center justify-center gap-2 shadow-xs transition-all duration-200 hover:shadow-sm">
                                    <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                                    <span>Sync Attendances Now</span>
                                </button>
                            </form>
                        </x-authorized>

                        <x-authorized permission="sync-logs.view">
                            <div class="text-center mt-1">
                                <a href="{{ route('admin.sync-logs.index') }}" class="text-xs text-base-content/60 hover:text-primary transition-colors inline-flex items-center gap-1.5 py-1">
                                    <span>View complete sync history logs</span>
                                    <i data-lucide="chevron-right" style="width: 13px; height: 13px;"></i>
                                </a>
                            </div>
                        </x-authorized>
                    </div>
                </div>
            </div>

            {{-- Company / Department Attendance Rate Card --}}
            <div class="card bg-base-100 border border-base-200 shadow-xs transition-all duration-200 hover:border-base-content/20 hover:shadow-sm">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-base-200">
                        <div>
                            <h3 class="font-bold text-base text-base-content tracking-tight">Company Divisions</h3>
                            <p class="text-xs text-base-content/60">Workforce attendance rate</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-base-200/70 text-base-content/70 flex items-center justify-center shrink-0">
                            <i data-lucide="building-2" style="width: 16px; height: 16px;"></i>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3.5">
                        @forelse($companyBreakdown as $comp)
                            <div class="p-2.5 rounded-xl bg-base-200/30 border border-base-200/60">
                                <div class="flex justify-between items-center text-xs mb-1.5">
                                    <span class="font-semibold text-base-content truncate">{{ $comp['name'] }}</span>
                                    <span class="text-base-content/70 font-mono tabular-nums text-[11px] shrink-0">
                                        {{ $comp['present'] }}/{{ $comp['total'] }} ({{ $comp['rate'] }}%)
                                    </span>
                                </div>
                                <progress class="progress {{ $comp['rate'] >= 80 ? 'progress-success' : ($comp['rate'] >= 50 ? 'progress-primary' : 'progress-warning') }} w-full" value="{{ $comp['rate'] }}" max="100" style="height: 6px;"></progress>
                            </div>
                        @empty
                            <div class="text-center py-6 text-base-content/50 text-xs">
                                <div class="w-10 h-10 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-2 opacity-60">
                                    <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
                                </div>
                                <div>No company divisions registered yet.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Live Attendance Feed Table --}}
    <div class="card bg-base-100 border border-base-200 shadow-xs overflow-hidden transition-all duration-200 hover:border-base-content/20 hover:shadow-sm">
        <div class="card-body p-4 sm:p-5 pb-3">
            <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-base-200">
                <div>
                    <h3 class="font-bold text-base text-base-content tracking-tight">Today's Live Punch Feed</h3>
                    <p class="text-xs text-base-content/60">Stream of biometric punches synchronized from devices</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="badge badge-primary badge-soft font-mono text-xs px-2.5 py-1">
                        {{ $recentPunches->count() }} Recent Records
                    </span>
                    <x-authorized permission="attendances.view">
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-xs btn-outline gap-1.5 shadow-xs">
                            <span>All Logs</span>
                            <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                        </a>
                    </x-authorized>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra table-hover w-full text-sm">
                <thead class="bg-base-200/50 text-base-content/70 text-xs uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="pl-5 py-3">Employee</th>
                        <th class="py-3">Card / Badge</th>
                        <th class="py-3">Company</th>
                        <th class="py-3">Punch Date</th>
                        <th class="py-3">Clock In</th>
                        <th class="py-3">Clock Out</th>
                        <th class="py-3">Status</th>
                        <th class="text-right pr-5 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse($recentPunches as $punch)
                        @php
                            $empName = $punch->employee ? $punch->employee->full_name : 'Card #' . $punch->card_no;
                            $empInitials = $punch->employee ? $punch->employee->initials : 'ID';
                            $company = $punch->employee?->company ?? '—';
                        @endphp
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="pl-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder shrink-0">
                                        <div class="bg-primary/10 text-primary border border-primary/20 rounded-xl w-8 h-8 text-xs font-bold font-mono flex items-center justify-center shadow-2xs">
                                            {{ $empInitials }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content leading-tight">{{ $empName }}</div>
                                        @if($punch->employee?->employee_id)
                                            <div class="text-[11px] text-base-content/60 font-mono mt-0.5">
                                                ID: {{ $punch->employee->employee_id }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-sm badge-neutral badge-soft font-mono px-2 py-0.5">
                                    {{ $punch->card_no }}
                                </span>
                            </td>
                            <td class="text-xs text-base-content/70 py-3 font-medium">
                                {{ $company }}
                            </td>
                            <td class="font-mono text-xs text-base-content py-3 tabular-nums">
                                {{ $punch->punch_date ? $punch->punch_date->format('M d, Y') : '—' }}
                            </td>
                            <td class="py-3">
                                <span class="font-mono text-xs font-semibold text-base-content tabular-nums">
                                    {{ $punch->check_in_time ?? ($punch->check_in_datetime ? $punch->check_in_datetime->format('h:i A') : '—') }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($punch->check_out_time || $punch->check_out_datetime)
                                    <span class="font-mono text-xs text-base-content tabular-nums">
                                        {{ $punch->check_out_time ?? $punch->check_out_datetime->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-base-content/40 font-mono text-xs">-- : --</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge badge-sm badge-success badge-soft flex items-center gap-1 font-mono px-2 py-0.5">
                                    <i data-lucide="check" style="width: 12px; height: 12px;"></i>
                                    <span>Present</span>
                                </span>
                            </td>
                            <td class="text-right pr-5 py-3">
                                <x-authorized permission="attendances.view">
                                    <a href="{{ route('admin.attendances.index', ['card_no' => $punch->card_no]) }}" class="btn btn-xs btn-outline font-medium transition-all duration-150 hover:border-primary hover:text-primary" title="View historical records for this card">
                                        Logs
                                    </a>
                                </x-authorized>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-base-content/60">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-2xl bg-base-200 p-4 mb-3 text-base-content/50">
                                        <i data-lucide="calendar-x" style="width: 32px; height: 32px;"></i>
                                    </div>
                                    <h4 class="font-bold text-base-content text-base mb-1">No attendance punches recorded today</h4>
                                    <p class="text-xs text-base-content/60 mb-4 max-w-sm leading-relaxed">Punches will automatically stream here upon biometric device synchronization.</p>
                                    <x-authorized permission="attendances.sync">
                                        <form action="{{ route('admin.attendances.sync') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm inline-flex items-center gap-2 shadow-xs transition-all duration-200">
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
                    const spanEl = quickSyncBtn.querySelector('span');
                    if (spanEl) {
                        spanEl.innerText = 'Syncing...';
                    }
                });
            }

            // Helper to get theme color tokens
            function getThemeConfig() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || document.documentElement.getAttribute('data-bs-theme') || 'dark';
                const isDark = currentTheme === 'dark' || currentTheme === 'business' || currentTheme === 'dim';
                return {
                    textColor: isDark ? '#94A3B8' : '#64748B',
                    gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
                    tooltipBg: isDark ? '#1E293B' : '#FFFFFF',
                    tooltipText: isDark ? '#F8FAFC' : '#0F172A',
                    tooltipBorder: isDark ? 'rgba(255, 255, 255, 0.12)' : 'rgba(0, 0, 0, 0.1)',
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
