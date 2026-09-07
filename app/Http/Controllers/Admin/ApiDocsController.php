<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
===============================================================================
                    ROI ATTENDANCE REST API SPECIFICATION
===============================================================================
Base URL: {$apiRootUrl}
Version: v1 / HRsale Compatible
Auth: Bearer Token (Laravel Sanctum)
Header: Authorization: Bearer <YOUR_API_TOKEN>
Header: Content-Type: application/json

-------------------------------------------------------------------------------
1. AUTHENTICATION & TOKEN GENERATION
-------------------------------------------------------------------------------
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

-------------------------------------------------------------------------------
2. HRSALE COMPATIBLE ATTENDANCE ENDPOINT (PRIMARY)
-------------------------------------------------------------------------------
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
  - card_no      (string)             Filter logs for a specific RFID\/biometric card number.
cURL Example:
  curl -X POST {$apiRootUrl}/attendance \
    -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
    -H "Content-Type: application/json" \
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
      "punch_date": "{+today}",
      "clock_in": "09:00:00",
      "clock_out": "18:00:00",
      "total_work": "09:00:00",
      "employee_id": "24",
      "employee_name": "John Doe",
      "company_name": "Demo Company"
    }
  ]

HTTP Status Codes:
  - 200 OK: Request succeeded. Returns array of attendance log objects.
  - 401 Unauthorized: {"error": "Missing or Invalid Authorization token"}

-------------------------------------------------------------------------------
3. PAGINATED ATTENDANCE RECORDS (V1 PAGINATED API)
-------------------------------------------------------------------------------
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
-------------------------------------------------------------------------------
4. GET DAILY ATTENDANCE SUMMARY METRICS
-------------------------------------------------------------------------------
Endpoint: GET {$baseUrl}/attendances/daily-summary
Description: Quick overview metrics for a given date.
Auth: Required (Bearer Token)

Query Parameters:
  - date (optional, YYYY-MM-DDl detaults to today)

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
    "data": {
      "id": 1,
      "card_no": "7701",
      "punch_date": "{$today}",
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
  }

================================================================================
Generated on: {$today} by ROI Attendance Management System
===============================================================================
TXT;

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, $headers);
    }

    public function exportEndpointTxt(string $endpoint): StreamedResponse
    {
        $baseUrl = url('/api/v1');
        $apiRootUrl = url('/api');
        $today = date('Y-m-d');

        switch ($endpoint) {
            case 'hrsale-attendance':
                $filename = 'hrsale_attendance_endpoint_spec.txt';
                $content = <<<TXT
================================================================================
          HRsale ATTENDANCE ENDPOINT SPECIFICATION (PRIMARY INTEGRATION)
================================================================================
Endpoint: POST {$apiRootUrl}/attendance
Alternative: POST {$baseUrl}/attendance
Format: JSON / Form-Data / x-www-form-urlencoded
Auth: Required (Bearer Token via Laravel Sanctum)
Rate Limit: 60 requests per minute

--------------------------------------------------------------------------------
1. HEADERS
-------------------------------------------------------------------------------
  Authorization: Bearer <YOUR_PERSONAL_ACCESS_TOKEN>   (Required)
  Content-Type: application/json                       (Recommended)
  Accept: application/json                             (Recommended)

--------------------------------------------------------------------------------
2. REQUEST BODY PARAMETERS (ALL OPTIONAL)
--------------------------------------------------------------------------------
  Parameter    | Type         | Default    | Description
  -------------+--------------+------------+-------------------------------------
  punch_date   | string (Y-m-d)< today date | Fetch logs for a specific single date
  start_date   | string (Y-m-d)| none       | Fetch logs starting from this date
  end_date     | string (Y-m-d)|A��������������э�����́���Ѽ�ѡ�́��є4(���������}���������Ѐ����ɥ��������������������ѕȁ���́�䁍������%��ȁ����4(�������啕}��������Ѐ����ɥ��������������������ѕȁ���́�䁕����啔��������%4(����ɑ}������������ɥ��������������������������ѕȁ���́�䁉�����ɥ����I%���ɐ4(-------------------------------------------------------------------------------
3. CODE SAMPLES
-------------------------------------------------------------------------------

[ cURL ]
curl -X POST "{$apiRootUrl}/attendance" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "punch_date": "{$today}",
    "company_id": 1
  }'

[ JavaScript (Fetch) ]
fetch("{$apiRootUrl}/attendance", {
  method: "POST",
  headers: {
    "Authorization": "Bearer YOUR_ACCESS_TOKEN",
    "Content-Type": "application/json",
    "Accept": "application/json"
  },
  body: JSON.stringify({
    punch_date: "{$today}",
    company_id: 1
  })
})
.then(res => res.json())
.then(data => console.log(data));

[ PHP (cURL) ]
\$curl = curl_init();
curl_setopt_array(\$curl, [
  CURLOPT_URL => "{$apiRootUrl}/attendance",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    "punch_date" => "{$today}",
    "company_id" => 1
  ]),
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer YOUR_ACCESS_TOKEN",
    "Content-Type: application/json",
    "Accept": "application/json"
  ],
]);
\$response = curl_exec(\$curl);
curl_close(\$curl);
echo \$response;

