<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CorrelationIdMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_response_includes_generated_request_id_header(): void
    {
        $response = $this->getJson('/up');

        $response->assertOk();
        $requestId = $response->headers->get('X-Request-Id');

        $this->assertNotNull($requestId);
        $this->assertNotSame('', $requestId);
        $this->assertLessThanOrEqual(128, strlen((string) $requestId));
    }

    #[Test]
    public function api_echoes_valid_incoming_request_id_header(): void
    {
        $incoming = 'test-correlation-abc-123';

        $response = $this->withHeaders(['X-Request-Id' => $incoming])->getJson('/up');

        $response->assertOk();
        $this->assertSame($incoming, $response->headers->get('X-Request-Id'));
    }
}
