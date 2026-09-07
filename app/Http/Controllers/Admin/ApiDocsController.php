<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiDocsController extends Controller
{
    public function index(): View
    {
        $baseUrl = url('/api/v1');
        $apiRootUrl = url('/api');
        return view('admin.api-docs.index', compact('baseUrl', 'apiRootUrl'));
    }

    public function exportTxt(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="attendance_api_specification.txt"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $baseUrl = url('/api/v1');
        $apiRootUrl = url('/api');
        $today = date('Y-m-d');

        $content = <<<TXT
================================================================================
                    ROI ATTENDANCE REST API SPECIFICATION
================================================================================
Base URL: {$apiRootUrl}
Version: v1 / HRsale Compatible
Auth: Bearer Token (Laravel Sanctum)
Header: Authorization: Bearer <YOUR_API_TOKEN>
Header: Content-Type: application/json

--------------------------------------------------------------------------------
1. AUTHENTICATION & TOKEN GENERATION
--------------------------------------------------------------------------------
Endpoint: POST {$baseUrl}/auth/token
Description: Exchange credentials for a personal access Bearer token.
Rate Limit: 10 requests per minute

Request Headers:
  Content-Type: application/json
  Accept: application/json

Request Body:
  {
    "email": "admin@example.com",
    "password": "password",
    "token_name": "hrsale_integration"
  }

Response (200 OK):
  {
    "success": true,
    "message": "Token generated successfully.",
    "token": "1|AbCdEf123456...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@example.com"
    }
  }

--------------------------------------------------------------------------------
2. HRSALE COMPATIBLE ATTENDANCE ENDPOINT (PRIMARY)
--------------------------------------------------------------------------------
Endpoint: POST {$apiRootUrl}/attendance
Alternative: POST {$baseUrl}/attendance
Description: Retrieve, filter, and fetch employee attendance logs matching HRsale schema.
Rate Limit: 60 requests per minute
Auth: Required (Bearer Token)
Request Format: JSON, URL-encoded, or Form-Data

Headers:
  Authorization: Bearer <YOUR_API_TOKEN> (Required)
  Content-Type: application/json         (Recommended)

POST Body Parameters (All Optional):
  - punch_date   (string, YYYY-MM-DD) Fetch logs for a specific date (Defaults to current server date if no dates provided).
  - start_date   (string, YYYY-MM-DD) Fetch logs starting from this date (inclusive).
  - end_date     (string, YYYY-MM-DD) Fetch logs up to this date (inclusive).
  - company_id   (integer/string)     Filter logs for employees belonging to this company.
  - employee_id  (integer/string)     Filter logs for a specific employee ID / code.
  - card_no      (string)             Filter logs for a specific RFID/biometric card number.

cURL Example:
  curl -X POST {$apiRootUrl}/attendance \\
    -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \\
    -H "Content-Type: application/json" \\
    -d '{"company_id": 1, "punch_date": "{$today}"}'

JavaScript Fetch Example:
  fetch('{$apiRootUrl}/attendance', {
    method: 'POST',
    headers: {
      'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      company_id: 1,
      punch_date: '{$today}'
    })
  })
  .then(response => response.json())
  .then(data => console.log(data));

Response Schema (200 OK):
  [
    {
      "time_attendance_id": "12",
      "card_no": "1002",
      "punch_date": "{$today}",
      "clock_in": "09:00:00",
      "clock_in_ip_address": "127.0.0.1",
      "clock_out": "18:00:00",
      "clock_out_ip_address": "127.0.0.1",
      "clock_in_out": "1",
      "time_late": "00:00:00",
      "early_leaving": "00:00:00",
      "overtime": "00:00:00",
      "total_work": "09:00:00",
      "total_rest": "00:00:00",
      "attendance_status": "Present",
      "employee_id": "24",
      "employee_name": "John Doe",
      "company_name": "Demo Company"
    }
  ]

HTTP Status Codes:
  - 200 OK: Request succeeded. Returns array of attendance log objects.
  - 401 Unauthorized: {"error": "Missing or Invalid Authorization token"}

--------------------------------------------------------------------------------
3. PAGINATED ATTENDANCE RECORDS (V1 PAGINATED API)
--------------------------------------------------------------------------------
Endpoint: GET {$baseUrl}/attendances
Description: Retrieve paginated attendance logs with filter parameters.
Rate Limit: 60 requests per minute
Auth: Required (Bearer Token)

Query Parameters:
  - start_date   (optional, YYYY-MM-DD) Filter records on or after this date.
  - end_date     (optional, YYYY-MM-DD) Filter records on or before this date.
  - employee_id  (optional, string)     Filter by Employee Code (e.g. EMP-1001).
  - card_no      (optional, string)     Filter by Biometric Card Number (e.g. 7701).
  - company      (optional, string)     Filter by Company Name.
  - status       (optional, string)     Filter by status ('present' or 'late').
  - per_page     (optional, integer)    Records per page (1 to 100, default: 25).
  - page         (optional, integer)    Page number for pagination.

--------------------------------------------------------------------------------
4. GET DAILY ATTENDANCE SUMMARY METRICS
--------------------------------------------------------------------------------
Endpoint: GET {$baseUrl}/attendances/daily-summary
Description: Quick overview metrics for a given date.
Auth: Required (Bearer Token)

Query Parameters:
  - date (optional, YYYY-MM-DD, defaults to today)

Response (200 OK):
  {
    "success": true,
    "date": "{$today}",
    "summary": {
      "total_punches": 113,
      "present_count": 98,
      "late_count": 15
    }
  }

--------------------------------------------------------------------------------
5. GET SINGLE ATTENDANCE RECORD
--------------------------------------------------------------------------------
Endpoint: GET {$baseUrl}/attendances/{id}
Description: Get details for a specific attendance entry.
Auth: Required (Bearer Token)

Response (200 OK):
  {
    "success": true,
    "data": { ... }
  }

================================================================================
Generated on: {$today} by ROI Attendance Management System
================================================================================
TXT;

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, $headers);
    }
}