[ Python (requests) ]
import requests
Url = "{$apiRootUrl}/attendance"
headers = {
    "Authorization": "Bearer YOUR_ACCESS_TOKEN",
    "Content-Type": "application/json",
    "Accept": "application/json"
}
payload = {
    "punch_date": "{$today}",
    "company_id": 1
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())

--------------------------------------------------------------------------------
4. SAMPLE SUCCESS RESPONSE (200 OK):
--------------------------------------------------------------------------------
[
  {
    "time_attendance_id": "12",
    "card_no": "1002",
    "punch_date": "{$today}",
    "clock_in": "09:00:00",
    "clock_out": "18:00:00",
    "total_work": "09:00:00",
    "employee_id": "24",
    "employee_name": "John Doe",
    "company_name": "Demo Company"
  }
]

--------------------------------------------------------------------------------
5. ERROR RESPONSES
--------------------------------------------------------------------------------
401 Unauthorized:
  {
    "error": "Missing or Invalid Authorization token"
  }

422 Unprocessable Entity:
  {
    "message": "The punch date does not match the format Y-m-d.",
    "errors": {
      "punch_date": ["The punch date does not match the format Y-m-d."]
    }
  }

================================================================================
Generated on: {$today} by ROI Attendance System
================================================================================
TXT;
                break;
            case 'auth-token':
                $filename = 'auth_token_endpoint_spec.txt';
                $content = <<<TXT
================================================================================
          AUTHENTICATION TOKEN ENDPOINT SPECIFICATION
================================================================================
Endpoint: POST {$baseUrl}/auth/token
Auth: Public (No token required)
Rate Limit: 10 requests per minute

--------------------------------------------------------------------------------
1. HEADERS
-------------------------------------------------------------------------------
  Content-Type: application/json   (Required)
  Accept: application/json         (Required)

-------------------------------------------------------------------------------
2. REQUEST BODY (JSON)
-------------------------------------------------------------------------------
  Field       | Type   | Required | Description
  ------------+-------+----------+---------------------------------------------
  email       | string | Yes      | Registered admin user email address
  password    | string | Yes      | User password
  token_name  | string | No       | Friendly token descriptor (default: api-client-token)

Example Payload:
  {
    "email": "admin@example.com",
    "password": "your_secure_password",
    "token_name": "hrsale_service"
  }

-------------------------------------------------------------------------------
3. CODE SAMPLES
--------------------------------------------------------------------------------

[ cURL ]
curl -X POST "{$baseUrl}/auth/token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "your_secure_password",
    "token_name": "hrsale_service"
  }'

[ JavaScript (Fetch) ]
fetch("{$baseUrl}/auth/token", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
  },
  body: JSON.stringify({
    email: "admin@example.com",
    password: "your_secure_password",
    token_name: "hrsale_service"
  })
})
.then(res => res.json())
.then(data => console.log(data));

--------------------------------------------------------------------------------
4. SAMPLE SUCCESS RESPONSE (200 OK)
--------------------------------------------------------------------------------
{
  "success": true,
  "message": "Token generated successfully.",
  "token": "1|qWeRtYqIoP123456789...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@example.com"
  }
}

-------------------------------------------------------------------------------
5. ERROR RESPONSES
--------------------------------------------------------------------------------
401 Unauthorized:
  {
    "success": false,
    "message": "The provided credentials do not match our records."
  }

422 Unprocessable Entity:
  {
    "message": "The email field is required.",
    "errors": {
      "email": ["The email field is required."]
    }
  }

===============================================================================
Generated on: {$today} by ROI Attendance System
================================================================================
TXT;
                break;

            case 'attendances-list':
                $filename = 'attendances_list_endpoint_spec.txt';
                $content = <<<TXT
================================================================================
          PAGINATED ATTENDANCE RECORDS ENDPOINT SPECIFICATION
================================================================================
Endpoint: GET {$baseUrl}/attendances
Auth: Required (Bearer Token via Laravel Sanctum)
Rate Limit: 60 requests per minute

--------------------------------------------------------------------------------
1. HEADERS
--------------------------------------------------------------------------------
  Authorization: Bearer <YOUR_PERSONAL_ACCESS_TOKEN>   (Required)
  Accept: application/json                             (Recommended)

--------------------------------------------------------------------------------
2. QUERY PARAMETERS (ALL OPTIONAL)
--------------------------------------------------------------------------------
  Parameter    | Type         | Description
  -------------+--------------+--------------------------------------------------
  start_date   | string (Y-m-d) | Filter records on or after this date
  end_date     | string (Y-m-d) | Filter records on or before this date
  employee_id  | string        | Filter by Employee ID code (e.g. EMP-1001)
  card_no      | string        | Filter by RFID/Biometric Card Number (e.g. 7701)
  company      | string        | Filter by Company Name substring
  status       | string        | Filter by status ('present' or 'late')
  per_page     | integer      | Records per page (1 to 100, default: 25)
  page         | integer      | Page index for pagination (default: 1)
