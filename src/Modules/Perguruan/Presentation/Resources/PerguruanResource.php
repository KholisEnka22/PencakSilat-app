<?php

declare(strict_types=1);

namespace Modules\Perguruan\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerguruanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = $this->resource->status();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'decree_number' => $this->decree_number,
            'decree_date' => $this->decree_date?->toDateString(),
            'logo' => $this->logo,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'street_address' => $this->street_address,
            'village_id' => $this->village_id,
            'district_id' => $this->district_id,
            'regency_id' => $this->regency_id,
            'province_id' => $this->province_id,
            'postal_code' => $this->postal_code,
            'is_active' => (bool) $this->is_active,
            'status' => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
