<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Propagates X-Request-Id for log correlation (ADR-0007).
 */
final class AssignCorrelationId
{
    public const HEADER = 'X-Request-Id';

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->headers->get(self::HEADER);

        if (! is_string($requestId) || $requestId === '' || strlen($requestId) > 128) {
            $requestId = (string) Str::uuid();
        }

        $request->attributes->set('request_id', $requestId);

        Log::shareContext([
            'request_id' => $requestId,
        ]);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }
}
