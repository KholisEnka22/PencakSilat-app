<?php

declare(strict_types=1);

namespace Modules\Perguruan\Domain\Enums;

enum PerguruanStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public static function fromIsActive(bool $isActive): self
    {
        return $isActive ? self::Active : self::Inactive;
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Inactive => 'Tidak Aktif',
        };
    }
}
