<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    private const TESTING_APP_KEY = 'base64:YWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWE=';

    protected bool $autoIdempotencyKey = true;

    protected function setUp(): void
    {
        // Docker injects empty APP_KEY via env_file; force before and after boot.
        putenv('APP_KEY='.self::TESTING_APP_KEY);
        $_ENV['APP_KEY'] = self::TESTING_APP_KEY;
        $_SERVER['APP_KEY'] = self::TESTING_APP_KEY;

        parent::setUp();

        config(['app.key' => self::TESTING_APP_KEY]);
    }

    protected function withoutAutoIdempotencyKey(): static
    {
        $this->autoIdempotencyKey = false;

        return $this;
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        if ($this->shouldInjectIdempotencyKey((string) $method, (string) $uri, $headers)) {
            $headers['Idempotency-Key'] = (string) Str::uuid();
        }

        return parent::json($method, $uri, $data, $headers, $options);
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    private function shouldInjectIdempotencyKey(string $method, string $uri, array $headers): bool
    {
        if (! $this->autoIdempotencyKey || strtoupper($method) !== 'POST') {
            return false;
        }

        foreach (array_keys($headers) as $name) {
            if (strcasecmp((string) $name, 'Idempotency-Key') === 0) {
                return false;
            }
        }

        $path = parse_url($uri, PHP_URL_PATH) ?: $uri;

        return (bool) preg_match('#/(complete|refunds)$#', $path);
    }
}
