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
        return view('admin.api-docs.index', compact('baseUrl'));
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

        $content = <<<TXT
================================================================================
                    ROI ATTENDANCE REST API SPECIFICATION
================================================================================
Base URL: {$baseUrl}
Version: v1
Auth: Bearer Token (Laravel Sanctum)
Header: Authorization: Bearer <YOUR_API_TOKEN>
Header: Accept: application/json

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
    "token_name": "mobile_app"
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
2. GET ATTENDANCE RECORDS (FILTERED & PAGINATED)
--------------------------------------------------------------------------------
Endpoint: GET {$baseUrl}/attendances
Description: Retrieve paginated attendance logs with optional filter parameters.
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

cURL Example:
  curl -X GET "{$baseUrl}/attendances?start_date=2026-09-01&status=present" \
       -H "Authorization: Bearer YOUR_TOKEN_HERE" \
       -H "Accept: application/json"

Response (200 OK):
  {
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
  }

--------------------------------------------------------------------------------
3. GET DAILY ATTENDANCE SUMMARY METRICS
--------------------------------------------------------------------------------
Endpoint: GET {$baseUrl}/attendances/daily-summary
Description: Quick overview metrics for a given date.
Auth: Required (Bearer Token)

Query Parameters:
  - date (optional, YYYY-MM-DD, defaults to today)

Response (200 OK):
  {
    "success": true,
    "date": "2026-09-02",
    "summary": {
      "total_punches": 113,
      "present_count": 98,
      "late_count": 15
    }
  }

--------------------------------------------------------------------------------
4. GET SINGLE ATTENDANCE RECORD
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
Generated on: 2026-09-02 by ROI Attendance Management System
================================================================================
TXT;

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, $headers);
    }
}
