<?php

declare(strict_types=1);

namespace Modules\Perguruan\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerguruanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $perguruanId = $this->route('perguruan')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'abbreviation' => ['nullable', 'string', 'max:30'],
            'decree_number' => ['nullable', 'string', 'max:100'],
            'decree_date' => ['nullable', 'date'],
            'logo' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('perguruans', 'email')->ignore($perguruanId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'street_address' => ['nullable', 'string'],
            'village_id' => ['nullable', 'integer', 'exists:villages,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'regency_id' => ['nullable', 'integer', 'exists:regencies,id'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'postal_code' => ['nullable', 'digits:5'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
