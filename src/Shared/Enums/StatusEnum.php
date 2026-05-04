<?php

declare(strict_types=1);

namespace Src\Shared\Enums;

enum StatusEnum: string
{
    case Active   = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Aktif',
            self::Inactive => 'Tidak Aktif',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
