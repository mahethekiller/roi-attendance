<x-admin-layout>
    @section('title', 'Biometric 9-Hour Auto-Sync')
    @section('meta_description', 'Live biometric attendance telemetry inspector and automated 9-hour work compliance synchronization.')

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-mono text-base-content/60 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.attendances.index') }}" class="hover:text-primary transition-colors">Attendances</a>
                <span>/</span>
                <span class="text-primary font-semibold">9h Auto-Sync</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-warning/10 text-warning p-2 flex items-center justify-center">
                    <i data-lucide="clock-4" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-base-content tracking-tight">Biometric Attendance Sync Matrix</h1>
                    <p class="text-sm text-base-content/70 mt-0.5">Synchronize remote biometric API database with values saved locally in our database or 9h standard plan.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="badge badge-warning badge-soft gap-1.5 px-3 py-3 text-xs font-mono">
                <span class="telemetry-beacon" style="width:6px;height:6px;">
                    <span class="telemetry-pulse" style="background-color:#F59E0B;"></span>
                    <span class="telemetry-dot" style="width:6px;height:6px;background-color:#F59E0B;"></span>
                </span>
                Admin Security Guard
            </span>
            <a href="{{ route('admin.attendance-overrides.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs" title="Manage database override rules">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>DB Rules ({{ $activeRules->count() }})</span>
            </a>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Daily Logs</span>
            </a>
            <a href="{{ route('admin.sync-logs.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>Sync History</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card bg-base-100 border border-base-200/80 shadow-xs mb-6">
        <div class="card-body p-4 sm:p-5">
            <div class="flex items-center justify-between gap-2 border-b border-base-200 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4 text-primary"></i>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-base-content/80">Query Filters &amp; DB Plan Settings</h2>
                </div>
                <div class="text-xs text-base-content/50 font-mono hidden sm:block">
                    Target API: <span class="text-primary font-semibold">get_employee_punches_api.php</span>
                </div>
            </div>

            <form id="attendanceFilterForm" class="space-y-4" onsubmit="event.preventDefault(); loadAttendanceData();">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <!-- Employee Code Input & Fast Picker -->
                    <div class="sm:col-span-4">
                        <label for="empCodeInput" class="label label-text text-xs font-semibold uppercase tracking-wider text-base-content/70 pb-1">
                            Employee Code / Badge
                        </label>
                        <div class="join w-full">
                            <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                                <i data-lucide="badge-check" class="w-4 h-4 text-base-content/60"></i>
                            </span>
                            <input 
                                type="text" 
                                id="empCodeInput" 
                                list="employeeListDatalist" 
                                value="{{ $defaultEmpCode }}" 
                                placeholder="e.g. 10337" 
                                class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content font-mono font-semibold"
                                oninput="handleEmployeeCodeChange()"
                                required
                            />
                        </div>
                        <datalist id="employeeListDatalist">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->card_no ?: $emp->employee_id }}">{{ $emp->full_name }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </datalist>
                        <div class="text-[11px] text-base-content/50 mt-1 flex items-center justify-between">
                            <span>Badge/Card No from Biometric Device</span>
                            <span id="selectedEmpBadge" class="text-primary font-mono font-medium"></span>
                        </div>
                    </div>

                    <!-- Date Range: From -->
                    <div class="sm:col-span-3">
                        <label for="startDateInput" class="label label-text text-xs font-semibold uppercase tracking-wider text-base-content/70 pb-1">
                            From Date
                        </label>
                        <input 
                            type="date" 
                            id="startDateInput" 
                            value="{{ $startDate }}" 
                            class="input input-bordered input-sm sm:input-md w-full bg-base-100 text-base-content font-mono"
                            required
                        />
                    </div>

                    <!-- Date Range: To -->
                    <div class="sm:col-span-3">
                        <label for="endDateInput" class="label label-text text-xs font-semibold uppercase tracking-wider text-base-content/70 pb-1">
                            To Date
                        </label>
                        <input 
                            type="date" 
                            id="endDateInput" 
                            value="{{ $endDate }}" 
                            class="input input-bordered input-sm sm:input-md w-full bg-base-100 text-base-content font-mono"
                            required
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="sm:col-span-2 flex flex-col justify-end gap-2">
                        <button type="submit" id="loadAttendanceBtn" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full shadow-xs">
                            <i data-lucide="refresh-cw" class="w-4 h-4" id="loadBtnIcon"></i>
                            <span id="loadBtnText">Load Attendance</span>
                        </button>
                    </div>
                </div>

                <!-- Active DB Override Rule Plan Banner (Dynamic per selected Employee) -->
                <div id="activeRuleBanner" class="p-3 rounded-xl bg-info/10 border border-info/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="p-1.5 rounded-lg bg-info text-info-content">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="font-bold text-base-content" id="ruleBannerTitle">DB Override Rule Active for Employee</span>
                            <div class="text-[11px] text-base-content/70 font-mono mt-0.5" id="ruleBannerDesc">
                                Check-In: <strong class="text-primary">09:20 - 09:35</strong> | Check-Out: <strong class="text-success">&ge; 9.0 Hours</strong>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-info badge-sm badge-soft font-mono" id="ruleBannerBadge">Rule #1 Active</span>
                    </div>
                </div>

                <!-- Quick Date Presets Bar -->
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-base-200/60">
                    <span class="text-xs font-medium text-base-content/60 mr-1 flex items-center gap-1">
                        <i data-lucide="calendar-range" class="w-3.5 h-3.5"></i>
                        <span>Quick Presets:</span>
                    </span>
                    <button type="button" class="btn btn-xs btn-outline btn-ghost hover:btn-primary" onclick="setPreset('this_month')">This Month</button>
                    <button type="button" class="btn btn-xs btn-outline btn-ghost hover:btn-primary" onclick="setPreset('last_month')">Last Month</button>
                    <button type="button" class="btn btn-xs btn-outline btn-ghost hover:btn-primary" onclick="setPreset('last_30')">Last 30 Days</button>
                    <button type="button" class="btn btn-xs btn-outline btn-ghost hover:btn-primary" onclick="setPreset('this_week')">This Week</button>
                    <button type="button" class="btn btn-xs btn-ghost text-base-content/50 ml-auto" onclick="resetFilters()">
                        <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Error State / Network Diagnostic Notice (hidden by default) -->
    <div id="networkErrorAlert" class="alert alert-error shadow-sm mb-6 hidden" role="alert">
        <i data-lucide="alert-octagon" class="w-5 h-5 shrink-0"></i>
        <div class="text-xs sm:text-sm">
            <div class="font-bold">Biometric Server Unreachable</div>
            <div id="networkErrorMessage" class="mt-0.5">Could not connect to biometric API server. Please verify office VPN or network connectivity to 103.25.129.247.</div>
        </div>
        <button type="button" class="btn btn-xs btn-ghost" onclick="document.getElementById('networkErrorAlert').classList.add('hidden')">✕</button>
    </div>

    <!-- KPI Metrics Header (Dynamic) -->
    <div id="kpiContainer" class="hidden mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- Total Days Worked -->
            <div class="card bg-base-100 border border-base-200/80 shadow-xs relative overflow-hidden">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Total Days Worked</span>
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h2 class="text-3xl font-extrabold text-base-content tracking-tight font-mono" id="kpiTotalDays">0</h2>
                        <span class="text-xs text-base-content/60 font-mono" id="kpiTotalPunches">0 punches</span>
                    </div>
                    <div class="text-[11px] text-base-content/50 mt-1">Filtered date range duration</div>
                </div>
            </div>

            <!-- Completed Days (>= 9 Hours) -->
            <div class="card bg-base-100 border border-success/30 shadow-xs relative overflow-hidden">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-success uppercase tracking-wider">Completed (&ge; 9 Hours)</span>
                        <div class="w-8 h-8 rounded-lg bg-success/10 text-success flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h2 class="text-3xl font-extrabold text-success tracking-tight font-mono" id="kpiCompletedDays">0</h2>
                        <span class="badge badge-success badge-sm badge-soft font-mono" id="kpiCompletedPct">0%</span>
                    </div>
                    <div class="text-[11px] text-base-content/50 mt-1">Met standard 9-hour shift</div>
                </div>
            </div>

            <!-- Shortfall Days (< 9 Hours) -->
            <div class="card bg-base-100 border border-warning/40 shadow-xs relative overflow-hidden">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-warning uppercase tracking-wider">Shortfall (&lt; 9 Hours)</span>
                        <div class="w-8 h-8 rounded-lg bg-warning/10 text-warning flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h2 class="text-3xl font-extrabold text-warning tracking-tight font-mono" id="kpiShortfallDays">0</h2>
                        <span class="badge badge-warning badge-sm badge-soft font-mono" id="kpiShortfallNotice">Needs Sync</span>
                    </div>
                    <div class="text-[11px] text-base-content/50 mt-1">Below standard 9.0h duration</div>
                </div>
            </div>

            <!-- Locally Saved in MySQL DB -->
            <div class="card bg-base-100 border border-accent/40 shadow-xs relative overflow-hidden">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-accent uppercase tracking-wider">Saved in Local DB</span>
                        <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center">
                            <i data-lucide="database" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h2 class="text-3xl font-extrabold text-accent tracking-tight font-mono" id="kpiLocalDbCount">0</h2>
                        <span class="badge badge-accent badge-sm badge-soft font-mono">Ready to Push</span>
                    </div>
                    <div class="text-[11px] text-base-content/50 mt-1">Attendance records stored in MySQL</div>
                </div>
            </div>
        </div>

        <!-- Prominent Sync Action Bar -->
        <div id="bulkSyncBanner" class="mt-4 p-4 rounded-xl bg-base-100 border border-base-200/90 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 transition-all duration-300">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-accent/20 text-accent flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                    <i data-lucide="database-zap" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-base-content flex items-center gap-2">
                        <span>Synchronization Actions</span>
                        <span class="badge badge-accent badge-xs font-mono font-bold" id="localDbBadgeCount">0 Ready in DB</span>
                    </h3>
                    <p class="text-xs text-base-content/70 mt-0.5">
                        <strong class="text-accent font-bold">Push Local DB Values</strong> will overwrite the remote API database with the exact values saved in our local DB table.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="shrink-0 flex flex-wrap items-center gap-2">
                <!-- Highlighted Action: Update API DB with Locally Saved Values -->
                <button type="button" id="pushLocalDbBtn" class="btn btn-accent btn-sm sm:btn-md gap-2 font-bold shadow-xs" onclick="promptPushLocalDbConfirmation()">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    <span>Push Local DB to API DB</span>
                </button>

                <!-- Standard 9h Check-Out Sync -->
                <button type="button" id="bulkSyncBtn" class="btn btn-warning btn-sm sm:btn-md gap-2 font-bold shadow-xs" onclick="promptBulkSyncConfirmation()">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span id="bulkSyncBtnText">Auto-Sync 9h Out</span>
                </button>

                <!-- Sync via DB Rule Plan -->
                <button type="button" id="bulkDbRuleSyncBtn" class="btn btn-outline btn-primary btn-sm sm:btn-md gap-2 font-bold shadow-xs" onclick="promptBulkDbRuleSyncConfirmation()">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                    <span id="bulkDbRuleBtnText">DB Rule Plan</span>
                </button>
            </div>
        </div>

        <!-- Bulk Sync Live Progress Container (hidden when idle) -->
        <div id="bulkProgressContainer" class="mt-4 p-4 rounded-xl bg-base-100 border border-base-200 hidden">
            <div class="flex items-center justify-between text-xs mb-2">
                <span class="font-bold text-base-content flex items-center gap-2">
                    <span class="loading loading-spinner loading-xs text-accent" id="bulkProgressSpinner"></span>
                    <span id="bulkProgressStatus">Synchronizing Attendance Records...</span>
                </span>
                <span class="font-mono font-bold text-base-content/80" id="bulkProgressText">0 / 0 (0%)</span>
            </div>
            <progress id="bulkProgressBar" class="progress progress-accent w-full h-3" value="0" max="100"></progress>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="card bg-base-100 border border-base-200/80 shadow-xs">
        <div class="card-body p-0">
            <!-- Table Header Bar -->
            <div class="p-4 border-b border-base-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="table-2" class="w-4 h-4 text-base-content/60"></i>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-base-content/80">Biometric Attendance Logs &amp; Local DB Matrix</h2>
                    <span class="badge badge-sm badge-neutral font-mono" id="tableRowCountBadge">0 Days</span>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-base-content/50 font-mono">Legend:</span>
                    <span class="badge badge-sm badge-accent badge-soft font-mono">📤 Push DB (From Local)</span>
                    <span class="badge badge-sm badge-warning badge-soft font-mono">⚡ 9h Out</span>
                    <span class="badge badge-sm badge-primary badge-soft font-mono">🔄 Both (DB Plan)</span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto min-h-[300px]">
                <table class="table table-sm sm:table-md w-full" id="attendanceTable">
                    <thead>
                        <tr class="bg-base-200/50 text-base-content/70 text-xs uppercase tracking-wider border-b border-base-200">
                            <th class="py-3 px-4 font-bold">Date &amp; Day</th>
                            <th class="py-3 px-3 font-bold text-center">Logs</th>
                            <th class="py-3 px-3 font-bold">1st Entry (Check-In)</th>
                            <th class="py-3 px-3 font-bold">Last Entry (Check-Out)</th>
                            <th class="py-3 px-3 font-bold font-mono">Total Work Time</th>
                            <th class="py-3 px-3 font-bold text-center">Status / Local DB</th>
                            <th class="py-3 px-4 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody" class="divide-y divide-base-200/60 font-sans text-sm">
                        <!-- Initial Blank / Empty State -->
                        <tr id="initialEmptyRow">
                            <td colspan="7" class="py-16 text-center text-base-content/50">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center text-base-content/40">
                                        <i data-lucide="clock" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base text-base-content">No attendance data loaded yet</div>
                                        <p class="text-xs text-base-content/60 mt-1">Specify employee badge &amp; date range above, then click <strong class="text-primary font-bold">Load Attendance</strong>.</p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary gap-2 mt-2" onclick="loadAttendanceData()">
                                        <i data-lucide="search" class="w-4 h-4"></i>
                                        <span>Load for Employee {{ $defaultEmpCode }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Legend -->
            <div class="p-3 border-t border-base-200/60 bg-base-200/30 flex flex-wrap items-center justify-between text-xs text-base-content/60 gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                        <span>&ge; 9 Hours: Standard Completed</span>
                    </span>
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-warning"></span>
                        <span>Shortfall: Duration &lt; 9 Hours</span>
                    </span>
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-accent"></span>
                        <span>Saved Locally in MySQL DB</span>
                    </span>
                </div>
                <div class="font-mono text-[11px] text-base-content/50">
                    Live Telemetry Sync &bull; Server Time: Asia/Kolkata
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Adjustment Modal Dialog -->
    <dialog id="manualAdjustModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-100 border border-base-200/80 shadow-2xl p-5 sm:p-6 max-w-md">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-base-200 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-base-content">Manual Punch Adjustment</h3>
                        <p class="text-xs text-base-content/60">Modify biometric punches and save locally or push to API</p>
                    </div>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost" aria-label="Close modal">✕</button>
                </form>
            </div>

            <form id="manualAdjustForm" onsubmit="event.preventDefault(); submitManualAdjust();">
                <input type="hidden" id="modalTargetDate" />
                <input type="hidden" id="modalTargetEmpCode" />

                <!-- Details Card -->
                <div class="bg-base-200/60 rounded-xl p-3.5 space-y-2 mb-4 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-base-content/60 font-medium">Target Date:</span>
                        <span class="font-mono font-bold text-base-content" id="modalDisplayDate">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-base-content/60 font-medium">Employee Badge:</span>
                        <span class="font-mono font-bold text-primary" id="modalDisplayEmpCode">-</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-base-300/50 pt-2">
                        <span class="text-base-content/60 font-medium">Current Check-In (API):</span>
                        <span class="font-mono font-bold text-info" id="modalDisplayFirstPunch">-</span>
                    </div>
                    <div class="flex justify-between items-center" id="modalLocalDbRow">
                        <span class="text-base-content/60 font-medium">Saved in Local DB:</span>
                        <span class="font-mono font-bold text-accent" id="modalDisplayLocalDb">None</span>
                    </div>
                </div>

                <!-- Check-In Input (editable) -->
                <div class="mb-3">
                    <label for="modalFirstPunchInput" class="label label-text text-xs font-semibold text-base-content/80 pb-1">
                        Check-In Punch Time
                    </label>
                    <input 
                        type="datetime-local" 
                        step="1" 
                        id="modalFirstPunchInput" 
                        class="input input-bordered w-full bg-base-100 text-base-content font-mono font-semibold"
                        required
                    />
                </div>

                <!-- Check-Out Input -->
                <div class="mb-4">
                    <label for="modalLastPunchInput" class="label label-text text-xs font-semibold text-base-content/80 pb-1">
                        Check-Out Punch Time
                    </label>
                    <input 
                        type="datetime-local" 
                        step="1" 
                        id="modalLastPunchInput" 
                        class="input input-bordered w-full bg-base-100 text-base-content font-mono font-semibold"
                        required
                    />
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[11px] text-base-content/50">Auto-calculate random suggestion:</span>
                        <button 
                            type="button" 
                            class="btn btn-xs btn-outline btn-warning gap-1 font-mono" 
                            onclick="fillSuggestedInModal()"
                        >
                            <i data-lucide="sparkles" class="w-3 h-3"></i>
                            <span>Fill Suggested (9h 10m)</span>
                        </button>
                    </div>
                </div>

                <!-- Estimated Duration Preview -->
                <div class="p-3 rounded-lg bg-base-200/40 border border-base-200 text-xs flex items-center justify-between mb-5">
                    <span class="text-base-content/60">Estimated Shift Duration:</span>
                    <span class="font-mono font-bold text-success text-sm" id="modalEstimatedDuration">-</span>
                </div>

                <!-- Modal Actions -->
                <div class="modal-action mt-2 flex flex-wrap justify-end gap-2">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('manualAdjustModal').close()">Cancel</button>
                    <button type="button" id="modalSaveLocalOnlyBtn" class="btn btn-outline btn-accent btn-sm gap-1" onclick="submitSaveLocalOnly()">
                        <i data-lucide="database" class="w-3.5 h-3.5"></i>
                        <span>Save to Local DB</span>
                    </button>
                    <button type="submit" id="modalSaveBtn" class="btn btn-primary btn-sm gap-1">
                        <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i>
                        <span>Save &amp; Push to API DB</span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Confirmation Modal: Push Local DB Values to Remote API -->
    <dialog id="pushLocalDbConfirmModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-100 border border-base-200/80 shadow-2xl p-5 sm:p-6 max-w-lg">
            <div class="flex items-center gap-3 text-accent border-b border-base-200 pb-3 mb-4">
                <div class="p-2.5 rounded-xl bg-accent/10">
                    <i data-lucide="database-zap" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-base-content">Update API DB with Locally Saved Values</h3>
                    <p class="text-xs text-base-content/60">Pushes exact values from local MySQL database directly to the remote API database</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-accent/10 border border-accent/20 mb-3 text-xs">
                <div class="font-bold text-base-content mb-1">Direct Database Push</div>
                <p class="text-base-content/80">
                    This operation will read the <strong class="text-accent font-bold">locally saved check-in and check-out values</strong> for employee <strong class="font-mono text-base-content" id="confirmLocalPushEmpCode">-</strong> and update the remote API biometric database without computing new random numbers.
                </p>
            </div>

            <p class="text-xs text-base-content/80 mb-2 font-semibold">
                Records found in local database (<span class="text-accent font-mono" id="confirmLocalPushCount">0</span> days):
            </p>

            <!-- Scrollable List of Affected Dates -->
            <div class="border border-base-200 rounded-xl max-h-48 overflow-y-auto p-2 bg-base-200/40 divide-y divide-base-200 text-xs font-mono mb-4" id="confirmLocalPushDatesList">
                <!-- Dynamically populated -->
            </div>

            <div class="modal-action gap-2">
                <button type="button" class="btn btn-ghost btn-sm sm:btn-md" onclick="document.getElementById('pushLocalDbConfirmModal').close()">Cancel</button>
                <button type="button" class="btn btn-accent btn-sm sm:btn-md gap-2 font-bold" onclick="executePushLocalDb()">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    <span>Confirm &amp; Push to API DB</span>
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Bulk Sync Confirmation Modal (Standard 9h Check-Out Only) -->
    <dialog id="bulkSyncConfirmModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-100 border border-base-200/80 shadow-2xl p-5 sm:p-6 max-w-lg">
            <div class="flex items-center gap-3 text-warning border-b border-base-200 pb-3 mb-4">
                <div class="p-2.5 rounded-xl bg-warning/10">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-base-content">Confirm Auto-Sync (Check-Out Only)</h3>
                    <p class="text-xs text-base-content/60">Adjusts check-out punch to 09h 00m &ndash; 09h 15m from existing check-in</p>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-base-content/80 mb-3">
                This action will compute realistic check-out punches (<strong class="text-base-content">09h 00m &ndash; 09h 15m</strong>) and update the biometric attendance database for all <strong class="text-warning font-mono font-bold" id="confirmBulkShortfallCount">0</strong> shortfall days.
            </p>

            <div class="border border-base-200 rounded-xl max-h-48 overflow-y-auto p-2 bg-base-200/40 divide-y divide-base-200 text-xs font-mono mb-4" id="confirmBulkDatesList">
                <!-- Dynamically populated -->
            </div>

            <div class="alert alert-warning alert-soft text-xs py-2 mb-4">
                <i data-lucide="shield-alert" class="w-4 h-4 shrink-0"></i>
                <span>All biometric modifications are permanently recorded in the administrator audit log.</span>
            </div>

            <div class="modal-action gap-2">
                <button type="button" class="btn btn-ghost btn-sm sm:btn-md" onclick="document.getElementById('bulkSyncConfirmModal').close()">Cancel</button>
                <button type="button" class="btn btn-warning btn-sm sm:btn-md gap-2 font-bold" onclick="executeBulkSync()">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Confirm &amp; Run 9h Sync</span>
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Bulk DB Rule Plan Confirmation Modal (Both In & Out) -->
    <dialog id="bulkDbRuleSyncConfirmModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-100 border border-base-200/80 shadow-2xl p-5 sm:p-6 max-w-lg">
            <div class="flex items-center gap-3 text-primary border-b border-base-200 pb-3 mb-4">
                <div class="p-2.5 rounded-xl bg-primary/10">
                    <i data-lucide="zap" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-base-content">Confirm DB Rule Plan Sync (Both In &amp; Out)</h3>
                    <p class="text-xs text-base-content/60">Synchronize First Punch &amp; Last Punch to our database rule values</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-primary/10 border border-primary/20 mb-3 text-xs">
                <div class="font-bold text-base-content mb-1" id="confirmDbRuleTitle">Active Employee Rule Plan</div>
                <div class="text-base-content/80 font-mono space-y-1">
                    <div>&bull; Check-In: <span class="font-bold text-primary" id="confirmDbRuleIn">Preserves Original (As Is)</span></div>
                    <div>&bull; Target Duration: <span class="font-bold text-success" id="confirmDbRuleDuration">&ge; 9.0 Hours (Check-Out Adjusted to Match)</span></div>
                    <div>&bull; Action: <span class="text-base-content font-bold">Keeps check-in as is, updates check-out to match full shift duration</span></div>
                </div>
            </div>

            <p class="text-xs text-base-content/80 mb-3">
                Will process <strong class="text-primary font-mono font-bold" id="confirmDbRuleShortfallCount">0</strong> days for employee <strong class="font-mono text-base-content" id="confirmDbRuleEmpCode">-</strong>.
            </p>

            <div class="border border-base-200 rounded-xl max-h-44 overflow-y-auto p-2 bg-base-200/40 divide-y divide-base-200 text-xs font-mono mb-4" id="confirmDbRuleDatesList">
                <!-- Dynamically populated -->
            </div>

            <div class="modal-action gap-2">
                <button type="button" class="btn btn-ghost btn-sm sm:btn-md" onclick="document.getElementById('bulkDbRuleSyncConfirmModal').close()">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm sm:btn-md gap-2 font-bold" onclick="executeBulkDbRuleSync()">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Confirm &amp; Sync In &amp; Out (DB Plan)</span>
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Toast Notifications Container -->
    <div id="toastContainer" class="toast toast-end toast-bottom z-50 p-4 space-y-2 pointer-events-none">
        <!-- Dynamic Toast Items -->
    </div>

    @push('scripts')
    <script>
        // Global State Store
        const state = {
            empCode: '{{ $defaultEmpCode }}',
            days: [],
            statistics: null,
            activeRules: @json($activeRules),
            isLoading: false,
            isBulkSyncing: false,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            endpoints: {
                punches: '{{ route('admin.attendance-sync.punches') }}',
                sync: '{{ route('admin.attendance-sync.sync') }}',
                bulkSync: '{{ route('admin.attendance-sync.bulk-sync') }}',
                syncDbRule: '{{ route('admin.attendance-sync.sync-db-rule') }}',
                bulkSyncDbRule: '{{ route('admin.attendance-sync.bulk-sync-db-rule') }}',
                pushLocalToApi: '{{ route('admin.attendance-sync.push-local-to-api') }}',
                saveLocalAttendance: '{{ route('admin.attendance-sync.save-local-attendance') }}'
            }
        };

        // --- Active DB Rule Helper ---
        function getEmployeeActiveRule(code) {
            const clean = String(code || '').trim();
            return state.activeRules.find(r => 
                (r.card_no && String(r.card_no).trim() === clean) ||
                (r.employee_id && String(r.employee_id).trim() === clean)
            ) || null;
        }

        function handleEmployeeCodeChange() {
            const code = document.getElementById('empCodeInput').value.trim();
            updateActiveRuleUI(code);
        }

        function updateActiveRuleUI(code) {
            const rule = getEmployeeActiveRule(code);
            const banner = document.getElementById('activeRuleBanner');
            const titleEl = document.getElementById('ruleBannerTitle');
            const descEl = document.getElementById('ruleBannerDesc');
            const badgeEl = document.getElementById('ruleBannerBadge');

            if (rule) {
                banner.className = 'p-3 rounded-xl bg-info/10 border border-info/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs';
                titleEl.textContent = `DB Override Rule Plan Active for ${rule.employee_name || code}`;
                const minHours = rule.min_duration_hours || 9.0;
                descEl.innerHTML = `Check-In: <strong class="text-primary font-mono">Keeps Original (As Is)</strong> | Target Duration: <strong class="text-success font-mono">&ge; ${minHours}h (Check-Out Adjusted to Match)</strong>`;
                badgeEl.textContent = `Rule #${rule.id} Active`;
                badgeEl.className = 'badge badge-info badge-sm badge-soft font-mono';
            } else {
                banner.className = 'p-3 rounded-xl bg-base-200/50 border border-base-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs opacity-75';
                titleEl.textContent = `Standard 9-Hour Plan (No Custom DB Rule)`;
                descEl.innerHTML = `Check-In: <span class="font-mono">Preserves Original</span> | Check-Out: <strong class="text-warning font-mono">09h 00m - 09h 15m</strong>`;
                badgeEl.textContent = `Standard 9h Plan`;
                badgeEl.className = 'badge badge-neutral badge-sm badge-soft font-mono';
            }
        }

        // --- Presets Helper ---
        function setPreset(type) {
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

            let start, end;
            if (type === 'this_month') {
                start = new Date(now.getFullYear(), now.getMonth(), 1);
                end = now;
            } else if (type === 'last_month') {
                start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                end = new Date(now.getFullYear(), now.getMonth(), 0);
            } else if (type === 'last_30') {
                start = new Date(now.getTime() - (29 * 24 * 60 * 60 * 1000));
                end = now;
            } else if (type === 'this_week') {
                const day = now.getDay();
                const diff = now.getDate() - day + (day === 0 ? -6 : 1); // Monday
                start = new Date(now.setDate(diff));
                end = new Date();
            }

            if (start && end) {
                document.getElementById('startDateInput').value = formatDate(start);
                document.getElementById('endDateInput').value = formatDate(end);
                showToast('Preset date range applied', 'info');
            }
        }

        function resetFilters() {
            setPreset('this_month');
            document.getElementById('empCodeInput').value = '{{ $defaultEmpCode }}';
            updateActiveRuleUI('{{ $defaultEmpCode }}');
            showToast('Filters reset to default', 'info');
        }

        // --- Core 9-Hour Randomized Calculation ---
        function calculateRandomOutTime(firstPunchSqlStr, dateStr) {
            let clean = (firstPunchSqlStr || '').replace('T', ' ').trim();
            if (!clean.includes('-')) {
                clean = `${dateStr} ${clean}`;
            }

            let firstDate = new Date(clean.replace(' ', 'T'));
            if (isNaN(firstDate.getTime())) {
                firstDate = new Date(`${dateStr}T10:00:00`);
            }

            const randMinutes = Math.floor(Math.random() * 15); // 0 to 14
            const randSeconds = Math.floor(Math.random() * 51) + 5; // 5 to 55
            const totalOffsetMs = ((9 * 3600) + (randMinutes * 60) + randSeconds) * 1000;

            const targetDate = new Date(firstDate.getTime() + totalOffsetMs);

            const pad = (n) => String(n).padStart(2, '0');
            const y = targetDate.getFullYear();
            const m = pad(targetDate.getMonth() + 1);
            const d = pad(targetDate.getDate());
            const h = pad(targetDate.getHours());
            const min = pad(targetDate.getMinutes());
            const s = pad(targetDate.getSeconds());

            return `${y}-${m}-${d} ${h}:${min}:${s}`;
        }

        // --- API 1: Fetch Punches & Render Matrix ---
        async function loadAttendanceData() {
            const empCode = document.getElementById('empCodeInput').value.trim();
            const startDate = document.getElementById('startDateInput').value;
            const endDate = document.getElementById('endDateInput').value;

            if (!empCode) {
                showToast('Please provide an employee badge/code.', 'warning');
                document.getElementById('empCodeInput').focus();
                return;
            }

            state.empCode = empCode;
            updateActiveRuleUI(empCode);
            state.isLoading = true;
            updateLoadingUI(true);
            document.getElementById('networkErrorAlert').classList.add('hidden');

            const params = new URLSearchParams({
                emp_code: empCode,
                start_date: startDate,
                end_date: endDate,
                view: 'both'
            });

            try {
                const res = await fetch(`${state.endpoints.punches}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });

                const json = await res.json();

                if (json.status === 1 && json.data) {
                    state.days = json.data.days || [];
                    state.statistics = json.data.statistics || null;
                    renderDashboardData();
                    showToast(`Loaded ${state.days.length} days for employee ${empCode}`, 'success');
                } else {
                    const errMsg = json.message || 'Failed to retrieve attendance records.';
                    showNetworkError(errMsg);
                    showToast(errMsg, 'error');
                }
            } catch (err) {
                console.error('Fetch punches error:', err);
                const errMsg = 'Network or server error while connecting to Biometric API. Please verify VPN connection to 103.25.129.247.';
                showNetworkError(errMsg);
                showToast(errMsg, 'error');
            } finally {
                state.isLoading = false;
                updateLoadingUI(false);
            }
        }

        function updateLoadingUI(isLoading) {
            const btn = document.getElementById('loadAttendanceBtn');
            const icon = document.getElementById('loadBtnIcon');
            const text = document.getElementById('loadBtnText');

            if (isLoading) {
                btn.disabled = true;
                icon.className = 'loading loading-spinner loading-xs';
                text.textContent = 'Loading...';
            } else {
                btn.disabled = false;
                icon.className = 'w-4 h-4';
                icon.setAttribute('data-lucide', 'refresh-cw');
                text.textContent = 'Load Attendance';
                if (window.renderLucideIcons) window.renderLucideIcons();
            }
        }

        function showNetworkError(msg) {
            const alertBox = document.getElementById('networkErrorAlert');
            document.getElementById('networkErrorMessage').textContent = msg;
            alertBox.classList.remove('hidden');
        }

        // --- Render UI (KPIs and Table) ---
        function renderDashboardData() {
            const totalDays = state.days.length;
            const completedDays = state.days.filter(d => d.is_nine_hours).length;
            const shortfallDays = totalDays - completedDays;
            const localDbDays = state.days.filter(d => d.local_db && d.local_db.exists).length;

            // Update KPI Cards
            document.getElementById('kpiContainer').classList.remove('hidden');
            document.getElementById('kpiTotalDays').textContent = totalDays;
            
            const totalPunchesCount = state.days.reduce((acc, d) => acc + (d.punch_count || 0), 0);
            document.getElementById('kpiTotalPunches').textContent = `${totalPunchesCount} punches`;

            document.getElementById('kpiCompletedDays').textContent = completedDays;
            const compPct = totalDays > 0 ? Math.round((completedDays / totalDays) * 100) : 0;
            document.getElementById('kpiCompletedPct').textContent = `${compPct}%`;

            document.getElementById('kpiShortfallDays').textContent = shortfallDays;
            document.getElementById('kpiLocalDbCount').textContent = localDbDays;

            const bulkBadgeCount = document.getElementById('bulkShortfallBadgeCount');
            if (bulkBadgeCount) bulkBadgeCount.textContent = `${shortfallDays} Shortfall Days`;

            const localDbBadgeCount = document.getElementById('localDbBadgeCount');
            if (localDbBadgeCount) localDbBadgeCount.textContent = `${localDbDays} Ready in DB`;

            // Update Table
            const tbody = document.getElementById('attendanceTableBody');
            document.getElementById('tableRowCountBadge').textContent = `${totalDays} Days`;

            if (totalDays === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-12 text-center text-base-content/50">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                            <div class="font-semibold">No attendance punches found for this period.</div>
                            <div class="text-xs text-base-content/60">Try adjusting the date range or check employee badge number.</div>
                        </td>
                    </tr>
                `;
                if (window.renderLucideIcons) window.renderLucideIcons();
                return;
            }

            // Render Rows
            tbody.innerHTML = state.days.map((day) => {
                const isCompleted = !!day.is_nine_hours;
                const isSinglePunch = day.status === 'single_punch' || day.punch_count === 1 || !day.last_punch;
                const rowId = `att-row-${day.date}`;
                const hasLocal = !!(day.local_db && day.local_db.exists);

                // Status Badge
                let statusBadgeHtml = '';
                if (day.is_pushed_from_local) {
                    statusBadgeHtml = `<span class="badge badge-accent badge-soft font-mono gap-1 text-xs"><i data-lucide="check-check" class="w-3 h-3"></i> Pushed from DB</span>`;
                } else if (day.is_db_synced) {
                    statusBadgeHtml = `<span class="badge badge-primary badge-soft font-mono gap-1 text-xs"><i data-lucide="check-check" class="w-3 h-3"></i> DB Plan Synced</span>`;
                } else if (isCompleted) {
                    statusBadgeHtml = `<span class="badge badge-success badge-soft font-mono gap-1 text-xs"><i data-lucide="check" class="w-3 h-3"></i> &ge; 9 Hours</span>`;
                } else if (isSinglePunch) {
                    statusBadgeHtml = `<span class="badge badge-error badge-soft font-mono gap-1 text-xs"><i data-lucide="log-in" class="w-3 h-3"></i> 1 Punch (Missing Out)</span>`;
                } else {
                    statusBadgeHtml = `<span class="badge badge-warning badge-soft font-mono gap-1 text-xs"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Shortfall (&lt; 9h)</span>`;
                }

                const firstTime = day.first_punch ? (day.first_punch.time || day.first_punch.datetime.split(' ')[1]) : '--:--:--';
                const firstFull = day.first_punch ? day.first_punch.datetime : '';
                
                let lastTime = '--:--:--';
                let lastFull = '';
                if (day.last_punch && !isSinglePunch) {
                    lastTime = day.last_punch.time || day.last_punch.datetime.split(' ')[1];
                    lastFull = day.last_punch.datetime;
                }

                const durationFormatted = day.duration?.formatted || (isSinglePunch ? '00h 00m (In Only)' : '00h 00m 00s');

                // Local DB info subtext
                let localDbSubtext = '';
                if (hasLocal) {
                    const lIn = day.local_db.check_in || '--';
                    const lOut = day.local_db.check_out || '--';
                    localDbSubtext = `
                        <div class="mt-1 text-[11px] font-mono text-accent flex items-center gap-1 font-semibold">
                            <i data-lucide="database" class="w-3 h-3"></i>
                            <span>Local DB: ${lIn} &rarr; ${lOut}</span>
                        </div>
                    `;
                }

                return `
                    <tr id="${rowId}" class="hover:bg-base-200/40 transition-colors ${!isCompleted ? 'bg-warning/5' : ''}">
                        <!-- Date & Day -->
                        <td class="py-3 px-4">
                            <div class="font-bold font-mono text-base-content text-sm">${day.date}</div>
                            <div class="text-[11px] text-base-content/60 font-semibold uppercase">${day.day_of_week || ''}</div>
                        </td>

                        <!-- Punch Logs Count -->
                        <td class="py-3 px-3 text-center">
                            <span class="badge badge-sm badge-neutral font-mono">${day.punch_count || 1} logs</span>
                        </td>

                        <!-- 1st Entry (Check-In) -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-1.5 font-mono font-semibold text-info text-sm">
                                <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                                <span class="first-punch-time">${firstTime}</span>
                            </div>
                            <div class="text-[10px] text-base-content/40 font-mono">API Check-In</div>
                            ${hasLocal ? `<div class="text-[10px] text-accent font-mono font-semibold">Local: ${day.local_db.check_in || 'None'}</div>` : ''}
                        </td>

                        <!-- Last Entry (Check-Out) -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-1.5 font-mono font-semibold ${lastTime !== '--:--:--' ? 'text-base-content' : 'text-error'} text-sm">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 opacity-60"></i>
                                <span class="last-punch-time">${lastTime !== '--:--:--' ? lastTime : 'None (Missing)'}</span>
                            </div>
                            <div class="text-[10px] text-base-content/40 font-mono">API Check-Out</div>
                            ${hasLocal ? `<div class="text-[10px] text-accent font-mono font-semibold">Local: ${day.local_db.check_out || 'None'}</div>` : ''}
                        </td>

                        <!-- Total Duration -->
                        <td class="py-3 px-3">
                            <div class="font-mono font-bold text-sm ${isCompleted ? 'text-success' : 'text-warning'} duration-formatted">
                                ${durationFormatted}
                            </div>
                            <div class="text-[10px] text-base-content/40 font-mono">
                                ${day.duration?.decimal_hours ? `${day.duration.decimal_hours} hrs` : (isSinglePunch ? 'Single Punch' : 'Shortfall')}
                            </div>
                        </td>

                        <!-- Status Badge & Local DB Pill -->
                        <td class="py-3 px-3 text-center status-badge-cell">
                            ${statusBadgeHtml}
                            ${localDbSubtext}
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 action-buttons-group">
                                <!-- PRIMARY REQUESTED ACTION: Push Locally Saved DB Values to API DB -->
                                <button 
                                    type="button" 
                                    class="btn btn-xs ${hasLocal ? 'btn-accent' : 'btn-outline btn-ghost text-base-content/40'} gap-1 font-mono push-local-btn" 
                                    title="${hasLocal ? 'Push locally saved DB values to API DB' : 'No local DB record yet for this date'}"
                                    onclick="triggerSingleLocalPush('${day.date}')"
                                    ${!hasLocal ? 'disabled' : ''}
                                >
                                    <i data-lucide="upload-cloud" class="w-3 h-3"></i>
                                    <span>Push DB</span>
                                </button>

                                <!-- Option 2: Standard 9h Out Sync -->
                                <button 
                                    type="button" 
                                    class="btn btn-xs ${isCompleted ? 'btn-ghost text-base-content/50' : 'btn-warning'} gap-1 font-mono sync-single-btn" 
                                    title="Auto-Sync Check-Out to 09h00m - 09h15m"
                                    onclick="triggerSingleSync('${day.date}', '${firstFull}')"
                                >
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    <span>9h Out</span>
                                </button>

                                <!-- Option 3: Manual Adjustment -->
                                <button 
                                    type="button" 
                                    class="btn btn-xs btn-outline btn-ghost gap-1" 
                                    title="Manually adjust punches and save locally"
                                    onclick="openManualAdjustModal('${day.date}', '${firstFull}', '${lastFull}')"
                                >
                                    <i data-lucide="edit-2" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        // --- PRIMARY FEATURE: Push Single Day Local DB Values to Remote API DB ---
        async function triggerSingleLocalPush(dateStr) {
            const dayObj = state.days.find(d => d.date === dateStr);
            if (!dayObj || !dayObj.local_db || !dayObj.local_db.exists) {
                showToast(`No local DB attendance record found for ${dateStr}. Save locally first.`, 'warning');
                return;
            }

            const row = document.getElementById(`att-row-${dateStr}`);
            const pushBtn = row ? row.querySelector('.push-local-btn') : null;

            if (pushBtn) {
                pushBtn.disabled = true;
                pushBtn.innerHTML = `<span class="loading loading-spinner loading-xs"></span>`;
            }

            try {
                const formData = new URLSearchParams();
                formData.append('emp_code', state.empCode);
                formData.append('date', dateStr);

                const res = await fetch(state.endpoints.pushLocalToApi, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': state.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData.toString()
                });

                const json = await res.json();

                if (json.status === 1) {
                    const lIn = dayObj.local_db.check_in_full || `${dateStr} ${dayObj.local_db.check_in}`;
                    const lOut = dayObj.local_db.check_out_full || `${dateStr} ${dayObj.local_db.check_out}`;
                    const dur = dayObj.local_db.total_time || 'Synced from DB';

                    updateRowInDomBoth(dateStr, lIn, lOut, dur);
                    dayObj.is_pushed_from_local = true;

                    // Update badge to Pushed from DB
                    const badgeCell = row.querySelector('.status-badge-cell');
                    if (badgeCell) {
                        badgeCell.innerHTML = `
                            <span class="badge badge-accent badge-soft font-mono gap-1 text-xs">
                                <i data-lucide="check-check" class="w-3 h-3"></i> 
                                Pushed from DB
                            </span>
                            <div class="mt-1 text-[11px] font-mono text-accent flex items-center gap-1 font-semibold">
                                <i data-lucide="database" class="w-3 h-3"></i>
                                <span>Local DB: ${dayObj.local_db.check_in} &rarr; ${dayObj.local_db.check_out}</span>
                            </div>
                        `;
                    }

                    showToast(`API DB updated with locally saved values for ${dateStr}!`, 'success');
                } else {
                    showToast(`Error pushing to API DB: ${json.message || 'API rejected update'}`, 'error');
                }
            } catch (err) {
                console.error('Push local to API error:', err);
                showToast(`Network error pushing ${dateStr} to API`, 'error');
            } finally {
                if (pushBtn) {
                    pushBtn.disabled = false;
                    pushBtn.className = 'btn btn-xs btn-accent gap-1 font-mono push-local-btn';
                    pushBtn.innerHTML = `<i data-lucide="check" class="w-3 h-3"></i><span>Pushed</span>`;
                    if (window.renderLucideIcons) window.renderLucideIcons();
                }
            }
        }

        // --- PRIMARY FEATURE: Confirmation Dialog for Bulk Push Local DB -> API DB ---
        function promptPushLocalDbConfirmation() {
            const localDays = state.days.filter(d => d.local_db && d.local_db.exists);
            if (localDays.length === 0) {
                showToast('No locally saved attendance records found for this employee in the date range.', 'warning');
                return;
            }

            document.getElementById('confirmLocalPushCount').textContent = localDays.length;
            document.getElementById('confirmLocalPushEmpCode').textContent = state.empCode;

            const listEl = document.getElementById('confirmLocalPushDatesList');
            listEl.innerHTML = localDays.map(d => `
                <div class="py-1.5 px-2 flex justify-between items-center text-xs">
                    <div>
                        <span class="font-bold text-base-content">${d.date} (${d.day_of_week || ''})</span>
                    </div>
                    <div class="font-mono text-accent font-semibold">
                        In: ${d.local_db.check_in || '--'} | Out: ${d.local_db.check_out || '--'}
                    </div>
                    <span class="badge badge-accent badge-xs font-mono">From MySQL DB</span>
                </div>
            `).join('');

            document.getElementById('pushLocalDbConfirmModal').showModal();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        async function executePushLocalDb() {
            document.getElementById('pushLocalDbConfirmModal').close();

            const startDate = document.getElementById('startDateInput').value;
            const endDate = document.getElementById('endDateInput').value;

            state.isBulkSyncing = true;
            const progressBox = document.getElementById('bulkProgressContainer');
            const progressBar = document.getElementById('bulkProgressBar');
            const progressText = document.getElementById('bulkProgressText');
            const progressStatus = document.getElementById('bulkProgressStatus');
            const pushBtn = document.getElementById('pushLocalDbBtn');

            progressBox.classList.remove('hidden');
            pushBtn.disabled = true;
            progressBar.className = 'progress progress-accent w-full h-3';

            progressStatus.textContent = 'Updating API DB with locally saved values...';
            progressBar.value = 50;

            try {
                const formData = new URLSearchParams();
                formData.append('emp_code', state.empCode);
                formData.append('start_date', startDate);
                formData.append('end_date', endDate);

                const res = await fetch(state.endpoints.pushLocalToApi, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': state.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData.toString()
                });

                const json = await res.json();
                progressBar.value = 100;
                progressText.textContent = `100%`;

                if (json.status === 1) {
                    const results = json.data?.results || [];
                    results.forEach(r => {
                        if (r.status === 'success') {
                            updateRowInDomBoth(r.date, r.first_time, r.last_time, r.duration || 'Pushed from DB');
                            const dayObj = state.days.find(d => d.date === r.date);
                            if (dayObj) dayObj.is_pushed_from_local = true;
                        }
                    });

                    progressStatus.textContent = `Completed! ${json.data.success_count} dates pushed to API database.`;
                    showToast(json.message, 'success');
                } else {
                    progressStatus.textContent = `Failed: ${json.message}`;
                    showToast(json.message || 'Push failed', 'error');
                }
            } catch (err) {
                console.error('Bulk push local to API error:', err);
                showToast('Network error while pushing local DB to API', 'error');
            } finally {
                state.isBulkSyncing = false;
                pushBtn.disabled = false;
                setTimeout(() => {
                    progressBox.classList.add('hidden');
                }, 4000);
            }
        }

        // --- Single Day: 9h Check-Out Sync ---
        async function triggerSingleSync(dateStr, firstPunchFull) {
            const dayObj = state.days.find(d => d.date === dateStr);
            if (!dayObj) return;

            const firstTime = firstPunchFull || (dayObj.first_punch ? dayObj.first_punch.datetime : `${dateStr} 10:00:00`);
            const targetOutTime = calculateRandomOutTime(firstTime, dateStr);

            const row = document.getElementById(`att-row-${dateStr}`);
            const syncBtn = row ? row.querySelector('.sync-single-btn') : null;

            if (syncBtn) {
                syncBtn.disabled = true;
                syncBtn.innerHTML = `<span class="loading loading-spinner loading-xs"></span>`;
            }

            try {
                const formData = new URLSearchParams();
                formData.append('emp_code', state.empCode);
                formData.append('date', dateStr);
                formData.append('first_time', firstTime);
                formData.append('last_time', targetOutTime);
                formData.append('terminal_alias', 'AUTO_SYNC');

                const res = await fetch(state.endpoints.sync, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': state.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData.toString()
                });

                const json = await res.json();

                if (json.status === 1) {
                    const dur = json.data?.duration_formatted || '09h 08m 15s';
                    updateRowInDom(dateStr, targetOutTime, dur, true);
                    showToast(`Date ${dateStr} synced to ${dur}`, 'success');
                } else {
                    showToast(`Error syncing ${dateStr}: ${json.message || 'API rejected update'}`, 'error');
                }
            } catch (err) {
                console.error('Sync day error:', err);
                showToast(`Network error syncing ${dateStr}`, 'error');
            } finally {
                if (syncBtn) {
                    syncBtn.disabled = false;
                    syncBtn.className = 'btn btn-xs btn-ghost text-base-content/50 gap-1 font-mono sync-single-btn';
                    syncBtn.innerHTML = `<i data-lucide="check" class="w-3 h-3 text-success"></i><span>Synced</span>`;
                    if (window.renderLucideIcons) window.renderLucideIcons();
                }
            }
        }

        // --- DOM Live Row Update (Check-Out Only) ---
        function updateRowInDom(dateStr, newOutTimestamp, newDurationFormatted, isSyncedBadge = true) {
            const row = document.getElementById(`att-row-${dateStr}`);
            if (!row) return;

            const outTimeOnly = newOutTimestamp.includes(' ') ? newOutTimestamp.split(' ')[1] : newOutTimestamp;

            const lastPunchSpan = row.querySelector('.last-punch-time');
            if (lastPunchSpan) {
                lastPunchSpan.textContent = outTimeOnly;
                lastPunchSpan.parentElement.className = 'flex items-center gap-1.5 font-mono font-semibold text-base-content text-sm';
            }

            const durCell = row.querySelector('.duration-formatted');
            if (durCell) {
                durCell.textContent = newDurationFormatted;
                durCell.className = 'font-mono font-bold text-sm text-success duration-formatted';
            }

            const badgeCell = row.querySelector('.status-badge-cell');
            if (badgeCell) {
                badgeCell.innerHTML = `
                    <span class="badge badge-success badge-soft font-mono gap-1 text-xs">
                        <i data-lucide="check-check" class="w-3 h-3"></i> 
                        &ge; 9 Hours ${isSyncedBadge ? '(Synced)' : ''}
                    </span>
                `;
            }

            row.classList.remove('bg-warning/5');

            const dayObj = state.days.find(d => d.date === dateStr);
            if (dayObj) {
                dayObj.is_nine_hours = true;
                dayObj.status = 'completed';
                if (!dayObj.last_punch) {
                    dayObj.last_punch = { datetime: newOutTimestamp, time: outTimeOnly, punch_state: 1 };
                } else {
                    dayObj.last_punch.datetime = newOutTimestamp;
                    dayObj.last_punch.time = outTimeOnly;
                }
                if (!dayObj.duration) dayObj.duration = {};
                dayObj.duration.formatted = newDurationFormatted;
            }

            recalculateKpiSummary();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        // --- DOM Live Row Update (BOTH Check-In & Check-Out) ---
        function updateRowInDomBoth(dateStr, newInTimestamp, newOutTimestamp, newDurationFormatted) {
            const row = document.getElementById(`att-row-${dateStr}`);
            if (!row) return;

            const inTimeOnly = newInTimestamp.includes(' ') ? newInTimestamp.split(' ')[1] : newInTimestamp;
            const outTimeOnly = newOutTimestamp.includes(' ') ? newOutTimestamp.split(' ')[1] : newOutTimestamp;

            const firstPunchSpan = row.querySelector('.first-punch-time');
            if (firstPunchSpan) {
                firstPunchSpan.textContent = inTimeOnly;
                firstPunchSpan.parentElement.className = 'flex items-center gap-1.5 font-mono font-semibold text-primary text-sm';
            }

            const lastPunchSpan = row.querySelector('.last-punch-time');
            if (lastPunchSpan) {
                lastPunchSpan.textContent = outTimeOnly;
                lastPunchSpan.parentElement.className = 'flex items-center gap-1.5 font-mono font-semibold text-base-content text-sm';
            }

            const durCell = row.querySelector('.duration-formatted');
            if (durCell) {
                durCell.textContent = newDurationFormatted;
                durCell.className = 'font-mono font-bold text-sm text-success duration-formatted';
            }

            row.classList.remove('bg-warning/5');

            const dayObj = state.days.find(d => d.date === dateStr);
            if (dayObj) {
                dayObj.is_nine_hours = true;
                dayObj.status = 'completed';
                if (!dayObj.first_punch) {
                    dayObj.first_punch = { datetime: newInTimestamp, time: inTimeOnly, punch_state: 0 };
                } else {
                    dayObj.first_punch.datetime = newInTimestamp;
                    dayObj.first_punch.time = inTimeOnly;
                }
                if (!dayObj.last_punch) {
                    dayObj.last_punch = { datetime: newOutTimestamp, time: outTimeOnly, punch_state: 1 };
                } else {
                    dayObj.last_punch.datetime = newOutTimestamp;
                    dayObj.last_punch.time = outTimeOnly;
                }
                if (!dayObj.duration) dayObj.duration = {};
                dayObj.duration.formatted = newDurationFormatted;
            }

            recalculateKpiSummary();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        function recalculateKpiSummary() {
            const total = state.days.length;
            const completed = state.days.filter(d => d.is_nine_hours).length;
            const shortfall = total - completed;

            document.getElementById('kpiCompletedDays').textContent = completed;
            const compPct = total > 0 ? Math.round((completed / total) * 100) : 0;
            document.getElementById('kpiCompletedPct').textContent = `${compPct}%`;

            document.getElementById('kpiShortfallDays').textContent = shortfall;

            const bulkBadgeCount = document.getElementById('bulkShortfallBadgeCount');
            if (bulkBadgeCount) bulkBadgeCount.textContent = `${shortfall} Shortfall Days`;
        }

        // --- Manual Adjustment Modal Handling ---
        function openManualAdjustModal(dateStr, firstPunchFull, lastPunchFull) {
            const dayObj = state.days.find(d => d.date === dateStr);
            if (!dayObj) return;

            const modal = document.getElementById('manualAdjustModal');
            document.getElementById('modalTargetDate').value = dateStr;
            document.getElementById('modalTargetEmpCode').value = state.empCode;
            document.getElementById('modalDisplayDate').textContent = dateStr;
            document.getElementById('modalDisplayEmpCode').textContent = state.empCode;

            const firstTime = firstPunchFull || (dayObj.first_punch ? dayObj.first_punch.datetime : `${dateStr} 10:00:00`);
            document.getElementById('modalDisplayFirstPunch').textContent = firstTime;

            // Set In input
            const inInput = document.getElementById('modalFirstPunchInput');
            let initialIn = firstPunchFull || (dayObj.first_punch ? dayObj.first_punch.datetime : `${dateStr} 10:00:00`);
            if (dayObj.local_db && dayObj.local_db.exists && dayObj.local_db.check_in_full) {
                initialIn = dayObj.local_db.check_in_full;
            }
            inInput.value = initialIn.replace(' ', 'T');

            // Set Out input
            const outInput = document.getElementById('modalLastPunchInput');
            let initialOut = lastPunchFull || (dayObj.last_punch ? dayObj.last_punch.datetime : '');
            if (dayObj.local_db && dayObj.local_db.exists && dayObj.local_db.check_out_full) {
                initialOut = dayObj.local_db.check_out_full;
            } else if (!initialOut || dayObj.status === 'single_punch') {
                initialOut = calculateRandomOutTime(firstTime, dateStr);
            }
            outInput.value = initialOut.replace(' ', 'T');

            // Local DB display
            const localDbDisplay = document.getElementById('modalDisplayLocalDb');
            if (dayObj.local_db && dayObj.local_db.exists) {
                localDbDisplay.textContent = `${dayObj.local_db.check_in} -> ${dayObj.local_db.check_out}`;
            } else {
                localDbDisplay.textContent = 'None saved in MySQL yet';
            }

            updateModalEstimatedDuration();
            inInput.oninput = updateModalEstimatedDuration;
            outInput.oninput = updateModalEstimatedDuration;

            modal.showModal();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        function fillSuggestedInModal() {
            const dateStr = document.getElementById('modalTargetDate').value;
            const inVal = document.getElementById('modalFirstPunchInput').value.replace('T', ' ');
            const suggested = calculateRandomOutTime(inVal, dateStr);
            
            document.getElementById('modalLastPunchInput').value = suggested.replace(' ', 'T');
            updateModalEstimatedDuration();
            showToast('Populated 9h suggested punch time', 'info');
        }

        function updateModalEstimatedDuration() {
            const inVal = document.getElementById('modalFirstPunchInput').value;
            const lastVal = document.getElementById('modalLastPunchInput').value;
            const previewEl = document.getElementById('modalEstimatedDuration');

            if (!inVal || !lastVal) {
                previewEl.textContent = '--';
                return;
            }

            const tFirst = new Date(inVal).getTime();
            const tLast = new Date(lastVal).getTime();

            if (isNaN(tFirst) || isNaN(tLast) || tLast <= tFirst) {
                previewEl.textContent = 'Invalid / Out before In';
                previewEl.className = 'font-mono font-bold text-error text-sm';
                return;
            }

            const diffSec = Math.floor((tLast - tFirst) / 1000);
            const h = String(Math.floor(diffSec / 3600)).padStart(2, '0');
            const m = String(Math.floor((diffSec % 3600) / 60)).padStart(2, '0');
            const s = String(diffSec % 60).padStart(2, '0');

            const durStr = `${h}h ${m}m ${s}s`;
            previewEl.textContent = durStr;
            previewEl.className = `font-mono font-bold ${diffSec >= 32400 ? 'text-success' : 'text-warning'} text-sm`;
        }

        // Save to Local DB Only (without calling remote API)
        async function submitSaveLocalOnly() {
            const dateStr = document.getElementById('modalTargetDate').value;
            const empCode = document.getElementById('modalTargetEmpCode').value;
            const inVal = document.getElementById('modalFirstPunchInput').value.replace('T', ' ');
            const outVal = document.getElementById('modalLastPunchInput').value.replace('T', ' ');

            const saveBtn = document.getElementById('modalSaveLocalOnlyBtn');
            saveBtn.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('emp_code', empCode);
                formData.append('date', dateStr);
                formData.append('check_in_datetime', inVal);
                formData.append('check_out_datetime', outVal);

                const res = await fetch(state.endpoints.saveLocalAttendance, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': state.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData.toString()
                });

                const json = await res.json();
                if (json.status === 1) {
                    const dayObj = state.days.find(d => d.date === dateStr);
                    if (dayObj) {
                        dayObj.local_db = {
                            exists: true,
                            check_in: inVal.split(' ')[1],
                            check_in_full: inVal,
                            check_out: outVal.split(' ')[1],
                            check_out_full: outVal,
                            total_time: json.data?.total_time
                        };
                    }
                    renderDashboardData();
                    document.getElementById('manualAdjustModal').close();
                    showToast(`Saved locally in MySQL DB for ${dateStr}!`, 'success');
                } else {
                    showToast(json.message || 'Error saving locally', 'error');
                }
            } catch (err) {
                showToast('Network error saving locally', 'error');
            } finally {
                saveBtn.disabled = false;
            }
        }

        // Save locally and push to Remote API DB
        async function submitManualAdjust() {
            const dateStr = document.getElementById('modalTargetDate').value;
            const empCode = document.getElementById('modalTargetEmpCode').value;
            const firstTime = document.getElementById('modalFirstPunchInput').value.replace('T', ' ');
            const lastTime = document.getElementById('modalLastPunchInput').value.replace('T', ' ');

            const saveBtn = document.getElementById('modalSaveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = `<span class="loading loading-spinner loading-xs"></span> Saving...`;

            try {
                const formData = new URLSearchParams();
                formData.append('emp_code', empCode);
                formData.append('date', dateStr);
                formData.append('first_time', firstTime);
                formData.append('last_time', lastTime);
                formData.append('terminal_alias', 'MANUAL_ADJUST');

                const res = await fetch(state.endpoints.sync, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': state.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData.toString()
                });

                const json = await res.json();

                if (json.status === 1) {
                    const dur = json.data?.duration_formatted || document.getElementById('modalEstimatedDuration').textContent;
                    updateRowInDomBoth(dateStr, firstTime, lastTime, dur);
                    document.getElementById('manualAdjustModal').close();
                    showToast(`Updated both in API DB & Local DB for ${dateStr} (${dur})`, 'success');
                } else {
                    showToast(`Failed to update punch: ${json.message || 'API error'}`, 'error');
                }
            } catch (err) {
                console.error('Manual adjust error:', err);
                showToast('Network error while saving punch', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = `<i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i><span>Save &amp; Push to API DB</span>`;
                if (window.renderLucideIcons) window.renderLucideIcons();
            }
        }

        // --- Bulk Sync Mode 1: 9h Check-Out Sync ---
        function promptBulkSyncConfirmation() {
            const shortfallDays = state.days.filter(d => !d.is_nine_hours);
            if (shortfallDays.length === 0) {
                showToast('No shortfall days found to synchronize.', 'info');
                return;
            }

            document.getElementById('confirmBulkShortfallCount').textContent = shortfallDays.length;
            const listEl = document.getElementById('confirmBulkDatesList');
            listEl.innerHTML = shortfallDays.map(d => `
                <div class="py-1 px-2 flex justify-between items-center text-xs">
                    <span class="font-bold text-base-content">${d.date} (${d.day_of_week || ''})</span>
                    <span class="text-base-content/60">${d.duration?.formatted || 'Single Punch / Shortfall'}</span>
                    <span class="badge badge-warning badge-xs font-mono">Target: 09h 00m-15m</span>
                </div>
            `).join('');

            document.getElementById('bulkSyncConfirmModal').showModal();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        async function executeBulkSync() {
            document.getElementById('bulkSyncConfirmModal').close();

            const shortfallDays = state.days.filter(d => !d.is_nine_hours);
            if (shortfallDays.length === 0) return;

            state.isBulkSyncing = true;
            const progressBox = document.getElementById('bulkProgressContainer');
            const progressBar = document.getElementById('bulkProgressBar');
            const progressText = document.getElementById('bulkProgressText');
            const progressStatus = document.getElementById('bulkProgressStatus');
            const bulkBtn = document.getElementById('bulkSyncBtn');

            progressBox.classList.remove('hidden');
            bulkBtn.disabled = true;
            progressBar.className = 'progress progress-warning w-full h-3';

            const total = shortfallDays.length;
            let current = 0;
            let successCount = 0;
            let failedCount = 0;

            for (const day of shortfallDays) {
                current++;
                const pct = Math.round((current / total) * 100);
                progressBar.value = pct;
                progressText.textContent = `${current} / ${total} (${pct}%)`;
                progressStatus.textContent = `Syncing date ${day.date}...`;

                const firstTime = day.first_punch ? day.first_punch.datetime : `${day.date} 10:00:00`;
                const targetOut = calculateRandomOutTime(firstTime, day.date);

                try {
                    const formData = new URLSearchParams();
                    formData.append('emp_code', state.empCode);
                    formData.append('date', day.date);
                    formData.append('first_time', firstTime);
                    formData.append('last_time', targetOut);
                    formData.append('terminal_alias', 'BULK_AUTO_SYNC');

                    const res = await fetch(state.endpoints.sync, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': state.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData.toString()
                    });

                    const json = await res.json();
                    if (json.status === 1) {
                        successCount++;
                        const dur = json.data?.duration_formatted || '09h 08m 15s';
                        updateRowInDom(day.date, targetOut, dur, true);
                    } else {
                        failedCount++;
                    }
                } catch (e) {
                    failedCount++;
                }

                await new Promise(r => setTimeout(r, 200));
            }

            state.isBulkSyncing = false;
            bulkBtn.disabled = false;
            progressStatus.textContent = `Completed! ${successCount} synced, ${failedCount} failed.`;
            showToast(`Bulk Sync Complete: ${successCount} synced successfully`, successCount > 0 ? 'success' : 'error');

            setTimeout(() => {
                progressBox.classList.add('hidden');
            }, 4000);
        }

        // --- Bulk Sync Mode 2: Sync via DB Rule Plan ---
        function promptBulkDbRuleSyncConfirmation() {
            const shortfallDays = state.days.filter(d => !d.is_nine_hours);
            if (shortfallDays.length === 0) {
                showToast('No shortfall days found to synchronize.', 'info');
                return;
            }

            const rule = getEmployeeActiveRule(state.empCode);
            document.getElementById('confirmDbRuleShortfallCount').textContent = shortfallDays.length;
            document.getElementById('confirmDbRuleEmpCode').textContent = state.empCode;

            if (rule) {
                document.getElementById('confirmDbRuleTitle').textContent = `Active DB Rule #${rule.id} for ${rule.employee_name || state.empCode}`;
                const minH = rule.min_duration_hours || 9.0;
                document.getElementById('confirmDbRuleIn').textContent = `Keeps Original (As Is)`;
                document.getElementById('confirmDbRuleDuration').textContent = `≥ ${minH}h (Check-Out Adjusted)`;
            } else {
                document.getElementById('confirmDbRuleTitle').textContent = `Default 9-Hour Plan (Fallback)`;
                document.getElementById('confirmDbRuleIn').textContent = `Keeps Original (As Is)`;
                document.getElementById('confirmDbRuleDuration').textContent = `≥ 9.0 Hours`;
            }

            const listEl = document.getElementById('confirmDbRuleDatesList');
            listEl.innerHTML = shortfallDays.map(d => `
                <div class="py-1 px-2 flex justify-between items-center text-xs">
                    <span class="font-bold text-base-content">${d.date} (${d.day_of_week || ''})</span>
                    <span class="text-primary font-semibold">Align In &amp; Out</span>
                    <span class="badge badge-primary badge-xs font-mono">DB Plan</span>
                </div>
            `).join('');

            document.getElementById('bulkDbRuleSyncConfirmModal').showModal();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        async function executeBulkDbRuleSync() {
            document.getElementById('bulkDbRuleSyncConfirmModal').close();

            const shortfallDays = state.days.filter(d => !d.is_nine_hours);
            if (shortfallDays.length === 0) return;

            state.isBulkSyncing = true;
            const progressBox = document.getElementById('bulkProgressContainer');
            const progressBar = document.getElementById('bulkProgressBar');
            const progressText = document.getElementById('bulkProgressText');
            const progressStatus = document.getElementById('bulkProgressStatus');
            const bulkDbBtn = document.getElementById('bulkDbRuleSyncBtn');

            progressBox.classList.remove('hidden');
            bulkDbBtn.disabled = true;
            progressBar.className = 'progress progress-primary w-full h-3';

            const total = shortfallDays.length;
            let current = 0;
            let successCount = 0;
            let failedCount = 0;

            for (const day of shortfallDays) {
                current++;
                const pct = Math.round((current / total) * 100);
                progressBar.value = pct;
                progressText.textContent = `${current} / ${total} (${pct}%)`;
                progressStatus.textContent = `Syncing date ${day.date} via DB Rule...`;

                const firstTime = day.first_punch ? day.first_punch.datetime : null;

                try {
                    const formData = new URLSearchParams();
                    formData.append('emp_code', state.empCode);
                    formData.append('date', day.date);
                    if (firstTime) {
                        formData.append('first_time', firstTime);
                    }

                    const res = await fetch(state.endpoints.syncDbRule, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': state.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData.toString()
                    });

                    const json = await res.json();
                    if (json.status === 1 && json.data) {
                        successCount++;
                        const newIn = json.data.first_punch?.punch_time || '';
                        const newOut = json.data.last_punch?.punch_time || '';
                        const dur = json.data.duration_formatted || '09h 10m 00s';
                        updateRowInDomBoth(day.date, newIn, newOut, dur);
                    } else {
                        failedCount++;
                    }
                } catch (e) {
                    failedCount++;
                }

                await new Promise(r => setTimeout(r, 200));
            }

            state.isBulkSyncing = false;
            bulkDbBtn.disabled = false;
            progressStatus.textContent = `Completed DB Plan Sync! ${successCount} synced, ${failedCount} failed.`;
            showToast(`DB Plan Bulk Sync Complete: ${successCount} synced successfully`, successCount > 0 ? 'success' : 'error');

            setTimeout(() => {
                progressBox.classList.add('hidden');
            }, 4000);
        }

        // --- Toast System ---
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const id = 'toast-' + Date.now();
            const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-error' : (type === 'warning' ? 'alert-warning' : 'alert-info'));
            const iconName = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-circle' : (type === 'warning' ? 'alert-triangle' : 'info'));

            const toastItem = document.createElement('div');
            toastItem.id = id;
            toastItem.className = `alert ${alertClass} shadow-lg py-2.5 px-4 text-xs font-semibold rounded-xl flex items-center gap-2 pointer-events-auto transform transition-all duration-300 opacity-0 translate-y-2`;
            toastItem.innerHTML = `
                <i data-lucide="${iconName}" class="w-4 h-4 shrink-0"></i>
                <span>${message}</span>
            `;

            container.appendChild(toastItem);
            if (window.renderLucideIcons) window.renderLucideIcons();

            requestAnimationFrame(() => {
                toastItem.classList.remove('opacity-0', 'translate-y-2');
            });

            setTimeout(() => {
                toastItem.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toastItem.remove(), 300);
            }, 4000);
        }

        // --- Initial Auto-Load ---
        document.addEventListener('DOMContentLoaded', () => {
            if (window.renderLucideIcons) window.renderLucideIcons();
            updateActiveRuleUI('{{ $defaultEmpCode }}');

            // Auto-load attendance data on initial page visit for default employee
            setTimeout(() => {
                loadAttendanceData();
            }, 300);
        });
    </script>
    @endpush
</x-admin-layout>
