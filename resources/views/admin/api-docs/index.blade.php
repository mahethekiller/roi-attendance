<x-admin-layout>
    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small fw-medium">Developer Hub</span>
                <span class="text-body-secondary small">&bull;</span>
                <span class="text-body-secondary small font-monospace">REST API v1.0 & HRsale Spec</span>
            </div>
            <h2 class="fw-bold text-body-emphasis mb-1">REST API Reference & Documentation</h2>
            <p class="text-body-secondary mb-0">Complete endpoint specifications, request parameters, interactive multi-language code snippets, and authentication guides.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.api-tokens.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                <span>API Tokens</span>
            </a>
            <a href="{{ route('admin.api-docs.export-txt') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                <span>Download Spec (.txt)</span>
            </a>
        </div>
    </div>

    <!-- Layout Grid: Quick Nav Sidebar + Content -->
    <div class="row g-4">
        
        <!-- Left Column: Quick Navigation Sticky Card (Desktop) -->
        <div class="col-xl-3 col-lg-4">
            <div class="position-sticky" style="top: 85px;">
                <!-- Base URL Card -->
                <div class="card border-0 shadow-sm bg-body text-body mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-body-emphasis text-uppercase" style="letter-spacing: 0.05em;">Base URL</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0">Online</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control font-monospace bg-body-tertiary text-body border" value="{{ $apiRootUrl }}" id="baseUrlInput" readonly>
                            <button class="btn btn-outline-secondary copy-btn" type="button" data-copy-target="#baseUrlInput" title="Copy Base URL">
                                <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Quick Navigation -->
                <div class="card border-0 shadow-sm bg-body text-body">
                    <div class="card-header bg-body border-bottom p-3">
                        <h6 class="fw-bold text-body-emphasis mb-0 d-flex align-items-center gap-2 small text-uppercase" style="letter-spacing: 0.05em;">
                            <i data-lucide="list" style="width: 16px; height: 16px;" class="text-primary"></i>
                            <span>API Endpoints</span>
                        </h6>
                    </div>
                    <div class="list-group list-group-flush small">
                        <a href="#section-overview" class="list-group-item list-group-item-action bg-transparent text-body d-flex align-items-center justify-content-between py-2 px-3">
                            <span>Overview & Auth</span>
                            <i data-lucide="chevron-right" style="width: 14px; height: 14px;" class="text-body-secondary"></i>
                        </a>
                        <a href="#endpoint-hrsale-attendance" class="list-group-item list-group-item-action bg-transparent text-body d-flex align-items-center justify-content-between py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success text-white fw-bold px-1 py-0 font-monospace" style="font-size: 0.7rem;">POST</span>
                                <span class="text-truncate font-monospace" style="max-width: 130px;">/attendance</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">HRsale</span>
                        </a>
                        <a href="#endpoint-auth-token" class="list-group-item list-group-item-action bg-transparent text-body d-flex align-items-center justify-content-between py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success text-white fw-bold px-1 py-0 font-monospace" style="font-size: 0.7rem;">POST</span>
                                <span class="text-truncate font-monospace" style="max-width: 140px;">/v1/auth/token</span>
                            </div>
                        </a>
                        <a href="#endpoint-attendances-list" class="list-group-item list-group-item-action bg-transparent text-body d-flex align-items-center justify-content-between py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white fw-bold px-1 py-0 font-monospace" style="font-size: 0.7rem;">GET</span>
                                <span class="text-truncate font-monospace" style="max-width: 140px;">/v1/attendances</span>
                            </div>
                        </a>
                        <a href="#endpoint-daily-summary" class="list-group-item list-group-item-action bg-transparent text-body d-flex align-items-center justify-content-between py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white fw-bold px-1 py-0 font-monospace" style="font-size: 0.7rem;">GET</span>
                                <span class="text-truncate font-monospace" style="max-width: 140px;">/daily-summary</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- API Help Callout -->
                <div class="card border-0 shadow-sm bg-body-tertiary text-body mt-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-2">
                            <i data-lucide="shield-check" style="width: 18px; height: 18px;" class="text-success mt-1 flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-body-emphasis small mb-1">Sanctum Token Auth</h6>
                                <p class="text-body-secondary small mb-0">Use your personal access token in the <code>Authorization: Bearer</code> header for all secured calls.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: API Documentation Content -->
        <div class="col-xl-9 col-lg-8">

            <!-- Section 1: Overview & Authentication -->
            <div class="card border-0 shadow-sm bg-body text-body mb-4" id="section-overview">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                        <div>
                            <h4 class="fw-bold text-body-emphasis mb-1">Overview & Authentication</h4>
                            <p class="text-body-secondary small mb-0">How to authenticate and format requests against the ROI Attendance API.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle font-monospace">Rate Limit: 60 req/min</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3 border h-100">
                                <h6 class="fw-bold text-body-emphasis small mb-2 d-flex align-items-center gap-2">
                                    <i data-lucide="send" style="width: 15px; height: 15px;" class="text-primary"></i>
                                    <span>Required HTTP Headers</span>
                                </h6>
                                <div class="font-monospace small text-body d-flex flex-column gap-1">
                                    <div class="p-2 bg-body rounded border"><span class="text-primary fw-semibold">Authorization:</span> Bearer &lt;ACCESS_TOKEN&gt;</div>
                                    <div class="p-2 bg-body rounded border"><span class="text-primary fw-semibold">Content-Type:</span> application/json</div>
                                    <div class="p-2 bg-body rounded border"><span class="text-primary fw-semibold">Accept:</span> application/json</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3 border h-100">
                                <h6 class="fw-bold text-body-emphasis small mb-2 d-flex align-items-center gap-2">
                                    <i data-lucide="code" style="width: 15px; height: 15px;" class="text-success"></i>
                                    <span>Authentication Flow</span>
                                </h6>
                                <p class="text-body-secondary small mb-2">Create an API token from the admin panel under <a href="{{ route('admin.api-tokens.index') }}" class="text-primary text-decoration-none fw-semibold">API Tokens</a> or request one programmatically via <code>POST /api/v1/auth/token</code>.</p>
                                <div class="alert alert-info py-2 px-3 small mb-0 d-flex align-items-center gap-2">
                                    <i data-lucide="info" style="width: 16px; height: 16px;" class="flex-shrink-0"></i>
                                    <span>Tokens inherit read permissions for employee and attendance records.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Endpoint - HRsale Attendance (Featured) -->
            <div class="card border-0 shadow-sm bg-body text-body mb-4" id="endpoint-hrsale-attendance">
                <div class="card-header bg-body border-bottom p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-success text-white fw-bold px-2 py-1 font-monospace fs-6">POST</span>
                            <span class="fw-bold text-body-emphasis font-monospace fs-5">/attendance</span>
                            <span class="badge bg-body-tertiary text-body-secondary border font-monospace small">or /v1/attendance</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold">HRsale Compatible</span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Bearer Auth</span>
                        </div>
                    </div>
                    <p class="text-body-secondary mt-2 mb-0 small">Retrieve, filter, and fetch today's or historical employee attendance logs conforming directly to the HRsale schema array.</p>
                </div>

                <div class="card-body p-3 p-md-4">
                    <!-- Request Parameters Table -->
                    <h6 class="fw-bold text-body-emphasis small text-uppercase mb-3 d-flex align-items-center gap-2">
                        <i data-lucide="sliders" style="width: 15px; height: 15px;" class="text-primary"></i>
                        <span>Request Body Parameters (Optional)</span>
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-bordered align-middle mb-0 small">
                            <thead class="table-body-secondary">
                                <tr>
                                    <th style="width: 20%;">Parameter</th>
                                    <th style="width: 15%;">Type</th>
                                    <th style="width: 20%;">Default</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code class="fw-semibold text-primary">punch_date</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">string</span></td>
                                    <td><code>{{ date('Y-m-d') }}</code></td>
                                    <td>Fetch attendance logs for a specific date (Format: <code>YYYY-MM-DD</code>).</td>
                                </tr>
                                <tr>
                                    <td><code class="fw-semibold text-primary">start_date</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">string</span></td>
                                    <td><em class="text-body-secondary">None</em></td>
                                    <td>Fetch logs starting from this date (inclusive, Format: <code>YYYY-MM-DD</code>).</td>
                                </tr>
                                <tr>
                                    <td><code class="fw-semibold text-primary">end_date</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">string</span></td>
                                    <td><em class="text-body-secondary">None</em></td>
                                    <td>Fetch logs up to this date (inclusive, Format: <code>YYYY-MM-DD</code>).</td>
                                </tr>
                                <tr>
                                    <td><code class="fw-semibold text-primary">company_id</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">int | string</span></td>
                                    <td><em class="text-body-secondary">None</em></td>
                                    <td>Filter logs for employees belonging to this company ID or company name.</td>
                                </tr>
                                <tr>
                                    <td><code class="fw-semibold text-primary">employee_id</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">int | string</span></td>
                                    <td><em class="text-body-secondary">None</em></td>
                                    <td>Filter logs for a specific Employee ID or Code.</td>
                                </tr>
                                <tr>
                                    <td><code class="fw-semibold text-primary">card_no</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">string</span></td>
                                    <td><em class="text-body-secondary">None</em></td>
                                    <td>Filter logs for a specific RFID / biometric card number.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Code Examples & Response Tabs -->
                    <div class="card border bg-body-tertiary shadow-none rounded-3 mb-4">
                        <div class="card-header bg-body border-bottom p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <ul class="nav nav-pills card-header-pills small" id="hrsaleCodeTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3" id="tab-curl" data-bs-toggle="tab" data-bs-target="#content-curl" type="button" role="tab">cURL</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3" id="tab-js" data-bs-toggle="tab" data-bs-target="#content-js" type="button" role="tab">JavaScript (Fetch)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3" id="tab-php" data-bs-toggle="tab" data-bs-target="#content-php" type="button" role="tab">PHP (cURL)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3" id="tab-py" data-bs-toggle="tab" data-bs-target="#content-py" type="button" role="tab">Python</button>
                                </li>
                            </ul>
                            <button class="btn btn-outline-secondary btn-sm px-2 py-1 copy-btn d-flex align-items-center gap-1 font-monospace" type="button" style="font-size: 0.75rem;" data-copy-active-tab="#hrsaleCodeTabsContent">
                                <i data-lucide="copy" style="width: 13px; height: 13px;"></i>
                                <span>Copy Code</span>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="tab-content" id="hrsaleCodeTabsContent">
                                <div class="tab-pane fade show active" id="content-curl" role="tabpanel">
                                    <pre class="m-0 p-3 bg-body-secondary font-monospace small text-body" style="border-radius: 0 0 0.5rem 0.5rem; overflow-x: auto;"><code>curl -X POST {{ $apiRootUrl }}/attendance \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"company_id": 1, "punch_date": "{{ date('Y-m-d') }}"}'</code></pre>
                                </div>
                                <div class="tab-pane fade" id="content-js" role="tabpanel">
                                    <pre class="m-0 p-3 bg-body-secondary font-monospace small text-body" style="border-radius: 0 0 0.5rem 0.5rem; overflow-x: auto;"><code>fetch('{{ $apiRootUrl }}/attendance', {
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
                                <div class="tab-pane fade" id="content-php" role="tabpanel">
                                    <pre class="m-0 p-3 bg-body-secondary font-monospace small text-body" style="border-radius: 0 0 0.5rem 0.5rem; overflow-x: auto;"><code>&lt;?php
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
                                <div class="tab-pane fade" id="content-py" role="tabpanel">
                                    <pre class="m-0 p-3 bg-body-secondary font-monospace small text-body" style="border-radius: 0 0 0.5rem 0.5rem; overflow-x: auto;"><code>import requests

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
                    </div>

                    <!-- Expected Response Preview -->
                    <div class="card border shadow-none rounded-3">
                        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success font-monospace px-2 py-1">200 OK</span>
                                <span class="fw-bold text-body-emphasis small font-monospace">application/json</span>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm px-2 py-1 copy-btn d-flex align-items-center gap-1 font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#hrsaleResponsePreview">
                                <i data-lucide="copy" style="width: 13px; height: 13px;"></i>
                                <span>Copy Response</span>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <pre class="m-0 p-3 bg-body-secondary font-monospace small text-body" id="hrsaleResponsePreview" style="border-radius: 0 0 0.5rem 0.5rem; max-height: 320px; overflow-y: auto;"><code>[
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
            </div>

            <!-- Section 3: Endpoint - Auth Token Generation -->
            <div class="card border-0 shadow-sm bg-body text-body mb-4" id="endpoint-auth-token">
                <div class="card-header bg-body border-bottom p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success text-white fw-bold px-2 py-1 font-monospace fs-6">POST</span>
                            <span class="fw-bold text-body-emphasis font-monospace fs-5">/v1/auth/token</span>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Public (10 req/min)</span>
                    </div>
                    <p class="text-body-secondary mt-2 mb-0 small">Exchange admin credentials for a personal Bearer access token.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-0">Request Body (JSON)</h6>
                                <button class="btn btn-outline-secondary btn-sm px-2 py-0 copy-btn font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#authTokenReq">
                                    <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-body-secondary p-3 rounded-3 border font-monospace small text-body mb-0" id="authTokenReq"><code>{
  "email": "admin@example.com",
  "password": "your_secure_password",
  "token_name": "hrsale_service"
}</code></pre>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-0">Response (200 OK)</h6>
                                <button class="btn btn-outline-secondary btn-sm px-2 py-0 copy-btn font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#authTokenRes">
                                    <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-body-secondary p-3 rounded-3 border font-monospace small text-body mb-0" id="authTokenRes"><code>{
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
            <div class="card border-0 shadow-sm bg-body text-body mb-4" id="endpoint-attendances-list">
                <div class="card-header bg-body border-bottom p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white fw-bold px-2 py-1 font-monospace fs-6">GET</span>
                            <span class="fw-bold text-body-emphasis font-monospace fs-5">/v1/attendances</span>
                        </div>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Bearer Auth (60 req/min)</span>
                    </div>
                    <p class="text-body-secondary mt-2 mb-0 small">Query paginated attendance punch records with comprehensive search and metadata filters.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-body-emphasis small text-uppercase mb-3">Query Parameters</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-bordered align-middle mb-0 small">
                            <thead class="table-body-secondary">
                                <tr>
                                    <th style="width: 20%;">Parameter</th>
                                    <th style="width: 20%;">Type</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>start_date</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">String (YYYY-MM-DD)</span></td>
                                    <td>Filter records on or after this date.</td>
                                </tr>
                                <tr>
                                    <td><code>end_date</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">String (YYYY-MM-DD)</span></td>
                                    <td>Filter records on or before this date.</td>
                                </tr>
                                <tr>
                                    <td><code>employee_id</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">String</span></td>
                                    <td>Filter by Employee ID code (e.g. <code>EMP-1001</code>).</td>
                                </tr>
                                <tr>
                                    <td><code>card_no</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">String</span></td>
                                    <td>Filter by RFID / Biometric Card number (e.g. <code>7701</code>).</td>
                                </tr>
                                <tr>
                                    <td><code>company</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">String</span></td>
                                    <td>Filter by Company Name.</td>
                                </tr>
                                <tr>
                                    <td><code>status</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">String</span></td>
                                    <td>Filter by status (<code>present</code> or <code>late</code>).</td>
                                </tr>
                                <tr>
                                    <td><code>per_page</code></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">Integer</span></td>
                                    <td>Records per page (1 to 100, default: 25).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-body-emphasis small text-uppercase mb-0">Example cURL Request</h6>
                        <button class="btn btn-outline-secondary btn-sm px-2 py-0 copy-btn font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#getAttendancesCurl">
                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                        </button>
                    </div>
                    <pre class="bg-body-secondary p-3 rounded-3 border font-monospace small text-body mb-3" id="getAttendancesCurl"><code>curl -X GET "{{ $baseUrl }}/attendances?start_date={{ date('Y-m-d') }}&status=present" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-body-emphasis small text-uppercase mb-0">Paginated Response Sample</h6>
                        <button class="btn btn-outline-secondary btn-sm px-2 py-0 copy-btn font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#getAttendancesRes">
                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                        </button>
                    </div>
                    <pre class="bg-body-secondary p-3 rounded-3 border font-monospace small text-body mb-0" id="getAttendancesRes" style="max-height: 240px; overflow-y: auto;"><code>{
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

            <!-- Section 5: Endpoint - Daily Summary -->
            <div class="card border-0 shadow-sm bg-body text-body mb-4" id="endpoint-daily-summary">
                <div class="card-header bg-body border-bottom p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white fw-bold px-2 py-1 font-monospace fs-6">GET</span>
                            <span class="fw-bold text-body-emphasis font-monospace fs-5">/v1/attendances/daily-summary</span>
                        </div>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Bearer Auth</span>
                    </div>
                    <p class="text-body-secondary mt-2 mb-0 small">Retrieve high-level daily attendance metrics (total punches, present count, late count).</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Query Parameters</h6>
                            <p class="small text-body-secondary"><code>date</code> &mdash; (Optional, <code>YYYY-MM-DD</code>, defaults to today's date: <code>{{ date('Y-m-d') }}</code>).</p>
                            
                            <div class="d-flex align-items-center justify-content-between mb-2 mt-3">
                                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-0">cURL Request</h6>
                                <button class="btn btn-outline-secondary btn-sm px-2 py-0 copy-btn font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#dailySummaryCurl">
                                    <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-body-secondary p-3 rounded-3 border font-monospace small text-body mb-0" id="dailySummaryCurl"><code>curl -X GET "{{ $baseUrl }}/attendances/daily-summary?date={{ date('Y-m-d') }}" \
  -H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-0">Sample Response (200 OK)</h6>
                                <button class="btn btn-outline-secondary btn-sm px-2 py-0 copy-btn font-monospace" type="button" style="font-size: 0.75rem;" data-copy-target="#dailySummaryRes">
                                    <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Copy
                                </button>
                            </div>
                            <pre class="bg-body-secondary p-3 rounded-3 border font-monospace small text-body mb-0" id="dailySummaryRes"><code>{
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

        </div>
    </div>

    <!-- Client Script for Interactive Copy Buttons -->
    <script>
        (function() {
            function copyTextToClipboard(text, btnElement) {
                if (!text) return;

                function showSuccess() {
                    if (!btnElement) return;
                    const originalHTML = btnElement.innerHTML;
                    btnElement.innerHTML = '<i data-lucide="check" style="width: 13px; height: 13px;" class="text-success"></i> <span class="text-success fw-bold">Copied!</span>';
                    if (window.lucide) {
                        try { lucide.createIcons(); } catch(e) {}
                    }
                    setTimeout(function() {
                        btnElement.innerHTML = originalHTML;
                        if (window.lucide) {
                            try { lucide.createIcons(); } catch(e) {}
                        }
                    }, 2000);
                }

                // Try modern navigator.clipboard first if available
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

            // Global delegated event listener for all .copy-btn buttons
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
