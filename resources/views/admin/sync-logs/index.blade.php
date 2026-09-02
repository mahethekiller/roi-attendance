<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">Biometric Sync History</h2>
            <p class="text-body-secondary mb-0">Audit logs of all automated cron executions, manual triggers, and webhook sync runs.</p>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.attendances.sync') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                    <span>Trigger Sync Now</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Metrics Summary Grid -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Total Sync Runs</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalSyncs }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Successful Runs</span>
                    <h3 class="mb-0 fw-bold text-success mt-1">{{ $successfulSyncs }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Failed Runs</span>
                    <h3 class="mb-0 fw-bold text-danger mt-1">{{ $failedSyncs }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Punches Inserted / Updated</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalImported }} / {{ $totalUpdated }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.sync-logs.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <select name="trigger" class="form-select bg-body-tertiary text-body">
                        <option value="">All Trigger Types (Cron, Manual, Webhook, CLI)</option>
                        <option value="cron" {{ $trigger === 'cron' ? 'selected' : '' }}>Cron</option>
                        <option value="manual_ui" {{ $trigger === 'manual_ui' ? 'selected' : '' }}>Manual Dashboard Click</option>
                        <option value="command" {{ $trigger === 'command' ? 'selected' : '' }}>Artisan CLI Command</option>
                        <option value="webhook" {{ $trigger === 'webhook' ? 'selected' : '' }}>HTTP Webhook</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <select name="status" class="form-select bg-body-tertiary text-body">
                        <option value="">All Statuses (Success & Failed)</option>
                        <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success Only</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed Only</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
                    </button>
                    @if($status || $trigger)
                        <a href="{{ route('admin.sync-logs.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Sync Logs Data Table -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">Execution Logs ({{ $logs->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>Execution Time</th>
                        <th>Trigger Source</th>
                        <th>Date Range</th>
                        <th>Status</th>
                        <th>Inserted</th>
                        <th>Updated</th>
                        <th>Message</th>
                        <th class="text-end">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="fw-medium text-body-emphasis">{{ $log->created_at->format('M d, Y h:i:s A') }}</div>
                                <span class="text-body-secondary small">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                @php
                                    $triggerBadge = match($log->trigger_type) {
                                        'manual_ui' => ['color' => 'primary', 'icon' => 'mouse-pointer-click', 'label' => 'Manual UI'],
                                        'webhook' => ['color' => 'info', 'icon' => 'globe', 'label' => 'Webhook URL'],
                                        'command' => ['color' => 'secondary', 'icon' => 'terminal', 'label' => 'CLI Command'],
                                        default => ['color' => 'dark', 'icon' => 'clock', 'label' => 'Cron']
                                    };
                                @endphp
                                <span class="badge bg-{{ $triggerBadge['color'] }}-subtle text-{{ $triggerBadge['color'] }} border border-{{ $triggerBadge['color'] }}-subtle d-inline-flex align-items-center gap-1">
                                    <i data-lucide="{{ $triggerBadge['icon'] }}" style="width: 13px; height: 13px;"></i>
                                    {{ $triggerBadge['label'] }}
                                </span>
                            </td>
                            <td class="text-body-secondary small">
                                {{ $log->start_date ? $log->start_date->format('M d') : '-' }} &rarr; {{ $log->end_date ? $log->end_date->format('M d, Y') : '-' }}
                            </td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                                        <i data-lucide="check-circle-2" style="width: 13px; height: 13px;"></i> Success
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                                        <i data-lucide="x-circle" style="width: 13px; height: 13px;"></i> Failed
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-body-tertiary text-body border fw-mono">
                                    +{{ $log->imported_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-body-tertiary text-body border fw-mono">
                                    ~{{ $log->updated_count }}
                                </span>
                            </td>
                            <td>
                                <div class="text-body text-truncate" style="max-width: 260px;" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#logDetailModal"
                                        data-log-time="{{ $log->created_at->format('M d, Y h:i:s A') }}"
                                        data-log-trigger="{{ $triggerBadge['label'] }}"
                                        data-log-status="{{ $log->status }}"
                                        data-log-message="{{ $log->message }}"
                                        data-log-imported="{{ $log->imported_count }}"
                                        data-log-updated="{{ $log->updated_count }}"
                                        data-log-payload="{{ json_encode($log->payload_summary, JSON_PRETTY_PRINT) }}">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-body-secondary">
                                <i data-lucide="history" class="mb-2 d-block mx-auto text-body-tertiary" style="width: 36px; height: 36px;"></i>
                                <span>No synchronization logs recorded yet.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-body border-top p-3">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Sync Log Detail Modal -->
    <div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-body border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-body-emphasis" id="logDetailModalLabel">
                        <i data-lucide="file-text" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        Biometric Sync Execution Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-body">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <span class="text-body-secondary small fw-medium">Executed At</span>
                            <div id="modalLogTime" class="fw-semibold text-body-emphasis"></div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-body-secondary small fw-medium">Trigger Source</span>
                            <div id="modalLogTrigger" class="fw-semibold text-body-emphasis"></div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-body-secondary small fw-medium">Status</span>
                            <div id="modalLogStatus"></div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-body-secondary small fw-medium">Stats</span>
                            <div id="modalLogStats" class="fw-semibold text-body-emphasis"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="text-body-secondary small fw-medium">Result Message</span>
                        <div id="modalLogMessage" class="p-3 bg-body-tertiary rounded-3 border text-body-emphasis font-monospace small"></div>
                    </div>

                    <div>
                        <span class="text-body-secondary small fw-medium">Payload / Response Summary</span>
                        <pre id="modalLogPayload" class="p-3 bg-body-tertiary rounded-3 border text-body-emphasis font-monospace small mb-0" style="max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logModal = document.getElementById('logDetailModal');
            if (logModal) {
                logModal.addEventListener('show.bs.modal', (event) => {
                    const button = event.relatedTarget;
                    const time = button.getAttribute('data-log-time');
                    const trigger = button.getAttribute('data-log-trigger');
                    const status = button.getAttribute('data-log-status');
                    const message = button.getAttribute('data-log-message');
                    const imported = button.getAttribute('data-log-imported');
                    const updated = button.getAttribute('data-log-updated');
                    const payload = button.getAttribute('data-log-payload');

                    document.getElementById('modalLogTime').textContent = time;
                    document.getElementById('modalLogTrigger').textContent = trigger;
                    document.getElementById('modalLogStatus').innerHTML = status === 'success'
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Success</span>'
                        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Failed</span>';
                    document.getElementById('modalLogStats').textContent = `+${imported} inserted, ~${updated} updated`;
                    document.getElementById('modalLogMessage').textContent = message || 'No message';
                    document.getElementById('modalLogPayload').textContent = payload && payload !== 'null' ? payload : 'No payload details';
                });
            }
        });
    </script>
</x-admin-layout>
