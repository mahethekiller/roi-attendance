<x-admin-layout>
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge badge-primary badge-soft px-2 py-0.5 text-xs font-semibold">Developer Hub</span>
                <span class="text-base-content/40 text-xs">&bull;</span>
                <span class="text-base-content/60 text-xs font-mono">REST API v1.0 & HRsale Spec</span>
            </div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">REST API Reference & Documentation</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Complete endpoint specifications, request parameters, interactive multi-language code snippets, and authentication guides.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.api-tokens.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                <i data-lucide="key" class="w-4 h-4"></i>
                <span>API Tokens</span>
            </a>

            <!-- Split Dropdown with Full Spec and Individual Endpoint Downloads -->
            <div class="join shadow-xs">
                <a href="{{ route('admin.api-docs.export-txt') }}" class="btn btn-primary btn-sm sm:btn-md join-item gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Download Full Spec (.txt)</span>
                </a>
                <div class="dropdown dropdown-end">
                    <button tabindex="0" type="button" class="btn btn-primary btn-sm sm:btn-md join-item px-2 border-l border-primary-content/20" aria-label="Toggle Individual Endpoint Downloads">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 w-72 p-2 shadow-xl border border-base-200">
                        <li class="menu-title text-[11px] font-bold uppercase tracking-wider text-base-content/50 px-3">Individual Endpoint Specs</li>
                        <li>
                            <a class="flex items-center justify-between py-2 text-xs text-base-content" href="{{ route('admin.api-docs.export-endpoint', 'hrsale-attendance') }}">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-success badge-xs font-mono font-bold">POST</span>
                                    <span class="font-mono truncate max-w-[150px]">/api/attendance</span>
                                </div>
                                <i data-lucide="download" class="w-3.5 h-3.5 text-base-content/50"></i>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center justify-between py-2 text-xs text-base-content" href="{{ route('admin.api-docs.export-endpoint', 'auth-token') }}">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-success badge-xs font-mono font-bold">POST</span>
                                    <span class="font-mono truncate max-w-[150px]">/v1/auth/token</span>
                                </div>
                                <i data-lucide="download" class="w-3.5 h-3.5 text-base-content/50"></i>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center justify-between py-2 text-xs text-base-content" href="{{ route('admin.api-docs.export-endpoint', 'attendances-list') }}">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-primary badge-xs font-mono font-bold">GET</span>
                                    <span class="font-mono truncate max-w-[150px]">/v1/attendances</span>
                                </div>
                                <i data-lucide="download" class="w-3.5 h-3.5 text-base-content/50"></i>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center justify-between py-2 text-xs text-base-content" href="{{ route('admin.api-docs.export-endpoint', 'daily-summary') }}">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-primary badge-xs font-mono font-bold">GET</span>
                                    <span class="font-mono truncate max-w-[150px]">/daily-summary</span>
                                </div>
                                <i data-lucide="download" class="w-3.5 h-3.5 text-base-content/50"></i>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center justify-between py-2 text-xs text-base-content" href="{{ route('admin.api-docs.export-endpoint', 'attendance-single') }}">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-primary badge-xs font-mono font-bold">GET</span>
                                    <span class="font-mono truncate max-w-[150px]">/v1/attendances/{id}</span>
                                </div>
                                <i data-lucide="download" class="w-3.5 h-3.5 text-base-content/50"></i>
                            </a>
                        </li>
                        <li class="border-t border-base-200 mt-1 pt-1">
                            <a class="flex items-center gap-2 py-2 text-xs font-semibold text-primary" href="{{ route('admin.api-docs.export-txt') }}">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Complete Specification (.txt)</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Grid: Quick Nav Sidebar + Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Quick Navigation Sticky Card (Desktop) -->
        <div class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-20 space-y-4">
            <!-- Base URL Card -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-base-content/70">Base URL</span>
                        <span class="badge badge-success badge-soft font-mono text-[10px]">Online</span>
                    </div>
                    <div class="join w-full">
                        <input type="text" class="input input-bordered input-sm join-item w-full font-mono text-xs bg-base-200/40 text-base-content" value="{{ $apiRootUrl }}" id="baseUrlInput" readonly>
                        <button class="btn btn-outline btn-sm join-item copy-btn shrink-0" type="button" data-copy-target="#baseUrlInput" title="Copy Base URL">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section Quick Navigation -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
                <div class="p-3.5 border-b border-base-200/60 flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-primary"></i>
                    <span class="text-xs font-bold uppercase tracking-wider text-base-content">API Endpoints</span>
                </div>
                <ul class="menu menu-sm p-2 w-full gap-0.5">
                    <li>
                        <a href="#section-overview" class="flex items-center justify-between py-2 text-base-content/80 hover:bg-base-200 rounded-lg">
                            <span class="font-medium text-xs">Overview & Auth</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-base-content/40"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#endpoint-hrsale-attendance" class="flex items-center justify-between py-2 text-base-content/80 hover:bg-base-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-success badge-xs font-mono font-bold">POST</span>
                                <span class="font-mono text-xs truncate max-w-[120px]">/attendance</span>
                            </div>
                            <span class="badge badge-primary badge-soft font-mono text-[10px]">HRsale</span>
                        </a>
                    </li>
                    <li>
                        <a href="#endpoint-auth-token" class="flex items-center justify-between py-2 text-base-content/80 hover:bg-base-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-success badge-xs font-mono font-bold">POST</span>
                                <span class="font-mono text-xs truncate max-w-[130px]">/v1/auth/token</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#endpoint-attendances-list" class="flex items-center justify-between py-2 text-base-content/80 hover:bg-base-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-primary badge-xs font-mono font-bold">GET</span>
                                <span class="font-mono text-xs truncate max-w-[130px]">/v1/attendances</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#endpoint-daily-summary" class="flex items-center justify-between py-2 text-base-content/80 hover:bg-base-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-primary badge-xs font-mono font-bold">GET</span>
                                <span class="font-mono text-xs truncate max-w-[130px]">/daily-summary</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#endpoint-attendance-single" class="flex items-center justify-between py-2 text-base-content/80 hover:bg-base-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-primary badge-xs font-mono font-bold">GET</span>
                                <span class="font-mono text-xs truncate max-w-[130px]">/attendances/{id}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- API Help Callout -->
            <div class="p-4 rounded-xl bg-base-200/50 border border-base-200 text-base-content flex items-start gap-3">
                <i data-lucide="shield-check" class="w-5 h-5 text-success shrink-0 mt-0.5"></i>
                <div class="text-xs">
                    <div class="font-bold text-base-content mb-0.5">Sanctum Token Auth</div>
                    <p class="text-base-content/70 leading-relaxed">
                        Use your personal access token in the <code class="badge badge-neutral badge-xs">Authorization: Bearer</code> header for all secured calls.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: API Documentation Content -->
        <div class="lg:col-span-8 xl:col-span-9 space-y-6">

            <!-- Section 1: Overview & Authentication -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden" id="section-overview">
                <div class="p-5 sm:p-6 border-b border-base-200/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-base-content tracking-tight">Overview & Authentication</h2>
                        <p class="text-xs text-base-content/70 mt-0.5">How to authenticate and format requests against the ROI Attendance API.</p>
                    </div>
                    <span class="badge badge-neutral badge-soft font-mono text-xs">Rate Limit: 60 req/min</span>
                </div>

                <div class="card-body p-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-base-200/40 rounded-xl border border-base-200">
                            <h3 class="font-bold text-xs uppercase tracking-wider text-base-content flex items-center gap-2 mb-3">
                                <i data-lucide="send" class="w-4 h-4 text-primary"></i>
                                <span>Required HTTP Headers</span>
                            </h3>
                            <div class="space-y-2 font-mono text-xs">
                                <div class="p-2.5 bg-base-100 rounded-lg border border-base-200 text-base-content">
                                    <span class="text-primary font-bold">Authorization:</span> Bearer &lt;ACCESS_TOKEN&gt;
                                </div>
                                <div class="p-2.5 bg-base-100 rounded-lg border border-base-200 text-base-content">
                                    <span class="text-primary font-bold">Content-Type:</span> application/json
                                </div>
                                <div class="p-2.5 bg-base-100 rounded-lg border border-base-200 text-base-content">
                                    <span class="text-primary font-bold">Accept:</span> application/json
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-base-200/40 rounded-xl border border-base-200 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-base-content flex items-center gap-2 mb-3">
                                    <i data-lucide="code" class="w-4 h-4 text-success"></i>
                                    <span>Authentication Flow</span>
                                </h3>
                                <p class="text-xs text-base-content/70 leading-relaxed mb-3">
                                    Create an API token from the admin panel under <a href="{{ route('admin.api-tokens.index') }}" class="link link-primary font-semibold">API Tokens</a> or request one programmatically via <code class="badge badge-neutral badge-xs font-mono">POST /api/v1/auth/token</code>.
                                </p>
                            </div>
                            <div class="alert alert-info py-2 px-3 text-xs shadow-none">
                                <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                                <span>Tokens inherit read permissions for employee and attendance records.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Endpoint - HRsale Attendance (Featured) -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden" id="endpoint-hrsale-attendance">
                <div class="p-5 sm:p-6 border-b border-base-200/60">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge badge-success font-bold font-mono px-2 py-1 text-xs">POST</span>
                            <span class="font-bold text-base-content font-mono text-lg">/attendance</span>
                            <span class="badge badge-neutral badge-soft font-mono text-xs">or /v1/attendance</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.api-docs.export-endpoint', 'hrsale-attendance') }}" class="btn btn-outline btn-primary btn-xs sm:btn-sm gap-1.5 font-mono" title="Download individual .txt spec">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Download Doc</span>
                            </a>
                            <span class="badge badge-primary badge-soft font-semibold text-xs">HRsale Compatible</span>
                            <span class="badge badge-info badge-soft text-xs">Bearer Auth</span>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/70 leading-relaxed">
                        Retrieve, filter, and fetch today's or historical employee attendance logs conforming directly to the HRsale schema array.
                    </p>
                </div>

                <div class="card-body p-5 sm:p-6 space-y-6">
                    <!-- Request Parameters Table -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-base-content flex items-center gap-2 mb-3">
                            <i data-lucide="sliders" class="w-4 h-4 text-primary"></i>
                            <span>Request Body Parameters (Optional)</span>
                        </h3>
                        <div class="overflow-x-auto rounded-xl border border-base-200">
                            <table class="table table-zebra text-xs w-full">
                                <thead class="bg-base-200/60 text-base-content/70 font-semibold">
                                    <tr>
                                        <th class="w-1/4">Parameter</th>
                                        <th class="w-1/6">Type</th>
                                        <th class="w-1/6">Default</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">punch_date</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">string</span></td>
                                        <td><code class="font-mono text-xs">{{ date('Y-m-d') }}</code></td>
                                        <td class="text-base-content/80">Fetch attendance logs for a specific date (Format: <code class="font-mono text-xs">YYYY-MM-DD</code>).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">start_date</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">string</span></td>
                                        <td class="text-base-content/50 italic">None</td>
                                        <td class="text-base-content/80">Fetch logs starting from this date (inclusive, Format: <code class="font-mono text-xs">YYYY-MM-DD</code>).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">end_date</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">string</span></td>
                                        <td class="text-base-content/50 italic">None</td>
                                        <td class="text-base-content/80">Fetch logs up to this date (inclusive, Format: <code class="font-mono text-xs">YYYY-MM-DD</code>).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">company_id</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">int | string</span></td>
                                        <td class="text-base-content/50 italic">None</td>
                                        <td class="text-base-content/80">Filter logs for employees belonging to this company ID or company name.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">employee_id</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">int | string</span></td>
                                        <td class="text-base-content/50 italic">None</td>
                                        <td class="text-base-content/80">Filter logs for a specific Employee ID or Code.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">card_no</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">string</span></td>
                                        <td class="text-base-content/50 italic">None</td>
                                        <td class="text-base-content/80">Filter logs for a specific RFID / biometric card number.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Code Examples & Multi-language Tabs -->
                    <div class="card bg-base-200/30 border border-base-200 rounded-xl overflow-hidden">
                        <div class="p-3 border-b border-base-200 bg-base-100 flex flex-wrap items-center justify-between gap-2">
                            <div role="tablist" class="tabs tabs-box tabs-xs sm:tabs-sm" id="hrsaleCodeTabs">
                                <button role="tab" class="tab tab-active" onclick="switchCodeTab(this, '#content-curl')">cURL</button>
                                <button role="tab" class="tab" onclick="switchCodeTab(this, '#content-js')">JavaScript (Fetch)</button>
                                <button role="tab" class="tab" onclick="switchCodeTab(this, '#content-php')">PHP (cURL)</button>
                                <button role="tab" class="tab" onclick="switchCodeTab(this, '#content-py')">Python</button>
                            </div>
                            <button class="btn btn-outline btn-xs gap-1.5 font-mono copy-btn" type="button" data-copy-active-tab="#hrsaleCodeTabsContent">
                                <i data-lucide="copy" class="w-3 h-3"></i>
                                <span>Copy Code</span>
                            </button>
                        </div>
                        <div id="hrsaleCodeTabsContent">
                            <div class="tab-pane active block" id="content-curl">
                                <pre class="m-0 p-4 bg-slate-950 text-slate-100 font-mono text-xs overflow-x-auto leading-relaxed"><code>curl -X POST {{ $apiRootUrl }}/attendance \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"company_id": 1, "punch_date": "{{ date('Y-m-d') }}"}'</code></pre>
                            </div>
                            <div class="tab-pane hidden" id="content-js">
                                <pre class="m-0 p-4 bg-slate-950 text-slate-100 font-mono text-xs overflow-x-auto leading-relaxed"><code>fetch('{{ $apiRootUrl }}/attendance', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    company_id: 1,
    punch_date: '{{ date('Y-m-d') }}'
  })
})
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));</code></pre>
                            </div>
                            <div class="tab-pane hidden" id="content-php">
                                <pre class="m-0 p-4 bg-slate-950 text-slate-100 font-mono text-xs overflow-x-auto leading-relaxed"><code>&lt;?php
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => '{{ $apiRootUrl }}/attendance',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode([
    'company_id' => 1,
    'punch_date' => '{{ date('Y-m-d') }}'
  ]),
  CURLOPT_HTTPHEADER => [
    'Authorization: Bearer YOUR_ACCESS_TOKEN',
    'Content-Type: application/json',
    'Accept: application/json'
  ],
]);
$response = curl_exec($curl);
curl_close($curl);
echo $response;</code></pre>
                            </div>
                            <div class="tab-pane hidden" id="content-py">
                                <pre class="m-0 p-4 bg-slate-950 text-slate-100 font-mono text-xs overflow-x-auto leading-relaxed"><code>import requests

