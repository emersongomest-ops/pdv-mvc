<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Store;
use App\Domain\IdentityAccess\ValueObjects\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token', 'mfa_secret', 'mfa_last_otp_timestamp', 'mfa_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'mfa_secret' => 'encrypted',
            'mfa_confirmed_at' => 'datetime',
            'mfa_last_otp_timestamp' => 'integer',
            'mfa_recovery_codes' => 'encrypted:array',
        ];
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isOperator(): bool
    {
        return $this->role === UserRole::Operator;
    }

    public function hasMfaEnabled(): bool
    {
        return $this->mfa_confirmed_at !== null
            && is_string($this->mfa_secret)
            && $this->mfa_secret !== '';
    }

    /**
     * @return BelongsToMany<Store, $this>
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class);
    }
}
