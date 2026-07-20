<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Payments\ValueObjects\PaymentLineStatus;
use App\Domain\Payments\ValueObjects\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLine extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sale_id',
        'method',
        'amount',
        'cash_received',
        'change_amount',
        'transaction_reference',
        'status',
        'confirmed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'amount' => 'integer',
            'cash_received' => 'integer',
            'change_amount' => 'integer',
            'status' => PaymentLineStatus::class,
            'confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Sale, $this>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
