<?php

declare(strict_types=1);

namespace Tests\Support;

trait InteractsWithStatefulApi
{
    protected function enableStatefulApiHeaders(): void
    {
        $this->withHeaders([
            'Origin' => 'http://localhost',
            'Referer' => 'http://localhost/',
            // Laravel 13 PreventRequestForgery treats same-origin SPA posts as CSRF-safe.
            'Sec-Fetch-Site' => 'same-origin',
        ]);
    }
}
