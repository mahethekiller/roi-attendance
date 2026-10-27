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
                        <th>Records / Swipes</th>
                        <th>Status</th>
                        <th>HTTP</th>
                        <th>Message</th>
                        <th class="text-right">Payload</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="text-sm font-mono text-base-content/80 whitespace-nowrap">
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-base-content/50">{{ $log->created_at->format('h:i:s A') }}</div>
                            </td>
                            <td>
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
                                @else
                                    <span class="badge badge-ghost text-xs font-mono font-medium">{{ $log->trigger_type }}</span>
                                @endif
                                @if($log->user)
                                    <div class="text-[10px] text-base-content/50 mt-0.5">by {{ $log->user->name }}</div>
                                @endif
                            </td>
                            <td class="text-xs font-mono text-base-content/70 whitespace-nowrap">
                                <div>{{ $log->start_date ? $log->start_date->format('Y-m-d') : '-' }}</div>
                                <div class="text-[10px] text-base-content/40">to {{ $log->end_date ? $log->end_date->format('Y-m-d') : '-' }}</div>
                            </td>
                            <td>
                                <div class="text-xs font-mono font-semibold text-base-content">
                                    {{ $log->swipes_count }} swipes
                                </div>
                                <div class="text-[10px] text-base-content/50">
                                    from {{ $log->records_count }} records
                                </div>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                @if($log->http_status)
                                    <span class="badge badge-ghost font-mono text-xs">{{ $log->http_status }}</span>
                                @else
                                    <span class="text-base-content/40 text-xs">-</span>
                                @endif
                            </td>
                            <td class="text-xs text-base-content/80 max-w-xs truncate" title="{{ $log->message }}">
                                {{ $log->message }}
                            </td>
                            <td class="text-right">
                                <button type="button" class="btn btn-ghost btn-xs gap-1" onclick="openPayloadModal({{ $log->id }}, {{ json_encode($log->payload_sample ?? []) }}, {{ json_encode($log->response_payload ?? []) }}, '{{ $log->status }}')">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>Inspect</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-base-content/60">
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
        <div class="modal-box bg-base-100 border border-base-200 shadow-xl max-w-2xl">
            <h3 class="font-bold text-lg text-base-content flex items-center justify-between">
                <span>Push Telemetry & Payload Details</span>
                <span id="modalStatusBadge" class="badge badge-sm font-mono"></span>
            </h3>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-base-content/70 uppercase tracking-wider block mb-1">Swipes Payload Sample (Sent to greytHR)</label>
                    <pre id="modalPayloadSample" class="bg-base-200 p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-48 text-base-content"></pre>
                </div>

                <div>
                    <label class="text-xs font-semibold text-base-content/70 uppercase tracking-wider block mb-1">greytHR Response Body</label>
                    <pre id="modalResponsePayload" class="bg-base-200 p-3 rounded-lg text-xs font-mono overflow-x-auto max-h-48 text-base-content"></pre>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('payloadModal').close()">Close</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openPayloadModal(id, payload, response, status) {
            const payloadEl = document.getElementById('modalPayloadSample');
            const responseEl = document.getElementById('modalResponsePayload');
            const badgeEl = document.getElementById('modalStatusBadge');

            payloadEl.textContent = JSON.stringify(payload, null, 2);
            responseEl.textContent = JSON.stringify(response, null, 2);

            badgeEl.textContent = status.toUpperCase();
            badgeEl.className = 'badge badge-sm font-mono ' + 
                (status === 'success' ? 'badge-success' : (status === 'empty' ? 'badge-neutral' : 'badge-error'));

            document.getElementById('payloadModal').showModal();
        }
    </script>
</x-admin-layout>
