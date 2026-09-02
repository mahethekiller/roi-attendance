<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">REST API Documentation</h2>
            <p class="text-body-secondary mb-0">Interactive developer reference, endpoints guide, authentication, and downloadable spec.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.api-tokens.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                <span>Manage API Tokens</span>
            </a>
            <a href="{{ route('admin.api-docs.export-txt') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                <span>Download Spec (.txt)</span>
            </a>
        </div>
    </div>

    <!-- Quick Overview Card -->
    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">v1.0 API</span>
                    <h5 class="fw-bold text-body-emphasis mb-1">Base URL: <code class="text-primary">{{ $baseUrl }}</code></h5>
                    <p class="text-body-secondary small mb-0">All endpoints require a Bearer token in the <code>Authorization</code> header unless specified otherwise.</p>
                </div>
                <div class="d-flex flex-column gap-1 bg-body-tertiary p-3 rounded-3 border">
                    <span class="small fw-semibold text-body-emphasis">Required Headers:</span>
                    <code class="small text-body">Authorization: Bearer &lt;YOUR_API_TOKEN&gt;</code>
                    <code class="small text-body">Accept: application/json</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Endpoints Section -->
    <div class="d-flex flex-column gap-4 mb-5">

        <!-- Endpoint 1: Auth Token -->
        <div class="card border-0 shadow-sm bg-body text-body">
            <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success text-white fw-bold px-2 py-1 font-monospace">POST</span>
                    <span class="fw-bold text-body-emphasis font-monospace">/auth/token</span>
                </div>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Public (10 req/min)</span>
            </div>
            <div class="card-body p-3">
                <p class="text-body-secondary mb-3">Exchange email and password credentials for a personal access Bearer token.</p>
                
                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Request Body (JSON)</h6>
                <pre class="bg-body-tertiary p-3 rounded-3 border font-monospace small text-body mb-3">{
  "email": "admin@example.com",
  "password": "password",
  "token_name": "mobile_app"
}</pre>

                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Sample Response (200 OK)</h6>
                <pre class="bg-body-tertiary p-3 rounded-3 border font-monospace small text-body mb-0">{
  "success": true,
  "message": "Token generated successfully.",
  "token": "1|qWeRtYuIoP123456789...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@example.com"
  }
}</pre>
            </div>
        </div>

        <!-- Endpoint 2: Get Attendances List -->
        <div class="card border-0 shadow-sm bg-body text-body">
            <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white fw-bold px-2 py-1 font-monospace">GET</span>
                    <span class="fw-bold text-body-emphasis font-monospace">/attendances</span>
                </div>
                <span class="badge bg-info-subtle text-info border border-info-subtle">Bearer Auth (60 req/min)</span>
            </div>
            <div class="card-body p-3">
                <p class="text-body-secondary mb-3">Query paginated attendance punch entries with granular filters.</p>
                
                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Query Parameters</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>start_date</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td>Filter records on or after this date.</td>
                            </tr>
                            <tr>
                                <td><code>end_date</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td>Filter records on or before this date.</td>
                            </tr>
                            <tr>
                                <td><code>employee_id</code></td>
                                <td>String</td>
                                <td>Filter by unique Employee ID (e.g. <code>EMP-1001</code>).</td>
                            </tr>
                            <tr>
                                <td><code>card_no</code></td>
                                <td>String</td>
                                <td>Filter by RFID / Biometric Card number (e.g. <code>7701</code>).</td>
                            </tr>
                            <tr>
                                <td><code>company</code></td>
                                <td>String</td>
                                <td>Filter by Company Name.</td>
                            </tr>
                            <tr>
                                <td><code>status</code></td>
                                <td>String</td>
                                <td>Filter by punch status (<code>present</code> or <code>late</code>).</td>
                            </tr>
                            <tr>
                                <td><code>per_page</code></td>
                                <td>Integer</td>
                                <td>Records per page (1 to 100, default: 25).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Example cURL Query</h6>
                <pre class="bg-body-tertiary p-3 rounded-3 border font-monospace small text-body mb-3">curl -X GET "{{ $baseUrl }}/attendances?start_date={{ date('Y-m-d') }}&status=present" \
     -H "Authorization: Bearer YOUR_API_TOKEN" \
     -H "Accept: application/json"</pre>

                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Sample Response (200 OK)</h6>
                <pre class="bg-body-tertiary p-3 rounded-3 border font-monospace small text-body mb-0">{
  "success": true,
  "message": "Attendance records retrieved successfully.",
  "data": [
    {
      "id": 1,
      "card_no": "7701",
      "badgenumber": "1001",
      "punch_date": "2026-09-02",
      "check_in_time": "08:55:00",
      "check_out_time": "17:05:00",
      "check_in_datetime": "2026-09-02 08:55:00",
      "check_out_datetime": "2026-09-02 17:05:00",
      "show_status": "present",
      "employee": {
        "employee_id": "EMP-1001",
        "full_name": "Alexander Pierce",
        "first_name": "Alexander",
        "last_name": "Pierce",
        "email": "alex.pierce@example.com",
        "company": "ROI Technologies"
      },
      "created_at": "2026-09-02T11:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 25,
    "total": 1
  }
}</pre>
            </div>
        </div>

        <!-- Endpoint 3: Daily Summary -->
        <div class="card border-0 shadow-sm bg-body text-body">
            <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white fw-bold px-2 py-1 font-monospace">GET</span>
                    <span class="fw-bold text-body-emphasis font-monospace">/attendances/daily-summary</span>
                </div>
                <span class="badge bg-info-subtle text-info border border-info-subtle">Bearer Auth</span>
            </div>
            <div class="card-body p-3">
                <p class="text-body-secondary mb-3">Retrieve high-level daily attendance metrics (total punches, present, and late counts).</p>

                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Query Parameters</h6>
                <p class="small text-body-secondary"><code>date</code> &mdash; (Optional, YYYY-MM-DD, defaults to today).</p>

                <h6 class="fw-bold text-body-emphasis small text-uppercase mb-2">Sample Response (200 OK)</h6>
                <pre class="bg-body-tertiary p-3 rounded-3 border font-monospace small text-body mb-0">{
  "success": true,
  "date": "{{ date('Y-m-d') }}",
  "summary": {
    "total_punches": 113,
    "present_count": 98,
    "late_count": 15
  }
}</pre>
            </div>
        </div>

    </div>
</x-admin-layout>