-------------------------------------------------------------------------------
3. CODE SAMPLES
-------------------------------------------------------------------------------

[ cURL ]
curl -X GET "{$baseUrl}/attendances?start_date={$today}&status=present&per_page=25" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"

[ JavaScript (Fetch) ]
fetch("{$baseUrl}/attendances?start_date={$today}&status=present", {
  method: "GET",
  headers: {
    "Authorization": "Bearer YOUR_API_TOKEN",
    "Accept": "application/json"
  }
})
.then(res => res.json())
.then(data => console.log(data));

-------------------------------------------------------------------------------
4. SAMPLE SUCCESS RESPONSE (200 OK)
-------------------------------------------------------------------------------
{
  "success": true,
  "message": "Attendance records retrieved successfully.",
  "data": [
    {
      "id": 1,
      "card_no": "7701",
      "punch_date": "{$today}",
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
}

===============================================================================
Generated on: {$today} by ROI Attendance System
================================================================================
TXT;
                break;

            case 'daily-summary':
                $filename = 'daily_summary_endpoint_spec.txt';
                $content = <<<TXT
================================================================================
          DAILY ATTENDANCE SUMMARY ENDPOINT SPECIFICATION
================================================================================
Endpoint: GET {$baseUrl}/attendances/daily-summary
Auth: Required (Bearer Token via Laravel Sanctum)
Rate Limit: 60 requests per minute

--------------------------------------------------------------------------------
1. HEADERS
-------------------------------------------------------------------------------
  Authorization: Bearer <YOUR_PERSONAL_ACCESS_TOKEN>   (Required)
  Accept: application/json                             (Recommended)

--------------------------------------------------------------------------------
2. QUERY PARAMETERS
--------------------------------------------------------------------------------
  Parameter | Type         | Default    | Description
  ----------+--------------+------------+--------------------------------------
  date      | string (Y-m-d)|today date | Filter summary statistics by date
-------------------------------------------------------------------------------
3. CODE SAMPLES
-------------------------------------------------------------------------------

[ cURL ]
curl -X GET "{$baseUrl}/attendances/daily-summary?date={$today}" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"

[ JavaScript (Fetch) ]
fetch("{$baseUrl}/attendances/daily-summary?date={$today}", {
  method: "GET",
  headers: {
    "Authorization": "Bearer YOUR_API_TOKEN",
    "Accept": "application/json"
  }
})
.then(res => res.json())
.then(data => console.log(data));

--------------------------------------------------------------------------------
4. SAMPLE SUCCESS RESPONSE (200 OK)
--------------------------------------------------------------------------------
{
  "success": true,
  "date": "{$today}",
  "summary": {
    "total_punches": 113,
    "present_count": 98,
    "late_count": 15
  }
}

===============================================================================
Generated on: {$today} by ROI Attendance System
================================================================================
TXT;
                break;

            case 'attendance-single':
                $filename = 'attendance_single_endpoint_spec.txt';
                $content = <<<TXT
================================================================================
          SINGLE ATTENDANCE RECORD ENDPOINT SPECIFICATION
================================================================================
Endpoint: GET {$baseUrl}/attendances/{id}
Auth: Required (Bearer Token via Laravel Sanctum)
Rate Limit: 60 requests per minute

--------------------------------------------------------------------------------
1. HEADERS
--------------------------------------------------------------------------------
  Authorization: Bearer <YOUR_PERSONAL_ACCESS_TOKEN>   (Required)
  Accept: application/json                             (Recommended)

--------------------------------------------------------------------------------
2. URI PARAMETERS
--------------------------------------------------------------------------------
  Parameter | Type    | Required | Description
  ----------+---------+----------+--------------------------------------------
  id        | integer | Yes      | The unique database ID of the attendance log

--------------------------------------------------------------------------------
3. CODE SAMPLES
--------------------------------------------------------------------------------

[ cURL ]
curl -X GET "{$baseUrl}/attendances/1" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"

[ JavaScript (Fetch) ]
fetch("{$baseUrl}/attendances/1", {
  method: "GET",
  headers: {
    "Authorization": "Bearer YOUR_API_TOKEN",
    "Accept": "application/json"
  }
})
.then(res => res.json())
.then(data => console.log(data));

-------------------------------------------------------------------------------
4. SAMPLE SUCCESS RESPONSE (200 OK)
-------------------------------------------------------------------------------
{
  "success": true,
  "data": {
    "id": 1,
    "card_no": "7701",
    "punch_date": "{$today}",
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
}

-------------------------------------------------------------------------------
5. ERROR RESPONSES
--------------------------------------------------------------------------------
404 Not Found:
  {
    "success": false,
    "message": "Attendance record with ID 1 not found."
  }

================================================================================
Generated on: {$today} by ROI Attendance System
================================================================================
TXT;
                break;

            default:
                abort(404, 'API endpoint specification not found.');
        }

        $headers = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',

            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, $headers);
    }
}
