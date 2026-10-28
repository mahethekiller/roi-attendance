<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-base-content tracking-tight">greytHR Push Logs</h1>
                <span class="badge badge-primary badge-outline text-xs font-mono">v2 API</span>
            </div>
            <p class="text-sm text-base-content/70 mt-0.5">Automated hourly pushes and telemetry audit trail for other companies attendance pushed to <span class="font-mono text-xs font-semibold">i2k2-networks.greythr.com</span>.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs" onclick="document.getElementById('cronSetupModal').showModal()">
                <i data-lucide="clock" class="w-4 h-4 text-primary"></i>
                <span>Cron Setup</span>
            </button>
            <x-authorized permission="greythr_logs.clear">
                <button type="button" class="btn btn-outline btn-error btn-sm sm:btn-md gap-2 shadow-xs" onclick="document.getElementById('clearGreythrLogsModal').showModal()">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Clear Logs</span>
                </button>
            </x-authorized>
            <x-authorized permission="greythr_logs.push">
                <button type="button" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs" onclick="document.getElementById('pushNowModal').showModal()">
                    <i data-lucide="cloud-upload" class="w-4 h-4"></i>
                    <span>Push Now</span>
                </button>
            </x-authorized>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success shadow-xs mb-6" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error shadow-xs mb-6" role="alert">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Metrics Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Push Runs</span>
                        <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalRuns }}</h3>
                    </div>
                    <div class="rounded-xl bg-base-200 text-base-content/70 p-2.5">
                        <i data-lucide="activity" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Successful Runs</span>
                        <h3 class="text-2xl font-bold text-success mt-1">{{ $successfulRuns }}</h3>
                    </div>
                    <div class="rounded-xl bg-success/10 text-success p-2.5">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Failed Runs</span>
                        <h3 class="text-2xl font-bold text-error mt-1">{{ $failedRuns }}</h3>
                    </div>
                    <div class="rounded-xl bg-error/10 text-error p-2.5">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Swipes Synced</span>
                        <h3 class="text-2xl font-bold text-primary mt-1">{{ number_format($totalSwipesPushed) }}</h3>
                    </div>
                    <div class="rounded-xl bg-primary/10 text-primary p-2.5">
                        <i data-lucide="send" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.greythr-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-4">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Trigger Source</label>
                    <select name="trigger" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by trigger type">
                        <option value="">All Trigger Types (Cron, Manual, Command)</option>
                        <option value="cron" {{ $trigger === 'cron' ? 'selected' : '' }}>Automated Hourly Cron</option>
                        <option value="manual_ui" {{ $trigger === 'manual_ui' ? 'selected' : '' }}>Manual Dashboard Push</option>
                        <option value="command" {{ $trigger === 'command' ? 'selected' : '' }}>Artisan CLI Command</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Status</label>
                    <select name="status" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by execution status">
                        <option value="">All Statuses</option>
                        <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="empty" {{ $status === 'empty' ? 'selected' : '' }}>Empty (No Swipes)</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Date Range</label>
                    <div class="flex items-center gap-1.5">
                        <input type="date" name="start_date" class="input input-bordered input-sm sm:input-md w-full bg-base-100 text-base-content" value="{{ $startDate }}" placeholder="From">
                        <span class="text-base-content/40 text-xs">to</span>
                        <input type="date" name="end_date" class="input input-bordered input-sm sm:input-md w-full bg-base-100 text-base-content" value="{{ $endDate }}" placeholder="To">
                    </div>
                </div>
                <div class="sm:col-span-2 flex items-end gap-1.5 pt-4 sm:pt-0">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md flex-1">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if($status || $trigger || $startDate || $endDate)
                        <a href="{{ route('admin.greythr-logs.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Filters" aria-label="Reset filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Push Logs Data Table -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">greytHR Push Execution Logs ({{ $logs->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Timestamp</th>
                        <th>Trigger Source</th>
                        <th>Date Range</th>
                        <th>Status</th>
                        <th>Data Sent to API (Swipes Payload)</th>
                        <th>Message</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="text-sm font-mono text-base-content/80 whitespace-nowrap align-top py-3">
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-base-content/50">{{ $log->created_at->format('h:i:s A') }}</div>
                            </td>
                            <td class="align-top py-3">
                                @if($log->trigger_type === 'cron')
                                    <span class="badge badge-info badge-soft gap-1 text-xs font-mono font-medium">
                                        <i data-lucide="clock" class="w-3 h-3"></i> Hourly Cron
                                    </span>
                                @elseif($log->trigger_type === 'manual_ui')
                                    <span class="badge badge-primary badge-soft gap-1 text-xs font-mono font-medium">
                                        <i data-lucide="user" class="w-3 h-3"></i> Manual UI
                                    </span>
                                @elseif($log->trigger_type === 'command')
                                    <span class="badge badge-neutral badge-soft gap-1 text-xs font-mono font-medium">
                                        <i data-lucide="terminal" class="w-3 h-3"></i> CLI Command
                                    </span>
                                @elseif($log->trigger_type === 'webhook')
                                    <span class="badge badge-secondary badge-soft gap-1 text-xs font-mono font-medium">
                                        <i data-lucide="globe" class="w-3 h-3"></i> Webhook URL
                                    </span>
                                @else
                                    <span class="badge badge-ghost text-xs font-mono font-medium">{{ $log->trigger_type }}</span>
                                @endif
                                @if($log->user)
                                    <div class="text-[10px] text-base-content/50 mt-0.5">by {{ $log->user->name }}</div>
                                @endif
                            </td>
                            <td class="text-xs font-mono text-base-content/70 whitespace-nowrap align-top py-3">
                                <div>{{ $log->start_date ? $log->start_date->format('Y-m-d') : '-' }}</div>
                                <div class="text-[10px] text-base-content/40">to {{ $log->end_date ? $log->end_date->format('Y-m-d') : '-' }}</div>
                            </td>
                            <td class="align-top py-3">
                                <div class="flex flex-col gap-1">
                                    @if($log->status === 'success')
                                        <span class="badge badge-success badge-soft font-semibold text-xs px-2.5 py-1">
                                            Success
                                        </span>
                                    @elseif($log->status === 'empty')
                                        <span class="badge badge-neutral badge-soft font-medium text-xs px-2.5 py-1">
                                            No Data
                                        </span>
                                    @else
                                        <span class="badge badge-error badge-soft font-semibold text-xs px-2.5 py-1">
                                            Failed
                                        </span>
                                    @endif

                                    @if($log->http_status)
                                        <span class="badge badge-ghost font-mono text-[10px] w-fit">HTTP {{ $log->http_status }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="align-top py-3">
                                @php
                                    $swipesList = $log->payload_sample ?? [];
                                    $swipesCount = count($swipesList);
                                    $empIds = [];
                                    foreach ($swipesList as $sw) {
                                        $parts = explode(',', $sw);
                                        if (isset($parts[1])) {
                                            $cleanId = trim($parts[1]);
                                            if (!in_array($cleanId, $empIds)) {
                                                $empIds[] = $cleanId;
                                            }
                                        }
                                    }
                                @endphp

                                @if($swipesCount > 0)
                                    <div class="space-y-1.5 max-w-md">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="badge badge-primary badge-soft font-mono font-semibold text-xs px-2 py-0.5">
                                                {{ $swipesCount }} {{ Str::plural('Swipe', $swipesCount) }}
                                            </span>
                                            @if(count($empIds) > 0)
                                                <div class="flex items-center gap-1 flex-wrap text-[11px] text-base-content/70">
                                                    <span>Employees:</span>
                                                    @foreach(array_slice($empIds, 0, 3) as $eid)
                                                        <span class="badge badge-neutral badge-xs font-mono font-semibold">{{ $eid }}</span>
                                                    @endforeach
                                                    @if(count($empIds) > 3)
                                                        <span class="text-[10px] text-base-content/50">+{{ count($empIds) - 3 }} more</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Direct Preview of Data Sent to API -->
                                        <div class="bg-base-200/80 border border-base-300 rounded p-2 font-mono text-[11px] text-base-content/90 max-h-24 overflow-y-auto space-y-0.5 select-all leading-tight">
                                            @foreach(array_slice($swipesList, 0, 4) as $swipeLine)
                                                <div class="truncate text-[10.5px]" title="{{ $swipeLine }}">
                                                    {{ $swipeLine }}
                                                </div>
                                            @endforeach
                                            @if($swipesCount > 4)
                                                <div class="text-[10px] text-primary/80 font-sans font-medium pt-0.5">
                                                    +{{ $swipesCount - 4 }} more swipes sent
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-base-content/40 italic flex items-center gap-1">
                                        <i data-lucide="minus-circle" class="w-3.5 h-3.5"></i>
                                        <span>No payload sent (0 swipes)</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-xs text-base-content/80 max-w-xs align-top py-3" title="{{ $log->message }}">
                                <div class="line-clamp-2">
                                    {{ $log->message }}
                                </div>
                            </td>
                            <td class="text-right align-top py-3">
                                <button type="button" class="btn btn-primary btn-outline btn-xs gap-1" onclick="openPayloadModal({{ $log->id }}, {{ json_encode($log->payload_sample ?? []) }}, {{ json_encode($log->response_payload ?? []) }}, '{{ $log->status }}', '{{ $log->http_status }}', '{{ $log->start_date ? $log->start_date->format('Y-m-d') : '' }}', '{{ $log->end_date ? $log->end_date->format('Y-m-d') : '' }}')">
                                    <i data-lucide="file-code" class="w-3.5 h-3.5"></i>
                                    <span>View Payload</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-base-content/60">
                                <i data-lucide="inbox" class="w-9 h-9 mx-auto mb-2 text-base-content/30"></i>
                                <span class="font-medium text-sm block">No greytHR push logs recorded yet</span>
                                <span class="text-xs text-base-content/50">Scheduled hourly runs and manual pushes will automatically populate here.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-base-200/60 p-2">
                {{ $logs->links('vendor.pagination.daisyui') }}
            </div>
        @endif
    </div>

    <!-- Cron Setup Guide Modal -->
    <dialog id="cronSetupModal" class="modal">
        <div class="modal-box bg-base-100 border border-base-200 shadow-xl max-w-2xl p-6">
            <div class="flex items-center justify-between pb-3 border-b border-base-200">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-base-content">greytHR Cron Setup Guide</h3>
                        <p class="text-xs text-base-content/60">Automated background synchronization for partner companies attendance</p>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost btn-sm btn-circle" onclick="document.getElementById('cronSetupModal').close()">✕</button>
            </div>

            <div class="mt-4 space-y-4 text-xs text-base-content/80">
                <!-- Info banner -->
                <div class="bg-primary/5 border border-primary/20 rounded-lg p-3 text-base-content flex items-start gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 text-primary shrink-0 mt-0.5"></i>
                    <div class="space-y-1">
                        <div class="font-semibold text-xs text-primary">Default Behavior</div>
                        <p class="text-[11px] leading-relaxed">
                            By default, the cron command executes for the <strong>current date (today)</strong>. It automatically resolves registered employee IDs (e.g. <code class="font-mono bg-base-200 px-1 py-0.5 rounded text-primary">i2k2-2265</code>) and pushes punches directly to greytHR v2 API.
                        </p>
                    </div>
                </div>

                <!-- Setup Option 1: Master Laravel Scheduler (Recommended) -->
                <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-base-content flex items-center gap-1.5">
                            <span class="badge badge-primary badge-xs">Option 1</span>
                            Master Laravel Scheduler (Recommended)
                        </span>
                        <span class="badge badge-outline badge-xs text-[10px]">Runs All Schedules</span>
                    </div>
                    <p class="text-[11px] text-base-content/70">
                        Add this single line to your server crontab (<code class="bg-base-300 px-1 rounded">crontab -e</code>). It triggers the hourly greytHR push and minutely biometric machine sync automatically.
                    </p>
                    <div class="relative bg-base-300 p-2.5 rounded font-mono text-[11px] text-base-content flex items-center justify-between">
                        <code class="overflow-x-auto select-all" id="cronCmd1">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
                        <button type="button" class="btn btn-ghost btn-xs shrink-0 ml-2" onclick="copyText('cronCmd1', 'copyBtn1')" id="copyBtn1">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- Setup Option 2: Direct Command Cron -->
                <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-base-content flex items-center gap-1.5">
                            <span class="badge badge-neutral badge-xs">Option 2</span>
                            Direct Hourly greytHR Cron
                        </span>
                        <span class="badge badge-outline badge-xs text-[10px]">Hourly at Minute 0</span>
                    </div>
                    <p class="text-[11px] text-base-content/70">
                        If you want to trigger greytHR push independently on an hourly basis:
                    </p>
                    <div class="relative bg-base-300 p-2.5 rounded font-mono text-[11px] text-base-content flex items-center justify-between">
                        <code class="overflow-x-auto select-all" id="cronCmd2">0 * * * * cd {{ base_path() }} && php artisan attendance:push-greythr >> /dev/null 2>&1</code>
                        <button type="button" class="btn btn-ghost btn-xs shrink-0 ml-2" onclick="copyText('cronCmd2', 'copyBtn2')" id="copyBtn2">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- Setup Option 3: Windows Task Scheduler (Local XAMPP / Windows Server) -->
                <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-base-content flex items-center gap-1.5">
                            <span class="badge badge-neutral badge-xs">Option 3</span>
                            Windows Task Scheduler (XAMPP / Windows)
                        </span>
                        <span class="badge badge-outline badge-xs text-[10px]">Windows OS</span>
                    </div>
                    <p class="text-[11px] text-base-content/70">
                        Create a scheduled task set to repeat hourly with the following command:
                    </p>
                    <div class="relative bg-base-300 p-2.5 rounded font-mono text-[11px] text-base-content flex items-center justify-between">
                        <code class="overflow-x-auto select-all" id="cronCmd3">php {{ base_path('artisan') }} attendance:push-greythr</code>
                        <button type="button" class="btn btn-ghost btn-xs shrink-0 ml-2" onclick="copyText('cronCmd3', 'copyBtn3')" id="copyBtn3">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- Setup Option 4: External Cron Webhook URL (cron-job.org, EasyCron, Cloud) -->
                <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-base-content flex items-center gap-1.5">
                            <span class="badge badge-secondary badge-xs">Option 4</span>
                            External Cron Webhook URL (HTTP / Cloud)
                        </span>
                        <span class="badge badge-outline badge-xs text-[10px]">GET or POST</span>
                    </div>
                    <p class="text-[11px] text-base-content/70">
                        Use this authenticated HTTP URL with third-party cron services (e.g. <strong>cron-job.org</strong>, <strong>EasyCron</strong>, <strong>cPanel URL Cron</strong>):
                    </p>
                    <div class="relative bg-base-300 p-2.5 rounded font-mono text-[11px] text-base-content flex items-center justify-between">
                        <code class="overflow-x-auto select-all text-primary font-semibold" id="cronCmd4">{{ route('cron.push-greythr', ['token' => config('services.greythr.cron_token')]) }}</code>
                        <button type="button" class="btn btn-ghost btn-xs shrink-0 ml-2" onclick="copyText('cronCmd4', 'copyBtn4')" id="copyBtn4">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    <div class="space-y-1 pt-1">
                        <div class="text-[10px] text-base-content/60 font-semibold uppercase">cURL Command for Server:</div>
                        <div class="relative bg-base-300/70 p-2 rounded font-mono text-[10px] text-base-content flex items-center justify-between">
                            <code class="overflow-x-auto select-all" id="cronCmdCurl">curl -s "{{ route('cron.push-greythr', ['token' => config('services.greythr.cron_token')]) }}"</code>
                            <button type="button" class="btn btn-ghost btn-xs shrink-0 ml-2" onclick="copyText('cronCmdCurl', 'copyBtnCurl')" id="copyBtnCurl">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Test Run Command -->
                <div class="border-t border-base-200 pt-3 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-xs text-base-content">Manual Test from Terminal</div>
                        <div class="text-[11px] text-base-content/60">Run this command anytime to test current date push immediately:</div>
                    </div>
                    <code class="font-mono text-xs bg-base-200 px-2.5 py-1.5 rounded font-semibold text-primary select-all">php artisan attendance:push-greythr</code>
                </div>
            </div>

            <div class="modal-action border-t border-base-200 pt-3">
                <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('cronSetupModal').close()">Close</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Manual Push Modal -->
    <dialog id="pushNowModal" class="modal">
        <div class="modal-box bg-base-100 border border-base-200 shadow-xl max-w-md">
            <h3 class="font-bold text-lg text-base-content flex items-center gap-2">
                <i data-lucide="cloud-upload" class="w-5 h-5 text-primary"></i>
                <span>Push Swipes to greytHR</span>
            </h3>
            <p class="text-xs text-base-content/70 mt-1">
                Pushes other companies punch records (check-in & check-out) to greytHR API v2 (`i2k2-networks.greythr.com`).
            </p>

            <form method="POST" action="{{ route('admin.greythr-logs.push') }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-base-content/70 block mb-1">Start Date</label>
                        <input type="date" name="start_date" class="input input-bordered input-sm w-full bg-base-100 text-base-content" value="{{ $defaultStart }}" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-base-content/70 block mb-1">End Date</label>
                        <input type="date" name="end_date" class="input input-bordered input-sm w-full bg-base-100 text-base-content" value="{{ $defaultEnd }}" required>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-base-content/70 block mb-1">
                        Employee ID / Card No <span class="text-base-content/50 font-normal">(Optional)</span>
                    </label>
                    <input type="text" name="employee_id" class="input input-bordered input-sm w-full bg-base-100 text-base-content font-mono text-xs" placeholder="e.g. i2k2-2265 or leave empty for all">
                    <span class="text-[11px] text-base-content/60 mt-1 block">Leave empty to push swipes for all partner company employees.</span>
                </div>

                <div class="bg-base-200/60 rounded-lg p-3 text-xs text-base-content/70 space-y-1">
                    <div class="font-semibold text-base-content">greytHR Swipe Standard:</div>
                    <code class="font-mono text-[11px] block bg-base-300 p-1.5 rounded text-primary">["2026-10-27T09:30:00.000+05:30,i2k2-2265,door 1,1"]</code>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('pushNowModal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> Trigger Push
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Clear Logs Modal -->
    <dialog id="clearGreythrLogsModal" class="modal">
        <div class="modal-box bg-base-100 border border-base-200 shadow-xl max-w-md">
            <h3 class="font-bold text-lg text-error flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                <span>Clear greytHR Push Logs</span>
            </h3>
            <p class="text-sm text-base-content/70 mt-2">
                Choose the cleanup scope for greytHR execution audit logs:
            </p>
            <form method="POST" action="{{ route('admin.greythr-logs.clear') }}" class="mt-4 space-y-3">
                @csrf
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="radio" name="scope" value="older_7" class="radio radio-primary radio-sm" checked />
                        <span class="label-text text-sm">Older than 7 days</span>
                    </label>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="radio" name="scope" value="older_30" class="radio radio-primary radio-sm" />
                        <span class="label-text text-sm">Older than 30 days</span>
                    </label>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="radio" name="scope" value="failed" class="radio radio-primary radio-sm" />
                        <span class="label-text text-sm">Failed runs only</span>
                    </label>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="radio" name="scope" value="all" class="radio radio-error radio-sm" />
                        <span class="label-text text-sm text-error font-medium">All push logs (Truncate table)</span>
                    </label>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('clearGreythrLogsModal').close()">Cancel</button>
                    <button type="submit" class="btn btn-error btn-sm gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Clear Selected Logs
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Payload Inspect Modal -->
    <dialog id="payloadModal" class="modal">
        <div class="modal-box bg-base-100 border border-base-200 shadow-xl max-w-4xl p-6">
            <div class="flex items-center justify-between pb-3 border-b border-base-200">
                <div>
                    <h3 class="font-bold text-lg text-base-content flex items-center gap-2">
                        <i data-lucide="code-2" class="w-5 h-5 text-primary"></i>
                        <span>Data Sent to greytHR API & Response Details</span>
                    </h3>
                    <p class="text-xs text-base-content/60 mt-0.5">
                        Endpoint: <code class="text-primary font-mono font-semibold">POST https://api.greythr.com/attendance/v2/employee/swipes</code>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="modalHttpBadge" class="badge badge-ghost font-mono text-xs"></span>
                    <span id="modalStatusBadge" class="badge badge-sm font-mono font-semibold"></span>
                </div>
            </div>

            <div class="mt-4 space-y-5">
                <!-- Section 1: Data Sent (Swipes Payload) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-base-content">1. Sent Swipes Payload (Request Body)</span>
                            <span id="modalSwipesCountBadge" class="badge badge-primary badge-sm font-mono">0 swipes</span>
                        </div>
                        <button type="button" class="btn btn-ghost btn-xs gap-1" onclick="copyModalContent('modalPayloadSample', 'copyPayloadBtn')" id="copyPayloadBtn">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span>Copy Payload</span>
                        </button>
                    </div>

                    <!-- Parsed Swipes Table -->
                    <div class="overflow-x-auto max-h-56 border border-base-300/70 rounded-lg mb-2">
                        <table class="table table-xs table-pin-rows w-full font-mono">
                            <thead>
                                <tr class="bg-base-200 text-base-content/70">
                                    <th>#</th>
                                    <th>Timestamp (IST)</th>
                                    <th>Employee ID</th>
                                    <th>Door Name</th>
                                    <th>Direction</th>
                                </tr>
                            </thead>
                            <tbody id="modalSwipesTableBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Raw JSON Payload Code block -->
                    <details class="group bg-base-200/50 rounded-lg border border-base-300/50">
                        <summary class="cursor-pointer text-xs font-semibold p-2.5 text-base-content/70 flex items-center justify-between">
                            <span>View Raw JSON Array</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform group-open:rotate-180"></i>
                        </summary>
                        <div class="p-2.5 pt-0">
                            <pre id="modalPayloadSample" class="bg-base-300/60 p-3 rounded text-[11px] font-mono overflow-x-auto max-h-40 text-base-content select-all"></pre>
                        </div>
                    </details>
                </div>

                <!-- Section 2: greytHR Response Body -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-sm text-base-content">2. greytHR API Response</span>
                        <button type="button" class="btn btn-ghost btn-xs gap-1" onclick="copyModalContent('modalResponsePayload', 'copyResponseBtn')" id="copyResponseBtn">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span>Copy Response</span>
                        </button>
                    </div>
                    <pre id="modalResponsePayload" class="bg-base-200/80 border border-base-300/60 p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-44 text-base-content select-all"></pre>
                </div>
            </div>

            <div class="modal-action border-t border-base-200 pt-3">
                <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('payloadModal').close()">Close</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openPayloadModal(id, payload, response, status, httpStatus, startDate, endDate) {
            const payloadEl = document.getElementById('modalPayloadSample');
            const responseEl = document.getElementById('modalResponsePayload');
            const badgeEl = document.getElementById('modalStatusBadge');
            const httpBadgeEl = document.getElementById('modalHttpBadge');
            const swipesCountBadge = document.getElementById('modalSwipesCountBadge');
            const tableBody = document.getElementById('modalSwipesTableBody');

            // Set raw content
            payloadEl.textContent = JSON.stringify(payload, null, 2);
            responseEl.textContent = JSON.stringify(response, null, 2);

            // Set Badges
            badgeEl.textContent = status ? status.toUpperCase() : 'UNKNOWN';
            badgeEl.className = 'badge badge-sm font-mono ' + 
                (status === 'success' ? 'badge-success' : (status === 'empty' ? 'badge-neutral' : 'badge-error'));

            if (httpStatus && httpStatus !== 'null') {
                httpBadgeEl.textContent = 'HTTP ' + httpStatus;
                httpBadgeEl.classList.remove('hidden');
            } else {
                httpBadgeEl.classList.add('hidden');
            }

            const swipes = Array.isArray(payload) ? payload : [];
            swipesCountBadge.textContent = swipes.length + (swipes.length === 1 ? ' swipe' : ' swipes');

            // Populate parsed table rows
            tableBody.innerHTML = '';
            if (swipes.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-base-content/50 italic">No swipes were generated or sent for this run.</td></tr>';
            } else {
                swipes.forEach((swipeStr, index) => {
                    const parts = (typeof swipeStr === 'string' ? swipeStr.split(',') : []);
                    const timestamp = parts[0] || '-';
                    const empId = parts[1] || '-';
                    const door = parts[2] || '-';
                    const isCheckIn = parts[3] === '1';
                    const directionText = isCheckIn ? 'IN (Check-in)' : (parts[3] === '0' ? 'OUT (Check-out)' : parts[3]);
                    const badgeClass = isCheckIn ? 'badge-success badge-soft text-success' : 'badge-warning badge-soft text-warning';

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-base-200/50';
                    tr.innerHTML = `
                        <td class="text-base-content/50">${index + 1}</td>
                        <td class="text-base-content/90">${escapeHtml(timestamp)}</td>
                        <td><span class="badge badge-neutral badge-xs font-mono font-bold">${escapeHtml(empId)}</span></td>
                        <td class="text-base-content/70">${escapeHtml(door)}</td>
                        <td><span class="badge ${badgeClass} badge-xs font-semibold">${escapeHtml(directionText)}</span></td>
                    `;
                    tableBody.appendChild(tr);
                });
            }

            if (window.lucide) {
                window.lucide.createIcons();
            }

            document.getElementById('payloadModal').showModal();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function copyModalContent(elementId, btnId) {
            const el = document.getElementById(elementId);
            if (!el) return;

            navigator.clipboard.writeText(el.textContent).then(() => {
                const btn = document.getElementById(btnId);
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-success"></i><span class="text-success">Copied!</span>';
                if (window.lucide) window.lucide.createIcons();
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();
                }, 2000);
            });
        }

        function copyText(elementId, btnId) {
            const el = document.getElementById(elementId);
            if (!el) return;

            navigator.clipboard.writeText(el.textContent.trim()).then(() => {
                const btn = document.getElementById(btnId);
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-success"></i>';
                if (window.lucide) window.lucide.createIcons();
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();
                }, 2000);
            });
        }
    </script>
</x-admin-layout>
