<?php

declare(strict_types=1);

namespace App\Http\Customers\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'cpf' => ['sometimes', 'string', 'max:14'],
            'phone' => ['sometimes', 'string', 'max:32'],
            'birth_date' => ['sometimes', 'date'],
            'address' => ['sometimes', 'string', 'max:500'],
        ];
    }
}
