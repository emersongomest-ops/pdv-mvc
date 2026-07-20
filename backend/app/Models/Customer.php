<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PiiEncryptedDate;
use App\Casts\PiiEncryptedString;
use App\Support\Pii\PiiCrypto;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'birth_date',
        'address',
        'lifetime_spend',
        'cpf_hash',
        'email_hash',
        'anonymized_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'cpf_hash',
        'email_hash',
    ];

    protected static function booted(): void
    {
        static::saving(function (Customer $customer): void {
            if ($customer->isDirty('cpf') || $customer->cpf_hash === null) {
                $digits = PiiCrypto::normalizeCpf((string) $customer->cpf);
                $customer->cpf_hash = $digits !== '' ? PiiCrypto::blindIndex($digits) : null;
            }

            if ($customer->isDirty('email') || $customer->email_hash === null) {
                $email = PiiCrypto::normalizeEmail((string) $customer->email);
                $customer->email_hash = $email !== '' ? PiiCrypto::blindIndex('email:'.$email) : null;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email' => PiiEncryptedString::class,
            'cpf' => PiiEncryptedString::class,
            'phone' => PiiEncryptedString::class,
            'address' => PiiEncryptedString::class,
            'birth_date' => PiiEncryptedDate::class,
            'lifetime_spend' => 'integer',
            'anonymized_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<CustomerStoreStat, $this>
     */
    public function storeStats(): HasMany
    {
        return $this->hasMany(CustomerStoreStat::class);
    }

    /**
     * @return HasMany<Sale, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
