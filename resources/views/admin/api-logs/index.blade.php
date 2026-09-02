<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">API Traffic & Request Logs</h2>
            <p class="text-body-secondary mb-0">Live audit log of all REST API queries, client IP addresses, latency, and response status codes.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.api-tokens.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                <span>Manage API Tokens</span>
            </a>
            <a href="{{ route('admin.api-docs.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>View Documentation</span>
            </a>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Total API Calls</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalRequests }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Successful (2xx)</span>
                    <h3 class="mb-0 fw-bold text-success mt-1">{{ $successfulRequests }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Client / Auth Errors (4xx)</span>
                    <h3 class="mb-0 fw-bold text-danger mt-1">{{ $clientErrors }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Avg Execution Latency</span>
                    <h3 class="mb-0 fw-bold text-primary mt-1">{{ $avgDuration }} ms</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.api-logs.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                            <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-body-tertiary text-body border-start-0" placeholder="Search URL, IP, or Token..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="method" class="form-select bg-body-tertiary text-body">
                        <option value="">All Methods (GET, POST, etc.)</option>
                        <option value="GET" {{ $method === 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="POST" {{ $method === 'POST' ? 'selected' : '' }}>POST</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select bg-body-tertiary text-body">
                        <option value="">All Statuses</option>
                        <option value="2xx" {{ $status === '2xx' ? 'selected' : '' }}>2xx Success (200 OK)</option>
                        <option value="4xx" {{ $status === '4xx' ? 'selected' : '' }}>4xx Client Error (401 / 422 / 429)</option>
                        <option value="5xx" {{ $status === '5xx' ? 'selected' : '' }}>5xx Server Error</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
                    </button>
                    @if($status || $method || $search)
                        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- API Logs Table -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">Recent API Calls ({{ $logs->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>Timestamp</th>
                        <th>Method & Endpoint</th>
                        <th>Status</th>
                        <th>Latency</th>
                        <th>Client IP</th>
                        <th>Token / User</th>
                        <th class="text-end">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="fw-medium text-body-emphasis">{{ $log->created_at->format('M d, H:i:s') }}</div>
                                <span class="text-body-secondary small">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $log->method === 'GET' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} fw-mono" style="font-size: 0.75rem;">
                                        {{ $log->method }}
                                    </span>
                                    <span class="font-monospace text-body-emphasis small text-truncate" style="max-width: 280px;" title="{{ $log->url }}">
                                        {{ parse_url($log->url, PHP_URL_PATH) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($log->status_code >= 200 && $log->status_code < 300)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-mono">{{ $log->status_code }} OK</span>
                                @elseif($log->status_code >= 400 && $log->status_code < 500)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-mono">{{ $log->status_code }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-mono">{{ $log->status_code }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-body-tertiary text-body border fw-mono">
                                    {{ $log->duration_ms }} ms
                                </span>
                            </td>
                            <td class="font-monospace small text-body-secondary">{{ $log->ip_address }}</td>
                            <td>
                                @if($log->token_name)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle d-inline-flex align-items-center gap-1">
                                        <i data-lucide="key" style="width: 12px; height: 12px;"></i>
                                        {{ $log->token_name }}
                                    </span>
                                @elseif($log->user)
                                    <span class="text-body-emphasis small">{{ $log->user->name }}</span>
                                @else
                                    <span class="text-body-secondary small">Guest / Public</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#apiLogDetailModal"
                                        data-url="{{ $log->url }}"
                                        data-ip="{{ $log->ip_address }}"
                                        data-agent="{{ $log->user_agent }}"
                                        data-params="{{ json_encode($log->query_params, JSON_PRETTY_PRINT) }}">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    <span>Inspect</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-body-secondary">
                                <i data-lucide="activity" class="mb-2 d-block mx-auto text-body-tertiary" style="width: 36px; height: 36px;"></i>
                                <span>No API request logs recorded yet.</span>
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

    <!-- API Log Inspect Modal -->
    <div class="modal fade" id="apiLogDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-body border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-body-emphasis">
                        <i data-lucide="search" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        API Request Inspector
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-body">
                    <div class="mb-3">
                        <span class="text-body-secondary small fw-medium">Full Requested URL</span>
                        <div id="modalApiUrl" class="p-2 bg-body-tertiary rounded-2 border font-monospace small text-body-emphasis word-break"></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <span class="text-body-secondary small fw-medium">Client IP Address</span>
                            <div id="modalApiIp" class="fw-semibold text-body-emphasis font-monospace"></div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-body-secondary small fw-medium">User Agent</span>
                            <div id="modalApiAgent" class="small text-body-secondary text-truncate" style="max-width: 350px;"></div>
                        </div>
                    </div>
                    <div>
                        <span class="text-body-secondary small fw-medium">Query Parameters JSON</span>
                        <pre id="modalApiParams" class="p-3 bg-body-tertiary rounded-3 border text-body-emphasis font-monospace small mb-0" style="max-height: 180px; overflow-y: auto;"></pre>
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
            const modal = document.getElementById('apiLogDetailModal');
            if (modal) {
                modal.addEventListener('show.bs.modal', (event) => {
                    const btn = event.relatedTarget;
                    document.getElementById('modalApiUrl').textContent = btn.getAttribute('data-url') || '';
                    document.getElementById('modalApiIp').textContent = btn.getAttribute('data-ip') || '';
                    document.getElementById('modalApiAgent').textContent = btn.getAttribute('data-agent') || 'None';
                    const params = btn.getAttribute('data-params');
                    document.getElementById('modalApiParams').textContent = params && params !== 'null' ? params : 'No query parameters';
                });
            }
        });
    </script>
</x-admin-layout>
