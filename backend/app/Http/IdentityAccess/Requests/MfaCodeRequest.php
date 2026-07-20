<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class MfaCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'min:6', 'max:16'],
        ];
    }
}
