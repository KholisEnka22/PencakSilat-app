<?php

declare(strict_types=1);

namespace Src\Shared\Enums;

enum RoleEnum: string
{
    case SuperAdmin      = 'super_admin';
    case AdminPerguruan  = 'admin_perguruan';
    case PelatihRayon    = 'pelatih_rayon';
    case Member          = 'member';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin     => 'Super Admin',
            self::AdminPerguruan => 'Admin Perguruan',
            self::PelatihRayon   => 'Pelatih Rayon',
            self::Member         => 'Member',
        };
    }
}
