<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\RefundsReturns\ValueObjects\RefundType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refund extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sale_id',
        'store_id',
        'user_id',
        'type',
        'reason',
        'amount',
        'payment_refund_reference',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => RefundType::class,
            'amount' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Sale, $this>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<RefundLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(RefundLine::class);
    }
}
