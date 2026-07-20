<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Customers;

use App\Application\Customers\Actions\CreateCustomerAction;
use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Shared\ErrorCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateCustomerActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_required_field_throws_cust_required_field_missing(): void
    {
        $action = app(CreateCustomerAction::class);

        $this->expectException(CustomerDomainException::class);
        $this->expectExceptionMessage(ErrorCode::CustRequiredFieldMissing->message());

        $action->execute([
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'cpf' => '52998224725',
            'phone' => '11999998888',
            'birth_date' => '1990-01-01',
            'address' => '',
        ]);
    }
}
