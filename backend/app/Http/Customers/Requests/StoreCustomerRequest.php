<?php

declare(strict_types=1);

namespace App\Http\Customers\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'cpf' => ['required', 'string', 'max:14'],
            'phone' => ['required', 'string', 'max:32'],
            'birth_date' => ['required', 'date'],
            'address' => ['required', 'string', 'max:500'],
        ];
    }
}
