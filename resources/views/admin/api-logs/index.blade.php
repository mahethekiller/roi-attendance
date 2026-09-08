<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">API Traffic & Request Logs</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Live audit log of all REST API queries, client IP addresses, latency, and response status codes.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.api-tokens.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                <i data-lucide="key" class="w-4 h-4"></i>
                <span>Manage API Tokens</span>
            </a>
            <a href="{{ route('admin.api-docs.index') }}" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span>View Documentation</span>
            </a>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total API Calls</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalRequests }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Successful (2xx)</span>
                <h3 class="text-2xl font-bold text-success mt-1">{{ $successfulRequests }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Client / Auth Errors (4xx)</span>
                <h3 class="text-2xl font-bold text-error mt-1">{{ $clientErrors }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Avg Execution Latency</span>
                <h3 class="text-2xl font-bold text-primary mt-1">{{ $avgDuration }} ms</h3>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.api-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-4">
                    <div class="join w-full">
                        <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                            <i data-lucide="search" class="w-4 h-4 text-base-content/60"></i>
                        </span>
                        <input type="text" name="search" class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content" placeholder="Search URL, IP, or Token..." aria-label="Search API logs by URL, IP, or Token" value="{{ $search }}">
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <select name="method" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by HTTP method">
                        <option value="">All Methods (GET, POST, etc.)</option>
                        <option value="GET" {{ $method === 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="POST" {{ $method === 'POST' ? 'selected' : '' }}>POST</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <select name="status" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by HTTP status code">
                        <option value="">All Statuses</option>
                        <option value="2xx" {{ $status === '2xx' ? 'selected' : '' }}>2xx Success (200 OK)</option>
                        <option value="4xx" {{ $status === '4xx' ? 'selected' : '' }}>4xx Client Error (401 / 422 / 429)</option>
                        <option value="5xx" {{ $status === '5xx' ? 'selected' : '' }}>5xx Server Error</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if($status || $method || $search)
                        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Filters" aria-label="Reset API log filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- API Logs Table -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">Recent API Calls ({{ $logs->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Timestamp</th>
                        <th>Method & Endpoint</th>
                        <th>Status</th>
                        <th>Latency</th>
                        <th>Client IP</th>
                        <th>Token / User</th>
                        <th class="text-right">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="font-medium text-base-content">{{ $log->created_at->format('M d, H:i:s') }}</div>
                                <span class="text-xs text-base-content/60">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge {{ $log->method === 'GET' ? 'badge-primary' : 'badge-success' }} badge-soft font-mono text-xs">
                                        {{ $log->method }}
                                    </span>
                                    <span class="font-mono text-xs text-base-content/90 max-w-xs truncate" title="{{ $log->url }}">
                                        {{ parse_url($log->url, PHP_URL_PATH) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($log->status_code >= 200 && $log->status_code < 300)
                                    <span class="badge badge-success badge-soft font-mono text-xs">{{ $log->status_code }} OK</span>
                                @elseif($log->status_code >= 400 && $log->status_code < 500)
                                    <span class="badge badge-warning badge-soft font-mono text-xs">{{ $log->status_code }}</span>
                                @else
                                    <span class="badge badge-error badge-soft font-mono text-xs">{{ $log->status_code }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-soft font-mono text-xs">
                                    {{ $log->duration_ms }} ms
                                </span>
                            </td>
                            <td class="font-mono text-xs text-base-content/70">{{ $log->ip_address }}</td>
                            <td>
                                @if($log->token_name)
                                    <span class="badge badge-info badge-soft gap-1 text-xs">
                                        <i data-lucide="key" class="w-3 h-3"></i>
                                        {{ $log->token_name }}
                                    </span>
                                @elseif($log->user)
                                    <span class="text-xs text-base-content font-medium">{{ $log->user->name }}</span>
                                @else
                                    <span class="text-xs text-base-content/50">Guest / Public</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button type="button" class="btn btn-sm btn-ghost btn-square text-base-content/70 hover:text-primary"
                                        title="Inspect Request"
                                        aria-label="Inspect API request for {{ $log->url }}"
                                        onclick="openApiLogModal(
                                            '{{ addslashes($log->url) }}',
                                            '{{ $log->ip_address }}',
                                            '{{ addslashes($log->user_agent ?? 'None') }}',
                                            {{ json_encode($log->query_params ? json_encode($log->query_params, JSON_PRETTY_PRINT) : '') }}
                                        )">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-base-content/60">
                                <i data-lucide="activity" class="mb-2 mx-auto text-base-content/30 w-9 h-9"></i>
                                <span>No API request logs recorded yet.</span>
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

    <!-- API Log Inspect Modal -->
    <dialog id="apiLogDetailModal" class="modal">
        <div class="modal-box bg-base-100 max-w-2xl border border-base-200">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg text-base-content flex items-center gap-2 mb-4">
                <i data-lucide="search" class="w-5 h-5 text-primary"></i>
                API Request Inspector
            </h3>
            <div class="mb-4">
                <span class="text-xs font-semibold text-base-content/60 uppercase block mb-1">Full Requested URL</span>
                <div id="modalApiUrl" class="p-2.5 bg-base-200/60 rounded-lg font-mono text-xs text-base-content border border-base-200 break-all"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="text-xs font-semibold text-base-content/60 uppercase">Client IP Address</span>
                    <div id="modalApiIp" class="font-mono text-sm font-semibold text-base-content mt-0.5"></div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-base-content/60 uppercase">User Agent</span>
                    <div id="modalApiAgent" class="text-xs text-base-content/70 truncate mt-0.5"></div>
                </div>
            </div>
            <div>
                <span class="text-xs font-semibold text-base-content/60 uppercase block mb-1">Query Parameters JSON</span>
                <pre id="modalApiParams" class="p-3 bg-base-200/60 rounded-lg text-xs font-mono text-base-content border border-base-200 max-h-44 overflow-y-auto"></pre>
            </div>
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('apiLogDetailModal').close()">Close</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openApiLogModal(url, ip, agent, params) {
            document.getElementById('modalApiUrl').textContent = url || '';
            document.getElementById('modalApiIp').textContent = ip || '';
            document.getElementById('modalApiAgent').textContent = agent || 'None';
            document.getElementById('modalApiParams').textContent = params && params !== 'null' ? params : 'No query parameters';
            document.getElementById('apiLogDetailModal').showModal();
        }
    </script>
</x-admin-layout>
