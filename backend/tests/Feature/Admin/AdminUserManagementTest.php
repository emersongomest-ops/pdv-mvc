<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_list_show_and_update_user(): void
    {
        $manager = User::factory()->manager()->create();
        $storeA = Store::factory()->create(['name' => 'Loja A', 'code' => 'A1']);
        $storeB = Store::factory()->create(['name' => 'Loja B', 'code' => 'B1']);

        $create = $this->actingAs($manager)->postJson('/api/admin/users', [
            'name' => 'Ana Operator',
            'email' => 'ana@pos.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'operator',
            'is_active' => true,
            'store_ids' => [$storeA->id],
        ]);

        $create
            ->assertCreated()
            ->assertJsonPath('data.user.name', 'Ana Operator')
            ->assertJsonPath('data.user.email', 'ana@pos.test')
            ->assertJsonPath('data.user.role', 'operator')
            ->assertJsonPath('data.user.is_active', true)
            ->assertJsonPath('data.user.stores.0.id', $storeA->id)
            ->assertJsonMissingPath('data.user.password');

        $userId = (int) $create->json('data.user.id');

        $this->actingAs($manager)
            ->getJson('/api/admin/users?search=ana')
            ->assertOk()
            ->assertJsonCount(1, 'data.users')
            ->assertJsonPath('data.users.0.email', 'ana@pos.test');

        $this->actingAs($manager)
            ->getJson("/api/admin/users/{$userId}")
            ->assertOk()
            ->assertJsonPath('data.user.id', $userId)
            ->assertJsonMissingPath('data.user.password');

        $this->actingAs($manager)->patchJson("/api/admin/users/{$userId}", [
            'name' => 'Ana Updated',
            'role' => 'manager',
            'store_ids' => [$storeB->id],
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.name', 'Ana Updated')
            ->assertJsonPath('data.user.role', 'manager')
            ->assertJsonPath('data.user.stores.0.id', $storeB->id)
            ->assertJsonMissingPath('data.user.password');

        $user = User::query()->findOrFail($userId);
        $this->assertTrue(Hash::check('newpassword1', $user->password));
        $this->assertSame([$storeB->id], $user->stores()->pluck('stores.id')->all());
    }

    public function test_duplicate_email_returns_auth_email_duplicate(): void
    {
        $manager = User::factory()->manager()->create(['email' => 'manager@pos.test']);
        $store = Store::factory()->create();
        User::factory()->operator()->create(['email' => 'taken@pos.test']);

        $this->actingAs($manager)->postJson('/api/admin/users', [
            'name' => 'Dup',
            'email' => 'taken@pos.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'operator',
            'store_ids' => [$store->id],
        ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'AUTH_EMAIL_DUPLICATE');
    }

    public function test_password_never_exposed_in_list_or_show(): void
    {
        $manager = User::factory()->manager()->create();
        $user = User::factory()->operator()->create(['password' => 'secretpass']);
        $store = Store::factory()->create();
        $user->stores()->attach($store);

        $list = $this->actingAs($manager)->getJson('/api/admin/users');
        $list->assertOk();
        $this->assertStringNotContainsString('secretpass', $list->getContent());
        $this->assertArrayNotHasKey('password', $list->json('data.users.0'));

        $show = $this->actingAs($manager)->getJson("/api/admin/users/{$user->id}");
        $show->assertOk();
        $this->assertArrayNotHasKey('password', $show->json('data.user'));
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $manager = User::factory()->manager()->create();
        $store = Store::factory()->create();
        $user = User::factory()->operator()->create([
            'email' => 'will-deactivate@pos.test',
            'password' => 'password123',
            'is_active' => true,
        ]);
        $user->stores()->attach($store);

        $this->actingAs($manager)->patchJson("/api/admin/users/{$user->id}", [
            'is_active' => false,
        ])
            ->assertOk()
            ->assertJsonPath('data.user.is_active', false);

        $this->postJson('/api/auth/login', [
            'email' => 'will-deactivate@pos.test',
            'password' => 'password123',
        ])
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ACCOUNT_INACTIVE');
    }

    public function test_manager_cannot_deactivate_or_demote_self(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->patchJson("/api/admin/users/{$manager->id}", [
            'is_active' => false,
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_CANNOT_MODIFY_SELF');

        $this->actingAs($manager)->patchJson("/api/admin/users/{$manager->id}", [
            'role' => 'operator',
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_CANNOT_MODIFY_SELF');

        $this->assertTrue($manager->fresh()->is_active);
        $this->assertTrue($manager->fresh()->isManager());
    }

    public function test_operator_cannot_manage_users(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/admin/users')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }

    public function test_create_requires_store_and_password_confirmation(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->postJson('/api/admin/users', [
            'name' => 'No Store',
            'email' => 'nostore@pos.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'operator',
            'store_ids' => [],
        ])->assertUnprocessable();

        $this->actingAs($manager)->postJson('/api/admin/users', [
            'name' => 'Bad Pass',
            'email' => 'badpass@pos.test',
            'password' => 'password123',
            'password_confirmation' => 'mismatch',
            'role' => 'operator',
            'store_ids' => [Store::factory()->create()->id],
        ])->assertUnprocessable();
    }

    public function test_show_unknown_user_returns_not_found(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/users/99999')
            ->assertNotFound()
            ->assertJsonPath('error.code', 'AUTH_USER_NOT_FOUND');
    }

    public function test_manager_can_paginate_users_with_cursor(): void
    {
        $manager = User::factory()->manager()->create(['name' => 'Zulu Manager']);

        User::factory()->operator()->create(['name' => 'Alpha User']);
        User::factory()->operator()->create(['name' => 'Bravo User']);
        User::factory()->operator()->create(['name' => 'Charlie User']);

        $first = $this->actingAs($manager)->getJson('/api/admin/users?per_page=2');
        $first
            ->assertOk()
            ->assertJsonCount(2, 'data.users')
            ->assertJsonPath('data.users.0.name', 'Alpha User')
            ->assertJsonPath('data.users.1.name', 'Bravo User');

        $cursor = $first->json('meta.next_cursor');
        $this->assertNotNull($cursor);

        $second = $this->actingAs($manager)->getJson(
            '/api/admin/users?per_page=2&cursor='.urlencode((string) $cursor),
        );
        $second
            ->assertOk()
            ->assertJsonPath('data.users.0.name', 'Charlie User');

        $names = collect($second->json('data.users'))->pluck('name')->all();
        $this->assertContains('Charlie User', $names);
        $this->assertNotContains('Alpha User', $names);
    }

    public function test_invalid_users_cursor_returns_validation_error(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/users?per_page=2&cursor=not-a-cursor')
            ->assertStatus(422);
    }

    public function test_users_without_per_page_omits_meta_cursor(): void
    {
        $manager = User::factory()->manager()->create();
        User::factory()->operator()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/users')
            ->assertOk()
            ->assertJsonMissingPath('meta.next_cursor');
    }
}
