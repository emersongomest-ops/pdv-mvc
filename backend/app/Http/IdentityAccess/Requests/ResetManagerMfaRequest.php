<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ResetManagerMfaRequest extends FormRequest
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
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }
}
