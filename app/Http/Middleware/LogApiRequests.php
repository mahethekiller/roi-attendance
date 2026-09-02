<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('api_start_time', microtime(true));
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $startTime = $request->attributes->get('api_start_time');
        $duration = $startTime ? round((microtime(true) - $startTime) * 1000, 2) : 0;

        $user = $request->user('sanctum');
        $tokenName = null;
        if ($user && $user->currentAccessToken()) {
            $tokenName = $user->currentAccessToken()->name;
        }

        try {
            ApiRequestLog::create([
                'user_id' => $user ? $user->id : null,
                'token_name' => $tokenName,
                'ip_address' => $request->ip() ?? '127.0.0.1',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query_params' => $request->query() ?: null,
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore logging failures to not disrupt API responses
        }
    }
}
