<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use Database\Factories\CashShiftFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashShift extends Model
{
    /** @use HasFactory<CashShiftFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'store_id',
        'user_id',
        'status',
        'opening_cash_amount',
        'closing_cash_amount',
        'opened_at',
        'closed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CashShiftStatus::class,
            'opening_cash_amount' => 'integer',
            'closing_cash_amount' => 'integer',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOpen(): bool
    {
        return $this->status === CashShiftStatus::Open;
    }
}
