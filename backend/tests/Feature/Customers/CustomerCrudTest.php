<?php

declare(strict_types=1);

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\User;
use App\Support\Pii\PiiCrypto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CustomerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_customer_with_required_fields(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->postJson('/api/admin/customers', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'cpf' => '529.982.247-25',
            'phone' => '11999998888',
            'birth_date' => '1990-05-12',
            'address' => 'Rua das Flores, 100 - São Paulo - SP',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.customer.name', 'Maria Silva')
            ->assertJsonPath('data.customer.cpf', '52998224725')
            ->assertJsonPath('data.customer.lifetime_spend', '0.00');

        $this->assertDatabaseHas('customers', [
            'cpf_hash' => PiiCrypto::blindIndex('52998224725'),
            'email_hash' => PiiCrypto::blindIndex('email:maria@example.com'),
            'name' => 'Maria Silva',
        ]);

        $stored = Customer::query()->where('cpf_hash', PiiCrypto::blindIndex('52998224725'))->first();
        $this->assertNotNull($stored);
        $this->assertSame('52998224725', $stored->cpf);
        $this->assertSame('maria@example.com', $stored->email);
    }

    public function test_duplicate_cpf_returns_cust_cpf_duplicate(): void
    {
        $manager = User::factory()->manager()->create();
        Customer::factory()->create(['cpf' => '52998224725']);

        $this->actingAs($manager)->postJson('/api/admin/customers', [
            'name' => 'Other',
            'email' => 'other@example.com',
            'cpf' => '52998224725',
            'phone' => '11988887777',
            'birth_date' => '1985-01-01',
            'address' => 'Av Paulista, 1000',
        ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'CUST_CPF_DUPLICATE');
    }

    public function test_manager_can_list_and_show_customer(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = Customer::factory()->create(['name' => 'Ana Costa']);

        $this->actingAs($manager)
            ->getJson('/api/admin/customers?search=ana')
            ->assertOk()
            ->assertJsonCount(1, 'data.customers');

        $this->actingAs($manager)
            ->getJson("/api/admin/customers/{$customer->id}")
            ->assertOk()
            ->assertJsonPath('data.customer.name', 'Ana Costa');
    }

    public function test_manager_can_paginate_customers_with_cursor(): void
    {
        $manager = User::factory()->manager()->create();

        Customer::factory()->create(['name' => 'Alpha']);
        Customer::factory()->create(['name' => 'Bravo']);
        Customer::factory()->create(['name' => 'Charlie']);

        $first = $this->actingAs($manager)->getJson('/api/admin/customers?per_page=2');
        $first
            ->assertOk()
            ->assertJsonCount(2, 'data.customers')
            ->assertJsonPath('data.customers.0.name', 'Alpha')
            ->assertJsonPath('data.customers.1.name', 'Bravo');

        $cursor = $first->json('meta.next_cursor');
        $this->assertNotNull($cursor);

        $second = $this->actingAs($manager)->getJson(
            '/api/admin/customers?per_page=2&cursor='.urlencode((string) $cursor),
        );
        $second
            ->assertOk()
            ->assertJsonCount(1, 'data.customers')
            ->assertJsonPath('data.customers.0.name', 'Charlie')
            ->assertJsonPath('meta.next_cursor', null);
    }

    public function test_invalid_customers_cursor_returns_validation_error(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/customers?per_page=2&cursor=not-a-cursor')
            ->assertStatus(422);
    }

    public function test_customers_without_per_page_omits_meta_cursor(): void
    {
        $manager = User::factory()->manager()->create();
        Customer::factory()->count(2)->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/customers')
            ->assertOk()
            ->assertJsonCount(2, 'data.customers')
            ->assertJsonMissingPath('meta.next_cursor');
    }

    public function test_manager_can_update_customer(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = Customer::factory()->create(['phone' => '11911112222']);

        $this->actingAs($manager)->patchJson("/api/admin/customers/{$customer->id}", [
            'phone' => '11933334444',
        ])
            ->assertOk()
            ->assertJsonPath('data.customer.phone', '11933334444');
    }

    public function test_show_unknown_customer_returns_cust_not_found(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/customers/99999')
            ->assertNotFound()
            ->assertJsonPath('error.code', 'CUST_NOT_FOUND');
    }

    public function test_operator_cannot_access_admin_customers(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/admin/customers')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }
}
