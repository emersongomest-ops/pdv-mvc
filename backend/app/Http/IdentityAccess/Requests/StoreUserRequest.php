<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Requests;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::enum(UserRole::class)],
            'is_active' => ['sometimes', 'boolean'],
            'store_ids' => ['required', 'array', 'min:1'],
            'store_ids.*' => ['integer', 'distinct', 'exists:stores,id'],
        ];
    }
}
