<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'provider',
        'provider_event_id',
        'event_type',
        'transaction_reference',
        'payload',
        'processing_status',
        'processed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
