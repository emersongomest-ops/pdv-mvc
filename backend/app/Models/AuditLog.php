<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Audit\ValueObjects\AuditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;
use RuntimeException;

class AuditLog extends Model
{
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'action',
        'actor_user_id',
        'store_id',
        'subject_type',
        'subject_id',
        'old_values',
        'new_values',
        'metadata',
        'occurred_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(static function (): void {
            throw new LogicException('audit_logs are immutable');
        });

        static::deleting(static function (): void {
            throw new LogicException('audit_logs are immutable');
        });
    }

    public function save(array $options = []): bool
    {
        if ($this->exists) {
            throw new RuntimeException('audit_logs are immutable');
        }

        return parent::save($options);
    }

    public function delete(): ?bool
    {
        throw new RuntimeException('audit_logs are immutable');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
