<?php

namespace App\Services;

use App\Models\GreythrPushLog;
use App\Models\OtherCompanyAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GreythrPushService
{
    protected string $domain;
    protected string $username;
    protected string $password;
    protected string $tokenUrl;
    protected string $swipesUrl;

    public function __construct()
    {
        $this->domain = config('services.greythr.domain', 'i2k2-networks.greythr.com');
        $this->username = config('services.greythr.username', 'I2k2apiuser');
        $this->password = config('services.greythr.password', '40f33d1e-fe49-4212-9569-268e90003ca1');
        $this->tokenUrl = config('services.greythr.token_url', 'https://api.greythr.com/oauth2/v2/client-token');
        $this->swipesUrl = config('services.greythr.swipes_url', 'https://api.greythr.com/attendance/v2/employee/swipes');
    }

    /**
     * Retrieve or cache greytHR v2 OAuth access token.
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'greythr_token_' . md5($this->domain . $this->username);

        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'x-greythr-domain' => $this->domain,
                    'ACCESS-TOKEN'     => 'Access Token',
                ])
                ->timeout(20)
                ->post($this->tokenUrl);

            if (!$response->successful()) {
                Log::error("greytHR Token API error [{$response->status()}]: " . $response->body());
                return null;
            }

            $data = $response->json();
            $token = $data['access_token'] ?? null;
            $expiresIn = (int) ($data['expires_in'] ?? 3600);

            if ($token) {
                // Cache token with 5-minute safety buffer
                $ttl = max(60, $expiresIn - 300);
                Cache::put($cacheKey, $token, $ttl);
                return $token;
            }

            Log::error("greytHR Token API response missing access_token: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("greytHR Token Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format punch logs from other_company_attendances into the greytHR CSV swipe string array.
     *
     * Format: ["<ISO_TIMESTAMP>,<EMPLOYEE_ID>,<DOOR>,<1_FOR_IN_OR_0_FOR_OUT>"]
     */
    public function formatSwipes(string $startDate, string $endDate): array
    {
        $attendances = OtherCompanyAttendance::with('employee')
            ->whereDate('punch_date', '>=', $startDate)
            ->whereDate('punch_date', '<=', $endDate)
            ->orderBy('punch_date')
            ->orderBy('check_in_datetime')
            ->get();

        $swipes = [];

        foreach ($attendances as $att) {
            // Resolve employee identifier
            $empId = !empty($att->badgenumber) ? trim((string)$att->badgenumber) : null;
            if (empty($empId) && $att->employee && !empty($att->employee->employee_id)) {
                $empId = trim((string)$att->employee->employee_id);
            }
            if (empty($empId)) {
                $empId = trim((string)$att->card_no);
            }

            if (empty($empId)) {
                continue;
            }

            $dateString = $att->punch_date ? $att->punch_date->format('Y-m-d') : null;
            if (!$dateString) {
                continue;
            }

            // 1. Check-In Swipe (door 1, 1)
            $checkInTime = null;
            if ($att->check_in_datetime) {
                $checkInTime = Carbon::parse($att->check_in_datetime, 'Asia/Kolkata');
            } elseif (!empty($att->check_in_time)) {
                $checkInTime = Carbon::parse("{$dateString} {$att->check_in_time}", 'Asia/Kolkata');
            }

            if ($checkInTime) {
                $isoIn = $checkInTime->format('Y-m-d\TH:i:s.000P');
                $swipes[] = "{$isoIn},{$empId},door 1,1";
            }

            // 2. Check-Out Swipe (door 2, 0)
            $checkOutTime = null;
            if ($att->check_out_datetime) {
                $checkOutTime = Carbon::parse($att->check_out_datetime, 'Asia/Kolkata');
            } elseif (!empty($att->check_out_time)) {
                $checkOutTime = Carbon::parse("{$dateString} {$att->check_out_time}", 'Asia/Kolkata');
            }

            if ($checkOutTime) {
                $isoOut = $checkOutTime->format('Y-m-d\TH:i:s.000P');
                $swipes[] = "{$isoOut},{$empId},door 2,0";
            }
        }

        return [
            'swipes'        => $swipes,
            'records_count' => $attendances->count(),
        ];
    }

    /**
     * Push other companies attendance swipes to greytHR API and audit the result.
     */
    public function push(?string $startDate = null, ?string $endDate = null, string $triggerType = 'cron', ?int $userId = null): array
    {
        $startDate = $startDate ?: Carbon::now('Asia/Kolkata')->subDay()->format('Y-m-d');
        $endDate = $endDate ?: Carbon::now('Asia/Kolkata')->format('Y-m-d');

        $formatted = $this->formatSwipes($startDate, $endDate);
        $swipes = $formatted['swipes'];
        $recordsCount = $formatted['records_count'];
        $swipesCount = count($swipes);

        if ($swipesCount === 0) {
            $msg = "No attendance punch records found to push for range {$startDate} to {$endDate}.";
            Log::info("greytHR Push: {$msg}");

            GreythrPushLog::create([
                'trigger_type'     => $triggerType,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'records_count'    => 0,
                'swipes_count'     => 0,
                'status'           => 'empty',
                'http_status'      => null,
                'message'          => $msg,
                'payload_sample'   => [],
                'response_payload' => null,
                'triggered_by'     => $userId,
            ]);

            return [
                'success'      => true,
                'status'       => 'empty',
                'message'      => $msg,
                'swipes_count' => 0,
            ];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            $errorMsg = "Authentication failed: Unable to obtain access token from greytHR v2 API.";
            Log::error("greytHR Push: {$errorMsg}");

            GreythrPushLog::create([
                'trigger_type'     => $triggerType,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'records_count'    => $recordsCount,
                'swipes_count'     => $swipesCount,
                'status'           => 'failed',
                'http_status'      => 401,
                'message'          => $errorMsg,
                'payload_sample'   => array_slice($swipes, 0, 5),
                'response_payload' => null,
                'triggered_by'     => $userId,
            ]);

            return [
                'success'      => false,
                'status'       => 'failed',
                'message'      => $errorMsg,
                'swipes_count' => $swipesCount,
            ];
        }

        try {
            $response = Http::withHeaders([
                'ACCESS-TOKEN'     => $token,
                'x-greythr-domain' => $this->domain,
                'Content-Type'     => 'application/json',
            ])
            ->timeout(45)
            ->post($this->swipesUrl, $swipes);

            $httpStatus = $response->status();
            $responseData = $response->json() ?? ['raw' => substr($response->body(), 0, 1000)];
            $isSuccess = ($response->successful() || $httpStatus === 201);

            $message = $responseData['message'] ?? ($isSuccess ? 'Employee swipes pushed successfully.' : "greytHR API responded with status {$httpStatus}");

            GreythrPushLog::create([
                'trigger_type'     => $triggerType,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'records_count'    => $recordsCount,
                'swipes_count'     => $swipesCount,
                'status'           => $isSuccess ? 'success' : 'failed',
                'http_status'      => $httpStatus,
                'message'          => $message,
                'payload_sample'   => array_slice($swipes, 0, 10),
                'response_payload' => $responseData,
                'triggered_by'     => $userId,
            ]);

            if ($isSuccess) {
                Log::info("greytHR Push Successful: {$swipesCount} swipes synchronized for range {$startDate} to {$endDate}.");
            } else {
                Log::warning("greytHR Push Unsuccessful: HTTP {$httpStatus} - " . $response->body());
            }

            return [
                'success'      => $isSuccess,
                'status'       => $isSuccess ? 'success' : 'failed',
                'message'      => $message,
                'swipes_count' => $swipesCount,
                'http_status'  => $httpStatus,
            ];
        } catch (\Exception $e) {
            $errorMsg = "Exception during greytHR push: " . $e->getMessage();
            Log::error($errorMsg);

            GreythrPushLog::create([
                'trigger_type'     => $triggerType,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'records_count'    => $recordsCount,
                'swipes_count'     => $swipesCount,
                'status'           => 'failed',
                'http_status'      => 500,
                'message'          => $errorMsg,
                'payload_sample'   => array_slice($swipes, 0, 5),
                'response_payload' => ['error' => $e->getMessage()],
                'triggered_by'     => $userId,
            ]);

            return [
                'success'      => false,
                'status'       => 'failed',
                'message'      => $errorMsg,
                'swipes_count' => $swipesCount,
            ];
        }
    }
}
