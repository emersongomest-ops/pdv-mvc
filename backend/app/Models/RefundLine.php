<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundLine extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'refund_id',
        'sale_line_id',
        'quantity',
        'amount',
        'restocked',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'amount' => 'integer',
            'restocked' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Refund, $this>
     */
    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }

    /**
     * @return BelongsTo<SaleLine, $this>
     */
    public function saleLine(): BelongsTo
    {
        return $this->belongsTo(SaleLine::class);
    }
}
