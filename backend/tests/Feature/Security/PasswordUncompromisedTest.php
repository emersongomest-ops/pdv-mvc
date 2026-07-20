<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class PasswordUncompromisedTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    protected function tearDown(): void
    {
        config(['auth.password_uncompromised' => false]);
        Password::defaults(static fn (): Password => Password::min(12));
        Http::fake([]);

        parent::tearDown();
    }

    #[Test]
    public function create_user_rejects_password_found_in_hibp_range_response(): void
    {
        config(['auth.password_uncompromised' => true]);
        Password::defaults(static fn (): Password => Password::min(12)->uncompromised());

        $password = 'password12345';
        $hash = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        Http::fake([
            'api.pwnedpasswords.com/range/'.$prefix => Http::response($suffix.":42\r\n", 200),
        ]);

        $manager = User::factory()->manager()->create();
        $store = Store::factory()->create();

        $this->actingAs($manager)
            ->postJson('/api/admin/users', [
                'name' => 'Breached User',
                'email' => 'breached@pos.test',
                'password' => $password,
                'password_confirmation' => $password,
                'role' => 'operator',
                'is_active' => true,
                'store_ids' => [$store->id],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'breached@pos.test']);
    }

    #[Test]
    public function create_user_accepts_password_absent_from_hibp_range_response(): void
    {
        config(['auth.password_uncompromised' => true]);
        Password::defaults(static fn (): Password => Password::min(12)->uncompromised());

        $password = 'UniqueSafePass99!';
        $hash = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);

        Http::fake([
            'api.pwnedpasswords.com/range/'.$prefix => Http::response("AAAAA:1\r\nBBBBB:2\r\n", 200),
        ]);

        $manager = User::factory()->manager()->create();
        $store = Store::factory()->create();

        $this->actingAs($manager)
            ->postJson('/api/admin/users', [
                'name' => 'Safe User',
                'email' => 'safe@pos.test',
                'password' => $password,
                'password_confirmation' => $password,
                'role' => 'operator',
                'is_active' => true,
                'store_ids' => [$store->id],
            ])
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'safe@pos.test');
    }
}
