<?php

declare(strict_types=1);

namespace Modules\Perguruan\Application\DTOs;

use Illuminate\Http\Request;
use Shared\Contracts\DTOInterface;

class PerguruanData implements DTOInterface
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $abbreviation = null,
        public readonly ?string $decree_number = null,
        public readonly ?string $decree_date = null,
        public readonly ?string $logo = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $website = null,
        public readonly ?string $street_address = null,
        public readonly ?int $village_id = null,
        public readonly ?int $district_id = null,
        public readonly ?int $regency_id = null,
        public readonly ?int $province_id = null,
        public readonly ?string $postal_code = null,
        public readonly bool $is_active = true,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return static::fromArray($request->validated());
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            name: (string) $data['name'],
            abbreviation: $data['abbreviation'] ?? null,
            decree_number: $data['decree_number'] ?? null,
            decree_date: $data['decree_date'] ?? null,
            logo: $data['logo'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            website: $data['website'] ?? null,
            street_address: $data['street_address'] ?? null,
            village_id: isset($data['village_id']) ? (int) $data['village_id'] : null,
            district_id: isset($data['district_id']) ? (int) $data['district_id'] : null,
            regency_id: isset($data['regency_id']) ? (int) $data['regency_id'] : null,
            province_id: isset($data['province_id']) ? (int) $data['province_id'] : null,
            postal_code: $data['postal_code'] ?? null,
            is_active: array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'decree_number' => $this->decree_number,
            'decree_date' => $this->decree_date,
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
            'is_active' => $this->is_active,
        ];
    }
}
