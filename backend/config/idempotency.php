<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Idempotency record retention (RN-073)
    |--------------------------------------------------------------------------
    |
    | Rows in idempotency_records older than this many days are removed by
    | `php artisan idempotency:purge` (scheduled daily).
    |
    */
    'retention_days' => (int) env('IDEMPOTENCY_RETENTION_DAYS', 7),
];
