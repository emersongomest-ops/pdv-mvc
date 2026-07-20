<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Customer PII encryption (LGPD)
    |--------------------------------------------------------------------------
    |
    | Field-level encryption at rest for customer personal data. Uses a key
    | separate from APP_KEY so rotating session/cookie crypto does not force
    | a mass PII re-encrypt (and vice versa).
    |
    | Keys must be base64-encoded 32-byte secrets (Laravel "base64:..." format
    | or raw 32-byte string). Generate: php -r "echo 'base64:'.base64_encode(random_bytes(32)), PHP_EOL;"
    |
    */
    'encryption_key' => env('CUSTOMER_PII_ENCRYPTION_KEY', env('APP_KEY')),

    'blind_index_key' => env('CUSTOMER_PII_BLIND_INDEX_KEY', env('APP_KEY')),
];
