<?php

declare(strict_types=1);

namespace Shared\Contracts;

use Illuminate\Http\Request;

interface DTOInterface
{
    public static function fromRequest(Request $request): static;

    public static function fromArray(array $data): static;

    public function toArray(): array;
}
