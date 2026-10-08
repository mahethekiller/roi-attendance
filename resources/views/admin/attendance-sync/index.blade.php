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
                    <h1 class="text-2xl font-bold text-base-content tracking-tight">Biometric 9-Hour Auto-Sync</h1>
                    <p class="text-sm text-base-content/70 mt-0.5">Detect attendance shortfall days (&lt; 9 hours) and synchronize check-out punches to 09h 00m &ndash; 09h 15m.</p>
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
                    <h2 class="text-sm font-bold uppercase tracking-wider text-base-content/80">Query Filters & Presets</h2>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                        <span class="text-xs text-base-content/60 font-mono" id="kpiTotalPunches">0 punches recorded</span>
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
                    <div class="text-[11px] text-base-content/50 mt-1">Met standard 9-hour shift requirement</div>
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
                    <div class="text-[11px] text-base-content/50 mt-1">Missing check-out or below 9.0h duration</div>
                </div>
            </div>
        </div>

        <!-- Prominent Bulk Auto-Sync Action Bar -->
        <div id="bulkSyncBanner" class="mt-4 p-4 rounded-xl bg-warning/10 border border-warning/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-warning text-warning-content flex items-center justify-center shrink-0">
                    <i data-lucide="zap" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-base-content flex items-center gap-2">
                        <span>Attendance Shortfall Detected</span>
                        <span class="badge badge-warning badge-xs font-mono font-bold" id="bulkShortfallBadgeCount">0 Days</span>
                    </h3>
                    <p class="text-xs text-base-content/70 mt-0.5">
                        Synchronize all shortfall dates automatically. Each check-out will be randomized strictly between <span class="font-mono font-bold text-base-content">09h 00m</span> and <span class="font-mono font-bold text-base-content">09h 15m</span>.
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" id="bulkSyncBtn" class="btn btn-warning btn-sm sm:btn-md gap-2 font-bold shadow-sm" onclick="promptBulkSyncConfirmation()">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span id="bulkSyncBtnText">Auto-Sync ALL Shortfall Days</span>
                </button>
            </div>
        </div>

        <!-- Bulk Sync Live Progress Container (hidden when idle) -->
        <div id="bulkProgressContainer" class="mt-4 p-4 rounded-xl bg-base-100 border border-base-200 hidden">
            <div class="flex items-center justify-between text-xs mb-2">
                <span class="font-bold text-base-content flex items-center gap-2">
                    <span class="loading loading-spinner loading-xs text-warning"></span>
                    <span id="bulkProgressStatus">Synchronizing Shortfall Dates...</span>
                </span>
                <span class="font-mono font-bold text-base-content/80" id="bulkProgressText">0 / 0 (0%)</span>
            </div>
            <progress id="bulkProgressBar" class="progress progress-warning w-full h-3" value="0" max="100"></progress>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="card bg-base-100 border border-base-200/80 shadow-xs">
        <div class="card-body p-0">
            <!-- Table Header Bar -->
            <div class="p-4 border-b border-base-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="table-2" class="w-4 h-4 text-base-content/60"></i>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-base-content/80">Biometric Attendance Logs & Sync Matrix</h2>
                    <span class="badge badge-sm badge-neutral font-mono" id="tableRowCountBadge">0 Days</span>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="text-base-content/50 font-mono">Algorithm:</span>
                    <span class="badge badge-sm badge-ghost font-mono border-base-300">Offset = 9h + rand(0,14)m + rand(5,55)s</span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto min-h-[300px]">
                <table class="table table-sm sm:table-md w-full" id="attendanceTable">
                    <thead>
                        <tr class="bg-base-200/50 text-base-content/70 text-xs uppercase tracking-wider border-b border-base-200">
                            <th class="py-3 px-4 font-bold">Date & Day</th>
                            <th class="py-3 px-3 font-bold text-center">Logs</th>
                            <th class="py-3 px-3 font-bold">1st Entry (Check-In)</th>
                            <th class="py-3 px-3 font-bold">Last Entry (Check-Out)</th>
                            <th class="py-3 px-3 font-bold font-mono">Total Work Time</th>
                            <th class="py-3 px-3 font-bold text-center">Status Badge</th>
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
                                        <p class="text-xs text-base-content/60 mt-1">Specify employee badge & date range above, then click <strong class="text-primary font-bold">Load Attendance</strong>.</p>
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
                        <span class="w-2.5 h-2.5 rounded-full bg-error"></span>
                        <span>Single Punch: Missing Check-Out</span>
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
                        <p class="text-xs text-base-content/60">Modify biometric check-out time manually</p>
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
                        <span class="text-base-content/60 font-medium">1st Punch (Check-In):</span>
                        <span class="font-mono font-bold text-info" id="modalDisplayFirstPunch">-</span>
                    </div>
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
                <div class="modal-action mt-2 gap-2">
                    <form method="dialog">
                        <button type="button" class="btn btn-ghost btn-sm sm:btn-md" onclick="document.getElementById('manualAdjustModal').close()">Cancel</button>
                    </form>
                    <button type="submit" id="modalSaveBtn" class="btn btn-primary btn-sm sm:btn-md gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Bulk Sync Confirmation Modal Dialog -->
    <dialog id="bulkSyncConfirmModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-100 border border-base-200/80 shadow-2xl p-5 sm:p-6 max-w-lg">
            <div class="flex items-center gap-3 text-warning border-b border-base-200 pb-3 mb-4">
                <div class="p-2.5 rounded-xl bg-warning/10">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-base-content">Confirm Bulk Auto-Sync</h3>
                    <p class="text-xs text-base-content/60">You are about to batch synchronize multiple biometric punches</p>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-base-content/80 mb-3">
                This action will compute realistic randomized check-out punches (<strong class="text-base-content">09h 00m &ndash; 09h 15m</strong>) and update the biometric attendance database for all <strong class="text-warning font-mono font-bold" id="confirmBulkShortfallCount">0</strong> shortfall days.
            </p>

            <!-- Scrollable List of Affected Dates -->
            <div class="border border-base-200 rounded-xl max-h-48 overflow-y-auto p-2 bg-base-200/40 divide-y divide-base-200 text-xs font-mono mb-4" id="confirmBulkDatesList">
                <!-- Dynamically populated -->
            </div>

            <div class="alert alert-warning alert-soft text-xs py-2 mb-4">
                <i data-lucide="shield-alert" class="w-4 h-4 shrink-0"></i>
                <span>All biometric changes are permanently recorded in the administrator audit trail.</span>
            </div>

            <div class="modal-action gap-2">
                <button type="button" class="btn btn-ghost btn-sm sm:btn-md" onclick="document.getElementById('bulkSyncConfirmModal').close()">Cancel</button>
                <button type="button" class="btn btn-warning btn-sm sm:btn-md gap-2 font-bold" onclick="executeBulkSync()">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Confirm &amp; Run Auto-Sync</span>
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Toast Notifications Container -->
    <div id="toastContainer" class="toast toast-end toast-bottom z-50 p-4 space-y-2 pointer-events-none">
        <!-- Dynamic Toast Items (will receive pointer-events-auto) -->
    </div>

    @push('scripts')
    <script>
        // Global State Store
        const state = {
            empCode: '{{ $defaultEmpCode }}',
            days: [],
            statistics: null,
            isLoading: false,
            isBulkSyncing: false,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            endpoints: {
                punches: '{{ route('admin.attendance-sync.punches') }}',
                sync: '{{ route('admin.attendance-sync.sync') }}',
                bulkSync: '{{ route('admin.attendance-sync.bulk-sync') }}'
            }
        };

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
            showToast('Filters reset to default', 'info');
        }

        // --- Core 9-Hour Randomized Calculation ---
        // Specification Formula:
        // Offset = (9 * 3600) + (rand(0, 14) * 60) + rand(5, 55)
        // Guaranteed strictly between 09h 00m 05s and 09h 14m 55s
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
            // 1. Calculate KPI Metrics
            const totalDays = state.days.length;
            const completedDays = state.days.filter(d => d.is_nine_hours).length;
            const shortfallDays = totalDays - completedDays;

            // Update KPI Cards
            document.getElementById('kpiContainer').classList.remove('hidden');
            document.getElementById('kpiTotalDays').textContent = totalDays;
            
            const totalPunchesCount = state.days.reduce((acc, d) => acc + (d.punch_count || 0), 0);
            document.getElementById('kpiTotalPunches').textContent = `${totalPunchesCount} punches recorded`;

            document.getElementById('kpiCompletedDays').textContent = completedDays;
            const compPct = totalDays > 0 ? Math.round((completedDays / totalDays) * 100) : 0;
            document.getElementById('kpiCompletedPct').textContent = `${compPct}%`;

            document.getElementById('kpiShortfallDays').textContent = shortfallDays;

            // Bulk Sync Banner visibility
            const bulkBanner = document.getElementById('bulkSyncBanner');
            const bulkBadgeCount = document.getElementById('bulkShortfallBadgeCount');
            if (shortfallDays > 0) {
                bulkBanner.classList.remove('hidden');
                bulkBadgeCount.textContent = `${shortfallDays} Days`;
            } else {
                bulkBanner.classList.add('hidden');
            }

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
            tbody.innerHTML = state.days.map((day, index) => {
                const isCompleted = !!day.is_nine_hours;
                const isSinglePunch = day.status === 'single_punch' || day.punch_count === 1 || !day.last_punch;
                const rowId = `att-row-${day.date}`;

                // Status Badge
                let statusBadgeHtml = '';
                if (isCompleted) {
                    statusBadgeHtml = `<span class="badge badge-success badge-soft font-mono gap-1 text-xs"><i data-lucide="check" class="w-3 h-3"></i> &ge; 9 Hours</span>`;
                } else if (isSinglePunch) {
                    statusBadgeHtml = `<span class="badge badge-error badge-soft font-mono gap-1 text-xs"><i data-lucide="log-in" class="w-3 h-3"></i> 1 Punch (Missing Out)</span>`;
                } else {
                    statusBadgeHtml = `<span class="badge badge-warning badge-soft font-mono gap-1 text-xs"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Shortfall (&lt; 9h)</span>`;
                }

                // First and Last Punch Timestamps
                const firstTime = day.first_punch ? (day.first_punch.time || day.first_punch.datetime.split(' ')[1]) : '--:--:--';
                const firstFull = day.first_punch ? day.first_punch.datetime : '';
                
                let lastTime = '--:--:--';
                let lastFull = '';
                if (day.last_punch && !isSinglePunch) {
                    lastTime = day.last_punch.time || day.last_punch.datetime.split(' ')[1];
                    lastFull = day.last_punch.datetime;
                }

                const durationFormatted = day.duration?.formatted || (isSinglePunch ? '00h 00m (In Only)' : '00h 00m 00s');

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
                            <div class="text-[10px] text-base-content/40 font-mono">Check-In Punch</div>
                        </td>

                        <!-- Last Entry (Check-Out) -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-1.5 font-mono font-semibold ${lastTime !== '--:--:--' ? 'text-base-content' : 'text-error'} text-sm">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 opacity-60"></i>
                                <span class="last-punch-time">${lastTime !== '--:--:--' ? lastTime : 'None (Missing)'}</span>
                            </div>
                            <div class="text-[10px] text-base-content/40 font-mono">Check-Out Punch</div>
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

                        <!-- Status Badge -->
                        <td class="py-3 px-3 text-center status-badge-cell">
                            ${statusBadgeHtml}
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 action-buttons-group">
                                <button 
                                    type="button" 
                                    class="btn btn-xs ${isCompleted ? 'btn-ghost text-base-content/50' : 'btn-warning'} gap-1 font-mono sync-single-btn" 
                                    title="Auto-Sync strictly to random 9h00m - 9h15m"
                                    onclick="triggerSingleSync('${day.date}', '${firstFull}')"
                                >
                                    <i data-lucide="zap" class="w-3 h-3"></i>
                                    <span>${isCompleted ? 'Re-Sync' : 'Auto-Sync'}</span>
                                </button>
                                <button 
                                    type="button" 
                                    class="btn btn-xs btn-outline btn-ghost gap-1" 
                                    title="Manually adjust check-out punch"
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

        // --- API 2: Single Day Auto-Sync ---
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

        // --- DOM Live Row Update ---
        function updateRowInDom(dateStr, newOutTimestamp, newDurationFormatted, isSyncedBadge = true) {
            const row = document.getElementById(`att-row-${dateStr}`);
            if (!row) return;

            const outTimeOnly = newOutTimestamp.includes(' ') ? newOutTimestamp.split(' ')[1] : newOutTimestamp;

            // Update Last Punch column
            const lastPunchSpan = row.querySelector('.last-punch-time');
            if (lastPunchSpan) {
                lastPunchSpan.textContent = outTimeOnly;
                lastPunchSpan.parentElement.className = 'flex items-center gap-1.5 font-mono font-semibold text-base-content text-sm';
            }

            // Update Duration
            const durCell = row.querySelector('.duration-formatted');
            if (durCell) {
                durCell.textContent = newDurationFormatted;
                durCell.className = 'font-mono font-bold text-sm text-success duration-formatted';
            }

            // Update Status Badge
            const badgeCell = row.querySelector('.status-badge-cell');
            if (badgeCell) {
                badgeCell.innerHTML = `
                    <span class="badge badge-success badge-soft font-mono gap-1 text-xs">
                        <i data-lucide="check-check" class="w-3 h-3"></i> 
                        &ge; 9 Hours ${isSyncedBadge ? '(Synced)' : ''}
                    </span>
                `;
            }

            // Remove warning highlight background
            row.classList.remove('bg-warning/5');

            // Update internal state
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

            // Recalculate KPI summary counters
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

            const bulkBanner = document.getElementById('bulkSyncBanner');
            const bulkBadgeCount = document.getElementById('bulkShortfallBadgeCount');
            if (shortfall > 0) {
                bulkBanner.classList.remove('hidden');
                bulkBadgeCount.textContent = `${shortfall} Days`;
            } else {
                bulkBanner.classList.add('hidden');
            }
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

            // Set Check-out input value
            const input = document.getElementById('modalLastPunchInput');
            let initialOut = lastPunchFull || (dayObj.last_punch ? dayObj.last_punch.datetime : '');
            if (!initialOut || dayObj.status === 'single_punch') {
                initialOut = calculateRandomOutTime(firstTime, dateStr);
            }

            // Format for datetime-local (YYYY-MM-DDTHH:mm:ss)
            input.value = initialOut.replace(' ', 'T');
            updateModalEstimatedDuration();

            input.oninput = updateModalEstimatedDuration;
            modal.showModal();
            if (window.renderLucideIcons) window.renderLucideIcons();
        }

        function fillSuggestedInModal() {
            const dateStr = document.getElementById('modalTargetDate').value;
            const firstTime = document.getElementById('modalDisplayFirstPunch').textContent;
            const suggested = calculateRandomOutTime(firstTime, dateStr);
            
            document.getElementById('modalLastPunchInput').value = suggested.replace(' ', 'T');
            updateModalEstimatedDuration();
            showToast('Populated 9h suggested punch time', 'info');
        }

        function updateModalEstimatedDuration() {
            const firstStr = document.getElementById('modalDisplayFirstPunch').textContent;
            const lastVal = document.getElementById('modalLastPunchInput').value;
            const previewEl = document.getElementById('modalEstimatedDuration');

            if (!firstStr || !lastVal) {
                previewEl.textContent = '--';
                return;
            }

            const tFirst = new Date(firstStr.replace(' ', 'T')).getTime();
            const tLast = new Date(lastVal).getTime();

            if (isNaN(tFirst) || isNaN(tLast) || tLast <= tFirst) {
                previewEl.textContent = 'Invalid / Before In-Time';
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

        async function submitManualAdjust() {
            const dateStr = document.getElementById('modalTargetDate').value;
            const empCode = document.getElementById('modalTargetEmpCode').value;
            const firstTime = document.getElementById('modalDisplayFirstPunch').textContent;
            const lastTimeInput = document.getElementById('modalLastPunchInput').value.replace('T', ' ');

            const saveBtn = document.getElementById('modalSaveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = `<span class="loading loading-spinner loading-xs"></span> Saving...`;

            try {
                const formData = new URLSearchParams();
                formData.append('emp_code', empCode);
                formData.append('date', dateStr);
                formData.append('first_time', firstTime);
                formData.append('last_time', lastTimeInput);
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
                    updateRowInDom(dateStr, lastTimeInput, dur, true);
                    document.getElementById('manualAdjustModal').close();
                    showToast(`Punch updated for ${dateStr} (${dur})`, 'success');
                } else {
                    showToast(`Failed to update punch: ${json.message || 'API error'}`, 'error');
                }
            } catch (err) {
                console.error('Manual adjust error:', err);
                showToast('Network error while saving punch', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = `<i data-lucide="save" class="w-4 h-4"></i><span>Save Changes</span>`;
                if (window.renderLucideIcons) window.renderLucideIcons();
            }
        }

        // --- Bulk Sync Handling ---
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

                // Small gentle delay between calls to avoid server congestion
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

            // Auto-load attendance data on initial page visit for default employee
            setTimeout(() => {
                loadAttendanceData();
            }, 300);
        });
    </script>
    @endpush
</x-admin-layout>
