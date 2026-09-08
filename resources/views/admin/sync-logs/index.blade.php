<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Biometric Sync History</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Audit logs of all automated cron executions, manual triggers, and webhook sync runs.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-authorized permission="sync-logs.clear">
                <button type="button" class="btn btn-outline btn-error btn-sm sm:btn-md gap-2 shadow-xs" onclick="document.getElementById('clearSyncLogsModal').showModal()">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Clear Logs</span>
                </button>
            </x-authorized>
            <form method="POST" action="{{ route('admin.attendances.sync') }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Trigger Sync Now</span>
                </button>
            </form>
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
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Sync Runs</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalSyncs }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Successful Runs</span>
                <h3 class="text-2xl font-bold text-success mt-1">{{ $successfulSyncs }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Failed Runs</span>
                <h3 class="text-2xl font-bold text-error mt-1">{{ $failedSyncs }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Punches Inserted / Updated</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalImported }} / {{ $totalUpdated }}</h3>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.sync-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-5">
                    <select name="trigger" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by trigger type">
                        <option value="">All Trigger Types (Cron, Manual, Webhook, CLI)</option>
                        <option value="cron" {{ $trigger === 'cron' ? 'selected' : '' }}>Cron</option>
                        <option value="manual_ui" {{ $trigger === 'manual_ui' ? 'selected' : '' }}>Manual Dashboard Click</option>
                        <option value="command" {{ $trigger === 'command' ? 'selected' : '' }}>Artisan CLI Command</option>
                        <option value="webhook" {{ $trigger === 'webhook' ? 'selected' : '' }}>HTTP Webhook</option>
                    </select>
                </div>
                <div class="sm:col-span-4">
                    <select name="status" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by execution status">
                        <option value="">All Statuses (Success & Failed)</option>
                        <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success Only</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed Only</option>
                    </select>
                </div>
                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if($status || $trigger)
                        <a href="{{ route('admin.sync-logs.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Filters" aria-label="Reset sync log filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Sync Logs Data Table -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">Execution Logs ({{ $logs->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Execution Time</th>
                        <th>Trigger Source</th>
                        <th>Date Range</th>
                        <th>Status</th>
                        <th>Inserted</th>
                        <th>Updated</th>
                        <th>Message</th>
                        <th class="text-right">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="font-medium text-base-content">{{ $log->created_at->format('M d, Y h:i:s A') }}</div>
                                <span class="text-xs text-base-content/60">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                @php
                                    $triggerBadge = match($log->trigger_type) {
                                        'manual_ui' => ['color' => 'badge-primary', 'icon' => 'mouse-pointer-click', 'label' => 'Manual UI'],
                                        'webhook' => ['color' => 'badge-info', 'icon' => 'globe', 'label' => 'Webhook URL'],
                                        'command' => ['color' => 'badge-secondary', 'icon' => 'terminal', 'label' => 'CLI Command'],
                                        default => ['color' => 'badge-neutral', 'icon' => 'clock', 'label' => 'Cron']
                                    };
                                @endphp
                                <span class="badge {{ $triggerBadge['color'] }} badge-soft gap-1">
                                    <i data-lucide="{{ $triggerBadge['icon'] }}" class="w-3 h-3"></i>
                                    {{ $triggerBadge['label'] }}
                                </span>
                            </td>
                            <td class="text-xs text-base-content/70">
                                {{ $log->start_date ? $log->start_date->format('M d') : '-' }} &rarr; {{ $log->end_date ? $log->end_date->format('M d, Y') : '-' }}
                            </td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge badge-success badge-soft gap-1">
                                        <i data-lucide="check-circle-2" class="w-3 h-3"></i> Success
                                    </span>
                                @else
                                    <span class="badge badge-error badge-soft gap-1">
                                        <i data-lucide="x-circle" class="w-3 h-3"></i> Failed
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-soft font-mono">
                                    +{{ $log->imported_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-soft font-mono">
                                    ~{{ $log->updated_count }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm text-base-content/80 max-w-xs truncate" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </div>
                            </td>
                            <td class="text-right">
                                <button type="button" class="btn btn-sm btn-ghost btn-square text-base-content/70 hover:text-primary"
                                        title="View Log Details"
                                        aria-label="View sync log details from {{ $log->created_at->format('M d, Y h:i:s A') }}"
                                        onclick="openLogModal(
                                            '{{ $log->created_at->format('M d, Y h:i:s A') }}',
                                            '{{ $triggerBadge['label'] }}',
                                            '{{ $log->status }}',
                                            '{{ addslashes($log->message) }}',
                                            '{{ $log->imported_count }}',
                                            '{{ $log->updated_count }}',
                                            {{ json_encode($log->payload_summary ? json_encode($log->payload_summary, JSON_PRETTY_PRINT) : '') }}
                                        )">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-base-content/60">
                                <i data-lucide="history" class="mb-2 mx-auto text-base-content/30 w-9 h-9"></i>
                                <span>No synchronization logs recorded yet.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-base-200/60">
                {{ $logs->links('vendor.pagination.daisyui') }}
            </div>
        @endif
    </div>

    <!-- Sync Log Detail Modal -->
    <dialog id="logDetailModal" class="modal">
        <div class="modal-box bg-base-100 max-w-2xl border border-base-200">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg text-base-content flex items-center gap-2 mb-4">
                <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                Biometric Sync Execution Details
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="text-xs font-semibold text-base-content/60 uppercase">Executed At</span>
                    <div id="modalLogTime" class="font-medium text-base-content text-sm mt-0.5"></div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-base-content/60 uppercase">Trigger Source</span>
                    <div id="modalLogTrigger" class="font-medium text-base-content text-sm mt-0.5"></div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-base-content/60 uppercase">Status</span>
                    <div id="modalLogStatus" class="mt-0.5"></div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-base-content/60 uppercase">Stats</span>
                    <div id="modalLogStats" class="font-medium text-base-content text-sm mt-0.5"></div>
                </div>
            </div>

            <div class="mb-4">
                <span class="text-xs font-semibold text-base-content/60 uppercase block mb-1">Result Message</span>
                <div id="modalLogMessage" class="p-3 bg-base-200/60 rounded-lg text-xs font-mono text-base-content border border-base-200"></div>
            </div>

            <div>
                <span class="text-xs font-semibold text-base-content/60 uppercase block mb-1">Payload / Response Summary</span>
                <pre id="modalLogPayload" class="p-3 bg-base-200/60 rounded-lg text-xs font-mono text-base-content border border-base-200 max-h-48 overflow-y-auto"></pre>
            </div>

            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('logDetailModal').close()">Close</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openLogModal(time, trigger, status, message, imported, updated, payload) {
            document.getElementById('modalLogTime').textContent = time;
            document.getElementById('modalLogTrigger').textContent = trigger;
            document.getElementById('modalLogStatus').innerHTML = status === 'success'
                ? '<span class="badge badge-success badge-soft">Success</span>'
                : '<span class="badge badge-error badge-soft">Failed</span>';
            document.getElementById('modalLogStats').textContent = `+${imported} inserted, ~${updated} updated`;
            document.getElementById('modalLogMessage').textContent = message || 'No message';
            document.getElementById('modalLogPayload').textContent = payload && payload !== 'null' ? payload : 'No payload details';
            document.getElementById('logDetailModal').showModal();
        }
    </script>

    <!-- Clear Sync Logs Modal (Super Admin Only) -->
    <x-authorized permission="sync-logs.clear">
        <dialog id="clearSyncLogsModal" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box bg-base-100 border border-base-200 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-base-200">
                    <div class="flex items-center gap-2 text-error font-bold text-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        <span>Clear Sync History Logs</span>
                    </div>
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost">✕</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.sync-logs.clear') }}" class="mt-4">
                    @csrf
                    <p class="text-sm text-base-content/80 mb-4">
                        Select which logs to purge. This action will permanently remove audit records from the database.
                    </p>

                    <div class="form-control mb-4">
                        <label class="label pb-1.5" for="clearScope">
                            <span class="label-text font-semibold text-xs text-base-content/80">Select Purge Scope</span>
                        </label>
                        <select name="scope" id="clearScope" required class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content">
                            <option value="all">Purge All Logs (Complete Reset)</option>
                            <option value="older_than_7_days">Logs Older Than 7 Days</option>
                            <option value="older_than_30_days">Logs Older Than 30 Days</option>
                            <option value="failed_only">Failed Sync Attempts Only</option>
                        </select>
                    </div>

                    <div class="p-3 bg-error/10 border border-error/20 rounded-lg text-xs text-error font-medium mb-5 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5"></i>
                        <span>Permanent action: Deleted sync logs cannot be recovered.</span>
                    </div>

                    <div class="modal-action flex items-center justify-end gap-2">
                        <button type="button" class="btn btn-ghost btn-sm sm:btn-md" onclick="document.getElementById('clearSyncLogsModal').close()">Cancel</button>
                        <button type="submit" class="btn btn-error btn-sm sm:btn-md gap-2 shadow-xs">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Confirm & Clear</span>
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </x-authorized>
</x-admin-layout>
