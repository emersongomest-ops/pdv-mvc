<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Sales\ValueObjects\SaleStatus;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'store_id',
        'user_id',
        'cash_shift_id',
        'customer_id',
        'status',
        'hold_label',
        'held_at',
        'subtotal',
        'discount_total',
        'total',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SaleStatus::class,
            'subtotal' => 'integer',
            'discount_total' => 'integer',
            'total' => 'integer',
            'completed_at' => 'datetime',
            'held_at' => 'datetime',
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

    /**
     * @return BelongsTo<CashShift, $this>
     */
    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class);
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<SaleLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(SaleLine::class);
    }

    /**
     * @return HasMany<PaymentLine, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentLine::class);
    }

    /**
     * @return HasOne<FiscalReceipt, $this>
     */
    public function fiscalReceipt(): HasOne
    {
        return $this->hasOne(FiscalReceipt::class);
    }

    /**
     * @return HasMany<SalePromotion, $this>
     */
    public function salePromotions(): HasMany
    {
        return $this->hasMany(SalePromotion::class);
    }

    public function isInProgress(): bool
    {
        return $this->status === SaleStatus::InProgress;
    }

    public function isHeld(): bool
    {
        return $this->status === SaleStatus::Held;
    }
}
