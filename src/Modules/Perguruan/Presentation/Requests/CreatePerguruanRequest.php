<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePerguruanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // TODO: tambahkan rules
        ];
    }
}