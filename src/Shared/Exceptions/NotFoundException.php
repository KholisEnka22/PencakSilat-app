<?php

declare(strict_types=1);

namespace Src\Shared\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $resource = 'Data')
    {
        parent::__construct("{$resource} tidak ditemukan.", 404);
    }
}
