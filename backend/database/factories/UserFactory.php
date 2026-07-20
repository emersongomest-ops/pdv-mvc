<?php

namespace Database\Factories;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'role' => UserRole::Operator,
            'is_active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Manager,
        ]);
    }

    public function operator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Operator,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /** Known demo secret for Authenticator apps / tests (RFC 6238 test vector style). */
    public function withMfa(string $secret = 'JBSWY3DPEHPK3PXP'): static
    {
        return $this->state(fn (array $attributes) => [
            'mfa_secret' => $secret,
            'mfa_confirmed_at' => now(),
            'mfa_last_otp_timestamp' => null,
        ]);
    }
}
