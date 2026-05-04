<?php

declare(strict_types=1);

namespace Src\Modules\Perguruan\Application\DTOs;

use Spatie\LaravelData\Data;

class PerguruanData extends Data
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
}
