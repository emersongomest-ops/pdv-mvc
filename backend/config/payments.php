<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Payment acquirer transport
    |--------------------------------------------------------------------------
    |
    | Outbound charge/refund uses SOAP. The PDV HTTP API and payment webhooks
    | remain REST. Mode "stub" builds and validates SOAP envelopes locally
    | without a network call (safe for tests / local Docker).
    |
    */
    'soap' => [
        'mode' => env('PAYMENTS_SOAP_MODE', 'stub'), // stub|live
        'wsdl' => env('PAYMENTS_SOAP_WSDL'),
        'endpoint' => env('PAYMENTS_SOAP_ENDPOINT'),
        'timeout_seconds' => (int) env('PAYMENTS_SOAP_TIMEOUT', 30),
        'namespace' => env('PAYMENTS_SOAP_NAMESPACE', 'urn:pdv:payments'),
    ],

    'webhook' => [
        'secret' => env('PAYMENT_WEBHOOK_SECRET', 'local-payment-webhook-secret'),
    ],

    /*
    | Non-cash charges stay pending until webhook or reconcile (Option A).
    | Outbox / webhook-retry queue driver: redis | array (in-memory).
    */
    'async_settlement' => [
        'enabled' => (bool) env('PAYMENTS_ASYNC_SETTLEMENT', true),
        'provider' => env('PAYMENTS_ASYNC_PROVIDER', 'stub'),
    ],

    'reconcile' => [
        'driver' => env('PAYMENTS_RECONCILE_DRIVER', 'redis'), // redis|array
        'webhook_batch' => (int) env('PAYMENTS_WEBHOOK_RETRY_BATCH', 50),
        'webhook_max_attempts' => (int) env('PAYMENTS_WEBHOOK_RETRY_MAX', 24),
    ],
];