url = "{{ $apiRootUrl }}/attendance"
headers = {
    "Authorization": "Bearer YOUR_ACCESS_TOKEN",
    "Content-Type": "application/json",
    "Accept": "application/json"
}
payload = {
    "company_id": 1,
    "punch_date": "{{ date('Y-m-d') }}"
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Expected Response Preview -->
                    <div class="card bg-base-100 border border-base-200 rounded-xl overflow-hidden">
                        <div class="p-3.5 border-b border-base-200 bg-base-200/40 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-success font-mono font-bold text-xs">200 OK</span>
                                <span class="font-mono text-xs text-base-content/70">application/json</span>
                            </div>
                            <button class="btn btn-outline btn-xs gap-1.5 font-mono copy-btn" type="button" data-copy-target="#hrsaleResponsePreview">
                                <i data-lucide="copy" class="w-3 h-3"></i>
                                <span>Copy Response</span>
                            </button>
                        </div>
                        <pre class="m-0 p-4 bg-slate-950 text-emerald-400 font-mono text-xs max-h-80 overflow-y-auto leading-relaxed" id="hrsaleResponsePreview"><code>[
  {
    "time_attendance_id": "12",
    "card_no": "1002",
    "punch_date": "{{ date('Y-m-d') }}",
    "clock_in": "09:00:00",
    "clock_out": "18:00:00",
    "total_work": "09:00:00",
    "employee_id": "24",
    "employee_name": "John Doe",
    "company_name": "Demo Company"
  }
]</code></pre>
                    </div>
                </div>
            </div>

            <!-- Section 3: Endpoint - Auth Token Generation -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden" id="endpoint-auth-token">
                <div class="p-5 sm:p-6 border-b border-base-200/60">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-success font-bold font-mono px-2 py-1 text-xs">POST</span>
                            <span class="font-bold text-base-content font-mono text-lg">/v1/auth/token</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.api-docs.export-endpoint', 'auth-token') }}" class="btn btn-outline btn-primary btn-xs sm:btn-sm gap-1.5 font-mono" title="Download individual .txt spec">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Download Doc</span>
                            </a>
                            <span class="badge badge-neutral badge-soft text-xs">Public (10 req/min)</span>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/70 leading-relaxed">
                        Exchange admin credentials for a personal Bearer access token.
                    </p>
                </div>
                <div class="card-body p-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-base-content">Request Body (JSON)</span>
                                <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#authTokenReq">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-slate-950 text-slate-100 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed" id="authTokenReq"><code>{
  "email": "admin@example.com",
  "password": "your_secure_password",
  "token_name": "hrsale_service"
}</code></pre>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-base-content">Response (200 OK)</span>
                                <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#authTokenRes">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed" id="authTokenRes"><code>{
  "success": true,
  "message": "Token generated successfully.",
  "token": "1|qWeRtYuIoP123456789...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@example.com"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Endpoint - Paginated Attendances (v1) -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden" id="endpoint-attendances-list">
                <div class="p-5 sm:p-6 border-b border-base-200/60">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-primary font-bold font-mono px-2 py-1 text-xs">GET</span>
                            <span class="font-bold text-base-content font-mono text-lg">/v1/attendances</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.api-docs.export-endpoint', 'attendances-list') }}" class="btn btn-outline btn-primary btn-xs sm:btn-sm gap-1.5 font-mono" title="Download individual .txt spec">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Download Doc</span>
                            </a>
                            <span class="badge badge-info badge-soft text-xs">Bearer Auth (60 req/min)</span>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/70 leading-relaxed">
                        Query paginated attendance punch records with comprehensive search and metadata filters.
                    </p>
                </div>
                <div class="card-body p-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-base-content mb-3">Query Parameters</h3>
                        <div class="overflow-x-auto rounded-xl border border-base-200">
                            <table class="table table-zebra text-xs w-full">
                                <thead class="bg-base-200/60 text-base-content/70 font-semibold">
                                    <tr>
                                        <th class="w-1/4">Parameter</th>
                                        <th class="w-1/4">Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">start_date</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">String (YYYY-MM-DD)</span></td>
                                        <td class="text-base-content/80">Filter records on or after this date.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">end_date</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">String (YYYY-MM-DD)</span></td>
                                        <td class="text-base-content/80">Filter records on or before this date.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">employee_id</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">String</span></td>
                                        <td class="text-base-content/80">Filter by Employee ID code (e.g. <code class="font-mono text-xs">EMP-1001</code>).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">card_no</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">String</span></td>
                                        <td class="text-base-content/80">Filter by RFID / Biometric Card number (e.g. <code class="font-mono text-xs">7701</code>).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">company</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">String</span></td>
                                        <td class="text-base-content/80">Filter by Company Name.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">status</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">String</span></td>
                                        <td class="text-base-content/80">Filter by status (<code class="font-mono text-xs">present</code> or <code class="font-mono text-xs">late</code>).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="font-bold text-primary font-mono">per_page</code></td>
                                        <td><span class="badge badge-neutral badge-soft font-mono text-[11px]">Integer</span></td>
                                        <td class="text-base-content/80">Records per page (1 to 100, default: 25).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-base-content">Example cURL Request</span>
                            <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#getAttendancesCurl">
                                <i data-lucide="copy" class="w-3 h-3"></i> Copy
                            </button>
                        </div>
                        <pre class="bg-slate-950 text-slate-100 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed mb-4" id="getAttendancesCurl"><code>curl -X GET "{{ $baseUrl }}/attendances?start_date={{ date('Y-m-d') }}&status=present" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>

                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-base-content">Paginated Response Sample</span>
                            <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#getAttendancesRes">
                                <i data-lucide="copy" class="w-3 h-3"></i> Copy
                            </button>
                        </div>
                        <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs border border-base-300 max-h-60 overflow-y-auto leading-relaxed" id="getAttendancesRes"><code>{
  "success": true,
  "message": "Attendance records retrieved successfully.",
  "data": [
    {
      "id": 1,
      "card_no": "7701",
      "punch_date": "{{ date('Y-m-d') }}",
      "check_in_time": "08:55:00",
      "check_out_time": "17:05:00",
      "show_status": "present",
      "employee": {
        "employee_id": "EMP-1001",
        "full_name": "Alexander Pierce",
        "email": "alex.pierce@example.com",
        "company": "ROI Technologies"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 25,
    "total": 1
  }
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Section 5: Endpoint - Daily Summary -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden" id="endpoint-daily-summary">
                <div class="p-5 sm:p-6 border-b border-base-200/60">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-primary font-bold font-mono px-2 py-1 text-xs">GET</span>
                            <span class="font-bold text-base-content font-mono text-lg">/v1/attendances/daily-summary</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.api-docs.export-endpoint', 'daily-summary') }}" class="btn btn-outline btn-primary btn-xs sm:btn-sm gap-1.5 font-mono" title="Download individual .txt spec">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Download Doc</span>
                            </a>
                            <span class="badge badge-info badge-soft text-xs">Bearer Auth</span>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/70 leading-relaxed">
                        Retrieve high-level daily attendance metrics (total punches, present count, late count).
                    </p>
                </div>
                <div class="card-body p-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-base-content block mb-1">Query Parameters</span>
                            <p class="text-xs text-base-content/70 mb-3"><code class="font-mono text-primary font-bold">date</code> &mdash; (Optional, <code class="font-mono">YYYY-MM-DD</code>, defaults to today: <code class="font-mono">{{ date('Y-m-d') }}</code>).</p>
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-base-content">cURL Request</span>
                                <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#dailySummaryCurl">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-slate-950 text-slate-100 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed" id="dailySummaryCurl"><code>curl -X GET "{{ $baseUrl }}/attendances/daily-summary?date={{ date('Y-m-d') }}" \
  -H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-base-content">Sample Response (200 OK)</span>
                                <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#dailySummaryRes">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed" id="dailySummaryRes"><code>{
  "success": true,
  "date": "{{ date('Y-m-d') }}",
  "summary": {
    "total_punches": 113,
    "present_count": 98,
    "late_count": 15
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 6: Endpoint - Single Attendance Record -->
            <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden" id="endpoint-attendance-single">
                <div class="p-5 sm:p-6 border-b border-base-200/60">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-primary font-bold font-mono px-2 py-1 text-xs">GET</span>
                            <span class="font-bold text-base-content font-mono text-lg">/v1/attendances/{id}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.api-docs.export-endpoint', 'attendance-single') }}" class="btn btn-outline btn-primary btn-xs sm:btn-sm gap-1.5 font-mono" title="Download individual .txt spec">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Download Doc</span>
                            </a>
                            <span class="badge badge-info badge-soft text-xs">Bearer Auth</span>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/70 leading-relaxed">
                        Retrieve complete record details for a specific attendance entry by its ID.
                    </p>
                </div>
                <div class="card-body p-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-base-content block mb-1">URI Parameters</span>
                            <p class="text-xs text-base-content/70 mb-3"><code class="font-mono text-primary font-bold">id</code> &mdash; (Required, Integer, ID of the attendance record).</p>
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-base-content">cURL Request</span>
                                <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#singleAttendanceCurl">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-slate-950 text-slate-100 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed" id="singleAttendanceCurl"><code>curl -X GET "{{ $baseUrl }}/attendances/1" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-base-content">Sample Response (200 OK)</span>
                                <button class="btn btn-outline btn-xs gap-1 font-mono copy-btn" type="button" data-copy-target="#singleAttendanceRes">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs border border-base-300 leading-relaxed" id="singleAttendanceRes"><code>{
  "success": true,
  "data": {
    "id": 1,
    "card_no": "7701",
    "punch_date": "{{ date('Y-m-d') }}",
    "check_in_time": "08:55:00",
    "check_out_time": "17:05:00",
    "show_status": "present",
    "employee": {
      "employee_id": "EMP-1001",
      "full_name": "Alexander Pierce",
      "email": "alex.pierce@example.com",
      "company": "ROI Technologies"
    }
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Script for Interactive Copy Buttons & Code Tab Switching -->
    <script>
        function switchCodeTab(tabElement, targetPaneId) {
            const tabsContainer = tabElement.closest('#hrsaleCodeTabs');
            if (tabsContainer) {
                tabsContainer.querySelectorAll('.tab').forEach(t => t.classList.remove('tab-active'));
            }
            tabElement.classList.add('tab-active');

            const panesContainer = document.getElementById('hrsaleCodeTabsContent');
            if (panesContainer) {
                panesContainer.querySelectorAll('.tab-pane').forEach(p => {
                    p.classList.add('hidden');
                    p.classList.remove('block', 'active');
                });
                const target = document.querySelector(targetPaneId);
                if (target) {
                    target.classList.remove('hidden');
                    target.classList.add('block', 'active');
                }
            }
        }

        (function() {
            function copyTextToClipboard(text, btnElement) {
                if (!text) return;

                function showSuccess() {
                    if (!btnElement) return;
                    const originalHTML = btnElement.innerHTML;
                    btnElement.innerHTML = '<i data-lucide="check" class="w-3 h-3 text-success"></i> <span class="text-success font-bold">Copied!</span>';
                    if (window.renderLucideIcons) {
                        try { window.renderLucideIcons(); } catch(e) {}
                    }
                    setTimeout(function() {
                        btnElement.innerHTML = originalHTML;
                        if (window.renderLucideIcons) {
                            try { window.renderLucideIcons(); } catch(e) {}
                        }
                    }, 2000);
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(showSuccess).catch(function() {
                        fallbackCopy(text, showSuccess);
                    });
                } else {
                    fallbackCopy(text, showSuccess);
                }
            }

            function fallbackCopy(text, callback) {
                try {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    textArea.style.position = "fixed";
                    textArea.style.left = "-999999px";
                    textArea.style.top = "-999999px";
                    textArea.setAttribute("readonly", "");
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    textArea.setSelectionRange(0, 99999);
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textArea);
                    if (successful && callback) {
                        callback();
                    }
                } catch (err) {
                    console.error('Fallback copy execution failed: ', err);
                }
            }

            // Delegated click handler for copy buttons
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.copy-btn');
                if (!btn) return;
                
                e.preventDefault();
                e.stopPropagation();

                let textToCopy = '';

                if (btn.dataset.copyTarget) {
                    const target = document.querySelector(btn.dataset.copyTarget);
                    if (target) {
                        textToCopy = target.value !== undefined && target.tagName === 'INPUT' 
                            ? target.value 
                            : (target.innerText || target.textContent);
                    }
                } else if (btn.dataset.copyActiveTab) {
                    const container = document.querySelector(btn.dataset.copyActiveTab);
                    if (container) {
                        const activePane = container.querySelector('.tab-pane.active');
                        if (activePane) {
                            textToCopy = activePane.innerText || activePane.textContent;
                        }
                    }
                }

                if (textToCopy) {
                    copyTextToClipboard(textToCopy.trim(), btn);
                }
            });
        })();
    </script>
</x-admin-layout>
